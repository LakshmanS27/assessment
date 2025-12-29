<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Resume;
use App\Models\Keyword;

class ResumeUploadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');       // Require login
    }

    // Show the resume upload page
    public function create()
    {
        return view('resumeup');
    }

    // Handle resume upload
    public function store(Request $request)
    {
        // 1️⃣ Validate uploaded PDF
        $request->validate([
            'resume' => 'required|mimes:pdf|max:5120',
        ]);

        $file = $request->file('resume');
        $originalName = $file->getClientOriginalName();

        // 2️⃣ Compute SHA256 hash of uploaded file
        $fileHash = hash_file('sha256', $file->getRealPath());

        // 3️⃣ Check for duplicates by other users
        $duplicate = Resume::where('file_hash', $fileHash)
            ->where('user_id', '!=', auth()->id())
            ->first();

        if ($duplicate) {
            return back()->with('error', 'This resume has already been uploaded by another user.');
        }

        // 4️⃣ Store PDF in public/resumes
        $path = $file->storeAs('resumes', $originalName, 'public');

        // 5️⃣ Send file to FastAPI container for OCR
        $fastApiUrl = env('RESUME_SERVICE'); // Set in .env

        $response = Http::timeout(120)
            ->attach('file', file_get_contents($file->getRealPath()), $originalName)
            ->post($fastApiUrl);

        if ($response->failed()) {
            return back()->with('error', 'Failed to process resume. FastAPI is unreachable.');
        }

        $result = $response->json();

        if (!isset($result['text'])) {
            return back()->with('error', 'Resume processing failed. No text extracted.');
        }

        $extractedText = $result['text'];

        // 6️⃣ Keyword matching
        $keywords = Keyword::all();
        $matchedKeywords = [];

        foreach ($keywords as $keyword) {
            if (stripos($extractedText, $keyword->keyword) !== false) {
                $matchedKeywords[] = $keyword->keyword;
            }
        }

        $totalKeywords = $keywords->count();
        $matchedCount = count($matchedKeywords);
        $passPercentage = $totalKeywords > 0 ? round(($matchedCount / $totalKeywords) * 100, 2) : 0;
        $status = $passPercentage >= 50 ? 'valid' : 'invalid';

        // 7️⃣ Save resume record
        Resume::create([
            'user_id' => auth()->id(),
            'file_path' => $path,
            'status' => $status,
            'percentage' => $passPercentage,
            'matched_keywords' => json_encode($matchedKeywords),
            'extracted_text' => $extractedText,
            'file_hash' => $fileHash, // Automatic hash storage
        ]);

        // 8️⃣ Return results to Blade
        return back()->with('result', [
            'status' => $status,
            'percentage' => $passPercentage,
            'matched_skills' => $matchedKeywords,
        ]);
    }

    public function download($id)
{
    $resume = \App\Models\Resume::findOrFail($id);
    $user = auth()->user();

    if ($user->role !== 'admin' && $resume->user_id !== $user->id) {
        abort(403, 'Unauthorized');
    }

    $path = storage_path('app/public/' . $resume->file_path); // <-- use file_path

    if (!file_exists($path)) {
        abort(404, 'File not found');
    }

    // Use original filename from path
    $filename = basename($resume->file_path);

    return response()->download($path, $filename);
}

    // Logout user
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
