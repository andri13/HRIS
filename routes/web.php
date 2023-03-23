<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/base64', function(){
    $image = public_path('installer/img/pattern.png');;
    $img = \Image::make($image);
    return response()->make($img->encode($img->mime()), 200, array('Content-Type' => $img->mime(),'Cache-Control'=>'max-age=86400, public'));
});
# Employee Login
Route::get('/',['as'=>'front.login','uses'=>'Front\LoginController@index']);
Route::post('/login',['as'=>'login','uses'=>'Front\LoginController@ajaxLogin']);
Route::get('logout', ['as'=>'front.logout','uses'=>'Front\LoginController@logout']);

// contoh route yang bisa ini
//Route::get('logs',['as'=>'logs','uses'=>'Logs\LogsController@index']);
Route::group(['middleware' => ['auth.admin'], 'prefix' => 'logs','namespace' => 'Logs'], function()
{
    Route::get('data',['as'=>'logs.data','uses'=>'LogsController@index']);
    Route::post('ajax_logs',['as'=>'logs.ajax_logs','uses'=> 'LogsController@ajax_logs']);
});

# Employee Panel After Login
Route::group(['middleware' => ['auth.employees'],'namespace' => 'Front'], function()
{
    Route::get('/change_password_modal',['as'=>'front.change_password_modal','uses'=>'DashboardController@changePasswordModal']);
    Route::post('/change_password',['as'=>'front.change_password','uses'=>'DashboardController@change_password']);
    Route::get('ajaxApplications',['as'=>'front.leave_applications','uses'=> 'DashboardController@ajaxApplications']);

    Route::get('leave',['as'=>'front.leave','uses'=>'DashboardController@leave']);

    Route::post('dashboard/notice/{id}',['as'=>'front.notice_ajax','uses'=>'DashboardController@notice_ajax']);

    Route::post('leave_store',['as'=>'front.leave_store','uses'=>'DashboardController@leave_store']);


    Route::resource('dashboard','DashboardController');
});

# Admin Login
Route::group([ 'prefix' => 'admin','namespace' => 'Admin'], function()
{

    Route::get('/',['as'=>'admin.getlogin','uses'=>'AdminLoginController@index']);
    Route::get('logout',['as'=>'admin.logout','uses'=> 'AdminLoginController@logout']);

    Route::post('login',['as'=>'admin.login','uses'=> 'AdminLoginController@ajaxAdminLogin']);

});

