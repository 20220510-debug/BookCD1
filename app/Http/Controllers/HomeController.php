<?php

namespace App\Http\Controllers;

use App\Models\Book;

class HomeController extends Controller
{
    public function index()
    {
        $books = Book::latest()->take(8)->get();
        return view('home', compact('books'));
    }
}