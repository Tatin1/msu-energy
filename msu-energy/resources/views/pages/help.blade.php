@extends('layouts.app')

@section('content')
<section id="help" class="space-y-8">
  <h1 class="text-3xl font-bold text-maroon">Help & Support</h1>

  <div class="card space-y-4">
    <h3 class="font-bold">Common Issues</h3>
    <ul class="list-disc pl-6 space-y-1">
      <li>🔌 <strong>No Data Displayed:</strong> Ensure your sensors are online and transmitting properly.</li>
      <li>📶 <strong>Slow Dashboard:</strong> Check your internet connection or database sync.</li>
      <li>📊 <strong>Incorrect Values:</strong> Verify meter calibration in the Parameters tab.</li>
    </ul>

    <h3 class="font-bold mt-4">Contact Information</h3>
    <p>Email: <a href="mailto:energy@msuiit.edu.ph" class="text-maroon hover:underline">energy@msuiit.edu.ph</a></p>
    <p>Office: College of Engineering – Energy Monitoring Lab</p>
  </div>
</section>
@endsection
