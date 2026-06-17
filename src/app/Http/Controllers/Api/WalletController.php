<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\WithdrawRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\WalletResource;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        private WalletService $walletService
    ) {}

    public function show(Request $request): JsonResponse
    {
        $wallet = $request->user()->wallet;

        return response()->json([
            'wallet' => new WalletResource($wallet),
        ]);
    }

    public function deposit(DepositRequest $request): JsonResponse
    {
        $wallet = $request->user()->wallet;

        $transaction = $this->walletService->deposit(
            $wallet,
            $request->amount,
            $request->description
        );

        return response()->json([
            'transaction' => new TransactionResource($transaction),
        ], 201);
    }

    public function withdraw(WithdrawRequest $request): JsonResponse
    {
        $wallet = $request->user()->wallet;

        $transaction = $this->walletService->withdraw(
            $wallet,
            $request->amount,
            $request->description
        );

        return response()->json([
            'transaction' => new TransactionResource($transaction),
        ], 201);
    }
}
