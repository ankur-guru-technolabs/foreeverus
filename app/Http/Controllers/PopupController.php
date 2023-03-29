<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  DB;
use App\Models\Screens;
use App\Models\PopupScreens;
use App\Models\Popup;


class PopupController extends Controller
{
    /*const SCREENS = [
        'DiscoverFilter'=>'DiscoverFilter',
        'ProfileDetail'=>'ProfileDetail',
        'ProfileMatch'=>'ProfileMatch',
        'DiscoverTab'=>'DiscoverTab',
    ];*/
    
    public function editPopup($id)
    {
         $screens1 =  DB::table('screens')
                                ->select('screens.*','popup_screens.*','screens.id AS sid')
                                ->leftjoin('popup_screens','popup_screens.screen_id','=','screens.id')
                                ->where('popup_screens.screen_id','=',NULL)
                                ->get()->toArray();    
                                
        $screens2=  DB::table('screens')
                                ->select('screens.*','popup_screens.*','screens.id AS sid')
                                ->leftjoin('popup_screens','popup_screens.screen_id','=','screens.id')
                                ->where('popup_screens.popup_id','=',$id)
                                ->get()->toArray();   

        $screens = array_merge($screens2,$screens1);
                                
        $selected_screens =  DB::table('screens')
                                ->select('screens.*','popup_screens.*','screens.id AS sid')
                                ->leftjoin('popup_screens','popup_screens.screen_id','=','screens.id')
                                ->where('popup_screens.popup_id','=',$id)
                                ->pluck('screen_id')->toArray();
                                
                                
        $popup_details = DB::table('popup')
                        ->where('id',$id)->first();

        
            return view('popup.edit',[
                'screens'=>$screens,'popup_details' => $popup_details,'selected_screens' => $selected_screens
            ]);
        
        
    }
    
    
    public function updatePopup(Request $request)
    {
        //print_r($request->all());exit;
      
        
        if($request->icon != NULL)
        {
                $image = $request->file('icon');
                $icon = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/popup_icons');
                $image->move($destinationPath, $icon);
                
                  Popup::where("id",$request->popid)->update(array(
            'name' => $request->name,
             'description'=> $request->description,
             'icon' => 'https://app-backend.foreverusinlove.com/public/popup_icons/'.$icon,
             'txt_color'=> $request->txt_color,
             'bg_color'=>$request->bg_color,
             
             ));
        }
        else
        {
             Popup::where("id",$request->popid)->update(array(
            'name' => $request->name,
             'description'=> $request->description,
             'txt_color'=> $request->txt_color,
             'bg_color'=>$request->bg_color,
             
             ));
        }
        
       
       popupScreens::where('popup_id',$request->popid)/*->where('screen_id','=',$request->screen)*/->delete();
       
       
        foreach ($request->screens as $screen) 
            {
                if(!empty($screen))
                {
                         DB::table('popup_screens')->insertGetId([
                          // 'screen_id' => implode(',',$request->screens),
                            //'popup_id' => $popup
                            'screen_id' => $screen,
                             'popup_id' => $request->popid,
                          ]);
                }
            }        
             

    
        return redirect('/popup_list')->with('success', 'Popup updated successfully.');   
            //end

    }
    
    public function deletePopup($id)
    {
        DB::table('popup')->where('id', $id)->delete();
        
        DB::table('popup_screens')->where('popup_id', $id)->delete();
        
        return redirect('/popup_list')->with('success', 'Popup deleted successfully.');  

    }

    public function addPopup(/*Request $request*/)
    {
       // $screens = Screens::get();
       
       $screens =  DB::table('screens')
                                ->select('screens.*','popup_screens.*','screens.id AS sid')
                                ->leftjoin('popup_screens','popup_screens.screen_id','=','screens.id')
                                /*->leftjoin('screens','screens.id','=','popup_screens.screen_id')*/
                               // ->where('popup_screens.popup_id',$data->id)
                                ->where('popup_screens.screen_id','=',NULL)
                                ->get();    

        
        return view('popup.create',[
            'screens'=>$screens,
        ]);
    }
    
    public function storePopup(Request $request)
    {

         $image = $request->file('icon');
        $icon = time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('/popup_icons');
        $image->move($destinationPath, $icon);
        
        $txt_color = '';
        $bg_color = '';
        
        if($request->txt_color == NULL)
        {
            $txt_color = '#f6eeee';
        }
        else
        {
            $txt_color = $request->txt_color;
        }
        
        if($request->bg_color == NULL)
        {
            $bg_color = '#221b1b';
        }
        else
        {
             $bg_color = $request->bg_color;
        }
        
         $popup = DB::table('popup')->insertGetId([
         'name' => $request->name, 
        'description' => $request->description, 
        //'icon' => $icon, 
        'icon' =>'https://app-backend.foreverusinlove.com/public/popup_icons/'.$icon,
        'txt_color' => $txt_color,
       // 'screens' => implode(',',$request->screens), 
        'bg_color' => $bg_color
      ]);
        
       // print_r($request->screens);exit;
        
            foreach ($request->screens as $screen) 
            {
                if(!empty($screen))
                {
                         DB::table('popup_screens')->insertGetId([
                          // 'screen_id' => implode(',',$request->screens),
                            //'popup_id' => $popup
                            'screen_id' => $screen,
                             'popup_id' => $popup,
                          ]);
                }
            }  

    
        return redirect('/popup_list')->with('success', 'Popup added successfully.');   
        //return redirect()->back()->with('success', 'Popup added successfully.');   
        
    }
    
    public function list()
    {
          $list=array();
          //  $output="";  

                    $popup_array=DB::table('popup')
                                ->get();  
                   
                  if(isset($popup_array[0]))
                  {
                       foreach($popup_array AS $data)
                       {
                                  
                                  $screen_data =  DB::table('popup_screens')
                                ->leftjoin('popup','popup.id','=','popup_screens.popup_id')
                                ->leftjoin('screens','screens.id','=','popup_screens.screen_id')
                                ->where('popup_screens.popup_id',$data->id)
                                ->get();    

                                      
                                       $data->screen_data = $screen_data;
                                    
                                       $list[] = $data;                                  
                       }
                  }
                  
                 // print_r($list);exit;
       //$popup_details =   \DB::select("SELECT p.*,p.name AS  pnm,t2.*,t1.*,t1.screen_id, GROUP_CONCAT(t2.name)AS screens_list FROM popup_screens t1 JOIN screens t2 ON FIND_IN_SET(t2.id, t1.screen_id) JOIN popup p ON p.id = t1.popup_id GROUP BY t1.screen_id");
       
     
        
         return view('popup.list',[
            'list'=>$list,
        ]);
    }

   
}