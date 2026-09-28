<?php

namespace App\Http\Controllers;

use App\Models\SchoolProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $schoolProfile = SchoolProfile::first();
        $user = Auth::user();

        return view('home', compact('schoolProfile', 'user'));
    }
}
