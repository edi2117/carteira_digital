# Testes

O projeto usa PHPUnit (Laravel) para testes de feature e unit.

## Rodar todos os testes

```bash
docker compose exec app php artisan test
```

## Rodar um arquivo específico

```bash
docker compose exec app php artisan test tests/Feature/Api/WalletEdgeCasesTest.php
```

## Rodar um teste específico (filtro)

```bash
docker compose exec app php artisan test --filter=test_large_amount
```

## Rodar apenas testes Unit ou Feature

```bash
# Unit
docker compose exec app php artisan test --testsuite=Unit

# Feature
docker compose exec app php artisan test --testsuite=Feature
```

## Rodar com saída verbosa

```bash
docker compose exec app php artisan test -v
```

## Dicas

- O banco é resetado entre cada teste via `RefreshDatabase`
- Para inspecionar valores durante o teste, use `dump()` ou `dd()` na resposta
