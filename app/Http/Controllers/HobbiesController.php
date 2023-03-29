<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Passion;
use App\Models\Hobbies;
use App\Models\SubHobbies;

class HobbiesController extends Controller
{
	public function index()
	{
		$hobbies = Hobbies::all();
        return view('hobbies.index', ['hobbies' => $hobbies]);
	}

	public function create()
    {
        return view('hobbies.create', []);
    }

	public  function edit($id)
    {
    	$hobbies = Hobbies::find($id);
        return view('hobbies.edit', ['hobbies' => $hobbies]);
    }

	public function store(Request $request)
	{
		$messages = array(
            'hobbies.required'   => 'Hobbies field is required.',
        );

        $request->validate([
            'hobbies' => 'required',
        ],$messages);

        $params            = $request->all();
        $result            = Hobbies::addUpdateHobbies($params);
        if($result) {
            return redirect()->route('hobbies.index')->withSuccess('Hobbies successfully Added.');
        }

        return redirect('hobbies')->withErrors(__('Something went wrong!'));
	}

	public function update($id, Request $request)
    {
        $messages = array(
            'hobbies.required'   => 'Kids status field is required.',
        );

        $request->validate([
            'hobbies' => 'required',
        ],$messages);

        $params       = $request->all();
        $params['id'] = $id;
        $result       = Hobbies::addUpdateHobbies($params);

        if($result) {
            return redirect()->route('hobbies.index')->withSuccess('Hobbies successfully updated.');
        }

        return redirect('hobbies')->withErrors(__('Something went wrong!'));
    }

    public function deleteHobbies($id)
    {
		$hobbies = Hobbies::where('id', $id)->delete();
        return redirect()->route('hobbies.index')->withSuccess('Hobbies successfully deleted.');
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