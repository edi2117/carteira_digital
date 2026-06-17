<?php

namespace App\Services;

use App\Exceptions\InsufficientFundsException;
use App\Exceptions\InvalidAmountException;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function deposit(Wallet $wallet, float $amount, ?string $description = null): Transaction
    {
        if ($amount <= 0) {
            throw new InvalidAmountException('O valor do depósito deve ser maior que zero.');
        }

        return DB::transaction(function () use ($wallet, $amount, $description) {
            $wallet->increment('balance', $amount);
            $wallet->refresh();

            return $wallet->transactions()->create([
                'type' => 'deposit',
                'amount' => $amount,
                'balance_after' => $wallet->balance,
                'description' => $description,
            ]);
        });
    }

    public function withdraw(Wallet $wallet, float $amount, ?string $description = null): Transaction
    {
        if ($amount <= 0) {
            throw new InvalidAmountException('O valor do saque deve ser maior que zero.');
        }

        return DB::transaction(function () use ($wallet, $amount, $description) {
            $wallet = $wallet->fresh()->lockForUpdate();

            if ($wallet->balance < $amount) {
                throw new InsufficientFundsException();
            }

            $wallet->decrement('balance', $amount);
            $wallet->refresh();

            return $wallet->transactions()->create([
                'type' => 'withdraw',
                'amount' => $amount,
                'balance_after' => $wallet->balance,
                'description' => $description,
            ]);
        });
    }

    public function getTransactionsQuery(Wallet $wallet, ?string $type = null, ?string $startDate = null, ?string $endDate = null)
    {
        $query = $wallet->transactions()->orderBy('created_at', 'desc');

        if ($type && in_array($type, ['deposit', 'withdraw'])) {
            $query->where('type', $type);
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query;
    }

    public function getDashboardData(Wallet $wallet): array
    {
        $balance = $wallet->balance;

        $monthTotals = $wallet->transactions()
            ->selectRaw("SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END) as total_deposits")
            ->selectRaw("SUM(CASE WHEN type = 'withdraw' THEN amount ELSE 0 END) as total_withdrawals")
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->first();

        $recentTransactions = $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'balance' => $balance,
            'month_deposits' => (float) ($monthTotals->total_deposits ?? 0),
            'month_withdrawals' => (float) ($monthTotals->total_withdrawals ?? 0),
            'recent_transactions' => $recentTransactions,
        ];
    }
}