Route::group(['middleware' => ['auth.admin', 'lock'], 'prefix' => 'hris','namespace' => 'Hris'], function()
{
    Route::get('dashboard/index',['as'=>'hris.dashboard.index','uses'=>'DashboardController@index']);

    //Route::resource('mdabsenhadir', 'MdAbsenHadirController',['as' => 'hris']);
    Route::get('mdabsenhadir/datahadir',['as'=>'hris.mdabsenhadir.datahadir','uses'=>'MdAbsenHadirController@datahadir']);
    Route::post('mdabsenhadir/ajax_datahadir/',['as'=>'hris.mdabsenhadir.ajax_datahadir','uses'=> 'MdAbsenHadirController@ajax_datahadir']);
    Route::post('mdabsenhadir/ajax_caridatahadir/',['as'=>'hris.mdabsenhadir.ajax_caridatahadir','uses'=> 'MdAbsenHadirController@ajax_caridatahadir']);
    Route::get('mdabsenhadir/lihatabsen',['as'=>'hris.mdabsenhadir.lihatabsen','uses'=>'MdAbsenHadirController@lihatabsen']);
    Route::post('mdabsenhadir/ajax_lihatabsen/',['as'=>'hris.mdabsenhadir.ajax_lihatabsen','uses'=> 'MdAbsenHadirController@ajax_lihatabsen']);
    Route::post('mdabsenhadir/ajax_datawaktuabsen/',['as'=>'hris.mdabsenhadir.ajax_datawaktuabsen','uses'=> 'MdAbsenHadirController@ajax_datawaktuabsen']);
    Route::post('mdabsenhadir/save_datawaktuabsen/',['as'=>'hris.mdabsenhadir.save_datawaktuabsen','uses'=> 'MdAbsenHadirController@save_datawaktuabsen']);
    Route::post('mdabsenhadir/ajax_getselectdepart/',['as'=>'hris.mdabsenhadir.ajax_getselectdepart','uses'=> 'MdAbsenHadirController@ajax_getselectdepart']);
    Route::post('mdabsenhadir/ajax_getselectsubdept/',['as'=>'hris.mdabsenhadir.ajax_getselectsubdept','uses'=> 'MdAbsenHadirController@ajax_getselectsubdept']);
    Route::post('mdabsenhadir/ajax_getselectemployee/',['as'=>'hris.mdabsenhadir.ajax_getselectemployee','uses'=> 'MdAbsenHadirController@ajax_getselectemployee']);
    Route::post('mdabsenhadir/ajax_getemployeselectposisi/',['as'=>'hris.mdabsenhadir.ajax_getemployeselectposisi','uses'=> 'MdAbsenHadirController@ajax_getemployeselectposisi']);
    Route::post('mdabsenhadir/ajax_getallemployeeatribut/',['as'=>'hris.mdabsenhadir.ajax_getallemployeeatribut','uses'=> 'MdAbsenHadirController@ajax_getallemployeeatribut']);
    Route::post('mdabsenhadir/show_kehadiran/',['as'=>'hris.mdabsenhadir.show_kehadiran','uses'=> 'MdAbsenHadirController@show_kehadiran']);
    Route::post('mdabsenhadir/ajax_exportexcel/',['as'=>'hris.mdabsenhadir.ajax_exportexcel','uses'=> 'MdAbsenHadirController@ajax_exportexcel']);
    Route::post('mdabsenhadir/print/',['as'=>'hris.mdabsenhadir.print','uses'=> 'MdAbsenHadirController@print']);
    Route::get('mdabsenhadir/laporan_harian_kehadiran/',['as'=>'hris.mdabsenhadir.laporan_harian_kehadiran','uses'=> 'MdAbsenHadirController@laporan_harian_kehadiran']);
    Route::post('mdabsenhadir/ajax_exportexceldeptall/',['as'=>'hris.mdabsenhadir.ajax_exportexceldeptall','uses'=> 'MdAbsenHadirController@ajax_exportexceldeptall']);
    Route::post('mdabsenhadir/ajax_getemployeselectdeptid/',['as'=>'hris.mdabsenhadir.ajax_getemployeselectdeptid','uses'=> 'MdAbsenHadirController@ajax_getemployeselectdeptid']);
    Route::get('mdabsenhadir/proses/',['as'=>'hris.mdabsenhadir.proses','uses'=> 'MdAbsenHadirController@proses']);
    Route::post('mdabsenhadir/ajax_proses/',['as'=>'hris.mdabsenhadir.ajax_proses','uses'=> 'MdAbsenHadirController@ajax_proses']);
    Route::post('mdabsenhadir/download_mesin_kehadiran/',['as'=>'hris.mdabsenhadir.download_mesin_kehadiran','uses'=> 'MdAbsenHadirController@download_mesin_kehadiran']);
    Route::post('mdabsenhadir/ajax_getdashkehadiran',['as'=>'hris.mdabsenhadir.ajax_getdashkehadiran','uses'=>'MdAbsenHadirController@ajax_getdashkehadiran']);
    Route::post('mdabsenhadir/ajax_getTanggalKehadiranSekarang',['as'=>'hris.mdabsenhadir.ajax_getTanggalKehadiranSekarang','uses'=>'MdAbsenHadirController@ajax_getTanggalKehadiranSekarang']);

    // DATA GAGAL ABSEN
    Route::get('gagalabsen/index/',['as'=>'hris.gagalabsen.index','uses'=> 'GagalAbsenController@index']);
    Route::post('gagalabsen/ajax_gagalabsen/',['as'=>'hris.gagalabsen.ajax_gagalabsen','uses'=> 'GagalAbsenController@ajax_gagalabsen']);
    Route::post('gagalabsen/ajax_loggagalabsen/',['as'=>'hris.gagalabsen.ajax_loggagalabsen','uses'=> 'GagalAbsenController@ajax_loggagalabsen']);
    Route::post('gagalabsen/form_gagalabsen/',['as'=>'hris.gagalabsen.form_gagalabsen','uses'=> 'GagalAbsenController@form_gagalabsen']);
    Route::post('gagalabsen/update/',['as'=>'hris.gagalabsen.update','uses'=> 'GagalAbsenController@update']);
    Route::post('logdatagagalabsen/store/',['as'=>'hris.logdatagagalabsen.store','uses'=> 'LogDataGagalAbsenController@store']);
    Route::post('gagalabsen/ajax_exportexcel/',['as'=>'hris.gagalabsen.ajax_exportexcel','uses'=> 'GagalAbsenController@ajax_exportexcel']);

    // DATA LEMBUR
    Route::get('datalembur/index/',['as'=>'hris.datalembur.index','uses'=> 'DataLemburController@index']);
    Route::post('datalembur/ajax_datahadir/',['as'=>'hris.datalembur.ajax_datahadir','uses'=> 'DataLemburController@ajax_datahadir']);
    Route::post('datalembur/form_datalembur/',['as'=>'hris.datalembur.form_datalembur','uses'=> 'DataLemburController@form_datalembur']);
    Route::post('datalembur/getEmployeeLembur/',['as'=>'hris.datalembur.getEmployeeLembur','uses'=> 'DataLemburController@getEmployeeLembur']);
    Route::post('datalembur/ajax_getNomorFormLembur/',['as'=>'hris.datalembur.ajax_getNomorFormLembur','uses'=> 'DataLemburController@ajax_getNomorFormLembur']);
    Route::get('datalembur/add_datalembur/',['as'=>'hris.datalembur.add_datalembur','uses'=> 'DataLemburController@add_datalembur']);
    Route::post('datalembur/ajax_getemployeselectdeptid/',['as'=>'hris.datalembur.ajax_getemployeselectdeptid','uses'=> 'DataLemburController@ajax_getemployeselectdeptid']);
    Route::post('datalembur/ajax_getemployeselectnfl/',['as'=>'hris.datalembur.ajax_getemployeselectnfl','uses'=> 'DataLemburController@ajax_getemployeselectnfl']);
    Route::post('datalembur/ajax_gettanggalnfl/',['as'=>'hris.datalembur.ajax_gettanggalnfl','uses'=> 'DataLemburController@ajax_gettanggalnfl']);
    Route::post('datalembur/store_multi/',['as'=>'hris.datalembur.store_multi','uses'=> 'DataLemburController@store_multi']);
    Route::post('datalembur/update/',['as'=>'hris.datalembur.update','uses'=> 'DataLemburController@update']);
    Route::post('datalembur/delete/',['as'=>'hris.datalembur.delete','uses'=> 'DataLemburController@delete']);
    Route::post('datalembur/remove/',['as'=>'hris.datalembur.remove','uses'=> 'DataLemburController@remove']);
    Route::post('datalembur/ajax_exportexcel/',['as'=>'hris.datalembur.ajax_exportexcel','uses'=> 'DataLemburController@ajax_exportexcel']);
    Route::post('datalembur/ajax_getemployee',['as'=>'hris.datalembur.ajax_getemployee','uses'=>'DataLemburController@ajax_getemployee']);
    Route::post('datalembur/ajax_getsubdept',['as'=>'hris.datalembur.ajax_getsubdept','uses'=>'DataLemburController@ajax_getsubdept']);
    Route::post('datalembur/ajax_getnomorspl',['as'=>'hris.datalembur.ajax_getnomorspl','uses'=>'DataLemburController@ajax_getnomorspl']);
    Route::post('datalembur/ajax_datalembur',['as'=>'hris.datalembur.ajax_datalembur','uses'=>'DataLemburController@ajax_datalembur']);
    Route::post('datalembur/replace',['as'=>'hris.datalembur.replace','uses'=>'DataLemburController@replace']);
    Route::post('datalembur/updatelembur',['as'=>'hris.datalembur.updatelembur','uses'=>'DataLemburController@updatelembur']);
    Route::post('datalembur/updatelemburall',['as'=>'hris.datalembur.updatelemburall','uses'=>'DataLemburController@updatelemburall']);
    Route::post('datalembur/tambahkaryawan',['as'=>'hris.datalembur.tambahkaryawan','uses'=>'DataLemburController@tambahkaryawan']);
    Route::post('datalembur/removenospl',['as'=>'hris.datalembur.removenospl','uses'=>'DataLemburController@removenospl']);

    // DATA PAYROLL
    Route::get('payroll/lembur/',['as'=>'hris.payroll.lembur','uses'=> 'PayrollController@lembur']);

    // DEPARTMENT ALL
    Route::get('departmentall/index',['as'=>'hris.departmentall.index','uses'=>'DepartmentAllController@index']);
    Route::post('departmentall/ajax_departmentall/',['as'=>'hris.departmentall.ajax_departmentall','uses'=> 'DepartmentAllController@ajax_departmentall']);
    Route::post('departmentall/store/',['as'=>'hris.departmentall.store','uses'=> 'DepartmentAllController@store']);
    Route::post('departmentall/show_data/',['as'=>'hris.departmentall.show_data','uses'=> 'DepartmentAllController@show_data']);
    Route::post('departmentall/edit_data/',['as'=>'hris.departmentall.edit_data','uses'=> 'DepartmentAllController@edit_data']);
    Route::post('departmentall/getSelectSubDept/',['as'=>'hris.departmentall.getSelectSubDept','uses'=> 'DepartmentAllController@getSelectSubDept']);

    // EMPLOYEE ATTRIBUTE
    Route::get('employeeatr/index',['as'=>'hris.employeeatr.index','uses'=>'EmployeeAtrController@index']);
    Route::post('employeeatr/ajax_getemployeeatr/',['as'=>'hris.employeeatr.ajax_getemployeeatr','uses'=> 'EmployeeAtrController@ajax_getemployeeatr']);
    Route::post('employeeatr/ajax_getempatr/',['as'=>'hris.employeeatr.ajax_getempatr','uses'=> 'EmployeeAtrController@ajax_getempatr']);
    Route::post('employeeatr/ajax_getselectdept/',['as'=>'hris.employeeatr.ajax_getselectdept','uses'=> 'EmployeeAtrController@ajax_getselectdept']);
    Route::post('employeeatr/ajax_getselectsubdept/',['as'=>'hris.employeeatr.ajax_getselectsubdept','uses'=> 'EmployeeAtrController@ajax_getselectsubdept']);
    Route::post('employeeatr/ajax_periksaenroll_id/',['as'=>'hris.employeeatr.ajax_periksaenroll_id','uses'=> 'EmployeeAtrController@ajax_periksaenroll_id']);
    Route::post('employeeatr/ajax_periksanik/',['as'=>'hris.employeeatr.ajax_periksanik','uses'=> 'EmployeeAtrController@ajax_periksanik']);
    Route::post('employeeatr/create/',['as'=>'hris.employeeatr.create','uses'=> 'EmployeeAtrController@create']);
    Route::post('employeeatr/replace/',['as'=>'hris.employeeatr.replace','uses'=> 'EmployeeAtrController@replace']);
    Route::post('employeeatr/destroy/',['as'=>'hris.employeeatr.destroy','uses'=> 'EmployeeAtrController@destroy']);
    Route::post('employeeatr/ajax_exportexcel/',['as'=>'hris.employeeatr.ajax_exportexcel','uses'=> 'EmployeeAtrController@ajax_exportexcel']);

    // REF ABSEN IJIN
    Route::get('refabsenijin/index',['as'=>'hris.refabsenijin.index','uses'=>'RefAbsenIjinController@index']);
    Route::post('refabsenijin/ajax_refabsenijin',['as'=>'hris.refabsenijin.ajax_refabsenijin','uses'=>'RefAbsenIjinController@ajax_refabsenijin']);
    //Route::post('refabsenijin/index',['as'=>'hris.refabsenijin.ajax_refabsenijin','uses'=>'RefAbsenIjinController@ajax_refabsenijin']);
    Route::post('refabsenijin/replace',['as'=>'hris.refabsenijin.replace','uses'=>'RefAbsenIjinController@replace']);
    Route::post('refabsenijin/destroy',['as'=>'hris.refabsenijin.destroy','uses'=>'RefAbsenIjinController@destroy']);
    //Route::post('refabsenijin/ajax_getmodalabsenijin/',['as'=>'hris.refabsenijin.ajax_getmodalabsenijin','uses'=> 'RefAbsenIjinController@ajax_getmodalabsenijin']);

    // REF ABSEN IJIN
    Route::get('refharilibur/index',['as'=>'hris.refharilibur.index','uses'=>'RefHariLiburController@index']);
    Route::post('refharilibur/ajax_refharilibur',['as'=>'hris.refharilibur.ajax_refharilibur','uses'=>'RefHariLiburController@ajax_refharilibur']);
    Route::post('refharilibur/replace',['as'=>'hris.refharilibur.replace','uses'=>'RefHariLiburController@replace']);
    Route::post('refharilibur/destroy',['as'=>'hris.refharilibur.destroy','uses'=>'RefHariLiburController@destroy']);

    Route::get('datajadwalkerjalog/index',['as'=>'hris.datajadwalkerjalog.index','uses'=>'DataJadwalKerjaLogController@index']);
    Route::post('datajadwalkerjalog/ajax_datajadwalkerjalog',['as'=>'hris.datajadwalkerjalog.ajax_datajadwalkerjalog','uses'=>'DataJadwalKerjaLogController@ajax_datajadwalkerjalog']);
    Route::post('datajadwalkerjalog/ajax_datahadir',['as'=>'hris.datajadwalkerjalog.ajax_datahadir','uses'=>'DataJadwalKerjaLogController@ajax_datahadir']);
    Route::post('datajadwalkerjalog/replace',['as'=>'hris.datajadwalkerjalog.replace','uses'=>'DataJadwalKerjaLogController@replace']);
    Route::post('datajadwalkerjalog/process',['as'=>'hris.datajadwalkerjalog.process','uses'=>'DataJadwalKerjaLogController@process']);
    Route::post('datajadwalkerjalog/ajax_getemployeselectdeptid/',['as'=>'hris.datajadwalkerjalog.ajax_getemployeselectdeptid','uses'=> 'DataJadwalKerjaLogController@ajax_getemployeselectdeptid']);
    Route::post('datajadwalkerjalog/ajax_getallemployeeatribut/',['as'=>'hris.datajadwalkerjalog.ajax_getallemployeeatribut','uses'=> 'DataJadwalKerjaLogController@ajax_getallemployeeatribut']);
    Route::post('datajadwalkerjalog/getEmployeeKehadiran/',['as'=>'hris.datajadwalkerjalog.getEmployeeKehadiran','uses'=> 'DataJadwalKerjaLogController@getEmployeeKehadiran']);

    Route::get('dataabsenperijinan/index',['as'=>'hris.dataabsenperijinan.index','uses'=>'DataAbsenPerijinanController@index']);
    Route::post('dataabsenperijinan/ajax_dataabsenperizinan',['as'=>'hris.dataabsenperijinan.ajax_dataabsenperizinan','uses'=>'DataAbsenPerijinanController@ajax_dataabsenperizinan']);
    Route::post('dataabsenperijinan/ajax_getkehadiran/',['as'=>'hris.dataabsenperijinan.ajax_getkehadiran','uses'=> 'DataAbsenPerijinanController@ajax_getkehadiran']);
    Route::post('dataabsenperijinan/create_perizinan/',['as'=>'hris.dataabsenperijinan.create_perizinan','uses'=> 'DataAbsenPerijinanController@create_perizinan']);
    Route::post('dataabsenperijinan/create_iks/',['as'=>'hris.dataabsenperijinan.create_iks','uses'=> 'DataAbsenPerijinanController@create_iks']);
    Route::post('dataabsenperijinan/create_perizinan_menu/',['as'=>'hris.dataabsenperijinan.create_perizinan_menu','uses'=> 'DataAbsenPerijinanController@create_perizinan_menu']);
    Route::post('dataabsenperijinan/create_iks_menu/',['as'=>'hris.dataabsenperijinan.create_iks_menu','uses'=> 'DataAbsenPerijinanController@create_iks_menu']);
    Route::post('dataabsenperijinan/cekperizinan/',['as'=>'hris.dataabsenperijinan.cekperizinan','uses'=> 'DataAbsenPerijinanController@cekperizinan']);
    Route::post('dataabsenperijinan/cekiks/',['as'=>'hris.dataabsenperijinan.cekiks','uses'=> 'DataAbsenPerijinanController@cekiks']);
    Route::post('dataabsenperijinan/destroy',['as'=>'hris.dataabsenperijinan.destroy','uses'=>'DataAbsenPerijinanController@destroy']);
    Route::post('dataabsenperijinan/ajax_exportexcel/',['as'=>'hris.dataabsenperijinan.ajax_exportexcel','uses'=> 'DataAbsenPerijinanController@ajax_exportexcel']);
    Route::post('dataabsenperijinan/ajax_exportexcel2/',['as'=>'hris.dataabsenperijinan.ajax_exportexcel2','uses'=> 'DataAbsenPerijinanController@ajax_exportexcel2']);

    Route::get('koreksiupah/index',['as'=>'hris.koreksiupah.index','uses'=>'KoreksiUpahController@index']);
    Route::post('koreksiupah/ajax_datakoreksiupah',['as'=>'hris.koreksiupah.ajax_datakoreksiupah','uses'=>'KoreksiUpahController@ajax_datakoreksiupah']);
    Route::post('koreksiupah/create',['as'=>'hris.koreksiupah.create','uses'=>'KoreksiUpahController@create']);
    Route::post('koreksiupah/update',['as'=>'hris.koreksiupah.update','uses'=>'KoreksiUpahController@update']);
    Route::post('koreksiupah/destroy',['as'=>'hris.koreksiupah.destroy','uses'=>'KoreksiUpahController@destroy']);

    Route::get('koreksipotongan/index',['as'=>'hris.koreksipotongan.index','uses'=>'KoreksiPotonganController@index']);
    Route::post('koreksipotongan/ajax_datakoreksipotongan',['as'=>'hris.koreksipotongan.ajax_datakoreksipotongan','uses'=>'KoreksiPotonganController@ajax_datakoreksipotongan']);
    Route::post('koreksipotongan/create',['as'=>'hris.koreksipotongan.create','uses'=>'KoreksiPotonganController@create']);
    Route::post('koreksipotongan/update',['as'=>'hris.koreksipotongan.update','uses'=>'KoreksiPotonganController@update']);
    Route::post('koreksipotongan/destroy',['as'=>'hris.koreksipotongan.destroy','uses'=>'KoreksiPotonganController@destroy']);

    Route::get('bgprocess/index',['as'=>'hris.bgprocess.index','uses'=>'BgProcessController@index']);
    Route::post('bgprocess/ajax_bgprocess',['as'=>'hris.bgprocess.ajax_bgprocess','uses'=>'BgProcessController@ajax_bgprocess']);
    Route::post('bgprocess/create',['as'=>'hris.bgprocess.create','uses'=>'BgProcessController@create']);
    Route::post('bgprocess/update',['as'=>'hris.bgprocess.update','uses'=>'BgProcessController@update']);
    Route::post('bgprocess/destroy',['as'=>'hris.bgprocess.destroy','uses'=>'BgProcessController@destroy']);

    Route::get('rekapperhitunganlembur/index',['as'=>'hris.rekapperhitunganlembur.index','uses'=>'RekapPerhitunganLemburController@index']);
    Route::post('rekapperhitunganlembur/ajax_rekap',['as'=>'hris.rekapperhitunganlembur.ajax_rekap','uses'=>'RekapPerhitunganLemburController@ajax_rekap']);
    Route::post('rekapperhitunganlembur/ajax_exportexcel/',['as'=>'hris.rekapperhitunganlembur.ajax_exportexcel','uses'=> 'RekapPerhitunganLemburController@ajax_exportexcel']);

    Route::get('rekapkehadirankaryawan/index',['as'=>'hris.rekapkehadirankaryawan.index','uses'=>'RekapKehadiranKaryawanController@index']);
    Route::post('rekapkehadirankaryawan/ajax_rekap',['as'=>'hris.rekapkehadirankaryawan.ajax_rekap','uses'=>'RekapKehadiranKaryawanController@ajax_rekap']);
    Route::post('rekapkehadirankaryawan/ajax_exportexcel/',['as'=>'hris.rekapkehadirankaryawan.ajax_exportexcel','uses'=> 'RekapKehadiranKaryawanController@ajax_exportexcel']);
    Route::post('rekapkehadirankaryawan/ajax_getemployeselectstaff/',['as'=>'hris.rekapkehadirankaryawan.ajax_getemployeselectstaff','uses'=> 'RekapKehadiranKaryawanController@ajax_getemployeselectstaff']);
    Route::post('rekapkehadirankaryawan/ajax_getallemployeeatribut/',['as'=>'hris.rekapkehadirankaryawan.ajax_getallemployeeatribut','uses'=> 'RekapKehadiranKaryawanController@ajax_getallemployeeatribut']);

    Route::get('employeebpjs/index',['as'=>'hris.employeebpjs.index','uses'=>'EmployeeBpjsController@index']);
    Route::post('employeebpjs/ajax_empbpjs',['as'=>'hris.employeebpjs.ajax_empbpjs','uses'=>'EmployeeBpjsController@ajax_empbpjs']);
    Route::post('employeebpjs/ajax_exportexcel/',['as'=>'hris.employeebpjs.ajax_exportexcel','uses'=> 'EmployeeBpjsController@ajax_exportexcel']);
    Route::post('employeebpjs/ajax_getemployeselectstaff/',['as'=>'hris.employeebpjs.ajax_getemployeselectstaff','uses'=> 'EmployeeBpjsController@ajax_getemployeselectstaff']);
    Route::post('employeebpjs/ajax_getallemployeeatribut/',['as'=>'hris.employeebpjs.ajax_getallemployeeatribut','uses'=> 'EmployeeBpjsController@ajax_getallemployeeatribut']);
    Route::post('employeebpjs/getempbpjsbyid/',['as'=>'hris.employeebpjs.getempbpjsbyid','uses'=> 'EmployeeBpjsController@getempbpjsbyid']);
    Route::post('employeebpjs/update_bpjs/',['as'=>'hris.employeebpjs.update_bpjs','uses'=> 'EmployeeBpjsController@update_bpjs']);
    Route::post('employeebpjs/ajax_bpjssetting/',['as'=>'hris.employeebpjs.ajax_bpjssetting','uses'=> 'EmployeeBpjsController@ajax_bpjssetting']);
    Route::post('employeebpjs/update/',['as'=>'hris.employeebpjs.update','uses'=> 'EmployeeBpjsController@update']);

    Route::get('dasarpotbpjs/index',['as'=>'hris.dasarpotbpjs.index','uses'=>'DasarPotBpjsController@index']);
    Route::post('dasarpotbpjs/ajax_data',['as'=>'hris.dasarpotbpjs.ajax_data','uses'=>'DasarPotBpjsController@ajax_data']);
    Route::post('dasarpotbpjs/replace',['as'=>'hris.dasarpotbpjs.replace','uses'=>'DasarPotBpjsController@replace']);
    Route::post('dasarpotbpjs/destroy',['as'=>'hris.dasarpotbpjs.destroy','uses'=>'DasarPotBpjsController@destroy']);

    Route::get('bpjssetting/index',['as'=>'hris.bpjssetting.index','uses'=>'BpjsSettingController@index']);
    Route::post('bpjssetting/ajax_data',['as'=>'hris.bpjssetting.ajax_data','uses'=>'BpjsSettingController@ajax_data']);
    Route::post('bpjssetting/replace',['as'=>'hris.bpjssetting.replace','uses'=>'BpjsSettingController@replace']);
    Route::post('bpjssetting/destroy',['as'=>'hris.bpjssetting.destroy','uses'=>'BpjsSettingController@destroy']);

    Route::get('gradingsalary/index',['as'=>'hris.gradingsalary.index','uses'=>'GradingSalaryController@index']);
    Route::post('gradingsalary/ajax_data',['as'=>'hris.gradingsalary.ajax_data','uses'=>'GradingSalaryController@ajax_data']);
    Route::post('gradingsalary/replace',['as'=>'hris.gradingsalary.replace','uses'=>'GradingSalaryController@replace']);
    Route::post('gradingsalary/destroy',['as'=>'hris.gradingsalary.destroy','uses'=>'GradingSalaryController@destroy']);

    Route::get('employeegrading/index',['as'=>'hris.employeegrading.index','uses'=>'EmployeeGradingController@index']);
    Route::post('employeegrading/ajax_data',['as'=>'hris.employeegrading.ajax_data','uses'=>'EmployeeGradingController@ajax_data']);
    Route::post('employeegrading/replace',['as'=>'hris.employeegrading.replace','uses'=>'EmployeeGradingController@replace']);
    Route::post('employeegrading/destroy',['as'=>'hris.employeegrading.destroy','uses'=>'EmployeeGradingController@destroy']);
    Route::post('employeegrading/update_grading',['as'=>'hris.employeegrading.update_grading','uses'=>'EmployeeGradingController@update_grading']);

    Route::get('tunjangankaryawan/index',['as'=>'hris.tunjangankaryawan.index','uses'=>'TunjanganKaryawanController@index']);
    Route::post('tunjangankaryawan/ajax_data',['as'=>'hris.tunjangankaryawan.ajax_data','uses'=>'TunjanganKaryawanController@ajax_data']);
    Route::post('tunjangankaryawan/ajax_getemployee',['as'=>'hris.tunjangankaryawan.ajax_getemployee','uses'=>'TunjanganKaryawanController@ajax_getemployee']);
    Route::post('tunjangankaryawan/ajax_getnamatunjangan',['as'=>'hris.tunjangankaryawan.ajax_getnamatunjangan','uses'=>'TunjanganKaryawanController@ajax_getnamatunjangan']);
    Route::post('tunjangankaryawan/replace',['as'=>'hris.tunjangankaryawan.replace','uses'=>'TunjanganKaryawanController@replace']);
    Route::post('tunjangankaryawan/destroy',['as'=>'hris.tunjangankaryawan.destroy','uses'=>'TunjanganKaryawanController@destroy']);
    Route::post('tunjangankaryawan/edit',['as'=>'hris.tunjangankaryawan.edit','uses'=>'TunjanganKaryawanController@edit']);

    Route::get('rekapperhitungandtpc/index',['as'=>'hris.rekapperhitungandtpc.index','uses'=>'RekapPerhitunganDtpcController@index']);
    Route::post('rekapperhitungandtpc/ajax_rekap',['as'=>'hris.rekapperhitungandtpc.ajax_rekap','uses'=>'RekapPerhitunganDtpcController@ajax_rekap']);
    Route::post('rekapperhitungandtpc/ajax_exportexcel/',['as'=>'hris.rekapperhitungandtpc.ajax_exportexcel','uses'=> 'RekapPerhitunganDtpcController@ajax_exportexcel']);

    Route::get('rekapperhitunganiks/index',['as'=>'hris.rekapperhitunganiks.index','uses'=>'RekapPerhitunganIksController@index']);
    Route::post('rekapperhitunganiks/ajax_rekap',['as'=>'hris.rekapperhitunganiks.ajax_rekap','uses'=>'RekapPerhitunganIksController@ajax_rekap']);
    Route::post('rekapperhitunganiks/ajax_exportexcel/',['as'=>'hris.rekapperhitunganiks.ajax_exportexcel','uses'=> 'RekapPerhitunganIksController@ajax_exportexcel']);

    Route::get('rekapperhitunganpayroll/index',['as'=>'hris.rekapperhitunganpayroll.index','uses'=>'RekapPerhitunganPayrollController@index']);
    Route::post('rekapperhitunganpayroll/ajax_exportexcel/',['as'=>'hris.rekapperhitunganpayroll.ajax_exportexcel','uses'=> 'RekapPerhitunganPayrollController@ajax_exportexcel']);

    Route::get('rekapperhitunganpayrollcutoff/index',['as'=>'hris.rekapperhitunganpayrollcutoff.index','uses'=>'RekapPerhitunganPayrollCutOffController@index']);
    Route::post('rekapperhitunganpayrollcutoff/ajax_exportexcel/',['as'=>'hris.rekapperhitunganpayrollcutoff.ajax_exportexcel','uses'=> 'RekapPerhitunganPayrollCutOffController@ajax_exportexcel']);
    Route::post('rekapperhitunganpayrollcutoff/ajax_prosescutoff/',['as'=>'hris.rekapperhitunganpayrollcutoff.ajax_prosescutoff','uses'=> 'RekapPerhitunganPayrollCutOffController@ajax_prosescutoff']);

    Route::get('dataclosingpayroll/index',['as'=>'hris.dataclosingpayroll.index','uses'=>'DataClosingPayrollController@index']);
    Route::post('dataclosingpayroll/ajax_data',['as'=>'hris.dataclosingpayroll.ajax_data','uses'=>'DataClosingPayrollController@ajax_data']);
    Route::post('dataclosingpayroll/ajax_getclosing/',['as'=>'hris.dataclosingpayroll.ajax_getclosing','uses'=> 'DataClosingPayrollController@ajax_getclosing']);
    Route::post('dataclosingpayroll/create/',['as'=>'hris.dataclosingpayroll.create','uses'=> 'DataClosingPayrollController@create']);
    Route::post('dataclosingpayroll/update/',['as'=>'hris.dataclosingpayroll.update','uses'=> 'DataClosingPayrollController@update']);
    Route::post('dataclosingpayroll/destroy/',['as'=>'hris.dataclosingpayroll.destroy','uses'=> 'DataClosingPayrollController@destroy']);

    include "routechunks/new.php";
});

