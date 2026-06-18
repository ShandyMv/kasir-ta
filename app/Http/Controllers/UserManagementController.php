<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserManagementRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $users = User::when($search, function ($q, $search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        })->latest()->paginate(10);

        return view('user-management.index', compact('users', 'search'));
    }

    public function store(UserManagementRequest $request)
    {
        User::create($request->validated());

        return redirect()->route('user-management')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(UserManagementRequest $request, User $user)
    {
        $data = $request->validated();

        if (!$request->filled('password')) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('user-management')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('user-management')->with('success', 'User berhasil dihapus.');
    }
}
