<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('category', 'publisher', 'authors');

        // Tìm theo tên sách
        if ($request->filled('keyword')) {
            $query->where('title', 'like', '%' . $request->keyword . '%');
        }

        // Lọc theo thể loại
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Lọc theo nhà xuất bản
        if ($request->filled('publisher_id')) {
            $query->where('publisher_id', $request->publisher_id);
        }

        // Lọc theo tác giả
        if ($request->filled('author_id')) {
            $query->whereHas('authors', function ($q) use ($request) {
                $q->where('authors.id', $request->author_id);
            });
        }

        // Lọc theo giá từ
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        // Lọc theo giá đến
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Sắp xếp
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('title', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $books = $query->paginate(8)->appends($request->all());
        $categories = Category::all();
        $publishers = Publisher::all();
        $authors = Author::all();

        return view('books.index', compact('books', 'categories', 'publishers', 'authors'));
    }

    public function show($id)
    {
        $book = Book::with('category', 'publisher', 'authors')->findOrFail($id);
        return view('books.show', compact('book'));
    }
}