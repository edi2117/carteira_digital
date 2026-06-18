# Carteira Digital

Carteira digital com API REST em Laravel e frontend em Vue.

## Stack

- PHP 8.4 (Laravel 11)
- MySQL 8.0
- Nginx
- Vue 3 + Vite

## Pré-requisitos

- Docker + Docker Compose

## Setup

```bash
# Sobe os containers
docker compose up -d

# Instala dependências PHP
docker compose exec app composer install

# Gera APP_KEY (se não existir)
docker compose exec app php artisan key:generate

# Roda migrations
docker compose exec app php artisan migrate

# Opcional: popula o banco com dados de exemplo
docker compose exec app php artisan db:seed
```

## Acessos

| Serviço      | URL                          |
|-------------|------------------------------|
| API (Nginx) | http://localhost:8080        |
| Frontend    | http://localhost:5173        |
| MySQL       | localhost:3307 (user: carteira / senha: carteira) |

## API

Registro: `POST /api/register`  
Depósito: `POST /api/wallet/deposit`  
Saque: `POST /api/wallet/withdraw`  
Extrato: `GET /api/transactions`  

## Comandos úteis

```bash
# Logs do Laravel
docker compose exec app php artisan pail

# Queue worker
docker compose exec app php artisan queue:listen --tries=1

# Tinker (REPL interativo)
docker compose exec app php artisan tinker

# Parar containers
docker compose down

# Reset completo (derruba tudo e apaga dados)
docker compose down -v
```
