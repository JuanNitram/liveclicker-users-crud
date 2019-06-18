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
            
            $this->store_media($user, isset($request->profile_image) ? $request->file('profile_image') : null, $user->name, $user->surname);

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
                if($key != 'email' && $key != 'active' && $key != 'password' && $key != 'c_password')
                $data[$key] = $value;
            }
            
            $user = User::where('email', $request->email)->where('active', 1)->withMedia(['thumb'])->first();
            
            if($user){
                if($user && Hash::check($request->password, $user->password)){
                    if($request->profile_image) 
                        foreach($user->media as $media) { $media->delete(); }
                    // $user->fresh();
                    $user->update($data);
                    
                    if($request->profile_image) $this->store_media($user, isset($request->profile_image) ? $request->file('profile_image') : null, $user->name, $user->surname);
                    
                    $success['user'] = User::where('email', $request->email)->where('active', 1)->withMedia(['thumb'])->first();
                    
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
            
            $user = User::where('email', $data['email'])->where('active', 1)->withMedia(['thumb'])->first();

            if($user && Hash::check($data['password'], $user->password)){
                $success['token'] = 'Bearer ' . $user->createToken('PageToken')->accessToken;
                $success['user'] =  $user;

                return $this->sendResponse($success, 'User logged successfully.');
            }

            return $this->sendError('Wrong data/Account not active.', [], 200);
        }
        return $this->sendError('Error, check the parameters.', [], 200);
    }

    public function delete($id, Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'password' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 200);
        }

        if($id){
            $user = User::where('id', $id)->first();
            if($user && Hash::check($data['password'], $user->password)){
                foreach($user->media as $media)
                    $media->delete();
                    
                $user->delete();
                return $this->sendResponse([], 'User deleted successfully.');
            }
            return $this->sendError('User not found or wrong password.', [], 200);
        }
        return $this->sendError('No id param!', [], 200);
    }

    public function check(){
        return $this->sendResponse([], 'The token is valid!');
    }
}
