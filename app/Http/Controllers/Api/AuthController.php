<?php


namespace App\Http\Controllers\Api;

use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\GeneralResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'nomor' => ['required', 'string', 'max:13', 'regex:/^08[0-9]{9,}$/', Rule::exists(User::class)],
            'password' => ['required'],
        ]);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()->first(),
            ];

            return GeneralResource::formatResponse($response);
        }
        $user = User::where('nomor', $request->nomor)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            $response = [
                'status' => 400,
                'message' => 'Wrong credentials',
            ];

            return GeneralResource::formatResponse($response);
        }
        //delete all token with tokenable_id = $user->id
        //$user->tokens()->delete();
        if($user->role == 'admin'){
            $scopes = ['admin'];
        }else if($user->role == 'user'){
            $scopes = ['user'];
        }

        $token = $user->createToken('myAppToken', $scopes);

        return GeneralResource::formatResponse([
            'status' => 200,
            'message' => 'Login success',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token->plainTextToken,
            ],
        ]);
    }
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:255'],
            //nomor is unique from User start with 08
            'nomor' => ['required', 'string', 'max:13', 'unique:users', 'regex:/^08[0-9]{9,}$/', Rule::unique(User::class)],

            'alamat' => ['required', 'string', 'max:255'],
            'password' => ['required', 'min:8'],
        ]);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()->first(), 
            ];

            return GeneralResource::formatResponse($response);
        }

        $user = User::create([
            'nama' => $request->nama,
            'nomor' => $request->nomor,
            'alamat' => $request->alamat,
            'password' => Hash::make($request->password),
        ]);
        if(!$user){
            $response = [
                'status' => 400,
                'message' => 'Failed to create user',
            ];

            return GeneralResource::formatResponse($response);
        }

        return GeneralResource::formatResponse([
            'status' => 200,
            'message' => 'User created successfully',
        ]);
    }
    public function logout(Request $request){
        try{
        $request->user()->currentAccessToken()->delete();
        }catch(\Exception $e){
            return GeneralResource::formatResponse([
                'status' => 400,
                'message' => 'Failed to logout',
            ]);
        }

        return GeneralResource::formatResponse([
            'status' => 200,
            'message' => 'Logout success',
        ]);
    }
}
