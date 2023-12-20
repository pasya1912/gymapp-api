<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LatihanController extends Controller
{
    public function get(Request $request)
    {
        //get all latihan
        $latihan = DB::table('latihan')
            ->select(
                'latihan.id',
                'latihan.dateTime',
                'latihan.isCheckin',
                //get user name from user_id
                DB::raw('(SELECT users.nama from users where users.id = latihan.user_id) as nama')
            );
        if ($request->limit) {
            $latihan = $latihan->limit($request->limit);
        }
        if ($request->page && $request->limit) {
            $offset = ($request->page - 1) * $request->limit;
            $latihan = $latihan->offset($offset);
        }
        try {
            $latihan = $latihan->get();
            if ($latihan->count() <= 0) {
                return GeneralResource::formatResponse([
                    'status' => 400,
                    'message' => 'No latihan found',
                ]);
            }

            return GeneralResource::formatResponse([
                'status' => 200,
                'message' => 'Success',
                'data' => [
                    'latihan' => $latihan,
                ],
            ]);
        } catch (\Exception $e) {
            return GeneralResource::formatResponse([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
