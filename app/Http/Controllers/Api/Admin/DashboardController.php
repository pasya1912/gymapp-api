<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralResource;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function get()
    {
        $date = date('Y-m-d');
        $data = DB::table('latihan')
            ->select(
                DB::raw('COUNT(CASE WHEN latihan.isCheckin = "1" AND DATE(dateTime) = "' . $date . '" THEN 1 END) as today_checkin'),
                DB::raw('(SELECT COUNT(CASE WHEN users.membership > now() THEN 1 END) from users) as active_member')
            )
            ->first();




        return GeneralResource::formatResponse([
            'status' => 200,
            'message' => 'Success',
            'data' => $data,
        ]);
    }
}
