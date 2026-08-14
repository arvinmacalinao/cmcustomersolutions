<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\DeviceInventoryRequest;
use App\Http\Controllers\Controller;

use App\DeviceInventory;
use App\DeviceModel;
use Auth;

class DeviceInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view_inventory');

        $limit = $request->input('limit') ? : 30;
        $imei = trim($request->input('imei')) ? : null; 
        $code = trim($request->input('code')) ? : null; 

        $inventories = DeviceInventory::with('model', 'creator', 'updater')
                                        ->where('flag', true)
                                        ->where(function ($query) use($imei) {
                                                    if ($imei) {
                                                        $query->where('imei', 'like', '%'.$imei.'%');
                                                    }
                                                })
                                        ->whereHas('model', function ($query) use($code) {
                                                        if ($code) {
                                                            $query->where('code', 'like', '%'.$code.'%');
                                                        }
                                                    })
                                        ->paginate($limit);

        //dd($inventories);

        return view('deviceInventories.index', compact('inventories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('manage_inventory');

        $models = DeviceModel::where('flag', true)->orderBy('name', 'asc')->lists('name', 'id')->toArray();

        return view('deviceInventories.create', compact('models'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeviceInventoryRequest $request)
    {
        $this->authorize('manage_inventory');

        $request['created_by'] = $request->user()->id;
        
        DeviceInventory::create($request->all());

        flash(trans('validation.create_success', ['attribute' => 'IMEI']), 'success');

        return redirect()->route('device_inventory.index');
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
    public function edit($imei)
    {
        $this->authorize('manage_inventory');

        $inventory = DeviceInventory::where('imei', $imei)->select('imei', 'device_model_id', 'color')->lockForUpdate()->firstOrFail();
        $models = DeviceModel::where('flag', true)->orderBy('code', 'asc')->lists('code', 'id')->toArray();

        return view('deviceInventories.edit', compact('inventory', 'models'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DeviceInventoryRequest $request, $imei)
    {
        $this->authorize('manage_inventory');

        $inventory = DeviceInventory::findOrFail($imei);
        
        DeviceInventory::find($imei)->update([
            'imei' => $request->get('imei'),
            'device_model_id' => $request->get('device_model_id'),
            'color' => $request->get('color'),
            'updated_by' => Auth::id()
        ]);

        flash(trans('validation.update_success', ['attribute' => 'inventory']), 'success');

        return redirect()->route('device_inventory.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('manage_inventory'); 

        DeviceInventory::find($id)->update([
            'flag' => false,
            'updated_by' => Auth::id()
        ]);

        flash(trans('validation.delete_success', ['attribute' => 'IMEI']), 'success');

        return redirect()->route('device_inventory.index');
    }
}
