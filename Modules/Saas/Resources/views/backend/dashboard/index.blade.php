@extends('backend.layouts.app')

@section('title', @$data['title'])

@section('content')
    <h3 class="mb-5">{{ _trans('common.Welcome to') }} {{ @base_settings('company_name') }}
        [{{ Auth::user()->name }}]
    </h3>
    <div class="row">
        <div class="col-12 col-sm-6 col-xs-6 col-md-4 col-lg-3 col-xl-3 pb-24 pl-12 pr-12">
            <div class="card summery-card ot-card h-100">
                <div class="card-heading d-flex align-items-center">
                    <div class="card-icon circle-brown dashboard-card-icon">
                        <i class="las la-building"></i>
                    </div>
                    <div class="card-content">
                        <h4>Total Companies</h4>
                        <h1>{{ $data['totalCompany'] }}</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xs-6 col-md-4 col-lg-3 col-xl-3 pb-24 pl-12 pr-12">
            <div class="card summery-card ot-card h-100">
                <div class="card-heading d-flex align-items-center">
                    <div class="card-icon  circle-primary dashboard-card-icon">
                        <i class="las la-crown"></i>
                    </div>
                    <div class="card-content">
                        <h4>Total Subscriptions</h4>
                        <h1>{{ $data['totalSubscription'] }}</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xs-6 col-md-4 col-lg-3 col-xl-3 pb-24 pl-12 pr-12">
            <div class="card summery-card ot-card h-100">
                <div class="card-heading d-flex align-items-center">
                    <div class="card-icon circle-success dashboard-card-icon">
                        <i class="las la-crown"></i>
                    </div>
                    <div class="card-content">
                        <h4>Total Active Subscriptions</h4>
                        <h1>{{ $data['totalActiveSubscription'] }}</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xs-6 col-md-4 col-lg-3 col-xl-3 pb-24 pl-12 pr-12">
            <div class="card summery-card ot-card h-100">
                <div class="card-heading d-flex align-items-center">
                    <div class="card-icon circle-warning dashboard-card-icon">
                        <i class="las la-file-invoice-dollar"></i>
                    </div>
                    <div class="card-content">
                        <h4>Total Plans</h4>
                        <h1>{{ $data['totalPlan'] }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
         <!-- Dashboard Summery Card End -->
        <div class="col-12 statistic-card">
            <div class="row g-4">
                <!-- Dashboard Statistic start  -->
                <div class="ot-charts col-12 col-xxl-8">
                    <div class="card statistic-card ot-card">
                        <div class="card-header">
                            <div class="card-title">
                                <h4>{{ _trans('common.Company Earning') }}</h4>
                            </div>
                        </div>

                        <div class="card-body">
                            <div id="company_earning"></div>
                        </div>
                    </div>
                </div>
                <div class="ot-charts col-12 col-xxl-4">
                    <div class="card ot-card ot-visit-chart h-100">
                        <div class="card-header mb-20">
                            <div class="card-title">
                                <h4>{{ _trans('common.Subscription Plan Status') }}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="subscription_plan_status"></div>
                        </div>
                    </div>
                </div>
                <!-- Dashboard Statistic end  -->
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('vendors/apexchart/js/apexcharts.min.js')}}"></script>
    <script src="{{ asset('js/ot-chart.js')}}"></script>

    {{-- chart js (Company Earning) --}}
    <script>
        var monthlyCompanyEarnings = {!! json_encode($data['monthly_company_earnings']) !!};
        var totalEarningsData = monthlyCompanyEarnings.map(function (earning) {
            return parseFloat(earning.total_earnings) || 0;
        });
        // console.log(totalEarningsData);

        if($("#company_earning").length){
            var companyEarningChartoptions = {
                series: [
                    {
                        name: "Earning",
                        data: totalEarningsData,
                    },
                ],
                colors: ["#6CD6FD"],
                chart: {
                    type: "bar",
                    height: 350,
                    toolbar: {
                        show: false,
                    },
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: "55%",
                        endingShape: "rounded",
                    },
                },
            
                legend: {
                    itemMargin: {
                        horizontal: 5,
                        vertical: 5,
                    },
                    horizontalAlign: "center",
                    verticalAlign: "center",
                    position: "bottom",
                    fontSize: "14px",
                    fontWight: "bold",
                    markers: {
                        radius: 5,
                        height: 14,
                        width: 14,
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ["transparent"],
                },
                xaxis: {
                    categories: [
                        "Jan",
                        "Feb",
                        "Mar",
                        "Apr",
                        "May",
                        "June",
                        "July",
                        "Aug",
                        "Sep",
                        "Oct",
                        "Nov",
                        "Dec",
                    ],
                },
                yaxis: {
                    title: {
                        show: false,
                    },
                    labels: {
                        formatter: function (value) {
                            var val = Math.abs(value);
                            if (val >= 1000) {
                                val = (val / 1000).toFixed(0) + "K";
                            }
                            return val;
                        },
                    },
                },
                fill: {
                    opacity: 1,
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            var currencySymbol = "{{ settings('currency_symbol') }}";

                            if (val >= 1000000000) {
                                return currencySymbol + (val / 1000000000).toFixed(1) + "B";
                            } else if (val >= 1000000) {
                                return currencySymbol + (val / 1000000).toFixed(1) + "M";
                            } else if (val >= 1000) {
                                return currencySymbol + (val / 1000).toFixed(1) + "k";
                            } else {
                                return currencySymbol + val.toString();
                            }
                            //return val + "K";
                        },
                    },
                },
            };
            
            var companyEarningChart = new ApexCharts(
                document.querySelector("#company_earning"),
                companyEarningChartoptions
            );
            companyEarningChart.render();
        }
    </script>
    {{-- chart js (Company Earning) end --}}

    
    {{-- chart js (Subscription Plan Status) --}}
    <script>
        var totalSubscription = {{ $data['totalSubscription'] }};
        var total_subscription_status_pending = {{ $data['total_subscription_status_pending'] }};
        var total_subscription_status_approve = {{ $data['total_subscription_status_approve'] }};
        var total_subscription_status_reject = {{ $data['total_subscription_status_reject'] }};

        let optionsAnalyticsVisit = {
        series: [total_subscription_status_pending, total_subscription_status_approve, total_subscription_status_reject],
        chart: {
            id: "subscription_plan_status",
            type: "donut",
            height: 320,
            toolbar: {
                show: false,
            },
            zoom: {
                enabled: false,
            },
        },
        colors: ["#6CD6FD", "#72cf87", "#ff8a82"],

        labels: ["Pending", "Approve", "Reject"],

        dataLabels: {
            enabled: false,
        },
            plotOptions: {
                pie: {
                    donut: {
                        size: "75%",
                        labels: {
                            show: true,
                            name: {
                                fontSize: "12px",
                                offsetY: 0,
                            },
                            value: {
                                fontSize: "12px",
                                offsetY: 0,
                                formatter(val) {
                                    return `% ${val}`;
                                },
                            },
                            total: {
                                show: true,
                                fontSize: "16px",
                                label: "Total",

                                formatter: function (w) {
                                    return totalSubscription;
                                },
                            },
                        },
                    },
                },
            },

            legend: {
                itemMargin: {
                    horizontal: 0,
                    vertical: 10,
                },
                horizontalAlign: "center",
                verticalAlign: "center",
                position: "right",
                fontFamily: "Lexend",
                fontSize: "15px",
                fontWight: "500",
                markers: {
                    radius: 5,
                    height: 14,
                    width: 14,
                },
            },

            responsive: [
                {
                    breakpoint: 1400,
                    options: {
                        legend: {
                            itemMargin: {
                                horizontal: 5,
                                vertical: 5,
                            },
                            horizontalAlign: "center",
                            verticalAlign: "center",
                            position: "bottom",
                            fontFamily: "Lexend",
                            fontSize: "15px",
                            fontWight: "500",
                            markers: {
                                radius: 5,
                                height: 14,
                                width: 14,
                            },
                        },
                    },
                    breakpoint: 420,
                    options: {
                        legend: {
                            itemMargin: {
                                horizontal: 5,
                                vertical: 5,
                            },
                            horizontalAlign: "center",
                            verticalAlign: "center",
                            position: "bottom",
                            fontFamily: "Lexend",
                            fontSize: "12px",
                            fontWight: "500",
                            markers: {
                                radius: 5,
                                height: 14,
                                width: 14,
                            },
                        },
                    },
                },
            ],
        };

        if($("#subscription_plan_status").length){
            if (document.querySelector("#subscription_plan_status")) {
                let chart = new ApexCharts(
                    document.querySelector("#subscription_plan_status"),
                    optionsAnalyticsVisit
                );
                chart.render();
            }
        }
    </script>
    {{-- chart js (Subscription Plan Status) end --}}

@endsection
