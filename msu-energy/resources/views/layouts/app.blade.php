<!DOCTYPE html>
<html lang="en">
  
<head>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'MSU–IIT Energy Monitoring System') }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-white text-gray-900">

  {{-- Top Status Bar --}}
  <header class="bg-maroon text-white flex items-center justify-between px-6 py-2 shadow">
    <div class="font-bold tracking-wide text-lg">MSU–IIT Energy Monitoring System</div>
    <div class="flex items-center gap-3">
      <button id="fullscreenBtn" class="px-3 py-1 rounded-lg border border-white text-sm hover:bg-maroon-700">⛶ Fullscreen</button>
      <div id="clock" class="opacity-90 font-mono"></div>
    </div>
  </header>

  {{-- Navbar --}}
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
      'View' => 'view',
      'Help' => 'help',
      'About' => 'about'
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


  {{-- Main Content --}}
  <main class="max-w-[1400px] mx-auto p-6 flex-1">
    @yield('content')
  </main>

  {{-- Footer --}}
  <footer class="text-center text-xs text-gray-600 py-2 border-t">© {{ date('Y') }} MSU–IIT Energy Monitoring System</footer>

</body>
</html>
