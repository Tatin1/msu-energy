@extends('layouts.app')

@section('content')
<section id="options">
  <h1 class="text-3xl font-bold text-maroon mb-6">System Options</h1>

  <div class="bg-white border rounded-2xl shadow p-6 max-w-xl">
    <form class="space-y-4">
      <div>
        <label class="block text-sm mb-1 font-medium">Theme</label>
        <select class="w-full border-gray-300 rounded-md">
          <option>Light</option>
          <option>Dark</option>
        </select>
      </div>

      <div>
        <label class="block text-sm mb-1 font-medium">Data Refresh Interval (s)</label>
        <input type="number" class="w-full border-gray-300 rounded-md" value="10">
      </div>

      <button type="submit" class="bg-maroon text-white px-4 py-2 rounded-md hover:bg-maroon-700">Save</button>
    </form>
  </div>
</section>
@endsection
