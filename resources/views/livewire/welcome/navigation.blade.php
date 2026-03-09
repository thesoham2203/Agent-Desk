<nav class="flex gap-4">
    @auth
        <a href="{{ url('/dashboard') }}" class="text-xs font-medium text-gray-400 hover:text-gray-100 transition-colors">
            Dashboard
        </a>
    @else
        <a href="{{ route('login') }}" class="text-xs font-medium text-gray-400 hover:text-gray-100 transition-colors">
            Sign In
        </a>

        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="text-xs font-medium bg-indigo-600 text-white px-3 py-1.5 rounded-md
                              hover:bg-indigo-500 transition-colors">
                Get Started
            </a>
        @endif
    @endauth
</nav>