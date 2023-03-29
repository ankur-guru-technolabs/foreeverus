<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Passion;
use App\Models\kids;

class KidsController extends Controller
{
	public function index()
	{
		$kids = kids::all();
        return view('kids_status.index', ['kids' => $kids]);
	}

	public function create()
    {
        return view('kids_status.create', []);
    }

	public  function edit($id)
    {
    	$kids = kids::find($id);
        return view('kids_status.edit', ['kids' => $kids]);
    }

	public function store(Request $request)
	{
		$messages = array(
            'kids_status.required'   => 'Kids status field is required.',
        );

        $request->validate([
            'kids_status' => 'required',
        ],$messages);

        $params            = $request->all();
        $result            = kids::addUpdatekidsStatus($params);
        if($result) {
            return redirect()->route('kids.index')->withSuccess('Kids stuats successfully Added.');
        }

        return redirect('kids')->withErrors(__('Something went wrong!'));
	}

	public function update($id, Request $request)
    {
        $messages = array(
            'kids_status.required'   => 'Kids status field is required.',
        );

        $request->validate([
            'kids_status' => 'required',
        ],$messages);

        $params       = $request->all();
        $params['id'] = $id;
        $result       = kids::addUpdatekidsStatus($params);

        if($result) {
            return redirect()->route('kids.index')->withSuccess('Kids status successfully updated.');
        }

        return redirect('kids')->withErrors(__('Something went wrong!'));
    }

    public function deleteKids($id)
    {
		$Smoking = kids::where('id', $id)->delete();
        return redirect()->route('kids.index')->withSuccess('kids stauts successfully deleted.');
    }
}