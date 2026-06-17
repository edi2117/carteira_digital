<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['password' => bcrypt('password')]);
        $this->user->wallet()->create(['balance' => 100]);
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    private function headers(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_minimum_deposit_amount(): void
    {
        $response = $this->withHeaders($this->headers())
            ->postJson('/api/wallet/deposit', ['amount' => 0.01]);

        $response->assertStatus(201);
        $this->assertEquals(100.01, $this->user->wallet->fresh()->balance);
    }

    public function test_minimum_withdraw_amount(): void
    {
        $response = $this->withHeaders($this->headers())
            ->postJson('/api/wallet/withdraw', ['amount' => 0.01]);

        $response->assertStatus(201);
        $this->assertEquals(99.99, $this->user->wallet->fresh()->balance);
    }

    public function test_decimal_precision(): void
    {
        $this->withHeaders($this->headers())
            ->postJson('/api/wallet/deposit', ['amount' => 0.33]);

        $this->withHeaders($this->headers())
            ->postJson('/api/wallet/withdraw', ['amount' => 0.11]);

        $this->assertEquals(100.22, $this->user->wallet->fresh()->balance);
    }

    public function test_large_amount(): void
    {
        $response = $this->withHeaders($this->headers())
            ->postJson('/api/wallet/deposit', ['amount' => 9999999.99]);

        $response->assertStatus(201);
        $this->assertEquals(10000999.99, $this->user->wallet->fresh()->balance);
    }

    public function test_exact_balance_withdraw(): void
    {
        $response = $this->withHeaders($this->headers())
            ->postJson('/api/wallet/withdraw', ['amount' => 100]);

        $response->assertStatus(201);
        $this->assertEquals(0, $this->user->wallet->fresh()->balance);
    }

    public function test_does_not_create_transaction_on_failed_deposit(): void
    {
        $wallet = $this->user->wallet;

        $response = $this->withHeaders($this->headers())
            ->postJson('/api/wallet/deposit', ['amount' => -10]);

        $response->assertStatus(422);
        $this->assertEquals(0, $wallet->transactions()->count());
    }

    public function test_empty_transaction_history(): void
    {
        $response = $this->withHeaders($this->headers())
            ->getJson('/api/transactions');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'transactions');
    }

    public function test_invalid_pagination_page(): void
    {
        $response = $this->withHeaders($this->headers())
            ->getJson('/api/transactions?page=-1');

        $response->assertStatus(200);
    }

    public function test_non_existent_page_returns_empty(): void
    {
        $response = $this->withHeaders($this->headers())
            ->getJson('/api/transactions?page=9999');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'transactions');
    }

    public function test_sql_injection_in_amount(): void
    {
        $response = $this->withHeaders($this->headers())
            ->postJson('/api/wallet/deposit', ['amount' => '1; DROP TABLE wallets;']);

        $response->assertStatus(422);
        $this->assertDatabaseHas('wallets', ['id' => $this->user->wallet->id]);
    }

    public function test_string_instead_of_numeric(): void
    {
        $response = $this->withHeaders($this->headers())
            ->postJson('/api/wallet/deposit', ['amount' => 'abc']);

        $response->assertStatus(422);
    }

    public function test_negative_page(): void
    {
        $response = $this->withHeaders($this->headers())
            ->getJson('/api/transactions?page=-5&per_page=10');

        $response->assertStatus(200);
    }

    public function test_excessive_per_page(): void
    {
        $response = $this->withHeaders($this->headers())
            ->getJson('/api/transactions?per_page=9999');

        $response->assertStatus(200);
    }

    public function test_balance_after_multiple_operations(): void
    {
        $ops = [
            ['type' => 'deposit', 'amount' => 100],
            ['type' => 'withdraw', 'amount' => 30],
            ['type' => 'deposit', 'amount' => 50.50],
            ['type' => 'withdraw', 'amount' => 20.25],
        ];

        foreach ($ops as $op) {
            $endpoint = $op['type'] === 'deposit' ? 'deposit' : 'withdraw';
            $this->withHeaders($this->headers())
                ->postJson("/api/wallet/{$endpoint}", ['amount' => $op['amount']])
                ->assertStatus(201);
        }

        $this->assertEquals(200.25, $this->user->wallet->fresh()->balance);
    }

    public function test_transaction_history_ordered_by_recent_first(): void
    {
        $wallet = $this->user->wallet;
        for ($i = 1; $i <= 5; $i++) {
            $wallet->transactions()->create([
                'type' => 'deposit',
                'amount' => $i * 10,
                'balance_after' => 100 + $i * 10,
                'created_at' => now()->addMinutes($i),
            ]);
        }

        $response = $this->withHeaders($this->headers())
            ->getJson('/api/transactions');

        $amounts = collect($response['transactions'])->pluck('amount')->toArray();
        $this->assertTrue($amounts[0] > $amounts[4]);
    }

    public function test_register_creates_wallet_with_zero_balance(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Novo Usuário',
            'email' => 'novo@teste.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $user = User::where('email', 'novo@teste.com')->first();
        $this->assertNotNull($user->wallet);
        $this->assertEquals(0, $user->wallet->balance);
    }
}
