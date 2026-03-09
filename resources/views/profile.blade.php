<x-app-layout>
    <div class="max-w-2xl mx-auto py-8">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-base font-medium text-gray-100">Profile Settings</h1>
            <p class="text-xs text-gray-500 mt-0.5">Manage your account information and security</p>
        </div>

        <div class="space-y-6">
            {{-- Update Profile --}}
            <div class="p-6 bg-gray-900 border border-gray-800 rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            {{-- Update Password --}}
            <div class="p-6 bg-gray-900 border border-gray-800 rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="p-6 bg-gray-900 border border-gray-800 rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>