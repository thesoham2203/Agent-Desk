<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
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
    public function notifications(): \Illuminate\Support\Collection
    {
        return Auth::user()->notifications()->latest()->limit(5)->get();
    }

    /**
     * Mark a specific notification as read.
     *
     * @param string $id The notification UUID
     */
    public function markAsRead(string $id): void
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->each->markAsRead();
    }
}; ?>

<div class="relative ms-3" wire:poll.30s>
    <x-dropdown align="right" width="w-80" contentClasses="py-0 bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-100">
        <x-slot name="trigger">
            <button class="relative p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-full focus:outline-none transition duration-150 ease-in-out">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>

                @if($this->unreadCount > 0)
                    <span class="absolute top-1.5 right-1.5 flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex items-center justify-center rounded-full h-4 w-4 bg-red-600 text-[10px] font-bold text-white shadow-sm">
                            {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
                        </span>
                    </span>
                @endif
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="flex items-center justify-between px-4 py-3 bg-indigo-50/50 border-b border-gray-100">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-900">{{ __('Recent Notifications') }}</span>
                @if($this->unreadCount > 0)
                    <button wire:click="markAllAsRead" class="text-[10px] font-semibold text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-tight">
                        {{ __('Clear All') }}
                    </button>
                @endif
            </div>

            <div class="max-h-96 overflow-y-auto">
                @forelse($this->notifications as $notification)
                    <div class="relative border-b border-gray-50 last:border-0">
                        <a href="{{ $notification->data['url'] ?? '#' }}"
                           wire:click="markAsRead('{{ $notification->id }}')"
                           class="group block px-4 py-3 transition duration-150 ease-in-out {{ $notification->unread() ? 'bg-indigo-50/20 hover:bg-indigo-50/40' : 'hover:bg-gray-50' }}">
                            
                            <div class="flex gap-3">
                                <div @class([
                                    'shrink-0 h-10 w-10 flex items-center justify-center rounded-full',
                                    'bg-indigo-100 text-indigo-600' => !str_contains($notification->data['type'] ?? '', 'overdue'),
                                    'bg-red-100 text-red-600' => str_contains($notification->data['type'] ?? '', 'overdue'),
                                ])>
                                    @if(str_contains($notification->data['type'] ?? '', 'assigned'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    @elseif(str_contains($notification->data['type'] ?? '', 'replied'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                                    @elseif(str_contains($notification->data['type'] ?? '', 'resolved'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    @else
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    @endif
                                </div>
                                
                                <div class="grow min-w-0">
                                    <div class="flex items-center justify-between mb-0.5">
                                        <span class="text-[10px] font-semibold text-indigo-500 uppercase tracking-widest">
                                            {{ str_replace('.', ' ', $notification->data['type'] ?? 'Notification') }}
                                        </span>
                                        <span class="shrink-0 text-[10px] text-gray-400 font-medium ms-2">
                                            {{ $notification->created_at->diffForHumans(short: true) }}
                                        </span>
                                    </div>
                                    <div @class(['text-sm line-clamp-1 mb-0.5', 'font-bold text-gray-900' => $notification->unread(), 'font-medium text-gray-700' => !$notification->unread()])>
                                        {{ $notification->data['ticket_title'] ?? 'Ticket Update' }}
                                    </div>
                                    <p @class(['text-xs line-clamp-2', 'text-gray-600 font-medium' => $notification->unread(), 'text-gray-500' => !$notification->unread()])>
                                        @if($notification->data['type'] === 'ticket.assigned')
                                            {{ __('Assigned by :name', ['name' => $notification->data['assigned_by']]) }}
                                        @elseif($notification->data['type'] === 'ticket.replied')
                                            {{ __('New reply from :name', ['name' => $notification->data['author_name'] ?? 'requester']) }}
                                        @elseif($notification->data['type'] === 'ticket.overdue')
                                            {{ __('SLA Breach! Overdue for response.') }}
                                        @else
                                            {{ __('Status updated for ticket #:id', ['id' => $notification->data['ticket_id']]) }}
                                        @endif
                                    </p>
                                </div>
                                
                                @if($notification->unread())
                                    <div class="shrink-0 h-2 w-2 rounded-full bg-indigo-500 self-center"></div>
                                @endif
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 px-4 text-center">
                        <div class="bg-gray-50 p-4 rounded-full mb-3 text-gray-300">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                        </div>
                        <p class="text-sm font-medium text-gray-500 italic">{{ __('Inbox zero. You are all caught up!') }}</p>
                    </div>
                @endforelse
            </div>

            @if($this->notifications->isNotEmpty())
                <div class="bg-gray-50/80 px-4 py-2 border-t border-gray-100 flex justify-center">
                    <span class="text-[10px] uppercase font-bold tracking-widest text-gray-400">{{ __('Showing latest 5 entries') }}</span>
                </div>
            @endif
        </x-slot>
    </x-dropdown>
</div>
