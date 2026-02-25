<?php

use App\Livewire\Elevador;

it('permite inputAndar com valor negativo', function () {
    \Livewire\Livewire::test(Elevador::class)
        ->set('inputAndar', -1)
        ->call('chamar')
        ->assertHasNoErrors(['inputAndar' => 'min']);
});

it('enfileira chamados no state mantendo FIFO', function () {
    \Livewire\Livewire::test(Elevador::class)
        ->set('inputAndar', 3)
        ->call('chamar')
        ->set('inputAndar', 5)
        ->call('chamar')
        ->set('inputAndar', 2)
        ->call('chamar')
        ->assertSet('elevadorState.filaChamados', [3, 5, 2]);
});

it('move na ordem do FIFO (estado + fila atualizadas)', function () {
    $teste = \Livewire\Livewire::test(Elevador::class)
        ->set('inputAndar', 3)
        ->call('chamar')
        ->set('inputAndar', 5)
        ->call('chamar')
        ->set('inputAndar', 2)
        ->call('chamar');

    $teste->call('mover')
        ->assertSet('elevadorState.andarAtual', 3)
        ->assertSet('elevadorState.filaChamados', [5, 2]);

    $teste->call('mover')
        ->assertSet('elevadorState.andarAtual', 5)
        ->assertSet('elevadorState.filaChamados', [2]);

    $teste->call('mover')
        ->assertSet('elevadorState.andarAtual', 2)
        ->assertSet('elevadorState.filaChamados', []);
});

it('mover com fila vazia registra log de erro', function () {
    \Livewire\Livewire::test(Elevador::class)
        ->call('mover')
        ->assertSet('logs.0.type', 'error')
        ->assertSet('logs.0.message', 'Não há chamados pendentes para mover o elevador.');
});

it('logs mantem apenas os 10 mais recentes', function () {
    $teste = \Livewire\Livewire::test(Elevador::class);

    for ($i = 0; $i < 12; $i++) {
        $teste->call('mover');
    }

    $teste->assertCount('logs', 10)
        ->assertSet('logs.0.message', 'Não há chamados pendentes para mover o elevador.')
        ->assertSet('logs.9.message', 'Não há chamados pendentes para mover o elevador.');
});