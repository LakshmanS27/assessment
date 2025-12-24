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
        $this->middleware('auth'); // Require login
        $this->middleware('role:user'); // Only users can access
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

        // 2️⃣ Store PDF in public/resumes
        $path = $file->storeAs('resumes', $originalName, 'public'); 
        // Stored in storage/app/public/resumes, publicly accessible via /storage/resumes/filename

        // 3️⃣ Send file to FastAPI container for OCR
        // $fastApiUrl = 'http://fastapi-container:8081/extract-resume'; // Docker container name
       $fastApiUrl = env('RESUME_SERVICE');

        $response = Http::timeout(120)->attach(
            'file',
            file_get_contents($file->getRealPath()), 
            $originalName
        )->post($fastApiUrl);

        if ($response->failed()) {
            return back()->with('error', 'Failed to process resume. FastAPI is unreachable.');
        }

        $result = $response->json();

        if (!isset($result['text'])) {
            return back()->with('error', 'Resume processing failed. No text extracted.');
        }

        $extractedText = $result['text'];

        // 4️⃣ Keyword matching
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

        // 5️⃣ Save record to database
        Resume::create([
        'user_id' => auth()->id(),
        'file_path' => $path,
        'status' => $status,
        'percentage' => $passPercentage,
        'matched_keywords' => json_encode($matchedKeywords), // ✅ Must match DB column
        'extracted_text' => $extractedText,
    ]);

        // 6️⃣ Return results to Blade
        return back()->with('result', [
            'status' => $status,
            'percentage' => $passPercentage,
            'matched_skills' => $matchedKeywords,
        ]);
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
