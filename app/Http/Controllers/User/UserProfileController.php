<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
//this controller is for the customer can edit they profiles
class UserProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('admin.users.profileuser', compact('user'));

    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id_user . ',id_user',
            'phone' => 'nullable|string|max:20',
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/[0-9]/',
                'regex:/[.:!@#$%^&*()_+\-]/',
                'confirmed',
            ],
        ]);

        $user->full_name = $request->full_name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        
        return redirect()->route('home')->with('success', 'Your profile has been updated successfully!');
    }

}
