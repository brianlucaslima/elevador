<?php
use App\Dominio\Elevador\Elevador;
it('inicia no térreo (0) e com fila vazia', function () {
    $elevador = new Elevador(10);

    expect($elevador->getAndarAtual())->toBe(0)
        ->and($elevador->getChamadosPendentes())->toBeEmpty();
});

it('não permite capacidade <= 0', function () {
    new Elevador(0);
})->throws(InvalidArgumentException::class);

it('não permite chamar andar negativo', function () {
    $elevador = new Elevador(10);
    $elevador->chamar(-1);
})->throws(InvalidArgumentException::class);

it('processa chamados em FIFO (enqueue/dequeue)', function () {
    $elevador = new Elevador(10);

    $elevador->chamar(3);
    $elevador->chamar(5);
    $elevador->chamar(2);

    $elevador->mover();
    expect($elevador->getAndarAtual())->toBe(3);

    $elevador->mover();
    expect($elevador->getAndarAtual())->toBe(5);

    $elevador->mover();
    expect($elevador->getAndarAtual())->toBe(2)
        ->and($elevador->getChamadosPendentes()->isEmpty())->toBeTrue();
});


it('mover com fila vazia gera exception', function () {
    $elevador = new Elevador(10);
    $elevador->mover();
})->throws(InvalidArgumentException::class);

it('getChamadosPendentes retorna clone (não altera a fila original)', function () {
    $elevador = new Elevador(10);
    $elevador->chamar(3);
    $elevador->chamar(5);

    $chamados = iterator_to_array($elevador->getChamadosPendentes());
    expect($chamados)->toHaveCount(2);

    array_pop($chamados);
    expect(iterator_to_array($elevador->getChamadosPendentes()))->toHaveCount(2);
});