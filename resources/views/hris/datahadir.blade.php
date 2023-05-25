@extends('admin.adminlayouts.adminlayout')

@section('head')
    <!-- Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/responsivebootstrap4.min.css') }}" rel="stylesheet" />

    <!--Select2 css -->
    <link href="{{URL::asset('assets/plugins/select2/select2.min.css')}}" rel="stylesheet" />

    <!-- Date Picker css-->
    <link href="{{URL::asset('assets/plugins/spectrum-date-picker/spectrum.css')}}" rel="stylesheet" />

    <!-- Time picker css-->
    <link href="{{URL::asset('assets/plugins/time-picker/jquery.timepicker.css')}}" rel="stylesheet" />

    <!--Mutipleselect css-->
    <link rel="stylesheet" href="{{URL::asset('assets/plugins/multipleselect/multiple-select.css')}}">

	<!-- Notifications  css -->
	<link href="{{URL::asset('assets/plugins/notify-growl/css/jquery.growl.css')}}" rel="stylesheet" />
	<link href="{{URL::asset('assets/plugins/notify-growl/css/notifIt.css')}}" rel="stylesheet" />

	<!---Sweetalert Css-->
	<link href="{{URL::asset('assets/plugins/sweet-alert/jquery.sweet-modal.min.css')}}" rel="stylesheet" />
	<link href="{{URL::asset('assets/plugins/sweet-alert/sweetalert.css')}}" rel="stylesheet" />

	<!-- Tabs css-->
	<link href="{{URL::asset('assets/plugins/tabs/tabs-style.css')}}" rel="stylesheet" />

    <style>
        .table-responsive{
            height:500px;
            overflow:scroll;
          }
          thead tr:nth-child(1) th{
            background: white;
            position: sticky;
            top: 0;
            z-index: 10;
          }
    </style>

