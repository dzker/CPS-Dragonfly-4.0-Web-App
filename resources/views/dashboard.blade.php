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
        <div style="padding-top:15px;" class="container-fluid mt-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3 class="mb-0">Missions Over Time</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="missionChart" width="400" height="200"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h3 class="mb-0">Items by Status</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="itemChart" width="400" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Mission Duration Chart -->
        <div style="padding-top:15px;" class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h3 class="mb-0">Mission Durations</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="dateRange" class="form-label">Filter by Date Range:</label>
                            <input type="text" class="form-control" id="dateRange">
                        </div>
                        <canvas id="missionDurationChart" width="800" height="400"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<!-- Add JavaScript to Fetch and Render the Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">
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
        type: 'line',
        data: {
            labels: missionDates,
            datasets: [{
                label: 'Cumulative Missions',
                data: missionCounts,
                fill: false,
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.1,
                pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(75, 192, 192, 1)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cumulative Number of Missions'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        title: function(tooltipItems) {
                            return 'Date: ' + tooltipItems[0].label;
                        },
                        label: function(context) {
                            return 'Total Missions: ' + context.parsed.y;
                        }
                    }
                }
            }
        }
    });
    // Render the item status chart
    new Chart(document.getElementById('itemChart'), {
        type: 'doughnut',
        data: {
            labels: itemStatuses,
            datasets: [{
                data: itemCounts,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend:{
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context){
                            let label = context.label || '';
                            if(label){
                                label += ': ';
                            }
                            if(context.parsed != null){
                                label += context.parsed + ' items';
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Mission Duration Chart
    const missionDurationData = @json($missionDurations);
    let chart;

    function renderMissionDurationChart(data) {
        const ctx = document.getElementById('missionDurationChart').getContext('2d');

        if (chart) {
            chart.destroy();
        }

        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(m => m.mission_id),
                datasets: [{
                    label: 'Mission Duration (hours)',
                    data: data.map(m => m.duration),
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Duration (hours)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Mission ID'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function(tooltipItems) {
                                return 'Mission: ' + tooltipItems[0].label;
                            },
                            label: function(context) {
                                return 'Duration: ' + context.parsed.y.toFixed(2) + ' hours';
                            }
                        }
                    }
                }
            }
        });
    }

    // Initialize date range picker
    $('#dateRange').daterangepicker({
        startDate: moment(missionDurationData[0].start_time),
        endDate: moment(missionDurationData[missionDurationData.length - 1].start_time),
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Filter data based on date range
    function filterData(start, end) {
        return missionDurationData.filter(m => {
            const missionDate = moment(m.start_time);
            return missionDate.isSameOrAfter(start) && missionDate.isSameOrBefore(end);
        });
    }

    // Initial render
    renderMissionDurationChart(missionDurationData);

    // Update chart when date range changes
    $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
        const filteredData = filterData(picker.startDate, picker.endDate);
        renderMissionDurationChart(filteredData);
    });
</script>
@endsection

