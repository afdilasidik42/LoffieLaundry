<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of admin users.
     * Hanya menampilkan pengguna dengan role = 'admin'.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::where('role', 'admin')
            ->when($search, function ($query, $search) {
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('username', 'LIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('owner.users.index', compact('users', 'search'));
    }

    /**
     * Show the form for creating a new admin user.
     */
    public function create()
    {
        return view('owner.users.create');
    }

    /**
     * Store a newly created admin user in storage.
     * Role dikunci secara hardcoded sebagai 'admin'.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username sudah digunakan.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'password' => $validated['password'], // Auto-hashed via model cast
            'role'     => 'admin', // Hardcoded — Owner tidak bisa buat Owner lain
        ]);

        return redirect()->route('owner.users.index')
                         ->with('success', 'Staf Admin berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified admin user.
     */
    public function edit(User $user)
    {
        // Pastikan hanya bisa edit user dengan role admin
        if ($user->role !== 'admin') {
            abort(403, 'Tidak dapat mengedit pengguna ini.');
        }

        return view('owner.users.edit', compact('user'));
    }

    /**
     * Update the specified admin user in storage.
     * Jika password dikosongkan, pertahankan hash password lama.
     */
    public function update(Request $request, User $user)
    {
        // Pastikan hanya bisa update user dengan role admin
        if ($user->role !== 'admin') {
            abort(403, 'Tidak dapat mengedit pengguna ini.');
        }

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username sudah digunakan.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $data = [
            'name'     => $validated['name'],
            'username' => $validated['username'],
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = $validated['password']; // Auto-hashed via model cast
        }

        $user->update($data);

        return redirect()->route('owner.users.index')
                         ->with('success', 'Data Staf Admin berhasil diperbarui.');
    }

    /**
     * Remove the specified admin user from storage.
     * Guard: Owner tidak bisa menghapus akun sendiri.
     */
    public function destroy($id)
    {
        // Proteksi mutlak: cegah Owner menghapus akun sendiri
        if ($id == auth()->id()) {
            abort(403, 'Tidak dapat menghapus akun sendiri');
        }

        $user = User::findOrFail($id);

        // Pastikan hanya bisa hapus user dengan role admin
        if ($user->role !== 'admin') {
            abort(403, 'Tidak dapat menghapus pengguna ini.');
        }

        $user->delete();

        return redirect()->route('owner.users.index')
                         ->with('success', 'Staf Admin berhasil dihapus.');
    }
}
