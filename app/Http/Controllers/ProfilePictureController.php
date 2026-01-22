<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfilePictureController extends Controller
{
    /**
     * Update profile picture - stored in database as base64
     */
    public function update(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Get the uploaded file
        $file = $request->file('profile_picture');
        
        // Read file contents and convert to base64
        $imageData = base64_encode(file_get_contents($file->getRealPath()));
        $mimeType = $file->getMimeType();

        // Store in database (old picture is automatically replaced - no accumulation)
        $user->profile_picture = $imageData;
        $user->profile_picture_mime = $mimeType;
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
            // Return default avatar if no profile picture
            return response()->file(public_path('images/default-avatar.png'));
        }

        // Decode base64 and return as image response
        $imageData = base64_decode($user->profile_picture);
        $mimeType = $user->profile_picture_mime ?? 'image/jpeg';

        return response($imageData)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=86400'); // Cache for 1 day
    }

    /**
     * Delete profile picture
     */
    public function destroy()
    {
        $user = Auth::user();
        $user->profile_picture = null;
        $user->profile_picture_mime = null;
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil dihapus!');
    }
}
