<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Requests;
use App\Http\Controllers\Controller;
//use App\Http\Repository\UserManagementRepository;
use Illuminate\Support\Facades\Http;
use Validator;
use DB;


class PopupController extends Controller
{
    public function pendingPopupList(Request $request)
    {
       // $viewed_popups =  $request->user()->viewed_popups;
       $viwewd_popups =  DB::table('viewed_popups')
       ->where('user_id','=',$request->user()->id)
       ->count();
                         
        
        if($viwewd_popups == 0)
        {
           
            //$data = \DB::select("SELECT p.*,p.name AS popup_name,t2.*,t1.*,t1.screen_id, GROUP_CONCAT(t2.name)AS screens_list FROM popup_screens t1 JOIN screens t2 ON FIND_IN_SET(t2.id, t1.screen_id) JOIN popup p ON p.id = t1.popup_id GROUP BY t1.screen_id");
            
            $list=array();

                    $popup_array=DB::table('popup')
                                ->get();  
                   
                  if(isset($popup_array[0]))
                  {
                       foreach($popup_array AS $data)
                       {
                                  
                                  /*$screen_data =  DB::table('popup_screens')
                                ->leftjoin('popup','popup.id','=','popup_screens.popup_id')
                                ->leftjoin('screens','screens.id','=','popup_screens.screen_id')
                                ->get(); */
                                
                                  
                                  $screen_data =  DB::table('popup_screens')
                                ->leftjoin('popup','popup.id','=','popup_screens.popup_id')
                                ->leftjoin('screens','screens.id','=','popup_screens.screen_id')
                                ->where('popup_screens.popup_id',$data->id)
                                ->get();    


                                      
                                       $data->screen_data = $screen_data;
                                    
                                       $list[] = $data;                                  
                       }
                  }
            
             return $this->successResponse($list, 'Success');
        }
        else
        {
           
                    $list=array();

                    $popup_array=DB::table('popup')
                                ->get();  
                   
                  if(isset($popup_array[0]))
                  {
                       foreach($popup_array AS $data)
                       {
                                  
                                  /*$screen_data =  DB::table('popup_screens')
                                ->leftjoin('popup','popup.id','=','popup_screens.popup_id')
                                ->leftjoin('screens','screens.id','=','popup_screens.screen_id')
                                ->leftjoin('viewed_popups','viewed_popups.popup_screen_id','=','popup_screens.screen_id')
                                ->where('viewed_popups.popup_screen_id','=',NULL)
                                ->get();  */
                                
                                 $screen_data =  DB::table('popup_screens')
                                ->leftjoin('popup','popup.id','=','popup_screens.popup_id')
                                ->leftjoin('screens','screens.id','=','popup_screens.screen_id')
                                 ->leftjoin('viewed_popups','viewed_popups.popup_screen_id','=','popup_screens.screen_id')
                                ->where('viewed_popups.popup_screen_id','=',NULL)
                                ->where('popup_screens.popup_id',$data->id)
                                ->get();    

                                      
                                       $data->screen_data = $screen_data;
                                    
                                       $list[] = $data;                                  
                       }
                  }
            
             return $this->successResponse($list, 'Success');
           
           
        }
        
    }
    
    
    public function viewPopup(Request $request)
    {
        $messages = array(
            'screen_id.required'     => 'Screen id field is required.',
        );
        $validator = Validator::make($request->all(),[
            'screen_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }
        
         DB::table('viewed_popups')->insert([
         'user_id' => $request->user()->id, 
         'popup_screen_id' => $request->screen_id
         ]);
         
         
           return $this->successResponse('', 'Success!');
      
    }
    
    
    
    
    
    
    
    
    
    
}