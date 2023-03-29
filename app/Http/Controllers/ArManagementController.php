<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Passion;
use App\Models\ArImages;

class ArManagementController extends Controller
{
	public function index()
    {
        $arImages = ArImages::all();
        return view('ar_images.index', ['arImages' => $arImages]); 
    }

    public function create()
    {
        return view('ar_images.create', []);
    }

    public function store(Request $request)
    {
		$messages = array(
            'ar_name.required'   => 'Ar name field is required.',
            'ar_image.required'  => 'Ar image field is required.',
            'coin.required'      => 'Coin field is required.',
            'coin.integer'       => 'Coin should be integer value',
        );

        $request->validate([
            'ar_name'  => 'required',
            'ar_image' => 'required',
            'coin'     => 'required|integer',
        ],$messages);

        $params            = $request->all();
        $imageName         = time().'.'.$request->ar_image->extension();
        $request->ar_image->move(public_path('ar_images'), $imageName);
        $params['ar_file'] = $imageName;
        $result            = ArImages::addUpdateArImages($params);
        if($result) {
            return redirect()->route('ar_management.index')->withSuccess('Ar successfully Added.');
        }

        return redirect('ar_management')->withErrors(__('Something went wrong!'));
    }

    public function deleteAr($id)
    {
    	ArImages::where('id', $id)->delete();
        return redirect()->route('ar_management.index')->withSuccess('Ar successfully deleted.');
    }
}