@stop
@section('mainarea')
    <!-- page-header -->
    <div class="page-header shadow pr-2 m-0 pt-0 pb-0 pl-2">
        <ol class="breadcrumb breadcrumb-arrow m-0 p-0">
            <li><a href="#">ABSENSI KARYAWAN</a></li>
            <li class="active"><span>DATA KEHADIRAN</span></li>
        </ol>
        <div class="ml-auto">
            <div class="input-group">
                <a href="#" id="btn-refresh-data" class="btn btn-icon btn-secondary p-0 m-0" data-toggle="tooltip"
                    title="" data-placement="bottom" data-original-title="Refresh Halaman">
                    <span>
                        <i class="fa fa-refresh"></i>
                    </span>
                </a>
            </div>
        </div>
    </div>
    <!-- End page-header -->

    <!-- row -->
    <div class="row">

        <div class="col-md-6">
            <div class="card shadow card-collapsed">
                <div class="card-header text-white bg-gradient-primary p-2">
                    <div class="card-title">MESIN ABSENSI</div>
                    <div class="card-options ">
                        <a href="#" class="card-options-collapse mr-2" data-toggle="card-collapse"><i class="fe fe-chevron-up text-white"></i></a>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">TANGGAL ABSENSI : </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                        </div>
                                    </div>
                                    <input readonly id="tanggal_mesin_absensi" class="form-control fc-datepicker" placeholder="DD-MM-YYYY" type="text">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light m-0 p-1">
                    <div class="text-white">
                        <a id="btn-updateKehadiran" class="btn btn-app btn-primary mr-0 mt-0 mb-0 text-white"><span><i class="fa fa-download"></i></span> UPDATE ABSENSI</a>
                    </div>
                </div>
            </div>
        </div><!-- col end -->
 
        <div class="col-md-6">
            <div class="card shadow card-collapsed">
                <div class="card-header text-white bg-gradient-primary p-2">
                    <div class="card-title">MESIN ABSENSI LINTAS HARI</div>
                    <div class="card-options ">
                        <a href="#" class="card-options-collapse mr-2" data-toggle="card-collapse"><i class="fe fe-chevron-up text-white"></i></a>
                    </div>
                </div>
                <form id="form_update_lintashari" method="post">
                        @csrf
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">TANGGAL ABSENSI  : </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                            </div>
                                        </div>
                                        <input id="" name="periode_absen" type="text" class="form-control data_range" required></input>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">PILIH KARYAWAN  : </label>
                                <div class="form-group">
                                    <span class="input-group">
                                        <select id="selectEmployeeID" name="selectEmployeeID[]" multiple data-placeholder="Pilih karyawan" class="form-control select2 EmployeeID" style="width: 699.238px;" required>
                                            @foreach ($selectemployee as $r_empl)
                                                <option value="{{$r_empl->enroll_id}}">{{$r_empl->select_employee}}</option>
                                            @endforeach
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light m-0 p-1">
                        <div class="text-white">
                            <a id="BtnUpdateLintasHari" class="btn btn-app btn-primary mr-0 mt-0 mb-0 text-white BtnUpdateLintasHari"><span><i class="fa fa-download"></i></span> UPDATE ABSENSI</a>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- col end -->

        <div class="col-md-12">
            <!-- BEGIN FORM-->
            {!! Form::open(['route' => 'hris.mdabsenhadir.ajax_exportexcel', 'id' => 'formExport', 'name' => 'formExport','method'=>'post']) !!}

            @csrf
            <div class="card shadow">
                <div class="card-header text-white bg-gradient-primary p-2">
                    <div class="card-title">KEHADIRAN : </div>
                    <input type="hidden" id="daterange1" name="daterange1">
                    <a class="ml-2 p-0 nav-link card-title" id="daterange-btn1" data-toggle="tooltip"
                    title="" data-placement="bottom" data-original-title="Klik di sini untuk pilih tanggal kehadiran">
                    </a>
                    <div class="card-options ">
                        <a href="#" class="card-options-collapse mr-2" data-toggle="card-collapse"><i class="fe fe-chevron-up text-white"></i></a>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">DEPARTMENT : </label>
                                <select id="selectDepartment" name="selectDepartment" class="form-control">
                                    <option value="">-- PILIH DEPARTMENT --</option>
                                    @foreach ($department as $r_department)
                                        <option value="{{$r_department->department_id}}">{{$r_department->department_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">BAGIAN : </label>
                                <select id="selectBagian" name="selectBagian" class="form-control">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">STAFF / NON STAFF : </label>
                                <select id="status_staff" name="status_staff" class="form-control">
                                    <option value="">-- PILIH STAFF --</option>
                                    <option value="STAFF">STAFF</option>
                                    <option value="NON STAFF">NON STAFF</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">CARI DATA : </label>
                                <input id="searchData" name="searchData" class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light m-0 p-1">
                    <div class="text-white">
                        <button type="submit" id="btn-exportexcel" class="btn btn-app btn-primary mr-0 mt-0 mb-0" data-toggle="tooltip" title="Export Data ke File Excel"><i class="ion-ios7-download"></i> EXPORT</button>
                        <button type="button" id="btn-importexcel" class="btn btn-app btn-primary mr-0 mt-0 mb-0" data-toggle="tooltip" title="Import Data Dari File Excel"><i class="ion-ios7-upload"></i> IMPORT</button>
                        <a id="btn-caridata" class="btn btn-app btn-primary mr-0 mt-0 mb-0 text-white" data-toggle="tooltip" title="Cari Data"><i class="ion-search"></i> CARI</a>
                        <button type="button" id="btn-prosespayroll" class="btn btn-app btn-secondary mr-0 mt-0 mb-0" data-toggle="tooltip" title="Proses Payroll"><i class="ion-cash"></i> PROSES PAYROLL</button>
                    </div>
                </div>

            </div>
        </div><!-- col end -->
        {{-- </form> --}}
        {!! Form::close() !!}
        <!-- END FORM-->

        

        <div id="data-absensi-karyawan" class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="card shadow">
                <div class="card-header text-white bg-gradient-primary p-2">
                    <div class="card-title">DATA ABSENSI KARYAWAN</div>
                    <div class="card-options ">
                        <a href="#" class="card-options-collapse mr-2" data-toggle="card-collapse"><i class="fe fe-chevron-up text-white"></i></a>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="datatable-ajax-crud" class="table table-sm table-striped table-hover w-100">
                                    <thead>
                                        <tr class="text-center">
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-50 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-50 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                            <th scope="col" class="bg-primary border-primary w-5 align-middle"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light p-1">
                </div>
            </div>
        </div>
    </div>

    <!-- boostrap show department model -->
    <div class="modal fade" id="ajax-absenijin-model-add" role="dialog" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary p-2">
                    <h4 class="modal-title pl-2" id="ajaxAbsenIjinModel"></h4>
                    <button type="button" id="btn-close" class="close text-white ml-1" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Tutup Dialog">
                        <i class="fa fa-remove"></i>
                    </button>
                </div>
                <div class="progress progress-xs mb-0">
                    <div id="progress-show-1" class="progress-bar progress-bar-indeterminate bg-green"></div>
                    <div id="progress-hide-1" class="progress-bar"></div>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['route' => 'hris.employeeatr.create', 'id' => 'form1', 'name' => 'form1', 'method'=>'post']) !!}
                    <input id="uuid" type="hidden">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">No. Form Perizinan : </label>
                                <input type="text" readonly class="form-control" id="nomor_form_perizinan" name="nomor_form_perizinan" placeholder="Nomor Form Perizinan">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Tanggal Permohonan Izin</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                        </div>
                                    </div>
                                    <input id="tanggal_perijinan" name="tanggal_perijinan" type="text" class="form-control fc-datepicker" placeholder="Tanggal Permohonan Izin" maxlength="50" size="50">
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
                                <input type="text" class="form-control" id="nik" name="nik" placeholder="NIK">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Nama Karyawan : </label>
                                <input type="text" readonly class="form-control" id="employee_name" name="employee_name" placeholder="Nama Karyawan">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Bagian : </label>
                                <input type="text" readonly class="form-control" id="sub_dept_name" name="sub_dept_name" placeholder="Bagian">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Jabatan : </label>
                                <input type="text" readonly class="form-control" id="posisi_name" name="posisi_name" placeholder="Jabatan">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label"> Aktif/Non Aktif : </label>
                                <input type="text" readonly class="form-control" id="work_status" name="work_status">
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
                                                    <input id="tanggal_mulai_ijin" name="tanggal_mulai_ijin" type="text" class="form-control fc-datepicker" placeholder="Tanggal Mulai Izin" maxlength="50" size="50">
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
                                                    <input id="tanggal_akhir_ijin" name="tanggal_akhir_ijin" type="text" class="form-control fc-datepicker" placeholder="Tanggal Mulai Izin" maxlength="50" size="50">
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
                                                <textarea class="form-control" id="absen_alasan_izin" name="absen_alasan_izin" rows="2" placeholder="Keterangan"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12 pt-3">
                                            <div class="btn-list">
                                                <button type="button" id="btn-save-izin" class="btn btn-secondary btn-app"><i class="fa fa-save"></i> Simpan</button>
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
                                                    <input id="time_mulai_ijin" name="time_mulai_ijin" class="form-control" id="tpBasic" placeholder="--:--" maxlength="5" onChange="hitungtotaljam();" type="text">
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
                                                    <input id="time_akhir_ijin" name="time_akhir_ijin" class="form-control" id="tpBasic" placeholder="--:--" maxlength="5" onChange="hitungtotaljam();"  type="text">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Total Jam Izin</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa fa-clock-o tx-16 lh-0 op-6"></i>
                                                        </div>
                                                    </div><!-- input-group-prepend -->
                                                    <input id="total_time_ijin" name="total_time_ijin" class="form-control" id="tpBasic" placeholder="Set time" type="text">
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
                                                <textarea class="form-control" id="absen_alasan_iks" name="absen_alasan_iks" rows="2" placeholder="Keterangan"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12 pt-3">
                                            <div class="btn-list">
                                                <button type="button" id="btn-save-iks" class="btn btn-secondary btn-app"><i class="fa fa-save"></i> Simpan</button>
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
                <div class="modal-footer bg-primary p-1">
                    <div class="btn-list">
                        <button type="button" id="btn-close" class="btn btn-warning btn-app" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
          </div>
    </div>
    <!-- end bootstrap model -->

