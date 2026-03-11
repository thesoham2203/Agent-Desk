<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-screen-xl mx-auto px-6 h-14 flex items-center justify-between gap-6">

        <div class="flex items-center gap-6">
            {{-- Logo --}}
            <a href="{{ route(auth()->user()->role->dashboardRoute()) }}"
               wire:navigate
               class="font-mono text-sm font-medium text-gray-100 no-underline shrink-0">
                agent<span class="text-indigo-400">desk</span>
            </a>

            {{-- Divider (hidden on mobile) --}}
            <div class="hidden md:block h-4 w-px bg-gray-700 shrink-0"></div>

            {{-- Desktop Role-based nav links --}}
            <div class="hidden md:flex items-center gap-1 overflow-x-auto">
                @php $role = auth()->user()->role; @endphp

                @if($role === \App\Enums\UserRole::Admin)
                    @foreach([
                        ['route' => 'admin.dashboard',   'label' => 'Dashboard'],
                        ['route' => 'admin.categories',  'label' => 'Categories'],
                        ['route' => 'admin.macros',       'label' => 'Macros'],
                        ['route' => 'admin.sla',          'label' => 'SLA'],
                        ['route' => 'admin.kb-articles',  'label' => 'KB Articles'],
                        ['route' => 'admin.audit-log',    'label' => 'Audit Log'],
                        ['route' => 'admin.ai-runs',      'label' => 'AI Runs'],
                    ] as $navLink)
                        <a href="{{ route($navLink['route']) }}"
                           wire:navigate
                           class="text-xs px-3 py-1.5 rounded-md transition-colors whitespace-nowrap
                                  {{ request()->routeIs($navLink['route'])
                                      ? 'bg-gray-800 text-gray-100'
                                      : 'text-gray-400 hover:text-gray-200 hover:bg-gray-800' }}">
                            {{ $navLink['label'] }}
                        </a>
                    @endforeach

                @elseif($role === \App\Enums\UserRole::Agent)
                    <a href="{{ route('agent.my-tickets') }}"
                       wire:navigate
                       class="text-xs px-3 py-1.5 rounded-md transition-colors
                              {{ request()->routeIs('agent.my-tickets')
                                  ? 'bg-gray-800 text-gray-100'
                                  : 'text-gray-400 hover:text-gray-200 hover:bg-gray-800' }}">
                        My Tickets
                    </a>
                    <a href="{{ route('agent.queue') }}"
                       wire:navigate
                       class="text-xs px-3 py-1.5 rounded-md transition-colors
                              {{ request()->routeIs('agent.queue')
                                  ? 'bg-gray-800 text-gray-100'
                                  : 'text-gray-400 hover:text-gray-200 hover:bg-gray-800' }}">
                        Triage Queue
                    </a>

                @else
                    <a href="{{ route('requester.tickets.index') }}"
                       wire:navigate
                       class="text-xs px-3 py-1.5 rounded-md transition-colors
                              {{ request()->routeIs('requester.tickets.index')
                                  ? 'bg-gray-800 text-gray-100'
                                  : 'text-gray-400 hover:text-gray-200 hover:bg-gray-800' }}">
                        My Tickets
                    </a>
                    <a href="{{ route('requester.tickets.create') }}"
                       wire:navigate
                       class="text-xs px-3 py-1.5 rounded-md transition-colors
                              {{ request()->routeIs('requester.tickets.create')
                                  ? 'bg-gray-800 text-gray-100'
                                  : 'text-gray-400 hover:text-gray-200 hover:bg-gray-800' }}">
                        New Ticket
                    </a>
                @endif
            </div>
        </div>

        {{-- Right side --}}
        <div class="flex items-center gap-3 shrink-0">
            @livewire('shared.notification-bell')
            
            <div class="hidden md:flex items-center gap-3">
                <a href="{{ route('profile') }}" 
                   wire:navigate
                   class="text-xs text-gray-500 hover:text-gray-300 transition-colors border-l border-gray-700 pl-3">
                    {{ auth()->user()->name }}
                </a>
                <button wire:click="logout"
                        class="text-xs text-gray-500 hover:text-gray-300 transition-colors cursor-pointer">
                    Sign out
                </button>
            </div>

            {{-- Mobile menu button --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen" 
                    class="md:hidden text-gray-500 hover:text-gray-300 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display: none;"
         class="md:hidden bg-gray-900 border-b border-gray-800 px-6 py-4 space-y-3">
        @php $role = auth()->user()->role; @endphp
        
        @if($role === \App\Enums\UserRole::Admin)
            @foreach(['admin.dashboard' => 'Dashboard', 'admin.categories' => 'Categories', 'admin.macros' => 'Macros', 'admin.sla' => 'SLA', 'admin.kb-articles' => 'KB Articles', 'admin.audit-log' => 'Audit Log', 'admin.ai-runs' => 'AI Runs'] as $route => $label)
                <a href="{{ route($route) }}" wire:navigate class="block text-sm text-gray-400 hover:text-gray-200">{{ $label }}</a>
            @endforeach
        @elseif($role === \App\Enums\UserRole::Agent)
            <a href="{{ route('agent.my-tickets') }}" wire:navigate class="block text-sm text-gray-400 hover:text-gray-200">My Tickets</a>
            <a href="{{ route('agent.queue') }}" wire:navigate class="block text-sm text-gray-400 hover:text-gray-200">Triage Queue</a>
        @else
            <a href="{{ route('requester.tickets.index') }}" wire:navigate class="block text-sm text-gray-400 hover:text-gray-200">My Tickets</a>
            <a href="{{ route('requester.tickets.create') }}" wire:navigate class="block text-sm text-gray-400 hover:text-gray-200">New Ticket</a>
        @endif

        <div class="pt-4 border-t border-gray-800 flex items-center justify-between">
            <a href="{{ route('profile') }}" wire:navigate class="text-xs text-gray-500">{{ auth()->user()->name }}</a>
            <button wire:click="logout" class="text-xs text-gray-500 cursor-pointer">Sign out</button>
        </div>
    </div>
</nav>