<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

new class extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login(): void
    {
        $this->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        session()->regenerate();

        $this->redirect(route('dashboard'), navigate: true);
    }
};
?>

<div class="min-h-screen flex items-center justify-center bg-[#F5F1E8]"
     style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%232D5A1B\' fill-opacity=\'0.04\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">

    <div class="w-full max-w-md px-6 py-10">

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-[#e8e2d6]">

            {{-- Header verde --}}
            <div class="bg-[#2D5A1B] px-8 pt-10 pb-8 flex flex-col items-center">
                {{-- Logo --}}
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ asset('images/logo.png') }}" alt="São Carlos" class="h-24 w-auto mb-4 drop-shadow-lg">
                @else
                    {{-- Fallback: ícone SVG de porco estilizado --}}
                    <div class="mb-4 bg-white/10 rounded-full p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="10" r="7" stroke="white" stroke-width="1.5" fill="white" fill-opacity="0.15"/>
                            <circle cx="9.5" cy="9" r="1" fill="white"/>
                            <circle cx="14.5" cy="9" r="1" fill="white"/>
                            <path d="M9.5 12.5 Q12 14 14.5 12.5" stroke="white" stroke-width="1.2" stroke-linecap="round"/>
                            <path d="M3 7 Q1 5 2 3 Q4 5 5 7" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M21 7 Q23 5 22 3 Q20 5 19 7" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                @endif

                <h1 class="text-white text-2xl font-bold tracking-widest uppercase">São Carlos</h1>
                <p class="text-[#A8D090] text-xs tracking-wider uppercase mt-1">Frigorífico · Produtos Cárneos</p>
                <div class="mt-4 w-12 border-t border-white/20"></div>
                <p class="text-white/70 text-sm mt-3">Sistema ERP</p>
            </div>

            {{-- Formulário --}}
            <div class="px-8 py-8">
                <h2 class="text-[#1A3A0A] text-lg font-semibold mb-6 text-center">Acesse sua conta</h2>

                <form wire:submit="login" class="space-y-5">

                    {{-- E-mail --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-[#3A5C2A] mb-1.5">
                            E-mail
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="h-4 w-4 text-[#7A9E6A]" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                            </div>
                            <input
                                wire:model="email"
                                id="email"
                                type="email"
                                autocomplete="email"
                                required
                                placeholder="seu@email.com"
                                class="w-full pl-9 pr-4 py-2.5 border border-[#C8DDB8] rounded-lg text-sm text-[#1A3A0A] placeholder-[#9DBD8A] bg-[#FAFDF7] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/30 focus:border-[#2D5A1B] transition"
                            >
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Senha --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-[#3A5C2A] mb-1.5">
                            Senha
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="h-4 w-4 text-[#7A9E6A]" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z" />
                                </svg>
                            </div>
                            <input
                                wire:model="password"
                                id="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                placeholder="••••••••"
                                class="w-full pl-9 pr-4 py-2.5 border border-[#C8DDB8] rounded-lg text-sm text-[#1A3A0A] placeholder-[#9DBD8A] bg-[#FAFDF7] focus:outline-none focus:ring-2 focus:ring-[#2D5A1B]/30 focus:border-[#2D5A1B] transition"
                            >
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Lembrar --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input
                                wire:model="remember"
                                type="checkbox"
                                class="h-4 w-4 rounded border-[#C8DDB8] text-[#2D5A1B] focus:ring-[#2D5A1B]/30 accent-[#2D5A1B]"
                            >
                            <span class="text-sm text-[#4A6B3A]">Lembrar-me</span>
                        </label>
                    </div>

                    {{-- Botão --}}
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="w-full flex items-center justify-center gap-2 bg-[#2D5A1B] hover:bg-[#245016] active:bg-[#1C3E12] text-white font-semibold py-3 px-4 rounded-lg transition duration-150 shadow-md hover:shadow-lg disabled:opacity-60 disabled:cursor-not-allowed mt-2"
                    >
                        <span wire:loading.remove>Entrar</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Aguarde...
                        </span>
                    </button>

                </form>
            </div>

            {{-- Rodapé --}}
            <div class="bg-[#F5F1E8] border-t border-[#E0D8C8] px-8 py-4 text-center">
                <p class="text-xs text-[#8A7A60]">
                    © {{ date('Y') }} Frigorífico São Carlos · Todos os direitos reservados
                </p>
            </div>
        </div>

    </div>
</div>