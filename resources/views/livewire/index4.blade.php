@extends('layouts.app')

@section('styles')

	<!-- Owl Theme css-->
	<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet">

	<!-- Morris  Charts css-->
	<link href="{{URL::asset('assets/plugins/morris/morris.css')}}" rel="stylesheet" />

@endsection

@section('content')

					    <!-- page-header -->
						<div class="page-header">
							<ol class="breadcrumb"><!-- breadcrumb -->
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Dashboard 04</li>
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

						<div class="row">
							<div class="col-xl-8 col-lg-12 col-md-12">
								<div class="card">
									<div class="card-header custom-header pb-0">
										<div>
											<h3 class="card-title">Patients Details</h3>
											<h6 class="card-subtitle">Overview of all patients information</h6>
										</div>
										<div class="card-options">
											<a href="" class="mr-4 text-default" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
												<span class="fa fa-ellipsis-v"></span>
											</a>
											<ul class="dropdown-menu dropdown-menu-right" role="menu">
												<li><a href="#"><i class="si si-plus mr-2"></i>Add</a></li>
												<li><a href="#"><i class="si si-trash mr-2"></i>Remove</a></li>
												<li><a href="#"><i class="si si-eye mr-2"></i>View</a></li>
												<li><a href="#"><i class="si si-settings mr-2"></i>More</a></li>
											</ul>
										</div>
									</div>
									<div class="card-body">
										<div class="row">
											<div class="col-lg-6">
												<div id="chart-details" class="chartsh"></div>
											</div>
											<div class="col-lg-6">
												<h3>Patient Details</h3>
												<p class="text-muted">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi</p>
												<h4 class="font-weight-semibold mb-2">Total patients</h4>
												<div class="row">
													<div class="col mb-5 ">
														<h5 class="mb-2 d-block">
															<span class="fs-14 text-uppercase">In patients</span>
															<span class="float-right">65,485</span>
														</h5>
														<div class="progress progress-md h-1">
															<div class="progress-bar progress-bar-striped progress-bar-animated bg-primary w-65"></div>
														</div>
													</div>
													<div class="col mb-5 ">
														<h5 class="mb-2 d-block">
															<span class="fs-14 text-uppercase">Out patients</span>
															<span class="float-right">98,754</span>
														</h5>
														<div class="progress progress-md h-1">
															<div class="progress-bar progress-bar-striped progress-bar-animated bg-primary w-75"></div>
														</div>
													</div>
												</div>
												<h4 class="font-weight-semibold mb-2">Weekly</h4>
												<div class="row">
													<div class="col mb-5 ">
														<h5 class="mb-2 d-block">
															<span class="fs-14 text-uppercase">In patients</span>
															<span class="float-right">485</span>
														</h5>
														<div class="progress progress-md h-1">
															<div class="progress-bar progress-bar-striped progress-bar-animated bg-secondary w-65"></div>
														</div>
													</div>
													<div class="col mb-5 ">
														<h5 class="mb-2 d-block">
															<span class="fs-14 text-uppercase">Out patients</span>
															<span class="float-right">754</span>
														</h5>
														<div class="progress progress-md h-1">
															<div class="progress-bar progress-bar-striped progress-bar-animated bg-secondary w-75"></div>
														</div>
													</div>
												</div>
												<h4 class="font-weight-semibold mb-2">Monthly</h4>
												<div class="row">
													<div class="col mb-5 ">
														<h5 class="mb-2 d-block">
															<span class="fs-14 text-uppercase">In patients</span>
															<span class="float-right">5,485</span>
														</h5>
														<div class="progress progress-md h-1">
															<div class="progress-bar progress-bar-striped progress-bar-animated bg-warning w-65"></div>
														</div>
													</div>
													<div class="col mb-5 ">
														<h5 class="mb-2 d-block">
															<span class="fs-14 text-uppercase">Out patients</span>
															<span class="float-right">8,754</span>
														</h5>
														<div class="progress progress-md h-1">
															<div class="progress-bar progress-bar-striped progress-bar-animated bg-warning w-75"></div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xl-4 col-lg-12 col-md-12">
								<div class="card">
									<div class="card-header custom-header pb-0">
										<div>
											<h3 class="card-title">Patients Satisfaction</h3>
											<h6 class="card-subtitle">Overview of all patients information</h6>
										</div>
										<div class="card-options">
											<a href="" class="mr-4 text-default" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
												<span class="fa fa-ellipsis-v"></span>
											</a>
											<ul class="dropdown-menu dropdown-menu-right" role="menu">
												<li><a href="#"><i class="si si-plus mr-2"></i>Add</a></li>
												<li><a href="#"><i class="si si-trash mr-2"></i>Remove</a></li>
												<li><a href="#"><i class="si si-eye mr-2"></i>View</a></li>
												<li><a href="#"><i class="si si-settings mr-2"></i>More</a></li>
											</ul>
										</div>
									</div>
									<div class="card-body">
										<div id="chart-details2" class="chartsh"></div>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-xxl-3">
								<div class="card overflow-hidden">
									<div class="card-header custom-header">
										<div>
											<h3 class="card-title">Contacts</h3>
											<h6 class="card-subtitle">Overview of Friends information</h6>
										</div>
										<div class="card-options">
											<a href="" class="mr-4 text-default" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
												<span class="fa fa-ellipsis-v"></span>
											</a>
											<ul class="dropdown-menu dropdown-menu-right" role="menu">
												<li><a href="#"><i class="si si-plus mr-2"></i>Add</a></li>
												<li><a href="#"><i class="si si-trash mr-2"></i>Remove</a></li>
												<li><a href="#"><i class="si si-eye mr-2"></i>View</a></li>
												<li><a href="#"><i class="si si-settings mr-2"></i>More</a></li>
											</ul>
										</div>
									</div>
									<div class="card-body p-0">
										<div class="list-group list-group-flush ">
											<div class="list-group-item d-flex  align-items-center">
												<div class="mr-2">
													<span class="avatar avatar-md brround cover-image" data-image-src="{{URL::asset('assets/images/users/female/12.jpg')}}"><span class="avatar-status bg-green"></span></span>
												</div>
												<div class="">
													<div class="font-weight-semibold">Mozelle Belt</div>
												</div>
												<div class="ml-auto">
													<a href="#" class="btn btn-sm btn-light"><i class="fa fa-comment"></i></a>
													<a href="#" class="btn btn-sm btn-light"><i class="fa fa-phone"></i></a>
												</div>
											</div>
											<div class="list-group-item d-flex  align-items-center">
												<div class="mr-2">
													<span class="avatar avatar-md brround cover-image" data-image-src="{{URL::asset('assets/images/users/female/21.jpg')}}"></span>
												</div>
												<div class="">
													<div class="font-weight-semibold">Florinda Carasco</div>
												</div>
												<div class="ml-auto">
													<a href="#" class="btn btn-sm btn-light"><i class="fa fa-comment"></i></a>
													<a href="#" class="btn btn-sm btn-light"><i class="fa fa-phone"></i></a>
												</div>
											</div>
											<div class="list-group-item d-flex  align-items-center">
												<div class="mr-2">
													<span class="avatar avatar-md brround cover-image" data-image-src="{{URL::asset('assets/images/users/female/29.jpg')}}"><span class="avatar-status bg-green"></span></span>
												</div>
												<div class="">
													<div class="font-weight-semibold">Alina Bernier</div>
												</div>
												<div class="ml-auto">
													<a href="#" class="btn btn-sm btn-light"><i class="fa fa-comment"></i></a>
													<a href="#" class="btn btn-sm btn-light"><i class="fa fa-phone"></i></a>
												</div>
											</div>
											<div class="list-group-item d-flex  align-items-center">
												<div class="mr-2">
													<span class="avatar avatar-md brround cover-image" data-image-src="{{URL::asset('assets/images/users/female/2.jpg')}}"><span class="avatar-status bg-green"></span></span>
												</div>
												<div class="">
													<div class="font-weight-semibold">Zula Mclaughin</div>
												</div>
												<div class="ml-auto">
													<a href="#" class="btn btn-sm btn-light"><i class="fa fa-comment"></i></a>
													<a href="#" class="btn btn-sm btn-light"><i class="fa fa-phone"></i></a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card overflow-hidden">
									<div class="card-body pb-0">
										<div class="d-flex">
											<div>
												<p class="mb-1 text-uppercase">Daily Admissions</p>
												<h2 class="fs-2 mb-2">8,965</h2>
												<div class="text-muted"><i class="fa fa-signal text-success mr-1"></i> <span class="font-weight-bold text-body">9.25%</span> (30 days)</div>
											</div>
											<i class="ti-write ml-auto fs-3 op4 text-danger"></i>
										</div>
										<div class="chart-wrapper mt-3">
											<canvas id="AreaChart4" class="h-8"></canvas>
										</div>
									</div>
								</div>
								<div class="card overflow-hidden">
									<div class="card-body pb-0">
										<div class="d-flex">
											<div>
												<p class="mb-1 text-uppercase">Daily Out patients</p>
												<h2 class="fs-2 mb-2">19,584</h2>
												<div class="text-muted"><i class="fa fa-signal text-success mr-1"></i> <span class="font-weight-bold text-body">10.15%</span> (30 days)</div>
											</div>
											<i class="ti-bar-chart ml-auto fs-3 op4 text-primary"></i>
										</div>
										<div class="chart-wrapper mt-3">
											<canvas id="AreaChart5" class="h-8"></canvas>
										</div>
									</div>
								</div>
								<div class="card bg-gradient-secondary text-white overflow-hidden">
									<div class="card-body pb-0">
										<div class="d-flex">
											<div>
												<p class="mb-1 text-white text-uppercase">Daily Updates</p>
												<h2 class="fs-2 mb-2">626</h2>
												<div class="text-white-50"><i class="fa fa-signal text-white mr-1"></i> <span class="font-weight-bold text-white">0.85%</span> (30 days)</div>
											</div>
											<i class="ti-export ml-auto fs-3 op4"></i>
										</div>
										<div class="chart-wrapper mt-3">
											<canvas id="AreaChart6" class="h-8"></canvas>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xxl-9">
								<div class="row">
									<div class="col-xl-8 col-lg-7">
										<div class="card">
											<div class="card-header custom-header pb-0">
												<div>
													<h3 class="card-title">Top Doctors</h3>
													<h6 class="card-subtitle">Overview of all patients information</h6>
												</div>
												<div class="card-options">
													<a href="" class="mr-4 text-default" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
														<span class="fa fa-ellipsis-v"></span>
													</a>
													<ul class="dropdown-menu dropdown-menu-right" role="menu">
														<li><a href="#"><i class="si si-plus mr-2"></i>Add</a></li>
														<li><a href="#"><i class="si si-trash mr-2"></i>Remove</a></li>
														<li><a href="#"><i class="si si-eye mr-2"></i>View</a></li>
														<li><a href="#"><i class="si si-settings mr-2"></i>More</a></li>
													</ul>
												</div>
											</div>
											<div class="card-body pb-1">
												<div class="row">
													<div class="col-xl-4 col-lg-6 col-sm-6">
														<div class="card overflow-hidden border p-0">
															<div class="card-body">
																<div class="text-center">
																	<img src="{{URL::asset('assets/images/users/female/1.jpg')}}" alt="Generic placeholder image" class="avatar brround avatar-xl">
																</div>
																<div class="item-card2 mt-3">
																	<div class="item-card2-desc text-center">
																		<div class="item-card2-text">
																			<a href="" class="text-dark"><h5 class="font-weight-semibold mb-1">Dr.Sid Quebedeaux</h5></a>
																		</div>
																		<p class="fs-16 mb-0">Allergist</p>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="col-xl-4 col-lg-6 col-sm-6">
														<div class="card overflow-hidden border p-0">
															<div class="card-body">
																<div class="text-center">
																	<img src="{{URL::asset('assets/images/users/male/1.jpg')}}" alt="Generic placeholder image" class="avatar brround avatar-xl">
																</div>
																<div class="item-card2 mt-3">
																	<div class="item-card2-desc text-center">
																		<div class="item-card2-text">
																			<a href="" class="text-dark"><h5 class="font-weight-semibold mb-1">Dr.Lamar Haught</h5></a>
																		</div>
																		<p class="fs-16 mb-0">Cardiologist</p>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="col-xl-4 col-lg-6 col-sm-6">
														<div class="card overflow-hidden border p-0">
															<div class="card-body">
																<div class="text-center">
																	<img src="{{URL::asset('assets/images/users/female/18.jpg')}}" alt="Generic placeholder image" class="avatar brround avatar-xl">
																</div>
																<div class="item-card2 mt-3">
																	<div class="item-card2-desc text-center">
																		<div class="item-card2-text">
																			<a href="" class="text-dark"><h5 class="font-weight-semibold mb-1">Dr.Sharie Doten</h5></a>
																		</div>
																		<p class="fs-16 mb-0">Allergist</p>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="col-xl-4 col-lg-6 col-sm-6">
														<div class="card overflow-hidden border p-0">
															<div class="card-body">
																<div class="text-center">
																	<img src="{{URL::asset('assets/images/users/male/2.jpg')}}" alt="Generic placeholder image" class="avatar brround avatar-xl">
																</div>
																<div class="item-card2 mt-3">
																	<div class="item-card2-desc text-center">
																		<div class="item-card2-text">
																			<a href="" class="text-dark"><h5 class="font-weight-semibold mb-1">Dr.Rupert Hauser</h5></a>
																		</div>
																		<p class="fs-16 mb-0">Dentist</p>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="col-xl-4 col-lg-6 col-sm-6">
														<div class="card overflow-hidden border p-0">
															<div class="card-body">
																<div class="text-center">
																	<img src="{{URL::asset('assets/images/users/female/10.jpg')}}" alt="Generic placeholder image" class="avatar brround avatar-xl">
																</div>
																<div class="item-card2 mt-3">
																	<div class="item-card2-desc text-center">
																		<div class="item-card2-text">
																			<a href="" class="text-dark"><h5 class="font-weight-semibold mb-1">Dr.Trena Conner</h5></a>
																		</div>
																		<p class="fs-16 mb-0">Gynocologist</p>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="col-xl-4 col-lg-6 col-sm-6">
														<div class="card overflow-hidden border p-0">
															<div class="card-body">
																<div class="text-center">
																	<img src="{{URL::asset('assets/images/users/male/10.jpg')}}" alt="Generic placeholder image" class="avatar brround avatar-xl">
																</div>
																<div class="item-card2 mt-3">
																	<div class="item-card2-desc text-center">
																		<div class="item-card2-text">
																			<a href="" class="text-dark"><h5 class="font-weight-semibold mb-1">Dr.Ramiro Stoner</h5></a>
																		</div>
																		<p class="fs-16 mb-0">Cardiologist</p>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card">
											<div class="card-header custom-header pb-0">
												<div>
													<h3 class="card-title">Country Wise Donors</h3>
													<h6 class="card-subtitle">Overview of all patients information</h6>
												</div>
												<div class="card-options">
													<a href="" class="mr-4 text-default" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
														<span class="fa fa-ellipsis-v"></span>
													</a>
													<ul class="dropdown-menu dropdown-menu-right" role="menu">
														<li><a href="#"><i class="si si-plus mr-2"></i>Add</a></li>
														<li><a href="#"><i class="si si-trash mr-2"></i>Remove</a></li>
														<li><a href="#"><i class="si si-eye mr-2"></i>View</a></li>
														<li><a href="#"><i class="si si-settings mr-2"></i>More</a></li>
													</ul>
												</div>
											</div>
											<div class="card-body">
												<div class="row">
													<div class="col-md-12">
														<div id="echart1" class="chartsh chart-dropshadow"></div>
														<div>
															<p class="mt-3 mb-0 text-muted">Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam</p>
															<div class="row">
																<div class="col text-center mt-4">
																	<p class="mb-1 font-weight-semibold text-uppercase">Total Organ Donors</p>
																	<h3 class="mb-0 font-weight-semibold">63,254</h3>
																</div>
																<div class="col text-center mt-4">
																	<p class="mb-1 font-weight-semibold  text-uppercase">Males</p>
																	<h3 class="mb-0 font-weight-semibold">32,548</h3>
																</div>
																<div class="col text-center mt-4">
																	<p class="mb-1 font-weight-semibold text-uppercase">Females</p>
																	<h3 class="mb-0 font-weight-semibold">30,706</h3>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-xl-4 col-lg-5">
										<div class="card">
											<div class="card-header custom-header">
												<div>
													<h3 class="card-title">Patient Reviews</h3>
													<h6 class="card-subtitle">Overview of this week</h6>
												</div>
												<div class="card-options">
													<a href="" class="mr-4 text-default" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
														<span class="fa fa-ellipsis-v"></span>
													</a>
													<ul class="dropdown-menu dropdown-menu-right" role="menu">
														<li><a href="#"><i class="si si-plus mr-2"></i>Add</a></li>
														<li><a href="#"><i class="si si-trash mr-2"></i>Remove</a></li>
														<li><a href="#"><i class="si si-eye mr-2"></i>View</a></li>
														<li><a href="#"><i class="si si-settings mr-2"></i>More</a></li>
													</ul>
												</div>
											</div>
											<div class="card-body p-0 ">
												<div class="list-group list-lg-group list-group-flush">
													<a class="list-group-item list-group-item-action" href="#">
														<div class="media mt-0">
															<img class="avatar-xxl rounded-circle mr-3" src="{{URL::asset('assets/images/users/female/2.jpg')}}" alt="Image description">
															<div class="media-body">
																<div class="d-md-flex align-items-center">
																	<h4 class="mb-3">
																		Samantha Wilson
																	</h4>
																	<small class="text-primary ml-md-auto"><i class="fe fe-calendar mr-1"></i> 28 Feb 2019</small>
																</div>
																<p class="mb-0 text-muted">Itaque earum rerum hic tenetur a sapiente reiciendis voluptatibus.</p>
															</div>
														</div>
													</a>
													<a class="list-group-item list-group-item-action" href="#">
														<div class="media mt-0">
															<img class="avatar-xxl rounded-circle mr-3" src="{{URL::asset('assets/images/users/male/2.jpg')}}" alt="Image description">
															<div class="media-body">
																<div class="d-md-flex align-items-center">
																	<h4 class="mb-3">
																		Kevin North
																	</h4>
																	<small class="text-primary ml-md-auto"><i class="fe fe-calendar mr-1"></i> 26 Feb 2019</small>
																</div>
																<p class="mb-0 text-muted">Itaque earum rerum hic tenetur a sapiente reiciendis voluptatibus.</p>
															</div>
														</div>
													</a>
													<a class="list-group-item list-group-item-action" href="#">
														<div class="media mt-0">
															<img class="avatar-xxl rounded-circle mr-3" src="{{URL::asset('assets/images/users/male/12.jpg')}}" alt="Image description">
															<div class="media-body">
																<div class="d-md-flex align-items-center">
																	<h4 class="mb-3">
																		Steven Fisher
																	</h4>
																	<small class="text-primary ml-md-auto"><i class="fe fe-calendar mr-1"></i> 26 Feb 2019</small>
																</div>
																<p class="mb-0 text-muted">Itaque earum rerum hic tenetur a sapiente reiciendis voluptatibus.</p>
															</div>
														</div>
													</a>
													<a class="list-group-item list-group-item-action br-br-7 br-bl-7" href="#">
														<div class="media mt-0">
															<img class="avatar-xxl rounded-circle mr-3" src="{{URL::asset('assets/images/users/female/5.jpg')}}" alt="Image description">
															<div class="media-body">
																<div class="d-md-flex align-items-center">
																	<h4 class="mb-3">
																		Joanne Taylor
																	</h4>
																	<small class="text-primary ml-md-auto"><i class="fe fe-calendar mr-1"></i> 25 Feb 2019</small>
																</div>
																<p class="mb-0 text-muted">Itaque earum rerum hic tenetur a sapiente reiciendis voluptatibus.</p>
															</div>
														</div>
													</a>
												</div>
											</div>
										</div>
										<div class="card">
											<div class="card-header custom-header pb-0">
												<div>
													<h3 class="card-title">Staff Details</h3>
													<h6 class="card-subtitle">Overview of all patients information</h6>
												</div>
												<div class="card-options">
													<a href="" class="mr-4 text-default" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
														<span class="fa fa-ellipsis-v"></span>
													</a>
													<ul class="dropdown-menu dropdown-menu-right" role="menu">
														<li><a href="#"><i class="si si-plus mr-2"></i>Add</a></li>
														<li><a href="#"><i class="si si-trash mr-2"></i>Remove</a></li>
														<li><a href="#"><i class="si si-eye mr-2"></i>View</a></li>
														<li><a href="#"><i class="si si-settings mr-2"></i>More</a></li>
													</ul>
												</div>
											</div>
											<div class="card-body">
												<div class="table-responsive">
													<table class="table table-bordered text-nowrap mb-0">
														<thead>
															<tr>
																<th>No</th>
																<th>Department</th>
																<th>Male</th>
																<th>Female</th>
																<th>Info</th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td>001</td>
																<td>Allergist</td>
																<td>34</td>
																<td>87</td>
																<td><a class="btn btn-sm btn-danger" href="#"><i class="si si-eye"></i> View Details</a></td>
															</tr>
															<tr>
																<td>002</td>
																<td>Cardiologist</td>
																<td>34</td>
																<td>87</td>
																<td><a class="btn btn-sm btn-primary" href="#"><i class="si si-eye"></i> View Details</a></td>
															</tr>
															<tr>
																<td>003</td>
																<td>Dermatologist</td>
																<td>34</td>
																<td>87</td>
																<td><a class="btn btn-sm btn-light" href="#"><i class="si si-eye"></i> View Details</a></td>
															</tr>
															<tr>
																<td>004</td>
																<td>Neurologist</td>
																<td>34</td>
																<td>87</td>
																<td><a class="btn btn-sm btn-success" href="#"><i class="si si-eye"></i> View Details</a></td>
															</tr>
															<tr>
																<td>005</td>
																<td>Pathologist</td>
																<td>34</td>
																<td>87</td>
																<td><a class="btn btn-sm btn-danger" href="#"><i class="si si-eye"></i> View Details</a></td>
															</tr>
															<tr>
																<td>006</td>
																<td>Psychiatrist</td>
																<td>34</td>
																<td>87</td>
																<td><a class="btn btn-sm btn-primary" href="#"><i class="si si-eye"></i> View Details</a></td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

@endsection('content')

@section('scripts')

		<!--Jquery Sparkline js-->
		<script src="{{URL::asset('assets/plugins/vendors/jquery.sparkline.min.js')}}"></script>

		<!-- Chart Circle js-->
		<script src="{{URL::asset('assets/plugins/vendors/circle-progress.min.js')}}"></script>

		<!--  Chart js -->
		<script src="{{URL::asset('assets/plugins/chart/chart.bundle.js')}}"></script>
		
        <!--Owl Carousel js -->
		<script src="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.js')}}"></script>
		<script src="{{URL::asset('assets/plugins/owl-carousel/owl-main.js')}}"></script>
		
        <!--Time Counter js-->
		<script src="{{URL::asset('assets/plugins/counters/jquery.missofis-countdown.js')}}"></script>
		<script src="{{URL::asset('assets/plugins/counters/counter.js')}}"></script>
		
		<!-- ApexChart -->
		<script src="{{URL::asset('assets/plugins/apexcharts/apexcharts.js')}}"></script>
		
        <!-- ECharts js -->
		<script src="{{URL::asset('assets/plugins/echarts/echarts.js')}}"></script>
		
        <!-- Custom-charts js-->
		<script src="{{URL::asset('assets/js/index4.js')}}"></script>

@endsection