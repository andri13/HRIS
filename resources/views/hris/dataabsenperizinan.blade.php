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

    <!-- Time picker css-->
    <link href="{{URL::asset('assets/plugins/time-picker/jquery.timepicker.css')}}" rel="stylesheet" />

	<!---Sweetalert Css-->
	<link href="{{URL::asset('assets/plugins/sweet-alert/jquery.sweet-modal.min.css')}}" rel="stylesheet" />
	<link href="{{URL::asset('assets/plugins/sweet-alert/sweetalert.css')}}" rel="stylesheet" />

	<!-- Tabs css-->
	<link href="{{URL::asset('assets/plugins/tabs/tabs-style.css')}}" rel="stylesheet" />

@stop
@section('mainarea')
    <!-- page-header -->
    <div class="page-header p-2 shadow">
        <ol class="breadcrumb breadcrumb-arrow mt-0">
            <li><a href="#">ABSENSI KARYAWAN</a></li>
            <li class="active"><span>PERIZINAN</span></li>
        </ol>
        <div class="ml-auto">
            <div class="input-group">

            <div class="text-white">
                    <!-- <a href="{{route('hris.employeeatr.format')}}" id="btn-examimport" class="btn btn-icon btn-orange text-white p-0 mr-1" data-toggle="tooltip" title="" data-original-title="Format File Excel"><i class="fa fa-file-excel-o"></i>Format File </a> -->
                    <button type="button" id="btn-import" class="btn btn-icon btn-warning text-white p-0 mr-1"  data-target="#import_grade" data-toggle="modal" title="" data-original-title="Import Data Dari File Excel"><i class="fa fa-file-excel-o"></i> Import Data</button>
                </div>
                    <!-- modal -->
             
                    <form name="custForm" action="{{route ('hris.exportperijinan.store')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal fade" id="import_grade" role="dialog" data-backdrop="static" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary p-2">
                                                <h4 class="modal-title pl-2 font-weight-bold" >Import Grading & BPJS</h4>
                                                <button type="button" id="btn-close" class="close text-white ml-1" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Tutup Dialog">
                                                    <i class="fa fa-remove"></i>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-label">file import : </label>
                                                            <input class="form-control" name="file_import" type="file" accept=".xlsx, .xls, .csv" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-primary p-1">
                                                <div class="btn-list">
                                                    <button type="submit" id="export_grade_bpjs" class="btn btn-secondary btn-app">Simpan</button>
                                                    <!-- <button type="button" id="" class="btn btn-warning btn-app" data-dismiss="modal">Tutup</button> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- end modal -->

                <a class="btn btn-primary mr-1 mt-0 mb-1 text-white btn-icon" id="daterange-btn1" data-toggle="tooltip"
                title="" data-placement="bottom" data-original-title="Klik di sini untuk pilih tanggal perizinan">
                </a>
                <!-- BEGIN FORM-->
                {!! Form::open(['route' => 'hris.dataabsenperijinan.ajax_exportexcel', 'id' => 'formExport1', 'name' => 'formExport1','method'=>'post']) !!}
                    <input type="hidden" id="daterange1" name="daterange1">
                    <button type="submit" id="btn-exportexcel" class="btn btn-primary mr-1 mt-0 mb-0 text-white btn-icon" data-toggle="tooltip" title="Export Data Perizinan ke Excel"><i class="fa fa-file-excel-o"></i> EXPORT IZIN</button>
                {{-- </form> --}}
                {!! Form::close() !!}
                <!-- END FORM-->
                
                {!! Form::open(['route' => 'hris.dataabsenperijinan.ajax_exportexcel2', 'id' => 'formExport2', 'name' => 'formExport2','method'=>'post']) !!}
                    <input type="hidden" id="daterange2" name="daterange2">
                    <button type="submit" id="btn-exportexcel" class="btn btn-primary mr-1 mt-0 mb-0 text-white btn-icon" data-toggle="tooltip" title="Export Data Izin IKS ke Excel"><i class="fa fa-file-excel-o"></i> EXPORT IKS</button>
                {{-- </form> --}}
                {!! Form::close() !!}
                <!-- END FORM-->
                
            </div>
        </div>
    </div>
    <!-- End page-header -->

    <!-- row -->
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
            <!-- Begin Form Edit Absen Karyawan -->

            <div id="data-perizinan-iks" class="card shadow">
                <div class="card-header text-white bg-gradient-primary p-2">
                    <div class="card-title">CARI DATA PERIZINAN & IKS</div>
                    <div class="card-options ">
                        <a href="#" class="card-options-collapse mr-2" data-toggle="card-collapse"><i class="fe fe-chevron-up text-white"></i></a>
                    </div>
                </div>
                <div class="card-body m-0">
                    <div class="table-responsive">
                        <table id="datatable-ajax-crud"
                            class="table table-sm table-striped table-hover table-bordered w-100">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-primary br-br-7 br-bl-7">
                    <div class="text-white"></div>
                </div>
            </div>

            <div id="data-karyawan" class="card shadow">
                <div class="card-header text-white bg-gradient-primary p-2">
                    <div class="card-title">CARI DATA KARYAWAN</div>
                    <div class="card-options ">
                        <a href="#" class="card-options-collapse mr-2" data-toggle="card-collapse"><i class="fe fe-chevron-up text-white"></i></a>
                    </div>
                </div>
                <div class="card-body m-0">
                    <div class="table-responsive">
                        <table id="datatable-ajax-karyawan"
                            class="table table-sm table-striped table-hover table-bordered w-100">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-primary br-br-7 br-bl-7">
                    <div class="text-white"></div>
                </div>
            </div>
        </div>        
        <div class="col-sm-12 col-md-12 col-lg-7 col-xl-7">
            <!-- Begin Form Edit Absen Karyawan -->
            <div id="data-gagal-absen" class="card shadow">
                <div class="card-header text-white bg-gradient-primary p-2">
                    <div class="card-title">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle pl-1 pt-0 pb-0 pr-1 mr-1 text-sm" data-toggle="dropdown">
                                <i class="fa fa-navicon"></i>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="javascript:void(0)" id="btn-print"><i class="fa fa-print"></i> Print</a></li>
                                <li><a href="javascript:void(0)" id="btn-add"><i class="fa fa-plus"></i> Tambah</a></li>
                                <li><a href="javascript:void(0)" id="btn-remove"><i class="fa fa-remove"></i> Hapus</a></li>
                            </ul>
                        </div>                        
                        [ADD/EDIT] PERIZINAN DAN IKS
                    </div>
                    <div class="card-options ">
                        <a href="#" class="card-options-collapse mr-2" data-toggle="card-collapse"><i class="fe fe-chevron-up text-white"></i></a>
                    </div>
                </div>
                <div class="progress progress-xs mb-0">
                    <div id="progress-show-1" class="progress-bar progress-bar-indeterminate bg-green"></div>
                    <div id="progress-hide-1" class="progress-bar"></div>
                </div>
                <div class="card-body">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['route' => 'hris.employeeatr.create', 'id' => 'form1', 'name' => 'form1', 'method'=>'post']) !!}
                    <input id="uuid" type="hidden">
                    <input id="uuid_master" type="hidden">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">No. Form Perizinan : </label>
                                <input type="text" readonly class="form-control" id="nomor_form_perizinan" name="nomor_form_perizinan" placeholder="Nomor Form Perizinan">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Tanggal Perizinan</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                        </div>
                                    </div>
                                    <input readonly id="tanggal_perijinan" name="tanggal_perijinan" type="text" class="form-control fc-datepicker" placeholder="Tanggal Perizinan" maxlength="50" size="50">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Nomor Absen : </label>
                                <input type="text" readonly class="form-control" id="enroll_id" name="enroll_id" placeholder="Nomor Absen">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">NIK : </label>
                                <input type="text" readonly class="form-control" id="nik" name="nik" placeholder="NIK">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Nama Karyawan : </label>
                                <input type="text" readonly class="form-control" id="employee_name" name="employee_name" placeholder="Nama Karyawan">
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-primary">
                        <div class="tab_wrapper first_tab">
                            <ul class="tab_list">
                                <li class="text-sm" id="tab-izin">Perizinan</li>
                                <li class="text-sm" id="tab-iks">Izin Keluar Sementara (IKS)</li>
                            </ul>
                            <div class="content_wrapper">
                                <div class="tab_content active">                
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Tanggal Mulai Izin</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                        </div>
                                                    </div>
                                                    <input readonly id="tanggal_mulai_ijin" name="tanggal_mulai_ijin" type="text" class="form-control" placeholder="Tanggal Mulai Izin" maxlength="50" size="50">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Tanggal Akhir Izin</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                        </div>
                                                    </div>
                                                    <input readonly id="tanggal_akhir_ijin" name="tanggal_akhir_ijin" type="text" class="form-control fc-datepicker" placeholder="Tanggal Akhir Izin" maxlength="50" size="50">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">Jenis Perizinan</label>
                                                <select id="kode_absen_ijin" class="form-control" data-placeholder="-- Pilih Jenis Perijinan --">
                                                    <option value="">-- Pilih Jenis Perizinan --</option>
                                                    @foreach ($refabsenijin as $r_refabsenijin)
                                                        @php
                                                            if (($r_refabsenijin->kode_absen_ijin <> 'IKS') && ($r_refabsenijin->kode_absen_ijin <> 'M')) {
                                                        @endphp
                                                            <option value="{{$r_refabsenijin->kode_absen_ijin}}">{{$r_refabsenijin->kode_nama_absen_ijin}}</option>
                                                        @php
                                                            }
                                                        @endphp
                                                    @endforeach
                                                </select>                                                
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-0">
                                                <label class="form-label">Keterangan</label>
                                                <textarea class="form-control mb-2" id="absen_alasan_izin" name="absen_alasan_izin" rows="2" placeholder="Keterangan"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="form-label">Tanggal Dibuat</label>
                                                <div id="created_at_izin" class="pl-2 p-1 border-white"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="form-label">Tanggal Diubah</label>
                                                <div id="updated_at_izin" class="pl-2 p-1 border-white"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 pt-3">
                                            <div class="btn-list">
                                                <button type="button" id="btn-save-izin" class="btn btn-secondary btn-app"><i class="fa fa-save"></i> Add</button>
                                                <button type="button" id="btn-cancel-izin" class="btn btn-warning btn-app mt-1">
                                                    <span>
                                                        <i class="fa fa-close"></i>
                                                    </span>
                                                    Tutup</button>                            
                                            </div>                        
                                        </div>    
                                    </div>
                                </div>
                                <div class="tab_content">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Jam Mulai Izin</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa fa-clock-o tx-16 lh-0 op-6"></i>
                                                        </div>
                                                    </div><!-- input-group-prepend -->
                                                    <input id="time_mulai_ijin" name="time_mulai_ijin" class="form-control" placeholder="--:--" onChange="hitungtotaljam();" type="text" maxlength="5">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Jam Akhir Izin</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa fa-clock-o tx-16 lh-0 op-6"></i>
                                                        </div>
                                                    </div><!-- input-group-prepend -->
                                                    <input id="time_akhir_ijin" name="time_akhir_ijin" class="form-control" placeholder="--:--" onChange="hitungtotaljam();"  type="text" maxlength="5">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Total MENIT Izin</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa fa-clock-o tx-16 lh-0 op-6"></i>
                                                        </div>
                                                    </div><!-- input-group-prepend -->
                                                    <input id="total_time_ijin" name="total_time_ijin" class="form-control" placeholder="0" type="text" maxlength="3" size="3">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">Jenis Perizinan</label>
                                                <select id="kode_absen_ijin_iks" class="form-control" data-placeholder="-- Pilih Jenis Perijinan --">
                                                    @foreach ($refabsenijin as $r_refabsenijin)
                                                        @php
                                                            if ($r_refabsenijin->kode_absen_ijin == 'IKS') {
                                                        @endphp
                                                            <option value="{{$r_refabsenijin->kode_absen_ijin}}">{{$r_refabsenijin->kode_nama_absen_ijin}}</option>
                                                        @php
                                                            }
                                                        @endphp
                                                    @endforeach
                                                </select>                                                
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-0">
                                                <label class="form-label">Keterangan</label>
                                                <textarea class="form-control mb-2" id="absen_alasan_iks" name="absen_alasan_iks" rows="2" placeholder="Keterangan"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="form-label">Tanggal Dibuat</label>
                                                <div id="created_at_iks" class="pl-2 p-1 border-white"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="form-label">Tanggal Diubah</label>
                                                <div id="updated_at_iks" class="pl-2 p-1 border-white"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 pt-3">
                                            <div class="btn-list">
                                                <button type="button" id="btn-save-iks" class="btn btn-secondary btn-app"><i class="fa fa-save"></i> Update</button>
                                                <button type="button" id="btn-cancel-iks" class="btn btn-warning btn-app mt-1">
                                                    <span>
                                                        <i class="fa fa-close"></i>
                                                    </span>
                                                    Tutup</button>                            
                                            </div>                        
                                        </div>
                                    </div>                                                                                
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- </form> --}}
                    {!! Form::close() !!}
                    <!-- END FORM-->
                </div>
                <div class="card-footer bg-primary pl-2 pt-1 pb-2">
                </div>    
            </div>
        </div>
    </div>
    <!-- row end -->
