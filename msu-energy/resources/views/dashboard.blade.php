<x-app-layout>
    <!-- Fullscreen Dashboard -->
    <section id="dashboard" class="h-screen flex flex-col">
        <!-- Status bar -->
        <div class="statusbar">
            <div class="brand">MSU-IIT ENERGY DASHBOARD</div>
            <div class="clock">{{ now()->format('H:i') }}</div>
        </div>

        <!-- Navbar -->
        <div class="navbar">
            <button class="tab-btn active">Overview</button>
            <button class="tab-btn">Buildings</button>
            <button class="tab-btn">Reports</button>
        </div>

        <!-- Main content -->
        <div class="wrap grid grid-auto flex-1 overflow-y-auto p-6">
            <!-- Example KPI cards -->
            <div class="card">
                <h3>Total Energy Consumption</h3>
                <div class="kpi">1,245 kWh</div>
            </div>

            <div class="card">
                <h3>Active Buildings</h3>
                <div class="kpi">12</div>
            </div>

            <div class="card">
                <h3>Offline Sensors</h3>
                <div class="kpi">3</div>
            </div>

            <!-- Additional cards or charts -->
        </div>
    </section>
</x-app-layout>
