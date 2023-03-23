<?php

namespace App\Exports;

use App\Http\Controllers\Hris\RekapKehadiranKaryawanController;
use App\Models\RekapKehadiranKaryawan;
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

use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use \Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\DB;

use Auth;

class RekapKehadiranKaryawanExport implements FromQuery, WithMapping, ShouldAutoSize, WithEvents, WithCustomStartCell, WithTitle
{
    use Exportable;

    public function exportParams(string $periode_payroll, string $status_staff, string $searchData)
    {
        $this->periode_payroll = $periode_payroll;
        $this->status_staff = $status_staff;
        $this->searchData = $searchData;

        return $this;
    }

    public function query()
    {

        $periode_payroll = $this->periode_payroll;

        $status_staff = $this->status_staff;
        if($status_staff) {
            $whereStatusStaff = ' AND upper(status_staff) = upper( "' . $status_staff . '" ) ';
        } else {
            $whereStatusStaff = '';
        }

        $searchData = $this->searchData;
        if($searchData) {
            $whereSearchData = ' AND (upper( enroll_id ) LIKE upper( "%' . $searchData . '%" ) OR upper( employee_name ) LIKE upper( "%' . $searchData . '%" ) OR upper( sub_dept_name ) LIKE upper( "%' . $searchData . '%" ) OR upper( status_aktif ) LIKE upper( "%' . $searchData . '%" ) ) ';
        } else {
            $whereSearchData = '';
        }

        $q =  RekapKehadiranKaryawan::query()
                ->whereRaw('
                    periode_payroll = "' . $periode_payroll . '"
                    ' . $whereStatusStaff . '
                    ' . $whereSearchData . '
                ')
                ->orderBy('employee_name','asc')
                ->limit(1);

        return $q;
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function map($Data): array
    {
        $enroll_id = $Data->enroll_id;
        $nik = $Data->nik;
        $employee_name = $Data->employee_name;
        $sub_dept_name = $Data->sub_dept_name;
        $status_aktif = $Data->status_aktif;
        $status_staff = $Data->status_staff;
        $kehadiran_iby = $Data->kehadiran_iby;
        $kehadiran_itb = $Data->kehadiran_itb;
        $kehadiran_lby = $Data->kehadiran_lby;
        $kehadiran_lsm = $Data->kehadiran_lsm;
        $kehadiran_dt = $Data->kehadiran_dt;
        $kehadiran_pc = $Data->kehadiran_pc;
        $kehadiran_dtpc = $Data->kehadiran_dtpc;
        $kehadiran_m = $Data->kehadiran_m;
        $kehadiran_r = $Data->kehadiran_r;
        $kehadiran_tk = $Data->kehadiran_tk;
        $kehadiran_ok = $Data->kehadiran_ok;
        $total_kehadiran = $Data->total_kehadiran;
        $total_kehadiran_net = $Data->total_kehadiran_net;
        $updated_at = substr($Data->updated_at, 1, 19);

        return [
            $enroll_id,
            $nik,
            $employee_name,
            $sub_dept_name,
            $status_aktif,
            $status_staff,
            $kehadiran_iby,
            $kehadiran_itb,
            $kehadiran_lby,
            $kehadiran_lsm,
            $kehadiran_dt,
            $kehadiran_pc,
            $kehadiran_dtpc,
            $kehadiran_m,
            $kehadiran_r,
            $kehadiran_tk,
            $kehadiran_ok,
            $total_kehadiran,
            $total_kehadiran_net,
            $updated_at
        ];
    }

    public function title(): string
    {
        return 'REKAPKEHADIRANKARYAWAN';
    }

    public function registerEvents() : array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $sheet = $event->sheet;
                $sheet->setCellValue('A1', 'PT NIRWANA ALABARE GARMENT');
                $sheet->getDelegate()->getStyle('A1')->getFont()->setSize(18);
                $sheet->setCellValue('A2', 'Laporan Rekap Kehadiran Karyawan');
                $sheet->getDelegate()->getStyle('A1')->getFont()->setSize(16);
                $sheet->setCellValue('A3', 'Periode Payroll  : ' . $this->periode_payroll);
                $sheet->getDelegate()->getStyle('A1')->getFont()->setSize(14);
                $sheet->mergeCells('A1:D1');
                $sheet->mergeCells('A2:D2');
                $sheet->mergeCells('A3:D3');

                $sheet->setCellValue('A5', 'Nomor Absen');
                $sheet->setCellValue('B5', 'NIK');
                $sheet->setCellValue('C5', 'Nama Karyawan');
                $sheet->setCellValue('D5', 'Bagian');
                $sheet->setCellValue('E5', 'Aktif');
                $sheet->setCellValue('F5', 'Staff');
                $sheet->setCellValue('G5', 'IBY');
                $sheet->setCellValue('H5', 'ITB');
                $sheet->setCellValue('I5', 'LBY');
                $sheet->setCellValue('J5', 'LSM');
                $sheet->setCellValue('K5', 'DT');
                $sheet->setCellValue('L5', 'PC');
                $sheet->setCellValue('M5', 'DTPC');
                $sheet->setCellValue('N5', 'M');
                $sheet->setCellValue('O5', 'R');
                $sheet->setCellValue('P5', 'TK');
                $sheet->setCellValue('Q5', 'OK');
                $sheet->setCellValue('R5', 'Total');
                $sheet->setCellValue('S5', 'Net');
                $sheet->setCellValue('T5', 'Perubahan Terakhir');

            },
        ];
    }

    public function properties(): array
    {
        return [
            'creator'        => 'PT NAG - HRIS',
            'lastModifiedBy' => 'HRIS',
            'title'          => 'Rekap Perhitungan Kehadiran Karyawan',
            'description'    => 'Rekap Perhitungan Kehadiran Karyawan',
            'subject'        => 'Rekap Perhitungan Kehadiran Karyawan',
            'keywords'       => 'perhitungan,kehadiran,hr,hris,hrm,daily,report,karyawan,data',
            'category'       => 'RekapPerhitunganKehadiranKaryawan',
            'manager'        => 'HRIS',
            'company'        => 'PT Nirwana Alabare Garment',
        ];
    }
}
