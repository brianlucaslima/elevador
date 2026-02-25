<?php

namespace App\Dominio\Elevador;

use InvalidArgumentException;
use SplQueue;

class Elevador
{
    private int $capacidade;
    private int $andarAtual;
    private SplQueue $filaChamados;

    public function __construct(int $capacidade)
    {
        if ($capacidade <= 0) {
            throw new InvalidArgumentException('Capacidade deve ser maior que zero.');
        }
        $this->capacidade   = $capacidade;
        $this->andarAtual   = 0;
        $this->filaChamados = new SplQueue();
    }


    public function chamar(int $andar) : void
    {
        if ($andar < 0) throw new InvalidArgumentException("Andar inválido: $andar. O andar deve ser maior ou igual a 0 para o térreo.");
        $this->filaChamados->enqueue($andar);
    }

    public function mover() : void
    {
        if ($this->filaChamados->isEmpty()) throw new InvalidArgumentException("Não há chamados pendentes para mover o elevador.");

        $proximoAndar = $this->filaChamados->dequeue();
        $this->andarAtual = $proximoAndar;
    }

    public function getAndarAtual() : int
    {
        return $this->andarAtual;
    }

    public function getChamadosPendentes() : SplQueue
    {
        return clone $this->filaChamados;
    }

    public function hidratarAndarAtual(int $andarAtual)
    {
        if ($andarAtual < 0) throw new InvalidArgumentException("Andar inválido: $andarAtual. O andar deve ser maior ou igual a 0 para o térreo.");
        $this->andarAtual = $andarAtual;
    }

}