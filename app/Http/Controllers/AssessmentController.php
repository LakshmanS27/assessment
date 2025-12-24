<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index()
    {
        return view('dashboard'); // admin dashboard
    }

    public function showLoginForm()
    {
        return view('login'); // login page
    }
}
