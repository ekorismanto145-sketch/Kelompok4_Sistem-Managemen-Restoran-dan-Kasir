<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $menus = Menu::with('category')->orderBy('category_id')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('manajer.menus', compact('menus', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_available' => ['boolean'],
            'has_spicy_level' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $data['image_url'] = asset('storage/'.$path);
        }
        unset($data['image']);

        $data['is_available'] = $request->boolean('is_available', true);
        $data['has_spicy_level'] = $request->boolean('has_spicy_level');

        Menu::create($data);

        return back()->with('success', 'Menu berhasil ditambahkan.');
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_available' => ['boolean'],
            'has_spicy_level' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $data['image_url'] = asset('storage/'.$path);
        }
        unset($data['image']);

        $data['is_available'] = $request->boolean('is_available');
        $data['has_spicy_level'] = $request->boolean('has_spicy_level');

        $menu->update($data);

        return back()->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return back()->with('success', 'Menu berhasil dihapus.');
    }
}
