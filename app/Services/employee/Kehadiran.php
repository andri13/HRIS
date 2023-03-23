<?php

namespace App\Services\employee;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
Use App\Models\HR_GA\AuditBuyer\Apar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use App\Models\MasterDataAbsenKehadiran;
use App\Models\EmployeeAtribut;
use Illuminate\Support\Facades\Auth;




class Kehadiran{

    public function new_employee($enroll_id)
    {
        $loggedAdmin = Auth::guard('admin')->user();
        $loggedAdmin->email;


        // $enroll_id=8888;
        $employee=EmployeeAtribut::where('enroll_id', $enroll_id)->first();
        $join_date=$employee->join_date;
        $tgl=date('d',strtotime( $join_date));
        $bln_sekarang=date('M-Y',strtotime( $join_date));
        $date = strtotime($bln_sekarang);
        $bulan_sebelum= strtotime("-1 month", $date);
        $bulan_sesudah= strtotime("+1 month", $date);
        $bulan_sebelum=date('M-Y', $bulan_sebelum);
        $bulan_sesudah=date('M-Y', $bulan_sesudah);

        if($tgl>=26){
            $tanggal_akhir=date('Y-m-d', strtotime('25-'.$bulan_sesudah));
        }
        elseif($tgl<26){
            $tanggal_akhir=date('Y-m-d', strtotime('25-'.$bln_sekarang));
        }
        $tgl_berjalan=$join_date;
        while (strtotime( $tgl_berjalan) <= strtotime($tanggal_akhir)) {  
            $hari=date('D',strtotime(  $tgl_berjalan));
            if($hari=='Sun'){
                $kode_hari='6';
                $nama_hari='Minggu';
                $status_absen=null;
            }else if($hari=='Mon'){
                $kode_hari='0';
                $nama_hari='Senin';
                $status_absen='M';
            }else if($hari=='Tue'){
                $kode_hari='1';
                $nama_hari='Selasa';
                $status_absen='M';
            }else if($hari=='Wed'){
                $kode_hari='2';
                $nama_hari='Rabu';
                $status_absen='M';
            }else if($hari=='Thu'){
                $kode_hari='3';
                $nama_hari='Kamis';
                $status_absen='M';
            }else if($hari=='Fri'){
                $kode_hari='4';
                $nama_hari='Jumat';
                $status_absen='M';
            }else if($hari=='Sat'){
                $kode_hari='5';
                $nama_hari='Sabtu';
                $status_absen=null;

            }


            $x=[
                'uuid' => Str::uuid('uuid'),
                'tanggal_berjalan' => $tgl_berjalan,
                'kode_hari' =>$kode_hari,
                'nama_hari' => $nama_hari,
                'employee_id' => $employee->employee_id,
                'employee_name' => $employee->employee_name,
                'enroll_id' => $employee->enroll_id,
                'join_date' => $employee->join_date,
                'tanggal_resign' => $employee->tanggal_resign,
                'work_status' => $employee->work_status,
                'status_aktif' => $employee->status_aktif,
                'status_kontrak_tetap' => $employee->status_kontrak_tetap,
                'employee_status' => $employee->employee_status,
                'status_jabatan' => $employee->status_jabatan,
                'posisi_name' => $employee->posisi_name,
                'status_staff' => $employee->status_staff,
                'nik' => $employee->nik,
                'site_nirwana_id' => $employee->site_nirwana_id,
                'site_nirwana_name' => $employee->site_nirwana_name,
                'department_id' => $employee->department_id,
                'department_name' => $employee->department_name,
                'sub_dept_id' => $employee->sub_dept_id,
                'sub_dept_name' => $employee->sub_dept_name,
                'status_absen' => $status_absen,
                'operator' =>$loggedAdmin->email,
            ];
            MasterDataAbsenKehadiran::create($x);
             $tgl_berjalan = date ("Y-m-d", strtotime("+1 day", strtotime($tgl_berjalan)));
        }
    }
}