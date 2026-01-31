<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with('category', 'mainImage')
            ->when(request('search'), fn ($q) => $q->where('name', 'like', '%'.request('search').'%'))
            ->when(request('category'), fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', request('category'))))
            ->latest()
            ->paginate(15);

        $categories = Category::query()->orderBy('sort_order')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('sort_order')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $product = Product::create([
            ...$data,
            'brand' => $data['brand'] ?? 'LEGO',
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->handleImages($request, $product);

        return redirect()->route('admin.products.index')->with('toast', __('messages.product_created'));
    }

    public function edit(Product $product): View
    {
        $product->load('images');
        $categories = Category::query()->orderBy('sort_order')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        $product->update([
            ...$data,
            'brand' => $data['brand'] ?? 'LEGO',
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->handleImages($request, $product);

        return redirect()->route('admin.products.edit', $product)->with('toast', __('messages.product_updated'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $product->delete();

        return back()->with('toast', __('messages.product_deleted'));
    }

    private function handleImages(ProductRequest $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $mainIndex = (int) $request->input('main_image', 0);

        foreach ($request->file('images') as $index => $file) {
            $path = $file->store('products', 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'is_main' => $index === $mainIndex,
            ]);
        }

        if (! $product->images()->where('is_main', true)->exists()) {
            $first = $product->images()->first();
            if ($first) {
                $first->update(['is_main' => true]);
            }
        }
    }
}
