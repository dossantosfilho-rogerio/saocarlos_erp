<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard · São Carlos ERP</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-[#F5F1E8] min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-3xl font-bold text-[#2D5A1B]">Bem-vindo ao São Carlos ERP</h1>
        <p class="text-[#4A6B3A] mt-2">{{ auth()->user()->name }}</p>
        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button type="submit" class="px-4 py-2 bg-[#2D5A1B] text-white rounded-lg text-sm hover:bg-[#245016] transition">
                Sair
            </button>
        </form>
    </div>
</body>
</html>
