@extends('layouts.app')

@section('styles')

@endsection

@section('content')

					    <!-- page-header -->
						<div class="page-header">
							<ol class="breadcrumb"><!-- breadcrumb -->
								<li class="breadcrumb-item"><a href="#">Advanced UI</a></li>
								<li class="breadcrumb-item active" aria-current="page">Modal</li>
							</ol><!-- breadcrumb end -->
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
						<!-- page-header -->

                        <!-- row -->
						<div class="row">
							<div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
								<div class="card">
									<div class="card-header pb-0">
										<h3 class="card-title">Basic Modal</h3>
									</div>
									<div class="card-body">
										<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">View modal</button>
									</div>
								</div>
							</div><!-- col end -->
							<div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
								<div class="card">
									<div class="card-header pb-0">
										<h3 class="card-title">Scrolling Modal</h3>
									</div>
									<div class="card-body">
										<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#exampleModalLong">View modal</button>
									</div>
								</div>
							</div><!-- col end -->
							<div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
								<div class="card">
									<div class="card-header pb-0">
										<h3 class="card-title">Large Modal</h3>
									</div>
									<div class="card-body">
										<button class="btn btn-danger" data-toggle="modal" data-target="#largeModal">View modal</button>
									</div>
								</div>
							</div>
						</div><!-- col end -->
						<!-- row end -->

						<!-- row -->
						<div class="row">
							<div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
								<div class="card">
									<div class="card-header pb-0">
										<h3 class="card-title">Grid Modal</h3>
									</div>
									<div class="card-body">
										<button type="button" class="btn btn-pink" data-toggle="modal" data-target="#exampleModal2">View modal</button>
									</div>
								</div>
							</div><!-- col end -->
							<div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
								<div class="card">
									<div class="card-header pb-0">
										<h3 class="card-title">Small Modal</h3>
									</div>
									<div class="card-body">
										<button type="button" class="btn btn-success" data-toggle="modal" data-target="#smallModal">View modal</button>
									</div>
								</div>
							</div><!-- col end -->
							<div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
								<div class="card">
									<div class="card-header pb-0">
										<h3 class="card-title">Input Modal</h3>
									</div>
									<div class="card-body">
										<button type="button" class="btn btn-info" data-toggle="modal" data-target="#exampleModal3">View modal</button>
									</div>
								</div>
							</div>
						</div><!-- col end -->
						<!-- row end -->

@endsection('content')

@section('modals')

					<!-- Modal -->
					<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">×</span>
									</button>
								</div>
								<div class="modal-body">
									<p>Modal body text goes here.</p>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<button type="button" class="btn btn-primary">Save changes</button>
								</div>
							</div>
						</div>
					</div>
					<!-- Modal -->
					<div class="modal  fade" id="smallmodal" tabindex="-1" role="dialog" aria-labelledby="smallmodal" aria-hidden="true">
						<div class="modal-dialog modal-sm" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="smallmodal1">Modal title</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">×</span>
									</button>
								</div>
								<div class="modal-body">
									<p>Modal body text goes here.</p>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<button type="button" class="btn btn-primary">Save changes</button>
								</div>
							</div>
						</div>
					</div>

					<!-- Modal -->
					<div class="modal fade" id="normalmodal" tabindex="-1" role="dialog" aria-labelledby="normalmodal" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="normalmodal1">Modal title</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">×</span>
									</button>
								</div>
								<div class="modal-body">
									<p>Modal body text goes here.</p>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<button type="button" class="btn btn-primary">Save changes</button>
								</div>
							</div>
						</div>
					</div>

					<!-- Modal -->
					<div class="modal fade" id="largemodal" tabindex="-1" role="dialog" aria-labelledby="largemodal" aria-hidden="true">
						<div class="modal-dialog modal-lg " role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="largemodal1">Modal title</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">×</span>
									</button>
								</div>
								<div class="modal-body">
									<p>Modal body text goes here.</p>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<button type="button" class="btn btn-primary">Save changes</button>
								</div>
							</div>
						</div>
					</div>
					<!--Scrolling Modal-->
					<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
							  <div class="modal-header">
								<h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								  <span aria-hidden="true">&times;</span>
								</button>
							  </div>
							  <div class="modal-body">
								<p> sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
								<p>Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae.</p>
								<p>These cases are perfectly simple and easy to distinguish. In a free hour, when our power of choice is untrammelled and when nothing prevents our being able to do what we like best, every pleasure is to be welcomed and every pain avoided. </p>
								<p> Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p>
								<p> No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure.</p>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."</p>
							  </div>
							  <div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-primary">Save changes</button>
							  </div>
							</div>
						</div>
					</div>

					<!-- Large Modal -->
					<div id="largeModal" class="modal fade">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content ">
								<div class="modal-header pd-x-20">
									<h6 class="modal-title">Message Preview</h6>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body pd-20">
									<h5 class=" lh-3 mg-b-20"><a href="" class="font-weight-bold">Why We Use Electoral College, Not Popular Vote</a></h5>
									<p class="">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. </p>
								</div><!-- modal-body -->
								<div class="modal-footer">
									<button type="button" class="btn btn-primary">Save changes</button>
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								</div>
							</div>
						</div><!-- modal-dialog -->
					</div><!-- modal -->

					<!-- small Modal -->
					<div id="smallModal" class="modal fade">
						<div class="modal-dialog modal-sm" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Message Preview</h6>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<p>This is a modal with small size</p>
								</div><!-- modal-body -->
								<div class="modal-footer">
									<button type="button" class="btn btn-primary">Save changes</button>
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								</div>
							</div>
						</div><!-- modal-dialog -->
					</div><!-- modal -->

					<!-- Grid Modal -->
					<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModal2" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="example-Modal2">Modal title</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">×</span>
									</button>
								</div>
								<div class="modal-body">
									<div class="row">
										<div class="col-md-6">
											<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
										</div>
										<div class="col-md-6">
											<p>But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the who loves toil and pain can procureor sit aspernatur  system.</p>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<p>expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure desires to obtain pain.</p>
										</div>
										<div class="col-md-6">
											<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat aut odit voluptatem.</p>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<button type="button" class="btn btn-primary">Save changes</button>
								</div>
							</div>
						</div>
					</div>

					<!-- Message Modal -->
					<div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog"  aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="example-Modal3">New message</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<form>
										<div class="form-group">
											<label for="recipient-name" class="form-control-label">Recipient:</label>
											<input type="text" class="form-control" id="recipient-name">
										</div>
										<div class="form-group">
											<label for="message-text" class="form-control-label">Message:</label>
											<textarea class="form-control" id="message-text"></textarea>
										</div>
									</form>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<button type="button" class="btn btn-primary">Send message</button>
								</div>
							</div>
						</div>
					</div>

@endsection('modals')

@section('scripts')

		<!--Jquery Sparkline js-->
		<script src="{{URL::asset('assets/plugins/vendors/jquery.sparkline.min.js')}}"></script>

		<!-- Chart Circle js-->
		<script src="{{URL::asset('assets/plugins/vendors/circle-progress.min.js')}}"></script>
		
        <!--Time Counter js-->
		<script src="{{URL::asset('assets/plugins/counters/jquery.missofis-countdown.js')}}"></script>
		<script src="{{URL::asset('assets/plugins/counters/counter.js')}}"></script>

@endsection