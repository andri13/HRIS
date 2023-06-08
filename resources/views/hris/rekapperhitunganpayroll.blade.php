@extends('admin.adminlayouts.adminlayout')

@section('head')
    <!-- Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/responsivebootstrap4.min.css') }}" rel="stylesheet" />

	<!-- Notifications  css -->
	<link href="{{URL::asset('assets/plugins/notify-growl/css/jquery.growl.css')}}" rel="stylesheet" />
	<link href="{{URL::asset('assets/plugins/notify-growl/css/notifIt.css')}}" rel="stylesheet" />

    <!-- Date Picker css-->
    <link href="{{URL::asset('assets/plugins/spectrum-date-picker/spectrum.css')}}" rel="stylesheet" />

	<!---Sweetalert Css-->
	<link href="{{URL::asset('assets/plugins/sweet-alert/jquery.sweet-modal.min.css')}}" rel="stylesheet" />
	<link href="{{URL::asset('assets/plugins/sweet-alert/sweetalert.css')}}" rel="stylesheet" />

@stop
@section('mainarea')
    <!-- page-header -->
    <div class="page-header p-2 shadow">
        <ol class="breadcrumb breadcrumb-arrow mt-0">
            <li><a href="#">Perhitungan</a></li>
            <li class="active"><span>Payroll Karyawan</span></li>
        </ol>
        <div class="ml-auto">
            <div class="input-group">
                <a href="#" id="btn-refresh-page" class="btn btn-secondary p-0 mr-0 text-white btn-icon" data-toggle="tooltip"
                    title="" data-placement="bottom" data-original-title="Refresh Page">
                    <span>
                        <i class="fa fa-refresh"></i>
                    </span>
                </a>
            </div>
        </div>
    </div>
    <!-- End page-header -->
    <div class="card shadow card-collapsed">
        <div class="card-header bg-primary p-2">
            <div class="card-title">PROSES PAYROLL</div>
            <div class="card-options ">
                <a href="#" class="card-options-collapse mr-2" data-toggle="card-collapse"><i class="fe fe-chevron-up text-white"></i></a>
            </div>
        </div>
        <form id="form_proses_payroll" method="post">
                @csrf
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">PERIODE PAYROLL  : </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                    </div>
                                </div>
                                <input id="" name="periode_payrol" type="month" class="form-control PriodeProses" required></input>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light m-0 p-1">
                <div class="text-white">
                    <a id="BtnProsesPayroll" class="btn btn-app btn-primary mr-0 mt-0 mb-0 text-white BtnProsesPayroll"><span><i class="fa fa-download"></i></span> PROSES PAYROLL</a>
                </div>
            </div>
        </form>
    </div>

    <!-- BEGIN FORM-->
    {!! Form::open(['route' => 'hris.rekapperhitunganpayroll.ajax_exportexcel', 'id' => 'formExport', 'name' => 'formExport','method'=>'post']) !!}

    @csrf
    <input id="selDepVal" name="selDepVal" type="hidden">
    <div class="card shadow">
        <div class="card-header bg-primary p-2">
            <div class="card-title">FILTER DATA PAYROLL KARYAWAN</div>
            <div class="card-options ">
                <a href="#" class="card-options-collapse mr-2" data-toggle="card-collapse"><i class="fe fe-chevron-up text-white"></i></a>
            </div>
        </div>
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group m-0 p-0">
                        <label class="form-label">PERIODE PAYROLL : </label>
                        <select id="periode_payroll" name="periode_payroll" class="form-control">
                            @foreach ($periode_payroll as $r_periode_payroll)
                                <option  value="{{$r_periode_payroll->periode_payroll}}">
                                @php
                                    setlocale(LC_ALL, 'id-ID', 'id_ID');
                                    $datePeriode = explode("-", $r_periode_payroll->periode_payroll);
                                    echo strtoupper(date("F", mktime(0, 0, 0, $datePeriode[1], 10))) . ' ' . $datePeriode[0];
                                @endphp
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-light m-0 p-1">
            <div class="text-white">
                <div class="input-group-append">                
                    <button type="submit" id="btn-exportexcel" class="btn btn-app btn-primary mr-1 mt-0 mb-0" data-toggle="tooltip" title="Export Data ke File Excel"><i class="ion-ios7-download"></i> EXPORT</button>
                </div>
            </div>
        </div>
    </div>
    {{-- </form> --}}
    {!! Form::close() !!}
    <!-- END FORM-->