// Admin Panel After Login
Route::group(['middleware' => ['auth.admin', 'lock'], 'prefix' => 'admin','namespace' => 'Admin'], function()
{

    //	Dashboard Routing
    //Route::resource('dashboard', 'AdminDashboardController');
    Route::resource('dashboard', 'AdminDashboardController',['as' => 'admin']);

    //   Admin user Routing
    Route::get('admin/index',['as'=>'admin.admin.index','uses'=>'AdminController@index']);
    Route::post('admin/ajaxAdmin/',['as'=>'admin.admin.ajaxAdmin','uses'=> 'AdminController@ajaxAdmin']);
    Route::post('admin/show_data/',['as'=>'admin.admin.show_data','uses'=> 'AdminController@show_data']);
    Route::post('admin/edit_data/',['as'=>'admin.admin.edit_data','uses'=> 'AdminController@edit_data']);
    Route::post('admin/store/',['as'=>'admin.admin.store','uses'=> 'AdminController@store']);
    Route::post('admin/update/',['as'=>'admin.admin.update','uses'=> 'AdminController@update']);
    Route::post('admin/destroy/',['as'=>'admin.admin.destroy','uses'=> 'AdminController@destroy']);
    Route::post('admin/keepalive/',['as'=>'admin.admin.keepalive','uses'=> 'AdminController@keepalive']);
    Route::get('admin/editprofile/',['as'=>'admin.admin.editprofile','uses'=> 'AdminController@editprofile']);
    Route::post('admin/ajax_resetpwd/',['as'=>'admin.admin.ajax_resetpwd','uses'=> 'AdminController@ajax_resetpwd']);

    Route::get('datakehadiraninoutedited/abseninout/',['as'=>'admin.datakehadiraninoutedited.abseninout','uses'=> 'DataKehadiranInOutEditedController@abseninout']);
    Route::post('datakehadiraninoutedited/ajax_abseninout/',['as'=>'admin.datakehadiraninoutedited.ajax_abseninout','uses'=> 'DataKehadiranInOutEditedController@ajax_abseninout']);
    Route::post('datakehadiraninoutedited/ajax_abseninout_edited/',['as'=>'admin.datakehadiraninoutedited.ajax_abseninout_edited','uses'=> 'DataKehadiranInOutEditedController@ajax_abseninout_edited']);
    Route::post('datakehadiraninoutedited/form_kehadiraninout_edited/',['as'=>'admin.datakehadiraninoutedited.form_kehadiraninout_edited','uses'=> 'DataKehadiranInOutEditedController@form_kehadiraninout_edited']);
    Route::post('datakehadiraninoutedited/store/',['as'=>'admin.datakehadiraninoutedited.store','uses'=> 'DataKehadiranInOutEditedController@store']);



});
Event::listen('auth.login', function($user)
{
    $user->last_login = new DateTime;
    $user->save();
});
// Lock Screen Routing
Route::get('screenlock', 'Admin\AdminController@screenlock');


