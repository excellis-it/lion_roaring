<!DOCTYPE html>
<html lang="en">

<head>
    <meta content="width=device-width,  initial-scale=1,  maximum-scale=1,  shrink-to-fit=no" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" href="{{ asset('frontend_assets/uploads/2023/04/cropped-logo-1-32x32.png') }}" sizes="32x32" />
    <title>@yield('title')</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('frontend_assets/images/icons/favicon.ico')}}" />
    <link rel="stylesheet" href="{{asset('admin_assets/bootstrap-5.3/css/bootstrap.min.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/phosphor-icons"></script>
    <link rel="stylesheet" href="{{asset('admin_assets/css/app.min.css')}}" />
    <link rel="stylesheet" href="{{asset('admin_assets/css/custom.css')}}" />
    <link rel="stylesheet" href="{{asset('admin_assets/css/style.css')}}" />

    <link rel="stylesheet" href="{{asset('admin_assets/css/morris.css')}}">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    @stack('styles')
    <style>
        .error {
            color: red;
        }
    </style>
</head>

<body class="light light-sidebar theme-white">
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <!--header-->
            @include('admin.includes.header')
            <section class="section_breadcrumb bred-des by-me">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="page-title m-b-0">@yield('head')</h4>
                    </div>
                    <div class="col-md-6 create-button">
                        @yield('create_button')
                    </div>
                </div>
            </section>
            <!--end header-->
            <!--sidebar-wrapper-->
            @include('admin.includes.sidebar')
            <!--end sidebar-wrapper-->
            <!--page-wrapper-->
            @yield('content')

            <!--end page-wrapper-->
            <!--footer -->
            @include('admin.includes.footer')

            <!-- end footer -->
        </div>
    </div>
    <script src="{{asset('admin_assets/js/jquery-3.4.1.min.js')}}"></script>
    <script src="{{asset('admin_assets/bootstrap-5.3/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('admin_assets/js/raphael-min.js')}}"></script>
    <script src="{{asset('admin_assets/js/morris.min.js')}}"></script>
    <script src="{{asset('admin_assets/js/Chart.min.js')}}"></script>
    <script src="{{asset('admin_assets/js/custom.js')}}" async=""></script>
    <script src="{{asset('admin_assets/js/app.min.js')}}"></script>
    <script src="{{asset('admin_assets/js/scripts.js')}}" async=""></script>
    <script src="{{asset('admin_assets/js/jquery-ui.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
     {{-- trippy cdn link --}}
 <script src="https://unpkg.com/popper.js@1"></script>
 <script src="https://unpkg.com/tippy.js@5"></script>
 {{-- trippy --}}
 <script>
     tippy('[data-tippy-content]', {
         allowHTML: true,
         placement: 'bottom',
         theme: 'light-theme',
     });
 </script>
    <script>
        @if (Session::has('message'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.success("{{ session('message') }}");
        @endif

        @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.error("{{ session('error') }}");
        @endif

        @if (Session::has('info'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.info("{{ session('info') }}");
        @endif

        @if (Session::has('warning'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.warning("{{ session('warning') }}");
        @endif
    </script>



    <script type="text/javascript">
        var colorDanger = "#F4B11A";
        Morris.Donut({
            element: 'donut-example',
            resize: false,
            colors: [
                '#E34804 ',
                '#E34804 ',
                '#F1EFFB',
                '#F1EFFB'
            ],
            //labelColor:"#cccccc", // text color
            //backgroundColor: '#333333', // border color
            data: [{
                    label: "Total Booking",
                    value: 200,
                    color: colorDanger
                },
                {
                    label: "Total Booking",
                    value: 120
                },
                {
                    label: "Total Booking",
                    value: 80
                }
            ]
        });


        var ctx = document.getElementById("myChart").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ["Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: 'Flight',
                    data: [200, 250, 100, 150, 180, 90, 70],
                    backgroundColor: "#97C7FF"
                }, {
                    label: 'Hotel',
                    data: [300, 200, 250, 100, 200, 40, 60],
                    backgroundColor: "#1BAC18"
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }],
                    xAxes: [{
                        // Change here
                        barPercentage: 0.8,
                        gridLines: {
                            drawOnChartArea: false,
                            color: "black",
                            zeroLineColor: "black"
                        }
                    }]
                }
            }
        });

        var ctx = document.getElementById("myChart2").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ["Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: 'Agency',
                    data: [200, 250, 100, 150, 180, 90, 70],
                    backgroundColor: "#97C7FF"
                }, {
                    label: 'Corporate',
                    data: [300, 200, 250, 100, 200, 40, 60],
                    backgroundColor: "1BAC18"
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }

                    }],
                    xAxes: [{
                        // Change here
                        barPercentage: 0.8,
                        gridLines: {
                            drawOnChartArea: false,
                            color: "black",
                            zeroLineColor: "black"
                        }
                    }]

                }
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
