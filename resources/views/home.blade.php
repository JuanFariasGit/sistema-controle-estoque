@extends('layouts.app')
@section('title', 'Principal')

@section('content')
<div class="container-fluid">
    <h1>DASHBOARD</h1> 
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Entradas
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="entryPie"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Saídas
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="exitPie"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.0.0/chartjs-plugin-datalabels.min.js"></script>

<script>
    window.onload = function () {
        let ctxEntry = document.getElementById('entryPie').getContext('2d')
        let ctxExit = document.getElementById('exitPie').getContext('2d')
        
        window.entryPie = new Chart(ctxEntry, {
            "type": "doughnut",
            "data": {
                "datasets": [{
                    "data": {{$entryValues}},
                    "backgroundColor": "#009B95"
                }],
                "labels": {!! $entryLabels !!}
            },
            "options": {
                "responsive": true,
                "plugins": {  
                    "legend": {
                        "display": false
                    },
                    "tooltip": {
                        "enabled": false
                    },
                    "datalabels": {
                        "color": "#ffffff",
                        "formatter": (value, context) => {
                            const total = {{ $entryTotal }}
                            let porcent = (value / total) * 100
                            return [ `${context.chart.data.labels[context.dataIndex]}`, `${porcent.toFixed(2)} %` ]
                        }
                    }
                },
                "maintainAspectRatio": false
            },
            "plugins": [ChartDataLabels]
        })

        window.exitPie = new Chart(ctxExit, {
            "type": "doughnut",
            "data": {
                "datasets": [{
                    "data": {{$exitValues}},
                    "backgroundColor": "#009B95"
                }],
                "labels": {!! $exitLabels !!}
            },
            "options": {
                "responsive": true,
                "plugins": {  
                    "legend": {
                        "display": false
                    },
                    "tooltip": {
                        "enabled": false
                    },
                    "datalabels": {
                        "color": "#ffffff",
                        "formatter": (value, context) => {
                            const total = {{ $exitTotal }}
                            let porcent = (value / total) * 100
                            return [ `${context.chart.data.labels[context.dataIndex]}`, `${porcent.toFixed(2)} %` ]
                        }
                    }
                },
                "maintainAspectRatio": false
            },
            "plugins": [ChartDataLabels]
        })
    }
</script>
@endsection