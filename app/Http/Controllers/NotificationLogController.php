<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationMemberLog;
use App\Models\NotificationMitraLog;

class NotificationLogController extends Controller
{
    public function logsMember()
    {
        try {
            $notifications = NotificationMemberLog::latest()->get();

            return response()->json([
                'status' => 200,
                'message' => 'Notification logs member retrieved successfully',
                'data' => $notifications,
                'errors' => null,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage(),
                'data' => null,
            ]);
        }
    }

    public function logsMitra()
    {
        try {
            $notifications = NotificationMitraLog::latest()->get();
            return response()->json([
                'status' => 200,
                'message' => 'Notification logs mitra retrieved successfully',
                'data' => $notifications,
                'errors' => null,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage(),
                'data' => null,
            ]);
        }
    }
}
