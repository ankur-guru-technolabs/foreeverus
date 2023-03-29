<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Passion;
use App\Models\Smoking;

class SmokingController extends Controller
{
	public function index()
	{
		$smoking = Smoking::all();
        return view('smoking.index', ['smoking' => $smoking]);
	}

	public function create()
    {
        return view('smoking.create', []);
    }

	public  function edit($id)
    {
    	$smoking = Smoking::find($id);
        return view('smoking.edit', ['smoking' => $smoking]);
    }

	public function store(Request $request)
	{
		$messages = array(
            'title.required'   => 'Smoking title field is required.',
        );

        $request->validate([
            'title' => 'required',
        ],$messages);

        $params            = $request->all();
        $result            = Smoking::addUpdateSmokingStatus($params);
        if($result) {
            return redirect()->route('smoking.index')->withSuccess('Smoking title successfully Added.');
        }

        return redirect('smoking')->withErrors(__('Something went wrong!'));
	}

	public function update($id, Request $request)
    {
        $messages = array(
            'title.required'   => 'Smoking title field is required.',
        );

        $request->validate([
            'title' => 'required',
        ],$messages);

        $params       = $request->all();
        $params['id'] = $id;
        $result       = Smoking::addUpdateSmokingStatus($params);

        if($result) {
            return redirect()->route('smoking.index')->withSuccess('Smoking title successfully updated.');
        }

        return redirect('smoking')->withErrors(__('Something went wrong!'));
    }

    public function deleteSmoking($id)
    {
		$Smoking = Smoking::where('id', $id)->delete();
        return redirect()->route('smoking.index')->withSuccess('Smoking title successfully deleted.');
    }
}