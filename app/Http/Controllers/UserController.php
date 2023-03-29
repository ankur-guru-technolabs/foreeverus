<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersReport;

class UserController extends Controller
{
    public function usersList()
    {
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "User"], ['name' => "Users List"]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        return view('pages.page-users-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs]);
    }
    public function usersView()
    {
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "User"], ['name' => "Users View"]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];

        return view('pages.page-users-view', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs]);
    }
    public function usersEdit()
    {
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "User"], ['name' => "Users Edit"]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true, 'isFabButton' => true];
        return view('pages.page-users-edit', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs]);
    }

    public function index()
    {
        $user = User::where('email_verified','1')
              ->where('first_name','!=',NULL) 
              ->where('users.email', '!=', '')
            ->where('users.phone', '!=', '')->get();
        return view('users.index', ['users' => $user]); 
    }


    public function create()
    {
        return view('users.create', []); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'           => 'required|email|unique:users,email',
            'phone'           => 'required|numeric|unique:users,phone',
            'first_name'      => 'required',
            'last_name'       => 'required',
            'status'          => 'required',
            'gender'          => 'required',
        ]);

        $params = $request->all();
        $result = User::addUpdateUser($params);

        if($result) {
            return redirect()->route('user.index')->withSuccess('User successfully Added.');
        }

        return redirect('user')->withErrors(__('Something went wrong!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        return view('users.edit', ['user' => $user]);
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'email'           => 'required|email|unique:users,email,'.$id,
            'phone'           => 'required|numeric|unique:users,phone,'.$id,
            'first_name'      => 'required',
            'last_name'       => 'required',
            'status'          => 'required',
            'gender'          => 'required',
        ]);

        $params       = $request->all();
        $params['id'] = $id;
        $result       = User::addUpdateUser($params);

        if($result) {
            return redirect()->route('user.index')->withSuccess('User successfully Updated.');
        }

        return redirect('user')->withErrors(__('Something went wrong!'));
    }

    public function userReports(Request $request)
    {
        $usersReport = UsersReport::all();
        foreach ($usersReport as $key => $value) {
            $userId       = User::find($value->user_id);
            $reportUserId = User::find($value->reporter_id);
            $value->username = isset($userId->first_name) ? $userId->first_name : '';
            $value->report_by = isset($reportUserId->first_name) ? $reportUserId->first_name : '';
            # code...
        }
        return view('report.index', ['report' => $usersReport]); 
        //echo "<pre>";print_r($usersReport);exit;
    }

    public function userBlock(Request $request, $id)
    {
        $userReport = UsersReport::find($id);
        $reportId   = isset($userReport->reporter_id) ? $userReport->reporter_id : '';

        $userReport->action = 1;
        $userReport->save();

        $user         = User::find($reportId);
        $user->status = 'deactivate';

        return redirect('reports')->withSuccess('User blocked successfully.');
    }

    public function userActive(Request $request, $id)
    {
        $userReport = UsersReport::find($id);
        $reportId   = isset($userReport->reporter_id) ? $userReport->reporter_id : '';

        $userReport->action = 0;
        $userReport->save();

        $user         = User::find($reportId);
        $user->status = 'active';

        return redirect('reports')->withSuccess('User activated successfully.');
    }

}
