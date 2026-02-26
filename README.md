# Elevador (FIFO) — Laravel 12 + Livewire 3

Implementação do desafio do **Elevador com fila FIFO (First-In, First-Out)**.

A fila representa os chamados de andares. O elevador processa os chamados **na ordem em que foram recebidos**, usando `SplQueue`.

## Visão geral

- **Domínio (DDD):** `app/Dominio/Elevador/Elevador.php`
  - Implementa a regra de negócio do elevador usando `SplQueue`.
- **UI (Livewire):** `app/Livewire/Elevador.php` + `resources/views/livewire/elevador.blade.php`
  - Interface simples para chamar e mover o elevador.
  - Mantém um *estado serializável* (`$elevadorState`) para sobreviver ao ciclo de hidratação do Livewire.
- **CLI (Artisan):** `app/Console/Commands/ElevadorDemoCommand.php`
  - Demonstração via terminal usando a classe de domínio.
- **Tests (Pest):** `tests/Domain` e `tests/Feature/Livewire`

---

## Requisitos

- Docker + Docker Compose

> Alternativamente, dá para rodar localmente com PHP 8.2+, Composer e Node, mas este projeto já está preparado para subir com Docker.

---

## Subindo o projeto com Docker

O container expõe:
- App: http://localhost:8050
- Vite (dev server): http://localhost:5173 (porta exposta no compose)

### 1) Build + up

```bash
docker compose up --build
```

Na primeira execução, o entrypoint (`docker/start.sh`) faz automaticamente:
- copia `.env.example` para `.env` se não existir
- `composer install`
- gera `APP_KEY`
- prepara SQLite em `database/database.sqlite`
- `npm install`
- `npm run build`
- inicia `php artisan serve --host 0.0.0.0 --port 8050`

### 2) Acessar

Abra:
- http://localhost:8050

### 3) Parar

```bash
docker compose down
```

---

## Executando comandos dentro do container

Para executar comandos Artisan, Composer ou Pest dentro do container:

```bash
docker compose exec app bash
```

A partir desse shell, você pode rodar os comandos abaixo.

---

## Rodando testes (Pest)

Este projeto usa **Pest** com Laravel.

### Rodar todo o suite

```bash
php artisan test
```

### Rodar apenas testes de domínio

```bash
php artisan test --testsuite=Domain
```

### Rodar apenas Livewire/Feature

```bash
php artisan test --testsuite=Feature
```

Arquivos principais:
- `tests/Domain/Elevador/ElevadorTest.php` (regras FIFO, validações, clone da fila)
- `tests/Feature/Livewire/ElevadorTest.php` (estado serializável + logs no componente)

---

## Demo via Artisan (CLI)

Existe um comando para demonstrar o funcionamento do elevador no terminal:

- **Command:** `elevador:demo`
- **Opção obrigatória no desafio:** `--capacidade`

Exemplo (capacidade 10):

```bash
php artisan elevador:demo --capacidade=10
```

O comando:
- instancia `App\Dominio\Elevador\Elevador`
- gera chamadas aleatórias
- processa a fila em FIFO chamando `mover()` até esvaziar

Código: `app/Console/Commands/ElevadorDemoCommand.php`

---

## Fluxo no Livewire (UI Web)

Rota principal:
- `GET /` → `App\Livewire\Elevador` (ver `routes/web.php`)

View:
- `resources/views/livewire/elevador.blade.php`

### Como o estado funciona (ponto importante)

O Livewire **não mantém instâncias de objetos PHP** entre interações do usuário. Cada clique (`wire:click`) é uma nova requisição que reidrata o componente a partir de propriedades públicas serializáveis.

Por isso, o componente mantém um “memento” serializável:

- `public array $elevadorState = ['capacidade' => 10, 'andarAtual' => 0, 'filaChamados' => []];`

E a cada ação:

1. **Reconstitui** o agregado do domínio (`reconstituirDominio()`), criando um `ElevadorDomain` e re-enfileirando os itens do estado.
2. Executa a regra (ex.: `chamar()` ou `mover()`) no agregado.
3. **Persiste de volta** no `elevadorState` (`persistirEstado()`), convertendo `SplQueue` para array.

### Ações

- **Chamar Elevador** (`chamar()`): valida entrada, chama `ElevadorDomain::chamar($andar)` e atualiza a fila.
- **Mover Elevador** (`mover()`): chama `ElevadorDomain::mover()`, atualiza o andar atual e remove o próximo item da fila.

### Logs

O componente mantém um buffer simples de logs:
- `public array $logs` (mantém apenas os 10 mais recentes)

Isso é testado em `tests/Feature/Livewire/ElevadorTest.php`.

---

## Domínio (classe Elevador)

Arquivo: `app/Dominio/Elevador/Elevador.php`

Atributos:
- `$filaChamados` (`SplQueue`) — garante FIFO
- `$andarAtual` (inicia em `0`)
- `$capacidade` (validada no construtor; não entra na lógica de movimento neste desafio)

Métodos:
- `__construct(int $capacidade)`
- `chamar(int $andar)` (valida `andar >= 0`, enfileira)
- `mover()` (dequeue + atualiza `andarAtual`)
- `getAndarAtual(): int`
- `getChamadosPendentes(): SplQueue` (retorna clone)

---

## Estrutura relevante

- `app/Dominio/Elevador/Elevador.php`
- `app/Console/Commands/ElevadorDemoCommand.php`
- `app/Livewire/Elevador.php`
- `resources/views/livewire/elevador.blade.php`
- `docker-compose.yml`, `Dockerfile`, `docker/start.sh`
- `tests/Domain/Elevador/ElevadorTest.php`
- `tests/Feature/Livewire/ElevadorTest.php`
