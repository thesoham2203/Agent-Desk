<!DOCTYPE html>
<html lang="en" class="bg-gray-950">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'AgentDesk' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-950 text-gray-100 antialiased min-h-screen">

    <livewire:layout.navigation />

    {{-- MAIN CONTENT --}}
    <main class="max-w-screen-xl mx-auto px-6 py-8">
        {{ $slot }}
    </main>

    @livewireScripts
</body>

</html>