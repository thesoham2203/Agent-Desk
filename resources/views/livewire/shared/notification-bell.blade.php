<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Whether the notification dropdown is visible.
     */
    public bool $showDropdown = false;

    /**
     * Get the unread notifications count for the current user.
     */
    #[\Livewire\Attributes\Computed]
    public function unreadCount(): int
    {
        return Auth::user()->unreadNotifications()->count();
    }

    /**
     * Get the latest 5 notifications for the current user.
     */
    #[\Livewire\Attributes\Computed]
    public function recentNotifications(): \Illuminate\Support\Collection
    {
        return Auth::user()->notifications()->latest()->limit(5)->get();
    }

    /**
     * Toggle the dropdown visibility.
     */
    public function toggleDropdown(): void
    {
        $this->showDropdown = !$this->showDropdown;
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllRead(): void
    {
        Auth::user()->unreadNotifications->each->markAsRead();
    }
}; ?>

<div class="relative" wire:poll.10000ms="$refresh">
    {{-- Bell button --}}
    <button wire:click="toggleDropdown" class="relative p-1.5 rounded-md text-gray-400
                   hover:text-gray-200 hover:bg-gray-800 transition-colors">

        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11
                     a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341
                     C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436
                     L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        @if($this->unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center
                             w-3.5 h-3.5 bg-red-500 text-white font-mono text-[9px]
                             rounded-full leading-none">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    @if($showDropdown)
        <div class="absolute right-0 top-full mt-2 w-72
                        bg-gray-900 border border-gray-700 rounded-lg
                        shadow-2xl shadow-black/50 z-50 overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between
                            px-4 py-2.5 border-b border-gray-800">
                <span class="text-xs font-medium text-gray-300">
                    Notifications
                </span>
                <button wire:click="markAllRead" class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors">
                    Mark all read
                </button>
            </div>

            {{-- List --}}
            <div class="max-h-64 overflow-y-auto divide-y divide-gray-800">
                @forelse($this->recentNotifications as $notification)
                    <a href="{{ $notification->data['url'] ?? '#' }}" class="block px-4 py-3 transition-colors
                                      hover:bg-gray-800
                                      {{ $notification->read_at ? '' : 'bg-indigo-950/40' }}">
                        <p class="text-xs text-gray-200 truncate mb-1">
                            {{ $notification->data['ticket_title'] ?? 'Notification' }}
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-[10px] text-gray-600 uppercase tracking-wide">
                                {{ str_replace('.', ' › ', $notification->data['type'] ?? '') }}
                            </span>
                            <span class="text-[10px] text-gray-600">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-8 text-center text-xs text-gray-600">
                        No notifications
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>