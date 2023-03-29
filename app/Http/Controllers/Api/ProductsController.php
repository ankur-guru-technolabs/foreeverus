<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Repository\UserManagementRepository;
use Illuminate\Support\Facades\Http;
use Validator;
use DB;

/*use App\Models\Education;
use App\Models\Drink;
use App\Models\LookingFor;
use App\Models\Horoscope;
use App\Models\Religion;
use App\Models\PoliticalLeaning;
use App\Models\RelationshipStatus;
use App\Models\LifeStyle;
use App\Models\Language;
use App\Models\FirstDateIceBreaker;
use App\Models\DietaryLifestyle;
use App\Models\Drugs;
use App\Models\Pets;
use App\Models\CovidVaccine;
use App\Models\Arts;
use App\Models\Interests;
use App\Models\Smoking;*/
use App\Models\Products;
use App\Models\ProductsOrder;

class ProductsController extends Controller
{
    public function getProductList(Request $request)
    {
        $messages = array(
            'product_type.required' => 'Product Type is required.',
        );

        $validator = Validator::make($request->all(),[
            'product_type' => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $productType = $request->get('product_type');
        $getData = Products::where(['type'=>$productType,'status'=>'active'])->get();
        
        return $this->successResponse($getData, 'Product List Get Successfully');
    }

    public function purchaseSuperLike(Request $request) {        
        
        $messages = array(
            'product_id.required' => 'Product Id is required.',
        );

        $validator = Validator::make($request->all(),[
            'product_id' => 'required',
        ],$messages);

        if ($validator->fails()) {
            return $this->errorResponse([], $validator->errors());
        }

        $getData = Products::where(['product_id'=>$request->product_id,'type'=>'super_like','status'=>'active'])->first();

        if ($getData) {
            $user           = $request->user();

            $data = [
                'product_id'=>$getData->product_id,
                'user_id'   => $user->id,
                'payment_status' => 'Paid',
                'payment_type'  => 'Play Store',
                'qty'=>$getData->qty,
            ];
            
            $createOrder = ProductsOrder::create($data);
            if ($createOrder) {
                return $this->successResponse($getData, 'Super Like Purchase Successfully');   
            }
        }

    }

    
}