# Testes

## Backend (Laravel / PHPUnit)

### Rodar todos os testes
```bash
docker compose exec app php artisan test
```

### Rodar apenas Unit
```bash
docker compose exec app php artisan test --testsuite=Unit
```

### Rodar apenas Feature
```bash
docker compose exec app php artisan test --testsuite=Feature
```

### Rodar um arquivo específico
```bash
docker compose exec app php artisan test tests/Unit/Services/WalletServiceTest.php
docker compose exec app php artisan test tests/Feature/Api/AuthTest.php
docker compose exec app php artisan test tests/Feature/Api/WalletTest.php
docker compose exec app php artisan test tests/Feature/Api/WalletEdgeCasesTest.php
```

### Rodar um teste específico (filtro)
```bash
docker compose exec app php artisan test --filter=test_user_can_deposit
```

### Rodar com saída verbosa
```bash
docker compose exec app php artisan test -v
```

### Lista completa de arquivos de teste

| Arquivo | Escopo | Qtd testes |
|---------|--------|------------|
| `tests/Unit/ExampleTest.php` | Smoke test unitário | 1 |
| `tests/Unit/Services/WalletServiceTest.php` | Lógica de depósito/saque/transações | 8 |
| `tests/Feature/ExampleTest.php` | Smoke test de requisição | 1 |
| `tests/Feature/Api/AuthTest.php` | Registro, login, rotas protegidas | 5 |
| `tests/Feature/Api/WalletTest.php` | Depósito, saque, saldo, extrato, dashboard | 9 |
| `tests/Feature/Api/WalletEdgeCasesTest.php` | Valores mínimos, decimais, SQL injection, paginação | 16 |

**Total:** ~40 testes

### Dicas
- O banco é resetado entre cada teste via `RefreshDatabase`
- Para inspecionar valores durante o teste, use `dump()` ou `dd()` na resposta
- O `phpunit.xml` já configura ambiente `testing` com cache array, session array e queue sync

---

## Frontend (Vue 3 + Vite)

**Nenhuma infraestrutura de teste configurada.** O frontend não possui test runner, configs ou arquivos de teste.

Se quiser adicionar, recomenda-se:

```bash
# Instalar vitest
docker exec -w /app frontend npm install -D vitest @vue/test-utils happy-dom
```

Depois adicionar ao `package.json`:
```json
"scripts": {
  "test": "vitest"
}
```

E criar `vitest.config.js`:
```js
import { defineConfig } from 'vitest/config'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  test: {
    environment: 'happy-dom',
  },
})
```
