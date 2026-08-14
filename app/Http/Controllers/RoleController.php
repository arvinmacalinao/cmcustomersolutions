<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\RoleRequest;
use App\Http\Controllers\Controller;
use App\Role;
use App\Permission;

use Auth;
use DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ? : 50;
        $roles = Role::with('creator')->where('flag', true)->orderBy('role_name', 'asc')->paginate($limit);

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $request['created_by'] = $request->user()->id;
        Role::create($request->all());

        flash(trans('validation.create_success', ['attribute' => 'role']), 'success');

        return redirect()->route('role.index');
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
        $role = Role::find($id);

        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        Role::findOrFail($id)->update([
            'role_name' => $request->get('role_name'),
            'role_label' => $request->get('role_label'),
            'updated_by' => Auth::id()
        ]);

        flash(trans('validation.update_success', ['attribute' => 'role']), 'success');

        return redirect()->route('role.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('super_admin');
        
        Role::find($id)->update([
            'flag' => false,
            'updated_by' => Auth::id()
        ]);

        flash(trans('validation.delete_success', ['attribute' => 'role']), 'success');

        return back();
    }

    /**
     * Show the form which allow user to set permission for the role selected.
     *
     * @return \Illuminate\Http\Response
     */
    public function createRolePermission()
    {
        // Validate the request...
        $role_list = Role::where('flag', true)->lists('role_label', 'id')->toArray();
        /*
        $parent_permission_list = Permission::where('flag', true)->where('parent_id', 0)->lists('permission_label', 'id')->toArray();
        $child_permission_list = Permission::where('flag', true)->where('parent_id', '<>', 0)->lists('id', 'parent_id', 'permission_label');
        
        return view('roles.setPermission', compact('role_list', 'parent_permission_list', 'child_permission_list'));
        */
        $parent_permission = Permission::where('flag', true)
                                        ->where('parent_id', 0)
                                        ->select('id', 'permission_name', 'permission_label')
                                        ->get();

        $child_permission = Permission::where('flag', true)
                                        ->where('parent_id', '<>', 0)
                                        ->select('id', 'permission_name', 'permission_label', 'parent_id')
                                        ->get();

        return view('roles.setPermission', compact('role_list', 'parent_permission', 'child_permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeRolePermission(Request $request)
    {
        $this->validate($request, [
            'role_id' => 'required|integer|exists:roles,id,flag,1',
            'permission' => 'required|array',
        ]);

        $role = Role::find($request->role_id);
        $role->setPermission($request);

        flash(trans('validation.create_success', ['attribute' => 'role and permission']), 'success');

        return back();
    }

    /**
     * Show the form which allow user to set permission for the role selected.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRolePermission($id)
    {
        $permissions = DB::table('permission_role')->where('role_id', $id)->lists('permission_id');

        return $permissions;
    }
}
