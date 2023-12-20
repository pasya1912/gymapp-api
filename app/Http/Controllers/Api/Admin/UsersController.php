<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;


use App\Models\User;


class UsersController extends Controller
{
    public function get(Request $request)
    {
        //check if there is limit and page
        $limit = $request->limit ?? 10;
        $page = $request->page ?? 1;
        $offset = ($page - 1) * $limit;
        //get all users
        $users = User::offset($offset)->limit($limit)->where('role', 'user')->get();
        if ($users->count() <= 0) {
            return GeneralResource::formatResponse([
                'status' => 400,
                'message' => 'No users found',
            ]);
        } else {
            return GeneralResource::formatResponse([
                'status' => 200,
                'message' => 'Success',
                'data' => [
                    'users' => UserResource::collection($users),
                ],
            ]);
        }
    }
    public function update(Request $request, $id)
    {


        if ($request->add_membership) {

            try {
                $user = User::find($id);
                if (!$user) {
                    return GeneralResource::formatResponse([
                        'status' => 400,
                        'message' => 'User not found',
                    ]);
                }


                $user->membership = date('Y-m-d H:i:s', strtotime('+' . $request->add_membership . ' days'));

                if ($user->save()) {
                    return GeneralResource::formatResponse([
                        'status' => 200,
                        'message' => 'Success',
                        'data' => [
                            'user' => new UserResource($user),
                        ],
                    ]);
                }
            } catch (\Exception $e) {
                return GeneralResource::formatResponse([
                    'status' => 400,
                    'message' => $e->getMessage(),
                ]);
            }
        }
        //if the code pass here then return error

        return GeneralResource::formatResponse([
            'status' => 400,
            'message' => 'Something wrong',
        ]);
    }
}
