<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthorDashboardController extends Controller{
    public function __construct() {
        $this->middleware(['auth', 'role:Author | Admin']);
    }
    public function index() {
        return view('author.dashboard');
        
    }
}
