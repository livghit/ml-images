<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\VerifyDownloadCode;
use App\Jobs\ProcessPhotosUpdate;
use App\Models\DownloadCode;
use App\Models\Photo;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use ZipArchive;

Route::get('/', function () {
    return Inertia::render('Welcome');
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

// Gallery routes
Route::get('/gallery', function () {
    return Inertia::render('Gallery/EnterCode');
})->name('gallery.enter-code');

Route::post('/gallery/verify-code', function (Request $request) {
    $request->validate([
        'code' => 'required|string'
    ]);

    $downloadCode = DownloadCode::findValidCode($request->code);
    
    if (!$downloadCode) {
        return back()->withErrors(['code' => 'Invalid or expired download code']);
    }

    $request->session()->put('download_code_verified', true);
    $request->session()->put('download_code', $downloadCode->code);
    
    return redirect()->route('gallery.index');
})->name('gallery.verify-code');

Route::middleware([VerifyDownloadCode::class])->group(function () {
    Route::get('/gallery/photos', function (Request $request) {
        $photos = Photo::latest()->paginate(20)->withQueryString();
        
        return Inertia::render('Gallery/Index', [
            'photos' => $photos
        ]);
    })->name('gallery.index');

    Route::post('/gallery/download', function (Request $request) {
        $request->validate([
            'photo_ids' => 'required|array',
            'photo_ids.*' => 'integer|exists:photos,id'
        ]);

        $photos = Photo::whereIn('id', $request->photo_ids)->get();
        
        if ($photos->isEmpty()) {
            return back()->with('error', 'No photos found');
        }

        // Create temporary directory for zip
        $tempDir = storage_path('app/temp');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $zipFileName = 'wedding-photos-' . now()->format('Y-m-d-H-i-s') . '.zip';
        $zipPath = $tempDir . '/' . $zipFileName;

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($photos as $photo) {
                $photoPath = storage_path('app/public/' . $photo->path);
                if (File::exists($photoPath)) {
                    $zip->addFile($photoPath, $photo->name);
                }
            }
            $zip->close();

            return Response::download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Failed to create download archive');
    })->name('gallery.download');

    Route::post('/gallery/logout', function (Request $request) {
        $request->session()->forget(['download_code_verified', 'download_code']);
        return redirect()->route('gallery.enter-code');
    })->name('gallery.logout');
});

require __DIR__ . '/auth.php';
