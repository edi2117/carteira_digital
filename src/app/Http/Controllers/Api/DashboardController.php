<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private WalletService $walletService
    ) {}

    public function summary(Request $request): JsonResponse
    {
        $wallet = $request->user()->wallet;

        $data = $this->walletService->getDashboardData($wallet);

        return response()->json([
            'balance' => $data['balance'],
            'month_deposits' => $data['month_deposits'],
            'month_withdrawals' => $data['month_withdrawals'],
            'recent_transactions' => TransactionResource::collection($data['recent_transactions']),
        ]);
    }
}
