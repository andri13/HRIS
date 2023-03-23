<?php

namespace App\Http\Controllers\Hris;

use App\Models\EmployeeAtribut;
use App\Http\Controllers\AdminBaseController;
use App\Models\DataKoreksiUpah;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Datatables;
use App\Exports\MasterDataAbsenKehadiranExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Class KoreksiUpahController
 * @package App\Http\Controllers\Hris
 */
class KoreksiUpahController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->dashboardActive = 'active';
        $this->pageTitle = 'Koreksi Upah';
    }

    public function index()
    {
        return View::make('hris/koreksiupah', $this->data);
    }

    public function ajax_datakoreksiupah(Request $request)
    {

        if(request()->ajax()) {

        $limit = $request->input('length');
        $start = $request->input('start');
        $totalData = 0;
        $totalFiltered = 0;

        if(empty($request->input('search.value')))
        {
            $query =  DataKoreksiUpah::selectRaw('
                        data_koreksi_upah.uuid,
                        data_koreksi_upah.kode_koreksi_upah,
                        data_koreksi_upah.tanggal_koreksi,
                        employee_atribut.enroll_id,
                        employee_atribut.nik,
                        employee_atribut.employee_name,
                        department_all.sub_dept_name,
                        department_all.department_name,
                        data_koreksi_upah.jumlah_rp_potongan,
                        data_koreksi_upah.periode_tanggal_koreksi,
                        data_koreksi_upah.operator,
                        data_koreksi_upah.keterangan,
                        data_koreksi_upah.created_at,
                        data_koreksi_upah.updated_at                            
                    ')
                    ->leftJoin('employee_atribut','data_koreksi_upah.enroll_id','=','employee_atribut.enroll_id')
                    ->leftJoin('department_all','employee_atribut.sub_dept_id','=','department_all.sub_dept_id')
                    ->orderBy('data_koreksi_upah.updated_at','desc')
                    ->offset($start)
                    ->limit($limit)
                    ->get();

            $totalData = DataKoreksiUpah::count();
            $totalFiltered = $totalData;  

        } else {
            $search = $request->input('search.value');

            $query =  DataKoreksiUpah::selectRaw('
                        data_koreksi_upah.uuid,
                        data_koreksi_upah.kode_koreksi_upah,
                        data_koreksi_upah.tanggal_koreksi,
                        employee_atribut.enroll_id,
                        employee_atribut.nik,
                        employee_atribut.employee_name,
                        department_all.department_name,
                        department_all.sub_dept_name,
                        data_koreksi_upah.jumlah_rp_potongan,
                        data_koreksi_upah.periode_tanggal_koreksi,
                        data_koreksi_upah.operator,
                        data_koreksi_upah.keterangan,
                        data_koreksi_upah.created_at,
                        data_koreksi_upah.updated_at                            
                    ')
                    ->whereRaw('
                        data_koreksi_upah.kode_koreksi_upah LIKE "%' . $search . '%"
                        OR data_koreksi_upah.tanggal_koreksi = "' . $search . '%"
                        OR employee_atribut.enroll_id LIKE "%' . $search . '%"
                        OR employee_atribut.nik LIKE "%' . $search . '%"
                        OR employee_atribut.employee_name LIKE "%' . $search . '%"
                        OR department_all.sub_dept_name LIKE "%' . $search . '%"
                        OR data_koreksi_upah.keterangan LIKE "%' . $search . '%"
                    ')                            
                    ->leftJoin('employee_atribut','data_koreksi_upah.enroll_id','=','employee_atribut.enroll_id')
                    ->leftJoin('department_all','employee_atribut.sub_dept_id','=','department_all.sub_dept_id')
                    ->orderBy('data_koreksi_upah.updated_at','desc')
                    ->offset($start)
                    ->limit($limit)
                    ->get();

            $totalData = DataKoreksiUpah::whereRaw('
                                data_koreksi_upah.kode_koreksi_upah LIKE "%' . $search . '%"
                                OR data_koreksi_upah.tanggal_koreksi = "' . $search . '%"
                                OR employee_atribut.enroll_id LIKE "%' . $search . '%"
                                OR employee_atribut.nik LIKE "%' . $search . '%"
                                OR employee_atribut.employee_name LIKE "%' . $search . '%"
                                OR department_all.site_nirwana_name LIKE "%' . $search . '%"
                                OR data_koreksi_upah.keterangan LIKE "%' . $search . '%"
                            ')
                            ->leftJoin('employee_atribut','data_koreksi_upah.enroll_id','=','employee_atribut.enroll_id')
                            ->leftJoin('department_all','employee_atribut.sub_dept_id','=','department_all.sub_dept_id')                            
                            ->count();
            $totalFiltered = $totalData;                            

        }

        $data = array();
        if(!empty($query))
        {
            foreach ($query as $q)
            {
   
                $nestedData['uuid'] = $q->uuid;
                $nestedData['kode_koreksi_upah'] = $q->kode_koreksi_upah;
                $nestedData['tanggal_koreksi'] = $q->tanggal_koreksi;
                $nestedData['enroll_id'] = $q->enroll_id;
                $nestedData['nik'] = $q->nik;
                $nestedData['employee_name'] = $q->employee_name;
                $nestedData['department_name'] = $q->department_name;
                $nestedData['sub_dept_name'] = $q->sub_dept_name;
                $nestedData['jumlah_rp_potongan'] = $q->jumlah_rp_potongan;
                $nestedData['jumlah_rp_potongan_format'] = number_format($q->jumlah_rp_potongan);
                $nestedData['periode_tanggal_koreksi'] = $q->periode_tanggal_koreksi;

                setlocale(LC_ALL, 'id-ID', 'id_ID');
                $explodePeriode = explode(" - ", $q->periode_tanggal_koreksi);
                $periodeStartKoreksi = strtoupper(strftime("%d %b %Y", strtotime(substr($explodePeriode[0], 6, 4) . "-" . substr($explodePeriode[0], 0, 2) . "-" . substr($explodePeriode[0], 3, 2))));
                $periodeEndKoreksi = strtoupper(strftime("%d %b %Y", strtotime(substr($explodePeriode[1], 6, 4) . "-" . substr($explodePeriode[1], 0, 2) . "-" . substr($explodePeriode[1], 3, 2))));
                $periode_tanggal_koreksi_format = $periodeStartKoreksi . " - " . $periodeEndKoreksi;

                $nestedData['periode_tanggal_koreksi_format'] = $periode_tanggal_koreksi_format;
                
                $nestedData['operator'] = $q->operator;
                $nestedData['keterangan'] = $q->keterangan;
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

    public function create(Request $request)
    {
        $loggedAdmin = Auth::guard('admin')->user();
        $email = $loggedAdmin->email;

        $kode_koreksi_upah = $request->kode_koreksi_upah;
        $tanggal_koreksi = $request->tanggal_koreksi;
        $enroll_id = $request->enroll_id;
        $nik = $request->nik;
        $employee_name = $request->employee_name;
        $site_nirwana_id = $request->site_nirwana_id;
        $site_nirwana_name = $request->site_nirwana_name;
        $department_id = $request->department_id;
        $department_name = $request->department_name;
        $sub_dept_id = $request->sub_dept_id;
        $sub_dept_name = $request->sub_dept_name;
        $jumlah_rp_potongan = $request->jumlah_rp_potongan;
        $periode_tanggal_koreksi = $request->periode_tanggal_koreksi;
        $keterangan = $request->keterangan;
        //sebelumnya
        // $findDT = DataKoreksiUpah::where('kode_koreksi_upah','=', $kode_koreksi_upah)->count();

        // if($findDT > 0) {
        //     $query = false;

        $findDT = DataKoreksiUpah::where('enroll_id',$request->enroll_id)->where('periode_tanggal_koreksi',$request->periode_tanggal_koreksi)->count();

        if($findDT > 0) {
            $query = false;
        } else {
            $query = DataKoreksiUpah::create([
                'uuid' => Str::uuid(),
                'kode_koreksi_upah' => $kode_koreksi_upah,
                'tanggal_koreksi' => $tanggal_koreksi,
                'enroll_id' => $enroll_id,
                'nik' => $nik,
                'employee_name' => $employee_name,
                'site_nirwana_id' => $site_nirwana_id,
                'site_nirwana_name' => $site_nirwana_name,
                'department_id' => $department_id,
                'department_name' => $department_name,
                'sub_dept_id' => $sub_dept_id,
                'sub_dept_name' => $sub_dept_name,
                'jumlah_rp_potongan' => $jumlah_rp_potongan,
                'periode_tanggal_koreksi' => $periode_tanggal_koreksi,
                'keterangan' => $keterangan,
                'operator' => $email
            ]);
        }

        return $query;
    }

    public function update(Request $request)
    {
        $loggedAdmin = Auth::guard('admin')->user();
        $email = $loggedAdmin->email;

        $uuid = $request->uuid;
        $kode_koreksi_upah = $request->kode_koreksi_upah;
        $tanggal_koreksi = $request->tanggal_koreksi;
        $enroll_id = $request->enroll_id;
        $nik = $request->nik;
        $employee_name = $request->employee_name;
        $site_nirwana_id = $request->site_nirwana_id;
        $site_nirwana_name = $request->site_nirwana_name;
        $department_id = $request->department_id;
        $department_name = $request->department_name;
        $sub_dept_id = $request->sub_dept_id;
        $sub_dept_name = $request->sub_dept_name;
        $jumlah_rp_potongan = $request->jumlah_rp_potongan;
        $periode_tanggal_koreksi = $request->periode_tanggal_koreksi;
        $keterangan = $request->keterangan;

        $query = DataKoreksiUpah::where('uuid','=', $uuid)
                    ->update([
                        'kode_koreksi_upah' => $kode_koreksi_upah,
                        'tanggal_koreksi' => $tanggal_koreksi,
                        'enroll_id' => $enroll_id,
                        'nik' => $nik,
                        'employee_name' => $employee_name,
                        // 'site_nirwana_id' => $site_nirwana_id,
                        // 'site_nirwana_name' => $site_nirwana_name,
                        // 'department_id' => $department_id,
                        // 'department_name' => $department_name,
                        // 'sub_dept_id' => $sub_dept_id,
                        // 'sub_dept_name' => $sub_dept_name,
                        'jumlah_rp_potongan' => $jumlah_rp_potongan,
                        'periode_tanggal_koreksi' => $periode_tanggal_koreksi,
                        'keterangan' => $keterangan,
                        'operator' => $email
                    ]);

        return $query;
    }

    public function destroy(Request $request)
    {
        $uuid = $request->uuid;

        $findDT = DataKoreksiUpah::where('uuid','=', $uuid)->count();

        if($findDT > 0) {
            $query = DataKoreksiUpah::where('uuid','=',$uuid)->delete();
        } else {
            $query = false;
        }

        return $query;
    }

     //=============Andri====================
     public function format_import_koreksiupah()
     {
         $filepath = public_path('format_import/format_import_koreksi_upah.xlsx');
         return Response()->download($filepath);
     }
 
     public function import_koreksiupah(Request $request)
     {
        try{
            $data=Excel::toArray([],$request->file('file_import'));
            $loggedAdmin = Auth::guard('admin')->user();
            $loggedAdmin->email;

            $data_import=[];

            $head=$data[0][0];
            if($head[0]=='enroll_id' && $head[3]=='jumlah_rp_koreksi' ){
                foreach ($data[0] as $key2 => $row) {
                    if($key2>0){
                        $error=$key2;
                        $employee=EmployeeAtribut::where('enroll_id',$row[0])->first();
                        $nik=$employee->nik??null;

                        $tgl_awal =$row[4];
                        $tgl_awal = ($tgl_awal - 25569) * 86400;
                        $tgl_awal = 25569 + ($tgl_awal / 86400);
                        $tgl_awal = ($tgl_awal - 25569) * 86400;
                        $tgl_priode_awal=date('m/d/Y', $tgl_awal);

                        $tgl_akhir =$row[5];
                        $tgl_akhir = ($tgl_akhir - 25569) * 86400;
                        $tgl_akhir = 25569 + ($tgl_akhir / 86400);
                        $tgl_akhir = ($tgl_akhir - 25569) * 86400;
                        $tgl_priode_akhir=date('m/d/Y', $tgl_akhir);


                        $data_import[]=[
                            'kode_koreksi_upah'=>date('Y').date('m').date('i').date('s').$nik,
                            'tanggal_koreksi'=>date('Y-m-d'),
                            'enroll_id'=>$row[0],
                            'nik'=>$employee->nik??null,
                            'employee_name'=>$employee->employee_name??null,
                            'site_nirwana_id'=>$employee->site_nirwana_id??null,
                            'site_nirwana_name'=>$employee->site_nirwana_name??null,
                            'department_id'=>$employee->department_id??null,
                            'department_name'=>$employee->department_name??null,
                            'sub_dept_id'=>$employee->sub_dept_id??null,
                            'sub_dept_name'=>$employee->sub_dept_name??null,
                            'jumlah_rp_potongan'=>$row[3],
                            'periode_tanggal_koreksi'=>$tgl_priode_awal.' - '.$tgl_priode_akhir,
                            'keterangan'=>$row[6],
                            'operator'=> $loggedAdmin->email
                        ];
                    }
                }
                $collect=collect($data_import)->where('nik','!=',null);

                foreach ($collect as $key => $value) {
                    $findDT = DataKoreksiUpah::where('enroll_id',$value['enroll_id'])->where('periode_tanggal_koreksi',$value['periode_tanggal_koreksi'])->count();
                    if($findDT){
                        $data_update=[
                            'kode_koreksi_upah'=>$value['kode_koreksi_upah'],
                            'tanggal_koreksi'=>$value['tanggal_koreksi'],    
                            'enroll_id'=>$value['enroll_id'],
                            'nik'=>$value['nik'],            
                            'employee_name'=>$value['employee_name'],
                            'site_nirwana_id'=>$value['site_nirwana_id'],
                            'site_nirwana_name'=>$value['site_nirwana_name'],
                            'department_id'=>$value['department_id'],
                            'department_name'=>$value['department_name'],
                            'sub_dept_id'=>$value['sub_dept_id'],
                            'sub_dept_name'=>$value['sub_dept_name'],
                            'jumlah_rp_potongan'=>$value['jumlah_rp_potongan'],
                            'periode_tanggal_koreksi'=>$value['periode_tanggal_koreksi'],
                            'keterangan'=>$value['keterangan'],
                            'operator'=>$value['operator'],        
                        ];
                        DataKoreksiUpah::where('enroll_id',$value['enroll_id'])->where('periode_tanggal_koreksi',$value['periode_tanggal_koreksi'])->update($data_update);
                    }
                    else{
                        $data_insert=[
                            'uuid' => Str::uuid(),
                            'kode_koreksi_upah'=>$value['kode_koreksi_upah'],
                            'tanggal_koreksi'=>$value['tanggal_koreksi'],    
                            'enroll_id'=>$value['enroll_id'],
                            'nik'=>$value['nik'],            
                            'employee_name'=>$value['employee_name'],
                            'site_nirwana_id'=>$value['site_nirwana_id'],
                            'site_nirwana_name'=>$value['site_nirwana_name'],
                            'department_id'=>$value['department_id'],
                            'department_name'=>$value['department_name'],
                            'sub_dept_id'=>$value['sub_dept_id'],
                            'sub_dept_name'=>$value['sub_dept_name'],
                            'jumlah_rp_potongan'=>$value['jumlah_rp_potongan'],
                            'periode_tanggal_koreksi'=>$value['periode_tanggal_koreksi'],
                            'keterangan'=>$value['keterangan'],
                            'operator'=>$value['operator'],        
                        ];
                        DataKoreksiUpah::create($data_insert);
                    }
                }

                return back()->with("success",'berhasil disimpan');
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