@endsection

@section('footerjs')

    <!--Jquery Sparkline js-->
    <script src="{{ URL::asset('assets/plugins/vendors/jquery.sparkline.min.js') }}"></script>

    <!-- Chart Circle js-->
    <script src="{{ URL::asset('assets/plugins/vendors/circle-progress.min.js') }}"></script>

    <!--Time Counter js-->
    <script src="{{ URL::asset('assets/plugins/counters/jquery.missofis-countdown.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/counters/counter.js') }}"></script>

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

    <!--Select2 js -->
    <script src="{{URL::asset('assets/plugins/select2/select2.full.min.js')}}"></script>

    <!--MutipleSelect js-->
    <script src="{{URL::asset('assets/plugins/multipleselect/multiple-select.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/multipleselect/multi-select.js')}}"></script>

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

    <!-- Sweet alert js-->
    <script src="{{URL::asset('assets/plugins/sweet-alert/jquery.sweet-modal.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/sweet-alert/sweetalert.min.js')}}"></script>

    <script type="text/javascript">
        $('#progress-show-1').hide();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('body').on('click', '#btn-refresh-data', function (event) {
            $("#datatable-ajax-crud").DataTable().ajax.reload();
            notif({
                msg: "<b>Success:</b> Data sudah di refresh.",
                type: "success"
            });
        });

        $('.fc-datepicker').datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'dd-mm-yy'
        });

        $(function(){
            'use strict';

            $('.select2').select2({
              minimumResultsForSearch: Infinity
            });

            // Select2 by showing the search
            $('.select2-show-search').select2({
              minimumResultsForSearch: ''
            });

            // Colored Hover
            $('#select2').select2({
              dropdownCssClass: 'hover-success',
              minimumResultsForSearch: Infinity // disabling search
            });

            $('#select3').select2({
              dropdownCssClass: 'hover-danger',
              minimumResultsForSearch: Infinity // disabling search
            });

            // Outline Select
            $('#select4').select2({
              containerCssClass: 'select2-outline-success',
              dropdownCssClass: 'bd-success hover-success',
              minimumResultsForSearch: Infinity // disabling search
            });

            $('#select5').select2({
              containerCssClass: 'select2-outline-info',
              dropdownCssClass: 'bd-info hover-info',
              minimumResultsForSearch: Infinity // disabling search
            });

            // Full Colored Select Box
            $('#select6').select2({
              containerCssClass: 'select2-full-color select2-primary',
              minimumResultsForSearch: Infinity // disabling search
            });

            $('#select7').select2({
              containerCssClass: 'select2-full-color select2-danger',
              dropdownCssClass: 'hover-danger',
              minimumResultsForSearch: Infinity // disabling search
            });

            // Full Colored Dropdown
            $('#select8').select2({
              dropdownCssClass: 'select2-drop-color select2-drop-primary',
              minimumResultsForSearch: Infinity // disabling search
            });

            $('#select9').select2({
              dropdownCssClass: 'select2-drop-color select2-drop-indigo',
              minimumResultsForSearch: Infinity // disabling search
            });

            // Full colored for both box and dropdown
            $('#select10').select2({
              containerCssClass: 'select2-full-color select2-primary',
              dropdownCssClass: 'select2-drop-color select2-drop-primary',
              minimumResultsForSearch: Infinity // disabling search
            });

            $('#select11').select2({
              containerCssClass: 'select2-full-color select2-indigo',
              dropdownCssClass: 'select2-drop-color select2-drop-indigo',
              minimumResultsForSearch: Infinity // disabling search
            });
        });

        function format(d) {
            // `d` is the original data object for the row
            var adaPerizinan = "";
            var adaGagalAbsen = "";
            var adaSPL = "";

            if ((d.nomor_absen_ijin !== "") && (d.nomor_absen_ijin !== null) && (d.status_absen !== "IKS") && (d.status_absen !== "M")) {
                adaPerizinan = '<div class="col-md-4">' +
                    '<div class="expanel expanel-light p-1 mt-1 mb-1">' +
                        '<div class="expanel-heading p-1">' +
                            ' <b>PERIZINAN (CUTI DAN IZIN)</b>' +
                        '</div>' +
                        '<div class="expanel-body collapse" id="collapse03' + d.uuid +'">' +
                            '<div class="row">' +
                                '<div class="col-md-12">' +
                                    '<table class="table table-sm w-100">' +
                                        '<tbody>' +
                                            '<tr><th>Nomor Form Perizinan</th></tr>' +
                                            '<tr><td>' + d.nomor_absen_ijin + '</td></tr>' +
                                            '<tr><th>Nama Perizinan</th></tr>' +
                                            '<tr><td>' + d.nama_absen_ijin + '</td></tr>' +
                                            '<tr><th>Kode Payroll</th></tr>' +
                                            '<tr><td>' + d.kode_ijin_payroll + '</td></tr>' +
                                            '<tr><th>Tanggal Izin Dari</th></tr>' +
                                            '<tr><td>' + d.tanggal_mulai_ijin + '</td></tr>' +
                                            '<tr><th>Tanggal Izin Sampai</th></tr>' +
                                            '<tr><td>' + d.tanggal_akhir_ijin + '</td></tr>' +
                                        '</tbody>' +
                                    '</table>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            } else if ((d.nomor_absen_ijin !== "") && (d.nomor_absen_ijin !== null) && (d.status_absen == "IKS")) {
                adaPerizinan = '<div class="col-md-4">' +
                    '<div class="expanel expanel-light p-1 mt-1 mb-1">' +
                        '<div class="expanel-heading p-1">' +
                            ' <b>PERIZINAN (IKS)</b>' +
                        '</div>' +
                        '<div class="expanel-body collapse" id="collapse03' + d.uuid +'">' +
                            '<div class="row">' +
                                '<div class="col-md-12">' +
                                    '<table class="table table-sm w-100">' +
                                        '<tbody>' +
                                            '<tr><th>Nomor Form IKS</th></tr>' +
                                            '<tr><td>' + d.nomor_absen_ijin + '</td></tr>' +
                                            '<tr><th>Nama Perizinan</th></tr>' +
                                            '<tr><td>' + d.nama_absen_ijin + '</td></tr>' +
                                            '<tr><th>Kode Payroll</th></tr>' +
                                            '<tr><td>' + d.kode_ijin_payroll + '</td></tr>' +
                                            '<tr><th>Dari Pukul</th></tr>' +
                                            '<tr><td>' + d.permits_dari_pukul + '</td></tr>' +
                                            '<tr><th>Sampai Pukul</th></tr>' +
                                            '<tr><td>' + d.permits_sampai_pukul + '</td></tr>' +
                                            '<tr><th>Total Jam IKS</th></tr>' +
                                            '<tr><td>' + d.total_menit_permits + '</td></tr>' +
                                        '</tbody>' +
                                    '</table>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            }

            if (d.nomor_form_perubahan_absen) {
                adaGagalAbsen = '<div class="col-md-4">' +
                    '<div class="expanel expanel-light p-1 mt-1 mb-1">' +
                        '<div class="expanel-heading p-1">' +
                            ' <b>GAGAL ABSEN</b>' +
                        '</div>' +
                        '<div class="expanel-body collapse" id="collapse03' + d.uuid +'">' +
                            '<div class="row">' +
                                '<div class="col-md-12">' +
                                    '<table class="table table-sm w-100">' +
                                        '<tr><th>Nomor Form Gagal Absen</th></tr>' +
                                        '<tr><td>' + d.nomor_form_perubahan_absen + '</td></tr>' +
                                        '<tr><th>Tanggal Izin Dari</th></tr>' +
                                        '<tr><td>' + d.tanggal_mulai_ijin + '</td></tr>' +
                                        '<tr><th>Tanggal Izin Sampai</th></tr>' +
                                        '<tr><td>' + d.tanggal_akhir_ijin + '</td></tr>' +
                                    '</table>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            }

            if (d.nomor_form_lembur) {
                adaSPL = '<div class="col-md-4">' +
                    '<div class="expanel expanel-light p-1 mt-1 mb-1">' +
                        '<div class="expanel-heading p-1">' +
                            ' <b>LEMBUR (SPL)</b>' +
                        '</div>' +
                        '<div class="expanel-body collapse" id="collapse03' + d.uuid +'">' +
                            '<div class="row">' +
                                '<div class="col-md-12">' +
                                    '<table class="table table-sm w-100">' +
                                        '<tbody>' +
                                            '<tr><th>Nomor Form SPL</th></tr>' +
                                            '<tr><td>' + d.nomor_form_lembur + '</td></tr>' +
                                            '<tr><th>Waktu Lembur Dari</th></tr>' +
                                            '<tr><td>' + d.mulai_jam_lembur + '</td></tr>' +
                                            '<tr><th>Waktu Lembur Sampai</th></tr>' +
                                            '<tr><td>' + d.akhir_jam_lembur + '</td></tr>' +
                                            '<tr><th>Jumlah Jam Lembur</th></tr>' +
                                            '<tr><td>' + d.jumlah_jam_lembur + '</td></tr>' +
                                        '</tbody>' +
                                    '</table>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            }
            return (

                '<div class="expanel expanel-success">' +
                    '<div class="expanel-heading p-1">' +
                        '<h4 class="expanel-title">' +
                            '<div class="btn-group">' +
                                '<button id="btn-add" class="btn btn-icon btn-sm text-sm btn-light pl-1 pr-1 pt-0 pb-0 m-0" type="button"><i class="fa fa-plus"></i> ADD PERIZINAN</button>' +
                            '</div>' +
                        ' <b>DETAIL DATA KEHADIRAN KARYAWAN</b></h4>' +
                    '</div>' +
                    '<input type="hidden" id="uuid_detail" name="uuid_detail" value="' + d.uuid +'">' +
                    '<input type="hidden" id="nomor_absen_ijin" name="nomor_absen_ijin" value="' + d.nomor_absen_ijin +'">' +
                    '<div class="expanel-body">' +
                        '<div class="row">' +
                            '<div class="col-md-12">' +
                                '<div class="expanel expanel-light p-0 mt-1 mb-1">' +
                                    '<div class="expanel-heading p-1 clearfix">' +
                                        '<button class="btn btn-icon btn-sm btn-light pl-0 pt-0 pb-0 pr-0 mr-0" type="button" data-toggle="collapse" data-target="#collapse01' + d.uuid +'" aria-expanded="false" aria-controls="collapse01' + d.uuid +'"><i class="fa fa-info"></i></button>' +
                                        ' <b>DIVISI</b>' +
                                    '</div>' +
                                    '<div class="expanel-body collapse" id="collapse01' + d.uuid +'">' +
                                        '<div class="row">' +
                                            '<div class="col-md-4">' +
                                                '<div class="form-group">' +
                                                    '<label class="form-label">Divisi</label>' +
                                                    '<div>' + d.site_nirwana_name + '</div>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div class="col-md-4">' +
                                                '<div class="form-group">' +
                                                    '<label class="form-label">Department</label>' +
                                                    '<div>' + d.department_name + '</div>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div class="col-md-4">' +
                                                '<div class="form-group">' +
                                                    '<label class="form-label">Bagian</label>' +
                                                    '<div>' + d.sub_dept_name + '</div>' +
                                                '</div>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                            '<div class="col-md-12">' +
                                '<div class="expanel expanel-light p-0 mt-1 mb-1">' +
                                    '<div class="expanel-heading p-1 clearfix">' +
                                        '<button class="btn btn-icon btn-sm btn-light pl-0 pt-0 pb-0 pr-0 mr-0" type="button" data-toggle="collapse" data-target="#collapse02' + d.uuid +'" aria-expanded="false" aria-controls="collapse02' + d.uuid +'"><i class="fa fa-info"></i></button>' +
                                        ' <b>STATUS KARYAWAN</b>' +
                                    '</div>' +
                                    '<div class="expanel-body collapse" id="collapse02' + d.uuid +'">' +
                                        '<div class="row">' +
                                            '<div class="col-md-4">' +
                                                '<div class="form-group">' +
                                                    '<label class="form-label">Aktif/Non Aktif</label>' +
                                                    '<div>' + d.work_status + '</div>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div class="col-md-4">' +
                                                '<div class="form-group">' +
                                                    '<label class="form-label">Kontrak/Tetap</label>' +
                                                    '<div>' + d.employee_status + '</div>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div class="col-md-4">' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                            '<div class="col-md-12">' +
                                '<div class="expanel expanel-light p-0 mt-1 mb-1">' +
                                    '<div class="expanel-heading p-1 clearfix">' +
                                        '<button class="btn btn-icon btn-sm btn-light pl-0 pt-0 pb-0 pr-0 mr-0" type="button" data-toggle="collapse" data-target="#collapse03' + d.uuid +'" aria-expanded="false" aria-controls="collapse03' + d.uuid +'"><i class="fa fa-info"></i></button>' +
                                        ' <b>GAGAL ABSEN, PERIZINAN DAN SPL</b>' +
                                    '</div>' +
                                    '<div class="expanel-body collapse" id="collapse03' + d.uuid +'">' +
                                        '<div class="row">' +
                                            '<div class="col-md-6">' +
                                                '<div class="form-group">' +
                                                    '<label class="form-label">Alasan Absen</label>' +
                                                    '<div class="border">' + d.absen_alasan + '</div>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div class="col-md-6">' +
                                                '<div class="form-group">' +
                                                    '<label class="form-label">Catatan HRD</label>' +
                                                    '<div class="border">' + d.catatan_hrd + '</div>' +
                                                '</div>' +
                                            '</div>' +
                                            adaGagalAbsen + adaPerizinan + adaSPL +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
        }

        $("#check-all-karyawan").attr('disabled','disabled');

        $(document).ready(function() {
            var start = moment().subtract(29, 'days');
            var end = moment();
            var htmlDateRange = '<span><i class="fa fa-calendar"></i> ' + start.format("D MMM YYYY").toUpperCase() + ' s/d ' + end.format("D MMM YYYY").toUpperCase() + '</span><i class="fa fa-angle-down ml-1"></i>'
            var daterange1 = start.format("YYYY-MM-DD") + " s/d " + end.format("YYYY-MM-DD");
            var dateUpdateKehadiran = end.format("DD-MM-YYYY");

            $('#daterange-btn1').html(htmlDateRange);
            $('#daterange1').val(daterange1);
            $('#tanggal_mesin_absensi').val(dateUpdateKehadiran);

            $("#data-absensi-karyawan").hide();
            $("#selectBagian").append(new Option("-- PILIH BAGIAN --", ""));
        });


        $('body').on('click', '#check-all-karyawan', function (event) {
            var department_id = $('#selectDepartment').val();
            var selectEmployeeID = $('#selectEmployeeID').val();
            //alert($("#selectEmployeeID").val(null).length);

            if ($("#selectEmployeeID").val() == "") {
                $("#selectEmployeeID > option").prop("selected", "selected");
                $("#selectEmployeeID").trigger("change");
            } else {
                $("#selectEmployeeID").val(null).trigger("change");
            }

        });

        $('body').on('change', '#selectDepartment', function () {
            var department_id = $('#selectDepartment').val();

            $("#selectBagian").empty();

            $("#selectBagian").append(new Option("-- PILIH BAGIAN --", ""));

            if(department_id){
                $.ajax({
                    type:"POST",
                    url: "{{route('hris.departmentall.getSelectSubDept')}}",
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: {
                        department_id:department_id,
                    },
                    dataType: 'json',
                    success: function(resA){
                        if(resA){
                            for(i=0;i<resA.length;i++) {
                                $("#selectBagian").append(new Option(resA[i].sub_dept_name, resA[i].sub_dept_id));
                            }
                        }
                    }
                });
            }
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
        })

        $("#formExport").on("keypress", function (event) {

            var keyPressed = event.keyCode || event.which;
            if (keyPressed === 13) {

                event.preventDefault();
                return false;
            }
        });

        $("#formExport").on('keypress',function(e) {
            if(e.which == 13) {
                $('#btn-caridata').click();
            }
        });

        $('body').on('click', '#btn-caridata', function (event) {

            $("#data-absensi-karyawan").show('slow');

            $('#datatable-ajax-crud').DataTable().clear();
            $('#datatable-ajax-crud').DataTable().destroy();
            $('#datatable-ajax-crud').empty();

            //myHeadDataTable();

            var selectDepartment = $('#selectDepartment').val();
            var selectBagian = $('#selectBagian').val();
            var daterange1 = $('#daterange1').val();
            var status_staff = $('#status_staff').val();
            var searchData = $('#searchData').val();

            var table = $('#datatable-ajax-crud').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                destroy: true,
                "language": {
                    processing: '<center><div class="dimmer active"><div class="lds-hourglass p-0 m-0"></div></div> Mohon untuk menunggu...</center> '},
                "ajax": {
                    "url": "{{ route('hris.mdabsenhadir.ajax_datahadir') }}",
                    "dataType": "json",
                    "type": "POST",
                    "headers": {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    "dataSrc": "data",
                    "data": {
                        selectDepartment:selectDepartment,
                        selectBagian:selectBagian,
                        daterange1:daterange1,
                        status_staff:status_staff,
                        searchData:searchData,
                    },

                },
                columns: [
                    {
                        title: 'UUID',
                        data: 'uuid',
                        name: 'uuid'
                    },
                    {
                        title: 'Employee ID',
                        data: 'employee_id',
                        name: 'employee_id'
                    },
                    {
                        title: 'TANGGAL<br>KEHADIRAN',
                        data: 'tanggal_berjalan',
                        name: 'tanggal_berjalan'
                    },
                    {
                        title: 'KODE HARI',
                        data: 'kode_hari',
                        name: 'kode_hari'
                    },
                    {
                        title: 'HARI',
                        data: 'nama_hari',
                        name: 'nama_hari'
                    },
                    {
                        title: 'NIK',
                        data: 'nik',
                        name: 'nik'
                    },
                    {
                        title: 'NO.<br>ABSEN',
                        data: 'enroll_id',
                        name: 'enroll_id'
                    },
                    {
                        title: 'NAMA KARYAWAN',
                        data: 'employee_name',
                        name: 'employee_name'
                    },
                    {
                        title: 'BAGIAN',
                        data: 'sub_dept_name',
                        name: 'sub_dept_name'
                    },
                    {
                        title: 'STAFF/<br>NON STAFF',
                        data: 'status_staff',
                        name: 'status_staff'
                    },
                    {
                        title: 'KERJA/<br>LIBUR',
                        data: 'kerjalibur',
                        name: 'kerjalibur'
                    },
                    {
                        title: 'JADWAL<br>KERJA (IN)',
                        data: 'mulai_jam_kerja',
                        name: 'mulai_jam_kerja'
                    },
                    {
                        title: 'JADWAL<br>KERJA (OUT)',
                        data: 'akhir_jam_kerja',
                        name: 'akhir_jam_kerja'
                    },
                    {
                        title: 'ABSEN<br>(IN)',
                        data: 'absen_masuk_kerja',
                        name: 'absen_masuk_kerja'
                    },
                    {
                        title: 'ABSEN<br>(OUT)',
                        data: 'absen_pulang_kerja',
                        name: 'absen_pulang_kerja'
                    },
                    {
                        title: 'DT',
                        data: 'jumlah_menit_absen_dt',
                        name: 'jumlah_menit_absen_dt'
                    },
                    {
                        title: 'PC',
                        data: 'jumlah_menit_absen_pc',
                        name: 'jumlah_menit_absen_pc'
                    },
                    {
                        title: 'Jumlah<br>DTPC',
                        data: 'jumlah_menit_absen_dtpc',
                        name: 'jumlah_menit_absen_dtpc'
                    },
                    {
                        title: 'STATUS<br>ABSEN',
                        data: 'status_absen',
                        name: 'status_absen'
                    },
                    {
                        title: 'STATUS<br>AKTIF',
                        data: 'status_aktif',
                        name: 'status_aktif'
                    },
                ],
                order: [],
                columnDefs: [
                    {
                        'visible': false,
                        'targets': [0,1,3]
                    },
                    {
                        orderable: false,
                        targets: [2,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19]
                    },
                    {
                        className: "w-5 text-center text-nowrap",
                        targets: [2,4,5,9,10,11,12,13,14,19]
                    },
                    {
                        className: "w-5 text-right text-nowrap",
                        targets: [15,16,17]
                    },
                    {
                        className: "w-50 text-nowrap",
                        targets: [6,7,8]
                    }
                ],
                "createdRow": function (row, data, dataIndex) {
                    if ((data['kode_hari'] == "5") || (data['kode_hari'] == "6") || (data['kerjalibur'] == "LIBUR")) {
                        $(row).css('background', 'yellow');
                    } else if ((data['status_absen'] == "M") || (data['status_absen'] == "TL")) {
                            $(row).css('background', 'red');
                     }
                    if (data['nomor_form_perubahan_absen'] !== null) {
                        $(row).css('background', 'lime');
                    }
                }
            });

            $('#datatable-ajax-crud tbody').on('click', 'tr', function () {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }

                $("#datatable-ajax-crud tbody tr").removeClass('bg-cyan');
                $(this).addClass('bg-cyan');
             });

             table.draw();

        });

        $('body').on('click', '#btn-exportexcel', function (event) {
            notif({
                msg: "<b>Info:</b> Data sedang di proses, mohon menunggu",
                type: "info"
            });
        });

        $('body').on('change', '#tanggal_perijinan', function (event) {
            var kode_absen_ijin = $('#kode_absen_ijin').val();
            var date_permohonan = $(this).val();
            $('#tanggal_mulai_ijin').val(date_permohonan);
            $('#tanggal_akhir_ijin').val(date_permohonan);
            var tglform = date_permohonan;
            var tgl = tglform.split('-');
            var thn = tgl[0].substring(4,2);
            var bln = tgl[1];

            if ((kode_absen_ijin == 'DL') || (kode_absen_ijin == 'I') || (kode_absen_ijin == 'S')) {
                $('#nomor_form_perizinan').val('FPI/HR/' + thn + bln + '/');
            } else {
                $('#nomor_form_perizinan').val('FPC/HR/' + thn + bln + '/');
            }

        });

        $('body').on('click', '#btn-add', function (event) {
            var uuid = $('#uuid_detail').val();
            var tanggal_perijinan = "";

            $.ajax({
                type:"POST",
                url: "{{route('hris.dataabsenperijinan.ajax_getkehadiran')}}",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    uuid:uuid,
                },
                dataType: 'json',
                success: function(res){
                    $('#uuid').val(res['uuid']);

                    var nomor_form_perizinan = res['nomor_absen_ijin'];

                    if (nomor_form_perizinan) {
                        notif({
                            msg: "<b>Warning:</b> Data sudah memiliki Nomor Form Perizinan.",
                            type: "warning"
                        });
                        return false;
                    } else {

                        var tanggal = res['tanggal_berjalan'];
                        
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

                                    $('#tanggal_perijinan').val(res['tanggal_berjalan']);
                                    $('#tanggal_mulai_ijin').val(res['tanggal_berjalan']);
                                    $('#tanggal_akhir_ijin').val(res['tanggal_berjalan']);
                                    $('#enroll_id').val(res['enroll_id']);
                                    $('#nik').val(res['nik']);
                                    $('#employee_name').val(res['employee_name']);
                                    $('#sub_dept_name').val(res['sub_dept_name']);
                                    $('#posisi_name').val(res['posisi_name']);
                                    $('#work_status').val(res['work_status']);
                                    $('#ajax-absenijin-model-add').modal('show');
                                    $('#ajaxAbsenIjinModel').html('<b>[ADD] ABSEN PERIZINAN & IKS</b>');
                                    var tglform = res['tanggal_berjalan'];
                                    var tgl = tglform.split('-');
                                    var thn = tgl[0].substring(4,2);
                                    var bln = tgl[1];
                                    $('#nomor_form_perizinan').val('FPI/HR/' + thn + bln + '/');
                                    
                                }

                            },
                            error: function(resA){
                                            
                            }
                        });                       

                    }
                },
                error: function(res){
                    $('#ajax-absenijin-model-add').modal('hide');
                }
            });
        });

        $('body').on('click', '#tab-iks', function (event) {
            var tglform = $('#tanggal_perijinan').val();
            var tgl = tglform.split('-');
            var thn = tgl[0].substring(4,2);
            var bln = tgl[1];
            $('#nomor_form_perizinan').val('FPI/HR/' + thn + bln + '/');
        });

        $('body').on('click', '#tab-izin', function (event) {
            $("#kode_absen_ijin").val(null).trigger("change");

            var tglform = $('#tanggal_perijinan').val();
            var tgl = tglform.split('-');
            var thn = tgl[0].substring(4,2);
            var bln = tgl[1];
            $('#nomor_form_perizinan').val('FPI/HR/' + thn + bln + '/');
        });


        $('body').on('change', '#kode_absen_ijin', function (event) {
            var tanggal_perijinan = $('#tanggal_perijinan').val();
            var kode_absen_ijin = $('#kode_absen_ijin').val();
            var tglform = tanggal_perijinan;
            var tgl = tglform.split('-');
            var thn = tgl[0].substring(4,2);
            var bln = tgl[1];

            if ((kode_absen_ijin == 'DL') || (kode_absen_ijin == 'I') || (kode_absen_ijin == 'S')) {
                $('#nomor_form_perizinan').val('FPI/HR/' + thn + bln + '/');
            } else {
                $('#nomor_form_perizinan').val('FPC/HR/' + thn + bln + '/');
            }

        });

        function diff_hours(dt2, dt1)
        {

         var diff =(dt2.getTime() - dt1.getTime()) / 1000;
         diff /= (60 * 60);
         return Math.abs(parseFloat(diff).toFixed(1));

        }

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

        function defaultDate(s) {
            if(s) {
                var bits = s.split('-');
                var d = bits[2] + '-' + bits[1] + '-' + bits[0];
            }
            return d;
        }

        $('body').on('click', '#btn-updateKehadiran', function (event) {

            var tgl = defaultDate($('#tanggal_mesin_absensi').val());
            var tanggal = tgl.split(' s/d ');
            
            // LAGI COBA TEST CLOSING PAYROLL
            $.ajax({
                type:"POST",
                url: "{{route('hris.dataclosingpayroll.ajax_getclosing')}}",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    tanggal:tanggal[0],
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
                        var tanggal_mesin_absensi = defaultDate($('#tanggal_mesin_absensi').val());
                        var tgl_absensi_pilih = new Date(tanggal_mesin_absensi);
                        var days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
                        var months = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AGU','SEP','OKT','NOV','DES'];
            
                        tgl_absensi_pilih = days[tgl_absensi_pilih.getDay()] + " TANGGAL " + tgl_absensi_pilih.getDate() + " " + months[tgl_absensi_pilih.getMonth()] + " " + tgl_absensi_pilih.getFullYear();
            
                        text  = "DATA KEHADIRAN PADA HARI " + tgl_absensi_pilih + " AKAN DI REPLACE OLEH DATA TERBARU DARI MESIN ABSENSI !!!";
                        message = "APAKAH ANDA YAKIN ?";
                        type = "warning";
                        swal({
                            title: message,
                            text: text,
                            type: type,
                            showCancelButton: true,
                            confirmButtonText: 'UPDATE',
                            cancelButtonText: 'TUTUP'
                        },function(isConfirm){
                            if(isConfirm) {
                                notif({
                                    msg: "<b>Info:</b> Data sedang di PROSES, mohon di tunggu.",
                                    type: "info"
                                });
            
                                $('#btn-updateKehadiran').addClass("btn-loading");
                                $("#btn-updateKehadiran").html('Please wait...');
                                $("#btn-updateKehadiran").attr("disabled", true);
            
                                $.ajax({
                                    type:"POST",
                                    url: "{{route('hris.mdabsenhadir.download_mesin_kehadiran')}}",
                                    dataType: 'json',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                    data: {
                                        tanggal_mesin_absensi:tanggal_mesin_absensi,
                                    },
                                    dataType: 'json',
                                    success: function(res){
            
                                        if(res == "ADA") {
                                            notif({
                                                msg: "<b>Info:</b> Data berhasil di UPDATE.",
                                                type: "info"
                                            });
                                        } else {
                                            notif({
                                                msg: "<b>Info:</b> Data Tidak Ditemukan, UPDATE GAGAL.",
                                                type: "warning"
                                            });
                                        }
            
                                        $('#btn-updateKehadiran').removeClass("btn-loading");
                                        $("#btn-updateKehadiran").html('<span><i class="fa fa-download"></i></span> UPDATE ABSENSI');
                                        $("#btn-updateKehadiran").attr("disabled", false);
                                        $("#btn-caridata").click();
                                    },
                                    error: function(res){
                                        notif({
                                            msg: "<b>Error:</b> Oops data gagal di UPDATE.",
                                            type: "error"
                                        });
            
                                        $('#btn-updateKehadiran').removeClass("btn-loading");
                                        $("#btn-updateKehadiran").attr("disabled", false);
                                        $("#btn-updateKehadiran").html('<span><i class="fa fa-download"></i></span> UPDATE ABSENSI');
                                    }
                                });
                            }
                        });        
                    }

                },
                error: function(res){
                                
                }
            });           
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

            $('#btn-save').addClass("btn-loading");
            $("#btn-save").html('Please wait...');
            $("#btn-save").attr("disabled", true);
            $('#progress-show-1').show();
            $('#progress-hide-1').hide();

            $.ajax({
                type:"POST",
                url: "{{route('hris.dataabsenperijinan.create_perizinan')}}",
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

            $('#progress-show-1').hide();
            $('#progress-hide-1').show();
            $('#btn-save').removeClass("btn-loading");
            $('#ajax-absenijin-model-add').modal('hide');
            $("#btn-save").html('<span><i class="fa fa-save"></i></span> Simpan');
            $("#datatable-ajax-crud").DataTable().ajax.reload();

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

            $('#btn-save').addClass("btn-loading");
            $("#btn-save").html('Please wait...');
            $("#btn-save").attr("disabled", true);
            $('#progress-show-1').show();
            $('#progress-hide-1').hide();

            $.ajax({
                type:"POST",
                url: "{{route('hris.dataabsenperijinan.create_iks')}}",
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

            $('#progress-show-1').hide();
            $('#progress-hide-1').show();
            $('#btn-save').removeClass("btn-loading");
            $('#ajax-absenijin-model-add').modal('hide');
            $("#btn-save").html('<span><i class="fa fa-save"></i></span> Simpan');
            $("#datatable-ajax-crud").DataTable().ajax.reload();
        });

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

    </script>
    <script>
        $('.data_range').daterangepicker({
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
            // startDate: moment().startOf('month'),
            // endDate: moment().endOf('month')
            }, function(start, end) {
                $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
        })

        jQuery(document).ready(function($) {
            const BtnUpdateLintasHari = document.getElementsByClassName('BtnUpdateLintasHari')[0];
            const EmployeeID = document.getElementsByClassName("EmployeeID");

            BtnUpdateLintasHari.addEventListener('click', function(event) {
                let tmp = EmployeeID[0].value;
                
                if (tmp == ''||tmp==null) {
                    swal({
                        title: "Harap Pilih Karyawan",
                        text: "Data karyawan tidak boleh kosong",
                        icon: "warning",
                        button : false,
                    });
                } else{
                    event.preventDefault();
                    const submited =document.getElementsByTagName('form')[0];
                    swal({
                        title: 'Apakah Anda Yakin ?',
                        text: 'Update Absen Lintas Hari',
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: 'UPDATE',
                        cancelButtonText: 'TUTUP'
                    },function(isConfirm){
                        if(isConfirm) {
                            $('#BtnUpdateLintasHari').addClass("btn-loading");
                            $("#BtnUpdateLintasHari").html('Please wait...');
                            $("#BtnUpdateLintasHari").attr("disabled", true);

                            $.ajax({
                                data: $('#form_update_lintashari').serialize(),
                                url: '{{ route("hris.mdabsenhadir.download_mesin_kehadiran_lintas") }}',           
                                type: "post",
                                dataType: 'json',           
                                success: function (data) {
                                    

                                    if(data == "ADA") {
                                        notif({
                                            msg: "<b>Info:</b> Data berhasil di UPDATE.",
                                            type: "info"
                                        });
                                    } else {
                                        notif({
                                            msg: "<b>Info:</b> Data Tidak Ditemukan, UPDATE GAGAL.",
                                            type: "warning"
                                        });
                                    }

                                    $('#BtnUpdateLintasHari').removeClass("btn-loading");
                                    $("#BtnUpdateLintasHari").html('<span><i class="fa fa-download"></i></span> UPDATE ABSENSI');
                                    $("#BtnUpdateLintasHari").attr("disabled", false);
                                    $("#btn-caridata").click();
                                   
                                },
                                error: function (xhr, status, error) {
                                    notif({
                                        msg: "<b>Error:</b> Oops data gagal di UPDATE.",
                                        type: "error"
                                    });

                                    $('#BtnUpdateLintasHari').removeClass("btn-loading");
                                    $("#BtnUpdateLintasHari").attr("disabled", false);
                                    $("#BtnUpdateLintasHari").html('<span><i class="fa fa-download"></i></span> UPDATE ABSENSI');
                                }
                            }); 
                        }
                    });    
                }
            });
        });
    </script>

@endsection
