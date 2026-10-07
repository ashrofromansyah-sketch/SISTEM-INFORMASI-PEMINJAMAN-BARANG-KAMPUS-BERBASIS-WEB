<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public const ROLES = ['admin', 'petugas', 'peminjam'];

    public function index(Request $request): View
    {
        $search = $request->input('q');
        $role = $request->input('role');

        $users = User::query()
            ->when($search, function ($query, $value) {
                $query->where(function ($w) use ($value) {
                    $w->where('name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhere('identity_number', 'like', "%{$value}%");
                });
            })
            ->when($role, fn ($query, $value) => $query->where('role', $value))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', ['users' => $users, 'roles' => self::ROLES]);
    }

    public function create(): View
    {
        return view('admin.users.create', ['user' => new User(['is_active' => true, 'role' => 'peminjam']), 'roles' => self::ROLES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'identity_number' => ['nullable', 'string', 'max:50', 'unique:users,identity_number'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(self::ROLES)],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = new User($data);
        $user->is_active = $request->boolean('is_active', true);
        // Akun dibuat admin, jadi tidak perlu verifikasi email.
        $user->email_verified_at = now();
        $user->save();

        AuditLog::record('pengguna', "Menambah akun {$user->email} ({$user->role}).");

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', ['user' => $user, 'roles' => self::ROLES]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'identity_number' => ['nullable', 'string', 'max:50', Rule::unique('users', 'identity_number')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(self::ROLES)],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $aktif = $request->boolean('is_active');

        if ($user->id === auth()->id() && ($data['role'] !== 'admin' || ! $aktif)) {
            throw ValidationException::withMessages([
                'role' => 'Anda tidak dapat menurunkan peran atau menonaktifkan akun Anda sendiri.',
            ]);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->fill($data);
        $user->is_active = $aktif;
        $user->save();

        AuditLog::record('pengguna', "Mengubah akun {$user->email} ({$user->role}).");

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function toggle(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        AuditLog::record('pengguna', ($user->is_active ? 'Mengaktifkan' : 'Menonaktifkan')." akun {$user->email}.");

        return back()->with('success', $user->is_active ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.');
    }
}
