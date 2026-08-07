<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Image;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['images', 'category'])->paginate(10);

        if (request()->wantsJson()) {
            if ($products->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron productos.'
                ], 404);
            }

            return response()->json([
                'message' => 'Productos obtenidos exitosamente.',
                'data'    => $products
            ], 200);
        }

        if ($products->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron productos.');
        }

        return view('products.index', compact('products'));
    }

    public function show($id)
    {
        $product = Product::with(['images', 'category'])->find($id);

        if (request()->wantsJson()) {
            if (!$product) {
                return response()->json([
                    'message' => 'Producto no encontrado.'
                ], 404);
            }

            return response()->json([
                'message' => 'Producto obtenido exitosamente.',
                'data'    => $product
            ], 200);
        }

        if (!$product) {
            return redirect()->back()->with('error', 'Producto no encontrado.');
        }

        return view('products.show', compact('product'));
    }

    public function main()
    {
        $products = Product::with(['images', 'category'])->paginate(10, ['*'], 'page_products');
        $categories = Category::with('products')->paginate(20, ['*'], 'page_categories');

        if (request()->wantsJson()) {
            if ($products->isEmpty() && $categories->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron productos ni categorías.'
                ], 404);
            }

            return response()->json([
                'message'         => 'Productos y categorías obtenidos exitosamente.',
                'data_products'   => $products,
                'data_categories' => $categories
            ], 200);
        }

        return view('products.main', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoryID'  => 'required|integer|exists:categories,categoryID',
            'name'        => 'required|string|max:255',
            'sell_price'  => 'required|numeric|min:0',
            'buy_price'   => 'required|numeric|min:0',
            'description' => 'required|string',
            'stock'       => 'required|integer|min:0',
            'bar_code'    => 'required|string|unique:products,bar_code',
            'state'       => 'required|in:ACTIVO,INACTIVO',
            'images.*'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $productData = $request->only([
                'categoryID', 'name', 'sell_price', 'buy_price',
                'description', 'stock', 'bar_code', 'state'
            ]);

            $product = Product::create($productData);

            if ($request->hasFile('images')) {
                $imagesIds = [];
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products', 'public');
                    $image = Image::create(['ImageURL' => $path]);
                    $imagesIds[] = $image->imageID ?? $image->id;
                }
                $product->images()->attach($imagesIds);
            }

            DB::commit();

            $product->load(['images', 'category']);

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Producto creado exitosamente.',
                    'data'    => $product
                ], 201);
            }

            return redirect()->back()->with('success', 'Producto creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al crear el producto: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Producto no encontrado.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Producto no encontrado.');
        }

        $productId = $product->productID ?? $product->id;

        $request->validate([
            'categoryID'      => 'sometimes|integer|exists:categories,categoryID',
            'name'            => 'sometimes|string|max:255',
            'sell_price'      => 'sometimes|numeric|min:0',
            'wholesale_price' => 'sometimes|numeric|min:0',
            'buy_price'       => 'sometimes|numeric|min:0',
            'bar_code'        => [
                'sometimes', 'string',
                Rule::unique('products', 'bar_code')->ignore($productId, $product->getKeyName() ?? 'productID')
            ],
            'stock'           => 'sometimes|integer|min:0',
            'description'     => 'sometimes|string',
            'state'           => 'sometimes|in:ACTIVO,INACTIVO',
            'images.*'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $productData = $request->only([
                'categoryID', 'name', 'sell_price', 'wholesale_price', 'buy_price',
                'bar_code', 'stock', 'description', 'state'
            ]);

            $product->update($productData);

            if ($request->hasFile('images')) {
                // Eliminación física y lógica de imágenes previas
                foreach ($product->images as $image) {
                    if (Storage::disk('public')->exists($image->ImageURL)) {
                        Storage::disk('public')->delete($image->ImageURL);
                    }
                    $image->delete();
                }
                $product->images()->detach();

                $imagesIds = [];
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products', 'public');
                    $image = Image::create(['ImageURL' => $path]);
                    $imagesIds[] = $image->imageID ?? $image->id;
                }
                $product->images()->attach($imagesIds);
            }

            DB::commit();

            $product->load(['images', 'category']);

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Producto actualizado exitosamente.',
                    'data'    => $product
                ], 200);
            }

            return redirect()->back()->with('success', 'Producto actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al actualizar el producto: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $product = Product::with('images')->find($id);

        if (!$product) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Producto no encontrado.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Producto no encontrado.');
        }

        try {
            DB::beginTransaction();

            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image->ImageURL)) {
                    Storage::disk('public')->delete($image->ImageURL);
                }
                $image->delete();
            }

            $product->images()->detach();
            $product->delete();

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Producto eliminado exitosamente.'
                ], 200);
            }

            return redirect()->back()->with('success', 'Producto eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al eliminar el producto: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    public function searchname($search)
    {
        try {
            $products = Product::with(['images', 'category'])
                ->join('categories', 'categories.categoryID', '=', 'products.categoryID')
                ->where(function ($query) use ($search) {
                    $query->where('products.name', 'like', '%' . $search . '%')
                        ->orWhere('categories.name', 'like', '%' . $search . '%')
                        ->orWhere('products.bar_code', 'like', '%' . $search . '%');
                })
                ->select('products.*')
                ->paginate(16);

            if (request()->wantsJson()) {
                if ($products->isEmpty()) {
                    return response()->json([
                        'message' => 'No se encontraron productos para la búsqueda.'
                    ], 404);
                }

                return response()->json([
                    'message' => 'Productos obtenidos exitosamente.',
                    'data'    => $products
                ], 200);
            }

            if ($products->isEmpty()) {
                return redirect()->back()->with('error', 'No se encontraron productos para la búsqueda.');
            }

            return view('products.index', compact('products'));

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al buscar productos: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al buscar productos: ' . $e->getMessage());
        }
    }

    public function searchCategory($category)
    {
        if (!$category) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Categoría no válida.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Categoría no válida.');
        }

        try {
            $products = Product::with(['images', 'category'])
                ->join('categories', 'categories.categoryID', '=', 'products.categoryID')
                ->where('categories.categoryID', '=', $category)
                ->select('products.*')
                ->paginate(10);

            if (request()->wantsJson()) {
                if ($products->isEmpty()) {
                    return response()->json([
                        'message' => 'No se encontraron productos para esta categoría.'
                    ], 404);
                }

                return response()->json([
                    'message' => 'Productos obtenidos exitosamente.',
                    'data'    => $products
                ], 200);
            }

            if ($products->isEmpty()) {
                return redirect()->back()->with('error', 'No se encontraron productos para esta categoría.');
            }

            return view('products.index', compact('products'));

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al filtrar por categoría: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al filtrar por categoría: ' . $e->getMessage());
        }
    }
}