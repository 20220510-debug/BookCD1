<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminBookController extends Controller
{
    public function index()
    {
        $books = Book::with('category', 'publisher', 'authors')->latest()->paginate(10);

        return view('admin.books.index', compact('books'));
    }

    public function create()
    {
        $categories = Category::all();
        $publishers = Publisher::all();

        return view('admin.books.create', compact('categories', 'publishers'));
    }

    public function store(Request $request)
    {
        $data = $this->validateBook($request);
        $data['image'] = $this->resolveImagePath($request);
        $data['ebook_file'] = $this->resolveEbookPath($request);

        if ($data['book_type'] === 'online') {
            $data['stock'] = 0;
        }

        $book = Book::create($data);
        $book->authors()->sync($this->resolveAuthorIds($request->input('author_names')));

        return redirect()->route('admin.books.index')->with('success', 'Them sach thanh cong');
    }

    public function edit(string $id)
    {
        $book = Book::with('authors')->findOrFail($id);
        $categories = Category::all();
        $publishers = Publisher::all();

        return view('admin.books.edit', compact('book', 'categories', 'publishers'));
    }

    public function show(string $id)
    {
        return redirect()->route('admin.books.edit', $id);
    }

    public function update(Request $request, string $id)
    {
        $book = Book::findOrFail($id);
        $data = $this->validateBook($request, $book);
        $data['image'] = $this->resolveImagePath($request, $book->image);
        $data['ebook_file'] = $this->resolveEbookPath($request, $book->ebook_file);

        if ($data['book_type'] === 'online') {
            $data['stock'] = 0;
        } else {
            $data['ebook_file'] = null;
        }

        $book->update($data);
        $book->authors()->sync($this->resolveAuthorIds($request->input('author_names')));

        return redirect()->route('admin.books.index')->with('success', 'Cap nhat sach thanh cong');
    }

    public function destroy(string $id)
    {
        Book::findOrFail($id)->delete();

        return redirect()->route('admin.books.index')->with('success', 'Xoa sach thanh cong');
    }

    protected function validateBook(Request $request, ?Book $book = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'publisher_id' => ['required', 'exists:publishers,id'],
            'book_type' => ['required', 'in:online,offline'],
            'author_names' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'image_file' => ['nullable', 'image', 'max:2048'],
            'ebook_upload' => ['nullable', 'file', 'mimes:pdf', 'max:102400'],
            'description' => ['nullable', 'string'],
        ]);

        if ($request->input('book_type') === 'online' && !$request->hasFile('ebook_upload') && !$book?->ebook_file) {
            $request->validate([
                'ebook_upload' => ['required', 'file', 'mimes:pdf', 'max:102400'],
            ]);
        }

        return $data;
    }

    protected function resolveImagePath(Request $request, ?string $currentImage = null): ?string
    {
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/books');

            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $filename);

            return '/uploads/books/' . $filename;
        }

        if ($request->filled('image_url')) {
            return $request->input('image_url');
        }

        return $currentImage;
    }

    protected function resolveEbookPath(Request $request, ?string $currentFile = null): ?string
    {
        if ($request->hasFile('ebook_upload')) {
            $file = $request->file('ebook_upload');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/ebooks');

            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $filename);

            return '/uploads/ebooks/' . $filename;
        }

        return $currentFile;
    }

    protected function resolveAuthorIds(?string $authorNames): array
    {
        if (!$authorNames) {
            return [];
        }

        $names = collect(preg_split('/\r\n|\r|\n/', $authorNames))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique()
            ->values();

        return $names->map(function ($name) {
            return Author::firstOrCreate([
                'author_name' => $name,
            ])->id;
        })->all();
    }
}
