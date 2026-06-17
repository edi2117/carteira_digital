<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $this->user->wallet()->create(['balance' => 100]);

        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_deposit(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/wallet/deposit', ['amount' => 50]);

        $response->assertStatus(201);
        $this->assertEquals(150, $this->user->wallet->fresh()->balance);
    }

    public function test_can_withdraw(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/wallet/withdraw', ['amount' => 30]);

        $response->assertStatus(201);
        $this->assertEquals(70, $this->user->wallet->fresh()->balance);
    }

    public function test_cannot_withdraw_insufficient_funds(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/wallet/withdraw', ['amount' => 999]);

        $response->assertStatus(422);
        $this->assertEquals(100, $this->user->wallet->fresh()->balance);
    }

    public function test_cannot_deposit_zero_or_negative(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/wallet/deposit', ['amount' => 0]);

        $response->assertStatus(422);

        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/wallet/deposit', ['amount' => -10]);

        $response->assertStatus(422);
    }

    public function test_cannot_withdraw_zero_or_negative(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/wallet/withdraw', ['amount' => 0]);

        $response->assertStatus(422);

        $response = $this->withHeaders($this->authHeaders())
            ->postJson('/api/wallet/withdraw', ['amount' => -10]);

        $response->assertStatus(422);
    }

    public function test_can_see_wallet_balance(): void
    {
        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/wallet');

        $response->assertStatus(200)
            ->assertJsonPath('wallet.balance', 100);
    }

    public function test_can_see_transaction_history(): void
    {
        $wallet = $this->user->wallet;
        $wallet->transactions()->create([
            'type' => 'deposit',
            'amount' => 50,
            'balance_after' => 150,
        ]);
        $wallet->transactions()->create([
            'type' => 'withdraw',
            'amount' => 30,
            'balance_after' => 120,
        ]);

        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/transactions');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'transactions');
    }

    public function test_can_filter_transactions_by_type(): void
    {
        $wallet = $this->user->wallet;
        $wallet->transactions()->create(['type' => 'deposit', 'amount' => 50, 'balance_after' => 150]);
        $wallet->transactions()->create(['type' => 'withdraw', 'amount' => 30, 'balance_after' => 120]);

        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/transactions?type=deposit');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'transactions');
    }

    public function test_can_see_dashboard_summary(): void
    {
        $wallet = $this->user->wallet;
        $wallet->transactions()->create(['type' => 'deposit', 'amount' => 200, 'balance_after' => 300]);
        $wallet->transactions()->create(['type' => 'withdraw', 'amount' => 50, 'balance_after' => 250]);

        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/dashboard/summary');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'balance', 'month_deposits', 'month_withdrawals', 'recent_transactions',
            ]);
    }

    public function test_unauthenticated_cannot_access(): void
    {
        $this->getJson('/api/wallet')->assertStatus(401);
        $this->postJson('/api/wallet/deposit', ['amount' => 10])->assertStatus(401);
        $this->postJson('/api/wallet/withdraw', ['amount' => 10])->assertStatus(401);
        $this->getJson('/api/transactions')->assertStatus(401);
        $this->getJson('/api/dashboard/summary')->assertStatus(401);
    }

    public function test_transaction_history_pagination(): void
    {
        $wallet = $this->user->wallet;
        for ($i = 0; $i < 20; $i++) {
            $wallet->transactions()->create([
                'type' => 'deposit',
                'amount' => 10,
                'balance_after' => 100 + ($i + 1) * 10,
            ]);
        }

        $response = $this->withHeaders($this->authHeaders())
            ->getJson('/api/transactions?per_page=5');

        $response->assertStatus(200);
        $this->assertCount(5, $response['transactions']);
        $this->assertEquals(5, $response['meta']['per_page']);
        $this->assertEquals(20, $response['meta']['total']);
    }
}
