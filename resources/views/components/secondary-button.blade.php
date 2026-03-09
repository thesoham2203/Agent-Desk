<button {{ $attributes->merge(['type' => 'button', 'class' => 'bg-gray-800 border border-gray-700 hover:bg-gray-700 text-gray-300 text-xs font-medium px-4 py-2 rounded-md transition-colors disabled:opacity-50']) }}>
    {{ $slot }}
</button>