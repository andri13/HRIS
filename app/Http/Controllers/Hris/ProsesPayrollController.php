<?php

namespace App\Http\Controllers\Hris;

use App\Http\Controllers\AdminBaseController;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


use App\Models\MasterDataAbsenKehadiran;
use App\Models\RefAbsenIjin;
use App\Models\DataKoreksiPotongan;
use App\Models\DataKoreksiUpah;

use App\Models\RekapPerhitunganLembur;
use App\Models\RekapKehadiranKaryawan;
use App\Models\DataLembur;
use App\Models\EmployeeGrading;
use App\Models\DataAbsenPerijinan;
use App\Models\RekapPerhitunganIKS;
use App\Models\RekapPerhitunganDTPC;
use App\Models\EmployeeBpjs;
use App\Models\EmployeeAtribut;
use App\Models\TunjanganKaryawan;
use App\Models\RekapPerhitunganPayroll;
use App\Models\BpjsSetting;

use Illuminate\Support\Str;
use App\Models\RekapPerhitunganKehadiranKaryawan;



/**
 * Class MdAbsenHadirController
 * @package App\Http\Controllers\Hris
 */
class ProsesPayrollController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->dashboardActive = 'active';
        $this->pageTitle = 'Data Kehadiran Karyawan';
    }

    // public function index()
    // {
    //     # code...
    // }

    // rekap_absen
    public function rekap_absen($bulan_priode)
    {
        $bulan_sekarang1 = strtotime(date( $bulan_priode));

        $bulan_sebelum = strtotime("-1 month", $bulan_sekarang1);
        $bulan_sebelum=date('Y-m-', $bulan_sebelum);
        $bulan_sekarang=date('Y-m-', $bulan_sekarang1);

        $tanggal_awal=$bulan_sebelum.'26';
        $tanggal_akhir=$bulan_sekarang.'25';

        $bulan=date('m', $bulan_sekarang1);
        $tahun=date('Y', $bulan_sekarang1);
        $ijin_bayar=RefAbsenIjin::where('kode_ijin_payroll','IBY')->get()->toArray();
        $IBY=array_column($ijin_bayar,'kode_absen_ijin');

        $tidak_bayar=RefAbsenIjin::where('kode_ijin_payroll','ITB')->where('kode_absen_ijin','!=','M')->where('kode_absen_ijin','!=','IKS')->get()->toArray();
        $ITB=array_column($tidak_bayar,'kode_absen_ijin');

        $timestamp1 = strtotime($tanggal_awal);
        $timestamp2 = strtotime($tanggal_akhir);
        $jumlah_hari =(abs($timestamp2 - $timestamp1) / (60 * 60 * 24)+1);

        $jumlah_hari_sabtu_minggu = 0;

        $security=EmployeeAtribut::where('sub_dept_id','DEP08SUB005')->where('jenis_kelamin','LAKI-LAKI')->get();



        for ($i = strtotime($tanggal_awal); $i <= strtotime($tanggal_akhir); $i += 86400) {
            if ((date('N', $i) == 6)||(date('N', $i) == 7)) {
                $jumlah_hari_sabtu_minggu++;
            }
        }

        // $x=MasterDataAbsenKehadiran::where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->where('department_id','DEP10')->get()->groupby('enroll_id');
        // $x=MasterDataAbsenKehadiran::where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->where('enroll_id','1755')->get()->groupby('enroll_id');
        $x=MasterDataAbsenKehadiran::where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->get()->groupby('enroll_id');

        foreach ($x as $key => $value) {
            $IBY_employe=$value->wherein('status_absen', $IBY)->count();
            $LBY_employe=$value->where('status_absen','LN')->whereNotin('kode_hari', ['5','6'])->count();
            $ITB_employe=$value->wherein('status_absen', $ITB)->count();
            $lsm_employe=$value->wherein('kode_hari', ['5','6'])->count();
            $dt_employe=$value->where('jumlah_menit_absen_dt','>','0')->where('jumlah_menit_absen_pc','0')->count();
            $pc_employe=$value->where('jumlah_menit_absen_pc','>','0')->where('jumlah_menit_absen_dt','0')->count();

            $dtpc_employe=$value->where('jumlah_menit_absen_dt','>','0')->where('jumlah_menit_absen_pc','>','0')->count();

            $absen_M=$value->where('status_absen','M')->count();
            $absen_R=$value->where('status_absen','R')->count();
            $absen_TL=$value->where('status_absen','TL')->count();
            $absen_IKS=$value->where('status_absen','IKS')->where('jumlah_menit_absen_dtpc','0')->count();
            // $absen_ok=$value->where('absen_masuk_kerja','!=',null)->where('absen_pulang_kerja','!=',null)
            //                 ->where('mulai_jam_kerja','!=',null)->where('akhir_jam_kerja','!=',null)
            //                 ->where('status_absen',null)->where('jumlah_menit_absen_dtpc','0')->count();
            $absen_ok=$value->whereNotin('kode_hari', ['5','6'])->where('status_absen',null)->where('jumlah_menit_absen_dtpc','0')->count();
            $absen_ok=$absen_ok+$absen_IKS;
            $unik=$angka_string =sprintf("%04d", $key);
            $kode_rekap_kehadiran=$bulan_sekarang.$unik;

            $total_kehadiran=$IBY_employe+$ITB_employe+$lsm_employe+$dtpc_employe+$absen_M+$absen_R+$absen_ok+$LBY_employe+$dt_employe+$pc_employe+$absen_TL;
            // $kehadiran_tk=$jumlah_hari-($total_kehadiran+$absen_TL);
           
            $total_kehadiran_net=$absen_ok+$dt_employe+$pc_employe+$dtpc_employe;
            $aa=$total_kehadiran_net+$ITB_employe+$LBY_employe+$IBY_employe+$lsm_employe+$absen_M+$absen_TL+$absen_R;
            $kehadiran_tk=$jumlah_hari-$aa;

            if($security->where('enroll_id',$value->first()->enroll_id)->count()){
                $jumlah_hari_kerja=25;

            }else{
                $jumlah_hari_kerja=$jumlah_hari-$jumlah_hari_sabtu_minggu;

            }


            $y=[
                'uuid'=>Str::uuid('uuid'),
                'kode_rekap_kehadiran'=> $kode_rekap_kehadiran,
                'periode_payroll'=>$tanggal_awal.' s/d '.$tanggal_akhir,
                'periode_tahun'=>$tahun,
                'periode_bulan'=>$bulan,
                'enroll_id'=>$value->first()->enroll_id,
                'nik'=>$value->first()->nik,
                'employee_name'=>$value->first()->employee_name,
                'site_nirwana_id'=>$value->first()->site_nirwana_id,
                'site_nirwana_name'=>$value->first()->site_nirwana_name,
                'department_id'=>$value->first()->department_id,
                'department_name'=>$value->first()->department_name,
                'sub_dept_id'=>$value->first()->sub_dept_id,
                'sub_dept_name'=>$value->first()->sub_dept_name,
                'join_date'=>$value->first()->join_date,
                'tanggal_resign'=>$value->first()->tanggal_resign,
                'status_aktif'=>$value->first()->status_aktif,
                'status_staff'=>$value->first()->status_staff,
                'kehadiran_iby'=>$IBY_employe,
                'kehadiran_itb'=>$ITB_employe,
                'kehadiran_lby'=>$LBY_employe,
                'kehadiran_lsm'=>$lsm_employe,
                'kehadiran_dt'=> $dt_employe,
                'kehadiran_pc'=> $pc_employe,
                'kehadiran_dtpc'=>$dtpc_employe,
                'kehadiran_m'=>$absen_M+$absen_TL,
                'kehadiran_r'=>$absen_R,
                'kehadiran_tk'=>$kehadiran_tk,
                'kehadiran_ok'=>$absen_ok,
                'total_kehadiran'=> $total_kehadiran,
                // 'total_kehadiran'=> $total_kehadiran+$kehadiran_tk,
                'total_kehadiran_net'=>$absen_ok+$dt_employe+$pc_employe+$dtpc_employe+$LBY_employe+$IBY_employe,
                'jumlah_hari'=>$jumlah_hari,
                'jumlah_hari_kerja'=> $jumlah_hari_kerja,
            ];
            // dd($y);
            $count=RekapKehadiranKaryawan::where( 'kode_rekap_kehadiran',$kode_rekap_kehadiran)->count();
            if($count){
                RekapKehadiranKaryawan::where( 'kode_rekap_kehadiran',$kode_rekap_kehadiran)->update($y);
            }
            else{
                RekapKehadiranKaryawan::create($y);

            }
        }
        return true;
    
    }

    // rekap_absen perhitungan
    public function  rekap_absen_perhitungan($bulan_priode)
    {
        // $periode_tahun='2023';
        // $periode_bulan='03';
        $bulan_sekarang1 = strtotime(date( $bulan_priode));

        $bulan_sebelum = strtotime("-1 month", $bulan_sekarang1);
        $bulan_sebelum=date('Y-m-', $bulan_sebelum);
        $bulan_sekarang=date('Y-m-', $bulan_sekarang1);

        $tanggal_awal=$bulan_sebelum.'26';
        $tanggal_akhir=$bulan_sekarang.'25';

        $periode_bulan=date('m', $bulan_sekarang1);
        $periode_tahun=date('Y', $bulan_sekarang1);

        $data=RekapKehadiranKaryawan::where('periode_tahun',$periode_tahun)->where('periode_bulan',$periode_bulan)->get();

        $security=EmployeeAtribut::where('sub_dept_id','DEP08SUB005')->where('jenis_kelamin','LAKI-LAKI')->get();

        foreach ($data as $key => $value) {
            $periode = $value->periode_payroll ;
            $enroll_id = $value->enroll_id;
            $kode = str_replace(array('-', ' '), '', $periode) . str_pad($enroll_id, 5, '0', STR_PAD_LEFT);;
            $kode_rekap = date('Ymd', strtotime(substr($kode, 0, 8))) . date('Ymd', strtotime(substr($kode, 11, 8))) . substr($kode, 19);

            $salary=EmployeeGrading::where('enroll_id',$value->enroll_id)->latest()->first();
            $salary_bulanan=$salary->salary_bulanan??0;

            $hari_potongan=max($value->jumlah_hari_kerja-$value->total_kehadiran_net, 0);
            
            if($security->where('enroll_id',$value->enroll_id)->count()){
                $jumlah_menit_kerja=420;

            }else{
                $jumlah_menit_kerja=480;

            }
           
            $perhitungan=[
                'kode_rekap'=>$kode_rekap,
                'periode_payroll'=>$value->periode_payroll,
                'periode_tahun_bulan'=>$value->periode_tahun.'-'.$value->periode_bulan,
                'enroll_id'=>$value->enroll_id,
                'employee_name'=>$value->employee_name,
                'kehadiran_iby'=>$value->kehadiran_iby,
                'kehadiran_itb'=>$value->kehadiran_itb,
                'kehadiran_lby'=>$value->kehadiran_lby,
                'kehadiran_lsm'=>$value->kehadiran_lsm,
                'kehadiran_dt'=>$value->kehadiran_dt,
                'kehadiran_pc'=>$value->kehadiran_pc,
                'kehadiran_dtpc'=>$value->kehadiran_dtpc,
                'kehadiran_m'=>$value->kehadiran_m,
                'kehadiran_r'=>$value->kehadiran_r,
                'kehadiran_tk'=>$value->kehadiran_tk,
                'kehadiran_ok'=>$value->kehadiran_ok,
                'total_kehadiran'=>$value->total_kehadiran,
                'total_kehadiran_net'=>$value->total_kehadiran_net,
                'jumlah_hari'=>$value->jumlah_hari,
                'jumlah_hari_kerja'=>$value->jumlah_hari_kerja,
                'gaji_pokok'=>$salary_bulanan,
                'gaji_harian'=>$salary_bulanan/$value->jumlah_hari_kerja,
                'gaji_menit'=>($salary_bulanan/$value->jumlah_hari_kerja)/$jumlah_menit_kerja,
                'potongan_kehadiran_rupiah'=>($salary_bulanan/$value->jumlah_hari_kerja)*$hari_potongan,
            ];
            $count=RekapPerhitunganKehadiranKaryawan::where( 'kode_rekap',$kode_rekap)->count();
            if($count){
                RekapPerhitunganKehadiranKaryawan::where( 'kode_rekap',$kode_rekap)->update($perhitungan);
            }
            else{
                RekapPerhitunganKehadiranKaryawan::create($perhitungan);

            }
        }
        return true;
    }


    //rekap lembur
    public function rekap_lembur($bulan_priode)
    {
        $bulan_sekarang1 = strtotime(date( $bulan_priode));

        $bulan_sebelum = strtotime("-1 month", $bulan_sekarang1);
        $bulan_sebelum=date('Y-m-', $bulan_sebelum);
        $bulan_sekarang=date('Y-m-', $bulan_sekarang1);

        $tanggal_awal=$bulan_sebelum.'26';
        $tanggal_akhir=$bulan_sekarang.'25';

        $a=MasterDataAbsenKehadiran::where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->where('nomor_form_lembur','!=',null)->get();
        

        // $a=MasterDataAbsenKehadiran::where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->where('nomor_form_lembur','=','SPL/HR/2304/0679')->where('enroll_id','1763')->get();
        // dd($a);
        $data=[];
        foreach ($a as $key => $value) {
            $y=DataLembur::where('enroll_id',$value->enroll_id)->where('nomor_form_lembur',$value->nomor_form_lembur)->first();
            // dd($y);
            $jumlah_jam_kerja=date_diff(date_create($value->mulai_jam_kerja),date_create($value->akhir_jam_kerja));
            $jam_efektif_kerja=date_diff(date_create($value->absen_masuk_kerja),date_create($value->absen_pulang_kerja));

            $jam_in=$value->absen_masuk_kerja;
            $jam_out=$value->absen_pulang_kerja;

            $spl_in=date('H:i:s', strtotime($value->mulai_jam_lembur));
            $spl_out=date('H:i:s', strtotime($value->akhir_jam_lembur));

            $jadwal_in=$value->mulai_jam_kerja;
            $jadwal_out=$value->akhir_jam_kerja;

            if($jadwal_in==null || $value->status_absen=='LN'){
                $finish_in=max([$spl_in,$jam_in]);
                $finish_out=min([$spl_out,$jam_out]);
            }
            elseif ( $spl_in<$jadwal_in) {
                $finish_in=max([$spl_in,$jam_in]);
                $finish_out=min([$spl_out,$jam_out]);
            }
            else{
                $finish_in=max([$jadwal_out,$spl_in,$jam_in]);
                $finish_out=min([$spl_out,$jam_out]);

            }


            $jam1 = strtotime($finish_in);
            $jam2 = strtotime($finish_out);
            
            // Jika $jam2 lebih kecil dari $jam1, tambahkan 1 hari (86400 detik)
            if ($jam2 < $jam1) {
                $jam2 += 86400;
            }
            
            $selisih_detik = $jam2 - $jam1;
            
            $selisih_jam = floor($selisih_detik / 3600);
            $selisih_detik %= 3600;
            
            $selisih_menit = floor($selisih_detik / 60);
            $selisih_detik %= 60;
            
            $final_total_jam_lembur = sprintf("%02d:%02d:%02d", $selisih_jam, $selisih_menit, $selisih_detik);

            if($value->kode_hari==5 || $value->kode_hari==6 || $value->status_absen=='LN'){
                $kerjalibur='LIBUR';
            }
            else{
                $kerjalibur='KERJA';
            }

            $data[]=[
                // 'uuid'=>Str::uuid('uuid'),
                'tanggal_berjalan'=>$value->tanggal_berjalan,
                'kode_hari'=>$value->kode_hari,
                'nama_hari'=>$value->nama_hari,
                'kerjalibur'=>$kerjalibur,
                'holiday_name'=>$value->holiday_name,
                'nomor_form_lembur'=>$value->nomor_form_lembur,
                'enroll_id'=>$value->enroll_id,
                'nik'=>$value->nik,
                'employee_name'=>$value->employee_name,
                'site_nirwana_id'=>$value->site_nirwana_id,
                'site_nirwana_name'=>$value->site_nirwana_name,
                'department_id'=>$value->department_id,
                'department_name'=>$value->department_name,
                'sub_dept_id'=>$value->sub_dept_id,
                'sub_dept_name'=>$value->sub_dept_name,
                'posisi_name'=>$value->posisi_name,
                'mulai_jam_kerja'=>$value->mulai_jam_kerja,
                'akhir_jam_kerja'=>$value->akhir_jam_kerja,
                'jumlah_jam_kerja'=>sprintf('%02d:%02d:%02d', $jumlah_jam_kerja->h, $jumlah_jam_kerja->i, $jumlah_jam_kerja->s),
                'absen_masuk_kerja'=>$value->absen_masuk_kerja,
                'absen_pulang_kerja'=>$value->absen_pulang_kerja,
                'jam_efektif_kerja'=>sprintf('%02d:%02d:%02d', $jam_efektif_kerja->h, $jam_efektif_kerja->i, $jam_efektif_kerja->s),
                'mulai_jam_lembur'=>$value->mulai_jam_lembur,
                'akhir_jam_lembur'=>$value->akhir_jam_lembur,
                'final_mulai_jam_lembur'=>$finish_in,
                'final_selesai_jam_lembur'=>$value->absen_pulang_kerja,
                'final_total_jam_lembur'=>$final_total_jam_lembur,
                'final_jam_istirahat_lembur'=>0,
                'final_total_menit_lembur'=>($selisih_jam*60)+$selisih_menit,
                'final_jam_lembur_roundown'=> $selisih_jam,
                'final_menit_lembur_roundown'=>$selisih_menit,
                'lembur_1'=>0,
                'lembur_2'=>0,
                'lembur_3'=>0,
                'lembur_4'=>0,
                'total_lembur_1234'=>0,
                'salary'=>0,
                'lembur1_rupiah'=>0,
                'lembur2_rupiah'=> 0,
                'lembur3_rupiah'=> 0,
                'lembur4_rupiah'=> 0,
                'total_lembur_rupiah'=> 0,
                'operator'=>'system',
                'jumlah_jam_istirahat_form'=>$y->jumlah_jam_istirahat??0,
                'jumlah_jam_lembur_form'=>$y->jumlah_jam_lembur??0,
                'status_absen'=>$value->status_absen

            ];
        }
        $record=[];
            foreach ($data as $key2 => $value2) {
                if ($value2['final_menit_lembur_roundown'] <= 15) {
                    $konveri_jam = 0;
                  } elseif ($value2['final_menit_lembur_roundown'] > 15 && $value2['final_menit_lembur_roundown'] <= 45) {
                    $konveri_jam = 0.5;
                  } else {
                    $konveri_jam = 1;
                  }

                $total_jam_lembur=$value2['final_jam_lembur_roundown'] + $konveri_jam;

                $total_jam_lembur_finis=$total_jam_lembur-$value2['jumlah_jam_istirahat_form'];


                $total_jam_lembur_finis=min($value2['jumlah_jam_lembur_form'],$total_jam_lembur_finis);

                    if($value2['kode_hari']==5 || $value2['kode_hari']==6 || $value2['status_absen']=='LN'){
                        $kerjalibur='LIBUR';
                        $l1=0;
                        $l2=($total_jam_lembur_finis <= 8) ? $total_jam_lembur_finis : 8;
                        if($total_jam_lembur_finis > 9){
                            $l3=1;
                            $l4=max($total_jam_lembur_finis -9, 0);
                        }
                        else if($total_jam_lembur_finis > 8 && $total_jam_lembur_finis <=9 ){
                            $l3=max($total_jam_lembur_finis -8, 0); 
                            $l4=0;
                        }
                        else{
                            $l3=0;
                            $l4=0; 
                        }
                    }
                    else{
                        $kerjalibur='KERJA';
                        $l1 = ($total_jam_lembur_finis <= 1) ? $total_jam_lembur_finis : 1;
                        $l2 = max($total_jam_lembur_finis - 1, 0);
                        $l3=0;
                        $l4=0;

                    }


                $salary=EmployeeGrading::where('enroll_id',$value2['enroll_id'])->latest()->first();
                $salary_bulanan=$salary->salary_bulanan??0;
                    if($value2['kode_hari']==6 || $value2['status_absen']=='LN'){
                        $l1_rupiah=$l1*($salary_bulanan/173*1);
                        $l2_rupiah=$l2*($salary_bulanan/173*2);
                        $l3_rupiah=$l3*($salary_bulanan/173*2);
                        $l4_rupiah=$l4*($salary_bulanan/173*2);
                    }
                    else{
                        $l1_rupiah=$l1*($salary_bulanan/173*1);
                        $l2_rupiah=$l2*($salary_bulanan/173*1);
                        $l3_rupiah=$l3*($salary_bulanan/173*1);
                        $l4_rupiah=$l4*($salary_bulanan/173*1);
                    }

                $record=[
                    'uuid'=>Str::uuid('uuid'),
                    'tanggal_berjalan'=>$value2['tanggal_berjalan'],
                    'kode_hari'=>$value2['kode_hari'],
                    'nama_hari'=>$value2['nama_hari'],
                    'kerjalibur'=>$value2['kerjalibur'],
                    'holiday_name'=>$value2['holiday_name'],
                    'nomor_form_lembur'=>$value2['nomor_form_lembur'],
                    'enroll_id'=>$value2['enroll_id'],
                    'nik'=>$value2['nik'],
                    'employee_name'=>$value2['employee_name'],
                    'site_nirwana_id'=>$value2['site_nirwana_id'],
                    'site_nirwana_name'=>$value2['site_nirwana_name'],
                    'department_id'=>$value2['department_id'],
                    'department_name'=>$value2['department_name'],
                    'sub_dept_id'=>$value2['sub_dept_id'],
                    'sub_dept_name'=>$value2['sub_dept_name'],
                    'posisi_name'=>$value2['posisi_name'],
                    'mulai_jam_kerja'=>$value2['mulai_jam_kerja'],
                    'akhir_jam_kerja'=>$value2['akhir_jam_kerja'],
                    'jumlah_jam_kerja'=>$value2['jumlah_jam_kerja'],
                    'absen_masuk_kerja'=>$value2['absen_masuk_kerja'],
                    'absen_pulang_kerja'=>$value2['absen_pulang_kerja'],
                    'jam_efektif_kerja'=>$value2['jam_efektif_kerja'],
                    'mulai_jam_lembur'=>$value2['mulai_jam_lembur'],
                    'akhir_jam_lembur'=>$value2['akhir_jam_lembur'],
                    'final_mulai_jam_lembur'=>$value2['final_mulai_jam_lembur'],
                    'final_selesai_jam_lembur'=>$value2['final_selesai_jam_lembur'],
                    'final_total_jam_lembur'=>$value2['final_total_jam_lembur'],
                    'final_jam_istirahat_lembur'=>$value2['jumlah_jam_istirahat_form'],
                    'final_total_menit_lembur'=>$value2['final_total_menit_lembur'],
                    'final_jam_lembur_roundown'=> $value2['final_jam_lembur_roundown'],
                    'final_menit_lembur_roundown'=>$value2['final_menit_lembur_roundown'],
                    'lembur_1'=>$l1,
                    'lembur_2'=>$l2,
                    'lembur_3'=>$l3,
                    'lembur_4'=>$l4,
                    'total_lembur_1234'=>$total_jam_lembur_finis,
                    'salary'=>$salary_bulanan,
                    'lembur1_rupiah'=>$l1_rupiah,
                    'lembur2_rupiah'=> $l2_rupiah,
                    'lembur3_rupiah'=> $l3_rupiah,
                    'lembur4_rupiah'=> $l4_rupiah,
                    'total_lembur_rupiah'=> $l1_rupiah+$l2_rupiah+$l3_rupiah+$l4_rupiah,
                    'operator'=>'system',
                ];

                $count=RekapPerhitunganLembur::where('tanggal_berjalan',$value2['tanggal_berjalan'])->where('enroll_id',$value2['enroll_id'])->count();
                if($count){
                    RekapPerhitunganLembur::where('tanggal_berjalan',$value2['tanggal_berjalan'])->where('enroll_id',$value2['enroll_id'])->update($record);
                }
                else{
                    RekapPerhitunganLembur::create($record);

                } 


            }
            // dd($record);

        return true;
    }
    //rekap IKS
    public function rekap_iks($bulan_priode)
    {
        // $tanggal_awal='2023-02-26';
        // $tanggal_akhir='2023-03-25';
        $bulan_sekarang1 = strtotime(date( $bulan_priode));

        $bulan_sebelum = strtotime("-1 month", $bulan_sekarang1);
        $bulan_sebelum=date('Y-m-', $bulan_sebelum);
        $bulan_sekarang=date('Y-m-', $bulan_sekarang1);

        $tanggal_awal=$bulan_sebelum.'26';
        $tanggal_akhir=$bulan_sekarang.'25';

        $a=MasterDataAbsenKehadiran::where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->where('total_menit_permits','>',0)->get();
        $priode=$tanggal_awal.' s/d '. $tanggal_akhir;
    
        foreach ($a as $key => $value) {

            if($value->kode_hari = 4){
                $jam_mulai_istirahat='11:30';
                $jam_selesai_istirahat='12:30';
            }
            else{
                if($value->mulai_jam_kerja = '06:00' AND $value->akhir_jam_kerja = '15:00'){
                    $jam_mulai_istirahat='10:00';
                    $jam_selesai_istirahat='11:00';
                }
                elseif($value->mulai_jam_kerja = '16:00' AND $value->akhir_jam_kerja = '23:00'){
                    $jam_mulai_istirahat='18:00';
                    $jam_selesai_istirahat='19:00';
                }
                else{
                    $jam_mulai_istirahat='12:00';
                    $jam_selesai_istirahat='13:00';
                }
            }
            $minutes = $value->total_menit_permits;
            $seconds = $minutes * 60;
            $time = gmdate("H:i:s", $seconds);
           

            $salary=RekapPerhitunganKehadiranKaryawan::where('periode_payroll',$priode)->where('enroll_id',$value->enroll_id)->first();
            $salary->gaji_pokok;

            $data=[
                'uuid'=>Str::uuid('uuid'),
                'nomor_form_perizinan'=>$value->nomor_absen_ijin,
                'tanggal_berjalan'=>$value->tanggal_berjalan,
                'enroll_id'=>$value->enroll_id,
                'employee_name'=>$value->employee_name,
                'sub_dept_name'=>$value->sub_dept_name,
                'time_mulai_ijin'=>$value->permits_dari_pukul,
                'time_akhir_ijin'=>$value->permits_sampai_pukul,
                'jam_mulai_istirahat'=>$jam_mulai_istirahat,
                'jam_selesai_istirahat'=> $jam_selesai_istirahat,
                'lama_istirahat_menit'=>60,
                'lama_ijin_menit'=>$value->total_menit_permits,
                'lama_ijin_jam'=>$time,
                'absen_alasan'=>$value->absen_alasan,
                'gaji_pokok'=> $salary->gaji_pokok,
                'gaji_harian'=> $salary->gaji_harian,
                'gaji_menit'=> $salary->gaji_menit,
                'potongan_iks_rupiah'=>$salary->gaji_menit*$value->total_menit_permits,
            ];
            $count=RekapPerhitunganIKS::where('tanggal_berjalan',$value->tanggal_berjalan)->where('enroll_id',$value->enroll_id)->count();
            if($count){
                RekapPerhitunganIKS::where('tanggal_berjalan',$value->tanggal_berjalan)->where('enroll_id',$value->enroll_id)->update($data);
            }
            else{
                RekapPerhitunganIKS::create($data);
    
            } 
        }
       
        return true;
    }
    //dt pc
    public function dt_pc($bulan_priode)
    {
        // $tanggal_awal='2023-02-26';
        // $tanggal_akhir='2023-03-25';
        $bulan_sekarang1 = strtotime(date( $bulan_priode));

        $bulan_sebelum = strtotime("-1 month", $bulan_sekarang1);
        $bulan_sebelum=date('Y-m-', $bulan_sebelum);
        $bulan_sekarang=date('Y-m-', $bulan_sekarang1);

        $tanggal_awal=$bulan_sebelum.'26';
        $tanggal_akhir=$bulan_sekarang.'25';

        $priode=$tanggal_awal.' s/d '. $tanggal_akhir;
        $a=MasterDataAbsenKehadiran::where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->get();

        // $a=MasterDataAbsenKehadiran::where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->where('jumlah_menit_absen_dtpc','>',0)->where('enroll_id','3942')->get();

        foreach ($a as $key => $value) {
            $salary=RekapPerhitunganKehadiranKaryawan::where('periode_payroll',$priode)->where('enroll_id',$value->enroll_id)->first();

            $data=[
                'uuid'=>Str::uuid('uuid'),
                'tanggal_berjalan'=>$value->tanggal_berjalan,
                'employee_id'=>$value->employee_id,
                'enroll_id'=>$value->enroll_id,
                'employee_name'=>$value->employee_name,
                'mulai_jam_kerja'=>$value->mulai_jam_kerja,
                'akhir_jam_kerja'=>$value->akhir_jam_kerja,
                'absen_masuk_kerja'=>$value->absen_masuk_kerja,
                'absen_pulang_kerja'=>$value->absen_pulang_kerja,
                'status_absen'=>$value->status_absen,
                'gaji_pokok'=> $salary->gaji_pokok,
                'gaji_menit'=> $salary->gaji_menit,
                'jumlah_menit_absen_dt'=>$value->jumlah_menit_absen_dt,
                'jumlah_menit_absen_pc'=>$value->jumlah_menit_absen_pc,
                'jumlah_menit_absen_dtpc'=>$value->jumlah_menit_absen_dtpc,
                'potongan_dt_rupiah'=>$salary->gaji_menit * $value->jumlah_menit_absen_dt,
                'potongan_pc_rupiah'=>$salary->gaji_menit * $value->jumlah_menit_absen_pc,
                'potongan_dtpc_rupiah'=>$salary->gaji_menit * $value->jumlah_menit_absen_dtpc,
                'jumlah_absen_menit_kerja'=>$value->jumlah_absen_menit_kerja,
            ];
            $count=RekapPerhitunganDTPC::where('tanggal_berjalan',$value->tanggal_berjalan)->where('enroll_id',$value->enroll_id)->count();
            if($count){
                RekapPerhitunganDTPC::where('tanggal_berjalan',$value->tanggal_berjalan)->where('enroll_id',$value->enroll_id)->update($data);
            }
            else{
                RekapPerhitunganDTPC::create($data);
            } 
        }
        RekapPerhitunganDTPC::where('jumlah_menit_absen_dtpc',0)->delete();
        return true;
       
    }

    // BPJS
    public function update_bpjs($bulan_priode)
    {
        $loggedAdmin = Auth::guard('admin')->user();
        $email = $loggedAdmin->email;



        $bulan_sekarang1 = strtotime(date( $bulan_priode));

        $bulan_sebelum = strtotime("-1 month", $bulan_sekarang1);
        $bulan_sebelum=date('Y-m-', $bulan_sebelum);
        $bulan_sekarang=date('Y-m-', $bulan_sekarang1);

        $tanggal_awal=$bulan_sebelum.'26';
        $tanggal_akhir=$bulan_sekarang.'25';

        $kode_bpjs =  BpjsSetting::orderBy('kode_periode_bpjs','desc')->limit(1)->first();

        $periode_payroll=$tanggal_awal.' s/d '. $tanggal_akhir;
        
        $explodePeriodePayroll = explode(" s/d ", $periode_payroll);  
        $periodePayroll = substr($explodePeriodePayroll[1], 0, 4) . substr($explodePeriodePayroll[1], 5, 2);
        $explodeKode = explode("-", $kode_bpjs->kode_periode_bpjs);
        $kode_periode_bpjs = $explodeKode[0] . $explodeKode[1];
        $sqlKodePeriodeBPJS = 'concat("' . $periodePayroll . '", lpad(enroll_id, 5, 0))';
        // $status_staff = $request->status_staff;
        // $searchData = $request->searchData;

        $queryEmpAtr =  EmployeeAtribut::selectRaw('
                    uuid() uuid,
                    concat(SUBSTR(DATE_ADD( LAST_DAY( DATE_SUB( MAX("' . $explodePeriodePayroll[1] . '"), INTERVAL 1 MONTH )), INTERVAL 25 DAY ), 1, 4),
                    SUBSTR(DATE_ADD( LAST_DAY( DATE_SUB( MAX("' . $explodePeriodePayroll[1] . '"), INTERVAL 1 MONTH )), INTERVAL 25 DAY ), 6, 2), lpad(enroll_id, 5, 0)) kode_bpjs,
                    substr("' . $explodePeriodePayroll[1] . '", 1, 4) periode_bpjs,
                    CONCAT(DATE_ADD( LAST_DAY( DATE_SUB( MAX("' . $explodePeriodePayroll[1] . '"), INTERVAL 2 MONTH )), INTERVAL 26 DAY ), " s/d ", 
                	DATE_ADD( LAST_DAY( DATE_SUB( MAX("' . $explodePeriodePayroll[1] . '"), INTERVAL 1 MONTH )), INTERVAL 25 DAY ))  periode_kehadiran,
                    enroll_id, nik, employee_name, site_nirwana_id, department_id, sub_dept_id,
                    status_aktif_bpjs_tk, tanggal_bpjs_ketenagakerjaan, nomor_bpjs_ketenagakerjaan,
                    status_aktif_bpjs_ks, tanggal_bpjs_kesehatan, nomor_bpjs_kesehatan, join_date
                    ')
                    ->whereRaw('
                    enroll_id is not null
                    AND (tanggal_resign is null OR tanggal_resign = "0000-00-00" OR 
                        NOT tanggal_resign < DATE_ADD( LAST_DAY( DATE_SUB( "' . $explodePeriodePayroll[1] . '", INTERVAL 2 MONTH )), INTERVAL 26 DAY ))
                    AND join_date <= "' . $explodePeriodePayroll[1] . '"
                    ')
                    ->groupBy('enroll_id')
                    ->groupBy('employee_name')
                    ->get();

        foreach ($queryEmpAtr as $key => $value) {

            $tanggal_masuk = $value['join_date'];

            // hitung selisih tahun antara tanggal masuk dan sekarang
            $selisih_tahun = date_diff(date_create($tanggal_masuk), date_create($explodePeriodePayroll[1]))->y;

            // tentukan besaran tunjangan berdasarkan masa kerja
            if ($selisih_tahun < 1) {
                $tunjangan = 0;
            } elseif ($selisih_tahun < 3) {
                $tunjangan = 2500;
            } elseif ($selisih_tahun < 6) {
                $tunjangan = 5000;
            }elseif ($selisih_tahun < 9) {
                $tunjangan = 7500;
            }elseif ($selisih_tahun < 12) {
                $tunjangan = 10000;
            }else{
                $tunjangan = 12500;
            }

            $countEmp = EmployeeBpjs::whereRaw('kode_bpjs = ' . $sqlKodePeriodeBPJS . ' AND enroll_id = "' . $value['enroll_id'] . '"')->count();

            if ($countEmp) {
                $queryEmpBpjs = EmployeeBpjs::whereRaw('kode_bpjs = ' . $sqlKodePeriodeBPJS . ' AND enroll_id = "' . $value['enroll_id'] . '"')
                ->update([
                    'status_aktif_bpjs_tk' => $value['status_aktif_bpjs_tk'],
                    'tanggal_bpjs_ketenagakerjaan' => $value['tanggal_bpjs_ketenagakerjaan'],
                    'nomor_bpjs_ketenagakerjaan' => $value['nomor_bpjs_ketenagakerjaan'],
                    'status_aktif_bpjs_ks' => $value['status_aktif_bpjs_ks'],
                    'tanggal_bpjs_kesehatan' => $value['tanggal_bpjs_kesehatan'],
                    'nomor_bpjs_kesehatan' => $value['nomor_bpjs_kesehatan'],
                    'kode_periode_bpjs' => null,
                    'kode_dasar_pot_bpjs' => null,
                    'dasar_pot_bpjs_rupiah' => 0,
                    'bpjs_tk_jkm_bruto_rupiah' => 0,
                    'bpjs_tk_jkk_bruto_rupiah' => 0,
                    'bpjs_ks_jkn_bruto_rupiah' => 0,
                    'bpjs_tk_jkm_neto_rupiah' => 0,
                    'bpjs_tk_jkk_neto_rupiah' => 0,
                    'bpjs_tk_jht_neto_rupiah' => 0,
                    'bpjs_tk_jpn_neto_rupiah' => 0,
                    'bpjs_ks_jkn_neto_rupiah' => 0,
                    'bpjs_tk_jkm_persen' => 0,
                    'bpjs_tk_jkk_persen' => 0,
                    'bpjs_tk_jht_persen' => 0,
                    'bpjs_tk_jpn_persen' => 0,
                    'bpjs_ks_jkn_persen' => 0,
                    'bpjs_tk_jkm_bruto_persen' => 0,
                    'bpjs_tk_jkk_bruto_persen' => 0,
                    'bpjs_tk_jht_bruto_persen' => 0,
                    'bpjs_tk_jpn_bruto_persen' => 0,
                    'bpjs_ks_jkn_bruto_persen' => 0,
                    'bpjs_tk_jkm_neto_persen' => 0,
                    'bpjs_tk_jkk_neto_persen' => 0,
                    'bpjs_tk_jht_neto_persen' => 0,
                    'bpjs_tk_jpn_neto_persen' => 0,
                    'bpjs_ks_jkn_neto_persen' => 0,
                    'tmk'=>$tunjangan,
                ]);
            } else {
                EmployeeBpjs::create([
                    'uuid' => Str::uuid(),
                    'kode_bpjs' => $value['kode_bpjs'],
                    'periode_bpjs' => $value['periode_bpjs'],
                    'periode_kehadiran' => $value['periode_kehadiran'],
                    'enroll_id' => $value['enroll_id'],
                    'nik' => $value['nik'],
                    'employee_name' => $value['employee_name'],
                    'status_aktif_bpjs_tk' => $value['status_aktif_bpjs_tk'],
                    'tanggal_bpjs_ketenagakerjaan' => $value['tanggal_bpjs_ketenagakerjaan'],
                    'nomor_bpjs_ketenagakerjaan' => $value['nomor_bpjs_ketenagakerjaan'],
                    'status_aktif_bpjs_ks' => $value['status_aktif_bpjs_ks'],
                    'tanggal_bpjs_kesehatan' => $value['tanggal_bpjs_kesehatan'],
                    'nomor_bpjs_kesehatan' => $value['nomor_bpjs_kesehatan'],
                    'kode_periode_bpjs' => null,
                    'kode_dasar_pot_bpjs' => null,
                    'dasar_pot_bpjs_rupiah' => 0,
                    'bpjs_tk_jkm_bruto_rupiah' => 0,
                    'bpjs_tk_jkk_bruto_rupiah' => 0,
                    'bpjs_ks_jkn_bruto_rupiah' => 0,
                    'bpjs_tk_jkm_neto_rupiah' => 0,
                    'bpjs_tk_jkk_neto_rupiah' => 0,
                    'bpjs_tk_jht_neto_rupiah' => 0,
                    'bpjs_tk_jpn_neto_rupiah' => 0,
                    'bpjs_ks_jkn_neto_rupiah' => 0,
                    'bpjs_tk_jkm_persen' => 0,
                    'bpjs_tk_jkk_persen' => 0,
                    'bpjs_tk_jht_persen' => 0,
                    'bpjs_tk_jpn_persen' => 0,
                    'bpjs_ks_jkn_persen' => 0,
                    'bpjs_tk_jkm_bruto_persen' => 0,
                    'bpjs_tk_jkk_bruto_persen' => 0,
                    'bpjs_tk_jht_bruto_persen' => 0,
                    'bpjs_tk_jpn_bruto_persen' => 0,
                    'bpjs_ks_jkn_bruto_persen' => 0,
                    'bpjs_tk_jkm_neto_persen' => 0,
                    'bpjs_tk_jkk_neto_persen' => 0,
                    'bpjs_tk_jht_neto_persen' => 0,
                    'bpjs_tk_jpn_neto_persen' => 0,
                    'bpjs_ks_jkn_neto_persen' => 0,
                    'tmk'=>$tunjangan,
                ]);    
            }

        }
        $query =  BpjsSetting::whereRaw(' substr(kode_periode_bpjs, 1, 4) = substr("' . $kode_bpjs->kode_periode_bpjs . '", 1, 4)')
                ->orderBy('kode_periode_bpjs','desc')
                ->limit(1)
                ->get();

        $kode_periode_bpjs = $query[0]->kode_periode_bpjs;
        $kode_dasar_pot_bpjs = $query[0]->kode_dasar_pot_bpjs;
        $dasar_pot_bpjs_rupiah_gapok = $query[0]->dasar_pot_bpjs_rupiah;
        $bpjs_tk_jkm_persen = $query[0]->bpjs_tk_jkm_persen;
        $bpjs_tk_jkm_perusahaan_persen = $query[0]->bpjs_tk_jkm_perusahaan_persen;
        $bpjs_tk_jkm_karyawan_persen = $query[0]->bpjs_tk_jkm_karyawan_persen;
        $bpjs_tk_jkk_persen = $query[0]->bpjs_tk_jkk_persen;
        $bpjs_tk_jkk_perusahaan_persen = $query[0]->bpjs_tk_jkk_perusahaan_persen;
        $bpjs_tk_jkk_karyawan_persen = $query[0]->bpjs_tk_jkk_karyawan_persen;
        $bpjs_tk_jht_persen = $query[0]->bpjs_tk_jht_persen;
        $bpjs_tk_jht_perusahaan_persen = $query[0]->bpjs_tk_jht_perusahaan_persen;
        $bpjs_tk_jht_karyawan_persen = $query[0]->bpjs_tk_jht_karyawan_persen;
        $bpjs_tk_jpn_persen = $query[0]->bpjs_tk_jpn_persen;
        $bpjs_tk_jpn_perusahaan_persen = $query[0]->bpjs_tk_jpn_perusahaan_persen;
        $bpjs_tk_jpn_karyawan_persen = $query[0]->bpjs_tk_jpn_karyawan_persen;
        $bpjs_ks_jkn_persen = $query[0]->bpjs_ks_jkn_persen;
        $bpjs_ks_jkn_perusahaan_persen = $query[0]->bpjs_ks_jkn_perusahaan_persen;
        $bpjs_ks_jkn_karyawan_persen = $query[0]->bpjs_ks_jkn_karyawan_persen;

        $EmpBpjs = EmployeeBpjs::where('periode_kehadiran',$periode_payroll)->get();

        foreach ($EmpBpjs as $key3 => $value3) {
            $dasar_pot_bpjs_rupiah=$dasar_pot_bpjs_rupiah_gapok+$value3->tmk;
            // dd($dasar_pot_bpjs_rupiah);
        $queryEmpBpjs = DB::update('update employee_bpjs set 
                kode_periode_bpjs = "' . $kode_periode_bpjs . '",
                kode_dasar_pot_bpjs = "' . $kode_dasar_pot_bpjs . '",
                dasar_pot_bpjs_rupiah = "' . $dasar_pot_bpjs_rupiah . '",
                bpjs_tk_jkm_bruto_rupiah = IF(status_aktif_bpjs_tk = "AKTIF", (' . $dasar_pot_bpjs_rupiah . ' * (' . $bpjs_tk_jkm_perusahaan_persen . '/100)), 0),
                bpjs_tk_jkk_bruto_rupiah = IF(status_aktif_bpjs_tk = "AKTIF", (' . $dasar_pot_bpjs_rupiah . ' * (' . $bpjs_tk_jkk_perusahaan_persen . '/100)), 0),
                bpjs_tk_jht_bruto_rupiah = IF(status_aktif_bpjs_tk = "AKTIF", (' . $dasar_pot_bpjs_rupiah . ' * (' . $bpjs_tk_jht_perusahaan_persen . '/100)), 0),
                bpjs_tk_jpn_bruto_rupiah = IF(status_aktif_bpjs_tk = "AKTIF", (' . $dasar_pot_bpjs_rupiah . ' * (' . $bpjs_tk_jpn_perusahaan_persen . '/100)), 0),
                bpjs_ks_jkn_bruto_rupiah = IF(status_aktif_bpjs_ks = "AKTIF", (' . $dasar_pot_bpjs_rupiah . ' * (' . $bpjs_ks_jkn_perusahaan_persen . '/100)), 0),
                bpjs_tk_jkm_neto_rupiah = IF(status_aktif_bpjs_tk = "AKTIF", (' . $dasar_pot_bpjs_rupiah . ' * (' . $bpjs_tk_jkm_karyawan_persen . '/100)), 0),
                bpjs_tk_jkk_neto_rupiah = IF(status_aktif_bpjs_tk = "AKTIF", (' . $dasar_pot_bpjs_rupiah . ' * (' . $bpjs_tk_jkk_karyawan_persen . '/100)), 0),
                bpjs_tk_jht_neto_rupiah = IF(status_aktif_bpjs_tk = "AKTIF", (' . $dasar_pot_bpjs_rupiah . ' * (' . $bpjs_tk_jht_karyawan_persen . '/100)), 0),
                bpjs_tk_jpn_neto_rupiah = IF(status_aktif_bpjs_tk = "AKTIF", (' . $dasar_pot_bpjs_rupiah . ' * (' . $bpjs_tk_jpn_karyawan_persen . '/100)), 0),
                bpjs_ks_jkn_neto_rupiah = IF(status_aktif_bpjs_ks = "AKTIF", (' . $dasar_pot_bpjs_rupiah . ' * (' . $bpjs_ks_jkn_karyawan_persen . '/100)), 0),
                bpjs_tk_jkm_persen = "' . $bpjs_tk_jkm_persen . '",
                bpjs_tk_jkk_persen = "' . $bpjs_tk_jkk_persen . '",
                bpjs_tk_jht_persen = "' . $bpjs_tk_jht_persen . '",
                bpjs_tk_jpn_persen = "' . $bpjs_tk_jpn_persen . '",
                bpjs_ks_jkn_persen = "' . $bpjs_ks_jkn_persen . '",
                bpjs_tk_jkm_bruto_persen = "' . $bpjs_tk_jkm_perusahaan_persen . '",
                bpjs_tk_jkk_bruto_persen = "' . $bpjs_tk_jkk_perusahaan_persen . '",
                bpjs_tk_jht_bruto_persen = "' . $bpjs_tk_jht_perusahaan_persen . '",
                bpjs_tk_jpn_bruto_persen = "' . $bpjs_tk_jpn_perusahaan_persen . '",
                bpjs_ks_jkn_bruto_persen = "' . $bpjs_ks_jkn_perusahaan_persen . '",
                bpjs_tk_jkm_neto_persen = "' . $bpjs_tk_jkm_karyawan_persen . '",
                bpjs_tk_jkk_neto_persen = "' . $bpjs_tk_jkk_karyawan_persen . '",
                bpjs_tk_jht_neto_persen = "' . $bpjs_tk_jht_karyawan_persen . '",
                bpjs_tk_jpn_neto_persen = "' . $bpjs_tk_jpn_karyawan_persen . '",
                bpjs_ks_jkn_neto_persen = "' . $bpjs_ks_jkn_karyawan_persen . '",
                operator = "' . $email . '"
            where enroll_id = "'. $value3->enroll_id .'"');
        }
        

        return Response()->json($queryEmpBpjs);
    }
    
    // rekap payroll
    public function rekap_payroll($bulan_priode)
    {
        // $tanggal_awal='2023-02-26';
        // $tanggal_akhir='2023-03-25';

        $bulan_sekarang1 = strtotime(date( $bulan_priode));

        $bulan_sebelum = strtotime("-1 month", $bulan_sekarang1);
        $bulan_sebelum=date('Y-m-', $bulan_sebelum);
        $bulan_sekarang=date('Y-m-', $bulan_sekarang1);

        $tanggal_awal=$bulan_sebelum.'26';
        $tanggal_akhir=$bulan_sekarang.'25';

        $karyawan=EmployeeAtribut::where('status_aktif','AKTIF')->where('join_date','<=', $tanggal_akhir)->ORwhere('tanggal_resign','>',$tanggal_awal)->get();
        // $karyawan=EmployeeAtribut::where('status_aktif','AKTIF')->where('enroll_id','4966')->get();

        $periode_payroll=$tanggal_awal.' s/d '.$tanggal_akhir;// menggunakan s/d

        $tanggal_awal_baru = date('m/d/Y', strtotime($tanggal_awal));
        $tanggal_akhir_baru = date('m/d/Y', strtotime($tanggal_akhir));
        $periode_payroll2 = $tanggal_awal_baru . ' - ' . $tanggal_akhir_baru;// menggunakan -

        $data=[];
        foreach ($karyawan as $key => $value) {

            $rekap_kehadiran=RekapPerhitunganKehadiranKaryawan::where('enroll_id',$value->enroll_id)->where('periode_payroll',$periode_payroll)->first();

            $rekap_lembur=RekapPerhitunganLembur::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->get();
        
            $rekap_iks=RekapPerhitunganIKS::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->get();
            
            $rekap_dtpc=RekapPerhitunganDTPC::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->get();

            $Bpjs=EmployeeBpjs::where('enroll_id',$value->enroll_id)->where('periode_kehadiran',$periode_payroll)->first();
            

            $koreksi_upah=DataKoreksiUpah::where('enroll_id',$value->enroll_id)->where('periode_tanggal_koreksi',$periode_payroll2)->get();

            $koreksi_potongan=DataKoreksiPotongan::where('enroll_id',$value->enroll_id)->where('periode_tanggal_koreksi',$periode_payroll2)->get();

            $tunjangan=TunjanganKaryawan::where('enroll_id',$value->enroll_id)->where('periode_payroll',$periode_payroll)->first();

            if($rekap_kehadiran!=null){
                list($periode_tahun_payroll, $periode_bulan_payroll) = explode("-", $rekap_kehadiran->periode_tahun_bulan);
                //tunjangan
                $tanggal_masuk = $value->join_date;

                // hitung selisih tahun antara tanggal masuk dan sekarang
                $selisih_tahun = date_diff(date_create($tanggal_masuk), date_create($tanggal_akhir))->y;

                // tentukan besaran tunjangan berdasarkan masa kerja
                if ($selisih_tahun < 1) {
                    $tunjangan = 0;
                } elseif ($selisih_tahun < 3) {
                    $tunjangan = 2500;
                } elseif ($selisih_tahun < 6) {
                    $tunjangan = 5000;
                }elseif ($selisih_tahun < 9) {
                    $tunjangan = 7500;
                }elseif ($selisih_tahun < 12) {
                    $tunjangan = 10000;
                }else{
                    $tunjangan = 12500;
                }


                $bpjs_tk_jkm_bruto_rupiah=$Bpjs->bpjs_tk_jkm_bruto_rupiah??0;
                $bpjs_tk_jkm_neto_rupiah=$Bpjs->bpjs_tk_jkm_neto_rupiah??0;
                $bpjs_tk_jkk_bruto_rupiah=$Bpjs->bpjs_tk_jkk_bruto_rupiah??0;
                $bpjs_tk_jkk_neto_rupiah=$Bpjs->bpjs_tk_jkk_neto_rupiah??0;
                $bpjs_tk_jht_bruto_rupiah=$Bpjs->bpjs_tk_jht_bruto_rupiah??0;
                $bpjs_tk_jht_neto_rupiah=$Bpjs->bpjs_tk_jht_neto_rupiah??0;
                $bpjs_tk_jpn_bruto_rupiah=$Bpjs->bpjs_tk_jpn_bruto_rupiah??0;
                $bpjs_tk_jpn_neto_rupiah=$Bpjs->bpjs_tk_jpn_neto_rupiah??0;
                $bpjs_ks_jkn_bruto_rupiah=$Bpjs->bpjs_ks_jkn_bruto_rupiah??0;
                $bpjs_ks_jkn_neto_rupiah=$Bpjs->bpjs_ks_jkn_neto_rupiah??0;

    

                $data[]=[
                    'kode_rekap_payroll'=>$rekap_kehadiran->kode_rekap,
                    'periode_kehadiran'=>$rekap_kehadiran->periode_payroll,
                    'periode_tahun_payroll'=>$periode_tahun_payroll,
                    'periode_bulan_payroll'=>$periode_bulan_payroll,
                    'enroll_id'=>$value->enroll_id,
                    'nik'=>$value->nik,
                    'kode_grade'=>$value->kode_grade,
                    'join_date'=>$value->join_date,
                    'employee_name'=>$value->employee_name,
                    'tanggal_resign'=>$value->tanggal_resign,
                    'kehadiran_iby'=>$rekap_kehadiran->kehadiran_iby,
                    'kehadiran_itb'=>$rekap_kehadiran->kehadiran_itb,
                    'kehadiran_m'=>$rekap_kehadiran->kehadiran_m,
                    'kehadiran_dt'=>$rekap_kehadiran->kehadiran_dt,
                    'kehadiran_pc'=>$rekap_kehadiran->kehadiran_pc,
                    'kehadiran_dtpc'=>$rekap_kehadiran->kehadiran_dtpc,
                    'kehadiran_lby'=>$rekap_kehadiran->kehadiran_lby,
                    'kehadiran_lsm'=>$rekap_kehadiran->kehadiran_lsm,
                    'kehadiran_r'=>$rekap_kehadiran->kehadiran_r,
                    'kehadiran_ok'=>$rekap_kehadiran->kehadiran_ok,
                    'kehadiran_tk'=>$rekap_kehadiran->kehadiran_tk,
                    'total_kehadiran'=>$rekap_kehadiran->total_kehadiran,
                    'total_kehadiran_net'=>$rekap_kehadiran->total_kehadiran_net,
                    'ptkp'=>$value->ptkp,
                    'upah_per_bulan'=>$rekap_kehadiran->gaji_pokok,
                    'upah_per_hari'=>$rekap_kehadiran->gaji_harian,
                    'upah_per_menit'=>$rekap_kehadiran->gaji_menit,

                    'tunjangan_karyawan_rupiah'=>$tunjangan,
                    'premi_karyawan'=>0,

                    'lembur_1'=>$rekap_lembur->sum('lembur_1')??0,
                    'lembur_2'=>$rekap_lembur->sum('lembur_2')??0,
                    'lembur_3'=>$rekap_lembur->sum('lembur_3')??0,
                    'lembur_4'=>$rekap_lembur->sum('lembur_4')??0,
                    'total_lembur_1234'=>$rekap_lembur->sum('total_lembur_1234')??0,
                    'lembur1_rupiah'=>$rekap_lembur->sum('lembur1_rupiah')??0,
                    'lembur2_rupiah'=>$rekap_lembur->sum('lembur2_rupiah')??0,
                    'lembur3_rupiah'=>$rekap_lembur->sum('lembur3_rupiah')??0,
                    'lembur4_rupiah'=>$rekap_lembur->sum('lembur4_rupiah')??0,
                    'total_lembur_rupiah'=>$rekap_lembur->sum('total_lembur_rupiah')??0,

                    'pendapatan_lainnya_rupiah'=>0,

                    'koreksi_upah_rupiah'=>$koreksi_upah->sum('jumlah_rp_potongan')??0,
                    'koreksi_potongan_rupiah'=>$koreksi_potongan->sum('jumlah_rp_potongan')??0,

                    'potongan_iks_menit'=>$rekap_iks->sum('lama_ijin_menit')??0,
                    'potongan_dt_menit'=>$rekap_dtpc->sum('jumlah_menit_absen_dt')??0,
                    'potongan_pc_menit'=>$rekap_dtpc->sum('jumlah_menit_absen_pc')??0,
                    'potongan_dtpc_menit'=>$rekap_dtpc->sum('jumlah_menit_absen_dtpc')??0,
                    'potongan_iks_rupiah'=>$rekap_iks->sum('potongan_iks_rupiah')??0,
                    'potongan_dt_rupiah'=>$rekap_dtpc->sum('potongan_dt_rupiah')??0,
                    'potongan_pc_rupiah'=>$rekap_dtpc->sum('potongan_pc_rupiah')??0,
                    'potongan_dtpc_rupiah'=>$rekap_dtpc->sum('potongan_dtpc_rupiah')??0,
                    'potongan_kehadiran_rupiah'=>$rekap_kehadiran->potongan_kehadiran_rupiah,


                    // 'upah_bruto_rupiah'=>$value,
                    // 'upah_neto_rupiah'=>$value,

                    // 'upah_bersih_rupiah'=>$value,
                    // 'total_upah_thp_rupiah'=>$value,
                    // 'jumlah_potongan_rupiah'=>$value,


                    'pph21'=>0, // dari mana?
                    'iuran_serikat_rupiah'=>0, // dari mana?
                    'iuran_koperasi'=>0, // dari mana?
                    'potongan_kasbon_rupiah'=>0, // dari mana?

                    'bpjs_tk_jkm_rupiah'=> $bpjs_tk_jkm_bruto_rupiah + $bpjs_tk_jkm_neto_rupiah,
                    'bpjs_tk_jkm_perusahaan_rupiah'=> $bpjs_tk_jkm_bruto_rupiah,
                    'bpjs_tk_jkm_karyawan_rupiah'=> $bpjs_tk_jkm_neto_rupiah,
                    'bpjs_tk_jkk_rupiah'=> $bpjs_tk_jkk_bruto_rupiah + $bpjs_tk_jkk_neto_rupiah,
                    'bpjs_tk_jkk_perusahaan_rupiah'=> $bpjs_tk_jkk_bruto_rupiah,
                    'bpjs_tk_jkk_karyawan_rupiah'=> $bpjs_tk_jkk_neto_rupiah,
                    'bpjs_tk_jht_rupiah'=> $bpjs_tk_jht_bruto_rupiah + $bpjs_tk_jht_neto_rupiah,
                    'bpjs_tk_jht_perusahaan_rupiah'=> $bpjs_tk_jht_bruto_rupiah,
                    'bpjs_tk_jht_karyawan_rupiah'=> $bpjs_tk_jht_neto_rupiah,
                    'bpjs_tk_jpn_rupiah'=> $bpjs_tk_jpn_bruto_rupiah + $bpjs_tk_jpn_neto_rupiah,
                    'bpjs_tk_jpn_perusahaan_rupiah'=> $bpjs_tk_jpn_bruto_rupiah,
                    'bpjs_tk_jpn_karyawan_rupiah'=> $bpjs_tk_jpn_neto_rupiah,
                    'bpjs_ks_jkn_rupiah'=> $bpjs_ks_jkn_bruto_rupiah + $bpjs_ks_jkn_neto_rupiah,
                    'bpjs_ks_jkn_perusahaan_rupiah'=> $bpjs_ks_jkn_bruto_rupiah,
                    'bpjs_ks_jkn_karyawan_rupiah'=> $bpjs_ks_jkn_neto_rupiah,
                    'total_bpjs_tk'=>$bpjs_tk_jkm_neto_rupiah + $bpjs_tk_jkk_neto_rupiah + $bpjs_tk_jht_neto_rupiah + $bpjs_tk_jpn_neto_rupiah,
                    'total_bpjs_ks'=>$bpjs_ks_jkn_neto_rupiah,
                    
                    'jabatan_karyawan'=>$value->status_jabatan,
                    'nama_bagian'=>$value->dept()->first()->sub_dept_name??'',
                    'nama_department'=>$value->dept()->first()->department_name??'',
                    'kategori_karyawan'=>$value->status_staff,
                    'aktif_karyawan'=>$value->status_aktif,
                    'jenis_kelamin'=>$value->jenis_kelamin,
                    'status_kawin'=>$value->status_kawin,
                    'site_nirwana_name'=>$value->site_nirwana_name,
                    'nama_bank'=>$value->nama_bank==null||$value->nama_bank==""||$value->nama_bank=="-"?'TUNAI':$value->nama_bank,
                    'nomor_rekening_bank'=>$value->nomor_rekening_bank==null||$value->nomor_rekening_bank==""||$value->nomor_rekening_bank=="-"?'TRANSFER':$value->nomor_rekening_bank,
                    'npwp'=>$value->npwp,
                    'operator'=>'sistem',
                ];
            }
        }
        // $security=EmployeeAtribut::where('sub_dept_id','DEP08SUB005')->where('jenis_kelamin','LAKI-LAKI')->get();

        $records=[];
        foreach ($data as $k => $v) {
            // $count=$security->where('enroll_id',$v['enroll_id'])->count();
            // if($count==0){
                $upah_bruto_rupiah=($v['upah_per_bulan']+$v['tunjangan_karyawan_rupiah']+$v['premi_karyawan']+$v['total_lembur_rupiah']+$v['pendapatan_lainnya_rupiah']+$v['koreksi_upah_rupiah'])-
                    ($v['koreksi_potongan_rupiah']+$v['potongan_iks_rupiah']+$v['potongan_dtpc_rupiah']+$v['potongan_kehadiran_rupiah']);
                
                $upah_neto_rupiah=($v['upah_per_bulan']+$v['tunjangan_karyawan_rupiah']+$v['premi_karyawan']+$v['total_lembur_rupiah']+$v['pendapatan_lainnya_rupiah']+$v['koreksi_upah_rupiah'])-
                    ($v['koreksi_potongan_rupiah']+$v['potongan_iks_rupiah']+$v['potongan_dtpc_rupiah']+$v['potongan_kehadiran_rupiah'])-$v['pph21'];
                
                $upah_bersih_rupiah=$upah_neto_rupiah-($v['total_bpjs_tk']+$v['total_bpjs_ks']+$v['iuran_koperasi']+$v['iuran_serikat_rupiah']);
            
                $total_upah_thp_rupiah=$upah_neto_rupiah-($v['total_bpjs_tk']+$v['total_bpjs_ks']+$v['iuran_koperasi']+$v['iuran_serikat_rupiah'])-$v['potongan_kasbon_rupiah'];
                
                $jumlah_potongan_rupiah=($v['total_bpjs_tk']+$v['total_bpjs_ks']+$v['iuran_koperasi']+$v['iuran_serikat_rupiah']);

                $records=[
                    'kode_rekap_payroll'=>$v['kode_rekap_payroll'],
                    'periode_kehadiran'=>$v['periode_kehadiran'],
                    'periode_tahun_payroll'=>$v['periode_tahun_payroll'],
                    'periode_bulan_payroll'=>$v['periode_bulan_payroll'],
                    'enroll_id'=>$v['enroll_id'],
                    'nik'=>$v['nik'],
                    'kode_grade'=>$v['kode_grade'],
                    'join_date'=>$v['join_date'],

                    'site_nirwana_name'=>$v['site_nirwana_name'],
                    'employee_name'=>$v['employee_name'],
                    'tanggal_resign'=>$v['tanggal_resign'],
                    'kehadiran_iby'=>$v['kehadiran_iby'],
                    'kehadiran_itb'=>$v['kehadiran_itb'],
                    'kehadiran_m'=>$v['kehadiran_m'],
                    'kehadiran_dt'=>$v['kehadiran_dt'],
                    'kehadiran_pc'=>$v['kehadiran_pc'],
                    'kehadiran_dtpc'=>$v['kehadiran_dtpc'],
                    'kehadiran_lby'=>$v['kehadiran_lby'],
                    'kehadiran_lsm'=>$v['kehadiran_lsm'],
                    'kehadiran_r'=>$v['kehadiran_r'],
                    'kehadiran_ok'=>$v['kehadiran_ok'],
                    'kehadiran_tk'=>$v['kehadiran_tk'],
                    'total_kehadiran'=>$v['total_kehadiran'],
                    'total_kehadiran_net'=>$v['total_kehadiran_net'],
                    'ptkp'=>$v['ptkp'],
                    'upah_per_bulan'=>$v['upah_per_bulan'],
                    'upah_per_hari'=>$v['upah_per_hari'],
                    'upah_per_menit'=>$v['upah_per_menit'],

                    'tunjangan_karyawan_rupiah'=>$v['tunjangan_karyawan_rupiah'],
                    'premi_karyawan'=>$v['premi_karyawan'],

                    'lembur_1'=>$v['lembur_1'],
                    'lembur_2'=>$v['lembur_2'],
                    'lembur_3'=>$v['lembur_3'],
                    'lembur_4'=>$v['lembur_4'],
                    'total_lembur_1234'=>$v['total_lembur_1234'],
                    'lembur1_rupiah'=>$v['lembur1_rupiah'],
                    'lembur2_rupiah'=>$v['lembur2_rupiah'],
                    'lembur3_rupiah'=>$v['lembur3_rupiah'],
                    'lembur4_rupiah'=>$v['lembur4_rupiah'],
                    'total_lembur_rupiah'=>$v['total_lembur_rupiah'],

                    'pendapatan_lainnya_rupiah'=>$v['pendapatan_lainnya_rupiah'],

                    'koreksi_upah_rupiah'=>$v['koreksi_upah_rupiah'],
                    'koreksi_potongan_rupiah'=>$v['koreksi_potongan_rupiah'],

                    'potongan_iks_menit'=>$v['potongan_iks_menit'],
                    'potongan_dt_menit'=>$v['potongan_dt_menit'],
                    'potongan_pc_menit'=>$v['potongan_pc_menit'],
                    'potongan_dtpc_menit'=>$v['potongan_dtpc_menit'],
                    'potongan_iks_rupiah'=>$v['potongan_iks_rupiah'],
                    'potongan_dt_rupiah'=>$v['potongan_dt_rupiah'],
                    'potongan_pc_rupiah'=>$v['potongan_pc_rupiah'],
                    'potongan_dtpc_rupiah'=>$v['potongan_dtpc_rupiah'],
                    'potongan_kehadiran_rupiah'=>$v['potongan_kehadiran_rupiah'],


                    'upah_bruto_rupiah'=>$upah_bruto_rupiah??0,
                    'upah_neto_rupiah'=>$upah_neto_rupiah??0,

                    'upah_bersih_rupiah'=>$upah_bersih_rupiah??0,
                    'total_upah_thp_rupiah'=>$total_upah_thp_rupiah??0,
                    'jumlah_potongan_rupiah'=>$jumlah_potongan_rupiah??0,


                    'pph21'=>$v['pph21'], // dari mana?
                    'iuran_serikat_rupiah'=>$v['iuran_serikat_rupiah'], // dari mana?
                    'iuran_koperasi'=>$v['iuran_koperasi'], // dari mana?
                    'potongan_kasbon_rupiah'=>$v['potongan_kasbon_rupiah'], // dari mana?

                    'bpjs_tk_jkm_rupiah'=> $v['bpjs_tk_jkm_rupiah'],
                    'bpjs_tk_jkm_perusahaan_rupiah'=> $v['bpjs_tk_jkm_perusahaan_rupiah'],
                    'bpjs_tk_jkm_karyawan_rupiah'=> $v['bpjs_tk_jkm_karyawan_rupiah'],
                    'bpjs_tk_jkk_rupiah'=> $v['bpjs_tk_jkk_rupiah'],
                    'bpjs_tk_jkk_perusahaan_rupiah'=> $v['bpjs_tk_jkk_perusahaan_rupiah'],
                    'bpjs_tk_jkk_karyawan_rupiah'=> $v['bpjs_tk_jkk_karyawan_rupiah'],
                    'bpjs_tk_jht_rupiah'=> $v['bpjs_tk_jht_rupiah'],
                    'bpjs_tk_jht_perusahaan_rupiah'=> $v['bpjs_tk_jht_perusahaan_rupiah'],
                    'bpjs_tk_jht_karyawan_rupiah'=> $v['bpjs_tk_jht_karyawan_rupiah'],
                    'bpjs_tk_jpn_rupiah'=> $v['bpjs_tk_jpn_rupiah'],
                    'bpjs_tk_jpn_perusahaan_rupiah'=> $v['bpjs_tk_jpn_perusahaan_rupiah'],
                    'bpjs_tk_jpn_karyawan_rupiah'=> $v['bpjs_tk_jpn_karyawan_rupiah'],
                    'bpjs_ks_jkn_rupiah'=> $v['bpjs_ks_jkn_rupiah'],
                    'bpjs_ks_jkn_perusahaan_rupiah'=> $v['bpjs_ks_jkn_perusahaan_rupiah'],
                    'bpjs_ks_jkn_karyawan_rupiah'=> $v['bpjs_ks_jkn_karyawan_rupiah'],
                    'total_bpjs_tk'=>$v['total_bpjs_tk'],
                    'total_bpjs_ks'=>$v['total_bpjs_ks'],
                    
                    'jabatan_karyawan'=>$v['jabatan_karyawan'],
                    'nama_bagian'=>$v['nama_bagian'],
                    'nama_department'=>$v['nama_department'],
                    'kategori_karyawan'=>$v['kategori_karyawan'],
                    'aktif_karyawan'=>$v['aktif_karyawan'],
                    'jenis_kelamin'=>$v['jenis_kelamin'],
                    'status_kawin'=>$v['status_kawin'],
                    'nama_bank'=>$v['nama_bank'],
                    'nomor_rekening_bank'=>$v['nomor_rekening_bank'],
                    'npwp'=>$v['npwp'],
                    'operator'=>$v['operator'],
                ];

                $count=RekapPerhitunganPayroll::where( 'kode_rekap_payroll',$v['kode_rekap_payroll'])->count();
                if($count){
                    RekapPerhitunganPayroll::where( 'kode_rekap_payroll',$v['kode_rekap_payroll'])->update($records);
                }
                else{
                    RekapPerhitunganPayroll::create($records);

                }
            // }
        }

        return true;
    }


    // // rekap payroll
    // public function rekap_payroll($bulan_priode)
    // {
    //     // $tanggal_awal='2023-02-26';
    //     // $tanggal_akhir='2023-03-25';

    //     $bulan_sekarang1 = strtotime(date( $bulan_priode));

    //     $bulan_sebelum = strtotime("-1 month", $bulan_sekarang1);
    //     $bulan_sebelum=date('Y-m-', $bulan_sebelum);
    //     $bulan_sekarang=date('Y-m-', $bulan_sekarang1);

    //     $tanggal_awal=$bulan_sebelum.'26';
    //     $tanggal_akhir=$bulan_sekarang.'25';

    //     $karyawan=EmployeeAtribut::where('status_aktif','AKTIF')->where('join_date','<=', $tanggal_akhir)->ORwhere('tanggal_resign','>',$tanggal_awal)->get();
    //     // $karyawan=EmployeeAtribut::where('status_aktif','AKTIF')->where('enroll_id','4966')->get();

    //     $periode_payroll=$tanggal_awal.' s/d '.$tanggal_akhir;// menggunakan s/d

    //     $tanggal_awal_baru = date('m/d/Y', strtotime($tanggal_awal));
    //     $tanggal_akhir_baru = date('m/d/Y', strtotime($tanggal_akhir));
    //     $periode_payroll2 = $tanggal_awal_baru . ' - ' . $tanggal_akhir_baru;// menggunakan -

    //     $data=[];
    //     foreach ($karyawan as $key => $value) {

    //         $rekap_kehadiran=RekapPerhitunganKehadiranKaryawan::where('enroll_id',$value->enroll_id)->where('periode_payroll',$periode_payroll)->first();

    //         // $rekap_lembur=RekapPerhitunganLembur::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->get();
        
    //         $rekap_iks=RekapPerhitunganIKS::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->get();
            
    //         // $rekap_dtpc=RekapPerhitunganDTPC::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->get();

    //         $Bpjs=EmployeeBpjs::where('enroll_id',$value->enroll_id)->where('periode_kehadiran',$periode_payroll)->first();
            
    //         $koreksi_upah=DataKoreksiUpah::where('enroll_id',$value->enroll_id)->where('periode_tanggal_koreksi',$periode_payroll2)->get();

    //         $koreksi_potongan=DataKoreksiPotongan::where('enroll_id',$value->enroll_id)->where('periode_tanggal_koreksi',$periode_payroll2)->get();

    //         $tunjangan=TunjanganKaryawan::where('enroll_id',$value->enroll_id)->where('periode_payroll',$periode_payroll)->first();
           
           
    //         $lembur_1=RekapPerhitunganLembur::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->sum('lembur_1');
    //         $lembur_2=RekapPerhitunganLembur::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->sum('lembur_2');
    //         $lembur_3=RekapPerhitunganLembur::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->sum('lembur_3');
    //         $lembur_4=RekapPerhitunganLembur::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->sum('lembur_4');
    //         $total_lembur_1234=RekapPerhitunganLembur::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->sum('total_lembur_1234');
    //         $lembur1_rupiah=RekapPerhitunganLembur::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->sum('lembur1_rupiah');
    //         $lembur2_rupiah=RekapPerhitunganLembur::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->sum('lembur2_rupiah');
    //         $lembur3_rupiah=RekapPerhitunganLembur::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->sum('lembur3_rupiah');
    //         $lembur4_rupiah=RekapPerhitunganLembur::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->sum('lembur4_rupiah');
    //         $total_lembur_rupiah=RekapPerhitunganLembur::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->sum('total_lembur_rupiah');
            

    //         $potongan_dt_menit=RekapPerhitunganDTPC::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->sum('jumlah_menit_absen_dt');
    //         $potongan_pc_menit=RekapPerhitunganDTPC::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->sum('jumlah_menit_absen_pc');
    //         $potongan_dtpc_menit=RekapPerhitunganDTPC::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->sum('jumlah_menit_absen_dtpc');
    //         $potongan_dt_rupiah=RekapPerhitunganDTPC::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->sum('potongan_dt_rupiah');
    //         $potongan_pc_rupiah=RekapPerhitunganDTPC::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->sum('potongan_pc_rupiah');
    //         $potongan_dtpc_rupiah=RekapPerhitunganDTPC::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->sum('potongan_dtpc_rupiah');


    //         if($rekap_kehadiran!=null){
    //             list($periode_tahun_payroll, $periode_bulan_payroll) = explode("-", $rekap_kehadiran->periode_tahun_bulan);
    //             //tunjangan
    //             $tanggal_masuk = $value->join_date;

    //             // hitung selisih tahun antara tanggal masuk dan sekarang
    //             $selisih_tahun = date_diff(date_create($tanggal_masuk), date_create($tanggal_akhir))->y;

    //             // tentukan besaran tunjangan berdasarkan masa kerja
    //             if ($selisih_tahun < 1) {
    //                 $tunjangan = 0;
    //             } elseif ($selisih_tahun < 3) {
    //                 $tunjangan = 2500;
    //             } elseif ($selisih_tahun < 6) {
    //                 $tunjangan = 5000;
    //             }elseif ($selisih_tahun < 9) {
    //                 $tunjangan = 7500;
    //             }elseif ($selisih_tahun < 12) {
    //                 $tunjangan = 10000;
    //             }else{
    //                 $tunjangan = 12500;
    //             }


    //             $bpjs_tk_jkm_bruto_rupiah=$Bpjs->bpjs_tk_jkm_bruto_rupiah??0;
    //             $bpjs_tk_jkm_neto_rupiah=$Bpjs->bpjs_tk_jkm_neto_rupiah??0;
    //             $bpjs_tk_jkk_bruto_rupiah=$Bpjs->bpjs_tk_jkk_bruto_rupiah??0;
    //             $bpjs_tk_jkk_neto_rupiah=$Bpjs->bpjs_tk_jkk_neto_rupiah??0;
    //             $bpjs_tk_jht_bruto_rupiah=$Bpjs->bpjs_tk_jht_bruto_rupiah??0;
    //             $bpjs_tk_jht_neto_rupiah=$Bpjs->bpjs_tk_jht_neto_rupiah??0;
    //             $bpjs_tk_jpn_bruto_rupiah=$Bpjs->bpjs_tk_jpn_bruto_rupiah??0;
    //             $bpjs_tk_jpn_neto_rupiah=$Bpjs->bpjs_tk_jpn_neto_rupiah??0;
    //             $bpjs_ks_jkn_bruto_rupiah=$Bpjs->bpjs_ks_jkn_bruto_rupiah??0;
    //             $bpjs_ks_jkn_neto_rupiah=$Bpjs->bpjs_ks_jkn_neto_rupiah??0;

    

    //             $data[]=[
    //                 'kode_rekap_payroll'=>$rekap_kehadiran->kode_rekap,
    //                 'periode_kehadiran'=>$rekap_kehadiran->periode_payroll,
    //                 'periode_tahun_payroll'=>$periode_tahun_payroll,
    //                 'periode_bulan_payroll'=>$periode_bulan_payroll,
    //                 'enroll_id'=>$value->enroll_id,
    //                 'nik'=>$value->nik,
    //                 'kode_grade'=>$value->kode_grade,
    //                 'join_date'=>$value->join_date,
    //                 'employee_name'=>$value->employee_name,
    //                 'tanggal_resign'=>$value->tanggal_resign,
    //                 'kehadiran_iby'=>$rekap_kehadiran->kehadiran_iby,
    //                 'kehadiran_itb'=>$rekap_kehadiran->kehadiran_itb,
    //                 'kehadiran_m'=>$rekap_kehadiran->kehadiran_m,
    //                 'kehadiran_dt'=>$rekap_kehadiran->kehadiran_dt,
    //                 'kehadiran_pc'=>$rekap_kehadiran->kehadiran_pc,
    //                 'kehadiran_dtpc'=>$rekap_kehadiran->kehadiran_dtpc,
    //                 'kehadiran_lby'=>$rekap_kehadiran->kehadiran_lby,
    //                 'kehadiran_lsm'=>$rekap_kehadiran->kehadiran_lsm,
    //                 'kehadiran_r'=>$rekap_kehadiran->kehadiran_r,
    //                 'kehadiran_ok'=>$rekap_kehadiran->kehadiran_ok,
    //                 'kehadiran_tk'=>$rekap_kehadiran->kehadiran_tk,
    //                 'total_kehadiran'=>$rekap_kehadiran->total_kehadiran,
    //                 'total_kehadiran_net'=>$rekap_kehadiran->total_kehadiran_net,
    //                 'ptkp'=>$value->ptkp,
    //                 'upah_per_bulan'=>$rekap_kehadiran->gaji_pokok,
    //                 'upah_per_hari'=>$rekap_kehadiran->gaji_harian,
    //                 'upah_per_menit'=>$rekap_kehadiran->gaji_menit,

    //                 'tunjangan_karyawan_rupiah'=>$tunjangan,
    //                 'premi_karyawan'=>0,

    //                 'lembur_1'=>$lembur_1,
    //                 'lembur_2'=>$lembur_2,
    //                 'lembur_3'=>$lembur_3,
    //                 'lembur_4'=>$lembur_4,
    //                 'total_lembur_1234'=>$total_lembur_1234,
    //                 'lembur1_rupiah'=>$lembur1_rupiah,
    //                 'lembur2_rupiah'=>$lembur2_rupiah,
    //                 'lembur3_rupiah'=>$lembur3_rupiah,
    //                 'lembur4_rupiah'=>$lembur4_rupiah,
    //                 'total_lembur_rupiah'=>$total_lembur_rupiah,

    //                 'pendapatan_lainnya_rupiah'=>0,

    //                 'koreksi_upah_rupiah'=>$koreksi_upah->sum('jumlah_rp_potongan')??0,
    //                 'koreksi_potongan_rupiah'=>$koreksi_potongan->sum('jumlah_rp_potongan')??0,

    //                 'potongan_iks_menit'=>$rekap_iks->sum('lama_ijin_menit')??0,
    //                 'potongan_iks_rupiah'=>$rekap_iks->sum('potongan_iks_rupiah')??0,

    //                 'potongan_dt_menit'=>$potongan_dt_menit,
    //                 'potongan_pc_menit'=>$potongan_pc_menit,
    //                 'potongan_dtpc_menit'=>$potongan_dtpc_menit,
    //                 'potongan_dt_rupiah'=>$potongan_dt_rupiah,
    //                 'potongan_pc_rupiah'=>$potongan_pc_rupiah,
    //                 'potongan_dtpc_rupiah'=>$potongan_dtpc_rupiah,
    //                 'potongan_kehadiran_rupiah'=>$rekap_kehadiran->potongan_kehadiran_rupiah,


    //                 // 'upah_bruto_rupiah'=>$value,
    //                 // 'upah_neto_rupiah'=>$value,

    //                 // 'upah_bersih_rupiah'=>$value,
    //                 // 'total_upah_thp_rupiah'=>$value,
    //                 // 'jumlah_potongan_rupiah'=>$value,


    //                 'pph21'=>0, // dari mana?
    //                 'iuran_serikat_rupiah'=>0, // dari mana?
    //                 'iuran_koperasi'=>0, // dari mana?
    //                 'potongan_kasbon_rupiah'=>0, // dari mana?

    //                 'bpjs_tk_jkm_rupiah'=> $bpjs_tk_jkm_bruto_rupiah + $bpjs_tk_jkm_neto_rupiah,
    //                 'bpjs_tk_jkm_perusahaan_rupiah'=> $bpjs_tk_jkm_bruto_rupiah,
    //                 'bpjs_tk_jkm_karyawan_rupiah'=> $bpjs_tk_jkm_neto_rupiah,
    //                 'bpjs_tk_jkk_rupiah'=> $bpjs_tk_jkk_bruto_rupiah + $bpjs_tk_jkk_neto_rupiah,
    //                 'bpjs_tk_jkk_perusahaan_rupiah'=> $bpjs_tk_jkk_bruto_rupiah,
    //                 'bpjs_tk_jkk_karyawan_rupiah'=> $bpjs_tk_jkk_neto_rupiah,
    //                 'bpjs_tk_jht_rupiah'=> $bpjs_tk_jht_bruto_rupiah + $bpjs_tk_jht_neto_rupiah,
    //                 'bpjs_tk_jht_perusahaan_rupiah'=> $bpjs_tk_jht_bruto_rupiah,
    //                 'bpjs_tk_jht_karyawan_rupiah'=> $bpjs_tk_jht_neto_rupiah,
    //                 'bpjs_tk_jpn_rupiah'=> $bpjs_tk_jpn_bruto_rupiah + $bpjs_tk_jpn_neto_rupiah,
    //                 'bpjs_tk_jpn_perusahaan_rupiah'=> $bpjs_tk_jpn_bruto_rupiah,
    //                 'bpjs_tk_jpn_karyawan_rupiah'=> $bpjs_tk_jpn_neto_rupiah,
    //                 'bpjs_ks_jkn_rupiah'=> $bpjs_ks_jkn_bruto_rupiah + $bpjs_ks_jkn_neto_rupiah,
    //                 'bpjs_ks_jkn_perusahaan_rupiah'=> $bpjs_ks_jkn_bruto_rupiah,
    //                 'bpjs_ks_jkn_karyawan_rupiah'=> $bpjs_ks_jkn_neto_rupiah,
    //                 'total_bpjs_tk'=>$bpjs_tk_jkm_neto_rupiah + $bpjs_tk_jkk_neto_rupiah + $bpjs_tk_jht_neto_rupiah + $bpjs_tk_jpn_neto_rupiah,
    //                 'total_bpjs_ks'=>$bpjs_ks_jkn_neto_rupiah,
                    
    //                 'jabatan_karyawan'=>$value->status_jabatan,
    //                 'nama_bagian'=>$value->dept()->first()->sub_dept_name??'',
    //                 'nama_department'=>$value->dept()->first()->department_name??'',
    //                 'kategori_karyawan'=>$value->status_staff,
    //                 'aktif_karyawan'=>$value->status_aktif,
    //                 'jenis_kelamin'=>$value->jenis_kelamin,
    //                 'site_nirwana_name'=>$value->site_nirwana_name,
    //                 'nama_bank'=>$value->nama_bank==null||$value->nama_bank==""||$value->nama_bank=="-"?'TUNAI':$value->nama_bank,
    //                 'nomor_rekening_bank'=>$value->nomor_rekening_bank==null||$value->nomor_rekening_bank==""||$value->nomor_rekening_bank=="-"?'TRANSFER':$value->nomor_rekening_bank,
    //                 'npwp'=>$value->npwp,
    //                 'operator'=>'sistem',
    //             ];
    //         }
    //     }

    //     $records=[];

    //         foreach ($data as $k => $v) {
    //             $upah_bruto_rupiah=($v['upah_per_bulan']+$v['tunjangan_karyawan_rupiah']+$v['premi_karyawan']+$v['total_lembur_rupiah']+$v['pendapatan_lainnya_rupiah']+$v['koreksi_upah_rupiah'])-
    //                 ($v['koreksi_potongan_rupiah']+$v['potongan_iks_rupiah']+$v['potongan_dtpc_rupiah']+$v['potongan_kehadiran_rupiah']);
                
    //             $upah_neto_rupiah=($v['upah_per_bulan']+$v['tunjangan_karyawan_rupiah']+$v['premi_karyawan']+$v['total_lembur_rupiah']+$v['pendapatan_lainnya_rupiah']+$v['koreksi_upah_rupiah'])-
    //                 ($v['koreksi_potongan_rupiah']+$v['potongan_iks_rupiah']+$v['potongan_dtpc_rupiah']+$v['potongan_kehadiran_rupiah'])-$v['pph21'];
                
    //             $upah_bersih_rupiah=$upah_neto_rupiah-($v['total_bpjs_tk']+$v['total_bpjs_ks']+$v['iuran_koperasi']+$v['iuran_serikat_rupiah']);
            
    //             $total_upah_thp_rupiah=$upah_neto_rupiah-($v['total_bpjs_tk']+$v['total_bpjs_ks']+$v['iuran_koperasi']+$v['iuran_serikat_rupiah'])-$v['potongan_kasbon_rupiah'];
                
    //             $jumlah_potongan_rupiah=($v['total_bpjs_tk']+$v['total_bpjs_ks']+$v['iuran_koperasi']+$v['iuran_serikat_rupiah']);

    //             $records=[
    //                 'kode_rekap_payroll'=>$v['kode_rekap_payroll'],
    //                 'periode_kehadiran'=>$v['periode_kehadiran'],
    //                 'periode_tahun_payroll'=>$v['periode_tahun_payroll'],
    //                 'periode_bulan_payroll'=>$v['periode_bulan_payroll'],
    //                 'enroll_id'=>$v['enroll_id'],
    //                 'nik'=>$v['nik'],
    //                 'kode_grade'=>$v['kode_grade'],
    //                 'join_date'=>$v['join_date'],

    //                 'site_nirwana_name'=>$v['site_nirwana_name'],
    //                 'employee_name'=>$v['employee_name'],
    //                 'tanggal_resign'=>$v['tanggal_resign'],
    //                 'kehadiran_iby'=>$v['kehadiran_iby'],
    //                 'kehadiran_itb'=>$v['kehadiran_itb'],
    //                 'kehadiran_m'=>$v['kehadiran_m'],
    //                 'kehadiran_dt'=>$v['kehadiran_dt'],
    //                 'kehadiran_pc'=>$v['kehadiran_pc'],
    //                 'kehadiran_dtpc'=>$v['kehadiran_dtpc'],
    //                 'kehadiran_lby'=>$v['kehadiran_lby'],
    //                 'kehadiran_lsm'=>$v['kehadiran_lsm'],
    //                 'kehadiran_r'=>$v['kehadiran_r'],
    //                 'kehadiran_ok'=>$v['kehadiran_ok'],
    //                 'kehadiran_tk'=>$v['kehadiran_tk'],
    //                 'total_kehadiran'=>$v['total_kehadiran'],
    //                 'total_kehadiran_net'=>$v['total_kehadiran_net'],
    //                 'ptkp'=>$v['ptkp'],
    //                 'upah_per_bulan'=>$v['upah_per_bulan'],
    //                 'upah_per_hari'=>$v['upah_per_hari'],
    //                 'upah_per_menit'=>$v['upah_per_menit'],

    //                 'tunjangan_karyawan_rupiah'=>$v['tunjangan_karyawan_rupiah'],
    //                 'premi_karyawan'=>$v['premi_karyawan'],

    //                 'lembur_1'=>$v['lembur_1'],
    //                 'lembur_2'=>$v['lembur_2'],
    //                 'lembur_3'=>$v['lembur_3'],
    //                 'lembur_4'=>$v['lembur_4'],
    //                 'total_lembur_1234'=>$v['total_lembur_1234'],
    //                 'lembur1_rupiah'=>$v['lembur1_rupiah'],
    //                 'lembur2_rupiah'=>$v['lembur2_rupiah'],
    //                 'lembur3_rupiah'=>$v['lembur3_rupiah'],
    //                 'lembur4_rupiah'=>$v['lembur4_rupiah'],
    //                 'total_lembur_rupiah'=>$v['total_lembur_rupiah'],

    //                 'pendapatan_lainnya_rupiah'=>$v['pendapatan_lainnya_rupiah'],

    //                 'koreksi_upah_rupiah'=>$v['koreksi_upah_rupiah'],
    //                 'koreksi_potongan_rupiah'=>$v['koreksi_potongan_rupiah'],

    //                 'potongan_iks_menit'=>$v['potongan_iks_menit'],
    //                 'potongan_dt_menit'=>$v['potongan_dt_menit'],
    //                 'potongan_pc_menit'=>$v['potongan_pc_menit'],
    //                 'potongan_dtpc_menit'=>$v['potongan_dtpc_menit'],
    //                 'potongan_iks_rupiah'=>$v['potongan_iks_rupiah'],
    //                 'potongan_dt_rupiah'=>$v['potongan_dt_rupiah'],
    //                 'potongan_pc_rupiah'=>$v['potongan_pc_rupiah'],
    //                 'potongan_dtpc_rupiah'=>$v['potongan_dtpc_rupiah'],
    //                 'potongan_kehadiran_rupiah'=>$v['potongan_kehadiran_rupiah'],


    //                 'upah_bruto_rupiah'=>$upah_bruto_rupiah??0,
    //                 'upah_neto_rupiah'=>$upah_neto_rupiah??0,

    //                 'upah_bersih_rupiah'=>$upah_bersih_rupiah??0,
    //                 'total_upah_thp_rupiah'=>$total_upah_thp_rupiah??0,
    //                 'jumlah_potongan_rupiah'=>$jumlah_potongan_rupiah??0,


    //                 'pph21'=>$v['pph21'], // dari mana?
    //                 'iuran_serikat_rupiah'=>$v['iuran_serikat_rupiah'], // dari mana?
    //                 'iuran_koperasi'=>$v['iuran_koperasi'], // dari mana?
    //                 'potongan_kasbon_rupiah'=>$v['potongan_kasbon_rupiah'], // dari mana?

    //                 'bpjs_tk_jkm_rupiah'=> $v['bpjs_tk_jkm_rupiah'],
    //                 'bpjs_tk_jkm_perusahaan_rupiah'=> $v['bpjs_tk_jkm_perusahaan_rupiah'],
    //                 'bpjs_tk_jkm_karyawan_rupiah'=> $v['bpjs_tk_jkm_karyawan_rupiah'],
    //                 'bpjs_tk_jkk_rupiah'=> $v['bpjs_tk_jkk_rupiah'],
    //                 'bpjs_tk_jkk_perusahaan_rupiah'=> $v['bpjs_tk_jkk_perusahaan_rupiah'],
    //                 'bpjs_tk_jkk_karyawan_rupiah'=> $v['bpjs_tk_jkk_karyawan_rupiah'],
    //                 'bpjs_tk_jht_rupiah'=> $v['bpjs_tk_jht_rupiah'],
    //                 'bpjs_tk_jht_perusahaan_rupiah'=> $v['bpjs_tk_jht_perusahaan_rupiah'],
    //                 'bpjs_tk_jht_karyawan_rupiah'=> $v['bpjs_tk_jht_karyawan_rupiah'],
    //                 'bpjs_tk_jpn_rupiah'=> $v['bpjs_tk_jpn_rupiah'],
    //                 'bpjs_tk_jpn_perusahaan_rupiah'=> $v['bpjs_tk_jpn_perusahaan_rupiah'],
    //                 'bpjs_tk_jpn_karyawan_rupiah'=> $v['bpjs_tk_jpn_karyawan_rupiah'],
    //                 'bpjs_ks_jkn_rupiah'=> $v['bpjs_ks_jkn_rupiah'],
    //                 'bpjs_ks_jkn_perusahaan_rupiah'=> $v['bpjs_ks_jkn_perusahaan_rupiah'],
    //                 'bpjs_ks_jkn_karyawan_rupiah'=> $v['bpjs_ks_jkn_karyawan_rupiah'],
    //                 'total_bpjs_tk'=>$v['total_bpjs_tk'],
    //                 'total_bpjs_ks'=>$v['total_bpjs_ks'],
                    
    //                 'jabatan_karyawan'=>$v['jabatan_karyawan'],
    //                 'nama_bagian'=>$v['nama_bagian'],
    //                 'nama_department'=>$v['nama_department'],
    //                 'kategori_karyawan'=>$v['kategori_karyawan'],
    //                 'aktif_karyawan'=>$v['aktif_karyawan'],
    //                 'jenis_kelamin'=>$v['jenis_kelamin'],
    //                 'nama_bank'=>$v['nama_bank'],
    //                 'nomor_rekening_bank'=>$v['nomor_rekening_bank'],
    //                 'npwp'=>$v['npwp'],
    //                 'operator'=>$v['operator'],
    //             ];

    //             $count=RekapPerhitunganPayroll::where( 'kode_rekap_payroll',$v['kode_rekap_payroll'])->count();
    //             if($count){
    //                 RekapPerhitunganPayroll::where( 'kode_rekap_payroll',$v['kode_rekap_payroll'])->update($records);
    //             }
    //             else{
    //                 RekapPerhitunganPayroll::create($records);

    //             }
    //         }

    //     return true;
    // }

   

   public function index(Request $request)
    {
        $bulan=$request->periode_payrol;

        $rekap_absen = $this->rekap_absen( $bulan);

        $rekap_absen_perhitungan = $this->rekap_absen_perhitungan( $bulan); //lama 5 menit

        $rekap_lembur = $this->rekap_lembur($bulan);

        $rekap_iks = $this->rekap_iks($bulan); 
        
        $update_bpjs = $this->update_bpjs($bulan); // 3 menit

        $dt_pc = $this->dt_pc($bulan); //lama 8 menit

        $rekap_payroll = $this->rekap_payroll($bulan);

        return true;

  
    }


    // rekap_absen
    public function index1()
    {
        $bulan_priode='2023-04';
        $bulan_sekarang1 = strtotime(date( $bulan_priode));
     
        $bulan_sebelum = strtotime("-1 month", $bulan_sekarang1);
        $bulan_sebelum=date('Y-m-', $bulan_sebelum);
        $bulan_sekarang=date('Y-m-', $bulan_sekarang1);

        $tanggal_awal=$bulan_sebelum.'26';
        $tanggal_akhir=$bulan_sekarang.'25';

        $bulan=date('m', $bulan_sekarang1);
        $tahun=date('Y', $bulan_sekarang1);
        $ijin_bayar=RefAbsenIjin::where('kode_ijin_payroll','IBY')->get()->toArray();
        $IBY=array_column($ijin_bayar,'kode_absen_ijin');

        $tidak_bayar=RefAbsenIjin::where('kode_ijin_payroll','ITB')->where('kode_absen_ijin','!=','M')->where('kode_absen_ijin','!=','IKS')->get()->toArray();
        $ITB=array_column($tidak_bayar,'kode_absen_ijin');

        $timestamp1 = strtotime($tanggal_awal);
        $timestamp2 = strtotime($tanggal_akhir);
        $jumlah_hari =(abs($timestamp2 - $timestamp1) / (60 * 60 * 24)+1);

        $jumlah_hari_sabtu_minggu = 0;

        $security=EmployeeAtribut::where('sub_dept_id','DEP08SUB005')->where('jenis_kelamin','LAKI-LAKI')->get();



        for ($i = strtotime($tanggal_awal); $i <= strtotime($tanggal_akhir); $i += 86400) {
            if ((date('N', $i) == 6)||(date('N', $i) == 7)) {
                $jumlah_hari_sabtu_minggu++;
            }
        }

        // $x=MasterDataAbsenKehadiran::where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->where('department_id','DEP10')->get()->groupby('enroll_id');
        $x=MasterDataAbsenKehadiran::where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->where('enroll_id','1757')->get()->groupby('enroll_id');
        // $x=MasterDataAbsenKehadiran::where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->get()->groupby('enroll_id');
        foreach ($x as $key => $value) {
            $IBY_employe=$value->wherein('status_absen', $IBY)->count();
            $LBY_employe=$value->where('status_absen','LN')->whereNotin('kode_hari', ['5','6'])->count();
            $ITB_employe=$value->wherein('status_absen', $ITB)->count();

            $lsm_employe=$value->wherein('kode_hari', ['5','6'])->where('mulai_jam_kerja',null)->count();
            $dt_employe=$value->where('jumlah_menit_absen_dt','>','0')->where('jumlah_menit_absen_pc','0')->count();
            $pc_employe=$value->where('jumlah_menit_absen_pc','>','0')->where('jumlah_menit_absen_dt','0')->count();
            $dtpc_employe=$value->where('jumlah_menit_absen_dt','>','0')->where('jumlah_menit_absen_pc','>','0')->count();

            $absen_M=$value->where('status_absen','M')->count();
            $absen_R=$value->where('status_absen','R')->count();
            $absen_TL=$value->where('status_absen','TL')->count();
            $absen_IKS=$value->where('status_absen','IKS')->where('jumlah_menit_absen_dtpc','0')->count();
            // $absen_ok=$value->where('absen_masuk_kerja','!=',null)->where('absen_pulang_kerja','!=',null)
            //                 ->where('mulai_jam_kerja','!=',null)->where('akhir_jam_kerja','!=',null)
            //                 ->where('status_absen',null)->where('jumlah_menit_absen_dtpc','0')->count();
            $absen_ok=$value->where('mulai_jam_kerja','!=',null)->where('status_absen',null)->where('jumlah_menit_absen_dtpc','0')->count();
            $absen_ok=$absen_ok+$absen_IKS;
            $unik=$angka_string =sprintf("%04d", $key);
            $kode_rekap_kehadiran=$bulan_sebelum.$unik;

            $total_kehadiran=$IBY_employe+$ITB_employe+$lsm_employe+$dtpc_employe+$absen_M+$absen_R+$absen_ok+$LBY_employe+$dt_employe+$pc_employe+$absen_TL;
            // $kehadiran_tk=$jumlah_hari-($total_kehadiran+$absen_TL);
           
            $total_kehadiran_net=$absen_ok+$dt_employe+$pc_employe+$dtpc_employe;
            $aa=$total_kehadiran_net+$ITB_employe+$LBY_employe+$IBY_employe+$lsm_employe+$absen_M+$absen_TL+$absen_R;
            $kehadiran_tk=$jumlah_hari-$aa;

            if($security->where('enroll_id',$value->first()->enroll_id)->count()){
                $jumlah_hari_kerja=25;

            }else{
                $jumlah_hari_kerja=$jumlah_hari-$jumlah_hari_sabtu_minggu;

            }


            $y=[
                'uuid'=>Str::uuid('uuid'),
                'kode_rekap_kehadiran'=> $kode_rekap_kehadiran,
                'periode_payroll'=>$tanggal_awal.' s/d '.$tanggal_akhir,
                'periode_tahun'=>$tahun,
                'periode_bulan'=>$bulan,
                'enroll_id'=>$value->first()->enroll_id,
                'nik'=>$value->first()->nik,
                'employee_name'=>$value->first()->employee_name,
                'site_nirwana_id'=>$value->first()->site_nirwana_id,
                'site_nirwana_name'=>$value->first()->site_nirwana_name,
                'department_id'=>$value->first()->department_id,
                'department_name'=>$value->first()->department_name,
                'sub_dept_id'=>$value->first()->sub_dept_id,
                'sub_dept_name'=>$value->first()->sub_dept_name,
                'join_date'=>$value->first()->join_date,
                'tanggal_resign'=>$value->first()->tanggal_resign,
                'status_aktif'=>$value->first()->status_aktif,
                'status_staff'=>$value->first()->status_staff,
                'kehadiran_iby'=>$IBY_employe,
                'kehadiran_itb'=>$ITB_employe,
                'kehadiran_lby'=>$LBY_employe,
                'kehadiran_lsm'=>$lsm_employe,
                'kehadiran_dt'=> $dt_employe,
                'kehadiran_pc'=> $pc_employe,
                'kehadiran_dtpc'=>$dtpc_employe,
                'kehadiran_m'=>$absen_M+$absen_TL,
                'kehadiran_r'=>$absen_R,
                'kehadiran_tk'=>$kehadiran_tk,
                'kehadiran_ok'=>$absen_ok,
                'total_kehadiran'=> $total_kehadiran,
                // 'total_kehadiran'=> $total_kehadiran+$kehadiran_tk,
                'total_kehadiran_net'=>$absen_ok+$dt_employe+$pc_employe+$dtpc_employe+$LBY_employe+$IBY_employe,
                'jumlah_hari'=>$jumlah_hari,
                'jumlah_hari_kerja'=> $jumlah_hari_kerja,
            ];
            dd($y);
            // $count=RekapKehadiranKaryawan::where( 'kode_rekap_kehadiran',$kode_rekap_kehadiran)->count();
            // if($count){
            //     RekapKehadiranKaryawan::where( 'kode_rekap_kehadiran',$kode_rekap_kehadiran)->update($y);
            // }
            // else{
            //     RekapKehadiranKaryawan::create($y);

            // }
        }
        return true;
    
    }


    // rekap payrol
    // public function rekap_payroll($bulan_priode)
    // {
    //     // $tanggal_awal='2023-02-26';
    //     // $tanggal_akhir='2023-03-25';

    //     $bulan_sekarang1 = strtotime(date( $bulan_priode));

    //     $bulan_sebelum = strtotime("-1 month", $bulan_sekarang1);
    //     $bulan_sebelum=date('Y-m-', $bulan_sebelum);
    //     $bulan_sekarang=date('Y-m-', $bulan_sekarang1);

    //     $tanggal_awal=$bulan_sebelum.'26';
    //     $tanggal_akhir=$bulan_sekarang.'25';

    //     $karyawan=EmployeeAtribut::where('status_aktif','AKTIF')->where('join_date','<=', $tanggal_akhir)->ORwhere('tanggal_resign','>=',$tanggal_awal)->get();
    //     // $karyawan=EmployeeAtribut::where('status_aktif','AKTIF')->where('enroll_id','4954')->get();

    //     $periode_payroll=$tanggal_awal.' s/d '.$tanggal_akhir;// menggunakan s/d

    //     $tanggal_awal_baru = date('m/d/Y', strtotime($tanggal_awal));
    //     $tanggal_akhir_baru = date('m/d/Y', strtotime($tanggal_akhir));
    //     $periode_payroll2 = $tanggal_awal_baru . ' - ' . $tanggal_akhir_baru;// menggunakan -

    //     $data=[];
    //     foreach ($karyawan as $key => $value) {

    //         // 'lembur_1'=>$rekap_lembur->sum('lembur_1')??0,
    //         // 'lembur_2'=>$rekap_lembur->sum('lembur_2')??0,
    //         // 'lembur_3'=>$rekap_lembur->sum('lembur_3')??0,
    //         // 'lembur_4'=>$rekap_lembur->sum('lembur_4')??0,
    //         // 'total_lembur_1234'=>$rekap_lembur->sum('total_lembur_1234')??0,
    //         // 'lembur1_rupiah'=>$rekap_lembur->sum('lembur1_rupiah')??0,
    //         // 'lembur2_rupiah'=>$rekap_lembur->sum('lembur2_rupiah')??0,
    //         // 'lembur3_rupiah'=>$rekap_lembur->sum('lembur3_rupiah')??0,
    //         // 'lembur4_rupiah'=>$rekap_lembur->sum('lembur4_rupiah')??0,
    //         // 'total_lembur_rupiah'=>$rekap_lembur->sum('total_lembur_rupiah')??0,

    //         $rekap_kehadiran=RekapPerhitunganKehadiranKaryawan::where('enroll_id',$value->enroll_id)->where('periode_payroll',$periode_payroll)->first();

    //         $rekap_lembur=RekapPerhitunganLembur::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->get();
    //         $lembur_1=collect( $rekap_lembur)->sum('lembur_1');
    //         $lembur_2=collect( $rekap_lembur)->sum('lembur_2');
    //         $lembur_3=collect( $rekap_lembur)->sum('lembur_3');
    //         $lembur_4=collect( $rekap_lembur)->sum('lembur_4');
    //         $total_lembur_1234=collect( $rekap_lembur)->sum('total_lembur_1234');
    //         $lembur1_rupiah=collect( $rekap_lembur)->sum('lembur1_rupiah');
    //         $lembur2_rupiah=collect( $rekap_lembur)->sum('lembur2_rupiah');
    //         $lembur3_rupiah=collect( $rekap_lembur)->sum('lembur3_rupiah');
    //         $lembur4_rupiah=collect( $rekap_lembur)->sum('lembur4_rupiah');
    //         $total_lembur_rupiah=collect( $rekap_lembur)->sum('total_lembur_rupiah');

    //         $rekap_iks=RekapPerhitunganIKS::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->get();
    //         $rekap_dtpc=RekapPerhitunganDTPC::where('enroll_id',$value->enroll_id)->where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->get();

            
    //         $potongan_iks_menit=collect($rekap_iks)->sum('lama_ijin_menit');
    //         $potongan_dt_menit=collect($rekap_dtpc)->sum('jumlah_menit_absen_dt');
    //         $potongan_pc_menit=collect($rekap_dtpc)->sum('jumlah_menit_absen_pc');
    //         $potongan_dtpc_menit=collect($rekap_dtpc)->sum('jumlah_menit_absen_dtpc');

    //         $potongan_iks_rupiah=collect($rekap_iks)->sum('potongan_iks_rupiah');
    //         $potongan_dt_rupiah=collect($rekap_dtpc)->sum('potongan_dt_rupiah');
    //         $potongan_pc_rupiah=collect($rekap_dtpc)->sum('potongan_pc_rupiah');
    //         $potongan_dtpc_rupiah=collect($rekap_dtpc)->sum('potongan_dtpc_rupiah');
            
    //         $Bpjs=EmployeeBpjs::where('enroll_id',$value->enroll_id)->where('periode_kehadiran',$periode_payroll)->first();

    //         // 'koreksi_upah_rupiah'=>$koreksi_upah->sum('jumlah_rp_potongan')??0,
    //         // 'koreksi_potongan_rupiah'=>$koreksi_potongan->sum('jumlah_rp_potongan')??0,
    //         $koreksi_upah_rupiah=DataKoreksiUpah::where('enroll_id',$value->enroll_id)->where('periode_tanggal_koreksi',$periode_payroll2)->sum('jumlah_rp_potongan');

    //         $koreksi_potongan_rupiah=DataKoreksiPotongan::where('enroll_id',$value->enroll_id)->where('periode_tanggal_koreksi',$periode_payroll2)->sum('jumlah_rp_potongan');

    //         $tunjangan=TunjanganKaryawan::where('enroll_id',$value->enroll_id)->where('periode_payroll',$periode_payroll)->first();

    //         list($periode_tahun_payroll, $periode_bulan_payroll) = explode("-", $rekap_kehadiran->periode_tahun_bulan??null);

    //         //tunjangan
    //         $tanggal_masuk = $value->join_date;

    //         // hitung selisih tahun antara tanggal masuk dan sekarang
    //         $selisih_tahun = date_diff(date_create($tanggal_masuk), date_create($tanggal_akhir))->y;

    //         // tentukan besaran tunjangan berdasarkan masa kerja
    //         if ($selisih_tahun < 1) {
    //             $tunjangan = 0;
    //         } elseif ($selisih_tahun < 3) {
    //             $tunjangan = 2500;
    //         } elseif ($selisih_tahun < 6) {
    //             $tunjangan = 5000;
    //         }elseif ($selisih_tahun < 9) {
    //             $tunjangan = 7500;
    //         }elseif ($selisih_tahun < 12) {
    //             $tunjangan = 10000;
    //         }else{
    //             $tunjangan = 12500;
    //         }

    //         $data[]=[
    //             'kode_rekap_payroll'=>$rekap_kehadiran->kode_rekap,
    //             'periode_kehadiran'=>$rekap_kehadiran->periode_payroll,
    //             'periode_tahun_payroll'=>$periode_tahun_payroll,
    //             'periode_bulan_payroll'=>$periode_bulan_payroll,
    //             'enroll_id'=>$value->enroll_id,
    //             'nik'=>$value->nik,
    //             'kode_grade'=>$value->kode_grade,
    //             'employee_name'=>$value->employee_name,
    //             'tanggal_resign'=>$value->tanggal_resign,
    //             'kehadiran_iby'=>$rekap_kehadiran->kehadiran_iby,
    //             'kehadiran_itb'=>$rekap_kehadiran->kehadiran_itb,
    //             'kehadiran_m'=>$rekap_kehadiran->kehadiran_m,
    //             'kehadiran_dt'=>$rekap_kehadiran->kehadiran_dt,
    //             'kehadiran_pc'=>$rekap_kehadiran->kehadiran_pc,
    //             'kehadiran_dtpc'=>$rekap_kehadiran->kehadiran_dtpc,
    //             'kehadiran_lby'=>$rekap_kehadiran->kehadiran_lby,
    //             'kehadiran_lsm'=>$rekap_kehadiran->kehadiran_lsm,
    //             'kehadiran_r'=>$rekap_kehadiran->kehadiran_r,
    //             'kehadiran_ok'=>$rekap_kehadiran->kehadiran_ok,
    //             'kehadiran_tk'=>$rekap_kehadiran->kehadiran_tk,
    //             'total_kehadiran'=>$rekap_kehadiran->total_kehadiran,
    //             'total_kehadiran_net'=>$rekap_kehadiran->total_kehadiran_net,
    //             'ptkp'=>$value->ptkp,
    //             'upah_per_bulan'=>$rekap_kehadiran->gaji_pokok,
    //             'upah_per_hari'=>$rekap_kehadiran->gaji_harian,
    //             'upah_per_menit'=>$rekap_kehadiran->gaji_menit,

    //             'tunjangan_karyawan_rupiah'=>$tunjangan,
    //             'premi_karyawan'=>0,

    //             'lembur_1'=>$lembur_1,
    //             'lembur_2'=>$lembur_2,
    //             'lembur_3'=>$lembur_3,
    //             'lembur_4'=>$lembur_4,
    //             'total_lembur_1234'=>$total_lembur_1234,
    //             'lembur1_rupiah'=>$lembur1_rupiah,
    //             'lembur2_rupiah'=>$lembur2_rupiah,
    //             'lembur3_rupiah'=>$lembur3_rupiah,
    //             'lembur4_rupiah'=>$lembur4_rupiah,
    //             'total_lembur_rupiah'=>$total_lembur_rupiah,

    //             'pendapatan_lainnya_rupiah'=>0,

    //             'koreksi_upah_rupiah'=>$koreksi_upah_rupiah,
    //             'koreksi_potongan_rupiah'=>$koreksi_potongan_rupiah,

    //             'potongan_iks_menit'=>$potongan_iks_menit,
    //             'potongan_dt_menit'=>$potongan_dt_menit,
    //             'potongan_pc_menit'=>$potongan_pc_menit,
    //             'potongan_dtpc_menit'=>$potongan_dtpc_menit,

    //             'potongan_iks_rupiah'=>$potongan_iks_rupiah,
    //             'potongan_dt_rupiah'=>$potongan_dt_rupiah,
    //             'potongan_pc_rupiah'=>$potongan_pc_rupiah,
    //             'potongan_dtpc_rupiah'=>$potongan_dtpc_rupiah,
    //             'potongan_kehadiran_rupiah'=>$rekap_kehadiran->potongan_kehadiran_rupiah,


    //             // 'upah_bruto_rupiah'=>$value,
    //             // 'upah_neto_rupiah'=>$value,

    //             // 'upah_bersih_rupiah'=>$value,
    //             // 'total_upah_thp_rupiah'=>$value,
    //             // 'jumlah_potongan_rupiah'=>$value,


    //             'pph21'=>0, // dari mana?
    //             'iuran_serikat_rupiah'=>0, // dari mana?
    //             'iuran_koperasi'=>0, // dari mana?
    //             'potongan_kasbon_rupiah'=>0, // dari mana?

    //             'bpjs_tk_jkm_rupiah'=> $Bpjs->bpjs_tk_jkm_bruto_rupiah + $Bpjs->bpjs_tk_jkm_neto_rupiah??0,
    //             'bpjs_tk_jkm_perusahaan_rupiah'=> $Bpjs->bpjs_tk_jkm_bruto_rupiah??0,
    //             'bpjs_tk_jkm_karyawan_rupiah'=> $Bpjs->bpjs_tk_jkm_neto_rupiah??0,
    //             'bpjs_tk_jkk_rupiah'=> $Bpjs->bpjs_tk_jkk_bruto_rupiah + $Bpjs->bpjs_tk_jkk_neto_rupiah??0,
    //             'bpjs_tk_jkk_perusahaan_rupiah'=> $Bpjs->bpjs_tk_jkk_bruto_rupiah??0,
    //             'bpjs_tk_jkk_karyawan_rupiah'=> $Bpjs->bpjs_tk_jkk_neto_rupiah??0,
    //             'bpjs_tk_jht_rupiah'=> $Bpjs->bpjs_tk_jht_bruto_rupiah + $Bpjs->bpjs_tk_jht_neto_rupiah??0,
    //             'bpjs_tk_jht_perusahaan_rupiah'=> $Bpjs->bpjs_tk_jht_bruto_rupiah??0,
    //             'bpjs_tk_jht_karyawan_rupiah'=> $Bpjs->bpjs_tk_jht_neto_rupiah??0,
    //             'bpjs_tk_jpn_rupiah'=> $Bpjs->bpjs_tk_jpn_bruto_rupiah + $Bpjs->bpjs_tk_jpn_neto_rupiah??0,
    //             'bpjs_tk_jpn_perusahaan_rupiah'=> $Bpjs->bpjs_tk_jpn_bruto_rupiah??0,
    //             'bpjs_tk_jpn_karyawan_rupiah'=> $Bpjs->bpjs_tk_jpn_neto_rupiah??0,
    //             'bpjs_ks_jkn_rupiah'=> $Bpjs->bpjs_ks_jkn_bruto_rupiah + $Bpjs->bpjs_ks_jkn_neto_rupiah??0,
    //             'bpjs_ks_jkn_perusahaan_rupiah'=> $Bpjs->bpjs_ks_jkn_bruto_rupiah??0,
    //             'bpjs_ks_jkn_karyawan_rupiah'=> $Bpjs->bpjs_ks_jkn_neto_rupiah??0,
    //             'total_bpjs_tk'=>$Bpjs->bpjs_tk_jkm_neto_rupiah + $Bpjs->bpjs_tk_jkk_neto_rupiah + $Bpjs->bpjs_tk_jht_neto_rupiah + $Bpjs->bpjs_tk_jpn_neto_rupiah??0,
    //             'total_bpjs_ks'=>$Bpjs->bpjs_ks_jkn_neto_rupiah??0,
                
    //             'jabatan_karyawan'=>$value->status_jabatan,
    //             'nama_bagian'=>$value->dept()->first()->sub_dept_name??'',
    //             'nama_department'=>$value->dept()->first()->department_name??'',
    //             'kategori_karyawan'=>$value->status_staff,
    //             'aktif_karyawan'=>$value->status_aktif,
    //             'jenis_kelamin'=>$value->jenis_kelamin,
    //             'site_nirwana_name'=>$value->site_nirwana_name,
    //             'nama_bank'=>$value->nama_bank==null||$value->nama_bank==""||$value->nama_bank=="-"?'TUNAI':$value->nama_bank,
    //             'nomor_rekening_bank'=>$value->nomor_rekening_bank==null||$value->nomor_rekening_bank==""||$value->nomor_rekening_bank=="-"?'TRANSFER':$value->nomor_rekening_bank,
    //             'npwp'=>$value->npwp,
    //             'operator'=>'sistem',
    //         ];
    //     }
    //     // dd($data);

    //     $records=[];
    //     foreach ($data as $k => $v) {

    //         $upah_bruto_rupiah=($v['upah_per_bulan']+$v['tunjangan_karyawan_rupiah']+$v['premi_karyawan']+$v['total_lembur_rupiah']+$v['pendapatan_lainnya_rupiah']+$v['koreksi_upah_rupiah'])-
    //             ($v['koreksi_potongan_rupiah']+$v['potongan_iks_rupiah']+$v['potongan_dtpc_rupiah']+$v['potongan_kehadiran_rupiah']);
            
    //         $upah_neto_rupiah=($v['upah_per_bulan']+$v['tunjangan_karyawan_rupiah']+$v['premi_karyawan']+$v['total_lembur_rupiah']+$v['pendapatan_lainnya_rupiah']+$v['koreksi_upah_rupiah'])-
    //             ($v['koreksi_potongan_rupiah']+$v['potongan_iks_rupiah']+$v['potongan_dtpc_rupiah']+$v['potongan_kehadiran_rupiah'])-$v['pph21'];
            
    //         $upah_bersih_rupiah=$upah_neto_rupiah-($v['total_bpjs_tk']+$v['total_bpjs_ks']+$v['iuran_koperasi']+$v['iuran_serikat_rupiah']);
           
    //         $total_upah_thp_rupiah=$upah_neto_rupiah-($v['total_bpjs_tk']+$v['total_bpjs_ks']+$v['iuran_koperasi']+$v['iuran_serikat_rupiah'])-$v['potongan_kasbon_rupiah'];
            
    //         $jumlah_potongan_rupiah=($v['total_bpjs_tk']+$v['total_bpjs_ks']+$v['iuran_koperasi']+$v['iuran_serikat_rupiah']);

    //         $records=[
    //             'kode_rekap_payroll'=>$v['kode_rekap_payroll'],
    //             'periode_kehadiran'=>$v['periode_kehadiran'],
    //             'periode_tahun_payroll'=>$v['periode_tahun_payroll'],
    //             'periode_bulan_payroll'=>$v['periode_bulan_payroll'],
    //             'enroll_id'=>$v['enroll_id'],
    //             'nik'=>$v['nik'],
    //             'kode_grade'=>$v['kode_grade'],
    //             'site_nirwana_name'=>$v['site_nirwana_name'],
    //             'employee_name'=>$v['employee_name'],
    //             'tanggal_resign'=>$v['tanggal_resign'],
    //             'kehadiran_iby'=>$v['kehadiran_iby'],
    //             'kehadiran_itb'=>$v['kehadiran_itb'],
    //             'kehadiran_m'=>$v['kehadiran_m'],
    //             'kehadiran_dt'=>$v['kehadiran_dt'],
    //             'kehadiran_pc'=>$v['kehadiran_pc'],
    //             'kehadiran_dtpc'=>$v['kehadiran_dtpc'],
    //             'kehadiran_lby'=>$v['kehadiran_lby'],
    //             'kehadiran_lsm'=>$v['kehadiran_lsm'],
    //             'kehadiran_r'=>$v['kehadiran_r'],
    //             'kehadiran_ok'=>$v['kehadiran_ok'],
    //             'kehadiran_tk'=>$v['kehadiran_tk'],
    //             'total_kehadiran'=>$v['total_kehadiran'],
    //             'total_kehadiran_net'=>$v['total_kehadiran_net'],
    //             'ptkp'=>$v['ptkp'],
    //             'upah_per_bulan'=>$v['upah_per_bulan'],
    //             'upah_per_hari'=>$v['upah_per_hari'],
    //             'upah_per_menit'=>$v['upah_per_menit'],

    //             'tunjangan_karyawan_rupiah'=>$v['tunjangan_karyawan_rupiah'],
    //             'premi_karyawan'=>$v['premi_karyawan'],

    //             'lembur_1'=>$v['lembur_1'],
    //             'lembur_2'=>$v['lembur_2'],
    //             'lembur_3'=>$v['lembur_3'],
    //             'lembur_4'=>$v['lembur_4'],
    //             'total_lembur_1234'=>$v['total_lembur_1234'],
    //             'lembur1_rupiah'=>$v['lembur1_rupiah'],
    //             'lembur2_rupiah'=>$v['lembur2_rupiah'],
    //             'lembur3_rupiah'=>$v['lembur3_rupiah'],
    //             'lembur4_rupiah'=>$v['lembur4_rupiah'],
    //             'total_lembur_rupiah'=>$v['total_lembur_rupiah'],

    //             'pendapatan_lainnya_rupiah'=>$v['pendapatan_lainnya_rupiah'],

    //             'koreksi_upah_rupiah'=>$v['koreksi_upah_rupiah'],
    //             'koreksi_potongan_rupiah'=>$v['koreksi_potongan_rupiah'],

    //             'potongan_iks_menit'=>$v['potongan_iks_menit'],
    //             'potongan_dt_menit'=>$v['potongan_dt_menit'],
    //             'potongan_pc_menit'=>$v['potongan_pc_menit'],
    //             'potongan_dtpc_menit'=>$v['potongan_dtpc_menit'],
    //             'potongan_iks_rupiah'=>$v['potongan_iks_rupiah'],
    //             'potongan_dt_rupiah'=>$v['potongan_dt_rupiah'],
    //             'potongan_pc_rupiah'=>$v['potongan_pc_rupiah'],
    //             'potongan_dtpc_rupiah'=>$v['potongan_dtpc_rupiah'],
    //             'potongan_kehadiran_rupiah'=>$v['potongan_kehadiran_rupiah'],


    //             'upah_bruto_rupiah'=>$upah_bruto_rupiah??0,
    //             'upah_neto_rupiah'=>$upah_neto_rupiah??0,

    //             'upah_bersih_rupiah'=>$upah_bersih_rupiah??0,
    //             'total_upah_thp_rupiah'=>$total_upah_thp_rupiah??0,
    //             'jumlah_potongan_rupiah'=>$jumlah_potongan_rupiah??0,


    //             'pph21'=>$v['pph21'], // dari mana?
    //             'iuran_serikat_rupiah'=>$v['iuran_serikat_rupiah'], // dari mana?
    //             'iuran_koperasi'=>$v['iuran_koperasi'], // dari mana?
    //             'potongan_kasbon_rupiah'=>$v['potongan_kasbon_rupiah'], // dari mana?

    //             'bpjs_tk_jkm_rupiah'=> $v['bpjs_tk_jkm_rupiah'],
    //             'bpjs_tk_jkm_perusahaan_rupiah'=> $v['bpjs_tk_jkm_perusahaan_rupiah'],
    //             'bpjs_tk_jkm_karyawan_rupiah'=> $v['bpjs_tk_jkm_karyawan_rupiah'],
    //             'bpjs_tk_jkk_rupiah'=> $v['bpjs_tk_jkk_rupiah'],
    //             'bpjs_tk_jkk_perusahaan_rupiah'=> $v['bpjs_tk_jkk_perusahaan_rupiah'],
    //             'bpjs_tk_jkk_karyawan_rupiah'=> $v['bpjs_tk_jkk_karyawan_rupiah'],
    //             'bpjs_tk_jht_rupiah'=> $v['bpjs_tk_jht_rupiah'],
    //             'bpjs_tk_jht_perusahaan_rupiah'=> $v['bpjs_tk_jht_perusahaan_rupiah'],
    //             'bpjs_tk_jht_karyawan_rupiah'=> $v['bpjs_tk_jht_karyawan_rupiah'],
    //             'bpjs_tk_jpn_rupiah'=> $v['bpjs_tk_jpn_rupiah'],
    //             'bpjs_tk_jpn_perusahaan_rupiah'=> $v['bpjs_tk_jpn_perusahaan_rupiah'],
    //             'bpjs_tk_jpn_karyawan_rupiah'=> $v['bpjs_tk_jpn_karyawan_rupiah'],
    //             'bpjs_ks_jkn_rupiah'=> $v['bpjs_ks_jkn_rupiah'],
    //             'bpjs_ks_jkn_perusahaan_rupiah'=> $v['bpjs_ks_jkn_perusahaan_rupiah'],
    //             'bpjs_ks_jkn_karyawan_rupiah'=> $v['bpjs_ks_jkn_karyawan_rupiah'],
    //             'total_bpjs_tk'=>$v['total_bpjs_tk'],
    //             'total_bpjs_ks'=>$v['total_bpjs_ks'],
                
    //             'jabatan_karyawan'=>$v['jabatan_karyawan'],
    //             'nama_bagian'=>$v['nama_bagian'],
    //             'nama_department'=>$v['nama_department'],
    //             'kategori_karyawan'=>$v['kategori_karyawan'],
    //             'aktif_karyawan'=>$v['aktif_karyawan'],
    //             'jenis_kelamin'=>$v['jenis_kelamin'],
    //             'nama_bank'=>$v['nama_bank'],
    //             'nomor_rekening_bank'=>$v['nomor_rekening_bank'],
    //             'npwp'=>$v['npwp'],
    //             'operator'=>$v['operator'],
    //         ];

    //         $count=RekapPerhitunganPayroll::where( 'kode_rekap_payroll',$v['kode_rekap_payroll'])->count();
    //         if($count){
    //             RekapPerhitunganPayroll::where( 'kode_rekap_payroll',$v['kode_rekap_payroll'])->update($records);
    //         }
    //         else{
    //             RekapPerhitunganPayroll::create($records);

    //         }
    //     }

    //     return true;
    // }
    

 // // rekap lembur
    // public function rekap_lembur($bulan_priode)
    // {
    //     // $tanggal_awal='2023-02-26';
    //     // $tanggal_akhir='2023-03-25';

    //     $bulan_sekarang1 = strtotime(date( $bulan_priode));

    //     $bulan_sebelum = strtotime("-1 month", $bulan_sekarang1);
    //     $bulan_sebelum=date('Y-m-', $bulan_sebelum);
    //     $bulan_sekarang=date('Y-m-', $bulan_sekarang1);

    //     $tanggal_awal=$bulan_sebelum.'26';
    //     $tanggal_akhir=$bulan_sekarang.'25';

    //     $a=MasterDataAbsenKehadiran::where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->where('nomor_form_lembur','!=',null)->get();

    //     // $a=MasterDataAbsenKehadiran::where('tanggal_berjalan','>=',$tanggal_awal)->where('tanggal_berjalan','<=',$tanggal_akhir)->where('nomor_form_lembur','!=',null)->where('enroll_id','1755')->get();

    //     foreach ($a as $key => $value) {
    //         $y=DataLembur::where('enroll_id',$value->enroll_id)->where('nomor_form_lembur',$value->nomor_form_lembur)->first();
    //         // dd($y);
    //         $jumlah_jam_kerja=date_diff(date_create($value->mulai_jam_kerja),date_create($value->akhir_jam_kerja));
    //         $jam_efektif_kerja=date_diff(date_create($value->absen_masuk_kerja),date_create($value->absen_pulang_kerja));
    //         $final_mulai_jam_lembur=date('H:i:s', strtotime($value->mulai_jam_lembur));
    //         $final_total_jam_lembur=date_diff(date_create($final_mulai_jam_lembur),date_create($value->absen_pulang_kerja));

    //         $salary=EmployeeGrading::where('enroll_id',$value->enroll_id)->latest()->first();
    //         $salary_bulanan=$salary->salary_bulanan??0;

    //         if($value->kode_hari==5 || $value->kode_hari==6 || $value->status_absen=='LN'){
    //             $kerjalibur='LIBUR';
    //             $l1=0;
    //             $l2=($y->jumlah_jam_lembur <= 8) ? $y->jumlah_jam_lembur : 8;
    //             if($y->jumlah_jam_lembur > 9){
    //                 $l3=1;
    //                 $l4=max($y->jumlah_jam_lembur -9, 0);
    //             }
    //             else if($y->jumlah_jam_lembur > 8 && $y->jumlah_jam_lembur <= 9 ){
    //                 $l3=max($y->jumlah_jam_lembur -8, 0); 
    //                 $l4=0;
    //             }
    //             else{
    //                 $l3=0;
    //                 $l4=0; 
    //             }
    //         }
    //         else{
    //             $kerjalibur='KERJA';
    //             $l1 = ($y->jumlah_jam_lembur <= 1) ? $y->jumlah_jam_lembur : 1;
    //             $l2 = max($y->jumlah_jam_lembur - 1, 0);
    //             $l3=0;
    //             $l4=0;

    //         }
    //         if($value->kode_hari==6 || $value->status_absen=='LN'){
    //             $l1_rupiah=$l1*($salary_bulanan/173*1);
    //             $l2_rupiah=$l2*($salary_bulanan/173*1);
    //             $l3_rupiah=$l3*($salary_bulanan/173*2);
    //             $l4_rupiah=$l4*($salary_bulanan/173*2);
    //         }
    //         else{
    //             $l1_rupiah=$l1*($salary_bulanan/173*1);
    //             $l2_rupiah=$l2*($salary_bulanan/173*1);
    //             $l3_rupiah=$l3*($salary_bulanan/173*1);
    //             $l4_rupiah=$l4*($salary_bulanan/173*1);
    //         }

    //         $data=[
    //             'uuid'=>Str::uuid('uuid'),
    //             'tanggal_berjalan'=>$value->tanggal_berjalan,
    //             'kode_hari'=>$value->kode_hari,
    //             'nama_hari'=>$value->nama_hari,
    //             'kerjalibur'=>$kerjalibur,
    //             'holiday_name'=>$value->holiday_name,
    //             'nomor_form_lembur'=>$value->nomor_form_lembur,
    //             'enroll_id'=>$value->enroll_id,
    //             'nik'=>$value->nik,
    //             'employee_name'=>$value->employee_name,
    //             'site_nirwana_id'=>$value->site_nirwana_id,
    //             'site_nirwana_name'=>$value->site_nirwana_name,
    //             'department_id'=>$value->department_id,
    //             'department_name'=>$value->department_name,
    //             'sub_dept_id'=>$value->sub_dept_id,
    //             'sub_dept_name'=>$value->sub_dept_name,
    //             'posisi_name'=>$value->posisi_name,
    //             'mulai_jam_kerja'=>$value->mulai_jam_kerja,
    //             'akhir_jam_kerja'=>$value->akhir_jam_kerja,
    //             'jumlah_jam_kerja'=>sprintf('%02d:%02d:%02d', $jumlah_jam_kerja->h, $jumlah_jam_kerja->i, $jumlah_jam_kerja->s),
    //             'absen_masuk_kerja'=>$value->absen_masuk_kerja,
    //             'absen_pulang_kerja'=>$value->absen_pulang_kerja,
    //             'jam_efektif_kerja'=>sprintf('%02d:%02d:%02d', $jam_efektif_kerja->h, $jam_efektif_kerja->i, $jam_efektif_kerja->s),
    //             'mulai_jam_lembur'=>$value->mulai_jam_lembur,
    //             'akhir_jam_lembur'=>$value->akhir_jam_lembur,
    //             'final_mulai_jam_lembur'=>$final_mulai_jam_lembur,
    //             'final_selesai_jam_lembur'=>$value->absen_pulang_kerja,
    //             'final_total_jam_lembur'=>sprintf('%02d:%02d:%02d', $final_total_jam_lembur->h, $final_total_jam_lembur->i, $final_total_jam_lembur->s),
    //             'final_jam_istirahat_lembur'=>$y->jumlah_jam_istirahat,
    //             'final_total_menit_lembur'=>($final_total_jam_lembur->h*60)+$final_total_jam_lembur->i,
    //             'final_jam_lembur_roundown'=> $final_total_jam_lembur->h,
    //             'final_menit_lembur_roundown'=>$final_total_jam_lembur->i,
    //             'lembur_1'=>$l1,
    //             'lembur_2'=>$l2,
    //             'lembur_3'=>$l3,
    //             'lembur_4'=>$l4,
    //             'total_lembur_1234'=>$l1+$l2+$l3+$l4,
    //             'salary'=>$salary_bulanan,
    //             'lembur1_rupiah'=> $l1_rupiah,
    //             'lembur2_rupiah'=> $l2_rupiah,
    //             'lembur3_rupiah'=> $l3_rupiah,
    //             'lembur4_rupiah'=> $l4_rupiah,
    //             'total_lembur_rupiah'=> $l1_rupiah + $l2_rupiah +  $l3_rupiah +  $l4_rupiah,
    //             'operator'=>'system',
    //         ];
    //         $count=RekapPerhitunganLembur::where('tanggal_berjalan',$value->tanggal_berjalan)->where('enroll_id',$value->enroll_id)->count();
    //         if($count){
    //             RekapPerhitunganLembur::where('tanggal_berjalan',$value->tanggal_berjalan)->where('enroll_id',$value->enroll_id)->update($data);
    //         }
    //         else{
    //             RekapPerhitunganLembur::create($data);

    //         } 
    //     }
    //     return true;
    // }
}