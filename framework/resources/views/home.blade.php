@extends('layouts.admin')
@section('body-title')
    @lang('equicare.home')
@endsection
@section('title')
    | @lang('equicare.dashboard')
@endsection
@section('breadcrumb')
    <li class="active">@lang('equicare.dashboard')</li>
@endsection
@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">

    <div class="row ">

        <body>
            <style>                
                .status {
                    padding: 5px;
                    border-radius: 3px;
                    font-weight: bold;
                }

                .status.abierto {
                    color: white;
                    background-color: green;
                }

                .status.cerrado {
                    color: white;
                    background-color: red;
                }

                #preloader6 {
                    position: relative;
                    width: 84px; /* Aumentado de 42px a 84px */
                    height: 84px; /* Aumentado de 42px a 84px */
                    animation: preloader_6 5s infinite linear;
                }

                #preloader6 span {
                    width: 40px;  /* Aumentado de 20px a 40px */
                    height: 40px; /* Aumentado de 20px a 40px */
                    position: absolute;
                    background: red;
                    display: block;
                    animation: preloader_6_span 1s infinite linear;
                }

                #preloader6 span:nth-child(1) {
                    background: #2ecc71;
                }

                #preloader6 span:nth-child(2) {
                    left: 44px; /* Aumentado de 22px a 44px */
                    background: #9b59b6;
                    animation-delay: .2s;
                }

                #preloader6 span:nth-child(3) {
                    top: 44px; /* Aumentado de 22px a 44px */
                    background: #3498db;
                    animation-delay: .4s;
                }

                #preloader6 span:nth-child(4) {
                    top: 44px; /* Aumentado de 22px a 44px */
                    left: 44px; /* Aumentado de 22px a 44px */
                    background: #f1c40f;
                    animation-delay: .6s;
                }

                @keyframes preloader_6_span {
                    0% { transform: scale(1); }
                    50% { transform: scale(0.5); }
                    100% { transform: scale(1); }
                }
            </style>

            <div class="container">
                <!-- main -->
                <div class="main">
                    @php
                        use Illuminate\Support\Facades\Log as log;
                        use Carbon\Carbon;

                        $oneWeekAgo = Carbon::now()->subWeek();
                        log::info($oneWeekAgo);

                        $countHospitals = 0;
                        $countHospitals = \App\Hospital::query()->Hospital()->count();

                        $countOpenTickets = 0;
                        $countOpenTickets = \App\Models\Tickets::where('status', '1')->count();

                        $countEquipment = 0;
                        $countEquipment = \App\Equipment::query()->Hospital()->count();
                        //log count countEquipment

                        // Consultar los usuarios con el número de reseñas en la última semana
                        $activeUsers = \App\Models\Reviews::select('user_id', \DB::raw('count(*) as reviews_count'))
                            ->where('created_at', '>=', $oneWeekAgo)
                            ->groupBy('user_id')
                            ->orderBy('reviews_count', 'desc')
                            ->with('user')
                            ->get();

                        log::info($activeUsers);

                        //query lastTickets

                        // $lastTickets = \App\Models\Tickets::query()->orderBy('created_at', 'desc')->limit(10)->get();
                        $lastTickets = \App\Models\Tickets::where('status', 1)
                            ->orderBy('created_at', 'desc')
                            ->take(10)
                            ->get();

                        // log::info($lastTickets);

                    @endphp

                    <!-- cards -->
                    <div class="cardBox">
                        <div class="card" onclick="showLoadingAndRedirect('{{ url('admin/hospitals') }}')">
                            <div class="cardName">
                                <div class="numbers">{{ $countHospitals }}</div>
                                <p>@lang('equicare.hospitals')</p>
                            </div>
                            <div class="iconBx">
                                <i class="fa fa-hospital-o"></i>
                            </div>
                        </div>
                    
                        <div class="card" onclick="showLoadingAndRedirect('{{ url('admin/equipments') }}')">
                            <div class="cardName">
                                <div class="numbers">{{ $countEquipment }}</div>
                                <p>@lang('equicare.equipments')</p>
                            </div>
                            <div class="iconBx">
                                <i class="fa fa-wheelchair"></i>
                            </div>
                        </div>
                    
                        <div class="card" onclick="showLoadingAndRedirect('{{ url('admin/tickets') }}')">
                            <div class="cardName">
                                <div class="numbers">{{ $countOpenTickets }}</div>
                                <p>Tickets Abiertos</p>
                            </div>
                            <div class="iconBx">
                                <i class="fa fa-wrench"></i>
                            </div>
                        </div>
                    
                        <div class="card" onclick="showLoadingAndRedirect('{{ url('admin/tickets') }}')">
                            <div class="cardName">
                                <div class="numbers">{{ $countOpenTickets }}</div>
                                <p>Tickets Abiertos</p>
                            </div>
                            <div class="iconBx">
                                <i class="fa fa-wrench"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Add Charts -->

                    <div class="graphBox">
                        <div class="carrousel-container">
                            <div class="carrousel"> 
                                <div class="box">
                                    <div class="canvas-container">
                                        <canvas id="equipmentStatusChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="carousel-container">
                            <div class="carousel">
                                <div class="carousel-item">
                                    <div class="box">
                                        <div class="canvas-container">
                                            <canvas id="earning"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="box">
                                        <div class="canvas-container">
                                            <canvas id="reviewsPerUserYesterdayChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="box">
                                        <div class="canvas-container">
                                            <canvas id="ticketsClosedIn72HoursPerMonthChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="box">
                                        <div class="canvas-container">
                                            <canvas id="ticketsClosedPerMonthChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="prev" onclick="moveSlide(-1)">&#10094;</button>
                            <button class="next" onclick="moveSlide(1)">&#10095;</button>
                        </div>
                    </div>


                    <div class="details">
                        <div class="recentOrders">
                            <div class="cardHeader">
                                <h2>Tickets Abiertos</h2>
                                <a href="{{ route('tickets.index') }}" class="btn">Ver todos los tickets</a>
                            </div>
                            @if($lastTickets->isEmpty())
                                <p>No hay tickets abiertos en este momento.</p>
                            @else
                                <table>
                                    <thead>
                                        <tr>
                                            <td>Título</td>
                                            <td>Categoría</td>
                                            {{-- <td>Fecha de Creación</td> --}}
                                            <td>Prioridad</td>
                                            <td>Estado</td>
                                            <td>Modelo</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lastTickets as $ticket)
                                            <tr>
                                                <td>{{ $ticket->title }}</td>
                                                <td>{{ $ticket->category }}</td>
                                                {{-- <td>{{ $ticket->created_at->format('Y-m-d') }}</td> --}}
                                                <td>{{ $ticket->priority }}</td>
                                                <td>
                                                    <span class="status {{ $ticket->status == 1 ? 'abierto' : 'cerrado' }}">
                                                        {{ $ticket->status == 1 ? 'Abierto' : 'Cerrado' }}
                                                    </span>
                                                </td>
                                                <td>{{ $ticket->model ?? 'Sin modelo' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>

                        <div class="recentCustomers">
                            <div class="cardHeader">
                                <h2>Usuarios mas activos en los ultimos 7 dias</h2>
                            </div>
                            <table>
                                @foreach ($activeUsers as $userReview)
                                    <tr>
                                        {{-- 
                                        Si tienes imágenes de perfil dinámicas para los usuarios, puedes descomentar la siguiente sección 
                                        y asegurarte de que la lógica esté funcionando.
                                        --}}
                                        {{-- 
                                        <td width="60px">
                                            <div class="imgBx">
                                                <img src="{{ $userReview->user->profile_image ?? 'default-user.jpg' }}" alt="{{ $userReview->user->name }}">
                                            </div>
                                        </td> 
                                        --}}
                                        <td>
                                            <h4>{{ $userReview->user->name }}<br>
                                                <span>Total de revisiones: {{ $userReview->reviews_count }}</span>
                                            </h4>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>






                    </div>
                </div>
            </div>

            <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
            <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

            <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
            <script src="{{ asset('assets/js/my_chart.js') }}" type="text/javascript"></script>

            <div id="loading" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255, 255, 255, 0.8); z-index:9999; text-align:center;">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                    
                    <div id="preloader6">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </body>
    @endsection
    @php
        function getNombreMes($numeroMes)
        {
            $meses = [
                1 => 'Enero',
                2 => 'Febrero',
                3 => 'Marzo',
                4 => 'Abril',
                5 => 'Mayo',
                6 => 'Junio',
                7 => 'Julio',
                8 => 'Agosto',
                9 => 'Septiembre',
                10 => 'Octubre',
                11 => 'Noviembre',
                12 => 'Diciembre',
            ];
            return $meses[$numeroMes] ?? '';
        }
    @endphp

    @section('scripts')
        <script>
              document.addEventListener("DOMContentLoaded", function() {
                // Ocultar la pantalla de carga al cargar la página
                document.getElementById('loading').style.display = 'none';
            });

            function showLoadingAndRedirect(url) {
                // Mostrar la pantalla de carga
                document.getElementById('loading').style.display = 'block';

                // Realizar una solicitud para redirigir a la nueva página y mantener el preloader hasta que la nueva página esté completamente cargada
                fetch(url, { method: 'GET' })
                    .then(response => {
                        // Verificar que la solicitud fue exitosa
                        if (response.ok) {
                            // Utilizar un pequeño retraso antes de la redirección para permitir que se vea el preloader y evitar el congelamiento
                            setTimeout(function() {
                                window.location.href = url;
                            }, 200); // Espera 200ms antes de redirigir
                        } else {
                            alert('Error al cargar la página.');
                        }
                    })
                    .catch(error => {
                        console.error('Error en la solicitud:', error);
                    });
            }

            const purpleGradient = {
                backgroundColor: [
                    'rgba(65, 22, 87, 1)',
                    'rgba(87, 30, 117, 0.9)',
                    'rgba(109, 38, 147, 0.8)',
                    'rgba(131, 46, 177, 0.7)',
                    'rgba(153, 54, 207, 0.6)',
                    'rgba(175, 62, 237, 0.5)',
                    'rgba(197, 70, 255, 0.4)'
                ],
                borderColor: [
                    'rgb(65, 22, 87)',
                    'rgb(87, 30, 117)',
                    'rgb(109, 38, 147)',
                    'rgb(131, 46, 177)',
                    'rgb(153, 54, 207)',
                    'rgb(175, 62, 237)',
                    'rgb(197, 70, 255)'
                ]
            };

            const coolColors = {
                backgroundColor: [
                    'rgba(0, 63, 92, 1)',
                    'rgba(5, 102, 141, 0.9)',
                    'rgba(2, 128, 144, 0.8)',
                    'rgba(0, 168, 150, 0.7)',
                    'rgba(2, 195, 154, 0.6)',
                    'rgba(240, 243, 189, 0.5)',
                    'rgba(73, 190, 170, 0.4)'
                ],
                borderColor: [
                    'rgb(0, 63, 92)',
                    'rgb(5, 102, 141)',
                    'rgb(2, 128, 144)',
                    'rgb(0, 168, 150)',
                    'rgb(2, 195, 154)',
                    'rgb(240, 243, 189)',
                    'rgb(73, 190, 170)'
                ]
            };

            var ctxMonth = document.getElementById('earning').getContext('2d');

            var reviewsPerUserThisMonthChart = new Chart(ctxMonth, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($reviewsPerUserThisMonth->pluck('user_name')) !!},
                    datasets: [{
                        label: 'Revisiones del Mes',
                        data: {!! json_encode($reviewsPerUserThisMonth->pluck('total_reviews')) !!},
                        backgroundColor: purpleGradient.backgroundColor,
                        borderColor: purpleGradient.borderColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',

                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 50 // Fija el límite máximo en el eje Y a 50
                        }
                    }
                }

            });

            var ctxYesterday = document.getElementById('reviewsPerUserYesterdayChart').getContext('2d');
            var reviewsPerUserYesterdayChart = new Chart(ctxYesterday, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($reviewsPerUserYesterday->pluck('user_name')) !!},
                    datasets: [{
                        label: 'Revisiones del Día de Ayer',
                        data: {!! json_encode($reviewsPerUserYesterday->pluck('total_reviews')) !!},
                        backgroundColor: purpleGradient.backgroundColor,
                        borderColor: purpleGradient.borderColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 20
                        }
                    }
                }

            });



            var ctx72HoursPerMonth = document.getElementById('ticketsClosedIn72HoursPerMonthChart').getContext('2d');
            var ticketsClosedIn72HoursPerMonthChart = new Chart(ctx72HoursPerMonth, {
                type: 'bar', // Puedes cambiar a 'line' si prefieres una gráfica de líneas
                data: {
                    labels: {!! json_encode(
                        $ticketsClosedIn72HoursPerMonth->map(function ($item) {
                            return getNombreMes($item->month);
                        }),
                    ) !!},
                    datasets: [{
                        label: 'Tickets Cerrados en 72 Horas por Mes',
                        data: {!! json_encode($ticketsClosedIn72HoursPerMonth->pluck('total_tickets')) !!},
                        backgroundColor: purpleGradient.backgroundColor,
                        borderColor: purpleGradient.borderColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 50 // Fija el límite máximo en el eje Y a 50
                        }
                    }
                }

            });

            var ctxPerMonth = document.getElementById('ticketsClosedPerMonthChart').getContext('2d');
            var ticketsClosedPerMonthChart = new Chart(ctxPerMonth, {
                type: 'line',
                data: {
                    labels: {!! json_encode(
                        $ticketsClosedPerMonth->map(function ($item) {
                            return getNombreMes($item->month);
                        }),
                    ) !!},
                    datasets: [{
                        label: 'Tickets Cerrados',
                        data: {!! json_encode($ticketsClosedPerMonth->pluck('total_tickets')) !!},
                        backgroundColor: purpleGradient.backgroundColor,
                        borderColor: purpleGradient.borderColor,
                        borderWidth: 1
                    }]
                },
                options: {


                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 50 // Fija el límite máximo en el eje Y a 50
                        }
                    }
                }

            });

            var ctx = document.getElementById('equipmentStatusChart').getContext('2d');
            var equipmentStatusChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($equipmentStatusCounts->pluck('status_description')) !!},
                    datasets: [{
                        label: 'Cantidad de Equipos por Estado',
                        data: {!! json_encode($equipmentStatusCounts->pluck('total')) !!},
                        backgroundColor: purpleGradient.backgroundColor,
                        borderColor: purpleGradient.borderColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Cantidad de Equipos por Estado',
                            font: {
                                size: 20
                            },
                            padding: {
                                bottom: 10
                            }
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }

                }

            });
        </script>
    @endsection
