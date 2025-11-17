<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'MSU–IIT Energy Monitoring System') }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body class="min-h-screen flex flex-col bg-white text-gray-900">

  {{-- Top Status Bar --}}
  <header class="bg-maroon text-white flex items-center justify-between px-6 py-2 shadow">
    <div class="font-bold tracking-wide text-lg">
      MSU–IIT Energy Monitoring System
    </div>

    <div class="flex items-center gap-3 relative">
      <div id="clock" class="opacity-90 font-mono"></div>

      {{-- User Info & Dropdown --}}
      @auth
        <div x-data="{ open: false }" class="relative">
          <button @click="open = !open" class="flex items-center gap-2 bg-white text-maroon px-3 py-1 text-sm rounded hover:bg-maroon-100 font-semibold">
            👋 {{ Auth::user()->name }}
            <svg class="w-4 h-4 transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>

          {{-- Dropdown Menu --}}
          <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-40 bg-white text-gray-800 rounded shadow-lg z-50">
            <a href="{{ route('help') }}" class="block px-4 py-2 hover:bg-gray-100">Help</a>
            <a href="{{ route('about') }}" class="block px-4 py-2 hover:bg-gray-100">About</a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
            </form>
          </div>
        </div>
      @endauth
    </div>
  </header>

  {{-- Navbar --}}
  @auth
  <nav id="navbar" class="sticky top-0 bg-gray-100 border-b border-gray-300 flex flex-wrap gap-1 px-4 py-2">
    @php
      $tabs = [
        'Home' => 'home',
        'Map' => 'map',
        'Parameters' => 'parameters',
        'Billing' => 'billing',
        'Tables' => 'tables',
        'Graphs' => 'graphs',
        'History' => 'history',
        'Options' => 'options',
        'View' => 'view'
      ];
    @endphp

    @foreach ($tabs as $label => $route)
      <a href="{{ route($route) }}"
        class="tab-btn px-4 py-2 rounded-lg font-semibold hover:bg-white hover:text-maroon focus:outline-none
        {{ request()->routeIs($route) ? 'bg-white border-2 border-maroon text-maroon' : 'text-gray-800' }}">
        {{ $label }}
      </a>
    @endforeach
  </nav>
  @endauth

  {{-- Main Content --}}
  <main class="max-w-[1400px] mx-auto p-6 flex-1">
    @yield('content')
  </main>

  {{-- Footer --}}
  <footer class="text-center text-xs text-gray-600 py-2 border-t">
    © {{ date('Y') }} MSU–IIT Energy Monitoring System
  </footer>

  {{-- Scripts --}}
  <script>
    function Clock() {
  const [time, setTime] = React.useState(new Date());

  React.useEffect(() => {
    const interval = setInterval(() => setTime(new Date()), 1000);
    return () => clearInterval(interval);
  }, []);

  return (
    <div className="font-mono">
      <div>{time.toLocaleDateString()}</div>
      <div>{time.toLocaleTimeString()}</div>
    </div>
  );
}


  </script>

</body>
</html>
