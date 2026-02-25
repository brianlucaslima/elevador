<div class="max-w-3xl mx-auto p-4">
    <h1 class="text-3xl font-bold underline text-center mb-4">
        Elevador - FIFO
    </h1>

    <div class="w-full grid grid-cols-1 md:grid-cols-3 gap-4 ">
        <x-dashboard-total-item label="Qtd. Chamadas" :value="count($filaChamados)" />
        <x-dashboard-total-item label="Andar Atual" :value="$andarAtual" />
        <x-dashboard-total-item label="Capacidade Total" :value="$capacidade" />
    </div>

    <div class="w-full mt-4">
        <input
                type="number"
                min="0"
                placeholder="Andar"
                class="w-full p-2 border rounded"
                wire:model="inputAndar"
        />

        @if($errors->any())
            <div class="w-full text-center">
                @foreach($errors->all() as $error)
                    <p class="text-red-500 text-sm mt-2">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <button
                type="button"
                class="mt-4 w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600"
                wire:click="chamar"
        >
            Chamar Elevador
        </button>

        <button
                type="button"
                class="w-full bg-green-500 text-white p-2 rounded hover:bg-green-600 mt-2"
                wire:click="mover"
        >
            Mover Elevador
        </button>
    </div>

    <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div class="w-full mt-4 bg-gray-100 p-4 rounded border h-[300px] max-h-[300px] overflow-y-auto">
            <h2 class="text-xl font-bold mb-2 text-center">Fila de Chamados</h2>
            <ol class="text-center  list-inside">
                @forelse($filaChamados as $chamado)
                    @php
                        $andarText = $chamado > 0 ? "Andar $chamado" : "Térreo";
                    @endphp
                    <li class="py-1 border-b last:border-b-0 border-gray-700 flex items-center justify-between gap-2">
                        <div>
                            <span class="font-semibold">{{ $loop->iteration }}º:</span>
                        </div>
                        <div class="text-left w-full ml-4">
                            {{ $andarText }}
                            @if($loop->first)
                                <span class="ml-2 text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-800">
                                    próximo
                                </span>
                            @endif
                        </div>

                    </li>
                @empty
                    <li class="text-gray-500">Nenhum chamado na fila.</li>
                @endforelse
            </ol>
        </div>

        <div class="w-full mt-4 bg-gray-100 p-4 rounded border h-[300px] max-h-[300px] overflow-y-auto">
            <h2 class="text-xl font-bold mb-2 text-center">Mensagens do Elevador</h2>

            <ul class="space-y-2">
                @forelse($logs as $log)
                    @php
                        $typeBadge = match($log['type'] ?? null) {
                            'success' => 'bg-green-100 text-green-700',
                            'error'   => 'bg-red-100 text-red-700',
                            default   => 'bg-gray-200 text-gray-700',
                        };

                        $actionBadge = match($log['action'] ?? null) {
                            'movimento' => 'bg-blue-100 text-blue-700',
                            'chamado'   => 'bg-purple-100 text-purple-700',
                            default     => 'bg-gray-100 text-gray-700',
                        };

                        $typeMessage = match($log['type'] ?? null) {
                            'success' => 'Sucesso',
                            'error'   => 'Erro',
                            default   => 'Info',
                        };

                        $actionMessage = match($log['action'] ?? null) {
                            'movimento' => 'Movimento',
                            'chamado'   => 'Chamado',
                            default     => 'Ação',
                        };
                    @endphp

                    <li class="bg-white rounded p-3 border">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-gray-500 text-xs">{{ $log['data'] ?? '' }}</span>

                            <div class="flex items-center gap-2">
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $typeBadge }}">
                                {{ $typeMessage ?? '—' }}
                            </span>

                                <span class="px-2 py-1 rounded text-xs font-medium {{ $actionBadge }}">
                                {{ $actionMessage ?? '—' }}
                            </span>
                            </div>
                        </div>

                        <p class="mt-2 text-sm text-gray-800">
                            {{ $log['message'] ?? '' }}
                        </p>
                    </li>
                @empty
                    <li class="text-gray-500 text-center">Nenhuma mensagem registrada.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>