</div>

@endsection

@section('footerjs')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.0/jquery.mask.min.js"></script>

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

    <!-- Sweet alert js-->
    <script src="{{URL::asset('assets/plugins/sweet-alert/jquery.sweet-modal.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/sweet-alert/sweetalert.min.js')}}"></script>

    <!-- Datepicker js -->
    <script src="{{URL::asset('assets/plugins/spectrum-date-picker/spectrum.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/spectrum-date-picker/jquery-ui.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/input-mask/jquery.maskedinput.js')}}"></script>

    <!-- Timepicker js -->
    <script src="{{URL::asset('assets/plugins/time-picker/jquery.timepicker.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/time-picker/toggles.min.js')}}"></script>

    <!---Tabs js-->
    <script src="{{URL::asset('assets/plugins/tabs/jquery.multipurpose_tabcontent.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/tabs/tabs.js')}}"></script>

    <!-- Popover js -->
    <script src="{{URL::asset('assets/js/popover.js')}}"></script>

    <script type="text/javascript">

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#progress-show-1').hide();
        $('#data-karyawan').hide();
        $("#form1 :input").prop("disabled", true);
        $("#btn-save-izin").prop("disabled", true);
        $("#btn-save-iks").prop("disabled", true);
        $("#btn-cancel-izin").prop("disabled", true);
        $("#btn-cancel-iks").prop("disabled", true);
        $('#btn-save-izin').html('<i class="fa fa-save"></i> Save');
        $('#btn-save-iks').html('<i class="fa fa-save"></i> Save');

        function replaceBadInputs(val) {
            // Replace impossible inputs as the appear
            val = val.replace(/[^\dh:]/, "");
            val = val.replace(/^[^0-2]/, "");
            val = val.replace(/^([2-9])[4-9]/, "$1");
            val = val.replace(/^\d[:h]/, "");
            val = val.replace(/^([01][0-9])[^:h]/, "$1");
            val = val.replace(/^(2[0-3])[^:h]/, "$1");      
            val = val.replace(/^(\d{2}[:h])[^0-5]/, "$1");
            val = val.replace(/^(\d{2}h)./, "$1");      
            val = val.replace(/^(\d{2}:[0-5])[^0-9]/, "$1");
            val = val.replace(/^(\d{2}:\d[0-9])./, "$1");
            return val;
        }
        
        // Apply input rules as the user types or pastes input
        $('#time_mulai_ijin').keyup(function(){
        var val = this.value;
        var lastLength;
        do {
            // Loop over the input to apply rules repeately to pasted inputs
            lastLength = val.length;
            val = replaceBadInputs(val);
        } while(val.length > 0 && lastLength !== val.length);
        this.value = val;
        });
        
        // Check the final result when the input has lost focus
        $('#time_mulai_ijin').blur(function(){
        var val = this.value;
        val = (/^(([01][0-9]|2[0-3])h)|(([01][0-9]|2[0-3]):[0-5][0-9])$/.test(val) ? val : "");
        this.value = val;
        });

        // Apply input rules as the user types or pastes input
        $('#time_akhir_ijin').keyup(function(){
        var val = this.value;
        var lastLength;
        do {
            // Loop over the input to apply rules repeately to pasted inputs
            lastLength = val.length;
            val = replaceBadInputs(val);
        } while(val.length > 0 && lastLength !== val.length);
        this.value = val;
        });
        
        // Check the final result when the input has lost focus
        $('#time_akhir_ijin').blur(function(){
        var val = this.value;
        val = (/^(([01][0-9]|2[0-3])h)|(([01][0-9]|2[0-3]):[0-5][0-9])$/.test(val) ? val : "");
        this.value = val;
        });
    
        $('.fc-datepicker').datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'yy-mm-dd'
        });

        $('body').on('click', '#btn-add', function (event) {

            $("#form1").trigger("reset");
            $("#uuid_master").val(null);
            $("#datatable-ajax-crud").DataTable().ajax.reload();                    

            $('#created_at_izin').text(null);
            $('#updated_at_izin').text(null);    
            $('#created_at_iks').text(null);
            $('#updated_at_iks').text(null);    
            $("#form1 :input").prop("disabled", false);
            $('#data-perizinan-iks').hide("slow");
            $('#data-karyawan').show("slow");
            $('#btn-save-izin').html('<i class="fa fa-save"></i> Add');
            $('#btn-save-iks').html('<i class="fa fa-save"></i> Add');

            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            
            today = yyyy + '-' + mm + '-' + dd;
            
            $('#tanggal_perijinan').val(today); 
            $('#tanggal_mulai_ijin').val(today);
            $('#tanggal_akhir_ijin').val(today);
                
            var table1 = $('#datatable-ajax-karyawan').DataTable({
                processing: true,
                serverSide: true,
                lengthChange: false,
                pageLength: 10,
                pagingType: "simple",
                dom: '<"top"ipf>rt<"bottom"l><"clear">',
                destroy: true,
                "ajax": {
                    "url": "{{ route('hris.employeeatr.ajax_getempatr') }}",
                    "dataType": "json",
                    "type": "POST",
                    "headers": {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    "dataSrc": "data",
                },
                columns: [
                    {
                        title: 'Nomor Absen',
                        data: 'enroll_id',
                        name: 'enroll_id'
                    },
                    {
                        title: 'NIK',
                        data: 'nik',
                        name: 'nik'
                    },
                    {
                        title: 'Nama Karyawan',
                        data: 'employee_name',
                        name: 'employee_name'
                    },
                ],
                columnDefs: [
                    {
                        'visible': false,
                        'targets': []
                    }
                ],
                order: [
                    [2, 'asc']
                ]
            });

            table1.draw();

            $('#datatable-ajax-karyawan tbody').on('click', 'tr', function () {
                var tr = $(this).closest('tr');
                var row = table1.row(tr);

                var data = row.data();
        
                $("#datatable-ajax-karyawan tbody tr").removeClass('bg-cyan');
                $(this).addClass('bg-cyan');

                $('#enroll_id').val(data['enroll_id']);  
                $('#nik').val(data['nik']);
                $('#employee_name').val(data['employee_name']);
            });            
        });
        
        $('body').on('click', '#btn-cancel-izin', function (event) {
            location.reload();
        });

        $('body').on('click', '#btn-cancel-iks', function (event) {
            location.reload();
        });

        $('body').on('click', '#btn-save-izin', function (event) {
            var uuid = $('#uuid').val();
            var tanggal_perizinan = $('#tanggal_perijinan').val();
            var nomor_form_perizinan = $('#nomor_form_perizinan').val();
            var enroll_id = $('#enroll_id').val();
            var nik = $('#nik').val();
            var employee_name = $('#employee_name').val();
            var kode_absen_ijin = $('#kode_absen_ijin').val();
            var absen_alasan = $('#absen_alasan_izin').val();
            var tanggal_mulai_ijin = $('#tanggal_mulai_ijin').val();
            var tanggal_akhir_ijin = $('#tanggal_akhir_ijin').val();

            if (!enroll_id) {
                notif({
                    msg: "<b>Warning:</b> Anda belum memilih data karyawan.",
                    type: "warning"
                });
                return false;
            }

            if (!tanggal_perizinan) {
                notif({
                    msg: "<b>Warning:</b> Anda belum memilih Tanggal Perizinan.",
                    type: "warning"
                });
                return false;
            }

            if (!kode_absen_ijin) {
                notif({
                    msg: "<b>Warning:</b> Anda belum memilih Nama Izin.",
                    type: "warning"
                });
                return false;
            }

            if (!tanggal_mulai_ijin) {
                notif({
                    msg: "<b>Warning:</b> Anda belum memilih Tanggal Mulai Izin.",
                    type: "warning"
                });
                return false;
            }

            if (!tanggal_akhir_ijin) {
                notif({
                    msg: "<b>Warning:</b> Anda belum memilih Tanggal Akhir Izin.",
                    type: "warning"
                });
                return false;
            }

            var tanggal = tanggal_perizinan;
            
            // LAGI COBA TEST CLOSING PAYROLL
            $.ajax({
                type:"POST",
                url: "{{route('hris.dataclosingpayroll.ajax_getclosing')}}",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    tanggal:tanggal,
                },
                dataType: 'json',
                success: function(resA){

                    if(resA["ada"]) {
                        notif({
                            type: resA["status"],
                            msg: resA["message"],
                            position: "center",
                            width: 800,
                            height: 120,
                            opacity: 0.6,
                            autohide: false
                        });
                    } else {

                        var TglMulaiIzin = new Date(tanggal_mulai_ijin);
                        var TglAkhirIzin = new Date(tanggal_akhir_ijin);
                        var TglPerizinan = new Date(tanggal_perizinan);

                        if (TglMulaiIzin.getDate() != TglPerizinan.getDate()) {
                            notif({
                                msg: "<b>Warning:</b> Tanggal Mulai Izin tidak sesuai.",
                                type: "warning"
                            });

                            $('#tanggal_mulai_ijin').val(tanggal_perizinan);
                            $('#tanggal_akhir_ijin').val(tanggal_perizinan);

                            return false;
                        }

                        if (TglAkhirIzin.getDate() < TglMulaiIzin.getDate()) {
                            notif({
                                msg: "<b>Warning:</b> Tanggal Akhir Izin tidak sesuai.",
                                type: "warning"
                            });

                            $('#tanggal_mulai_ijin').val(tanggal_perizinan);
                            $('#tanggal_akhir_ijin').val(tanggal_perizinan);
                            
                            return false;
                        }

                        $('#btn-save-izin').addClass("btn-loading");
                        $("#btn-save-izin").html('Please wait...');
                        $("#btn-save-izin").attr("disabled", true);
                        $('#progress-show-1').show();
                        $('#progress-hide-1').hide();

                        $.ajax({
                            type:"POST",
                            url: "{{route('hris.dataabsenperijinan.cekperizinan')}}",
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            data: {
                                enroll_id:enroll_id,
                                tanggal_mulai_ijin:tanggal_mulai_ijin,
                                tanggal_akhir_ijin:tanggal_akhir_ijin,
                            },
                            dataType: 'json',
                            success: function(res){

                                if (res > 0) {
                                    notif({
                                        msg: "<b>Warning:</b> Data Perizinan yang anda pilih sudah ada.",
                                        type: "warning"
                                    });    
                                } else {
                                    $.ajax({
                                        type:"POST",
                                        url: "{{route('hris.dataabsenperijinan.create_perizinan_menu')}}",
                                        dataType: 'json',
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                        data: {
                                            uuid:uuid,
                                            tanggal_perizinan:tanggal_perizinan,
                                            nomor_form_perizinan:nomor_form_perizinan,
                                            enroll_id:enroll_id,
                                            nik:nik,
                                            employee_name:employee_name,
                                            kode_absen_ijin:kode_absen_ijin,
                                            absen_alasan:absen_alasan,
                                            tanggal_mulai_ijin:tanggal_mulai_ijin,
                                            tanggal_akhir_ijin:tanggal_akhir_ijin,
                                        },
                                        dataType: 'json',
                                        success: function(res){
                                            notif({
                                                msg: "<b>Info:</b> Data berhasil di simpan.",
                                                type: "info"
                                            });    
                                        },
                                        error: function(res){
                                            notif({
                                                msg: "<b>Error:</b> Oops data gagal di simpan.",
                                                type: "error"
                                            });    
                                        }
                                    });                        
                                }
                            },
                            error: function(res){
                                notif({
                                    msg: "<b>Error:</b> Oops Cek Data Perizinan GAGAL.",
                                    type: "error"
                                });    
                            }
                        });

                        $('#progress-show-1').hide();
                        $('#progress-hide-1').show();
                        $('#btn-save-izin').removeClass("btn-loading");
                        $("#btn-save-izin").html('<span><i class="fa fa-save"></i></span> Save');
                        $("#form1 :input").prop("disabled", true);
                        $("#btn-save-izin").prop("disabled", true);
                        $("#btn-save-iks").prop("disabled", true);
                        $("#btn-cancel-izin").prop("disabled", true);    
                        $("#btn-cancel-iks").prop("disabled", true);    
                        setTimeout(function myFunction() {
                            location.reload();
                        }, 3000);           
                    }

                },
                error: function(resA){
                                
                }
            });           
                            
        });

        $('body').on('click', '#btn-save-iks', function (event) {
            var uuid = $('#uuid').val();
            var tanggal_perizinan = $('#tanggal_perijinan').val();
            var nomor_form_perizinan = $('#nomor_form_perizinan').val();
            var enroll_id = $('#enroll_id').val();
            var nik = $('#nik').val();
            var employee_name = $('#employee_name').val();
            var kode_absen_ijin = $('#kode_absen_ijin_iks').val();
            var absen_alasan = $('#absen_alasan_iks').val();
            var time_mulai_ijin = $('#time_mulai_ijin').val();
            var time_akhir_ijin = $('#time_akhir_ijin').val();
            var total_time_ijin = $('#total_time_ijin').val();

            if (!enroll_id) {
                notif({
                    msg: "<b>Warning:</b> Anda belum memilih data karyawan.",
                    type: "warning"
                });
                return false;
            }

            if (!tanggal_perizinan) {
                notif({
                    msg: "<b>Warning:</b> Anda belum menginput Tanggal Perizinan.",
                    type: "warning"
                });
                return false;
            }

            if (!kode_absen_ijin) {
                notif({
                    msg: "<b>Warning:</b> Anda belum menginput Nama Izin.",
                    type: "warning"
                });
                return false;
            }

            if (!time_mulai_ijin) {
                notif({
                    msg: "<b>Warning:</b> Anda belum menginput Jam Mulai Izin.",
                    type: "warning"
                });
                return false;
            }

            if (!time_akhir_ijin) {
                notif({
                    msg: "<b>Warning:</b> Anda belum menginput Jam Akhir Izin.",
                    type: "warning"
                });
                return false;
            }

            if (!total_time_ijin) {
                notif({
                    msg: "<b>Warning:</b> Anda belum menginput Total Jam Izin.",
                    type: "warning"
                });
                return false;
            }

            var tanggal = tanggal_perizinan;
            
            // LAGI COBA TEST CLOSING PAYROLL
            $.ajax({
                type:"POST",
                url: "{{route('hris.dataclosingpayroll.ajax_getclosing')}}",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    tanggal:tanggal,
                },
                dataType: 'json',
                success: function(res){

                    if(res["ada"]) {
                        notif({
                            type: res["status"],
                            msg: res["message"],
                            position: "center",
                            width: 800,
                            height: 120,
                            opacity: 0.6,
                            autohide: false
                        });
                    } else {

                        $('#btn-save-izin').addClass("btn-loading");
                        $("#btn-save-izin").html('Please wait...');
                        $("#btn-save-izin").attr("disabled", true);
                        $('#progress-show-1').show();
                        $('#progress-hide-1').hide();

                        $.ajax({
                            type:"POST",
                            url: "{{route('hris.dataabsenperijinan.cekiks')}}",
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            data: {
                                tanggal_perizinan:tanggal_perizinan,
                                enroll_id:enroll_id,
                            },
                            dataType: 'json',
                            success: function(res){

                                if (res > 0) {
                                    notif({
                                        msg: "<b>Warning:</b> Data IKS yang anda pilih sudah ada.",
                                        type: "warning"
                                    });    
                                } else {
                                    $.ajax({
                                        type:"POST",
                                        url: "{{route('hris.dataabsenperijinan.create_iks_menu')}}",
                                        dataType: 'json',
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                        data: {
                                            uuid:uuid,
                                            tanggal_perizinan:tanggal_perizinan,
                                            nomor_form_perizinan:nomor_form_perizinan,
                                            enroll_id:enroll_id,
                                            nik:nik,
                                            employee_name:employee_name,
                                            kode_absen_ijin:kode_absen_ijin,
                                            absen_alasan:absen_alasan,
                                            time_mulai_ijin:time_mulai_ijin,
                                            time_akhir_ijin:time_akhir_ijin,
                                            total_time_ijin:total_time_ijin,
                                        },
                                        dataType: 'json',
                                        success: function(res){
                                            notif({
                                                msg: "<b>Info:</b> Data berhasil di simpan.",
                                                type: "info"
                                            });    
                                        },
                                        error: function(res){
                                            notif({
                                                msg: "<b>Error:</b> Oops data gagal di simpan.",
                                                type: "error"
                                            });    
                                        }
                                    });            
                                }
                            },
                            error: function(res){
                                notif({
                                    msg: "<b>Error:</b> Oops Cek Data Perizinan GAGAL.",
                                    type: "error"
                                });    
                            }
                        });

                        $('#progress-show-1').hide();
                        $('#progress-hide-1').show();
                        $('#btn-save-izin').removeClass("btn-loading");
                        $("#btn-save-izin").html('<span><i class="fa fa-save"></i></span> Save');
                        $("#form1 :input").prop("disabled", true);
                        $("#btn-save-izin").prop("disabled", true);
                        $("#btn-save-iks").prop("disabled", true);
                        $("#btn-cancel-izin").prop("disabled", true);    
                        $("#btn-cancel-iks").prop("disabled", true);    

                        setTimeout(function myFunction() {
                            location.reload();
                        }, 3000);           

                    }

                },
                error: function(res){
                                
                }
            });           
                        
        });

        $('body').on('change', '#tanggal_perijinan', function (event) {
            var date_permohonan = $(this).val();
            $('#tanggal_mulai_ijin').val(date_permohonan);
            $('#tanggal_akhir_ijin').val(date_permohonan);
        });

        $('body').on('click', '#btn-remove', function (event) {
            var tanggal_perizinan = $('#tanggal_perijinan').val();
            var tanggal = tanggal_perizinan;
            
            // LAGI COBA TEST CLOSING PAYROLL
            $.ajax({
                type:"POST",
                url: "{{route('hris.dataclosingpayroll.ajax_getclosing')}}",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    tanggal:tanggal,
                },
                dataType: 'json',
                success: function(res){

                    if(res["ada"]) {
                        notif({
                            type: res["status"],
                            msg: res["message"],
                            position: "center",
                            width: 800,
                            height: 120,
                            opacity: 0.6,
                            autohide: false
                        });
                    } else {

                        var nomor_form_perizinan = $('#nomor_form_perizinan').val();
                        var enroll_id = $('#enroll_id').val();

                        if (!nomor_form_perizinan) {
                            notif({
                                msg: "<b>Warning:</b> Anda belum memilih data perizinan.",
                                type: "warning"
                            });
                            return false;
                        }

                        message = "Anda Yakin Ingin Menghapus Nomor Form : " + nomor_form_perizinan + " !!!";
                        type = "warning";
                        swal({
                            title: message,
                            type: type,
                            showCancelButton: true,
                            confirmButtonText: 'Saya Yakin',
                            cancelButtonText: 'Tutup'
                        },function(isConfirm){
                            if(isConfirm) {

                                $("#form1 :input").prop("disabled", true);
                                $("#btn-save-izin").prop("disabled", true);
                                $("#btn-save-iks").prop("disabled", true);
                                $("#btn-cancel-izin").prop("disabled", true);    
                                $("#btn-cancel-iks").prop("disabled", true);    
                                $('#progress-show-1').show();
                                $('#progress-hide-1').hide();
                    
                                $.ajax({
                                    type:"POST",
                                    url: "{{route('hris.dataabsenperijinan.destroy')}}",
                                    dataType: 'json',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                    data: {
                                        tanggal_perizinan:tanggal_perizinan,
                                        nomor_form_perizinan:nomor_form_perizinan,
                                        enroll_id:enroll_id,
                                    },
                                    dataType: 'json',
                                    success: function(res){
                                        notif({
                                            msg: "<b>Info:</b> Data berhasil di hapus.",
                                            type: "info"
                                        });    
                                    },
                                    error: function(res){
                                        notif({
                                            msg: "<b>Error:</b> Oops data gagal di hapus.",
                                            type: "error"
                                        });    
                                    }
                                });
                    
                                $('#progress-show-1').hide();
                                $('#progress-hide-1').show();    

                                setTimeout(function myFunction() {
                                    location.reload();
                                }, 3000);           
                                
                            } else {
                                // else everythings
                            }
                        });
                    }

                },
                error: function(res){
                                
                }
            });           
                    
        });
        
        //Date range as a button
        $('#daterange-btn1').daterangepicker({
            ranges: {
                'Hari ini': [moment(), moment()],
                'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 Hari Kemarin': [moment().subtract(6, 'days'), moment()],
                '30 Hari Kemarin': [moment().subtract(29, 'days'), moment()],
                'Bulan Sekarang': [moment().startOf('month'), moment().endOf('month')],
                'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        }, function(start, end) {
            $('#daterange-btn1').html('<span><i class="fa fa-calendar"></i> ' + start.format("D MMM YYYY").toUpperCase() + ' s/d ' + end.format("D MMM YYYY").toUpperCase() + '</span><i class="fa fa-angle-down ml-1"></i>');
            var daterange1 = start.format("YYYY-MM-DD") + " s/d " + end.format("YYYY-MM-DD");
            $('#daterange1').val(daterange1);
            $('#daterange2').val(daterange1);
        })

        $(document).ready(function() {

            var start = moment().subtract(29, 'days');
            var end = moment();
            var htmlDateRange = '<span><i class="fa fa-calendar"></i> ' + start.format("D MMM YYYY").toUpperCase() + ' s/d ' + end.format("D MMM YYYY").toUpperCase() + '</span><i class="fa fa-angle-down ml-1"></i>'
            var daterange1 = start.format("YYYY-MM-DD") + " s/d " + end.format("YYYY-MM-DD");
            var dateUpdateKehadiran = end.format("DD-MM-YYYY");

            $('#daterange-btn1').html(htmlDateRange);
            $('#daterange1').val(daterange1);
            $('#daterange2').val(daterange1);

            var table1 = $('#datatable-ajax-crud').DataTable({
                processing: true,
                serverSide: true,
                lengthChange: false,
                pageLength: 10,
                pagingType: "simple",
                dom: '<"top"ipf>rt<"bottom"l><"clear">',
                destroy: true,
                "ajax": {
                    "url": "{{ route('hris.dataabsenperijinan.ajax_dataabsenperizinan') }}",
                    "dataType": "json",
                    "type": "POST",
                    "headers": {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    "dataSrc": "data",
                },
                columns: [
                    {
                        title: 'UUID',
                        data: 'uuid',
                        name: 'uuid'
                    },
                    {
                        title: 'UUID Master',
                        data: 'uuid_master',
                        name: 'uuid_master'
                    },
                    {
                        title: 'Tanggal Perizinan',
                        data: 'tanggal_perizinan',
                        name: 'tanggal_perizinan'
                    },
                    {
                        title: 'Nomor Form Perizinan',
                        data: 'nomor_form_perizinan',
                        name: 'nomor_form_perizinan'
                    },
                    {
                        title: 'Nomor Absen',
                        data: 'enroll_id',
                        name: 'enroll_id'
                    },
                    {
                        title: 'NIK',
                        data: 'nik',
                        name: 'nik'
                    },
                    {
                        title: 'Nama Karyawan',
                        data: 'employee_name',
                        name: 'employee_name'
                    },
                    {
                        title: 'Tanggal Dibuat',
                        data: 'created_at',
                        name: 'created_at'
                    },
                ],
                columnDefs: [
                    {
                        'visible': false,
                        'targets': [0,1,5,7]
                    }
                ],
                order: [
                    [7, 'desc']
                ]
            });

            table1.draw();

            $('#datatable-ajax-crud tbody').on('click', 'tr', function () {
                var tr = $(this).closest('tr');
                var row = table1.row(tr);

                var data = row.data();
        
                $("#form1 :input").prop("disabled", true);
                $("#btn-save-izin").prop("disabled", true);
                $("#btn-save-iks").prop("disabled", true);
                $("#btn-cancel-izin").prop("disabled", true);
                $("#btn-cancel-iks").prop("disabled", true);
                
                $("#datatable-ajax-crud tbody tr").removeClass('bg-cyan');
                $(this).addClass('bg-cyan');

                $('#uuid').val(data['uuid']);                    
                $('#uuid_master').val(data['uuid_master']);                    
                $('#tanggal_perijinan').val(data['tanggal_perizinan']);  
                $('#nomor_form_perizinan').val(data['nomor_form_perizinan']);  
                $('#enroll_id').val(data['enroll_id']);  
                $('#nik').val(data['nik']);
                $('#employee_name').val(data['employee_name']);

                if (data['kode_absen_ijin'] == "IKS") {
                    $("#kode_absen_ijin_iks").val(data['kode_absen_ijin']).trigger("change");
                    $('#absen_alasan_iks').val(data['absen_alasan']);
                    $('#time_mulai_ijin').val(data['time_mulai_ijin']);
                    $('#time_akhir_ijin').val(data['time_akhir_ijin']);
                    $('#total_time_ijin').val(data['total_time_ijin']);
                    $('#created_at_iks').text(data['created_at']);
                    $('#updated_at_iks').text(data['updated_at']);    
    
                    $("#kode_absen_ijin").val(null).trigger("change");
                    $('#tanggal_mulai_ijin').val(null);
                    $('#tanggal_akhir_ijin').val(null);
                    $('#absen_alasan_izin').val(null);
                    $('#created_at_izin').text(null);
                    $('#updated_at_izin').text(null);    
                    
                } else {
                    $("#kode_absen_ijin_iks").val(null).trigger("change");
                    $('#absen_alasan_iks').val(null);
                    $('#time_mulai_ijin').val(null);
                    $('#time_akhir_ijin').val(null);
                    $('#total_time_ijin').val(null);
                    $('#created_at_iks').text(null);
                    $('#updated_at_iks').text(null);    

                    $("#kode_absen_ijin").val(data['kode_absen_ijin']).trigger("change");
                    $('#tanggal_mulai_ijin').val(data['tanggal_mulai_ijin']);
                    $('#tanggal_akhir_ijin').val(data['tanggal_akhir_ijin']);    
                    $('#absen_alasan_izin').val(data['absen_alasan']);
                    $('#created_at_izin').text(data['created_at']);
                    $('#updated_at_izin').text(data['updated_at']);    
                        
                }
                $('#operator').val(data['operator']);

             });

        });

        function hitungtotaljam() {
            var tglform = $('#tanggal_perijinan').val();
            var tm1 = new Date(tglform + " " + $('#time_mulai_ijin').val());
            var tm2 = new Date(tglform + " " + $('#time_akhir_ijin').val());
            var total_time_ijin = diff_minutes(tm1, tm2);
            $('#total_time_ijin').val(total_time_ijin);
        };

        function diff_minutes(dt2, dt1) 
        {
       
         var diff =(dt2.getTime() - dt1.getTime()) / 1000;
         diff /= 60;
         return Math.abs(Math.round(diff));
         
        }

        $('body').on('keyup', '#total_time_ijin', function (event) {
            this.value = this.value.replace(/[^-0-9\.]/g,'');
        });
        
    </script>

@endsection
