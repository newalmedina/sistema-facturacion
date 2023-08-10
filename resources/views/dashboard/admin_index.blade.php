@extends('layouts.admin.default')
@section('title')
    @parent {{ $pageTitle }}
@stop
@section('head_page')
<link href="{{ asset('/assets/admin/vendor/jquery-bonsai/css/jquery.bonsai.css')}}" rel="stylesheet" />
    
@stop

@section('breadcrumb')
<li><span>{{ $title }}</span></li>
@stop

@section('content')
<section role="main" class="content-body card-margin">
      
    <!-- start: page -->
   <div class="mt-3">
    
    @include('layouts.admin.includes.errors')
   </div>
    <div class="row">
        <div class="col-lg-6 mb-3">
            <section class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="chart-data-selector ready" id="salesSelectorWrapper">
                                <h2>
                                    Sales:
                                    <strong>
                                        <span class="multiselect-native-select"><select class="form-control" id="salesSelector" tabindex="-1">
                                            <option value="Porto Admin" selected="">Porto Admin</option>
                                            <option value="Porto Drupal">Porto Drupal</option>
                                            <option value="Porto Wordpress">Porto Wordpress</option>
                                        </select><div class="btn-group"><button type="button" class="multiselect dropdown-toggle form-select text-center" data-bs-toggle="dropdown" title="Porto Admin"><span class="multiselect-selected-text">Porto Admin</span></button><div class="multiselect-container dropdown-menu"><button type="button" class="multiselect-option dropdown-item active" title="Porto Admin"><span class="form-check"><input class="form-check-input" type="radio" value="Porto Admin"><label class="form-check-label">Porto Admin</label></span></button><button type="button" class="multiselect-option dropdown-item" title="Porto Drupal"><span class="form-check"><input class="form-check-input" type="radio" value="Porto Drupal"><label class="form-check-label">Porto Drupal</label></span></button><button type="button" class="multiselect-option dropdown-item" title="Porto Wordpress"><span class="form-check"><input class="form-check-input" type="radio" value="Porto Wordpress"><label class="form-check-label">Porto Wordpress</label></span></button></div></div></span>
                                    </strong>
                                </h2>

                                <div id="salesSelectorItems" class="chart-data-selector-items mt-3">
                                    <!-- Flot: Sales Porto Admin -->
                                    <div class="chart chart-sm chart-active" data-sales-rel="Porto Admin" id="flotDashSales1" style="height: 203px; padding: 0px;"><canvas class="flot-base" width="940" height="304" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 627.333px; height: 203px;"></canvas><div class="flot-text" style="position: absolute; inset: 0px; font-size: smaller; color: rgb(84, 84, 84);"><div class="flot-x-axis flot-x1-axis xAxis x1Axis" style="position: absolute; inset: 0px;"><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 180px; left: 27px; text-align: center;">Jan</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 180px; left: 110px; text-align: center;">Feb</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 180px; left: 192px; text-align: center;">Mar</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 180px; left: 276px; text-align: center;">Apr</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 180px; left: 356px; text-align: center;">May</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 180px; left: 440px; text-align: center;">Jun</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 180px; left: 525px; text-align: center;">Jul</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 180px; left: 605px; text-align: center;">Aug</div></div><div class="flot-y-axis flot-y1-axis yAxis y1Axis" style="position: absolute; inset: 0px;"><div class="flot-tick-label tickLabel" style="position: absolute; top: 154px; left: 15px; text-align: right;">0</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 116px; left: 4px; text-align: right;">100</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 77px; left: 2px; text-align: right;">200</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 39px; left: 1px; text-align: right;">300</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 0px; left: 1px; text-align: right;">400</div></div></div><canvas class="flot-overlay" width="940" height="304" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 627.333px; height: 203px;"></canvas></div>
                                    <script>

                                        var flotDashSales1Data = [{
                                            data: [
                                                ["Jan", 140],
                                                ["Feb", 240],
                                                ["Mar", 190],
                                                ["Apr", 140],
                                                ["May", 180],
                                                ["Jun", 320],
                                                ["Jul", 270],
                                                ["Aug", 180]
                                            ],
                                            color: "#0088cc"
                                        }];

                                        // See: js/examples/examples.dashboard.js for more settings.

                                    </script>

                                    <!-- Flot: Sales Porto Drupal -->
                                    <div class="chart chart-sm chart-hidden" data-sales-rel="Porto Drupal" id="flotDashSales2" style="padding: 0px;"><canvas class="flot-base" width="940" height="276" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 627.333px; height: 184px;"></canvas><div class="flot-text" style="position: absolute; inset: 0px; font-size: smaller; color: rgb(84, 84, 84);"><div class="flot-x-axis flot-x1-axis xAxis x1Axis" style="position: absolute; inset: 0px;"><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 161px; left: 27px; text-align: center;">Jan</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 161px; left: 110px; text-align: center;">Feb</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 161px; left: 192px; text-align: center;">Mar</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 161px; left: 276px; text-align: center;">Apr</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 161px; left: 356px; text-align: center;">May</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 161px; left: 440px; text-align: center;">Jun</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 161px; left: 525px; text-align: center;">Jul</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 161px; left: 605px; text-align: center;">Aug</div></div><div class="flot-y-axis flot-y1-axis yAxis y1Axis" style="position: absolute; inset: 0px;"><div class="flot-tick-label tickLabel" style="position: absolute; top: 135px; left: 15px; text-align: right;">0</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 113px; left: 4px; text-align: right;">100</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 90px; left: 2px; text-align: right;">200</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 68px; left: 1px; text-align: right;">300</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 45px; left: 1px; text-align: right;">400</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 23px; left: 1px; text-align: right;">500</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 0px; left: 1px; text-align: right;">600</div></div></div><canvas class="flot-overlay" width="940" height="276" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 627.333px; height: 184px;"></canvas></div>
                                    <script>

                                        var flotDashSales2Data = [{
                                            data: [
                                                ["Jan", 240],
                                                ["Feb", 240],
                                                ["Mar", 290],
                                                ["Apr", 540],
                                                ["May", 480],
                                                ["Jun", 220],
                                                ["Jul", 170],
                                                ["Aug", 190]
                                            ],
                                            color: "#2baab1"
                                        }];

                                        // See: js/examples/examples.dashboard.js for more settings.

                                    </script>

                                    <!-- Flot: Sales Porto Wordpress -->
                                    <div class="chart chart-sm chart-hidden" data-sales-rel="Porto Wordpress" id="flotDashSales3" style="padding: 0px;"><canvas class="flot-base" width="940" height="276" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 627.333px; height: 184px;"></canvas><div class="flot-text" style="position: absolute; inset: 0px; font-size: smaller; color: rgb(84, 84, 84);"><div class="flot-x-axis flot-x1-axis xAxis x1Axis" style="position: absolute; inset: 0px;"><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 161px; left: 30px; text-align: center;">Jan</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 161px; left: 112px; text-align: center;">Feb</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 161px; left: 194px; text-align: center;">Mar</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 161px; left: 277px; text-align: center;">Apr</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 161px; left: 357px; text-align: center;">May</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 161px; left: 441px; text-align: center;">Jun</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 161px; left: 525px; text-align: center;">Jul</div><div class="flot-tick-label tickLabel" style="position: absolute; max-width: 43px; top: 161px; left: 605px; text-align: center;">Aug</div></div><div class="flot-y-axis flot-y1-axis yAxis y1Axis" style="position: absolute; inset: 0px;"><div class="flot-tick-label tickLabel" style="position: absolute; top: 135px; left: 18px; text-align: right;">0</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 108px; left: 5px; text-align: right;">250</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 81px; left: 4px; text-align: right;">500</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 54px; left: 5px; text-align: right;">750</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 27px; left: 1px; text-align: right;">1000</div><div class="flot-tick-label tickLabel" style="position: absolute; top: 0px; left: 2px; text-align: right;">1250</div></div></div><canvas class="flot-overlay" width="940" height="276" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 627.333px; height: 184px;"></canvas></div>
                                    <script>

                                        var flotDashSales3Data = [{
                                            data: [
                                                ["Jan", 840],
                                                ["Feb", 740],
                                                ["Mar", 690],
                                                ["Apr", 940],
                                                ["May", 1180],
                                                ["Jun", 820],
                                                ["Jul", 570],
                                                ["Aug", 780]
                                            ],
                                            color: "#734ba9"
                                        }];

                                        // See: js/examples/examples.dashboard.js for more settings.

                                    </script>
                                </div>

                            </div>
                        </div>
                        <div class="col-xl-4 text-center">
                            <h2 class="card-title mt-3">Sales Goal</h2>
                            <div class="liquid-meter-wrapper liquid-meter-sm mt-3">
                                <div class="liquid-meter liquid-meter-loaded"><svg preserveAspectRatio="xMidYMid meet" viewBox="0 0 220 220" width="100%" height="100%"><desc>Created with Snap</desc><defs><linearGradient x1="0" y1="0" x2="100" y2="100" gradientUnits="userSpaceOnUse" id="linearGradientSlkfi6peu2"><stop offset="0%" stop-color="#ffffff"></stop><stop offset="100%" stop-color="#f9f9f9"></stop></linearGradient><mask id="maskSlkfi6peu8"><circle cx="110" cy="110" r="87" fill="#ffffff" style=""></circle></mask></defs><circle cx="110" cy="110" r="95" fill="url('#linearGradientSlkfi6peu2')" stroke="#f2f2f2" style="stroke-width: 15;"></circle><path id="front" fill="#0088cc" mask="url('#maskSlkfi6peu8')" stroke="#33bbff" style="stroke-width: 1;" d="M0,153.4 C120.4,159.4 169.4,158.4 220,146.4 L220,220 L0,220 z"></path><text x="50%" y="50%" fill="#333333" dy=".4em" stroke="#333333" style="font-family: &quot;Open Sans&quot;; font-size: 24px; font-weight: 600; text-anchor: middle;"><tspan>35</tspan><tspan stroke="none" style="font-size: 24px;">%</tspan></text></svg>
                                    <meter min="0" max="100" value="35" id="meterSales"></meter>
                                </div>
                                <div class="liquid-meter-selector mt-4 pt-1" id="meterSalesSel">
                                    <a href="#" data-val="35" class="active">Monthly Goal</a>
                                    <a href="#" data-val="28">Annual Goal</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="col-lg-6">
            <div class="row mb-3">
                <div class="col-xl-6">
                    <section class="card card-featured-left card-featured-primary mb-3">
                        <div class="card-body">
                            <div class="widget-summary">
                                <div class="widget-summary-col widget-summary-col-icon">
                                    <div class="summary-icon bg-primary">
                                        <i class="fas fa-life-ring"></i>
                                    </div>
                                </div>
                                <div class="widget-summary-col">
                                    <div class="summary">
                                        <h4 class="title">Support Questions</h4>
                                        <div class="info">
                                            <strong class="amount">1281</strong>
                                            <span class="text-primary">(14 unread)</span>
                                        </div>
                                    </div>
                                    <div class="summary-footer">
                                        <a class="text-muted text-uppercase" href="#">(view all)</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="col-xl-6">
                    <section class="card card-featured-left card-featured-secondary">
                        <div class="card-body">
                            <div class="widget-summary">
                                <div class="widget-summary-col widget-summary-col-icon">
                                    <div class="summary-icon bg-secondary">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                                <div class="widget-summary-col">
                                    <div class="summary">
                                        <h4 class="title">Total Profit</h4>
                                        <div class="info">
                                            <strong class="amount">$ 14,890.30</strong>
                                        </div>
                                    </div>
                                    <div class="summary-footer">
                                        <a class="text-muted text-uppercase" href="#">(withdraw)</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6">
                    <section class="card card-featured-left card-featured-tertiary mb-3">
                        <div class="card-body">
                            <div class="widget-summary">
                                <div class="widget-summary-col widget-summary-col-icon">
                                    <div class="summary-icon bg-tertiary">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                </div>
                                <div class="widget-summary-col">
                                    <div class="summary">
                                        <h4 class="title">Today's Orders</h4>
                                        <div class="info">
                                            <strong class="amount">38</strong>
                                        </div>
                                    </div>
                                    <div class="summary-footer">
                                        <a class="text-muted text-uppercase" href="#">(statement)</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="col-xl-6">
                    <section class="card card-featured-left card-featured-quaternary">
                        <div class="card-body">
                            <div class="widget-summary">
                                <div class="widget-summary-col widget-summary-col-icon">
                                    <div class="summary-icon bg-quaternary">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <div class="widget-summary-col">
                                    <div class="summary">
                                        <h4 class="title">Today's Visitors</h4>
                                        <div class="info">
                                            <strong class="amount">3765</strong>
                                        </div>
                                    </div>
                                    <div class="summary-footer">
                                        <a class="text-muted text-uppercase" href="#">(report)</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <!-- end: page -->
</section>   
@endsection
@section('foot_page')
@stop