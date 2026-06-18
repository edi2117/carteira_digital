<?php

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Carteira Digital API',
    description: 'API de carteira digital com depósitos, saques e extrato.'
)]
#[OA\Server(
    url: 'http://localhost:8080/api',
    description: 'Servidor de desenvolvimento'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    description: 'Informe o token Sanctum no formato: Bearer <token>'
)]

// --- Schemas ---

#[OA\Schema(
    schema: 'User',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@example.com'),
    ]
)]
#[OA\Schema(
    schema: 'Wallet',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'balance', type: 'number', format: 'float', example: 1500.50),
    ]
)]
#[OA\Schema(
    schema: 'Transaction',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'type', type: 'string', enum: ['deposit', 'withdraw'], example: 'deposit'),
        new OA\Property(property: 'amount', type: 'number', format: 'float', example: 500.00),
        new OA\Property(property: 'balance_after', type: 'number', format: 'float', example: 1500.50),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Salário'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-06-17T10:00:00.000000Z'),
    ]
)]
#[OA\Schema(
    schema: 'DashboardSummary',
    type: 'object',
    properties: [
        new OA\Property(property: 'balance', type: 'number', format: 'float', example: 1500.50),
        new OA\Property(property: 'month_deposits', type: 'number', format: 'float', example: 5000.00),
        new OA\Property(property: 'month_withdrawals', type: 'number', format: 'float', example: 2000.00),
        new OA\Property(
            property: 'recent_transactions',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Transaction')
        ),
    ]
)]
#[OA\Schema(
    schema: 'AuthResponse',
    type: 'object',
    properties: [
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
        new OA\Property(property: 'token', type: 'string', example: '1|abc123def456...'),
    ]
)]
#[OA\Schema(
    schema: 'MessageResponse',
    type: 'object',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Operação realizada com sucesso.'),
    ]
)]
#[OA\Schema(
    schema: 'ErrorResponse',
    type: 'object',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Credenciais inválidas.'),
    ]
)]
#[OA\Schema(
    schema: 'ValidationError',
    type: 'object',
    properties: [
        new OA\Property(
            property: 'message',
            type: 'string',
            example: 'O campo amount é obrigatório. (and 2 more errors)'
        ),
        new OA\Property(
            property: 'errors',
            type: 'object',
            example: ['amount' => ['O campo amount é obrigatório.']]
        ),
    ]
)]
#[OA\Schema(
    schema: 'TransactionList',
    type: 'object',
    properties: [
        new OA\Property(
            property: 'transactions',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Transaction')
        ),
        new OA\Property(
            property: 'meta',
            type: 'object',
            properties: [
                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                new OA\Property(property: 'last_page', type: 'integer', example: 5),
                new OA\Property(property: 'per_page', type: 'integer', example: 15),
                new OA\Property(property: 'total', type: 'integer', example: 72),
            ]
        ),
    ]
)]

// --- Request Schemas ---

#[OA\Schema(
    schema: 'RegisterRequest',
    type: 'object',
    required: ['name', 'email', 'password', 'password_confirmation'],
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'João Silva'),
        new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'joao@example.com'),
        new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'senha123'),
        new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'senha123'),
    ]
)]
#[OA\Schema(
    schema: 'LoginRequest',
    type: 'object',
    required: ['email', 'password'],
    properties: [
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@example.com'),
        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'senha123'),
    ]
)]
#[OA\Schema(
    schema: 'AmountRequest',
    type: 'object',
    required: ['amount'],
    properties: [
        new OA\Property(property: 'amount', type: 'number', format: 'float', minimum: 0.01, example: 100.00),
        new OA\Property(property: 'description', type: 'string', maxLength: 255, nullable: true, example: 'Descrição opcional'),
    ]
)]
class OpenApiSpec
{
    // Classe auxiliar apenas para agrupar as anotações OpenAPI.
}
