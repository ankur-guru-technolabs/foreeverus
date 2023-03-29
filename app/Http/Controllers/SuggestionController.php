<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Passion;
use App\Models\Suggestion;


class SuggestionController extends Controller
{
	public function index()
	{
		$suggestion = Suggestion::all();
        return view('suggestion.index', ['suggestion' => $suggestion]);
	}

	public function create()
    {
        return view('hobbies.create', []);
    }

	public  function edit($id)
    {
    	$suggestion = Suggestion::find($id);
        return view('suggestion.edit', ['suggestion' => $suggestion]);
    }

	/*public function store(Request $request)
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
	}*/

	public function update($id, Request $request)
    {
        /*$messages = array(
            'hobbies.required'   => 'Kids status field is required.',
        );

        $request->validate([
            'hobbies' => 'required',
        ],$messages);*/

        $params       = $request->all();
        $params['contact_id'] = $id;

        $result       = ContactSupport::addUpdateContactSupport($params);

        if($result) {
            return redirect()->route('supports.index')->withSuccess('Request Resolved successfully.');
        }

        return redirect('supports')->withErrors(__('Something went wrong!'));
    }

    public function delete($id)
    {
		$hobbies = ContactSupport::where('contact_id', $id)->delete();
        return redirect()->route('supports.index')->withSuccess('Contact & Support Request deleted.');
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