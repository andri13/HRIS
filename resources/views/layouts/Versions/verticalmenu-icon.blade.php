<!doctype html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta content="Sparic - Laravel Admin & Dashboard Template" name="description">
		<meta content="Spruko Technologies Private Limited" name="author">
		<meta name="keywords" content="admin template, bootstrap admin template, bootstrap dashboard, admin panel template, bootstrap dashboard template, bootstrap 4 admin template, laravel admin panel, admin dashboard template, bootstrap admin, blade laravel, blade template, laravel admin template, laravel dashboard, php admin dashboard, laravel blade template"/>

		<!-- Title -->
		<title>HRIS - PT NAG</title>

        @include('layouts.verticalmenu.vertical-light.styles')

	</head>

	<body class="app sidebar-mini">

		<!--Global-Loader-->
		<div id="global-loader">
			<img src="{{URL::asset('assets/images/brand/icon.png')}}" alt="loader">
		</div>

		<div class="page">
			<div class="page-main">
				<!--app-header-->
				<div class="app-header header d-flex">
					<div class="container-fluid">

						@include('layouts.components.app-header')

					</div>
				</div>
				<!--/app-header-->

				<!--News Ticker-->
				<div class="container-fluid bg-white news-ticker">

					@include('layouts.components.news-ticket')

				</div>
				<!--/News Ticker-->

                @include('layouts.components.sidebar-menu')

                <!-- app-content-->
				<div class="app-content  my-3 my-md-5">
					<div class="side-app">

                        @yield('content')

					</div>

                    @include('layouts.components.right-sidebar')

					@yield('modals')

				</div>
				<!-- End app-content-->
			</div>

            @include('layouts.components.footer')

		</div>
		<!-- End Page -->

        @include('layouts.verticalmenu.vertical-light.scripts')

	</body>
</html>
