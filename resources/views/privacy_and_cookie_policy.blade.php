<!doctype html>
<html lang="en" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy And Cookie Policy</title>

     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<!-- <link rel="stylesheet" type="text/css" href="css/bootstrap-grid.min.css" media="all" /> -->
	<!-- <link rel="stylesheet" type="text/css" href="css/bootstrap-reboot.min.css" media="all" /> -->
	<!-- <link rel="stylesheet" type="text/css" href="css/bootstrap-utilities.min.css" media="all" /> -->
	<!-- <link rel="stylesheet" type="text/css" href="css/all.min.css" media="all" /> -->
	<link rel="stylesheet" type="text/css" href="css/sticky-footer-navbar.css" media="all" />
	
    <!-- Bootstrap core CSS -->


    <style>
		main{
			padding-top:130px
		}
		.top-header{
			background: white;
			box-shadow: -1px 1px 6px #828282;
		}
		.app-logo{
			max-width:100px;
			width:100%;
		}
		.content{
			padding: 50px 0px 120px;
		}
		// .content h1{
			// margin-bottom: 30px;
		// }
		.heading{
			font-size: 20px;
			text-transform: uppercase;
			font-weight: 700;
			text-align: center;
			line-height: 2;
		}
		.sub-heading{
			font-size: 16px;
			text-transform: uppercase;
			font-weight: 600;
			line-height: 2;
		}
		.text{
			font-size: 15px;
			line-height: 1.8;
			text-align: justify;
			text-justify: inter-word;
		}
		.footer-style{
			background: white;
			border-top: 1px solid #e4e4e4;
			font-size: 14px;
		}
		.footer-style a{
			text-decoration: none;
			color: #000;
			font-size: 14px;
		}
		.quick-menu ul li {
			list-style-type: none;
			display: inline-block;
			padding:0px 2px;
			position: relative;
		}
    </style>

    
 <!-- Custom styles for this template -->
</head>
<body class="d-flex flex-column h-100">
   <div class="top-header fixed-top">
	<div class="container">
		<header class="d-flex justify-content-center py-3">
		<a href="#">
			<!-- <img src="https://www.zodiap.org/public/logos/logo_web_2.png?v=FFLVuy"> -->
			<img src="{{url('/')}}/app-icon.png" class="app-logo">
		</a>
		</header>
	</div>
  </div>
<!-- Begin page content -->
<main class="flex-shrink-0">
  <div class="container">
	<div class="content">
	{!! $privacy_and_cookie_policy->description !!}
   </div>
  </div>
</main>

<footer class="footer mt-auto pt-3 footer-style fixed-bottom">
  <div class="container">
	<div class="row">
		<div class="col-md-4">
			<i class="fa fa-copyright" aria-hidden="true"></i>Copyright © 2021 Gethingd, All Rights Reserved.
		</div>

		<div class="col-md-4">
			<div class="quick-menu">
				<ul>
					<li><a href="#">Terms &amp; Conditions</a></li>
					<li>|</li>
					<li><a href="#">Privacy Policy</a></li>
				</ul>
			</div>
		</div>

		<div class="col-md-4"><a style="font-weight:bold" href="https://www.gurutechnolabs.com/dating-app-development/" target="_blank">Dating App Development By Guru TechnoLabs</a>. </div>
		</div>
  </div>
</footer>

	
    <!-- <script src="js/bootstrap.bundle.min.js"></script> -->

      
  </body>
</html>
