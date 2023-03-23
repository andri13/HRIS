<?php

namespace App\Http\Controllers\Hris;

use App\Models\DepartmentAll;
use App\Http\Controllers\AdminBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Datatables;


/**
 * Class MdAbsenHadirController
 * @package App\Http\Controllers\Hris
 */
class DepartmentAllController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->dashboardActive = 'active';
        $this->pageTitle = 'Master Data';
    }

    public function index()
    {
        return View::make('hris/departmentall', $this->data);
    }

    public function getDepartmentName($id)
    {
        $query =  DepartmentAll::where('department_id','=',$id)
                                 ->groupby('sub_dept_id')
                                 ->first();
        return $query["department_name"];

    }

    public function getSelectSubDept(Request $request)
    {
        $department_id = $request->department_id;
        
        $query =  DepartmentAll::where('department_id','=',$department_id)
                    ->groupBy('sub_dept_id')
                    ->orderBy('sub_dept_name','asc')
                    ->get();

        return $query;

    }

    public function ajax_departmentall(Request $request)
    {
        if(request()->ajax()) {

            $columns = array(
                0 => 'site_nirwana_id',
                1 => 'site_nirwana_name',
                2 => 'department_id',
                3 => 'department_name',
                4 => 'sub_dept_id',
                5 => 'sub_dept_name'
            );

            $totalData = DepartmentAll::count();
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if(empty($request->input('search.value')))
            {
                $query = DepartmentAll::offset($start)
                             ->limit($limit)
                             ->orderBy($order,$dir)
                             ->get();
            } else {
                $search = $request->input('search.value');

                $query =  DepartmentAll::where('site_nirwana_id','LIKE',"%{$search}%")
                                ->orWhere('site_nirwana_name', 'LIKE',"%{$search}%")
                                ->orWhere('department_id', 'LIKE',"%{$search}%")
                                ->orWhere('department_name', 'LIKE',"%{$search}%")
                                ->orWhere('sub_dept_id', 'LIKE',"%{$search}%")
                                ->orWhere('sub_dept_name', 'LIKE',"%{$search}%")
                                ->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir)
                                ->get();

                $totalFiltered = DepartmentAll::where('site_nirwana_id','LIKE',"%{$search}%")
                                ->orWhere('site_nirwana_name', 'LIKE',"%{$search}%")
                                ->orWhere('department_id', 'LIKE',"%{$search}%")
                                ->orWhere('department_name', 'LIKE',"%{$search}%")
                                ->orWhere('sub_dept_id', 'LIKE',"%{$search}%")
                                ->orWhere('sub_dept_name', 'LIKE',"%{$search}%")
                                ->count();
            }

            $data = array();
            if(!empty($query))
            {
                foreach ($query as $q)
                {
                    $showData = $q->site_nirwana_id . "/" . $q->department_id . "/" . $q->sub_dept_id;
                    $addeditData = $q->site_nirwana_id . "/" . $q->department_id . "/" . $q->sub_dept_id;
                    $nestedData['site_nirwana_id'] = $q->site_nirwana_id;
                    $nestedData['site_nirwana_name'] = $q->site_nirwana_name;
                    $nestedData['department_id'] = $q->department_id;
                    $nestedData['department_name'] = $q->department_name;
                    $nestedData['sub_dept_id'] = $q->sub_dept_id;
                    $nestedData['sub_dept_name'] = $q->sub_dept_name;
                    $nestedData['option'] = '
                    <a href="" type="button" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <span class="glyphicon glyphicon-list"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        <li><a  href="javascript:void(0)" data-toggle="tooltip" id="showData-link" data-id="' . $showData . '" data-original-title="Show">Show</a></li>
                        <li><a  href="javascript:void(0)" data-toggle="tooltip" id="addeditData-link" data-id="' . $addeditData . '" data-original-title="Edit">Edit</a></li>
                        <li><a href="#">Delete</li>
                    </ul>';

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

    public function show_data(Request $request)
    {
        $showData = $request->showData;
        $dataAjax = explode("/", $showData);
        $site_nirwana_id = $dataAjax[0];
        $department_id = $dataAjax[1];
        $sub_dept_id = $dataAjax[2];

        $query =  DepartmentAll::where('site_nirwana_id','=',$site_nirwana_id)
        ->where('department_id', '=',$department_id)
        ->where('sub_dept_id', '=',$sub_dept_id)
        ->first();

        return Response()->json($query);

    }

    public function edit_data(Request $request)
    {
        $editData = $request->editData;
        $dataAjax = explode("/", $editData);
        $site_nirwana_id = $dataAjax[0];
        $department_id = $dataAjax[1];
        $sub_dept_id = $dataAjax[2];

        $query =  DepartmentAll::where('site_nirwana_id','=',$site_nirwana_id)
        ->where('department_id', '=',$department_id)
        ->where('sub_dept_id', '=',$sub_dept_id)
        ->first();

        return Response()->json($query);

    }

    public function store(Request $request)
    {
        $site_nirwana_id = $request->site_nirwana_id;
        $site_nirwana_name = $request->site_nirwana_name;
        $department_id = $request->department_id;
        $department_name = $request->department_name;
        $sub_dept_id = $request->sub_dept_id;
        $sub_dept_name = $request->sub_dept_name;

        $query =  DepartmentAll::where('site_nirwana_id','=',$site_nirwana_id)
                    ->where('department_id', '=',$department_id)
                    ->where('sub_dept_id', '=',$sub_dept_id)
                    ->update([
                        'site_nirwana_id' => $site_nirwana_id,
                        'site_nirwana_name' => $site_nirwana_name,
                        'department_id' => $department_id,
                        'department_name' => $department_name,
                        'sub_dept_id' => $sub_dept_id,
                        'sub_dept_name' => $sub_dept_name
                    ]);

        return Response()->json($query);
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
