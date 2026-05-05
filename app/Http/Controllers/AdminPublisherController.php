<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use Illuminate\Http\Request;

class AdminPublisherController extends Controller
{
    public function index()
    {
        $publishers = Publisher::latest()->paginate(10);

        return view('admin.publishers.index', compact('publishers'));
    }

    public function create()
    {
        return view('admin.publishers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'publisher_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ], [
            'publisher_name.required' => 'Vui lòng nhập tên nhà xuất bản.',
        ]);

        Publisher::create($data);

        return redirect()->route('admin.publishers.index')->with('success', 'Thêm nhà xuất bản thành công.');
    }

    public function show(string $id)
    {
        return redirect()->route('admin.publishers.index');
    }

    public function edit(string $id)
    {
        $publisher = Publisher::findOrFail($id);

        return view('admin.publishers.edit', compact('publisher'));
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'publisher_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ], [
            'publisher_name.required' => 'Vui lòng nhập tên nhà xuất bản.',
        ]);

        $publisher = Publisher::findOrFail($id);
        $publisher->update($data);

        return redirect()->route('admin.publishers.index')->with('success', 'Cập nhật nhà xuất bản thành công.');
    }

    public function destroy(string $id)
    {
        $publisher = Publisher::findOrFail($id);
        $publisher->delete();

        return redirect()->route('admin.publishers.index')->with('success', 'Xóa nhà xuất bản thành công.');
    }
}
