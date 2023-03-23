<?php

namespace App\Exports;

use App\Models\MasterDataAbsenKehadiran;
use App\Http\Controllers\Hris\DepartmentAllController;

use App\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithProperties;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use \Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\DB;

use Auth;

class MasterDataAbsenKehadiranExport implements FromQuery, WithMapping, ShouldAutoSize, WithEvents, WithCustomStartCell, WithTitle, WithChunkReading, ShouldQueue
{
    use Exportable;

    public function exportParams(string $daterange1, string $selectDepartment, string $selectBagian, string $status_staff, string $searchData)
    {
        $this->daterange1 = $daterange1;
        $daterange1 = explode(" s/d ", $this->daterange1);
        $tanggalMulai = date('Y-m-d', strtotime($daterange1[0]));
        $tanggalSampai = date('Y-m-d', strtotime($daterange1[1]));
        $this->tanggalMulai = $tanggalMulai;
        $this->tanggalSampai = $tanggalSampai;

        $this->selectDepartment = $selectDepartment;

        $this->status_staff = $status_staff;
        $this->searchData = strtoupper($searchData);

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

        $this->daterange1 = $daterange1;
        $this->filterStaff = $filterStaff;
        $this->inDepartment = $inDepartment;
        $this->inBagian = $inBagian;
        $this->inSearchData = $inSearchData;

        return $this;
    }

