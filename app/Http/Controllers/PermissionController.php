<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\PermissionRequest;
use App\Http\Controllers\Controller;
use App\Permission;

use Auth;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ? : 50;
        $permissions = Permission::with('creator')->where('flag', true)->orderBy('permission_name', 'asc')->paginate($limit);

        return view('permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission_list = Permission::where('flag', true)->lists('permission_label', 'id')->toArray();

        //dd($permission_list);

        return view('permissions.create', compact('permission_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PermissionRequest $request)
    {
        $request['created_by'] = $request->user()->id;
        $request['parent_id'] = empty($request['parent_id']) ? 0 : $request['parent_id'];
        
        Permission::create($request->all());

        flash(trans('validation.create_success', ['attribute' => 'permission']), 'success');

        return redirect()->route('permission.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Permission::find($id);
        $permission_list = Permission::where('flag', true)->lists('permission_label', 'id')->toArray();

        return view('permissions.edit', compact('permission', 'permission_list'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionRequest $request, $id)
    {
        Permission::findOrFail($id)->update([
            'permission_name' => $request->get('permission_name'),
            'permission_label' => $request->get('permission_label'),
            'description' => $request->get('description'),
            'parent_id' => $request->get('parent_id'),
            'updated_by' => Auth::id()
        ]);

        flash(trans('validation.update_success', ['attribute' => 'permission']), 'success');

        return redirect()->route('permission.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Permission::find($id)->update([
            'flag' => false,
            'updated_by' => Auth::id()
            ]);

        flash(trans('validation.delete_success', ['attribute' => 'permission']), 'success');

        return back();
    }
}
