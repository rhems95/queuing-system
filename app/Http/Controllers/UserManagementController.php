<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Window;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('window')->orderBy('id')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $windows = Window::orderBy('window_name')->get();

        return view('admin.users.form', [
            'user'    => new User(),
            'windows' => $windows,
            'mode'    => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'max:100', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:4'],
            'role'      => ['required', 'in:admin,staff'],
            'window_id' => ['nullable', 'integer', 'exists:windows,id'],
        ]);

        if ($data['role'] === 'staff' && empty($data['window_id'])) {
            return back()
                ->withErrors(['window_id' => 'Window is required for staff accounts.'])
                ->withInput();
        }

        if ($data['role'] !== 'staff') {
            $data['window_id'] = null;
        }

        if (! empty($data['window_id'])) {
            $existingStaff = User::where('window_id', $data['window_id'])->exists();
            if ($existingStaff) {
                return back()
                    ->withErrors(['window_id' => 'This window already has a staff account assigned.'])
                    ->withInput();
            }
        }

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index')->with('status', 'User created.');
    }

    public function edit(User $user)
    {
        $windows = Window::orderBy('window_name')->get();

        return view('admin.users.form', [
            'user'    => $user,
            'windows' => $windows,
            'mode'    => 'edit',
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'password'  => ['nullable', 'string', 'min:4'],
            'role'      => ['required', 'in:admin,staff'],
            'window_id' => ['nullable', 'integer', 'exists:windows,id'],
        ]);

        if ($data['role'] === 'staff' && empty($data['window_id'])) {
            return back()
                ->withErrors(['window_id' => 'Window is required for staff accounts.'])
                ->withInput();
        }

        if ($data['role'] !== 'staff') {
            $data['window_id'] = null;
        }

        if (! empty($data['window_id'])) {
            $existingStaff = User::where('window_id', $data['window_id'])
                ->where('id', '!=', $user->id)
                ->exists();
            if ($existingStaff) {
                return back()
                    ->withErrors(['window_id' => 'This window already has a staff account assigned.'])
                    ->withInput();
            }
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('status', 'User updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'User deleted.');
    }
}