@endsection

@section('footerjs')

    <!--Jquery Sparkline js-->
    <script src="{{ URL::asset('assets/plugins/vendors/jquery.sparkline.min.js') }}"></script>

    <!-- INTERNAL Data tables -->
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/responsive.bootstrap4.min.js') }}"></script>

    <!-- Notifications js -->
    <script src="{{URL::asset('assets/plugins/notify-growl/js/rainbow.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/notify-growl/js/sample.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/notify-growl/js/jquery.growl.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/notify-growl/js/notifIt.js')}}"></script>

    <!-- Datepicker js -->
    <script src="{{URL::asset('assets/plugins/spectrum-date-picker/spectrum.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/spectrum-date-picker/jquery-ui.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/input-mask/jquery.maskedinput.js')}}"></script>

    <!-- Sweet alert js-->
    <script src="{{URL::asset('assets/plugins/sweet-alert/jquery.sweet-modal.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/sweet-alert/sweetalert.min.js')}}"></script>

    <script type="text/javascript">

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('body').on('click', '#btn-exportexcel', function (event) {
            notif({
                msg: "<b>Info:</b> Data sedang di proses, mohon menunggu",
                type: "info"
            });
        });

        $('body').on('click', '#btn-refresh-page', function (event) {
            location.reload();
        });


    </script>

    <!-- Andri -->
    <script>

        $('.fc-datepicker').datepicker({
      format: 'Y-MM',
      showButtonPanel: true
    })





    jQuery(document).ready(function($) {
            const BtnProsesPayroll = document.getElementsByClassName('BtnProsesPayroll')[0];
            const PriodeProses = document.getElementsByClassName("PriodeProses");

            BtnProsesPayroll.addEventListener('click', function(event) {
                let tmp = PriodeProses[0].value;
                
                if (tmp == ''||tmp==null) {
                    swal({
                        title: "Harap Pilih Periode",
                        text: "Data Periode Payroll tidak boleh kosong",
                        icon: "warning",
                        button : false,
                    });
                } else{
                    event.preventDefault();
                    const submited =document.getElementsByTagName('form')[0];
                    swal({
                        title: 'Apakah Anda Yakin ?',
                        text: 'Proses payroll',
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: 'YES',
                        cancelButtonText: 'NO'
                    },function(isConfirm){
                        if(isConfirm) {
                            $('#BtnProsesPayroll').addClass("btn-loading");
                            $("#BtnProsesPayroll").html('Please wait...');
                            $("#BtnProsesPayroll").attr("disabled", true);

                            $.ajax({
                                data: $('#form_proses_payroll').serialize(),
<<<<<<< HEAD
                                url: '{{ route("hris.proses.payroll.rekap") }}',           
=======
                                url: '{{ route("hris.coba") }}',           
>>>>>>> 32a6ad281a5eba42037ee61c900ff57694d6fd43
                                type: "post",
                                dataType: 'json',           
                                success: function (data) {
                                    console.log(data)

                                    if(data == 1) {
                                        notif({
                                            msg: "<b>Info:</b> Data Berhasil di Proses.",
                                            type: "info"
                                        });
                                    } else {
                                        notif({
                                            msg: "<b>Info:</b> Data Gagal di Proses.",
                                            type: "warning"
                                        });
                                    }

                                    $('#BtnProsesPayroll').removeClass("btn-loading");
                                    $("#BtnProsesPayroll").html('<span><i class="fa fa-download"></i></span> PROSES PAYROLL');
                                    $("#BtnProsesPayroll").attr("disabled", false);
                                   
                                },
                                error: function (xhr, status, error) {
                                    notif({
                                        msg: "<b>Error:</b> Oops data gagal di Proses.",
                                        type: "error"
                                    });

                                    $('#BtnProsesPayroll').removeClass("btn-loading");
                                    $("#BtnProsesPayroll").attr("disabled", false);
                                    $("#BtnProsesPayroll").html('<span><i class="fa fa-download"></i></span> PROSES PAYROLL');
                                }
                            }); 

                            // setTimeout(function myFunction() {
                            //     location.reload();
                            // }, 6000);  
                        }
                    });    
                }
            });
        });
    </script>

@endsection
