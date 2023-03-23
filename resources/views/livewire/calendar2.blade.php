@extends('layouts.app')

@section('styles')

	<!-- Fullcalendar css -->
	<link href="{{URL::asset('assets/plugins/fullcalendar/fullcalendar.css')}}" rel='stylesheet' />
	<link href="{{URL::asset('assets/plugins/fullcalendar/fullcalendar.print.min.css')}}" rel='stylesheet' media='print' />

@endsection

@section('content')

						<!-- page-header -->
						<div class="page-header">
							<ol class="breadcrumb"><!-- breadcrumb -->
								<li class="breadcrumb-item"><a href="#">Calendar</a></li>
								<li class="breadcrumb-item active" aria-current="page">Full Calendar</li>
							</ol><!-- End breadcrumb -->
							<div class="ml-auto">
								<div class="input-group">
									<a href="#" class="btn btn-secondary text-white mr-2 btn-sm" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Rating">
										<span>
											<i class="fa fa-star"></i>
										</span>
									</a>
									<a href="{{url('lockscreen')}}" class="btn btn-primary text-white mr-2 btn-sm" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="lock">
										<span>
											<i class="fa fa-lock"></i>
										</span>
									</a>
									<a href="#" class="btn btn-warning text-white btn-sm" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Add New">
										<span>
											<i class="fa fa-plus"></i>
										</span>
									</a>
								</div>
							</div>
						</div>
						<!-- End page-header -->

						<!-- row -->
						<div class="row">
							<div class="col-md-12">
								<div class="card">
									<div class="card-header pb-0">
										<h3 class="mb-0 card-title">Default Calendar</h3>
									</div>
									<div class="card-body">
										<div id='calendar1'></div>
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<div class="card">
									<div class="card-header pb-0">
										<h3 class="mb-0 card-title">List Calendar</h3>
									</div>
									<div class="card-body">
										<div id='calendar'></div>
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<div class="card">
									<div class="card-header pb-0">
										<h3 class="mb-0 card-title">Calender With different Color Events</h3>
									</div>
									<div class="card-body">
										 <div id='calendar2'></div>
									</div>
								</div>
							</div>
						</div>
						<!-- row end -->

@endsection('content')

@section('scripts')

		<!--Jquery Sparkline js-->
		<script src="{{URL::asset('assets/plugins/vendors/jquery.sparkline.min.js')}}"></script>

		<!-- Chart Circle js-->
		<script src="{{URL::asset('assets/plugins/vendors/circle-progress.min.js')}}"></script>
		
        <!--Time Counter js-->
		<script src="{{URL::asset('assets/plugins/counters/jquery.missofis-countdown.js')}}"></script>
		<script src="{{URL::asset('assets/plugins/counters/counter.js')}}"></script>
		
        <!--Fullcalendar js-->
		<script src="{{URL::asset('assets/plugins/fullcalendar/moment.min.js')}}"></script>
		<script src="{{URL::asset('assets/plugins/fullcalendar/jquery-ui.min.js')}}"></script>
		<script src="{{URL::asset('assets/plugins/fullcalendar/fullcalendar.min.js')}}"></script>
		<script src="{{URL::asset('assets/js/fullcalendar.js')}}"></script>

@endsection