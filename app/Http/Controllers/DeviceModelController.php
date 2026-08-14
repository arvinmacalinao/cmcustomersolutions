<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\DeviceModelRequest;
use App\Http\Controllers\Controller;

use Auth;
use DB;
use Carbon\Carbon;

use App\DeviceModel;
use App\Bom;

class DeviceModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view_model');

        $limit = $request->input('limit') ? : 30;
        $code = trim($request->input('code')) ? : null; 
        $name = trim($request->input('name')) ? : null; 

        $models = DeviceModel::with('brand', 'deviceType', 'creator')
                            ->where('flag', true)
                            ->where(function($query) use($code, $name) {
                                                                            if ($code) {
                                                                                $query->where('code', 'like', '%'.$code.'%');
                                                                            }
                                                                            
                                                                            if ($name) {
                                                                                $query->where('name', 'like', '%'.$name.'%');
                                                                            }
                                                                        })
                            ->orderby('created_at','DESC')
                            ->paginate($limit);

        return view('deviceModels.index', compact('models'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('manage_model');

        $device_types = DB::table('device_types')->where('flag', true)->lists('name', 'id');
        
        return view('deviceModels.create', compact('device_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeviceModelRequest $request)
    {
        $this->authorize('manage_model');

        $request['created_by'] = Auth::id();
        
        DeviceModel::create($request->all());

        flash(trans('validation.create_success', ['attribute' => 'model']), 'success');

        return redirect()->route('model.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('manage_model');

        $model = DeviceModel::lockForUpdate()->find($id);
        $device_types = DB::table('device_types')->where('flag', true)->lists('name', 'id');

        return view('deviceModels.edit', compact('model', 'device_types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DeviceModelRequest $request, $id)
    {
        $this->authorize('manage_model');
        
        DeviceModel::find($id)->update([
            'code' => $request->get('code'),
            'name' => $request->get('name'),
            'brand_id' => $request->get('brand_id'),
            'device_type_id' => $request->get('device_type_id'),
            'warranty' => $request->get('warranty'),
            'price' => $request->get('price'),
            'labor_cost_1' => $request->get('labor_cost_1'),
            'labor_cost_2' => $request->get('labor_cost_2'),
            'labor_cost_3' => $request->get('labor_cost_3'),
            'updated_by' => Auth::id()
        ]);

        flash(trans('validation.update_success', ['attribute' => 'model']), 'success');

        return redirect()->route('model.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('manage_model');
        
        DeviceModel::find($id)->update([
            'flag' => false,
            'updated_by' => Auth::id()
        ]);

        flash(trans('validation.delete_success', ['attribute' => 'model']), 'success');

        return redirect()->route('model.index');
    }

    /**
     * Show the form for assigning BOM to selected Model.
     *
     * @return \Illuminate\Http\Response
     */
    public function createModelBom()
    {
        $device_models = DeviceModel::where('flag', true)->orderBy('name', 'asc')->lists('name', 'id');
        $bom = Bom::where('flag', true)->where('status', 1)->orderBy('code', 'asc')->lists('code', 'id');
        
        return view('deviceModels.createBom', compact('device_models', 'bom'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeModelBom(Request $request)
    {
        $bom_exist = DB::table('bom_device_model')
                        ->where('device_model_id', $request->device_model_id)
                        ->where('bom_id', $request->bom_id)
                        ->get();

        if( !$bom_exist ){
            DB::table('bom_device_model')
                ->insert([
                    'device_model_id' => $request->device_model_id, 
                    'bom_id' => $request->bom_id,
                    'category' => $request->category,
                    'created_by' => Auth::id(),
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]);
        }

        flash(trans('validation.create_success', ['attribute' => 'assignment of BOM to model']), 'success');

        return redirect()->route('model.bom.create');
    }

    /**
     * Retrieve list of BOM items for selected model.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getModelBom(Request $request)
    {
        $bom = DB::table('bom_device_model')
                    ->join('bom', 'bom_device_model.bom_id', '=', 'bom.id')
                    ->join('users', 'bom_device_model.created_by', '=', 'users.id')
                    ->select('bom.code', 'bom.name', 'bom_device_model.category', 'users.name as creator')
                    ->where('device_model_id', $request->model_id)
                    ->get();

        $html = view('deviceModels.bomList')->with('bom', $bom)->render();
        
        return $html;
    }
}
