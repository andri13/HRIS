<?php
namespace App\Models;

class RekapKehadiranKaryawan extends \Eloquent
{
    protected $table = 'rekap_kehadiran_karyawan';

    protected $fillable = [
        'uuid',
        'kode_rekap_kehadiran',
        'periode_payroll',
        'periode_tahun',
        'periode_bulan',
        'enroll_id',
        'nik',
        'employee_name',
        'site_nirwana_id',
        'site_nirwana_name',
        'department_id',
        'department_name',
        'sub_dept_id',
        'sub_dept_name',
        'join_date',
        'tanggal_resign',
        'status_aktif',
        'status_staff',
        'kehadiran_iby',
        'kehadiran_itb',
        'kehadiran_lby',
        'kehadiran_lsm',
        'kehadiran_dt',
        'kehadiran_pc',
        'kehadiran_dtpc',
        'kehadiran_m',
        'kehadiran_r',
        'kehadiran_tk',
        'kehadiran_ok',
        'total_kehadiran',
        'total_kehadiran_net',
        'jumlah_hari',
        'jumlah_hari_kerja',
        'operator',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $guarded = [''];

    protected $hidden = [''];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'uuid',
        'kode_rekap_kehadiran',
        'periode_payroll',
        'periode_tahun',
        'periode_bulan',
        'enroll_id',
        'nik',
        'employee_name',
        'site_nirwana_id',
        'site_nirwana_name',
        'department_id',
        'department_name',
        'sub_dept_id',
        'sub_dept_name',
        'join_date',
        'tanggal_resign',
        'status_aktif',
        'status_staff',
        'kehadiran_iby',
        'kehadiran_itb',
        'kehadiran_lby',
        'kehadiran_lsm',
        'kehadiran_dt',
        'kehadiran_pc',
        'kehadiran_dtpc',
        'kehadiran_m',
        'kehadiran_r',
        'kehadiran_tk',
        'kehadiran_ok',
        'total_kehadiran',
        'total_kehadiran_net',
        'jumlah_hari',
        'jumlah_hari_kerja',
        'operator',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [];
    public $incrementing = false;
    // public $primaryKey = null;
    public $primaryKey = ['kode_rekap_kehadiran'];

   
}
