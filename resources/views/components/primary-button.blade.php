<button {{ $attributes->merge(['type' => 'submit', 'class' => 'bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-medium px-4 py-2 rounded-md transition-colors disabled:opacity-50']) }}>
    {{ $slot }}
</button>