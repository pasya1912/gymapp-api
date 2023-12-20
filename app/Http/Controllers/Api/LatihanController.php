<?php

namespace App\Http\Controllers\Api;

use App\Models\Latihan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LatihanController extends Controller
{
    public function add(Request $request)
    {
        //check if membership is more than today
        $membership = $request->user()->membership;
        $today = date('Y-m-d H:i:s');
        if($membership < $today){
            return response()->json([
                'status' => 403,
                'message' => 'Your membership is expired',
            ]);
        }

        $latihan = Latihan::where('user_id', $request->user()->id)->orderBy('id', 'desc')->first();
        $dateTime = date('Y-m-d H:i:s');
        //if latihan is not exist or last latihan is 1 (checkout) then create new latihan 0 (checkin)
        if (!$latihan || $latihan->isCheckin == '0') {
            $isCheckin = '1';


        }else if($latihan->isCheckin == '1'){
            //if last latihan is 0 (checkin) then update last latihan to 1 (checkout)
            $isCheckin = '0';
        }
        $newLatihan = Latihan::create([
            'isCheckin' => $isCheckin,
            'dateTime' => $dateTime,
            'user_id' => $request->user()->id,
        ]);
        if(!$newLatihan){
            return response()->json([
                'status' => 500,
                'message' => 'Failed',
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => [
                'latihan' => $newLatihan,
            ],
        ]);
        
    }

    public function get(Request $request)
    {
        //validate if limit exist then it must number using laravel validation
        $request->validate([
            'limit' => 'numeric',
            'page' => 'numeric',
        ]);
        //get latihan 5 newest data owned by user
        $latihan = Latihan::where('user_id', $request->user()->id)->orderBy('id', 'desc');
        if($request->limit){
            $latihan = $latihan->limit($request->limit);
        }
        if($request->page && $request->limit){
            $offset = ($request->page - 1) * $request->limit;
            $latihan = $latihan->offset($offset);
        }
        $latihan = $latihan->get();

        if(!$latihan){
            return response()->json([
                'status' => 500,
                'message' => 'Failed',
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => [
                'latihan' => $latihan
            ],
        ]);
    }
}
