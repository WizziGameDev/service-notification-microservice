<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationMemberLog;
use App\Models\NotificationMitraLog;

class NotificationLogController extends Controller
{
    public function logsMember()
    {
        $notifications = NotificationMemberLog::latest()->get(); // Urut terbaru
        return response()->json([
            'status' => 200,
            'message' => 'Notification logs member retrieved successfully',
            'data' => $notifications,
            'errors' => null,
        ]);
    }

    public function logsMitra()
    {
        $notifications = NotificationMitraLog::latest()->get(); // Urut terbaru
        return response()->json([
            'status' => 200,
            'message' => 'Notification logs mitra retrieved successfully',
            'data' => $notifications,
            'errors' => null,
        ]);
    }
}
