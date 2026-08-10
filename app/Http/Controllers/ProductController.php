<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Image;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{
    
    public function index(){
        $products = Product::with(['images', 'category'])->paginate(10);
        
        if ($products->isEmpty()) {
            return response()->json([
                "message" => "No se encontraron productos."
            ], 400);
        }

        return response()->json([
            "data" => $products,
            "message" => "Productos obtenidos exitosamente."
        ],200);
    }

    public function show($id){
        $product = Product::with(['images', 'category'])->find($id);

        if (!$product) {
            return response()->json([
                "message" => "Producto no encontrado."
            ], 404);
        }

        return response()->json([
            "data" => $product,
            "message" => "Producto obtenido exitosamente."
        ],200);
    }

    public function shop()
{
    $products = Product::with([
        'images',
        'category'
    ])
    ->where('state', 'ACTIVO')
    ->where('stock', '>', 0)
    ->paginate(12);

    return response()->json([
        'data' => $products,
        'message' => 'Productos disponibles obtenidos exitosamente.'
    ], 200);
}

    public function main(){
        $products = Product::with(['images'])->paginate(10, ['*'], 'page_products');
        
        if ($products->isEmpty()) {
            return response()->json([
                "message" => "No se encontraron productos."
            ], 400);
        }
        
        $categories = Category::with('products')->paginate(20, ['*'], 'page_categories');

        if ($categories->isEmpty()) {
            return response()->json([
                "message" => "No categories found."
            ], 404);
        }

        return response()->json([
            "data_products" => $products,
            "data_categories" => $categories,
            "message" => "Productos obtenidos exitosamente."
        ],200);
    }

    public function store(Request $request)
{
    $request->validate([
        'categoryID' => 'required|integer|exists:categories,categoryID',
        'name' => 'required|string|max:255',
        'sell_price' => 'required|numeric',
        'description' => 'required|string',
        'stock' => 'required|integer',
        'buy_price' => 'required|numeric',
        'bar_code' => 'required|numeric|unique:products,bar_code',
        'state' => 'required|in:ACTIVO,INACTIVO',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    try {
        DB::beginTransaction();

        $product = Product::create($request->all());

        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $file) {

                $path = $file->store('products', 'public');

                $image = Image::create([
                    'image' => $path
                ]);

                $product->images()->attach($image->imageID);
            }
        }

        $product->load(['images', 'category']);

        DB::commit();

        return response()->json([
            'data' => $product,
            'message' => 'Producto creado exitosamente.'
        ], 201);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'message' => 'Error al crear el producto: ' . $e->getMessage()
        ], 500);
    }
}
    public function update(Request $request, $id){
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                "message" => "Producto no encontrado."
            ], 404);
        }

        $request->validate([
            'category_id' => 'integer|exists:categories,id',
            'name' => 'string|max:255',
            'sell_price' => 'numeric',
            'wholesale_price' => 'numeric',
            'buy_price' => 'numeric',
            'bar_code' => 'numeric|unique:products,bar_code,' . $id,
            'stock' => 'integer',
            'description' => 'string',
            'state' => 'in:ACTIVO,INACTIVO',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();
            
            $product->update($request->all());

            if ($request->hasFile('images')) {
                foreach ($product->images as $image) {
                    if (Storage::disk('public')->exists($image->image)) {
                        Storage::disk('public')->delete($image->image);
                    }
                    $image->delete();
                }

                $imagesIds = [];
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products', 'public');
                    $image = Image::create(['ImageURL' => $path]);
                    $imagesIds[] = $image->imageID;
                }
                $product->images()->attach($imagesIds);
            }

            $product->load(['images', 'category']);
            
            foreach ($product->images as $img) {
                $img->ImageURL = url('storage/' . $img->ImageURL);
            }

            DB::commit();

            return response()->json([
                "data" => $product,
                "message" => "Producto actualizado exitosamente."
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "message" => "Error al actualizar el producto: " . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id){
        $product = Product::find($id);

        if (!$product){
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        try {
            DB::beginTransaction();

            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image->image)) {
                    Storage::disk('public')->delete($image->image);
                }
                $image->delete();
            }

            $product->delete();

            DB::commit();

            return response()->json([
                'message' => 'Producto eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error al eliminar el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchname($search){
        try {
            // Realizar la búsqueda por nombre, categoría (por nombre) y marca
            $product = Product::with(['images', 'category'])
                ->join('categories', 'categories.categoryID', '=', 'products.categoryID')
                ->where(function ($query) use ($search) {
                    // Buscar por nombre del producto, nombre de la categoría, o nombre de la marca
                    $query->where('products.name', 'like', '%' . $search . '%')
                        ->orWhere('categories.name', 'like', '%' . $search . '%') // Buscar en el nombre de la categoría
                        ->orWhere('products.bar_code', 'like', '%' . $search . '%'); // Buscar en el código de barras
                })
                ->select('products.*')
                ->paginate(16);

            return response()->json([
                "data" => $product,
                "message" => "objetos obtenidos exitosamente."
            ],200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra el producto, se lanza una excepción de tipo 404
            throw new NotFoundHttpException("No se encontró el producto con el identificador o nombre: " . $search);
        }
    }

    public function searchCategory($category){
        if (!$category){
            return response()->json([
                "message" => "datos inexistentes"
            ], 404);
        }

        try {
            $product = Product::with(['images', 'category'])
                ->join('categories', 'categories.categoryID', '=', 'products.categoryID') // Join con la tabla 'categories'
                ->where(function ($query) use ($category) {
                    $query->where('categories.categoryID', '=', $category);
                })
                ->select('products.*') // Asegurarse de seleccionar solo los campos de la tabla 'products'
                ->paginate(10); // Esto lanza una excepción si no se encuentra el producto

            return response()->json([
                "data" => $product,
                "message" => "Productos obtenidos exitosamente."
            ],200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra el producto, se lanza una excepción de tipo 404
            throw new NotFoundHttpException("No se encontró el producto con el identificador o nombre: " . $category);
        }
    }
}