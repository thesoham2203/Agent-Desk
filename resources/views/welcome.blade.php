<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-gray-950">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AgentDesk — Professional Support System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-950 text-gray-100 selection:bg-indigo-500 selection:text-white">
    <div class="relative min-h-screen flex flex-col items-center overflow-hidden">

        {{-- Background Gradient --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-indigo-900/10 blur-[100px] rounded-full">
            </div>
            <div class="absolute top-[20%] -right-[10%] w-[30%] h-[50%] bg-indigo-800/5 blur-[120px] rounded-full">
            </div>
        </div>

        {{-- Top Nav --}}
        <nav class="relative w-full max-w-7xl px-6 py-8 flex justify-between items-center z-10">
            <div class="font-mono text-lg font-medium tracking-tight">
                agent<span class="text-indigo-400">desk</span>
            </div>

            @if (Route::has('login'))
                <livewire:welcome.navigation />
            @endif
        </nav>

        {{-- Hero Section --}}
        <main class="relative flex-1 flex flex-col items-center justify-center px-6 text-center z-10">
            <div class="max-w-4xl">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-950/40 border border-indigo-500/20 text-indigo-400 text-[10px] font-mono uppercase tracking-widest mb-6 translate-y-[-10px] opacity-0 animate-[fade-down_0.8s_ease_forwards]">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                    </span>
                    Next Generation Helpdesk
                </div>

                <h1
                    class="text-5xl md:text-7xl font-semibold tracking-tight text-white mb-6 leading-[1.1] translate-y-[20px] opacity-0 animate-[fade-up_0.8s_ease_0.2s_forwards]">
                    The calmest way to<br>
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-white to-gray-400">scale
                        your support.</span>
                </h1>

                <p
                    class="text-lg text-gray-400 max-w-2xl mx-auto mb-10 translate-y-[20px] opacity-0 animate-[fade-up_0.8s_ease_0.4s_forwards]">
                    AgentDesk combines intelligent triage, beautiful density, and a focused interface to help your team
                    resolve tickets without the noise.
                </p>

                <div
                    class="flex flex-col sm:flex-row items-center justify-center gap-4 translate-y-[20px] opacity-0 animate-[fade-up_0.8s_ease_0.6s_forwards]">
                    <a href="{{ route('register') }}"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white font-medium px-8 py-3 rounded-lg transition-all hover:scale-[1.02] active:scale-[0.98]">
                        Start Free Trial
                    </a>
                    <a href="https://github.com/thesoham2203/Agent-Desk" target="_blank"
                        class="bg-gray-900 hover:bg-gray-800 text-gray-300 border border-gray-800 font-medium px-8 py-3 rounded-lg transition-all">
                        View Documentation
                    </a>
                </div>
            </div>

            {{-- Feature Grid --}}
            <div
                class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mt-24 mb-24 translate-y-[20px] opacity-0 animate-[fade-up_0.8s_ease_0.8s_forwards]">
                <div class="p-6 bg-gray-900/40 border border-gray-800 rounded-2xl text-left">
                    <div class="text-indigo-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-white font-medium mb-2">AI-Powered Triage</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Automated classification and priority scoring for
                        every incoming ticket.</p>
                </div>
                <div class="p-6 bg-gray-900/40 border border-gray-800 rounded-2xl text-left">
                    <div class="text-indigo-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-white font-medium mb-2">SLA Management</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Stay on top of deadlines with visual age indicators
                        and proactive notifications.</p>
                </div>
                <div class="p-6 bg-gray-900/40 border border-gray-800 rounded-2xl text-left">
                    <div class="text-indigo-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                        </svg>
                    </div>
                    <h3 class="text-white font-medium mb-2">Focused Workspace</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">A professional dark-themed interface designed for
                        high-density support work.</p>
                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer
            class="w-full max-w-7xl px-6 py-12 border-t border-gray-900 flex flex-col md:flex-row justify-between items-center gap-6 z-10">
            <div class="text-xs text-gray-600">
                &copy; {{ date('Y') }} AgentDesk. Built with Laravel 12 & Livewire.
            </div>
            <div class="flex gap-6 text-xs text-gray-500">
                <span>v{{ Illuminate\Foundation\Application::VERSION }}</span>
                <span>PHP v{{ PHP_VERSION }}</span>
            </div>
        </footer>
    </div>

    <style>
        @keyframes fade-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-down {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</body>

</html>