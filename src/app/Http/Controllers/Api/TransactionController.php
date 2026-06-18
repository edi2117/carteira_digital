<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TransactionController extends Controller
{
    public function __construct(
        private WalletService $walletService
    ) {}

    #[OA\Get(
        path: '/transactions',
        summary: 'Listar transações',
        tags: ['Transações'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'type',
                in: 'query',
                description: 'Filtrar por tipo (deposit ou withdraw)',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['deposit', 'withdraw'])
            ),
            new OA\Parameter(
                name: 'start_date',
                in: 'query',
                description: 'Filtrar por data inicial (Y-m-d)',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
            new OA\Parameter(
                name: 'end_date',
                in: 'query',
                description: 'Filtrar por data final (Y-m-d)',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                description: 'Itens por página (máx: 50)',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 15, maximum: 50)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de transações paginada',
                content: new OA\JsonContent(ref: '#/components/schemas/TransactionList')
            ),
            new OA\Response(
                response: 401,
                description: 'Não autenticado',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $wallet = $request->user()->wallet;

        $query = $this->walletService->getTransactionsQuery(
            $wallet,
            $request->type,
            $request->start_date,
            $request->end_date,
            $request->search
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
