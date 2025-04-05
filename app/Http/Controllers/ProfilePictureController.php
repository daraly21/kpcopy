<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Uploadcare\Configuration;
use Uploadcare\Api;

class ProfilePictureController extends Controller
{
    public function edit()
    {
        return view('profile.edit-picture'); // Blade untuk form upload gambar
    }

    public function update(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi file gambar
        ]);

        // Inisialisasi Uploadcare
        $configuration = Configuration::create(env('UPLOADCARE_PUBLIC_KEY'), env('UPLOADCARE_SECRET_KEY'));
        $api = new Api($configuration);

        // Upload gambar ke Uploadcare
        $file = $api->uploader()->fromPath($request->file('profile_picture')->getRealPath());

        // Ambil UUID file dari Uploadcare
        $uuid = $file->getUuid();
        $publicUrl = "https://ucarecdn.com/{$uuid}/"; // URL publik

        // Simpan URL ke dalam database
        $user = Auth::user();
        $user->profile_picture = $publicUrl;
        $user->save();

        // Redirect dengan pesan sukses
        return redirect()->route('profile.edit')->with('success', 'Profile picture updated successfully!');
    }
}
