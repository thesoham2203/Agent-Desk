<!DOCTYPE html>
<html lang="en" class="bg-gray-950">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgentDesk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-950 text-gray-100 antialiased min-h-screen
             flex items-center justify-center p-4">

    <div class="w-full max-w-sm">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <span class="font-mono text-xl font-medium text-gray-100">
                agent<span class="text-indigo-400">desk</span>
            </span>
            <p class="text-xs text-gray-600 mt-1 tracking-wide">
                INTERNAL SUPPORT PLATFORM
            </p>
        </div>

        {{-- Card --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-8">
            {{ $slot }}
        </div>
    </div>

    @livewireScripts
</body>

</html>