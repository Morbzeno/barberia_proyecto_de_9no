<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->q.'%'))
            ->when($request->filled('category'), fn ($q) => $q->where('categoryID', $request->category))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

  public function store(Request $request)
{
    $data = $this->validated($request);

    $images = $request->file('images', []);

    unset($data['images']);

    DB::beginTransaction();

    try {
        $product = Product::create($data);

        foreach ($images as $file) {

            $path = $file->store('products', 'public');

            $image = Image::create([
                'image' => $path
            ]);

            $product->images()->attach($image->imageID);
        }

        DB::commit();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Producto creado correctamente.');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()
            ->withInput()
            ->withErrors([
                'error' => 'Error al crear el producto: ' . $e->getMessage()
            ]);
    }
}
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request, $product);

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Producto eliminado correctamente.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $barCodeRule = 'required|integer|unique:products,bar_code'.($product ? ','.$product->productID.',productID' : '');

        return $request->validate([
            'categoryID' => 'required|exists:categories,categoryID',
            'name' => 'required|string|max:100',
            'sell_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'buy_price' => 'required|numeric|min:0',
            'bar_code' => $barCodeRule,
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'state' => 'required|in:ACTIVO,INACTIVO',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
    }
}
