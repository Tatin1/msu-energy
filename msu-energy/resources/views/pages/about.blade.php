@extends('layouts.app')

@section('content')
<section id="about" class="space-y-8">
  <h1 class="text-3xl font-bold text-maroon">About</h1>

  <div class="card space-y-4">
    <h3 class="font-bold">MSU–IIT Energy Monitoring System</h3>
    <p>
      This project provides a centralized dashboard for real-time monitoring of energy consumption across 
      the MSU–IIT campus. It is designed to promote efficient power management and sustainability efforts.
    </p>

    <h3 class="font-bold">Developed By</h3>
    <ul class="list-disc pl-6 space-y-1">
      <li>"your name here"</li>
      <li>MSU–IIT College of Engineering – Project Supervision</li>
    </ul>

    <p class="text-gray-700">Version 1.0.0 • Laravel 12 + Tailwind + Chart.js</p>
  </div>
</section>
@endsection
