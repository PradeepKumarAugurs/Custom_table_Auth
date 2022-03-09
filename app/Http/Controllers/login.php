<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use Auth;
use App\Models\Admin;
use Validator;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Hash;




class login extends BaseController
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'        => 'required|email',
            'password'     => 'required',
        ]);

        if($validator->fails()){
            return $this->sendResponse($this->validation_error_code,null,$validator->errors());     
        }
        
        $getProfile = Admin::where("email",$request->email)->get(); 
        if(!count($getProfile)){
            Admin::create([
                'email'       => 'admin@gmail.com',
                'password'    => Hash::make('12345678'),
            ]);
        }
        $getProfile = Admin::where("email",$request->email)->get(); 
        if(count($getProfile)){
            if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])){ 
                $user = Auth::guard('admin')->user(); 
                $success['token'] =  $user->createToken('MyApp')->accessToken; 
                $success['user_detail'] =  $user;
                $success['pagination'] = null;
                return $this->sendResponse($this->success_code,$success,null);
            }
            else{
                return $this->sendResponse($this->validation_error_code,null,['password'=>['Invalid Password or Account is not active']]);
            }
        }
        else{ 
            return $this->sendResponse($this->validation_error_code,null,['email'=>['Invalid Email']]);
        } 
    }
}
