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
use OpenApi\Attributes as OA;

class WalletController extends Controller
{
    public function __construct(
        private WalletService $walletService
    ) {}

    #[OA\Get(
        path: '/wallet',
        summary: 'Saldo da carteira',
        tags: ['Carteira'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Dados da carteira',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'wallet', ref: '#/components/schemas/Wallet'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Não autenticado',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
    public function show(Request $request): JsonResponse
    {
        $wallet = $request->user()->wallet;

        return response()->json([
            'wallet' => new WalletResource($wallet),
        ]);
    }

    #[OA\Post(
        path: '/wallet/deposit',
        summary: 'Realizar depósito',
        tags: ['Carteira'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/AmountRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Depósito realizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'transaction', ref: '#/components/schemas/Transaction'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Não autenticado',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Erro de validação',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
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

    #[OA\Post(
        path: '/wallet/withdraw',
        summary: 'Realizar saque',
        tags: ['Carteira'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/AmountRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Saque realizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'transaction', ref: '#/components/schemas/Transaction'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Não autenticado',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Saldo insuficiente ou erro de validação',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
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
