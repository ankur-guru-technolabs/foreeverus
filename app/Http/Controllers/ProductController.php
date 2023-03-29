<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Passion;
use App\Models\Products;


class ProductController extends Controller
{
	public function index()
	{
		$product = Products::all();
        return view('product.index', ['product' => $product]);
	}

	public function create()
    {
        return view('product.create', []);
    }

	public  function edit($id)
    {
    	$product = Products::find($id);
        return view('product.edit', ['product' => $product]);
    }

	public function store(Request $request)
	{
		$messages = array(
            'product_name.required'   => 'Product Name field is required.',
        );

        $request->validate([
            'product_name' => 'required',
            'price'     => 'required',
            'qty'       => 'required',
            'type'       => 'required',
            'status'     => 'required'
        ],$messages);

        $params            = $request->all();

        $result            = Products::addUpdatePlan($params);
        if($result) {
            return redirect()->route('product.index')->withSuccess('Product successfully Added.');
        }

        return redirect('product')->withErrors(__('Something went wrong!'));
	}

	public function update($id, Request $request)
    {
        $messages = array(
            'product_name.required'   => 'Product Name field is required.',
        );

        $request->validate([
            'product_name' => 'required',
            'price'     => 'required',
            'qty'       => 'required',
            'type'       => 'required',
            'status'     => 'required'
        ],$messages);

        $params       = $request->all();
        $params['product_id'] = $id;
        $result       = Products::addUpdatePlan($params);

        if($result) {
            return redirect()->route('product.index')->withSuccess('Request Resolved successfully.');
        }

        return redirect('product')->withErrors(__('Something went wrong!'));
    }

    public function delete($id)
    {
		$hobbies = Products::where('product_id', $id)->delete();
        return redirect()->route('product.index')->withSuccess('Product deleted.');
    }

    public function getSubHobbies(Request $request)
    {
        $params      = $request->all();
        $subHobbies  = SubHobbies::where('hobbie_id', $params['id'])->get();

        if($subHobbies) {
            $subHobbies = $subHobbies->toArray();
            return json_encode($subHobbies);
        }
        return [];
    }
}