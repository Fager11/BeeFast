<?php

namespace App\Http\Controllers;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
    
        if ($user->role == 'admin') {
            $notifications = $user->notifications()->latest()->paginate(10);
        } elseif ($user->role == 'driver') {
            $notifications = $user->notifications()
                ->where('data->driver_id', $user->id)
                ->latest()
                ->paginate(10);
        } else {
            $notifications = $user->notifications()
                ->where('data->user_id', $user->id)
                ->latest()
                ->paginate(10);
        }
    
        return view('notifications.index', compact('notifications'));
    }
    
    
    

    public function show($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);

      
        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }

    public function markRead(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
    
        return response()->json(['success' => true]);
    }
    
    public function clear()
{
    $user = auth()->user();
    $user->notifications()->delete();

    return response()->json([
        'success' => true,
        'message' => 'تم حذف جميع الإشعارات بنجاح.'
    ]);
}

}
