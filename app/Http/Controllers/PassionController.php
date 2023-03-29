<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Passion;

class PassionController extends Controller
{
	public function index()
    {
        $passions = Passion::all();
        return view('passion.index', ['passions' => $passions]); 
    }

    public function create()
    {
        return view('passion.create', []);
    }

    public function store(Request $request)
    {
		$messages = array(
            'passion.required'    => 'Passion field is required.',
        );

        $request->validate([
            'passion' => 'required',
        ],$messages);

        $params = $request->all();
        $result = Passion::addUpdatePassion($params);

        if($result) {
            return redirect()->route('passion.index')->withSuccess('Passion successfully Added.');
        }

        return redirect('user')->withErrors(__('Something went wrong!'));
    }

    public  function edit($id)
    {
    	$passions = Passion::find($id);
        return view('passion.edit', ['passions' => $passions]);
    }

    public function update($id, Request $request)
    {
    	$messages = array(
            'passion.required'    => 'Passion field is required.',
        );

        $request->validate([
            'passion' => 'required',
        ],$messages);

        $params       = $request->all();
        $params['id'] = $id;
        $result       = Passion::addUpdatePassion($params);

        if($result) {
            return redirect()->route('passion.index')->withSuccess('Passion successfully updated.');
        }

        return redirect('user')->withErrors(__('Something went wrong!'));
    }

    public function deletePassion($id)
    {
    	$Passion = Passion::where('id', $id)->delete();
        return redirect()->route('passion.index')->withSuccess('Passion successfully deleted.');
    }
}