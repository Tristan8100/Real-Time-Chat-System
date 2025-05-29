<?php

namespace App\Http\Controllers;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;


class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    //my edits
    public function addPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:20480',
        ]);

        if(Auth::user()->profile_path) {
            $oldPhoto = Auth::user()->profile_path; // Get the old photo path
            $icon = Auth::user()->profile_icon_path; // Get the old icon path
            if($oldPhoto && file_exists(public_path('images/original/' . $oldPhoto))) { // check if the file exists
                unlink(public_path('images/original/' . $oldPhoto));
            }
            if($icon && file_exists(public_path('images/icon/' . $icon))) { // check if the file exists
                unlink(public_path('images/icon/' . $icon));
            }

            $user = $request->user(); //delete
                $user->profile_path = null;
                $user->profile_icon_path = null;
                $user->save();
        }

        if ($request->hasFile('photo')) { // another validation
            $file = $request->file('photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            
            // Move the original photo
            $file->move(public_path('images/original/'), $filename);

            // Save the original path to the user
            User::where('id', Auth::id())->update(['profile_path' => $filename]);

            // Create the image manager (v3 syntax)
            $manager = new ImageManager(new Driver());

            // Load and manipulate the image
            $image = $manager->read(public_path('images/original/' . $filename));
            $image = $image->cover(100, 100); // fit alternative in v3

            $iconFilename = 'icon_' . $filename;

            // Save the icon image
            $image->save(public_path('images/icon/' . $iconFilename));

            // Save the icon path to the user
            User::where('id', Auth::id())->update(['profile_icon_path' => $iconFilename]);

            dd('Photo uploaded successfully');
        }

    }
}
