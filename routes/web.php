<?php

use App\Http\Controllers\ProfileController;
use App\Jobs\ProcessPhotosUpdate;
use App\Models\Photo;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

Route::get('/', function () {
    $photos = Photo::paginate(5)->withQueryString();

    return Inertia::render('Welcome', ['photos' => $photos]);
})->name('upload');

Route::post('/upload', function (Request $request) {
    $request->validate([
        "photos" => "required|array|max:50",
        "photos.*" => "image|mimes:jpeg,png,jpg,gif,svg|max:51200",
    ]);

    /**@var UploadedFile[] */
    $photos = $request->photos;

    foreach ($photos as $photo) {
        $path = $photo->storeAs('photos', $photo->getClientOriginalName(), 'public');

        if ($path) {
            Photo::create([
                'name' => $photo->getClientOriginalName(),
                'path' => $path,
                'mime' => $photo->getClientMimeType(),
            ]);
        }
    }

    return back();
})->name('upload.photos');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
