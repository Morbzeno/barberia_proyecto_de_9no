<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('products', 'images')->paginate(10);

        if (request()->wantsJson()) {
            if ($categories->isEmpty()) {
                return response()->json([
                    "message" => "No categories found."
                ], 404);
            }

            return response()->json([
                "data" => $categories,
                "message" => "Categories retrieved successfully."
            ], 200);
        }

        if ($categories->isEmpty()) {
            return redirect()->back()->with('error', 'No categories found.');
        }

        return view('categories.index', compact('categories'));
    }

    public function show($id)
    {
        $category = Category::with('products', 'images')->find($id);

        if (request()->wantsJson()) {
            if (!$category) {
                return response()->json([
                    "message" => "Category not found."
                ], 404);
            }

            return response()->json([
                "data" => $category,
                "message" => "Category retrieved successfully."
            ], 200);
        }

        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        return view('categories.show', compact('category'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|max:255|unique:categories,name',
            'description' => 'required',
            'images.*'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags.*'      => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $category = Category::create($request->all());

            if ($request->hasFile('images')) {
                $imagesIds = [];
                foreach ($request->file('images') as $file) {
                    $path = $file->store('categories', 'public');
                    $image = Image::create(['ImageURL' => $path]);
                    $imagesIds[] = $image->imageID ?? $image->id;
                }

                $category->images()->attach($imagesIds);
            }

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    "data"    => $category->load('images'),
                    "message" => "Category created successfully."
                ], 201);
            }

            return redirect()->back()->with('success', 'Category created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    "message" => "Error creating category: " . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error creating category: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            if (request()->wantsJson()) {
                return response()->json([
                    "message" => "Category not found."
                ], 404);
            }
            return redirect()->back()->with('error', 'Category not found.');
        }

        $request->validate([
            'name'        => 'required|max:255|unique:categories,name,' . $category->getKey(),
            'description' => 'required',
            'images.*'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags.*'      => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $category->update($request->all());

            if ($request->hasFile('images')) {
                $imagesIds = [];
                foreach ($request->file('images') as $file) {
                    $path = $file->store('categories', 'public');
                    $image = Image::create(['ImageURL' => $path]);
                    $imagesIds[] = $image->imageID ?? $image->id;
                }

                $category->images()->syncWithoutDetaching($imagesIds);
            }

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    "data"    => $category->load('products', 'images'),
                    "message" => "Category updated successfully."
                ], 200);
            }

            return redirect()->back()->with('success', 'Category updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    "message" => "Error updating category: " . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error updating category: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            if (request()->wantsJson()) {
                return response()->json([
                    "message" => "Category not found."
                ], 404);
            }
            return redirect()->back()->with('error', 'Category not found.');
        }

        try {
            DB::beginTransaction();

            // Desvincular las imágenes asociadas en la tabla pivote antes de eliminar
            if (method_exists($category, 'images')) {
                $category->images()->detach();
            }

            $category->delete();

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    "message" => "Category deleted successfully."
                ], 200);
            }

            return redirect()->back()->with('success', 'Category deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    "message" => "Error deleting category: " . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error deleting category: ' . $e->getMessage());
        }
    }
}