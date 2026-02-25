<?php

namespace App\Console\Commands;

use App\Dominio\Elevador\Elevador;
use Illuminate\Console\Command;

class ElevadorDemoCommand extends Command
{
    protected $signature = 'elevador:demo {--capacidade=10}';

    protected $description = 'Demonstra o elevador com fila FIFO';

    public function handle() : int
    {
        $capacidade = (int) $this->option('capacidade', 8);
        $elevador = new Elevador($capacidade);

        $this->info("Elevador iniciando no térreo com capacidade de $capacidade pessoas.");

        $chamadas = rand(5, 15);
        $this->info("Gerando $chamadas chamadas aleatórias...");

        for ($i = 0; $i < $chamadas; $i++) {
            $andar = rand(0, 10);
            $this->info("Chamada para o andar $andar");
            $elevador->chamar($andar);
        }

        $this->info("Processando chamadas...");
        while (!$elevador->getChamadosPendentes()->isEmpty()) {
            $antes = $elevador->getAndarAtual();
            $elevador->mover();
            $depois = $elevador->getAndarAtual();

            if ($antes === $depois) {
                $this->info("Elevador já está no andar $depois, sem necessidade de movimento.");
            } else {
                $this->info("Elevador se moveu do andar $antes para o andar $depois");
            }
        }

        $this->warn("Todas as chamadas foram processadas. O elevador está agora no andar {$elevador->getAndarAtual()}.");
        return self::SUCCESS;
    }
}
