@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<form method="GET" style="margin-bottom:20px">

    <label>Select Category</label>

    <select name="category" onchange="this.form.submit()">

        <option value="">All Categories</option>

        @foreach($categories as $category)
            <option value="{{ $category->id }}"
                {{ $selectedCategory == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach

    </select>

</form>

{{-- STATS --}}
<div class="stats-row">
    <div class="stat-box bg-assets">
        <h3>{{ $assetsCount }}</h3>
        <p>Total Assets</p>
    </div>

    <div class="stat-box bg-assigned">
        <h3>{{ $assignedCount }}</h3>
        <p>Assigned Assets</p>
    </div>

    <div class="stat-box bg-available">
        <h3>{{ $availableCount }}</h3>
        <p>Available Assets</p>
    </div>

     <div class="stat-box bg-available">
        <h3>{{ $notAvailableCount }}</h3>
        <p>Not Available & waiting  Assets  & archived</p>
    </div>

    <div class="stat-box bg-users">
        <h3>{{ $usersCount }}</h3>
        <p>Total Users</p>
    </div>

    <div class="stat-box bg-locations">
        <h3>{{ $locationsCount }}</h3>
        <p>Locations</p>
    </div>
</div>

{{-- CHARTS --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

    <div class="chart-box">
        <h4>Assets by Status</h4>
        <div style="height:180px;width:180px;margin:auto">
            <canvas id="assetStatusChart"></canvas>
        </div>
    </div>

    <div class="chart-box">
        <h4>Assets by Assignment</h4>
        <div style="height:160px;width:260px;margin:auto">
            <canvas id="assetAssignmentChart"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1"></script>

<script>
new Chart(assetStatusChart, {
    type: 'doughnut',
    data: {
        labels: ['Assigned', 'Available'],
        datasets: [{
            data: [{{ $assignedCount }}, {{ $availableCount }}],
            backgroundColor: ['#6366f1', '#22c55e']
        }]
    },
    options: {
        cutout: '65%',
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
    }
});

new Chart(assetAssignmentChart, {
    type: 'bar',
    data: {
        labels: ['Users', 'Locations','assets'],
        datasets: [{
            data: [{{ $assignedToUsers }}, {{ $assignedToLocations }}, {{ $assignedToAssets }}],
            backgroundColor: ['#6366f1', '#f59e0b','#22c55e'],
            barThickness: 20
        }]
    },
    options: {
        indexAxis: 'y',
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true } }
    }
});
</script>

@endsection
