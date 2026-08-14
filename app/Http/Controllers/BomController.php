<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\BomRequest;
use App\Http\Controllers\Controller;

use Auth;

use App\Bom;
use App\Brand;

class BomController extends Controller
{
    /**
     * Display a listing of BOM items.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('bom_mgmt');

        $limit = $request->input('limit') ? : 50;
        $code = trim($request->input('code'));
        $name = trim($request->input('name'));

        $bom = Bom::with('creator')
                    ->where(function ($query) use($code, $name) {
                                if ($code) {
                                    $query->where('code', 'like', '%'.$code.'%');
                                }

                                if ($name) {
                                    $query->where('name', 'like', '%'.$name.'%');
                                }
                            })
                    ->orderby('code','ASC')
                    ->paginate($limit);

        return view('bom.index', compact('bom'));
    }


    /**
     * Show the form for creating a new BOM item.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('bom_mgmt');

        $brands = Brand::where('flag', 1)->lists('name', 'id')->all();

        return view('bom.create', compact('brands'));
    }


    /**
     * Store a newly created BOM item into DB.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BomRequest $request)
    {
        $this->authorize('bom_mgmt');

        Bom::create([
            'code' => $request['code'], 
            'name' => $request['name'], 
            'brand_id' => $request['brand_id'], 
            'warranty' => $request['warranty'], 
            'quantity' => $request['quantity'], 
            'suggested_retail_price' => $request['suggested_retail_price'],
            'retail_price' => $request['retail_price'],
            'dealer_price' => $request['dealer_price'],
            'status' => 1,
            'flag' => true,
            'created_by' => $request->user()->id,
        ]);

        flash(trans('validation.create_success', ['attribute' => 'BOM']), 'success');

        return redirect()->route('bom.index');
    }


    /**
     * Show the form for editing the BOM item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('bom_mgmt');

        $bom = Bom::find($id);
        $brands = Brand::where('flag', 1)->lists('name', 'id')->all();

        return view('bom.edit', compact('bom', 'brands'));
    }


    /**
     * Update the specified BOM item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BomRequest $request, $id)
    {
        $this->authorize('bom_mgmt');
        
        BOM::find($id)->update([
            'code' => $request['code'], 
            'name' => $request['name'], 
            'brand_id' => $request['brand_id'], 
            'warranty' => $request['warranty'], 
            'quantity' => $request['quantity'], 
            'suggested_retail_price' => $request['suggested_retail_price'],
            'retail_price' => $request['retail_price'],
            'dealer_price' => $request['dealer_price'],
            'created_by' => Auth::id()
        ]);

        flash(trans('validation.update_success', ['attribute' => 'BOM']), 'success');

        return redirect()->route('bom.index');
    }
}
