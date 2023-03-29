<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Passion;
use App\Models\Height;

class HeightController extends Controller
{
	public function index()
	{
		$height = Height::all();
        return view('height.index', ['height' => $height]);
	}

	public function create()
    {
        return view('height.create', []);
    }

	public  function edit($id)
    {
    	$height = Height::find($id);
        return view('height.edit', ['height' => $height]);
    }

	public function store(Request $request)
	{
		$messages = array(
            'title.required'   => 'Height field is required.',
        );

        $request->validate([
            'title' => 'required',
        ],$messages);

        $params            = $request->all();
        $result            = Height::addUpdateHeight($params);
        if($result) {
            return redirect()->route('height.index')->withSuccess('height successfully Added.');
        }

        return redirect('height')->withErrors(__('Something went wrong!'));
	}

	public function update($id, Request $request)
    {
    	$messages = array(
            'title.required'    => 'height field is required.',
        );

        $request->validate([
            'title' => 'required',
        ],$messages);

        $params       = $request->all();
        $params['id'] = $id;
        $result       = Height::addUpdateHeight($params);

        if($result) {
            return redirect()->route('height.index')->withSuccess('Height successfully updated.');
        }

        return redirect('height')->withErrors(__('Something went wrong!'));
    }

    public function deleteHeight($id)
    {
		$height = Height::where('id', $id)->delete();
        return redirect()->route('height.index')->withSuccess('Height successfully deleted.');
    }
}