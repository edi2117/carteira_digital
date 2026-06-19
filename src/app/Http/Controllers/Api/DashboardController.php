<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DashboardController extends Controller
{
    public function __construct(
        private WalletService $walletService
    ) {}

    #[OA\Get(
        path: '/dashboard/summary',
        summary: 'Resumo do dashboard',
        tags: ['Dashboard'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Resumo mensal com saldo e transações recentes',
                content: new OA\JsonContent(ref: '#/components/schemas/DashboardSummary')
            ),
            new OA\Response(
                response: 401,
                description: 'Não autenticado',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
    public function summary(Request $request): JsonResponse
    {
        $wallet = $request->user()->wallet;

        $data = $this->walletService->getDashboardData($wallet);

        return response()->json([
            'balance' => $data['balance'],
            'month_deposits' => $data['month_deposits'],
            'month_withdrawals' => $data['month_withdrawals'],
            'monthly_series' => $data['monthly_series'],
            'recent_transactions' => TransactionResource::collection($data['recent_transactions']),
        ]);
    }
}
