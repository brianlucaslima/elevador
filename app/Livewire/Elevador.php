<?php

namespace App\Livewire;

use App\Dominio\Elevador\Elevador as ElevadorDomain;
use Exception;
use Illuminate\View\View;
use Livewire\Component;

class Elevador extends Component
{
    /** @var int */
    public int $capacidade = 10;

    /** @var int|null */
    public ?int $inputAndar = null;

    /** @var array<int, array{data:string, message:string, type:string, action:string}> */
    public array $logs = [];

    /**
     * Estado serializável (Memento) para o Livewire.
     * Livewire consegue hidratar/desidratar isso entre requests.
     *
     * @var array{capacidade:int, andarAtual:int, filaChamados:int[]}
     */
    public array $elevadorState = [
        'capacidade' => 10,
        'andarAtual' => 0,
        'filaChamados' => [],
    ];

    private function reconstituirDominio(): ElevadorDomain
    {
        $capacidade = (int) ($this->elevadorState['capacidade'] ?? $this->capacidade);
        $elevador = new ElevadorDomain($capacidade);

        $elevador->hidratarAndarAtual((int) ($this->elevadorState['andarAtual'] ?? 0));

        foreach ((array) ($this->elevadorState['filaChamados'] ?? []) as $andar) {
            $elevador->chamar((int) $andar);
        }

        return $elevador;
    }

    private function persistirEstado(ElevadorDomain $elevador): void
{
    $this->elevadorState = [
        'capacidade' => (int) ($this->elevadorState['capacidade'] ?? $this->capacidade),
        'andarAtual' => $elevador->getAndarAtual(),
        'filaChamados' => array_map('intval', iterator_to_array($elevador->getChamadosPendentes(), false)),
    ];
}

    public function chamar(): void
    {
        $this->validate([
            'inputAndar' => 'required|integer',
        ], [
            'inputAndar.required' => 'O campo andar é obrigatório.',
            'inputAndar.integer' => 'O campo andar deve ser um número inteiro.',
            'inputAndar.min' => 'O campo andar deve ser maior ou igual a 0 para o térreo.',
        ]);

        try {
            $elevador = $this->reconstituirDominio();
            $elevador->chamar((int) $this->inputAndar);
            $this->persistirEstado($elevador);
            $message = "Chamado para o andar {$this->inputAndar} registrado.";
            $this->reset('inputAndar');
            $this->resetErrorBag();
        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->addError('inputAndar', $message);
        }

        $this->registraLog(isset($e) ? 'error' : 'success', 'chamado', $message);
    }

    public function mover(): void
    {
        try {
            $elevador = $this->reconstituirDominio();
            $de = $elevador->getAndarAtual();
            $elevador->mover();
            $this->persistirEstado($elevador);

            $message = $de === $elevador->getAndarAtual()
                ? "Elevador permaneceu no andar {$de}."
                : "Elevador se moveu do andar $de para o andar {$elevador->getAndarAtual()}";
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        $this->registraLog(
             isset($e) ? 'error' : 'success',
            'movimento',
            $message,
        );
    }

    private function registraLog(string $type, string $action, string $message): void
    {
        array_unshift($this->logs, [
            'data' => now()->format('d/m/y H:i:s'),
            'message' => $message,
            'type' => $type,
            'action' => $action,
        ]);

        $this->logs = array_slice($this->logs, 0, 10);
    }
    public function render(): View
    {
        return view('livewire.elevador', [
            'filaChamados' => $this->elevadorState['filaChamados'] ?? [],
            'andarAtual' => $this->elevadorState['andarAtual'] ?? 0,
        ]);
    }
}
