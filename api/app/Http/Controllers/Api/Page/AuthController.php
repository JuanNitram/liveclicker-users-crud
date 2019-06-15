<?php

namespace App\Http\Controllers\Api\Page;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Page\Base\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

use App\User;

class AuthController extends BaseController
{
    /**
    * Register api
    *
    * @return \Illuminate\Http\Response
    */
    public function register(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 200);
        }

        $user = User::where('email', $data['email'])->first();
        
        if(!$user){
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);
            
            $this->store_media($user, isset($data['profile_image']) ? $data['profile_image'] : null, $user->name, $user->surname);

            $success['user'] = $user;
            $success['token'] =  $user->createToken('PageToken')->accessToken;

            return $this->sendResponse($success, 'User register successfully.');
        }

        return $this->sendError('User already exists.', [], 200);
    }

    public function update(Request $request){
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 200);
        }
            
        $data = [];
        foreach($request->all() as $key => $value){
            if($key != 'email' && $key != 'active')
                $data[$key] = $value;
        }

        $user = User::where('email', $request->email)->where('active', 1)->first();

        if($user){
            if($user && Hash::check($data['password'], $user->password)){
                $data['password'] = Hash::make($data['password']);
                $user->update($data);
                $user->fresh();
                
                foreach($user->media as $media) $media->delete();
                $this->store_media($user, isset($data['profile_image']) ? $data['profile_image'] : null, $user->name, $user->surname);
    
                $success['user'] = $user->fresh();
    
                return $this->sendResponse($success, "Updated succesfully.");
            } else {
                return $this->sendError('Incorret User password.', [], 200);
            }
        }
        return $this->sendError('User not found.', [], 200);
    }

    public function login(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 200);
        }

        if($data['email'] && $data['password']){
            
            $user = User::where('email', $data['email'])->where('active', 1)->first();

            if($user && Hash::check($data['password'], $user->password)){
                $success['token'] = 'Bearer ' . $user->createToken('PageToken')->accessToken;
                $success['user'] =  $user;

                return $this->sendResponse($success, 'User logged successfully.');
            }

            return $this->sendError('Error, wrong password or email.', [], 200);
        }
        return $this->sendError('Error, check the parameters.', [], 200);
    }

}
