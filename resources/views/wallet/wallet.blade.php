<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Minha Carteira') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div
                    class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-900/50 dark:border-green-600 dark:text-green-300"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div
                    class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative dark:bg-red-900/50 dark:border-red-600 dark:text-red-300"
                    role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">Saldo Disponível</h3>
                            <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">
                                R$ {{ number_format($wallet?->balance, 2, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <form method="POST" action="{{ route('wallet.transfer') }}" class="space-y-4">
                                    @csrf
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Transferir</h3>

                                    <div>
                                        <x-input-label for="payee_id" :value="__('Selecione o Destinatário')"/>

                                        <select id="payee_id" name="payee_id"
                                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                                required>
                                            <option value="" disabled selected>Selecione um usuário...</option>

                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>

                                        <x-input-error :messages="$errors->get('payee_id')" class="mt-2"/>
                                    </div>
                                    <div>
                                        <x-input-label for="value_transfer" :value="__('Valor (R$)')"/>
                                        <x-text-input id="value_transfer" class="block mt-1 w-full" type="text"
                                                      name="value" required placeholder="50,00"/>
                                        <x-input-error :messages="$errors->get('value')" class="mt-2"/>
                                    </div>

                                    <x-primary-button class="w-full justify-center">
                                        {{ __('Transferir') }}
                                    </x-primary-button>
                                </form>

                                <form method="POST" action="{{ route('wallet.deposit') }}" class="space-y-4">
                                    @csrf
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Depositar</h3>
                                    <div>
                                        <x-input-label for="deposit_payee_id" :value="__('Selecione o Destinatário')"/>

                                        <select id="deposit_payee_id" name="deposit_payee_id"
                                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                                required>
                                            <option value="" disabled selected>Selecione um usuário...</option>

                                            <option value="{{ auth()->id() }}">{{ auth()->user()->name }}</option>

                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>

                                        <x-input-error :messages="$errors->get('deposit_payee_id')" class="mt-2"/>
                                    </div>

                                    <div>
                                        <x-input-label for="value_deposit" :value="__('Valor a Depositar (R$)')"/>
                                        <x-text-input id="value_deposit" class="block mt-1 w-full" type="text"
                                                      name="value" required placeholder="100,00"/>
                                        <x-input-error :messages="$errors->get('value')" class="mt-2"/>
                                    </div>
                                    <x-secondary-button type="submit" class="w-full justify-center">
                                        {{ __('Depositar') }}
                                    </x-secondary-button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Atividade Recente
                            </h3>
                            <ul class="space-y-4">
                                @forelse ($transactions as $transaction)
                                    @php
                                        // Verifica se a transação é de entrada ou saída para o usuário logado
                                        $isReceived = $transaction->payee_wallet_id == $wallet?->id;
                                        $isDeposit = $isReceived && $transaction->type === 'deposit';
                                        $isReversed = $transaction->type === 'reversal';
                                    @endphp
                                    <li class="flex items-center space-x-3">
                                        <div
                                            class="p-2 rounded-full @if($isDeposit) bg-sky-100 dark:bg-sky-900/50 @elseif($isReceived) bg-green-100 dark:bg-green-900/50 @else bg-red-100 dark:bg-red-900/50 @endif">
                                            @if($isDeposit)
                                                <svg class="w-5 h-5 text-sky-600 dark:text-sky-400" fill="none"
                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @elseif($isReceived)
                                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none"
                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none"
                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                @if($isDeposit)
                                                    Depósito realizado
                                                @elseif($isReceived)
                                                    Recebido de {{ $transaction->payerWallet->user->name }}
                                                @elseif($isReversed)
                                                    Estornado
                                                @else
                                                    Enviado para {{ $transaction->payeeWallet->user->name }}
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <p class="text-sm font-semibold @if($isReceived) text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif">
                                            {{ $isReceived ? '+' : '-' }}
                                            R$ {{ number_format($transaction->value, 2, ',', '.') }}
                                        </p>

                                        @if (in_array($transaction->type, ['transfer', 'deposit']) && !$transaction->reversal && !$transaction->reversed_transaction_id && $transaction->payeeWallet->user->id === auth()->id() && $transaction->payeeWallet->id !== $transaction->payerWallet->id)
                                            <form method="POST"
                                                  action="{{ route('transaction.reverse', $transaction->id) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="text-xs text-blue-600 hover:underline dark:text-blue-400">
                                                    Estornar
                                                </button>
                                            </form>
                                        @endif
                                    </li>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma atividade recente
                                        encontrada.</p>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
