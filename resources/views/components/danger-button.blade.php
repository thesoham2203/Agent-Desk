<button {{ $attributes->merge(['type' => 'submit', 'class' => 'bg-red-700/80 hover:bg-red-600 text-white text-xs font-medium px-4 py-2 rounded-md transition-colors disabled:opacity-50']) }}>
    {{ $slot }}
</button>