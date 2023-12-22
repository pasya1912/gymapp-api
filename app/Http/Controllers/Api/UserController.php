<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function me(Request $request)
    {

        return GeneralResource::formatResponse([
            'status' => 200,
            'message' => 'Success',
            'data' => [
                'user' => new UserResource($request->user()),
            ],
        ]);
    }
    public function update(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'string|max:255',
                'alamat' => 'string|max:255'
            ]);
        } catch (\Exception $e) {
            return GeneralResource::formatResponse([
                'status' => 400,
                'message' => $e->getMessage(),
            ]);
        }
        $data = [];
        if ($request->nama) {
            $data['nama'] = $request->nama;
        }
        if ($request->alamat) {
            $data['alamat'] = $request->alamat;
        }
        if($request->old_password && $request->new_password){
            try{
            $request->validate([
                'old_password' => 'required|string',
                'new_password' => 'required|string',
            ]);
            }catch(\Exception $e){
                return GeneralResource::formatResponse([
                    'status' => 400,
                    'message' => $e->getMessage(),
                ]);
            }
            //get user password
            $user = $request->user();
            //check if password is same with old password
            if(!Hash::check($request->old_password, $user->password)){
                return GeneralResource::formatResponse([
                    'status' => 400,
                    'message' => 'Wrong password',
                ]);
            }
            //check if new password is same with old password
            if(Hash::check($request->new_password, $user->password)){
                return GeneralResource::formatResponse([
                    'status' => 400,
                    'message' => 'New password cannot be same with old password',
                ]);
            }
            //update password
            $data['password'] = Hash::make($request->new_password);
            //delete all token except current token
            $user->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();
            
        }
        
        if(!$data){
            return GeneralResource::formatResponse([
                'status' => 400,
                'message' => 'No data to update',
            ]);
        }

        $user = $request->user();
        if (!$user->update($data)) {
            return GeneralResource::formatResponse([
                'status' => 500,
                'message' => 'Failed',
            ]);
        }
        return GeneralResource::formatResponse([
            'status' => 200,
            'message' => 'Success',
            'data' => [
                'user' => new UserResource($user),
            ],
        ]);
    }

}
