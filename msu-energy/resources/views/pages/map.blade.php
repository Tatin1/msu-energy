@extends('layouts.app')

@section('content')
<section id="map" class="space-y-6">
  <h1 class="text-3xl font-bold text-maroon">Campus Building Map</h1>

  <div class="grid md:grid-cols-3 gap-6">
    {{-- Map Display --}}
    <div class="md:col-span-2 bg-gray-50 border rounded-2xl shadow p-4 relative overflow-hidden">
      <div class="relative">
        <img src="{{ asset('images/msu-iit-map.jpg') }}" alt="MSU-IIT Campus Map" usemap="#msuMap"
          id="campusMap" class="rounded-xl w-full shadow-md">

        <!-- Accurate Clickable Map -->
        <map name="msuMap">
          <area target="_self" alt="Gymnasium" title="Gymnasium" href="#" coords="1259,625,148" shape="circle" data-building="GYMNASIUM">
          <area target="_self" alt="CASS" title="CASS" href="#" coords="546,269,102" shape="circle" data-building="CASS">
          <area target="_self" alt="CED" title="CED" href="#" coords="1209,300,120" shape="circle" data-building="CED">
          <area target="_self" alt="CSM" title="CSM" href="#" coords="950,676,740,561" shape="rect" data-building="CSM">
          <area target="_self" alt="CBAA" title="CBAA" href="#" coords="819,387,695,255,573,449,768,490,787,463" shape="poly" data-building="CBAA">
          <area target="_self" alt="CCS" title="CCS" href="#" coords="782,687,768,710,764,776,955,802,964,714" shape="poly" data-building="CCS">
          <area target="_self" alt="COE" title="COE" href="#" coords="925,248,1072,267,996,562,842,536,846,412" shape="poly" data-building="COE">
          <area target="_self" alt="MICEL" title="MICEL" href="#" coords="592,680,566,737,727,765,731,698" shape="poly" data-building="MICEL">
        </map>

        <!-- Tooltip -->
        <div id="tooltip" class="hidden absolute bg-maroon text-white text-xs px-2 py-1 rounded shadow-lg"></div>
      </div>

      <p class="text-sm italic text-gray-600 mt-2">Click a building to view details.</p>
    </div>

    {{-- Building Status --}}
    <div class="bg-white border rounded-2xl shadow p-4 overflow-y-auto max-h-[600px]">
      <h2 class="text-lg font-semibold text-maroon mb-2">Building Status</h2>
      <ul id="buildingStatus" class="space-y-1 text-sm">
        <li><strong>COE</strong> (Engineering) — <span class="status online">Online</span></li>
        <li><strong>CCS</strong> (Computer Studies) — <span class="status idle">Idle</span></li>
        <li><strong>CED</strong> (Education) — <span class="status offline">Offline</span></li>
        <li><strong>CBAA</strong> (Business) — <span class="status idle">Idle</span></li>
        <li><strong>CSM</strong> (Science & Math) — <span class="status online">Online</span></li>
        <li><strong>CASS</strong> (Arts & Social Sciences) — <span class="status online">Online</span></li>
        <li><strong>MICEL</strong> — <span class="status online">Online</span></li>
        <li><strong>GYMNASIUM</strong> — <span class="status online">Online</span></li>
      </ul>

      <hr class="my-3">
      <div id="building-info" class="text-sm text-gray-700">
        <strong>COE</strong><br>
        College of Engineering — Smart Meter: COE-1
      </div>
    </div>
  </div>

  {{-- Interactivity --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/image-map-resizer/1.0.10/js/imageMapResizer.min.js"></script>
  <script>
  document.addEventListener("DOMContentLoaded", () => {
    imageMapResize(); // keeps map responsive

    const tooltip = document.getElementById("tooltip");
    const info = document.getElementById("building-info");
    const mapImg = document.getElementById("campusMap");

    document.querySelectorAll("area").forEach(area => {
      area.addEventListener("mousemove", e => {
        tooltip.textContent = area.alt;
        const rect = mapImg.getBoundingClientRect();
        tooltip.style.left = (e.pageX - rect.left + 15) + "px";
        tooltip.style.top = (e.pageY - rect.top + 15) + "px";
        tooltip.classList.remove("hidden");
      });

      area.addEventListener("mouseleave", () => tooltip.classList.add("hidden"));

      area.addEventListener("click", e => {
        e.preventDefault();
        const name = area.dataset.building;
        info.innerHTML = `<strong>${name}</strong><br>Energy usage and smart meter data for ${name} are currently being monitored.`;
      });
    });
  });
  </script>

  <style>
    area:hover { cursor: pointer; outline: 2px solid #7a0e0e; }
    .status {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 9999px;
      font-size: 0.7rem;
      font-weight: bold;
    }
    .status.online { background: #d1fae5; color: #047857; }
    .status.offline { background: #fee2e2; color: #b91c1c; }
    .status.idle { background: #fef9c3; color: #a16207; }
  </style>
</section>
@endsection
