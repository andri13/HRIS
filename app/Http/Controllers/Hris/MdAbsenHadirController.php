<?php

namespace App\Http\Controllers\Hris;

use App\Http\Controllers\AdminBaseController;
use App\Models\MasterDataAbsenKehadiran;
use App\Models\EmployeeAtribut;
use App\Models\DepartmentAll;
use App\Models\RefAbsenIjin;
use App\Models\CheckInOut;
use App\Models\AttCheckInOut;
use App\Models\AttUserInfo;
use App\Models\WorkTimeTable;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Datatables;
use App\Exports\DepartmentAllExport;
use App\Exports\MasterDataAbsenKehadiranExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Class MdAbsenHadirController
 * @package App\Http\Controllers\Hris
 */
class MdAbsenHadirController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->dashboardActive = 'active';
        $this->pageTitle = 'Data Kehadiran Karyawan';
    }

    public function index()
    {

        //return View::make('admin/dashboard', $this->data);
    }

    public function datahadir()
    {               
        $DepartmentAllModel = new DepartmentAll();
        $this->department =  $DepartmentAllModel->getAllDepartment();

        $this->selectemployee = $this->ajax_getallemployeeatribut();
        $this->refabsenijin = $this->ajax_getselectrefabsenijin();
        return View::make('hris/datahadir', $this->data);
    }

    public function print()
    {
        $selectemployee = $this->ajax_getallemployeeatribut();
        echo json_encode($selectemployee);
    }

    public function laporan_harian_kehadiran()
    {
        $this->department = $this->ajax_getselectdepart();
        return view('hris/laporan_harian_kehadiran', $this->data);
    }

    public function lihatabsen()
    {

        return View::make('hris/lihatabsen', $this->data);
    }

    public function ajax_datahadir(Request $request)
    {
        $selectDepartment = $request->selectDepartment;
        $selectBagian = $request->selectBagian;
        $daterange1 = $request->daterange1;
        $status_staff = $request->status_staff;
        $searchData = strtoupper($request->searchData);

        $daterange1 = explode(" s/d ", $request->daterange1);
        $tanggalMulai = date('Y-m-d', strtotime($daterange1[0]));
        $tanggalSampai = date('Y-m-d', strtotime($daterange1[1]));

        $filterStaff = "";
        if($status_staff) {
            $filterStaff = " AND employee_atribut.status_staff = '" . $status_staff . "'";
        }

        $inDepartment = "";
        if($selectDepartment) {
            $inDepartment = ' AND employee_atribut.department_id = "' . $selectDepartment . '"';

        }

        $inBagian = "";
        if($selectBagian) {
            $inBagian = ' AND employee_atribut.sub_dept_id = "' . $selectBagian . '"';

        }

        $inSearchData = "";
        if($searchData) {
            $inSearchData = '
                AND (
                    UPPER(master_data_absen_kehadiran.enroll_id) LIKE ("%' . $searchData . '%")
                    OR UPPER(master_data_absen_kehadiran.nik) LIKE ("%' . $searchData . '%")
                    OR UPPER(master_data_absen_kehadiran.employee_name) LIKE ("%' . $searchData . '%")
                )
            ';

        }

        if(request()->ajax()) {

            $limit = $request->input('length');
            $start = $request->input('start');

            if(empty($department_id)) {
                $totalData = MasterDataAbsenKehadiran::
                whereRaw('
                    substr(master_data_absen_kehadiran.tanggal_berjalan, 1, 10) between "' . $tanggalMulai . '" and "' . $tanggalSampai . '"
                    ' . $filterStaff . ' ' . $inDepartment . ' ' . $inBagian . ' ' . $inSearchData . '
                ')
                ->leftJoin('employee_atribut','master_data_absen_kehadiran.enroll_id','=','employee_atribut.enroll_id')
                ->leftJoin('department_all','employee_atribut.sub_dept_id','=','department_all.sub_dept_id')
                ->count();
                $totalFiltered = $totalData;

                $query = MasterDataAbsenKehadiran::
                selectRaw('
                    master_data_absen_kehadiran.uuid,
                    employee_atribut.employee_id,
                    employee_atribut.nik,
                    master_data_absen_kehadiran.enroll_id,
                    employee_atribut.employee_name,
                    master_data_absen_kehadiran.tanggal_berjalan,
                    master_data_absen_kehadiran.kode_hari,
                    master_data_absen_kehadiran.nama_hari,
                    master_data_absen_kehadiran.mulai_jam_kerja,
                    master_data_absen_kehadiran.akhir_jam_kerja,
                    master_data_absen_kehadiran.absen_masuk_kerja,
                    master_data_absen_kehadiran.absen_pulang_kerja,
                    master_data_absen_kehadiran.absen_dt_datang_terlambat,
                    master_data_absen_kehadiran.absen_dtpc_datang_terlambat_pulang_cepat,
                    master_data_absen_kehadiran.jumlah_jam_kerja,
                    master_data_absen_kehadiran.status_absen,
                    master_data_absen_kehadiran.nama_absen_ijin,
                    master_data_absen_kehadiran.kode_ijin_payroll,
                    master_data_absen_kehadiran.absen_alasan,
                    master_data_absen_kehadiran.nomor_form_perubahan_absen,
                    master_data_absen_kehadiran.nomor_absen_ijin,
                    master_data_absen_kehadiran.nomor_form_lembur,
                    master_data_absen_kehadiran.tanggal_mulai_ijin,
                    master_data_absen_kehadiran.tanggal_akhir_ijin,
                    master_data_absen_kehadiran.permits_dari_pukul,
                    master_data_absen_kehadiran.permits_sampai_pukul,
                    master_data_absen_kehadiran.total_menit_permits,
                    master_data_absen_kehadiran.updated_absen_ijin,
                    master_data_absen_kehadiran.operator,
                    master_data_absen_kehadiran.holiday_name,
                    master_data_absen_kehadiran.catatan_hrd,
                    department_all.site_nirwana_name,
                    department_all.department_name,
                    department_all.sub_dept_name,
                    master_data_absen_kehadiran.tanggal_absen,
                    master_data_absen_kehadiran.jumlah_menit_absen_dt,
                    master_data_absen_kehadiran.jumlah_menit_absen_pc,
                    master_data_absen_kehadiran.jumlah_menit_absen_dtpc,
                    master_data_absen_kehadiran.jumlah_absen_menit_kerja,
                    employee_atribut.status_staff,
                    employee_atribut.status_aktif,
                    employee_atribut.status_jabatan,
                    employee_atribut.status_staff,
                    master_data_absen_kehadiran.work_status,
                    master_data_absen_kehadiran.employee_status,
                    master_data_absen_kehadiran.mulai_jam_lembur,
                    master_data_absen_kehadiran.akhir_jam_lembur,
                    master_data_absen_kehadiran.jumlah_jam_lembur,
                    master_data_absen_kehadiran.created_at,
                    master_data_absen_kehadiran.updated_at
                ')
                ->whereRaw('
                    substr(master_data_absen_kehadiran.tanggal_berjalan, 1, 10) between "' . $tanggalMulai . '" and "' . $tanggalSampai . '"
                    ' . $filterStaff . ' ' . $inDepartment . ' ' . $inBagian . ' ' . $inSearchData . '
                ')
                ->leftJoin('employee_atribut','master_data_absen_kehadiran.enroll_id','=','employee_atribut.enroll_id')
                ->leftJoin('department_all','employee_atribut.sub_dept_id','=','department_all.sub_dept_id')
                ->offset($start)
                ->limit($limit)
                ->orderBy('master_data_absen_kehadiran.tanggal_berjalan','asc')
                ->orderBy('employee_atribut.employee_name','asc')
                ->get();
            } else {
                $totalData = MasterDataAbsenKehadiran::
                whereRaw('
                    substr(tanggal_berjalan, 1, 10) between "' . $tanggalMulai . '" and "' . $tanggalSampai . '"
                    ' . $filterStaff . ' ' . $inDepartment . ' ' . $inBagian . ' ' . $inSearchData . '
                ')
                ->leftJoin('employee_atribut','master_data_absen_kehadiran.enroll_id','=','employee_atribut.enroll_id')
                ->leftJoin('department_all','employee_atribut.sub_dept_id','=','department_all.sub_dept_id')
                ->count();
                $totalFiltered = $totalData;

                $query =  MasterDataAbsenKehadiran::
                selectRaw('
                    master_data_absen_kehadiran.uuid,
                    employee_atribut.employee_id,
                    employee_atribut.nik,
                    master_data_absen_kehadiran.enroll_id,
                    employee_atribut.employee_name,
                    master_data_absen_kehadiran.tanggal_berjalan,
                    master_data_absen_kehadiran.kode_hari,
                    master_data_absen_kehadiran.nama_hari,
                    master_data_absen_kehadiran.mulai_jam_kerja,
                    master_data_absen_kehadiran.akhir_jam_kerja,
                    master_data_absen_kehadiran.absen_masuk_kerja,
                    master_data_absen_kehadiran.absen_pulang_kerja,
                    master_data_absen_kehadiran.absen_dt_datang_terlambat,
                    master_data_absen_kehadiran.absen_dtpc_datang_terlambat_pulang_cepat,
                    master_data_absen_kehadiran.jumlah_jam_kerja,
                    master_data_absen_kehadiran.status_absen,
                    master_data_absen_kehadiran.nama_absen_ijin,
                    master_data_absen_kehadiran.kode_ijin_payroll,
                    master_data_absen_kehadiran.absen_alasan,
                    master_data_absen_kehadiran.nomor_form_perubahan_absen,
                    master_data_absen_kehadiran.nomor_absen_ijin,
                    master_data_absen_kehadiran.nomor_form_lembur,
                    master_data_absen_kehadiran.tanggal_mulai_ijin,
                    master_data_absen_kehadiran.tanggal_akhir_ijin,
                    master_data_absen_kehadiran.permits_dari_pukul,
                    master_data_absen_kehadiran.permits_sampai_pukul,
                    master_data_absen_kehadiran.total_menit_permits,
                    master_data_absen_kehadiran.updated_absen_ijin,
                    master_data_absen_kehadiran.operator,
                    master_data_absen_kehadiran.holiday_name,
                    master_data_absen_kehadiran.catatan_hrd,
                    department_all.site_nirwana_name,
                    department_all.department_name,
                    department_all.sub_dept_name,
                    master_data_absen_kehadiran.tanggal_absen,
                    master_data_absen_kehadiran.jumlah_menit_absen_dt,
                    master_data_absen_kehadiran.jumlah_menit_absen_pc,
                    master_data_absen_kehadiran.jumlah_menit_absen_dtpc,
                    master_data_absen_kehadiran.jumlah_absen_menit_kerja,
                    employee_atribut.status_staff,
                    employee_atribut.status_aktif,
                    employee_atribut.status_jabatan,
                    employee_atribut.status_staff,
                    master_data_absen_kehadiran.work_status,
                    master_data_absen_kehadiran.employee_status,
                    master_data_absen_kehadiran.mulai_jam_lembur,
                    master_data_absen_kehadiran.akhir_jam_lembur,
                    master_data_absen_kehadiran.jumlah_jam_lembur,
                    master_data_absen_kehadiran.created_at,
                    master_data_absen_kehadiran.updated_at
                ')
                ->whereRaw('
                    (substr(master_data_absen_kehadiran.tanggal_berjalan, 1, 10) between "' . $tanggalMulai . '" and "' . $tanggalSampai . '")
                    ' . $filterStaff . ' ' . $inDepartment . ' ' . $inBagian . ' ' . $inSearchData . '
                ')
                ->leftJoin('employee_atribut','master_data_absen_kehadiran.enroll_id','=','employee_atribut.enroll_id')
                ->leftJoin('department_all','employee_atribut.sub_dept_id','=','department_all.sub_dept_id')
                ->offset($start)
                ->limit($limit)
                ->orderBy('master_data_absen_kehadiran.tanggal_berjalan','asc')
                ->orderBy('employee_atribut.employee_name','asc')
                ->get();

            }

            $data = array();
            if(!empty($query))
            {
                foreach ($query as $q)
                {
                    $tanggal_absen = $q->tanggal_absen;
                    $kode_hari = $q->kode_hari;
                    $liburnasional = $q->holiday_name;
                    $status_absen=$q->status_absen;
                    $absen_pulang_kerja=$q->absen_pulang_kerja;
                    $absen_masuk_kerja=$q->absen_masuk_kerja;

                    $kerjalibur = "KERJA";
                    if($tanggal_absen <> "") {
                        $kerjalibur = "KERJA";
                        switch ($kode_hari) {
                            case '5':
                                $kerjalibur = "LIBUR";
                                break;
                            case '6':
                                $kerjalibur = "LIBUR";
                                break;
                        }
                    } else if($tanggal_absen <> null) {
                        $kerjalibur = "KERJA";
                        switch ($kode_hari) {
                            case '5':
                                $kerjalibur = "LIBUR";
                                break;
                            case '6':
                                $kerjalibur = "LIBUR";
                                break;
                        }
                    } else {
                        switch ($kode_hari) {
                            case '5':
                                $kerjalibur = "LIBUR";
                                break;
                            case '6':
                                $kerjalibur = "LIBUR";
                                break;
                        }
                    }

                    if($liburnasional <> "") {
                        $kerjalibur = "LIBUR";
                    }
                    if(($status_absen == "LN" || $status_absen == "LP" ) &&($absen_pulang_kerja==null) && ($absen_masuk_kerja==null) ) {
                        $kerjalibur = "LIBUR";
                    }
                    $nestedData['uuid'] = $q->uuid;
                    $nestedData['employee_id'] = $q->employee_id;
                    $nestedData['nik'] = $q->nik;
                    $nestedData['enroll_id'] = $q->enroll_id;
                    $nestedData['employee_name'] = $q->employee_name;
                    $nestedData['tanggal_berjalan'] = $q->tanggal_berjalan;
                    $nestedData['kode_hari'] = $q->kode_hari;
                    $nestedData['nama_hari'] = $q->nama_hari;
                    $nestedData['kerjalibur'] = $kerjalibur;
                    $nestedData['mulai_jam_kerja'] = substr($q->mulai_jam_kerja, 0, 5);
                    $nestedData['akhir_jam_kerja'] = substr($q->akhir_jam_kerja, 0, 5);
                    $nestedData['absen_masuk_kerja'] = substr($q->absen_masuk_kerja, 0, 5);
                    $nestedData['absen_pulang_kerja'] = substr($q->absen_pulang_kerja, 0, 5);
                    $nestedData['absen_dt_datang_terlambat'] = $q->absen_dt_datang_terlambat;
                    $nestedData['absen_dtpc_datang_terlambat_pulang_cepat'] = $q->absen_dtpc_datang_terlambat_pulang_cepat;
                    $nestedData['jumlah_jam_kerja'] = $q->jumlah_jam_kerja;
                    $nestedData['status_absen'] = strtoupper($q->status_absen);
                    $nestedData['nama_absen_ijin'] = strtoupper($q->nama_absen_ijin);
                    $nestedData['kode_ijin_payroll'] = strtoupper($q->kode_ijin_payroll);
                    $nestedData['absen_alasan'] = $q->absen_alasan;
                    $nestedData['nomor_form_perubahan_absen'] = $q->nomor_form_perubahan_absen;
                    $nestedData['nomor_absen_ijin'] = $q->nomor_absen_ijin;
                    $nestedData['nomor_form_lembur'] = $q->nomor_form_lembur;
                    $nestedData['tanggal_mulai_ijin'] = $q->tanggal_mulai_ijin;
                    $nestedData['tanggal_akhir_ijin'] = $q->tanggal_akhir_ijin;
                    $nestedData['permits_dari_pukul'] = $q->permits_dari_pukul;
                    $nestedData['permits_sampai_pukul'] = $q->permits_sampai_pukul;
                    $nestedData['total_menit_permits'] = $q->total_menit_permits;
                    $nestedData['updated_absen_ijin'] = $q->updated_absen_ijin;
                    $nestedData['updated_at'] = substr($q->updated_at, 0, 10) . " " . substr($q->updated_at, 11, 8);
                    $nestedData['operator'] = $q->operator;
                    $nestedData['holiday_name'] = $q->holiday_name;
                    $nestedData['catatan_hrd'] = $q->catatan_hrd;
                    $nestedData['site_nirwana_name'] = $q->site_nirwana_name;
                    $nestedData['department_name'] = $q->department_name;
                    $nestedData['sub_dept_name'] = $q->sub_dept_name;
                    $nestedData['tanggal_absen'] = $q->tanggal_absen;
                    $nestedData['jumlah_menit_absen_dt'] = number_format($q->jumlah_menit_absen_dt, 0);
                    $nestedData['jumlah_menit_absen_pc'] = number_format($q->jumlah_menit_absen_pc, 0);
                    $nestedData['jumlah_menit_absen_dtpc'] = number_format($q->jumlah_menit_absen_dtpc, 0);
                    $nestedData['jumlah_absen_menit_kerja'] = number_format($q->jumlah_absen_menit_kerja, 0);
                    $nestedData['status_staff'] = $q->status_staff;
                    $nestedData['status_aktif'] = $q->status_aktif;
                    $nestedData['status_jabatan'] = $q->status_jabatan;
                    $nestedData['status_staff'] = $q->status_staff;
                    $nestedData['work_status'] = $q->work_status;
                    $nestedData['employee_status'] = $q->employee_status;
                    $nestedData['mulai_jam_lembur'] = $q->mulai_jam_lembur;
                    $nestedData['akhir_jam_lembur'] = $q->akhir_jam_lembur;
                    $nestedData['jumlah_jam_lembur'] = $q->jumlah_jam_lembur;
                    $nestedData['created_at'] = $q->created_at;
                    $nestedData['updated_at'] = $q->updated_at;

                    $data[] = $nestedData;

                }
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data
                );

            echo json_encode($json_data);
        }
    }

    private function ajax_getselectdepart()
    {
        $query =  DepartmentAll::selectRaw('department_id,
                                           concat("[",site_nirwana_id,"] "
                                                  ,department_name) department_name')
                                 ->groupby('department_name')
                                 ->orderby('department_name', 'asc')
                                 ->get();

        return $query;

    }

    private function ajax_getselectrefabsenijin()
    {
        $query =  RefAbsenIjin::selectRaw('kode_absen_ijin,
                                           concat(kode_absen_ijin," - "
                                                  ,nama_absen_ijin) kode_nama_absen_ijin')
                                 ->orderby('nama_absen_ijin', 'asc')
                                 ->get();

        return $query;

    }

    public function ajax_exportexceldeptall()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

       /*  $fileName = 'DepartmentAll.xlsx';
        return (new DepartmentAllExport)->download($fileName); */

        //$fileName = 'DepartmentAll.xlsx';
        //return (new DepartmentAllExport)->download($fileName);
        return Excel::download(new DepartmentAllExport, 'DepartmentAll.xlsx');

    }

    public function ajax_exportexcel(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2000M');

        if($request->selectDepartment) {
            $selectDepartment = $request->selectDepartment;
        } else {
            $selectDepartment = "";
        }

        if($request->selectBagian) {
            $selectBagian = $request->selectBagian;
        } else {
            $selectBagian = "";
        }

        if($request->status_staff) {
            $status_staff = $request->status_staff;
        } else {
            $status_staff = "";
        }

        if($request->searchData) {
            $searchData = strtoupper($request->searchData);
        } else {
            $searchData = "";
        }

        if($request->daterange1) {
            $daterange1 = $request->daterange1;
        } else {
            $daterange1 = "";
        }

        $fileName = 'DataAbsensiKaryawan_' . time() . '.xlsx';

        return (new MasterDataAbsenKehadiranExport)->exportParams($daterange1,$selectDepartment,$selectBagian,$status_staff,$searchData)->download($fileName);
    }

    public function ajax_getselectsubdept(Request $request)
    {
        $department_id = $request->department_id;
        $query =  DepartmentAll::where('department_id','=',$department_id)
                                 ->groupby('sub_dept_id')
                                 ->orderby('sub_dept_name', 'asc')
                                 ->pluck('sub_dept_id','sub_dept_name');
        return $query;

    }

    public function ajax_getallemployeeatribut()
    {
        $query =  EmployeeAtribut::selectRaw('enroll_id, nik, employee_name,
                                           concat(enroll_id, " - ", nik, " - ", employee_name) select_employee')
                                    ->groupby('enroll_id')
                                    ->orderby('employee_name', 'asc')
                                    ->get();
        return $query;

    }

    public function ajax_getselectemployee(Request $request)
    {
        $department_id = $request->department_id;
        $sub_dept_id = $request->sub_dept_id;

        if(($department_id) && (!$sub_dept_id)) {
            $query =  EmployeeAtribut::selectRaw('enroll_id no_pin, nik, employee_name,
                                            concat(enroll_id, " - ", nik, " - ", employee_name) select_employee')
                                        ->where('department_id','=',$department_id)
                                        ->groupby('nik')
                                        ->orderby('employee_name', 'asc')
                                        ->get();

            return $query;
        } else if(($department_id) && ($sub_dept_id)) {
            $query =  EmployeeAtribut::selectRaw('enroll_id no_pin, nik, employee_name,
                                            concat(enroll_id, " - ", nik, " - ", employee_name) select_employee')
                                        ->where('department_id','=',$department_id)
                                        ->where('sub_dept_id','=',$sub_dept_id)
                                        ->groupby('nik')
                                        ->orderby('employee_name', 'asc')
                                        ->get();

            return $query;
        } else {
            $query =  EmployeeAtribut::selectRaw('enroll_id no_pin, nik, employee_name,
                                            concat(enroll_id, " - ", nik, " - ", employee_name) select_employee')
                                        ->groupby('nik')
                                        ->orderby('employee_name', 'asc')
                                        ->get();

            return $query;
        }

        //return $query;

    }

    public function ajax_getemployeselectdeptid(Request $request)
    {
        $department_id = $request->department_id;
        $dataDepartment = "";
        $inDepartmentID = '';

        if($department_id) {
            $dataDepartment = implode('","',$department_id);
            $inDepartmentID = '"' . $dataDepartment . '"';

            $query =  EmployeeAtribut::
            selectRaw('enroll_id no_pin, nik, employee_name,
                            concat(enroll_id, " - ", nik, " - ", employee_name) select_employee')
            ->whereRaw('
                department_id in (' . $inDepartmentID . ')
            ')
            ->groupby('nik')
            ->orderby('employee_name', 'asc')
            ->get();

        } else {
            $query =  EmployeeAtribut::
            selectRaw('enroll_id no_pin, nik, employee_name,
                            concat(enroll_id, " - ", nik, " - ", employee_name) select_employee')
            ->groupby('nik')
            ->orderby('employee_name', 'asc')
            ->get();
        }

        //info('Query :' . $query);
        return $query;

    }

    public function ajax_getemployeselectposisi(Request $request)
    {
        $selectPosisiName = $request->input('selectPosisiName');
        $posisi = '';

        if($selectPosisiName) {
            $posisi = ' posisi_name = "' . $selectPosisiName . '" ';
            $query =  EmployeeAtribut::selectRaw('enroll_id no_pin, nik, employee_name,
                            concat(enroll_id, " - ", nik, " - ", employee_name) select_employee')
                            ->whereRaw('
                                ' . $posisi . '
                            ')
                            ->groupby('nik')
                            ->orderby('employee_name', 'asc')
                            ->get();
            info("Posisi : " . $posisi);
        } else {
            $query =  EmployeeAtribut::selectRaw('enroll_id no_pin, nik, employee_name,
                            concat(enroll_id, " - ", nik, " - ", employee_name) select_employee')
                            ->groupby('nik')
                            ->orderby('employee_name', 'asc')
                            ->get();
        }

        return $query;

    }

    public function download_mesin_kehadiran(Request $request)
    {
        $tanggal_mesin_absensi = $request->tanggal_mesin_absensi;

        $adaData = "ADA";
        $countData = MasterDataAbsenKehadiran::whereRaw("
                tanggal_berjalan = '" . $tanggal_mesin_absensi . "'
            ")
            ->count();

        if($countData > 0 ) {
            $checkinout =  DB::connection('sqlsrv2')->select(
            DB::raw("
                SELECT CONVERT
                    ( VARCHAR ( 10 ), a.CHECKTIME, 126 ) AS tanggal_absen,
                    b.Badgenumber AS enroll_id,
                    MIN ( CONVERT ( VARCHAR ( 5 ), a.CHECKTIME, 114 ) ) AS absen_in,
                    CASE WHEN MIN ( CONVERT ( VARCHAR ( 5 ), a.CHECKTIME, 114 ) ) = MAX ( CONVERT ( VARCHAR ( 5 ), a.CHECKTIME, 114 ) )
                        THEN NULL
                        ELSE MAX ( CONVERT ( VARCHAR ( 5 ), a.CHECKTIME, 114 ) )
                    END AS absen_out,
                    CASE WHEN MIN ( CONVERT ( VARCHAR ( 5 ), a.CHECKTIME, 114 ) ) = MAX ( CONVERT ( VARCHAR ( 5 ), a.CHECKTIME, 114 ) )
                        THEN 'TL'
                        ELSE NULL
                    END status_absen
                FROM
                    CHECKINOUT a
                    JOIN USERINFO b ON ( a.USERID = b.USERID )
                WHERE
                    CONVERT ( VARCHAR ( 10 ), a.CHECKTIME, 126 ) = '" . $tanggal_mesin_absensi . "'
                GROUP BY
                    CONVERT ( VARCHAR ( 10 ), a.CHECKTIME, 126 ),
                    b.Badgenumber
            ") );
            foreach($checkinout as $value) {
                $kehadiran = MasterDataAbsenKehadiran::selectRaw("
                    substr(tanggal_berjalan,1, 10) tanggal_absen,
                    enroll_id,
                    substr(absen_masuk_kerja, 1, 5) absen_in,
                    substr(absen_pulang_kerja, 1, 5) absen_out,
                    status_absen,
                    operator 
                ")
                ->whereRaw("
                    tanggal_berjalan = '" . $tanggal_mesin_absensi . "'
                    AND enroll_id = '" . $value->enroll_id . "'
                ")
                ->get();

                // foreach($kehadiran as $val) {
                //     if(!$val["absen_out"]) {
                //         if($val["status_absen"] == "TL" || $val["status_absen"] == "M" || $val["status_absen"] == "IKS" || $val["status_absen"] == "" || !$val["status_absen"]) {
                //             MasterDataAbsenKehadiran::where('tanggal_berjalan','=', $tanggal_mesin_absensi)
                //             ->where('enroll_id','=', $val["enroll_id"])
                //             ->update([
                //                 'absen_masuk_kerja' => $value->absen_in,
                //                 'absen_pulang_kerja' => $value->absen_out,
                //                 'status_absen' => $value->status_absen
                //             ]);
                //         }
                //     }
                // }

                //Andri
                foreach($kehadiran as $val) {
                    if((!$val["absen_out"]) || ($val["operator"]=='system')) {
                        if($val["status_absen"] == "TL" || $val["status_absen"] == "M" || $val["status_absen"] == "IKS" || $val["status_absen"] == "" || !$val["status_absen"]|| $val["status_absen"] == "LN" || $val["status_absen"] == "LP") {
                            if($val["status_absen"] == "LN"){
                                $status_absen='LN';
                            }
                            else{
                                $status_absen=$value->status_absen;
                            }
                            MasterDataAbsenKehadiran::where('tanggal_berjalan','=', $tanggal_mesin_absensi)
                            ->where('enroll_id','=', $val["enroll_id"])
                            ->update([
                                'absen_masuk_kerja' => $value->absen_in,
                                'absen_pulang_kerja' => $value->absen_out,
                                'status_absen' => $status_absen
                            ]);
                        }
                    }
                }
            }
            // untuk hitung dt pc kondisi
            $masterAbsen=MasterDataAbsenKehadiran::where('tanggal_berjalan',$tanggal_mesin_absensi)->get();

                foreach ($masterAbsen as $k => $v) {
                    $jadwal_in=$v->mulai_jam_kerja;
                    $jadwal_out=$v->akhir_jam_kerja;

                    $absen_in=$v->absen_masuk_kerja;
                    $absen_out=$v->absen_pulang_kerja;

                    $durasi_kerja=date_diff(date_create($jadwal_in),date_create($jadwal_out));
                    $durasi_kerja_menit=$durasi_kerja->i +($durasi_kerja->h*60);
                
                    $DT = date_diff(date_create($jadwal_in),date_create($absen_in));
                    $PC = date_diff(date_create($jadwal_out),date_create($absen_out));
                    if( $absen_in!=null && $absen_in>$jadwal_in ){
                        $total_DT = $DT->i +($DT->h*60);
                    }else{
                        $total_DT=0;
                    }
                    if( $absen_out !=null && $absen_out<$jadwal_out){
                        $total_PC = $PC->i +($PC->h*60);
                    }else{
                        $total_PC=0;
                    }
    
                    $jumlah_menit_absen_dtpc=$total_DT+$total_PC;
                    if( $absen_in!=null && $absen_out !=null){
                        $jumlah_absen_menit_kerja=$durasi_kerja_menit-$jumlah_menit_absen_dtpc;
                    }
                    else{
                        $jumlah_absen_menit_kerja=0;
                    }

                    $jumlah_menit_absen_dtpc=$total_DT+$total_PC;
                    $data_update=[
                        'jumlah_menit_absen_dtpc'=>$jumlah_menit_absen_dtpc,
                        'jumlah_absen_menit_kerja'=>$durasi_kerja_menit-$jumlah_menit_absen_dtpc,
                        'jumlah_menit_absen_dt'=>$total_DT,
                        'jumlah_menit_absen_pc'=>$total_PC,
                    ];
                    MasterDataAbsenKehadiran::where('tanggal_berjalan', $tanggal_mesin_absensi)
                                ->where('enroll_id', $v->enroll_id)->update($data_update);

                }
            // end andri
            $setClearMTL = MasterDataAbsenKehadiran::selectRaw("
                substr(tanggal_berjalan,1, 10) tanggal_absen,
                enroll_id,
                substr(absen_masuk_kerja, 1, 5) absen_in,
                substr(absen_pulang_kerja, 1, 5) absen_out,
                null status_absen
            ")
            ->whereRaw("
                tanggal_berjalan = '" . $tanggal_mesin_absensi . "'
                AND absen_masuk_kerja is not null AND absen_pulang_kerja is not null
                AND status_absen in ('M', 'TL')
            ")
            ->get();

            foreach($setClearMTL as $value) {
                MasterDataAbsenKehadiran::where('tanggal_berjalan','=', $tanggal_mesin_absensi)
                ->where('enroll_id','=', $value["enroll_id"])
                ->update([
                    'status_absen' => $value["status_absen"]
                ]);
            }

            $setSetTL = MasterDataAbsenKehadiran::selectRaw("
                substr(tanggal_berjalan,1, 10) tanggal_absen,
                enroll_id,
                substr(absen_masuk_kerja, 1, 5) absen_in,
                substr(absen_pulang_kerja, 1, 5) absen_out,
                'TL' status_absen
            ")
            ->whereRaw("
                tanggal_berjalan = '" . $tanggal_mesin_absensi . "'
                AND status_absen in ('M', 'TL')
                AND ((absen_masuk_kerja is null AND absen_pulang_kerja is not null) OR (absen_masuk_kerja is not null AND absen_pulang_kerja is null))
            ")
            ->get();

            foreach($setSetTL as $value) {
                MasterDataAbsenKehadiran::where('tanggal_berjalan','=', $tanggal_mesin_absensi)
                ->where('enroll_id','=', $value["enroll_id"])
                ->update([
                    'status_absen' => $value["status_absen"]
                ]);
            }

            $checkinoutSM =  DB::connection('sqlsrv2')->select(
                DB::raw("
                    SET DATEFIRST 1
                    SELECT CONVERT
                        ( VARCHAR ( 10 ), a.CHECKTIME, 126 ) AS tanggal_absen,
                        b.Badgenumber AS enroll_id,
                        MIN ( CONVERT ( VARCHAR ( 5 ), a.CHECKTIME, 114 ) ) AS absen_in,
                        CASE WHEN MIN ( CONVERT ( VARCHAR ( 5 ), a.CHECKTIME, 114 ) ) = MAX ( CONVERT ( VARCHAR ( 5 ), a.CHECKTIME, 114 ) )
                            THEN NULL
                            ELSE MAX ( CONVERT ( VARCHAR ( 5 ), a.CHECKTIME, 114 ) )
                        END AS absen_out,
                        CASE WHEN MIN ( CONVERT ( VARCHAR ( 5 ), a.CHECKTIME, 114 ) ) = MAX ( CONVERT ( VARCHAR ( 5 ), a.CHECKTIME, 114 ) )
                            THEN 'TL'
                            ELSE NULL
                        END status_absen,
                        DATEPART(WEEKDAY, CONVERT ( VARCHAR ( 10 ), a.CHECKTIME, 126 )) - 1 kode_hari
                    FROM
                        CHECKINOUT a
                        JOIN USERINFO b ON ( a.USERID = b.USERID )
                    WHERE
                        CONVERT ( VARCHAR ( 10 ), a.CHECKTIME, 126 ) = '" . $tanggal_mesin_absensi . "'
                        AND DATEPART(WEEKDAY, CONVERT ( VARCHAR ( 10 ), a.CHECKTIME, 126 )) - 1 IN (5,6)
                    GROUP BY
                        CONVERT ( VARCHAR ( 10 ), a.CHECKTIME, 126 ),
                        b.Badgenumber
                ") );

            foreach($checkinoutSM as $value) {
                MasterDataAbsenKehadiran::where('tanggal_berjalan','=', $tanggal_mesin_absensi)
                ->where('enroll_id','=', $value->enroll_id)
                ->update([
                    'absen_masuk_kerja' => $value->absen_in,
                    'absen_pulang_kerja' => $value->absen_out,
                    'status_absen' => $value->status_absen
                ]);
            }

            $checkinoutAtt =  DB::connection('sqlsrv2')->select(
                DB::raw("
                    SELECT
                        NEWID() uuid,
                        CONVERT( VARCHAR ( 10 ), a.CHECKTIME, 126 ) AS tanggal_absen,
                        b.Badgenumber AS enroll_id,
                        MIN ( CONVERT ( VARCHAR ( 5 ), a.CHECKTIME, 114 ) ) AS absen_in,
                        MAX ( CONVERT ( VARCHAR ( 5 ), a.CHECKTIME, 114 ) ) AS absen_out,
                        null type
                    FROM
                        CHECKINOUT a join	USERINFO b on (a.USERID = b.USERID)
                    WHERE
                        CONVERT ( VARCHAR ( 10 ), a.CHECKTIME, 126 ) = '" . $tanggal_mesin_absensi . "'
                    GROUP BY
                        CONVERT( VARCHAR ( 10 ), a.CHECKTIME, 126 ),
                        b.Badgenumber,
                        a.CHECKTYPE
                    ORDER BY
                        CONVERT( VARCHAR ( 10 ), a.CHECKTIME, 126 ) asc
                ") );

            foreach($checkinoutAtt as $value) {

                $checkinoutAttCount = CheckInOut::whereRaw("
                    tanggal_absen = '" . $tanggal_mesin_absensi . "'
                    AND enroll_id = '" . $value->enroll_id . "'
                ")
                ->count();

                if($checkinoutAttCount > 0) {
                    CheckInOut::where('tanggal_absen','=', $tanggal_mesin_absensi)
                    ->where('enroll_id','=', $value->enroll_id)
                    ->update([
                        'absen_in' => $value->absen_in,
                        'absen_out' => $value->absen_out,
                        'type' => $value->type
                    ]);
                } else {
                    CheckInOut::create([
                        'uuid' => $value->uuid,
                        'tanggal_absen' => $value->tanggal_absen,
                        'enroll_id' => $value->enroll_id,
                        'absen_in' => $value->absen_in,
                        'absen_out' => $value->absen_out,
                        'type' => $value->type
                    ]);
                }
            }
        } else {
            $adaData = "TIDAK ADA";
        }

        echo json_encode($adaData);
    }

    public function ajax_getdashkehadiran()
    {
        $query =  DB::select(DB::raw("
        SELECT
            a.tanggal_berjalan tanggal_hari_ini,
            a.kode_hari,
            a.nama_hari,
            FORMAT(NVL(count(a.absen_masuk_kerja), 0), 0) jumlah_karyawan_masuk,
            FORMAT(NVL(count(b.enroll_id), 0), 0) total_karyawan_aktif,
            FORMAT(NVL(ROUND((count(a.absen_masuk_kerja) / count(b.enroll_id)) * 100, 2), 0), 2) persentase_kehadiran,
            FORMAT(NVL(100 - ROUND((count(a.absen_masuk_kerja) / count(b.enroll_id)) * 100, 2), 0), 2) persentase_ketidakhadiran,
            FORMAT(NVL(SUM(c.jumlah_staff), 0), 0) jumlah_staff,
            FORMAT(NVL(SUM(d.jumlah_nonstaff), 0), 0) jumlah_nonstaff,
            FORMAT(NVL(SUM(e.absen_tl), 0), 0) absen_tl_hari_kemarin,
            FORMAT(NVL(SUM(f.absen_m_weekly), 0), 0) absen_m_weekly,
            FORMAT(NVL(SUM(g.absen_m_hari_ini), 0), 0) absen_m_hari_ini
        FROM
            master_data_absen_kehadiran a,
            employee_atribut b
            LEFT JOIN (
                SELECT
                    enroll_id,
                    COUNT(enroll_id) jumlah_staff
                FROM
                    employee_atribut
                WHERE
                    (tanggal_resign is NULL OR tanggal_resign <> '0000-00-00' OR enroll_id is not null)
                    AND status_staff = 'STAFF'
                GROUP BY
                    enroll_id
            ) c ON (b.enroll_id = c.enroll_id)
            LEFT JOIN (
                SELECT
                    enroll_id,
                    COUNT(enroll_id) jumlah_nonstaff
                FROM
                    employee_atribut
                WHERE
                    (tanggal_resign is NULL OR tanggal_resign <> '0000-00-00' OR enroll_id is not null)
                    AND status_staff = 'NON STAFF'
                GROUP BY
                    enroll_id
            ) d ON (b.enroll_id = d.enroll_id)
            LEFT JOIN (
                SELECT
                    enroll_id,
                    COUNT(status_absen) absen_tl
                FROM
                    master_data_absen_kehadiran
                WHERE
                    status_absen = 'TL'
                    AND tanggal_berjalan = SUBSTR(DATE_SUB(NOW(), INTERVAL 1 DAY), 1, 10)
                GROUP BY
                    enroll_id
            ) e ON (b.enroll_id = e.enroll_id)
            LEFT JOIN (
                SELECT
                    enroll_id,
                    COUNT(status_absen) absen_m_weekly
                FROM
                    master_data_absen_kehadiran
                WHERE
                    status_absen = 'M'
                    AND tanggal_berjalan BETWEEN SUBSTR(DATE_SUB(NOW(), INTERVAL 1 WEEK), 1, 10) AND SUBSTR(NOW(), 1,10)
                GROUP BY
                    enroll_id
            ) f ON (b.enroll_id = f.enroll_id)
            LEFT JOIN (
                SELECT
                    enroll_id,
                    COUNT(status_absen) absen_m_hari_ini
                FROM
                    master_data_absen_kehadiran
                WHERE
                    status_absen = 'M'
                    AND tanggal_berjalan = SUBSTR(NOW(), 1,10)
                GROUP BY
                    enroll_id
            ) g ON (b.enroll_id = g.enroll_id)
        WHERE
            a.tanggal_berjalan = SUBSTR(NOW(), 1, 10)
            and a.enroll_id = b.enroll_id
            AND (a.absen_masuk_kerja is not null OR b.tanggal_resign is NULL OR b.tanggal_resign <> '0000-00-00' OR b.enroll_id is not null)
        GROUP BY
            a.tanggal_berjalan,
            a.kode_hari,
            a.nama_hari
        "));

        return $query;
    }

    public function ajax_getTanggalKehadiranSekarang()
    {
        setlocale(LC_ALL, 'id-ID', 'id_ID');
        $tanggalKehadiranSekarang = 'TERAKHIR DI UPDATE PADA HARI ' . strtoupper(strftime("%A", strtotime(date("Y-m-d H:i:s")))) . ', <span><i class="fa fa-calendar"></i></span> TANGGAL ' . strtoupper(strftime("%d %b %Y", strtotime(date("Y-m-d H:i:s")))) . ' PUKUL ' . strtoupper(strftime("%H:%M", strtotime(date("H:i:s"))));

        echo json_encode($tanggalKehadiranSekarang);
    }

    /*    Screen lock controller.When screen lock button from menu is cliked this controller is called.
    *     lock variable is set to 1 when screen is locked.SET to 0  if you dont want screen variable
    */

    public function screenlock()
    {
        Session::put('lock', '1');
        return View::make('admin/screen_lock', $this->data);
    }

}
