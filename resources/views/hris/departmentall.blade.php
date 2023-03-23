@extends('admin.adminlayouts.adminlayout')

@section('head')
    <!-- Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/responsivebootstrap4.min.css') }}" rel="stylesheet" />

    <!-- Select2 css -->
    <link href="{{ URL::asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />

    <!-- Accordion Css -->
	<link href="{{URL::asset('assets/plugins/accordion/accordion.css')}}" rel="stylesheet" />
@stop
@section('mainarea')
    <!-- page-header -->
    <div class="page-header">
        <ol class="breadcrumb breadcrumb-arrow mt-0">
            <li><a href="#">Master Data</a></li>
            <li class="active"><span>Department</span></li>
        </ol>
        <div class="ml-auto">
            <div class="input-group">
                <a href="{{ url('lockscreen') }}" class="btn btn-primary text-white mr-2 btn-sm" data-toggle="tooltip"
                    title="" data-placement="bottom" data-original-title="lock">
                    <span>
                        <i class="fa fa-lock"></i>
                    </span>
                </a>
            </div>
        </div>
    </div>
    <!-- End page-header -->

    <!-- row -->
    <div class="row">
        <div class="col-md-12">
            <!-- Begin Form Edit Absen Karyawan -->
            <div class="card" id="ajax-department-model-addedit">
                <!-- boostrap absen time -->
                <div class="card-body">
                    <div class="form-group">
                        <div class="table-responsive">
                            <!-- BEGIN FORM-->
                            {!! Form::open(['url' => 'javascript:void(0)', 'id' => 'formSaveChanges1', 'name' => 'formSaveChanges1']) !!}
                            <table class="table-sm display" id="ajax-department-model-addedit-display">
                                <thead>
                                    <tr>
                                        <th>
                                            <h3 class="m-0 card-title" id="ajaxEditDepartmentModel"></h3>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="datatable-absen-time-display-body">
                                    <tr>
                                        <td>
                                            <label for="name" class="col-sm-6 control-label">Site Nirwana ID :</label>
                                            <input id="site_nirwana_id_addedit" name="site_nirwana_id_addedit" type="text" class="form-control" placeholder="Site Nirwana ID" maxlength="5" size="5">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="name" class="col-sm-6 control-label">Site Nirwana Nama :</label>
                                            <input id="site_nirwana_name_addedit" name="site_nirwana_name_addedit" type="text" class="form-control" placeholder="Site Nirwana Nama" maxlength="50" size="50">            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="name" class="col-sm-6 control-label">Department ID :</label>
                                            <input id="department_id_addedit" name="department_id_addedit" type="text" class="form-control" placeholder="Department ID" maxlength="5" size="5">            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="name" class="col-sm-6 control-label">Department Nama :</label>
                                            <input id="department_name_addedit" name="department_name_addedit" type="text" class="form-control" placeholder="Site Nirwana Nama" maxlength="50" size="50">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="name" class="col-sm-6 control-label">Sub Department ID :</label>
                                            <input id="sub_dept_id_addedit" name="sub_dept_id_addedit" type="text" class="form-control" placeholder="Sub Department" maxlength="5" size="5">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="name" class="col-sm-6 control-label">Sub Department Nama :</label>
                                            <input id="sub_dept_name_addedit" name="sub_dept_name_addedit" type="text" class="form-control" placeholder="Sub Department Nama" maxlength="50" size="50">
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="border-top">
                                        <th>
                                            <div class="btn-list">
                                                <a href="#" id="btn-save-changes" class="btn btn-primary btn-sm">Save all changes</a>
                                                <a href="javascript:void(0)" id="cancel-formSaveChanges1" class="btn btn-danger btn-sm">Cancel</a>
                                            </div>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                            {{-- </form> --}}
                            {!! Form::close() !!}
                            <!-- END FORM-->
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Edit Form Absen Karyawan -->

            <!-- Begin Form Edit Absen Karyawan -->
            <div class="card" id="datatable-data-karyawan">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-ajax-crud"
                            class="table table-sm table-striped table-hover table-bordered w-100 text-nowrap display">
                            <thead>
                                <tr>
                                    <th scope="col">Site Nirwana ID</th>
                                    <th scope="col">Site Nirwana Nama</th>
                                    <th scope="col">Department ID</th>
                                    <th scope="col">Department Nama</th>
                                    <th scope="col">Sub Deptartment ID</th>
                                    <th scope="col">Sub Deptartment Nama</th>
                                    <th scope="col">Option</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- boostrap show department model -->
    <div class="modal fade" id="ajax-department-model-show" role="dialog" data-backdrop="static" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="ajaxShowDepartmentModel"></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
                <label for="name" class="col-sm-6 control-label">Site Nirwana ID :</label>
                <div class="col-sm-12" id="site_nirwana_id"></div>
            </div>                
            <div class="form-group">
                <label for="name" class="col-sm-6 control-label">Site Nirwana Nama :</label>
                <div class="col-sm-12" id="site_nirwana_name"></div>
            </div>                
            <div class="form-group">
                <label for="name" class="col-sm-6 control-label">Department ID :</label>
                <div class="col-sm-12" id="department_id"></div>
            </div>                
            <div class="form-group">
                <label for="name" class="col-sm-6 control-label">Department Nama :</label>
                <div class="col-sm-12" id="department_name"></div>
            </div>                
            <div class="form-group">
                <label for="name" class="col-sm-6 control-label">Sub Department ID :</label>
                <div class="col-sm-12" id="sub_dept_id"></div>
            </div>                
            <div class="form-group">
                <label for="name" class="col-sm-6 control-label">Sub Department Nama :</label>
                <div class="col-sm-12" id="sub_dept_name"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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

    <!---Accordion js-->
    <script src="{{URL::asset('assets/plugins/accordion/accordion.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/accordion/accordions.js')}}"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            $("#ajax-department-model-addedit").hide();
          });


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $('#datatable-ajax-crud').DataTable({
                processing: true,
                serverSide: true,
                "ajax": {
                    "url": "{{ route('hris.departmentall.ajax_departmentall') }}",
                    "dataType": "json",
                    "type": "POST",
                    "headers": {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    "dataSrc": "data"
                },
                columns: [
                    {
                        data: 'site_nirwana_id',
                        name: 'site_nirwana_id'
                    },
                    {
                        data: 'site_nirwana_name',
                        name: 'site_nirwana_name'
                    },
                    {
                        data: 'department_id',
                        name: 'department_id'
                    },
                    {
                        data: 'department_name',
                        name: 'department_name'
                    },
                    {
                        data: 'sub_dept_id',
                        name: 'sub_dept_id'
                    },
                    {
                        data: 'sub_dept_name',
                        name: 'sub_dept_name'
                    },
                    {
                        data: 'option'
                    },
                ],
                columnDefs: [
                    {
                        "orderable": false,
                        "targets": [6]
                    },
                    {
                        'visible': false,
                        'targets': []
                    }
                ]
            });
        });

        $('body').on('click', '#showData-link', function () {
            var showData = $(this).data('id');
        //alert (showData);
            // ajax
            $.ajax({
                "type":"POST",
                "url": "{{route('hris.departmentall.show_data')}}",
                "data": { showData: showData },
                "dataType": 'json',
                "headers": {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                "success": function(res){
                  $('#ajaxShowDepartmentModel').html("Show Department");
                  $('#ajax-department-model-show').modal('show');
                  $('#site_nirwana_id').text(res.site_nirwana_id);
                  $('#site_nirwana_name').text(res.site_nirwana_name);
                  $('#department_id').text(res.department_id);
                  $('#department_name').text(res.department_name);
                  $('#sub_dept_id').text(res.sub_dept_id);
                  $('#sub_dept_name').text(res.sub_dept_name);
                  //alert(res.site_nirwana_name);
               }
            });
        });

        $('body').on('click', '#addeditData-link', function () {
            var editData = $(this).data('id');
        //alert (showData);
            // ajax
            $.ajax({
                "type":"POST",
                "url": "{{route('hris.departmentall.edit_data')}}",
                "data": { editData: editData },
                "dataType": 'json',
                "headers": {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                "success": function(res){
                  $('#ajaxEditDepartmentModel').html("Edit Department");
                  $("#ajax-department-model-addedit").show("slow");

                  //$('#formSaveChanges1').trigger("reset");

                  $('#site_nirwana_id_addedit').val(res.site_nirwana_id);
                  $('#site_nirwana_name_addedit').val(res.site_nirwana_name);
                  $('#department_id_addedit').val(res.department_id);
                  $('#department_name_addedit').val(res.department_name);
                  $('#sub_dept_id_addedit').val(res.sub_dept_id);
                  $('#sub_dept_name_addedit').val(res.sub_dept_name);
                  //alert(res.site_nirwana_name);
               }
            });
        });
                
        $("#cancel-formSaveChanges1").click(function () {
            $("#ajax-department-model-addedit").hide("slow");
           });

        $('body').on('click', '#btn-save-changes', function (event) {
            var site_nirwana_id = $("#site_nirwana_id_addedit").val();
            var site_nirwana_name = $("#site_nirwana_name_addedit").val();
            var department_id = $("#department_id_addedit").val();
            var department_name = $("#department_name_addedit").val();
            var sub_dept_id = $("#sub_dept_id_addedit").val();
            var sub_dept_name = $("#sub_dept_name_addedit").val();
            $("#btn-save-change").html('Please Wait...');
            $("#btn-save-change"). attr("disabled", true);
            
            // ajax
            $.ajax({
                type:"POST",
                url: "{{route('hris.departmentall.store')}}",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    site_nirwana_id:site_nirwana_id,
                    site_nirwana_name:site_nirwana_name,
                    department_id:department_id,
                    department_name:department_name,
                    sub_dept_id:sub_dept_id,
                    sub_dept_name:sub_dept_name,
                },
                dataType: 'json',
                success: function(res){
                    $("#btn-save-change").html('Save all changes');
                    $("#btn-save-change"). attr("disabled", false);
                    alert('Simpan berhasil');
                }
            });
        });


    </script>

@endsection