    public function query()
    {
        $tanggalMulai = $this->tanggalMulai;
        $tanggalSampai = $this->tanggalSampai;

        return MasterDataAbsenKehadiran::query()
                ->selectRaw('
                    master_data_absen_kehadiran.tanggal_berjalan,
                    master_data_absen_kehadiran.kode_hari,
                    master_data_absen_kehadiran.nama_hari,
                    employee_atribut.nik,
                    employee_atribut.enroll_id,
                    employee_atribut.employee_name,
                    employee_atribut.status_staff,
                    employee_atribut.department_name,
                    master_data_absen_kehadiran.mulai_jam_kerja,
                    master_data_absen_kehadiran.akhir_jam_kerja,
                    master_data_absen_kehadiran.absen_masuk_kerja,
                    master_data_absen_kehadiran.absen_pulang_kerja,
                    master_data_absen_kehadiran.jumlah_absen_menit_kerja,
                    master_data_absen_kehadiran.permits_dari_pukul,
                    master_data_absen_kehadiran.permits_sampai_pukul,
                    master_data_absen_kehadiran.total_menit_permits,
                    master_data_absen_kehadiran.jumlah_menit_absen_dt,
                    master_data_absen_kehadiran.jumlah_menit_absen_pc,
                    master_data_absen_kehadiran.jumlah_menit_absen_dtpc,
                    master_data_absen_kehadiran.status_absen,
                    master_data_absen_kehadiran.absen_alasan,
                    master_data_absen_kehadiran.catatan_hrd,
                    rekap_perhitungan_lembur.nomor_form_lembur,
                    rekap_perhitungan_lembur.final_mulai_jam_lembur,
                    rekap_perhitungan_lembur.final_selesai_jam_lembur,
                    rekap_perhitungan_lembur.final_total_jam_lembur,
                    rekap_perhitungan_lembur.final_jam_istirahat_lembur,
                    rekap_perhitungan_lembur.final_total_menit_lembur,
                    rekap_perhitungan_lembur.final_jam_lembur_roundown,
                    rekap_perhitungan_lembur.final_menit_lembur_roundown,
                    rekap_perhitungan_lembur.lembur_1,
                    rekap_perhitungan_lembur.lembur_2,
                    rekap_perhitungan_lembur.lembur_3,
                    rekap_perhitungan_lembur.lembur_4,
                    rekap_perhitungan_lembur.total_lembur_1234
                ')
                ->whereRaw('
                    substr(master_data_absen_kehadiran.tanggal_berjalan, 1, 10) between "' . $tanggalMulai . '" and "' . $tanggalSampai . '" 
                    ' . $this->filterStaff . ' ' . $this->inDepartment . ' ' . $this->inBagian . ' ' . $this->inSearchData . '
                   and (master_data_absen_kehadiran.status_absen != "R" OR master_data_absen_kehadiran.status_absen IS NULL)
                ')
                ->leftJoin('employee_atribut','master_data_absen_kehadiran.enroll_id','=','employee_atribut.enroll_id')
                ->leftJoin('department_all','employee_atribut.sub_dept_id','=','department_all.sub_dept_id')
                ->leftJoin('rekap_perhitungan_lembur',function($leftjoin){
                    $leftjoin->on("master_data_absen_kehadiran.tanggal_berjalan","=","rekap_perhitungan_lembur.tanggal_berjalan")
                        ->on("master_data_absen_kehadiran.enroll_id","=","rekap_perhitungan_lembur.enroll_id");
                })           
                ->orderBy('employee_atribut.employee_name','asc')
                ->orderBy('master_data_absen_kehadiran.tanggal_berjalan','asc')
                ->limit(1);
    }

    public function startCell(): string
    {
        return 'A8';
    }
    
    public function chunkSize(): int
    {
        return 1000;
    }

    public function map($Kehadiran): array
    {
        $kode_hari = $Kehadiran->kode_hari;
        $liburnasional = $Kehadiran->holiday_name;

        $mulai_jam_kerja = substr($Kehadiran->mulai_jam_kerja, 0, 5);
        $akhir_jam_kerja = substr($Kehadiran->akhir_jam_kerja, 0, 5);

        $absenIN = $Kehadiran->absen_masuk_kerja;
        $absenOUT = $Kehadiran->absen_pulang_kerja;

        // Creating DateTime objects
        $dateTimeObject1 = date_create($mulai_jam_kerja); 
        $dateTimeObject2 = date_create($akhir_jam_kerja); 
        
        // Calculating the difference between DateTime objects
        $interval = date_diff($dateTimeObject1, $dateTimeObject2); 
        
        $minutes = $interval->days * 24 * 60;
        $minutes += $interval->h * 60;
        $minutes += $interval->i;

        $jumlah_menit_kerja = $minutes;
        if ($Kehadiran->jumlah_menit_istirahat == "") {
            $jumlah_menit_istirahat = 60;
        } else {
            $jumlah_menit_istirahat = $Kehadiran->jumlah_menit_istirahat;
        }

        $kerjalibur = "KERJA";
        switch ($kode_hari) {
            case '5':
                $kerjalibur = "LIBUR";
                $jumlah_menit_istirahat = 30;
                break;
            case '6':
                $kerjalibur = "LIBUR";
                $jumlah_menit_istirahat = 30;
                break;
        }

        if(($absenIN <> null) || ($absenIN <> "") || ($absenOUT <> null) || ($absenOUT <> "")) {
            $kerjalibur = "KERJA";
            switch ($kode_hari) {
                case '5':
                    $kerjalibur = "LIBUR";
                    $jumlah_menit_istirahat = 30;
                    break;
                case '6':
                    $kerjalibur = "LIBUR";
                    $jumlah_menit_istirahat = 30;
                    break;
            }
        }

        if($liburnasional <> "") {
            $kerjalibur = "LIBUR";
        }

        if ($Kehadiran->total_menit_permits == 0) {
            $total_menit_permits = "0";
        } else {
            $total_menit_permits = $Kehadiran->total_menit_permits;
        }

        if ($Kehadiran->jumlah_menit_absen_dt == 0) {
            $jumlah_menit_absen_dt = "0";
        } else {
            $jumlah_menit_absen_dt = $Kehadiran->jumlah_menit_absen_dt;
        }

        if ($Kehadiran->jumlah_menit_absen_pc == 0) {
            $jumlah_menit_absen_pc = "0";
        } else {
            $jumlah_menit_absen_pc = $Kehadiran->jumlah_menit_absen_pc;
        }

        if ($Kehadiran->jumlah_menit_absen_dtpc == 0) {
            $jumlah_menit_absen_dtpc = "0";
        } else {
            $jumlah_menit_absen_dtpc = $Kehadiran->jumlah_menit_absen_dtpc;
        }

        if ($Kehadiran->nomor_form_lembur == "") {
            $nomor_form_lembur = "-";
        } else {
            $nomor_form_lembur = $Kehadiran->nomor_form_lembur;
        }

        if ($Kehadiran->absen_alasan == "") {
            $absen_alasan = "-";
        } else {
            $absen_alasan = $Kehadiran->absen_alasan;
        }

        if ($Kehadiran->catatan_hrd == "") {
            $catatan_hrd = "-";
        } else {
            $catatan_hrd = $Kehadiran->catatan_hrd;
        }

        if ($Kehadiran->final_mulai_jam_lembur == "") {
            $final_mulai_jam_lembur = "-";
        } else {
            $final_mulai_jam_lembur = $Kehadiran->final_mulai_jam_lembur;
        }

        if ($Kehadiran->final_selesai_jam_lembur == "") {
            $final_selesai_jam_lembur = "-";
        } else {
            $final_selesai_jam_lembur = $Kehadiran->final_selesai_jam_lembur;
        }

        if ($Kehadiran->final_total_jam_lembur == "") {
            $final_total_jam_lembur = "-";
        } else {
            $final_total_jam_lembur = $Kehadiran->final_total_jam_lembur;
        }

        if ($Kehadiran->final_jam_istirahat_lembur == 0) {
            $final_jam_istirahat_lembur = "0";
        } else {
            $final_jam_istirahat_lembur = $Kehadiran->final_jam_istirahat_lembur;
        }

        if ($Kehadiran->nomor_form_lembur == "") {
            $final_jam_lembur_roundown = "";
            $final_menit_lembur_roundown = "";
            $final_lembur_roundown = "";
        } else {
            $final_jam_lembur_roundown = str_pad($Kehadiran->final_jam_lembur_roundown,2,"0",STR_PAD_LEFT);
            $final_menit_lembur_roundown = str_pad($Kehadiran->final_menit_lembur_roundown,2,"0",STR_PAD_LEFT);
            $final_lembur_roundown = $final_jam_lembur_roundown . ":" . $final_menit_lembur_roundown;
        }

        if ($Kehadiran->lembur_1 == 0) {
            $lembur_1 = "0";
        } else {
            $lembur_1 = $Kehadiran->lembur_1;
        }

        if ($Kehadiran->lembur_2 == 0) {
            $lembur_2 = "0";
        } else {
            $lembur_2 = $Kehadiran->lembur_2;
        }

        if ($Kehadiran->lembur_3 == 0) {
            $lembur_3 = "0";
        } else {
            $lembur_3 = $Kehadiran->lembur_3;
        }

        if ($Kehadiran->lembur_4 == 0) {
            $lembur_4 = "0";
        } else {
            $lembur_4 = $Kehadiran->lembur_4;
        }

        if ($Kehadiran->total_lembur_1234 == 0) {
            $total_lembur_1234 = "0";
        } else {
            $total_lembur_1234 = $Kehadiran->total_lembur_1234;
        }

        return [
            $Kehadiran->tanggal_berjalan,
            $Kehadiran->nama_hari,
            $Kehadiran->nik,
            $Kehadiran->enroll_id,
            $Kehadiran->employee_name,
            $Kehadiran->status_staff,
            $Kehadiran->department_name,
            $kerjalibur,
            $mulai_jam_kerja,
            $akhir_jam_kerja,
            $jumlah_menit_istirahat,
            $jumlah_menit_kerja,
            substr($Kehadiran->absen_masuk_kerja, 0, 5),
            substr($Kehadiran->absen_pulang_kerja, 0, 5),
            $Kehadiran->jumlah_absen_menit_kerja,
            substr($Kehadiran->permits_dari_pukul, 0, 5),
            substr($Kehadiran->permits_sampai_pukul, 0, 5),
            $total_menit_permits,
            $jumlah_menit_absen_dt,
            $jumlah_menit_absen_pc,
            $jumlah_menit_absen_dtpc,
            $Kehadiran->status_absen,
            $absen_alasan,
            $catatan_hrd,
            "",
            $nomor_form_lembur,
            $final_mulai_jam_lembur,
            $final_selesai_jam_lembur,
            $final_total_jam_lembur,
            $final_jam_istirahat_lembur,
            $final_lembur_roundown,
            $lembur_1,
            $lembur_2,
            $lembur_3,
            $lembur_4,
            $total_lembur_1234,
        ];
    }

    public function title(): string
    {
        return $this->daterange1[0] . ' s/d ' . $this->daterange1[1];
    }

    public function registerEvents() : array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $sheet = $event->sheet;
                $sheet->setCellValue('A1', 'PT NIRWANA ALABARE GARMENT');
                $sheet->getDelegate()->getStyle('A1')->getFont()->setSize(18);
                $sheet->setCellValue('A2', 'LAPORAN ABSENSI KARYAWAN');
                $sheet->getDelegate()->getStyle('A1')->getFont()->setSize(16);

                setlocale(LC_ALL, 'id-ID', 'id_ID');
                $datePeriode = strtoupper(strftime("%d %b %Y", strtotime($this->tanggalMulai)) . ' s/d ' . strftime("%d %b %Y", strtotime($this->tanggalSampai)));

                $sheet->setCellValue('A3', 'TANGGAL ABSENSI : ' . $datePeriode);
                $sheet->getDelegate()->getStyle('A1')->getFont()->setSize(14);
                if($this->status_staff) { 
                    $status_staff = $this->status_staff;
                } else { 
                    $status_staff = 'SEMUA KARYAWAN';
                }
                $sheet->setCellValue('A4', 'STAFF / NON STAFF : ' . $status_staff);
                $sheet->getDelegate()->getStyle('A1')->getFont()->setSize(14);
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');
                $sheet->mergeCells('A3:E3');
                $sheet->mergeCells('A4:E4');

                $sheet->mergeCells('A6:A7');
                $sheet->setCellValue('A6', 'TANGGAL');

                $sheet->mergeCells('B6:B7');
                $sheet->setCellValue('B6', 'HARI');

                $sheet->mergeCells('C6:C7');
                $sheet->setCellValue('C6', 'NIK');

                $sheet->mergeCells('D6:D7');
                $sheet->setCellValue('D6', 'NO. ABSEN');

                $sheet->mergeCells('E6:E7');
                $sheet->setCellValue('E6', 'NAMA KARYAWAN');

                $sheet->mergeCells('F6:F7');
                $sheet->setCellValue('F6', 'STAFF / NON STAFF');

                $sheet->mergeCells('G6:G7');
                $sheet->setCellValue('G6', 'DEPARTMENT');

                $sheet->mergeCells('H6:H7');
                $sheet->setCellValue('H6', 'KERJA/LIBUR');

                $sheet->mergeCells('I6:L6');
                $sheet->setCellValue('I6', 'JADWAL KERJA');
                $sheet->setCellValue('I7', 'IN');
                $sheet->setCellValue('J7', 'OUT');
                $sheet->setCellValue('K7', 'DURASI ISTIRAHAT');
                $sheet->setCellValue('L7', 'DURASI KERJA');
                
                $sheet->mergeCells('M6:O6');
                $sheet->setCellValue('M6', 'ABSENSI');
                $sheet->setCellValue('M7', 'IN');
                $sheet->setCellValue('N7', 'OUT');
                $sheet->setCellValue('O7', 'EFEKTIF KERJA');
                
                $sheet->mergeCells('P6:R6');
                $sheet->setCellValue('P6', 'IJIN KELUAR SEMENTARA (IKS)');
                $sheet->setCellValue('P7', 'DARI');
                $sheet->setCellValue('Q7', 'SAMPAI');
                $sheet->setCellValue('R7', 'TOTAL');

                $sheet->mergeCells('S6:U6');
                $sheet->setCellValue('S6', 'POTONGAN MENIT');
                $sheet->setCellValue('S7', 'DT');
                $sheet->setCellValue('T7', 'PC');
                $sheet->setCellValue('U7', 'Total');

                $sheet->mergeCells('V6:V7');
                $sheet->setCellValue('V6', 'STATUS ABSEN');

                $sheet->mergeCells('W6:W7');
                $sheet->setCellValue('W6', 'ALASAN ABSEN');

                $sheet->mergeCells('X6:X7');
                $sheet->setCellValue('X6', 'KETERANGAN');

                $sheet->mergeCells('Y6:Y7');

                $sheet->mergeCells('Z6:AJ6');
                $sheet->setCellValue('Z6', 'DATA LEMBUR');
                $sheet->setCellValue('Z7', 'NO. SPL');
                $sheet->setCellValue('AA7', 'MULAI');
                $sheet->setCellValue('AB7', 'SELESAI');
                $sheet->setCellValue('AC7', 'JUMLAH JAM');
                $sheet->setCellValue('AD7', 'ISTIRAHAT');
                $sheet->setCellValue('AE7', 'TOTAL LEMBUR');
                $sheet->setCellValue('AF7', 'L1');
                $sheet->setCellValue('AG7', 'L2');
                $sheet->setCellValue('AH7', 'L3');
                $sheet->setCellValue('AI7', 'L4');
                $sheet->setCellValue('AJ7', 'TOTAL L');

            },
        ];
    }

    public function properties(): array
    {
        return [
            'creator'        => 'PT NAG - HRIS',
            'lastModifiedBy' => 'HRIS',
            'title'          => 'Data Kehadiran Karyawan (Daily)',
            'description'    => 'Data Kehadiran Karyawan (Daily)',
            'subject'        => 'Kehadiran Karyawan',
            'keywords'       => 'kehadiran,hr,hris,hrm,daily,report,karyawan,data',
            'category'       => 'KehadiranKaryawan',
            'manager'        => 'HRIS',
            'company'        => 'PT Nirwana Alabare Garment',
        ];
    }
}
