<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ config('app.name') }}</title>
        <meta name="description" content="Simulador de elevador para teste de lógica de programação.">
        <link rel="icon" href="{{ asset('catalise-icon.png') }}" type="image/png">
        @vite('resources/css/app.css')
    </head>
    <body>
        {{ $slot }}
    </body>
</html>
