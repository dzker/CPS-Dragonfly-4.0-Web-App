@extends('layouts.master')
@extends('layouts.layout')

@section('content')
@include('sidebar.sidebar')
<div style="padding-top:30px;" class="container">
    <div class="row justify-content-center">

        <div class="container">
            <div class="card text-white bg-dark mb-3">

                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    You are Logged In
                </div>
            </div>
        </div>

        <div style="padding-top:15px;" class="container-fluid">
            <div class="row">
                <!-- Total Item -->
                <div class="card text-white bg-primary mb-3" style="margin:10px; padding:2px; width:23%; height:8rem;">
                    <div class="card-body">
                        <div class="row">
                            <i style="padding:10px;" class="fa fa-box-open fa-4x"></i>
                            <div class="col">
                                <h5 class="card-title">Total Items</h5>
                                <h1 class="card-text">{{$items->count()}}</h1>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Available Items -->
                <div class="card text-white bg-success mb-3" style="margin:10px; padding:2px; width:23%; height:8rem;">
                    <div class="card-body">
                        <div class="row">
                            <i style="padding:10px; padding-left:30px;" class="fa fa-clipboard-check fa-4x"></i>
                            <div class="col">
                                <h5 class="card-title">Available</h5>
                                <h1 class="card-text">{{$items->where('status','=', 'Available')->count()}}</h1>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Unresolved Items -->
                <div class="card text-white bg-warning mb-3" style="margin:10px; padding:2px; width:23%; height:8rem;">
                    <div class="card-body">
                        <div class="row">
                            <i style="padding:10px; padding-left:30px;" class="fa fa-question fa-4x"></i>
                            <div class="col">
                                <h5 class="card-title">Unresolved</h5>
                                <h1 class="card-text">{{$items->where('status','=', 'Unresolved')->count()}}</h1>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Missing Items -->
                <div class="card text-white bg-danger mb-3" style="margin:10px; padding:2px; width:23%; height:8rem;">
                    <div class="card-body">
                        <div class="row">
                            <i style="padding:10px; padding-left:20px;" class="fa fa-search fa-4x"></i>
                            <div class="col">
                                <h5 class="card-title">Missing</h5>
                                <h1 class="card-text">{{$items->where('status','=', 'Lost')->count()}}</h1>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Add the Canvas for Charts -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <h3>Missions Over Time</h3>
                    <canvas id="missionChart" width="400" height="200"></canvas>
                </div>
                <div class="col-md-6">
                    <h3>Items by Status</h3>
                    <canvas id="itemChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add JavaScript to Fetch and Render the Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data from the server passed via Blade
    const missionData = @json($missionStats);
    const missionDates = missionData.map(entry => entry.date);
    const missionCounts = missionData.map(entry => entry.count);

    const itemData = @json($itemStats);
    const itemStatuses = itemData.map(entry => entry.status);
    const itemCounts = itemData.map(entry => entry.count);

    // Render the mission chart
    new Chart(document.getElementById('missionChart'), {
        type: 'bar',
        data: {
            labels: missionDates,
            datasets: [{
                label: 'Missions',
                data: missionCounts,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    // Render the item status chart
    new Chart(document.getElementById('itemChart'), {
        type: 'pie',
        data: {
            labels: itemStatuses,
            datasets: [{
                label: 'Items by Status',
                data: itemCounts,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(255, 99, 132, 0.2)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        }
    });
</script>
@endsection

