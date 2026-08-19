<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminProfileController extends Controller
{
    /**
     * Display the admin profile settings page
     */
    public function index()
    {
        $admin = Auth::user();

        return view('admin.profile.index', compact('admin'));
    }

    /**
     * Update admin username
     */
    public function updateUsername(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($admin) {
                if (!Hash::check($value, $admin->password)) {
                    $fail('Password saat ini tidak sesuai.');
                }
            }],
            'username' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_]+$/',
                'unique:pengguna,username,' . $admin->id_pengguna . ',id_pengguna'
            ],
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'username.max' => 'Username maksimal 50 karakter.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, dan underscore.',
            'username.unique' => 'Username sudah digunakan.',
            'current_password.required' => 'Password saat ini harus diisi.',
        ]);

        try {
            $admin->update([
                'username' => $request->username
            ]);

            return redirect()->route('admin.profile.index')
                ->with('success', 'Username berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('admin.profile.index')
                ->with('error', 'Gagal memperbarui username: ' . $e->getMessage());
        }
    }

    /**
     * Update admin password
     */
    public function updatePassword(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($admin) {
                if (!Hash::check($value, $admin->password)) {
                    $fail('Password saat ini tidak sesuai.');
                }
            }],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
            ],
        ], [
            'current_password.required' => 'Password saat ini harus diisi.',
            'new_password.required' => 'Password baru harus diisi.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        try {
            $admin->update([
                'password' => Hash::make($request->new_password)
            ]);

            return redirect()->route('admin.profile.index')
                ->with('success', 'Password berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('admin.profile.index')
                ->with('error', 'Gagal memperbarui password: ' . $e->getMessage());
        }
    }

    /**
     * Update admin profile information (nama, email, no_hp)
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:150',
            'email' => 'nullable|email|unique:pengguna,email,' . $admin->id_pengguna . ',id_pengguna',
            'no_hp' => 'nullable|string|max:15|regex:/^[0-9]+$/|unique:pengguna,no_hp,' . $admin->id_pengguna . ',id_pengguna',
        ], [
            'nama.required' => 'Nama tidak boleh kosong.',
            'nama.max' => 'Nama maksimal 150 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka.',
            'no_hp.unique' => 'Nomor HP sudah digunakan.',
        ]);

        try {
            $admin->update([
                'nama' => $request->nama,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
            ]);

            return redirect()->route('admin.profile.index')
                ->with('success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('admin.profile.index')
                ->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }
}
