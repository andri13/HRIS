<?php

namespace App\Http\Controllers\Hris;

use App\Http\Controllers\AdminBaseController;
use App\Models\MasterDataAbsenKehadiran;
use App\Models\EmployeeAtribut;
use App\Models\DepartmentAll;
use App\Models\WorkTimeTable;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Datatables;
use App\Exports\EmployeeAtrExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Services\employee\Kehadiran;
/**
 * Class MdAbsenHadirController
 * @package App\Http\Controllers\Hris
 */
class EmployeeAtrController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->dashboardActive = 'active';
        $this->pageTitle = 'Master Data Karyawan';
    }

    public function index()
    {


        $loggedAdmin = Auth::guard('admin')->user();
        $this->loggedAdmin = $loggedAdmin;
        $this->divisi = $this->ajax_getselectdivisi();
        return View::make('hris/employeeatr', $this->data);
    }

    private function ajax_getselectdivisi()
    {
        $query =  DepartmentAll::selectRaw('site_nirwana_id, site_nirwana_name')
                                 ->groupby('site_nirwana_name')
                                 ->orderby('site_nirwana_name', 'asc')
                                 ->get();

        return $query;

    }

    public function ajax_getselectdept(Request $request)
    {
        $site_nirwana_id = $request->site_nirwana_id;

        $query =  DepartmentAll::selectRaw('department_id, department_name')
                                 ->whereRaw('site_nirwana_id = "' . $site_nirwana_id . '"')
                                 ->groupby('department_name')
                                 ->orderby('department_name', 'asc')
                                 ->get();

        return $query;

    }

    public function ajax_getselectsubdept(Request $request)
    {
        $site_nirwana_id = $request->site_nirwana_id;
        $department_id = $request->department_id;

        $query =  DepartmentAll::selectRaw('sub_dept_id, sub_dept_name')
                                 ->whereRaw('site_nirwana_id = "' . $site_nirwana_id . '" and department_id = "' . $department_id . '"')
                                 ->groupby('sub_dept_name')
                                 ->orderby('sub_dept_name', 'asc')
                                 ->get();

        return $query;

    }

    public function ajax_periksaenroll_id(Request $request)
    {
        $enroll_id = $request->enroll_id;

        $query =  EmployeeAtribut::where('enroll_id', '=', $enroll_id)
                                 ->count();

        if($query > 0) {
            return false;
        } else {
            return true;
        }

    }

    public function ajax_periksanik(Request $request)
    {
        $nik = $request->nik;

        $query =  EmployeeAtribut::where('nik', '=', $nik)
                                 ->count();

        if($query > 0) {
            return false;
        } else {
            return true;
        }

    }

    public function ajax_getemployeeatr(Request $request)
    {

        if(request()->ajax()) {

        $columns = array(
            0 => 'nik',
            1 => 'enroll_id',
            2 => 'employee_name'
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $totalData = 0;
        $totalFiltered = 0;

        if(empty($request->input('search.value')))
        {
            $query =  EmployeeAtribut::
                            offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalData = EmployeeAtribut::count();
            $totalFiltered = $totalData;

        } else {
            $search = $request->input('search.value');

            $query =  EmployeeAtribut::where('employee_id','LIKE',"%{$search}%")
                            ->orWhere('nik','LIKE',"%{$search}%")
                            ->orWhere('enroll_id','LIKE',"%{$search}%")
                            ->orWhere('employee_name','LIKE',"%{$search}%")
                            ->orWhere('site_nirwana_name','LIKE',"%{$search}%")
                            ->orWhere('department_name','LIKE',"%{$search}%")
                            ->orWhere('sub_dept_name','LIKE',"%{$search}%")
                            ->orWhere('work_status','LIKE',"%{$search}%")
                            ->orWhere('employee_status','LIKE',"%{$search}%")
                            ->orWhere('posisi_name','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalData = EmployeeAtribut::where('employee_id','LIKE',"%{$search}%")
                            ->orWhere('nik','LIKE',"%{$search}%")
                            ->orWhere('enroll_id','LIKE',"%{$search}%")
                            ->orWhere('employee_name','LIKE',"%{$search}%")
                            ->orWhere('site_nirwana_name','LIKE',"%{$search}%")
                            ->orWhere('department_name','LIKE',"%{$search}%")
                            ->orWhere('sub_dept_name','LIKE',"%{$search}%")
                            ->orWhere('work_status','LIKE',"%{$search}%")
                            ->orWhere('employee_status','LIKE',"%{$search}%")
                            ->orWhere('posisi_name','LIKE',"%{$search}%")
                            ->count();
            $totalFiltered = $totalData;

        }

        $data = array();
        if(!empty($query))
        {
            foreach ($query as $q)
            {
                $nestedData['employee_id'] = $q->employee_id;
                $nestedData['employee_name'] = $q->employee_name;
                $nestedData['jenis_kelamin'] = $q->jenis_kelamin;
                $nestedData['tempat_lahir'] = $q->tempat_lahir;
                $nestedData['tanggal_lahir'] = $q->tanggal_lahir;
                $nestedData['golongan_darah'] = $q->golongan_darah;
                $nestedData['email'] = $q->email;
                $nestedData['nomor_tlpn'] = $q->nomor_tlpn;
                $nestedData['agama'] = $q->agama;
                $nestedData['status_kawin'] = $q->status_kawin;
                $nestedData['npwp'] = $q->npwp;
                $nestedData['nomor_ktp'] = $q->nomor_ktp;
                $nestedData['nomor_kk'] = $q->nomor_kk;
                $nestedData['ptkp'] = $q->ptkp;
                $nestedData['pendidikan_terakhir'] = $q->pendidikan_terakhir;
                $nestedData['jurusan_pendidikan'] = $q->jurusan_pendidikan;
                $nestedData['nama_bank'] = $q->nama_bank;
                $nestedData['nomor_rekening_bank'] = $q->nomor_rekening_bank;
                $nestedData['ibu_kandung'] = $q->ibu_kandung;
                $nestedData['propinsi'] = $q->propinsi;
                $nestedData['kota_kab'] = $q->kota_kab;
                $nestedData['kecamatan'] = $q->kecamatan;
                $nestedData['kelurahan_desa'] = $q->kelurahan_desa;
                $nestedData['alamat_rumah'] = $q->alamat_rumah;
                $nestedData['alamat_sementara'] = $q->alamat_sementara;
                $nestedData['site_nirwana_id'] = $q->site_nirwana_id;
                $nestedData['site_nirwana_name'] = $q->site_nirwana_name;
                $nestedData['department_id'] = $q->department_id;
                $nestedData['department_name'] = $q->department_name;
                $nestedData['sub_dept_id'] = $q->sub_dept_id;
                $nestedData['sub_dept_name'] = $q->sub_dept_name;
                $nestedData['enroll_id'] = $q->enroll_id;
                $nestedData['join_date'] = $q->join_date;

                $new_employee = 0;
                $bulanKemarin = date('Y-m-d', strtotime('first day of last month'));
                $bulanSkrng = date('Y-m-d', strtotime('last day of this month'));
                if (($q->join_date >= $bulanKemarin) && ($q->join_date <= $bulanSkrng)) {
                    $new_employee = 1;
                }

                $nestedData['new_employee'] = $new_employee;

                $nestedData['nik'] = $q->nik;
                $nestedData['status_aktif'] = $q->status_aktif;
                $nestedData['status_jabatan'] = $q->status_jabatan;
                $nestedData['status_kontrak_tetap'] = $q->status_kontrak_tetap;
                $nestedData['status_staff'] = $q->status_staff;

                $nestedData['tanggal_resign'] = $q->tanggal_resign;
                $deactive = 0;
                if (($q->tanggal_resign <= now()) && ($q->tanggal_resign !== null ) ) {
                    $deactive = 1;
                }

                $nestedData['deactive'] = $deactive;

                $nestedData['tunjangan'] = $q->tunjangan;
                $nestedData['kode_grade'] = $q->kode_grade;
                $nestedData['referensi'] = $q->referensi;
                $nestedData['employee_name_atasan'] = $q->employee_name_atasan;
                $nestedData['status_aktif_bpjs_tk'] = $q->status_aktif_bpjs_tk;
                $nestedData['tanggal_bpjs_ketenagakerjaan'] = $q->tanggal_bpjs_ketenagakerjaan;
                $nestedData['nomor_bpjs_ketenagakerjaan'] = $q->nomor_bpjs_ketenagakerjaan;
                $nestedData['status_aktif_bpjs_ks'] = $q->status_aktif_bpjs_ks;
                $nestedData['tanggal_bpjs_kesehatan'] = $q->tanggal_bpjs_kesehatan;
                $nestedData['nomor_bpjs_kesehatan'] = $q->nomor_bpjs_kesehatan;
                $nestedData['pengalaman_bekerja'] = $q->pengalaman_bekerja;
                $nestedData['lokasi_file_cv'] = $q->lokasi_file_cv;
                $nestedData['nama_kerabat'] = $q->nama_kerabat;
                $nestedData['nomor_tlpn_kerabat'] = $q->nomor_tlpn_kerabat;
                $nestedData['hubungan_kerabat'] = $q->hubungan_kerabat;
                $nestedData['alamat_kerabat'] = $q->alamat_kerabat;
                $nestedData['tanggal_vaccine1'] = $q->tanggal_vaccine1;
                $nestedData['nama_vaksin1'] = $q->nama_vaksin1;
                $nestedData['tanggal_vaccine2'] = $q->tanggal_vaccine2;
                $nestedData['nama_vaksin2'] = $q->nama_vaksin2;
                $nestedData['tanggal_vaccine3'] = $q->tanggal_vaccine3;
                $nestedData['nama_vaksin3'] = $q->nama_vaksin3;
                $nestedData['golongan_sim'] = $q->golongan_sim;
                $nestedData['nomor_sim'] = $q->nomor_sim;
                $nestedData['tanggal_expire_sim'] = $q->tanggal_expire_sim;
                $nestedData['catatan'] = $q->catatan;
                $nestedData['lokasi_foto'] = $q->lokasi_foto;
                $nestedData['operator'] = $q->operator;
                $nestedData['tanggal_mulai_kontrak'] = $q->tanggal_mulai_kontrak;
                $nestedData['tanggal_akhir_kontrak'] = $q->tanggal_akhir_kontrak;
                $nestedData['catatan_kontrak'] = $q->catatan_kontrak;
                $nestedData['created_at'] = substr($q->created_at, 0, 10) . " " . substr($q->created_at, 11, 5);
                $nestedData['updated_at'] = substr($q->updated_at, 0, 10) . " " . substr($q->updated_at, 11, 5);

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

    public function ajax_getempatr(Request $request)
    {

        if(request()->ajax()) {

        $columns = array(
            0 => 'enroll_id',
            1 => 'nik',
            2 => 'employee_name',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $totalData = 0;
        $totalFiltered = 0;

        if(empty($request->input('search.value')))
        {
            $query =  EmployeeAtribut::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalData = EmployeeAtribut::count();
            $totalFiltered = $totalData;

        } else {
            $search = $request->input('search.value');

            $query =  EmployeeAtribut::where('nik','LIKE',"%{$search}%")
                            ->orWhere('enroll_id','LIKE',"%{$search}%")
                            ->orWhere('employee_name','LIKE',"%{$search}%")
                            ->orWhere('site_nirwana_name','LIKE',"%{$search}%")
                            ->orWhere('department_name','LIKE',"%{$search}%")
                            ->orWhere('sub_dept_name','LIKE',"%{$search}%")
                            ->orWhere('status_staff','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalData = EmployeeAtribut::where('nik','LIKE',"%{$search}%")
                            ->orWhere('enroll_id','LIKE',"%{$search}%")
                            ->orWhere('employee_name','LIKE',"%{$search}%")
                            ->orWhere('site_nirwana_name','LIKE',"%{$search}%")
                            ->orWhere('department_name','LIKE',"%{$search}%")
                            ->orWhere('sub_dept_name','LIKE',"%{$search}%")
                            ->orWhere('status_staff','LIKE',"%{$search}%")
                            ->count();
            $totalFiltered = $totalData;

        }

        $data = array();
        if(!empty($query))
        {
            foreach ($query as $q)
            {
                $nestedData['employee_id'] = $q->employee_id;
                $nestedData['nik'] = $q->nik;
                $nestedData['employee_name'] = $q->employee_name;
                $nestedData['enroll_id'] = $q->enroll_id;
                $nestedData['site_nirwana_name'] = $q->site_nirwana_name;
                $nestedData['department_name'] = $q->department_name;
                $nestedData['sub_dept_name'] = $q->sub_dept_name;
                $nestedData['work_status'] = $q->work_status;
                $nestedData['employee_status'] = $q->employee_status;
                $nestedData['posisi_name'] = $q->posisi_name;
                $nestedData['join_date'] = $q->join_date;
                $new_employee = 0;
                $bulanKemarin = date('Y-m-d', strtotime('first day of last month'));
                $bulanSkrng = date('Y-m-d', strtotime('last day of this month'));
                if (($q->join_date >= $bulanKemarin) && ($q->join_date <= $bulanSkrng)) {
                    $new_employee = 1;
                }
                $nestedData['new_employee'] = $new_employee;
                $nestedData['tanggal_resign'] = $q->tanggal_resign;
                $deactive = 0;
                if (($q->tanggal_resign <= now()) && ($q->tanggal_resign !== null ) ) {
                    $deactive = 1;
                }
                $nestedData['deactive'] = $deactive;
                $nestedData['jadwal_waktu_kerja1'] = $q->jadwal_waktu_kerja1;
                $nestedData['jadwal_waktu_kerja2'] = $q->jadwal_waktu_kerja2;
                $nestedData['jadwal_waktu_kerja3'] = $q->jadwal_waktu_kerja3;
                $nestedData['site_nirwana_id'] = $q->site_nirwana_id;
                $nestedData['department_id'] = $q->department_id;
                $nestedData['sub_dept_id'] = $q->sub_dept_id;
                $nestedData['kode_grade'] = $q->kode_grade;
                $nestedData['jenis_kelamin'] = $q->jenis_kelamin;
                $nestedData['tempat_lahir'] = $q->tempat_lahir;
                $nestedData['tanggal_lahir'] = $q->tanggal_lahir;
                $nestedData['alamat_rumah'] = $q->alamat_rumah;
                $nestedData['saudara_yang_bisa_dihubungi'] = $q->saudara_yang_bisa_dihubungi;
                $nestedData['hamlet'] = $q->hamlet;
                $nestedData['kota_kab'] = $q->kota_kab;
                $nestedData['kelurahan'] = $q->kelurahan;
                $nestedData['wilayah'] = $q->wilayah;
                $nestedData['propinsi'] = $q->propinsi;
                $nestedData['kode_pos'] = $q->kode_pos;
                $nestedData['agama'] = $q->agama;
                $nestedData['nomor_ktp'] = $q->nomor_ktp;
                $nestedData['golongan_darah'] = $q->golongan_darah;
                $nestedData['status_kawin'] = $q->status_kawin;
                $nestedData['pendidikan_terakhir'] = $q->pendidikan_terakhir;
                $nestedData['jurusan_pendidikan'] = $q->jurusan_pendidikan;
                $nestedData['nomor_bpjs_ketenagakerjaan'] = $q->nomor_bpjs_ketenagakerjaan;
                $nestedData['tanggal_bpjs_ketenagakerjaan'] = $q->tanggal_bpjs_ketenagakerjaan;
                $nestedData['nomor_bpjs_kesehatan'] = $q->nomor_bpjs_kesehatan;
                $nestedData['tanggal_bpjs_kesehatan'] = $q->tanggal_bpjs_kesehatan;
                $nestedData['allowance'] = $q->allowance;
                $nestedData['nama_bank'] = $q->nama_bank;
                $nestedData['nomor_rekening_bank'] = $q->nomor_rekening_bank;
                $nestedData['catatan'] = $q->catatan;
                $nestedData['email'] = $q->email;
                $nestedData['ptkp'] = $q->ptkp;
                $nestedData['tanggal_vaccine1'] = $q->tanggal_vaccine1;
                $nestedData['tanggal_vaccine2'] = $q->tanggal_vaccine2;
                $nestedData['tanggal_vaccine3'] = $q->tanggal_vaccine3;
                $nestedData['created_at'] = $q->created_at;
                $nestedData['updated_at'] = $q->updated_at;
                $nestedData['nomor_tlpn'] = $q->nomor_tlpn;
                $nestedData['npwp'] = $q->npwp;
                $nestedData['golongan_sim'] = $q->golongan_sim;
                $nestedData['nomor_sim'] = $q->nomor_sim;
                $nestedData['tanggal_expire_sim'] = $q->tanggal_expire_sim;
                $nestedData['nama_vaksin1'] = $q->nama_vaksin1;
                $nestedData['nama_vaksin2'] = $q->nama_vaksin2;
                $nestedData['nama_vaksin3'] = $q->nama_vaksin3;
                $nestedData['employee_name_atasan'] = $q->employee_name_atasan;
                $nestedData['nomor_kk'] = $q->nomor_kk;
                $nestedData['lokasi_foto'] = $q->lokasi_foto;
                $nestedData['lokasi_file_cv'] = $q->lokasi_file_cv;
                $nestedData['pola_kerja'] = $q->pola_kerja;
                $nestedData['alamat_sementara'] = $q->alamat_sementara;
                $nestedData['kode_pos'] = $q->kode_pos;
                $nestedData['kode_pos_sementara'] = $q->kode_pos_sementara;
                $nestedData['nama_kerabat'] = $q->nama_kerabat;
                $nestedData['nomor_tlpn_kerabat'] = $q->nomor_tlpn_kerabat;
                $nestedData['alamat_kerabat'] = $q->alamat_kerabat;
                $nestedData['hubungan_kerabat'] = $q->hubungan_kerabat;
                $nestedData['pengalaman_bekerja'] = $q->pengalaman_bekerja;

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

    public function replace(Request $request)
    {
        $loggedAdmin = Auth::guard('admin')->user();
        $operator = $loggedAdmin->email;

        $employee_id = time();
        $employee_name = strtoupper($request->employee_name);
        $jenis_kelamin = strtoupper($request->jenis_kelamin);
        $tempat_lahir = strtoupper($request->tempat_lahir);
        $tanggal_lahir = $request->tanggal_lahir;
        $golongan_darah = strtoupper($request->golongan_darah);
        $email = $request->email;
        $nomor_tlpn = $request->nomor_tlpn;
        $agama = strtoupper($request->agama);
        $status_kawin = strtoupper($request->status_kawin);
        $npwp = $request->npwp;
        $nomor_ktp = $request->nomor_ktp;
        $nomor_kk = $request->nomor_kk;
        $ptkp = $request->ptkp;
        $pendidikan_terakhir = strtoupper($request->pendidikan_terakhir);
        $jurusan_pendidikan = strtoupper($request->jurusan_pendidikan);
        $nama_bank = strtoupper($request->nama_bank);
        $nomor_rekening_bank = $request->nomor_rekening_bank;
        $ibu_kandung = strtoupper($request->ibu_kandung);
        $propinsi = strtoupper($request->propinsi);
        $kota_kab = strtoupper($request->kota_kab);
        $kecamatan = strtoupper($request->kecamatan);
        $kelurahan_desa = strtoupper($request->kelurahan_desa);
        $alamat_rumah = strtoupper($request->alamat_rumah);
        $alamat_sementara = strtoupper($request->alamat_sementara);
        $site_nirwana_id = $request->site_nirwana_id;
        $department_id = $request->department_id;
        $sub_dept_id = $request->sub_dept_id;
        $enroll_id = $request->enroll_id;
        $join_date = $request->join_date;
        $nik = strtoupper($request->nik);
        $status_aktif = strtoupper($request->status_aktif);
        $status_jabatan = strtoupper($request->status_jabatan);
        $status_kontrak_tetap = strtoupper($request->status_kontrak_tetap);
        $status_staff = strtoupper($request->status_staff);
        if($request->tanggal_resign == "") { $tanggal_resign = null; } else { $tanggal_resign = $request->tanggal_resign; }
        $tunjangan = strtoupper($request->tunjangan);
        $kode_grade = strtoupper($request->kode_grade);
        $referensi = strtoupper($request->referensi);
        $employee_name_atasan = strtoupper($request->employee_name_atasan);
        $status_aktif_bpjs_tk = strtoupper($request->status_aktif_bpjs_tk);
        $tanggal_bpjs_ketenagakerjaan = $request->tanggal_bpjs_ketenagakerjaan;
        $nomor_bpjs_ketenagakerjaan = $request->nomor_bpjs_ketenagakerjaan;
        $status_aktif_bpjs_ks = strtoupper($request->status_aktif_bpjs_ks);
        $tanggal_bpjs_kesehatan = $request->tanggal_bpjs_kesehatan;
        $nomor_bpjs_kesehatan = $request->nomor_bpjs_kesehatan;
        $pengalaman_bekerja = strtoupper($request->pengalaman_bekerja);
        $lokasi_file_cv = $request->lokasi_file_cv;
        $nama_kerabat = strtoupper($request->nama_kerabat);
        $nomor_tlpn_kerabat = $request->nomor_tlpn_kerabat;
        $hubungan_kerabat = strtoupper($request->hubungan_kerabat);
        $alamat_kerabat = strtoupper($request->alamat_kerabat);
        $tanggal_vaccine1 = $request->tanggal_vaccine1;
        $nama_vaksin1 = strtoupper($request->nama_vaksin1);
        $tanggal_vaccine2 = $request->tanggal_vaccine2;
        $nama_vaksin2 = strtoupper($request->nama_vaksin2);
        $tanggal_vaccine3 = $request->tanggal_vaccine3;
        $nama_vaksin3 = strtoupper($request->nama_vaksin3);
        $golongan_sim = strtoupper($request->golongan_sim);
        $nomor_sim = $request->nomor_sim;
        $tanggal_expire_sim = $request->tanggal_expire_sim;
        $catatan = strtoupper($request->catatan);
        $lokasi_foto = $request->lokasi_foto;
        $tanggal_mulai_kontrak = $request->tanggal_mulai_kontrak;
        $tanggal_akhir_kontrak = $request->tanggal_akhir_kontrak;
        $catatan_kontrak = strtoupper($request->catatan_kontrak);

        $site_nirwana_name =  DepartmentAll::select('site_nirwana_name')
                                    ->where('site_nirwana_id', '=', $site_nirwana_id)
                                    ->groupby('site_nirwana_id')
                                    ->first();

        $department_name =  DepartmentAll::select('department_name')
                                    ->where('site_nirwana_id', '=', $site_nirwana_id)
                                    ->where('department_id', '=', $department_id)
                                    ->groupby('department_id')
                                    ->first();

        $sub_dept_name =  DepartmentAll::select('sub_dept_name')
                                    ->where('site_nirwana_id', '=', $site_nirwana_id)
                                    ->where('department_id', '=', $department_id)
                                    ->where('sub_dept_id', '=', $sub_dept_id)
                                    ->groupby('sub_dept_id')
                                    ->first();


        $query = EmployeeAtribut::whereRaw('enroll_id = "' . $enroll_id . '"')->count();

        if($query > 0) {
            $query = EmployeeAtribut::whereRaw('enroll_id = "' . $enroll_id . '"')
            ->update([
                'employee_name' => $employee_name,
                'jenis_kelamin' => $jenis_kelamin,
                'tempat_lahir' => $tempat_lahir,
                'tanggal_lahir' => $tanggal_lahir,
                'golongan_darah' => $golongan_darah,
                'email' => $email,
                'nomor_tlpn' => $nomor_tlpn,
                'agama' => $agama,
                'status_kawin' => $status_kawin,
                'npwp' => $npwp,
                'nomor_ktp' => $nomor_ktp,
                'nomor_kk' => $nomor_kk,
                'ptkp' => $ptkp,
                'pendidikan_terakhir' => $pendidikan_terakhir,
                'jurusan_pendidikan' => $jurusan_pendidikan,
                'nama_bank' => $nama_bank,
                'nomor_rekening_bank' => $nomor_rekening_bank,
                'ibu_kandung' => $ibu_kandung,
                'propinsi' => $propinsi,
                'kota_kab' => $kota_kab,
                'kecamatan' => $kecamatan,
                'kelurahan_desa' => $kelurahan_desa,
                'alamat_rumah' => $alamat_rumah,
                'alamat_sementara' => $alamat_sementara,
                'site_nirwana_id' => $site_nirwana_id,
                'site_nirwana_name' => $site_nirwana_name->site_nirwana_name,
                'department_id' => $department_id,
                'department_name' => $department_name->department_name,
                'sub_dept_id' => $sub_dept_id,
                'sub_dept_name' => $sub_dept_name->sub_dept_name,
                'enroll_id' => $enroll_id,
                'join_date' => $join_date,
                'nik' => $nik,
                'status_aktif' => $status_aktif,
                'status_jabatan' => $status_jabatan,
                'status_kontrak_tetap' => $status_kontrak_tetap,
                'status_staff' => $status_staff,
                'tanggal_resign' => $tanggal_resign,
                'tunjangan' => $tunjangan,
                'kode_grade' => $kode_grade,
                'referensi' => $referensi,
                'employee_name_atasan' => $employee_name_atasan,
                'status_aktif_bpjs_tk' => $status_aktif_bpjs_tk,
                'tanggal_bpjs_ketenagakerjaan' => $tanggal_bpjs_ketenagakerjaan,
                'nomor_bpjs_ketenagakerjaan' => $nomor_bpjs_ketenagakerjaan,
                'status_aktif_bpjs_ks' => $status_aktif_bpjs_ks,
                'tanggal_bpjs_kesehatan' => $tanggal_bpjs_kesehatan,
                'nomor_bpjs_kesehatan' => $nomor_bpjs_kesehatan,
                'pengalaman_bekerja' => $pengalaman_bekerja,
                'lokasi_file_cv' => $lokasi_file_cv,
                'nama_kerabat' => $nama_kerabat,
                'nomor_tlpn_kerabat' => $nomor_tlpn_kerabat,
                'hubungan_kerabat' => $hubungan_kerabat,
                'alamat_kerabat' => $alamat_kerabat,
                'tanggal_vaccine1' => $tanggal_vaccine1,
                'nama_vaksin1' => $nama_vaksin1,
                'tanggal_vaccine2' => $tanggal_vaccine2,
                'nama_vaksin2' => $nama_vaksin2,
                'tanggal_vaccine3' => $tanggal_vaccine3,
                'nama_vaksin3' => $nama_vaksin3,
                'golongan_sim' => $golongan_sim,
                'nomor_sim' => $nomor_sim,
                'tanggal_expire_sim' => $tanggal_expire_sim,
                'catatan' => $catatan,
                'lokasi_foto' => $lokasi_foto,
                'operator' => $operator,
                'tanggal_mulai_kontrak' => $tanggal_mulai_kontrak,
                'tanggal_akhir_kontrak' => $tanggal_akhir_kontrak,
                'catatan_kontrak' => $catatan_kontrak
            ]);

            if($query) {
                if($request->tanggal_resign == "") { 

                    $query1 =  MasterDataAbsenKehadiran::selectRaw('
                        tanggal_berjalan,
                        enroll_id,
                        null tanggal_resign,
                        null status_aktif,
                        IF(absen_masuk_kerja is not null AND absen_pulang_kerja is null, "TL", "M") status_absen
                    ')
                    ->whereRaw('
                        enroll_id = "' . $enroll_id . '"
                        AND status_aktif = "TIDAK AKTIF"
                        AND (absen_masuk_kerja is null OR absen_pulang_kerja is null)
                        AND kode_hari not in (5,6)
                        AND holiday_name is null
                        AND status_absen = "R"
                    ')
                    ->get();

                    if(!empty($query1))
                    {
                        foreach ($query1 as $q1)
                        {
                            $query = MasterDataAbsenKehadiran::whereRaw('
                                tanggal_berjalan = "' . $q1->tanggal_berjalan . '"
                                AND enroll_id = "' . $enroll_id . '"
                            ')
                            ->update([
                                'tanggal_resign' => $q1->tanggal_resign,
                                'status_aktif' => $q1->status_aktif,
                                'status_absen' => $q1->status_absen,
                                'operator' => $q1->operator
                            ]);
                        }
                    }
                    
                } else { 

                    $query1 =  MasterDataAbsenKehadiran::selectRaw('
                        tanggal_berjalan,
                        enroll_id,
                        null tanggal_resign,
                        null status_aktif,
                        IF(absen_masuk_kerja is not null AND absen_pulang_kerja is null, "TL", "M") status_absen
                    ')
                    ->whereRaw('
                        enroll_id = "' . $enroll_id . '"
                        AND status_aktif = "TIDAK AKTIF"
                        AND (absen_masuk_kerja is null OR absen_pulang_kerja is null)
                        AND kode_hari not in (5,6)
                        AND holiday_name is null
                        AND status_absen = "R"
                    ')
                    ->get();

                    if(!empty($query1))
                    {
                        foreach ($query1 as $q1)
                        {
                            $query = MasterDataAbsenKehadiran::whereRaw('
                                tanggal_berjalan = "' . $q1->tanggal_berjalan . '"
                                AND enroll_id = "' . $enroll_id . '"
                            ')
                            ->update([
                                'tanggal_resign' => $q1->tanggal_resign,
                                'status_aktif' => $q1->status_aktif,
                                'status_absen' => $q1->status_absen,
                                'operator' => $q1->operator
                            ]);
                        }
                    }

                    $query2 =  MasterDataAbsenKehadiran::selectRaw('
                        master_data_absen_kehadiran.tanggal_berjalan,
                        employee_atribut.enroll_id,
                        employee_atribut.tanggal_resign,
                        employee_atribut.status_aktif,
                        "R" status_absen
                    ')
                    ->whereRaw('
                        employee_atribut.enroll_id = "' . $enroll_id . '"
                        AND employee_atribut.tanggal_resign <= master_data_absen_kehadiran.tanggal_berjalan
                        AND master_data_absen_kehadiran.status_absen = "M"
                        AND master_data_absen_kehadiran.kode_hari not in (5,6)
                        AND master_data_absen_kehadiran.holiday_name is null                       
                    ')
                    ->leftJoin('employee_atribut','master_data_absen_kehadiran.enroll_id','=','employee_atribut.enroll_id')
                    ->get();

                    if(!empty($query2))
                    {
                        foreach ($query2 as $q2)
                        {
                            $query = MasterDataAbsenKehadiran::whereRaw('
                                tanggal_berjalan = "' . $q2->tanggal_berjalan . '"
                                AND enroll_id = "' . $enroll_id . '"
                            ')
                            ->update([
                                'tanggal_resign' => $q2->tanggal_resign,
                                'status_aktif' => $q2->status_aktif,
                                'status_absen' => $q2->status_absen,
                                'operator' => $q2->operator
                            ]);
                        }
                    }
                }
            }
        } else {

            $query = EmployeeAtribut::create([

                'employee_id' => $employee_id,
                'employee_name' => $employee_name,
                'jenis_kelamin' => $jenis_kelamin,
                'tempat_lahir' => $tempat_lahir,
                'tanggal_lahir' => $tanggal_lahir,
                'golongan_darah' => $golongan_darah,
                'email' => $email,
                'nomor_tlpn' => $nomor_tlpn,
                'agama' => $agama,
                'status_kawin' => $status_kawin,
                'npwp' => $npwp,
                'nomor_ktp' => $nomor_ktp,
                'nomor_kk' => $nomor_kk,
                'ptkp' => $ptkp,
                'pendidikan_terakhir' => $pendidikan_terakhir,
                'jurusan_pendidikan' => $jurusan_pendidikan,
                'nama_bank' => $nama_bank,
                'nomor_rekening_bank' => $nomor_rekening_bank,
                'ibu_kandung' => $ibu_kandung,
                'propinsi' => $propinsi,
                'kota_kab' => $kota_kab,
                'kecamatan' => $kecamatan,
                'kelurahan_desa' => $kelurahan_desa,
                'alamat_rumah' => $alamat_rumah,
                'alamat_sementara' => $alamat_sementara,
                'site_nirwana_id' => $site_nirwana_id,
                'site_nirwana_name' => $site_nirwana_name->site_nirwana_name,
                'department_id' => $department_id,
                'department_name' => $department_name->department_name,
                'sub_dept_id' => $sub_dept_id,
                'sub_dept_name' => $sub_dept_name->sub_dept_name,
                'enroll_id' => $enroll_id,
                'join_date' => $join_date,
                'nik' => $nik,
                'status_aktif' => $status_aktif,
                'status_jabatan' => $status_jabatan,
                'status_kontrak_tetap' => $status_kontrak_tetap,
                'status_staff' => $status_staff,
                'tanggal_resign' => $tanggal_resign,
                'tunjangan' => $tunjangan,
                'kode_grade' => $kode_grade,
                'referensi' => $referensi,
                'employee_name_atasan' => $employee_name_atasan,
                'status_aktif_bpjs_tk' => $status_aktif_bpjs_tk,
                'tanggal_bpjs_ketenagakerjaan' => $tanggal_bpjs_ketenagakerjaan,
                'nomor_bpjs_ketenagakerjaan' => $nomor_bpjs_ketenagakerjaan,
                'status_aktif_bpjs_ks' => $status_aktif_bpjs_ks,
                'tanggal_bpjs_kesehatan' => $tanggal_bpjs_kesehatan,
                'nomor_bpjs_kesehatan' => $nomor_bpjs_kesehatan,
                'pengalaman_bekerja' => $pengalaman_bekerja,
                'lokasi_file_cv' => $lokasi_file_cv,
                'nama_kerabat' => $nama_kerabat,
                'nomor_tlpn_kerabat' => $nomor_tlpn_kerabat,
                'hubungan_kerabat' => $hubungan_kerabat,
                'alamat_kerabat' => $alamat_kerabat,
                'tanggal_vaccine1' => $tanggal_vaccine1,
                'nama_vaksin1' => $nama_vaksin1,
                'tanggal_vaccine2' => $tanggal_vaccine2,
                'nama_vaksin2' => $nama_vaksin2,
                'tanggal_vaccine3' => $tanggal_vaccine3,
                'nama_vaksin3' => $nama_vaksin3,
                'golongan_sim' => $golongan_sim,
                'nomor_sim' => $nomor_sim,
                'tanggal_expire_sim' => $tanggal_expire_sim,
                'catatan' => $catatan,
                'lokasi_foto' => $lokasi_foto,
                'operator' => $operator,
                'tanggal_mulai_kontrak' => $tanggal_mulai_kontrak,
                'tanggal_akhir_kontrak' => $tanggal_akhir_kontrak,
                'catatan_kontrak' => $catatan_kontrak
            ]);

        $a=(new Kehadiran)->new_employee($enroll_id);


        }

        return $query;

    }

    public function destroy(Request $request)
    {
        $enroll_id = $request->enroll_id;

        $findDT = EmployeeAtribut::where('enroll_id','=', $enroll_id)->count();

        if($findDT > 0) {
            $query = EmployeeAtribut::where('enroll_id','=',$enroll_id)->delete();
        } else {
            $query = false;
        }

        return $query;
    }

    public function ajax_exportexcel(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        $fileName = 'DataKaryawan_' . time() . '.xlsx';
        return (new EmployeeAtrExport)->exportParams()->download($fileName);

    }



    //=============Andri====================
    public function format_import_grading()
    {
        $filepath = public_path('format_import/format_grading_bpjs.xlsx');
        return Response()->download($filepath);
    }

    public function import_grading(Request $request)
    {
        try{
            $data=Excel::toArray([],$request->file('file_import'));
            $data_update=[];
            $head=$data[0][0];
            if($head[0]=='NO. ABSEN' && $head[1]=='NIP' && $head[2]=='NAMA KARYAWAN' && $head[3]=='PAY BPJS TK' && $head[4]=='PAY BPJS KS' &&
                $head[5]=='NOMOR BPJS(TK)' && $head[6]=='TANGGAL KEPESERTAAN (TK)' && $head[7]=='NOMOR BPJS (KS)' && $head[8]=='TANGGAL KEPESERTAAN (KS)' &&
                $head[9]=='KODE GRADE'){
                foreach ($data[0] as $key => $row) {
                    if($key>0){
                        $error=$key;
                        if($row[6]!='-'&& $row[6]!=null && $row[6]!=''){
                            $tgl_tk =$row[6];
                            $tgl_tk = ($tgl_tk - 25569) * 86400;
                            $tgl_tk = 25569 + ($tgl_tk / 86400);
                            $tgl_tk = ($tgl_tk - 25569) * 86400;
                            $tgl_kep_tk=date('Y-m-d', $tgl_tk);
                        }
                        else{
                            $tgl_kep_tk=null;
                        }
                        if($row[8]!='-' && $row[8]!=null){
                            $tgl_ks =$row[8];
                            $tgl_ks = ($tgl_ks - 25569) * 86400;
                            $tgl_ks = 25569 + ($tgl_ks / 86400);
                            $tgl_ks = ($tgl_ks - 25569) * 86400;
                            $tgl_kep_ks=date('Y-m-d', $tgl_ks);
                        }
                        else{
                            $tgl_kep_ks=null;
                        }
                        $data_update[]=[
                            'enroll_id'=>$row[0],
                            'tanggal_bpjs_ketenagakerjaan'=>$tgl_kep_tk,
                            'nomor_bpjs_ketenagakerjaan'=>$row[5],
                            'status_aktif_bpjs_tk'=>$row[3],
                            'tanggal_bpjs_kesehatan'=>$tgl_kep_ks,
                            'nomor_bpjs_kesehatan'=>$row[7],
                            'status_aktif_bpjs_ks'=>$row[4],
                            'kode_grade'=>$row[9],
                        ];
                    }
                }

                foreach ($data_update as $key1 => $value1) {
                    $update=[
                        'tanggal_bpjs_ketenagakerjaan'=>$value1['tanggal_bpjs_ketenagakerjaan'],
                        'nomor_bpjs_ketenagakerjaan'=>$value1['nomor_bpjs_ketenagakerjaan'],
                        'status_aktif_bpjs_tk'=>$value1['status_aktif_bpjs_tk'],
                        'tanggal_bpjs_kesehatan'=>$value1['tanggal_bpjs_kesehatan'],
                        'nomor_bpjs_kesehatan'=>$value1['nomor_bpjs_kesehatan'],
                        'status_aktif_bpjs_ks'=>$value1['status_aktif_bpjs_ks'],
                        'kode_grade'=>$value1['kode_grade'],
                    ];
                    EmployeeAtribut::where('enroll_id',$value1['enroll_id'])->update($update);
                }
                $count=count($data_update);
                return back()->with("success", $count.' row berhasil di update');
            }
            else{
                return back()->with("error",'gagal tersimpan format salah');
            }
        }catch(\Exception $e){
            $error=$error+1;
            return back()->with("error",'gagal tersimpan terdapat kesalah di row '.$error);
        } 
    }
    

}
