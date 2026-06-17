<?php

namespace Tests\Unit\Services;

use App\Exceptions\InsufficientFundsException;
use App\Exceptions\InvalidAmountException;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletServiceTest extends TestCase
{
    use RefreshDatabase;

    private WalletService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WalletService();
    }

    public function test_deposit_increases_balance(): void
    {
        $wallet = Wallet::factory()->create(['balance' => 0]);

        $transaction = $this->service->deposit($wallet, 100.50);

        $wallet->refresh();

        $this->assertEquals(100.50, $wallet->balance);
        $this->assertEquals('deposit', $transaction->type);
        $this->assertEquals(100.50, $transaction->amount);
        $this->assertEquals(100.50, $transaction->balance_after);
    }

    public function test_deposit_with_zero_amount_throws_exception(): void
    {
        $this->expectException(InvalidAmountException::class);

        $wallet = Wallet::factory()->create(['balance' => 0]);
        $this->service->deposit($wallet, 0);
    }

    public function test_deposit_with_negative_amount_throws_exception(): void
    {
        $this->expectException(InvalidAmountException::class);

        $wallet = Wallet::factory()->create(['balance' => 0]);
        $this->service->deposit($wallet, -50);
    }

    public function test_withdraw_decreases_balance(): void
    {
        $wallet = Wallet::factory()->create(['balance' => 200]);

        $transaction = $this->service->withdraw($wallet, 50);

        $wallet->refresh();

        $this->assertEquals(150, $wallet->balance);
        $this->assertEquals('withdraw', $transaction->type);
        $this->assertEquals(50, $transaction->amount);
        $this->assertEquals(150, $transaction->balance_after);
    }

    public function test_withdraw_with_insufficient_funds_throws_exception(): void
    {
        $this->expectException(InsufficientFundsException::class);

        $wallet = Wallet::factory()->create(['balance' => 10]);
        $this->service->withdraw($wallet, 20);
    }

    public function test_withdraw_with_zero_amount_throws_exception(): void
    {
        $this->expectException(InvalidAmountException::class);

        $wallet = Wallet::factory()->create(['balance' => 100]);
        $this->service->withdraw($wallet, 0);
    }

    public function test_deposit_creates_transaction_record(): void
    {
        $wallet = Wallet::factory()->create(['balance' => 0]);

        $this->service->deposit($wallet, 75, 'teste');

        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $wallet->id,
            'type' => 'deposit',
            'amount' => 75.00,
            'balance_after' => 75.00,
            'description' => 'teste',
        ]);
    }

    public function test_withdraw_creates_transaction_record(): void
    {
        $wallet = Wallet::factory()->create(['balance' => 300]);

        $this->service->withdraw($wallet, 100, 'saque teste');

        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $wallet->id,
            'type' => 'withdraw',
            'amount' => 100.00,
            'balance_after' => 200.00,
            'description' => 'saque teste',
        ]);
    }
}
