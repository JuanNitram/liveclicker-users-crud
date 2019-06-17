<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Admin\Base\BaseController as BaseController;
use Illuminate\Support\Facades\Hash;
use Validator;

use App\User;

class UsersController extends BaseController
{
    /**
    * Register api
    *
    * @return \Illuminate\Http\Response
    */

    public function users(Request $request){
        if($request->email){
            $user = User::where('email', $request->email)->withMedia(['thumb'])->first();
            if($user){
                $success['user'] = $user;
                return $this->sendResponse($success, 'User');
            }
            return $this->sendError('No registered user.', [], 200);
        } else {
            $users = User::withMedia(['thumb'])->get();
            if(count($users) > 0){
                $success['users'] = $users;
                return $this->sendResponse($success, 'Users');
            }
            return $this->sendError('No registered users.', [], 200);
        }
    }

    public function delete($id){
        if($id){
            $user = User::where('id', $id)->first();
            if($user){
                foreach($user->media as $media)
                    $media->delete();
                    
                $user->delete();
                return $this->sendResponse([], 'User deleted successfully.');
            }
            return $this->sendError('User not found.', [], 200);
        }
        return $this->sendError('No id param!', [], 200);
    }

    public function toggle($id, Request $request){
        if($id){
            $validator = Validator::make($request->all(), [
                'toggle' => 'required',
            ]);

            if($validator->fails())
                return $this->sendError('Validation Error.', $validator->errors(), 200);
                
            $user = User::where('id', $request->id)->first();
            if($user){
                $user->update(['active' => $request->toggle]);

                $success['user'] = $user->fresh();
                return $this->sendResponse($success, 'User updated successfully.');
            }
            
            return $this->sendError('User not found!', [], 200);
        }
        return $this->sendError('No id param!', [], 200);
    }

    public function save(Request $request){
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

            return $this->sendResponse($success, 'User register successfully.');
        }
        return $this->sendError('User already exists.', [], 200);
    }

    public function update($id, Request $request){
        $user = User::where('id', $id)->first();
        if($user){
            $data = [];
            foreach($request->all() as $key => $value){
                if($key != 'email')
                    $data[$key] = $value;
            }

            if(isset($data['password'])){
                $data['password'] = Hash::make($data['password']);
            }
            
            $user->update($data);
            $user->fresh();
            
            foreach($user->media as $media) $media->delete();
            $this->store_media($user, isset($data['profile_image']) ? $data['profile_image'] : null, $user->name, $user->surname);

            $success['user'] = $user->fresh();

            return $this->sendResponse($success, "Updated succesfully.");
        }
        return $this->sendError('User not found.', [], 200);
    }

}
