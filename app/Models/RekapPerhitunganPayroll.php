<?php
namespace App\Models;

class RekapPerhitunganPayroll extends \Eloquent
{
    protected $table = 'rekap_perhitungan_payroll';

    protected $fillable = [
        'kode_rekap_payroll',
        'periode_kehadiran',
        'periode_tahun_payroll',
        'periode_bulan_payroll',
        'enroll_id',
        'nik',
        'kode_grade',
        'employee_name',
        'tanggal_resign',
        'kehadiran_iby',
        'kehadiran_itb',
        'kehadiran_m',
        'kehadiran_dt',
        'kehadiran_pc',
        'kehadiran_dtpc',
        'kehadiran_lby',
        'kehadiran_lsm',
        'kehadiran_r',
        'kehadiran_ok',
        'kehadiran_tk',
        'total_kehadiran',
        'total_kehadiran_net',
        'ptkp',
        'upah_per_bulan',
        'upah_per_hari',
        'upah_per_menit',
        'tunjangan_karyawan_rupiah',
        'premi_karyawan',
        'lembur_1',
        'lembur_2',
        'lembur_3',
        'lembur_4',
        'total_lembur_1234',
        'lembur1_rupiah',
        'lembur2_rupiah',
        'lembur3_rupiah',
        'lembur4_rupiah',
        'total_lembur_rupiah',
        'pendapatan_lainnya_rupiah',
        'koreksi_upah_rupiah',
        'koreksi_potongan_rupiah',
        'potongan_iks_menit',
        'potongan_dt_menit',
        'potongan_pc_menit',
        'potongan_dtpc_menit',
        'potongan_iks_rupiah',
        'potongan_dt_rupiah',
        'potongan_pc_rupiah',
        'potongan_dtpc_rupiah',
        'potongan_kehadiran_rupiah',
        'upah_bruto_rupiah',
        'pph21',
        'upah_neto_rupiah',
        'total_bpjs_tk',
        'total_bpjs_ks',
        'iuran_serikat_rupiah',
        'iuran_koperasi',
        'jumlah_potongan_rupiah',
        'upah_bersih_rupiah',
        'potongan_kasbon_rupiah',
        'total_upah_thp_rupiah',
        'bpjs_tk_jkm_rupiah',
        'bpjs_tk_jkm_perusahaan_rupiah',
        'bpjs_tk_jkm_karyawan_rupiah',
        'bpjs_tk_jkk_rupiah',
        'bpjs_tk_jkk_perusahaan_rupiah',
        'bpjs_tk_jkk_karyawan_rupiah',
        'bpjs_tk_jht_rupiah',
        'bpjs_tk_jht_perusahaan_rupiah',
        'bpjs_tk_jht_karyawan_rupiah',
        'bpjs_tk_jpn_rupiah',
        'bpjs_tk_jpn_perusahaan_rupiah',
        'bpjs_tk_jpn_karyawan_rupiah',
        'bpjs_ks_jkn_rupiah',
        'bpjs_ks_jkn_perusahaan_rupiah',
        'bpjs_ks_jkn_karyawan_rupiah',
        'jabatan_karyawan',
        'nama_bagian',
        'nama_department',
        'kategori_karyawan',
        'aktif_karyawan',
        'jenis_kelamin',
        'nama_bank',
        'nomor_rekening_bank',
        'npwp',
        'operator',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $guarded = ['kode_rekap_payroll'];

    protected $hidden = [''];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'kode_rekap_payroll',
        'periode_kehadiran',
        'periode_tahun_payroll',
        'periode_bulan_payroll',
        'enroll_id',
        'nik',
        'employee_name',
        'tanggal_resign',
        'kehadiran_iby',
        'kehadiran_itb',
        'kehadiran_m',
        'kehadiran_dt',
        'kehadiran_pc',
        'kehadiran_dtpc',
        'kehadiran_lby',
        'kehadiran_lsm',
        'kehadiran_r',
        'kehadiran_ok',
        'kehadiran_tk',
        'total_kehadiran',
        'total_kehadiran_net',
        'ptkp',
        'upah_per_bulan',
        'upah_per_hari',
        'upah_per_menit',
        'tunjangan_karyawan_rupiah',
        'premi_karyawan',
        'lembur_1',
        'lembur_2',
        'lembur_3',
        'lembur_4',
        'total_lembur_1234',
        'lembur1_rupiah',
        'lembur2_rupiah',
        'lembur3_rupiah',
        'lembur4_rupiah',
        'total_lembur_rupiah',
        'pendapatan_lainnya_rupiah',
        'koreksi_upah_rupiah',
        'koreksi_potongan_rupiah',
        'potongan_iks_menit',
        'potongan_dt_menit',
        'potongan_pc_menit',
        'potongan_dtpc_menit',
        'potongan_iks_rupiah',
        'potongan_dt_rupiah',
        'potongan_pc_rupiah',
        'potongan_dtpc_rupiah',
        'potongan_kehadiran_rupiah',
        'upah_bruto_rupiah',
        'pph21',
        'upah_neto_rupiah',
        'total_bpjs_tk',
        'total_bpjs_ks',
        'iuran_serikat_rupiah',
        'iuran_koperasi',
        'jumlah_potongan_rupiah',
        'upah_bersih_rupiah',
        'potongan_kasbon_rupiah',
        'total_upah_thp_rupiah',
        'bpjs_tk_jkm_rupiah',
        'bpjs_tk_jkm_perusahaan_rupiah',
        'bpjs_tk_jkm_karyawan_rupiah',
        'bpjs_tk_jkk_rupiah',
        'bpjs_tk_jkk_perusahaan_rupiah',
        'bpjs_tk_jkk_karyawan_rupiah',
        'bpjs_tk_jht_rupiah',
        'bpjs_tk_jht_perusahaan_rupiah',
        'bpjs_tk_jht_karyawan_rupiah',
        'bpjs_tk_jpn_rupiah',
        'bpjs_tk_jpn_perusahaan_rupiah',
        'bpjs_tk_jpn_karyawan_rupiah',
        'bpjs_ks_jkn_rupiah',
        'bpjs_ks_jkn_perusahaan_rupiah',
        'bpjs_ks_jkn_karyawan_rupiah',
        'jabatan_karyawan',
        'nama_bagian',
        'nama_department',
        'kategori_karyawan',
        'aktif_karyawan',
        'jenis_kelamin',
        'nama_bank',
        'nomor_rekening_bank',
        'npwp',
        'operator',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [];
    public $incrementing = false;
    // public $primaryKey = null;
    public $primaryKey = ['kode_rekap_payroll'];


}
