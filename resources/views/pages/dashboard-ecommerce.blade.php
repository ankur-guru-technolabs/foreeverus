<style>
.User{
	background: linear-gradient(45deg,#27ACA4,#000000);
}

.Subscription{
	background: linear-gradient(45deg,#FFC700,#000000);
}

.Earning{
	background: linear-gradient(45deg,#A72D90,#000000);
}
</style>

{{-- extend layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@section('title','Dashboard')

{{-- page style --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('css/pages/dashboard.css')}}">
@endsection



{{-- page content --}}
@section('content')
<div class="section">
   <!--card stats start-->
   <div id="card-stats" class="pt-0">
      <div class="row">
         <div class="col s12 m6 l6 xl3">
            <div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft ">
               <div class="padding-4 User">
                  <div class="row">
                     <div class="col s7 m7">
                        <i class="material-icons background-round mt-5">perm_identity</i>
                        <p>Users</p>
                     </div>
                     <div class="col s5 m5 right-align">
                        <h5 class="mb-0 white-text">{{$todayUsers}}</h5>
                        <p class="no-margin">Today</p>
                        <p>{{$userCount}}</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col s12 m6 l6 xl3">
            <div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft">
               <div class="padding-4 Subscription">
                  <div class="row">
                     <div class="col s7 m7">
                        <i class="material-icons background-round mt-5">add_shopping_cart</i>
                        <p>Subscription</p>
                     </div>
                     <div class="col s5 m5 right-align">
                        <h5 class="mb-0 white-text">{{$todayOrder}}</h5>
                        <p class="no-margin">Today</p>
                        <p>{{$order}}</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col s12 m6 l6 xl3">
            <div class="card gradient-45deg-green-teal gradient-shadow min-height-100 white-text animate fadeRight">
               <div class="padding-4 Earning">
                  <div class="row">
                     <div class="col s7 m7">
                        <i class="material-icons background-round mt-5">attach_money</i>
                        <p>Earning</p>
                     </div>
                     <div class="col s5 m5 right-align">
                        <h5 class="mb-0 white-text">{{$todayAmount}}</h5>
                        <p class="no-margin">Today</p>
                        <p>{{$orderAmount}}</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col s12 m6 l6 xl3">
            <div class="card gradient-45deg-amber-amber gradient-shadow min-height-100 white-text animate fadeRight">
               <div class="padding-4">
                  <div class="row">
                     <div class="col s7 m7">
                        <i class="material-icons background-round mt-5">timeline</i>
                        <p>Match</p>
                     </div>
                     <div class="col s5 m5 right-align">
                        <h5 class="mb-0 white-text">{{$todayMatch}}</h5>
                        <p class="no-margin">Today</p>
                        <p>{{$matchCount}}</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!--card stats end-->
   <!--yearly & weekly revenue chart start-->
   <div id="sales-chart" style="display: none">
      <div class="row">
         <div class="col s12 m8 l8">
            <div id="revenue-chart" class="card animate fadeUp">
               <div class="card-content">
                  <h4 class="header mt-0">
                     REVENUE FOR 2020
                     <span class="purple-text small text-darken-1 ml-1">
                        <i class="material-icons">keyboard_arrow_up</i> 15.58 %</span>
                     <a
                        class="waves-effect waves-light btn gradient-45deg-purple-deep-orange gradient-shadow right">Details</a>
                  </h4>
                  <div class="row">
                     <div class="col s12">
                        <div class="yearly-revenue-chart">
                           <canvas id="thisYearRevenue" class="firstShadow" height="350"></canvas>
                           <canvas id="lastYearRevenue" height="350"></canvas>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col s12 m4 l4">
            <div id="weekly-earning" class="card animate fadeUp">
               <div class="card-content">
                  <h4 class="header m-0">Earning <i class="material-icons right grey-text lighten-3">more_vert</i></h4>
                  <p class="no-margin grey-text lighten-3 medium-small">Mon 15 - Sun 21</p>
                  <h3 class="header">$899.39 <i class="material-icons deep-orange-text text-accent-2">arrow_upward</i>
                  </h3>
                  <canvas id="monthlyEarning" class="" height="150"></canvas>
                  <div class="center-align">
                     <p class="lighten-3">Total Weekly Earning</p>
                     <a class="waves-effect waves-light btn gradient-45deg-purple-deep-orange gradient-shadow">View
                        Full</a>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!--yearly & weekly revenue chart end-->
   <!-- Member online, Currunt Server load & Today's Revenue Chart start -->
   <div id="daily-data-chart">
      <div class="row">
         <div class="col s12 m4 l4">
            <div class="card pt-0 pb-0 animate fadeLeft">
               <div class="dashboard-revenue-wrapper padding-2 ml-2">
                  <!-- <span class="new badge gradient-45deg-light-blue-cyan gradient-shadow mt-2 mr-2"></span> -->
                  <p class="mt-2 mb-0">Total Match Profiles</p>
                  <p class="no-margin grey-text lighten-3"></p>
                  <h5>{{$matchCount}}</h5>
               </div>
               <div class="sample-chart-wrapper" style="margin-bottom: -14px; margin-top: -75px;">
                  <canvas id="custom-line-chart-sample-one" class="center"></canvas>
               </div>
            </div>
         </div>
         <div class="col s12 m4 l4 animate fadeUp">
            <div class="card pt-0 pb-0">
               <div class="dashboard-revenue-wrapper padding-2 ml-2">
                  <!-- <span class="new badge gradient-45deg-purple-deep-orange gradient-shadow mt-2 mr-2">+ 12%</span> -->
                  <p class="mt-2 mb-0">Total Users</p>
                  <p class="no-margin grey-text lighten-3"></p>
                  <h5>{{$userCount}}</h5>
               </div>
               <div class="sample-chart-wrapper" style="margin-bottom: -14px; margin-top: -75px;">
                  <canvas id="custom-line-chart-sample-two" class="center"></canvas>
               </div>
            </div>
         </div>
        <!--  <div class="col s12 m4 l4">
            <div class="card pt-0 pb-0 animate fadeRight">
               <div class="dashboard-revenue-wrapper padding-2 ml-2">
                  <span class="new badge gradient-45deg-amber-amber gradient-shadow mt-2 mr-2">+ $900</span>
                  <p class="mt-2 mb-0">Today's revenue</p>
                  <p class="no-margin grey-text lighten-3">$40,512 avg</p>
                  <h5>$ 22,300</h5>
               </div>
               <div class="sample-chart-wrapper" style="margin-bottom: -14px; margin-top: -75px;">
                  <canvas id="custom-line-chart-sample-three" class="center"></canvas>
               </div>
            </div>
         </div> -->
      </div>
   </div>
   <!-- Member online, Currunt Server load & Today's Revenue Chart start -->
   <!-- ecommerce product start-->
   <div id="ecommerce-product">
     <!--  <div class="row">
         <div class="col s12 m4">
            <div class="card animate fadeLeft">
               <div class="card-content  center">
                  <h6 class="card-title font-weight-400 mb-0">Apple Watch</h6>
                  <img src="{{asset('images/cards/watch.png')}}" alt="" class="responsive-img" />
                  <p><b>The Apple Watch</b></p>
                  <p>One day only exclusive sale on our marketplace</p>
               </div>
               <div class="card-action border-non center">
                  <a class="waves-effect waves-light btn gradient-45deg-light-blue-cyan box-shadow">$ 999/-</a>
               </div>
            </div>
         </div>
         <div class="col s12 m4">
            <div class="card animate fadeUp">
               <div class="card-content center">
                  <span class="card-title center-align">Music</span>
                  <img src="{{asset('images/cards/headphones-2.png')}}" alt="" class="responsive-img" />
               </div>
               <div class="card-action pt-0">
                  <p class="">Default Quality</p>
                  <div class="chip">192kb <i class="close material-icons">close</i></div>
                  <div class="chip">320kb <i class="close material-icons">close</i></div>
               </div>
               <div class="card-action pt-0">
                  <p class="">Save Video Quality</p>
                  <div class="switch">
                     <label> Off <input type="checkbox" /> <span class="lever"></span> On </label>
                  </div>
               </div>
            </div>
         </div>
         <div class="col s12 m4">
            <div class="card animate fadeRight">
               <div class="card-content center">
                  <h6 class="card-title font-weight-400 mb-0">iPhone</h6>
                  <img src="{{asset('images/cards/iphonec.png')}}" alt="" class="responsive-img" />
                  <p><b>The Apple iPhone X</b></p>
                  <p>One day only exclusive sale on our marketplace</p>
               </div>
               <div class="card-action border-non center">
                  <a class="waves-effect waves-light btn gradient-45deg-red-pink box-shadow">$ 299/-</a>
               </div>
            </div>
         </div>
      </div> -->
      <!-- ecommerce product end-->
      <!-- ecommerce offers start-->
     <!--  <div id="ecommerce-offer">
         <div class="row">
            <div class="col s12 m3">
               <div class="card gradient-shadow gradient-45deg-light-blue-cyan border-radius-3 animate fadeUp">
                  <div class="card-content center">
                     <img src="{{asset('images/icon/apple-watch.png')}}"
                        class="width-40 border-round z-depth-5 responsive-img" alt="image" />
                     <h5 class="white-text lighten-4">50% Off</h5>
                     <p class="white-text lighten-4">On apple watch</p>
                  </div>
               </div>
            </div>
            <div class="col s12 m3">
               <div class="card gradient-shadow gradient-45deg-red-pink border-radius-3 animate fadeUp">
                  <div class="card-content center">
                     <img src="{{asset('images/icon/printer.png')}}"
                        class="width-40 border-round z-depth-5 responsive-img" alt="images" />
                     <h5 class="white-text lighten-4">20% Off</h5>
                     <p class="white-text lighten-4">On Canon Printer</p>
                  </div>
               </div>
            </div>
            <div class="col s12 m3">
               <div class="card gradient-shadow gradient-45deg-amber-amber border-radius-3 animate fadeUp">
                  <div class="card-content center">
                     <img src="{{asset('images/icon/laptop.png')}}"
                        class="width-40 border-round z-depth-5 responsive-img" alt="image" />
                     <h5 class="white-text lighten-4">40% Off</h5>
                     <p class="white-text lighten-4">On apple macbook</p>
                  </div>
               </div>
            </div>
            <div class="col s12 m3">
               <div class="card gradient-shadow gradient-45deg-green-teal border-radius-3 animate fadeUp">
                  <div class="card-content center">
                     <img src="{{asset('images/icon/bowling.png')}}"
                        class="width-40 border-round z-depth-5 responsive-img" alt="image" />
                     <h5 class="white-text lighten-4">60% Off</h5>
                     <p class="white-text lighten-4">On any game</p>
                  </div>
               </div>
            </div>
         </div>
      </div> -->
      <!-- ecommerce offers end-->
      <!-- //////////////////////////////////////////////////////////////////////////// -->
   </div>
   <!--end container-->
</div>
@endsection

{{-- vendor script --}}
@section('vendor-script')
<script src="{{asset('vendors/chartjs/chart.min.js')}}"></script>
@endsection

{{-- page script --}}
@section('page-script')
<!-- <script src="{{asset('js/scripts/dashboard-ecommerce.js')}}"></script> -->
<script type="text/javascript">
   // Dashboard - eCommerce
//----------------------
(function(window, document, $) {
   //Sample toast
/*   setTimeout(function() {
      M.toast({ html: "Hey! I am a toast." });
   }, 2000);*/

   // Line chart with color shadow: Revenue for 2018 Chart
   var thisYearctx = document.getElementById("thisYearRevenue").getContext("2d");
   var lastYearctx = document.getElementById("lastYearRevenue").getContext("2d");

   // Chart shadow LineAlt
   Chart.defaults.LineAlt = Chart.defaults.line;
   var draw = Chart.controllers.line.prototype.draw;
   var custom = Chart.controllers.line.extend({
      draw: function() {
         draw.apply(this, arguments);
         var ctx = this.chart.chart.ctx;
         var _stroke = ctx.stroke;
         ctx.stroke = function() {
            ctx.save();
            ctx.shadowColor = "rgba(156, 46, 157,0.5)";
            ctx.shadowBlur = 20;
            ctx.shadowOffsetX = 2;
            ctx.shadowOffsetY = 20;
            _stroke.apply(this, arguments);
            ctx.restore();
         };
      }
   });
   Chart.controllers.LineAlt = custom;

   // Chart shadow LineAlt2
   Chart.defaults.LineAlt2 = Chart.defaults.line;
   var draw = Chart.controllers.line.prototype.draw;
   var custom = Chart.controllers.line.extend({
      draw: function() {
         draw.apply(this, arguments);
         var ctx = this.chart.chart.ctx;
         var _stroke = ctx.stroke;
         ctx.stroke = function() {
            ctx.save();
            _stroke.apply(this, arguments);
            ctx.restore();
         };
      }
   });
   Chart.controllers.LineAlt2 = custom;

   var thisYearData = {
      labels: ["January", "February", "March", "April", "May", "June","July","August","September","October","November","December"],
      datasets: [
         {
            label: "This year",
            data: [45, 62, 15, 78, 58, 98, 10],
            fill: false,
            pointRadius: 2.2,
            pointBorderWidth: 1,
            borderColor: "#9C2E9D",
            borderWidth: 5,
            pointBorderColor: "#9C2E9D",
            pointHighlightFill: "#9C2E9D",
            pointHoverBackgroundColor: "#9C2E9D",
            pointHoverBorderWidth: 2
         }
      ]
   };

   var lastYearData = {
      labels: ["January", "February", "March", "April", "May", "June"],
      datasets: [
         {
            label: "Last year dataset",
            data: [12, 6, 25, 58, 38, 68],
            borderDash: [15, 5],
            fill: false,
            pointRadius: 0,
            pointBorderWidth: 0,
            borderColor: "#E4E4E4",
            borderWidth: 5
         }
      ]
   };
   var thisYearOption = {
      responsive: true,
      maintainAspectRatio: true,
      datasetStrokeWidth: 3,
      pointDotStrokeWidth: 4,
      tooltipFillColor: "rgba(0,0,0,0.6)",
      legend: {
         display: false,
         position: "bottom"
      },
      hover: {
         mode: "label"
      },
      scales: {
         xAxes: [
            {
               display: false
            }
         ],
         yAxes: [
            {
               ticks: {
                  padding: 10,
                  stepSize: 20,
                  max: 100,
                  min: 0,
                  fontColor: "#9e9e9e"
               },
               gridLines: {
                  display: true,
                  drawBorder: false,
                  lineWidth: 1,
                  zeroLineColor: "#e5e5e5"
               }
            }
         ]
      },
      title: {
         display: false,
         fontColor: "#FFF",
         fullWidth: false,
         fontSize: 40,
         text: "82%"
      }
   };
   var lastYearOption = {
      responsive: true,
      maintainAspectRatio: true,
      datasetStrokeWidth: 3,
      pointDotStrokeWidth: 4,
      tooltipFillColor: "rgba(0,0,0,0.6)",
      legend: {
         display: false,
         position: "bottom"
      },
      hover: {
         mode: "label"
      },
      scales: {
         xAxes: [
            {
               display: false
            }
         ],
         yAxes: [
            {
               ticks: {
                  padding: 10,
                  stepSize: 20,
                  max: 100,
                  min: 0
               },
               gridLines: {
                  display: true,
                  drawBorder: false,
                  lineWidth: 1
               }
            }
         ]
      },
      title: {
         display: false,
         fontColor: "#FFF",
         fullWidth: false,
         fontSize: 40,
         text: "82%"
      }
   };

   var thisYearChart = new Chart(thisYearctx, {
      type: "LineAlt",
      data: thisYearData,
      options: thisYearOption
   });

   var lastYearChart = new Chart(lastYearctx, {
      type: "LineAlt2",
      data: lastYearData,
      options: lastYearOption
   });

   //  Monthly Earning Chart : Line chart with shadow
   //---------------------------------------------------

   // Chart shadow
   Chart.defaults.earnningLineShadow = Chart.defaults.line;
   var draw = Chart.controllers.line.prototype.draw;
   var custom = Chart.controllers.line.extend({
      draw: function() {
         draw.apply(this, arguments);
         var ctx = this.chart.chart.ctx;
         var _stroke = ctx.stroke;
         ctx.stroke = function() {
            ctx.save();
            ctx.shadowColor = "rgba(255, 111, 0, 0.3";
            ctx.shadowBlur = 14;
            ctx.shadowOffsetX = 2;
            ctx.shadowOffsetY = 16;
            _stroke.apply(this, arguments);
            ctx.restore();
         };
      }
   });
   Chart.controllers.earnningLineShadow = custom;

   //Chart gradient strock
   var Earningctx = document.getElementById("monthlyEarning").getContext("2d");
   var gradientStroke = Earningctx.createLinearGradient(500, 0, 0, 200);
   gradientStroke.addColorStop(0, "#ffca28");
   gradientStroke.addColorStop(1, "#ff6f00");
   //Chart data
   var earningChartData = {
      labels: ["1", "2", "3", "4", "5", "6", "7", "8", "8", "8", "8", "8", "8", "8"],
      datasets: [
         {
            label: "This year dataset",
            lineTension: 0,
            fill: false,
            pointRadius: 0,
            pointBorderWidth: 0,
            borderColor: gradientStroke,
            borderWidth: 3,
            data: [50, 40, 30, 55, 50, 90, 40, 50, 40, 70, 55, 80, 50, 55]
         }
      ]
   };

   var earningChartOptions = {
      responsive: true,
      maintainAspectRatio: true,
      datasetStrokeWidth: 3,
      pointDotStrokeWidth: 4,
      tooltipFillColor: "rgba(0,0,0,0.6)",
      layout: {
         padding: {
            left: 50,
            right: 0,
            top: 0,
            bottom: 0
         }
      },
      legend: {
         display: false,
         position: "bottom"
      },
      hover: {
         mode: "label"
      },
      scales: {
         xAxes: [
            {
               display: false
            }
         ],
         yAxes: [
            {
               display: false
            }
         ]
      },
      title: {
         display: false,
         fontColor: "#FFF",
         fullWidth: false,
         fontSize: 40,
         text: "82%"
      }
   };

   var MonthlyEarningChart = new Chart(Earningctx, {
      type: "earnningLineShadow",
      data: earningChartData,
      options: earningChartOptions
   });

   // Sampel Line Chart One
   // --------------------------

   // Options
   var SLOption = {
      responsive: true,
      maintainAspectRatio: true,
      datasetStrokeWidth: 3,
      pointDotStrokeWidth: 4,
      tooltipFillColor: "rgba(0,0,0,0.6)",
      legend: {
         display: false,
         position: "bottom"
      },
      hover: {
         mode: "label"
      },
      scales: {
         xAxes: [
            {
               display: false
            }
         ],
         yAxes: [
            {
               display: false
            }
         ]
      },
      title: {
         display: false,
         fontColor: "#FFF",
         fullWidth: false,
         fontSize: 40,
         text: "82%"
      }
   };
   var SLlabels = ["January", "February", "March", "April", "May", "June","July","August","September","October","November","December"];

   var LineSL1ctx = document.getElementById("custom-line-chart-sample-one").getContext("2d");

   var gradientStroke = LineSL1ctx.createLinearGradient(300, 0, 0, 300);
   gradientStroke.addColorStop(0, "#0288d1");
   gradientStroke.addColorStop(1, "#26c6da");

   var gradientFill = LineSL1ctx.createLinearGradient(300, 0, 0, 300);
   gradientFill.addColorStop(0, "#0288d1");
   gradientFill.addColorStop(1, "#26c6da");
   const monthlyMatchCount = JSON.parse('<?php echo $monthlyMatchCount;?>');
   var SL1Chart = new Chart(LineSL1ctx, {
      type: "line",
      data: {
         labels: SLlabels,
         datasets: [
            {
               label: "Match Profile",
               borderColor: gradientStroke,
               pointColor: "#fff",
               pointBorderColor: gradientStroke,
               pointBackgroundColor: "#fff",
               pointHoverBackgroundColor: gradientStroke,
               pointHoverBorderColor: gradientStroke,
               pointRadius: 4,
               pointBorderWidth: 1,
               pointHoverRadius: 4,
               pointHoverBorderWidth: 1,
               fill: true,
               backgroundColor: gradientFill,
               borderWidth: 1,
               data: monthlyMatchCount
            }
         ]
      },
      options: SLOption
   });

   // //Sampel Line Chart Two

   var LineSL2ctx = document.getElementById("custom-line-chart-sample-two").getContext("2d");

   var gradientStroke = LineSL2ctx.createLinearGradient(500, 0, 0, 200);
   gradientStroke.addColorStop(0, "#8e24aa");
   gradientStroke.addColorStop(1, "#ff6e40");

   var gradientFill = LineSL2ctx.createLinearGradient(500, 0, 0, 200);
   gradientFill.addColorStop(0, "#8e24aa");
   gradientFill.addColorStop(1, "#ff6e40");
   const montlyUsersList = JSON.parse('<?php echo $montlyUsersList;?>');
   var SL2Chart = new Chart(LineSL2ctx, {
      type: "line",
      data: {
         labels: SLlabels,
         datasets: [
            {
               label: "Users",
               borderColor: gradientStroke,
               pointColor: "#fff",
               pointBorderColor: gradientStroke,
               pointBackgroundColor: "#fff",
               pointHoverBackgroundColor: gradientStroke,
               pointHoverBorderColor: gradientStroke,
               pointRadius: 4,
               pointBorderWidth: 1,
               pointHoverRadius: 4,
               pointHoverBorderWidth: 1,
               fill: true,
               backgroundColor: gradientFill,
               borderWidth: 1,
               data: montlyUsersList
            }
         ]
      },
      options: SLOption
   });

   //Sampel Line Chart Three

   var LineSL3ctx = document.getElementById("custom-line-chart-sample-three").getContext("2d");

   var gradientStroke = LineSL3ctx.createLinearGradient(500, 0, 0, 200);
   gradientStroke.addColorStop(0, "#ff6f00");
   gradientStroke.addColorStop(1, "#ffca28");

   var gradientFill = LineSL3ctx.createLinearGradient(500, 0, 0, 200);
   gradientFill.addColorStop(0, "#ff6f00");
   gradientFill.addColorStop(1, "#ffca28");

   var SL3Chart = new Chart(LineSL3ctx, {
      type: "line",
      data: {
         labels: SLlabels,
         datasets: [
            {
               label: "My Second dataset",
               borderColor: gradientStroke,
               pointColor: "#fff",
               pointBorderColor: gradientStroke,
               pointBackgroundColor: "#fff",
               pointHoverBackgroundColor: gradientStroke,
               pointHoverBorderColor: gradientStroke,
               pointRadius: 4,
               pointBorderWidth: 1,
               pointHoverRadius: 4,
               pointHoverBorderWidth: 1,
               fill: true,
               backgroundColor: gradientFill,
               borderWidth: 1,
               data: [24, 18, 20, 30, 40, 43]
            }
         ]
      },
      options: SLOption
   });
})(window, document, jQuery);

</script>
@endsection