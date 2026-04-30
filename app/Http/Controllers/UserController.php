<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Auth;
class UserController extends Controller
{

    public function indexuser(Request $request)
    {

        if (Auth::user()->role != 'admin') {
            abort(403);
        }

        $users = User::where('role', '!=', 1)->get();

        if ($request->expectsJson()) {
            return UserResource::collection($users);
        } else {
            return view('admin.users', ['users' => $users]);
        }
    }


    public function destroy(Request $request, User $user)
    {

        if (Auth::user()->role != 1) {
            abort(403);
        }

        $user->delete();

        if ($request->expectsJson()) {
            return response('', 204);
        } else {
            return redirect()->route('dashboarde')->with('message', 'تم حذف المستخدم بنجاح!');
        }
    }
}
