<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfilePictureController extends Controller
{
    /**
     * Update profile picture - stored in database as data URI
     */
    public function update(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Get the uploaded file
        $file = $request->file('profile_picture');
        
        // Read file contents and convert to data URI
        $imageData = base64_encode(file_get_contents($file->getRealPath()));
        $mimeType = $file->getMimeType();
        $dataUri = "data:{$mimeType};base64,{$imageData}";

        // Store in database (old picture is automatically replaced - no accumulation)
        $user->profile_picture = $dataUri;
        $user->save();

        // Redirect with success message
        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Serve profile picture from database
     */
    public function show($userId = null)
    {
        $user = $userId ? User::find($userId) : Auth::user();

        if (!$user || !$user->profile_picture) {
            // Return default avatar
            return redirect('https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User') . '&color=7F9CF5&background=EBF4FF');
        }

        // Parse data URI and return as image
        if (preg_match('/^data:([^;]+);base64,(.+)$/', $user->profile_picture, $matches)) {
            $mimeType = $matches[1];
            $imageData = base64_decode($matches[2]);

            return response($imageData)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=86400');
        }

        // Fallback for old format or invalid data
        return redirect('https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF');
    }

    /**
     * Delete profile picture
     */
    public function destroy()
    {
        $user = Auth::user();
        $user->profile_picture = null;
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil dihapus!');
    }
}

