<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private WalletService $walletService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $wallet = $request->user()->wallet;

        $query = $this->walletService->getTransactionsQuery(
            $wallet,
            $request->type,
            $request->start_date,
            $request->end_date
        );

        $perPage = min((int) $request->per_page ?: 15, 50);
        $transactions = $query->paginate($perPage);

        return response()->json([
            'transactions' => TransactionResource::collection($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }
}
