<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Route middleware handles admin-only access for CRUD operations
        // profile and updateProfile methods are publicly available to all auth users
    }

    /**
     * Display a listing of users (Admin only).
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user (Admin only).
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user (Admin only).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', Rule::in(['admin', 'warehouse_staff', 'qc_staff'])],
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'name' => $validated['nama_lengkap'], // Also set name field
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => strtolower($validated['role']), // Ensure lowercase for consistency
        ]);
        
        // Ensure user_id is generated if not set
        if (empty($user->user_id)) {
            $latestId = User::max('id');
            $nextId = $latestId ? $latestId + 1 : 1;
            $user->user_id = 'USR' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            $user->update(['user_id' => $user->user_id]);
        }

        return redirect()->route('users.index')->with('success', 'Akun berhasil dibuat.');
    }

    /**
     * Display the specified user.
     */
    public function show($user_id)
    {
        $user = User::where('user_id', $user_id)->orWhere('id', $user_id)->firstOrFail();
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user (Admin only).
     */
    public function edit($user_id)
    {
        $user = User::where('user_id', $user_id)->orWhere('id', $user_id)->firstOrFail();
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user (Admin only).
     */
    public function update(Request $request, $user_id)
    {
        $user = User::where('user_id', $user_id)->orWhere('id', $user_id)->firstOrFail();
        
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => ['required', Rule::in(['admin', 'warehouse_staff', 'qc_staff'])],
        ]);

        $user->username = $validated['username'];
        $user->nama_lengkap = $validated['nama_lengkap'];
        $user->name = $validated['nama_lengkap'];
        $user->email = $validated['email'];
        $user->role = strtolower($validated['role']); // Ensure lowercase for consistency

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if (method_exists($user, 'save')) {
            $user->save();
        }

        return redirect()->route('users.index')->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Remove the specified user (Admin only).
     */
    public function destroy($user_id)
    {
        $user = User::where('user_id', $user_id)->orWhere('id', $user_id)->firstOrFail();
        
        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Akun berhasil dihapus.');
    }

    /**
     * Show user profile (for logged-in user).
     */
    public function profile()
    {
        $user = Auth::user();
        return view('users.profile', compact('user'));
    }

    /**
     * Update user profile (for logged-in user).
     */
    public function updateProfile(Request $request)
    {
        $user = \App\Models\User::find(Auth::id());

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $updateData = [
            'nama_lengkap' => $validated['nama_lengkap'],
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'],
        ];
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }
        $user->update($updateData);
        return redirect()->route('users.profile')->with('success', 'Profil berhasil diperbarui.');
    }
}

