<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Brand;
use Auth;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view_brand');

        $limit = $request->input('limit') ? : 50;
        $brands = Brand::with('creator')->where('flag', true)->paginate($limit);

        return view('brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('manage_brand');
        
        return view('brands.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('manage_brand');
        
        $request['created_by'] = $request->user()->id;
        Brand::create($request->all());

        flash(trans('validation.create_success', ['attribute' => 'brand']), 'success');

        return redirect()->route('brand.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('manage_brand');
        
        $brand = Brand::where('id', $id)->where('status', 1)->first();

        if( !$brand ) {
            return view('errors.404');
        }
        
        return view('brands.show', compact('brand'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('manage_brand');
        
        $brand = Brand::find($id);

        return view('brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('manage_brand');
        
        $brand = Brand::findOrFail($id);
        
        Brand::find($id)->update([
            'name' => $request->get('name'),
            'updated_by' => Auth::id()
            ]);

        flash(trans('validation.update_success', ['attribute' => 'brand']), 'success');

        return redirect()->route('brand.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('manage_brand');
        
        Brand::find($id)->update([
            'flag' => false,
            'updated_by' => Auth::id()
            ]);

        flash(trans('validation.delete_success', ['attribute' => 'brand']), 'success');

        return redirect()->route('brand.index');
    }
}
