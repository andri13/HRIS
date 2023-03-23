<?php

namespace App\Http\Controllers\Hris;

use App\Http\Controllers\AdminBaseController;
use App\Models\RefHariLibur;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Datatables;
use App\Exports\MasterDataAbsenKehadiranExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Class RefHariLiburController
 * @package App\Http\Controllers\Hris
 */
class RefHariLiburController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->dashboardActive = 'active';
        $this->pageTitle = 'Referensi Hari Libur';
    }

    public function index()
    {
        return View::make('hris/refharilibur', $this->data);
    }

    public function ajax_refharilibur(Request $request)
    {

        if(request()->ajax()) {

        $columns = array(
            0 => 'id',
            1 => 'nama_hari_libur',
            2 => 'tanggal_libur',
            3 => 'status_absen'
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $totalData = 0;
        $totalFiltered = 0;

        if(empty($request->input('search.value')))
        {
            $query =  RefHariLibur::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalData = RefHariLibur::count();
            $totalFiltered = $totalData;  

        } else {
            $search = $request->input('search.value');

            $query =  RefHariLibur::where('id','LIKE',"%{$search}%")
                            ->orWhere('nama_hari_libur','LIKE',"%{$search}%")
                            ->orWhere('tanggal_libur','LIKE',"%{$search}%")
                            ->orWhere('status_absen','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalData = RefHariLibur::where('id','LIKE',"%{$search}%")
                            ->orWhere('nama_hari_libur','LIKE',"%{$search}%")
                            ->orWhere('tanggal_libur','LIKE',"%{$search}%")
                            ->orWhere('status_absen','LIKE',"%{$search}%")
                            ->count();
            $totalFiltered = $totalData;                            

        }

        $data = array();
        if(!empty($query))
        {
            foreach ($query as $q)
            {
                $nestedData['id'] = $q->id;
                $nestedData['nama_hari_libur'] = $q->nama_hari_libur;
                $nestedData['tanggal_libur'] = $q->tanggal_libur;
                $nestedData['status_absen'] = $q->status_absen;
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

    public function replace(Request $request)
    {
        $id = $request->id;
        $nama_hari_libur = $request->nama_hari_libur;
        $tanggal_libur = $request->tanggal_libur;
        $status_absen = $request->status_absen;

        $findDT = RefHariLibur::where('id','=', $id)->count();

        if($findDT > 0) {
            $query = RefHariLibur::where('id','=', $id)
            ->update([
                'nama_hari_libur' => $nama_hari_libur,
                'tanggal_libur' => $tanggal_libur,
                'status_absen' => $status_absen
            ]);
        } else {
            $query = RefHariLibur::create([
                'nama_hari_libur' => $nama_hari_libur,
                'tanggal_libur' => $tanggal_libur,
                'status_absen' => $status_absen
            ]);
        }

        return $query;
    }

    public function destroy(Request $request)
    {
        $id = $request->id;

        $findDT = RefHariLibur::where('id','=', $id)->count();

        if($findDT > 0) {
            $query = RefHariLibur::where('id','=',$id)->delete();
        } else {
            $query = false;
        }

        return $query;
    }

}
