<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
{
    $categories = Category::with(['children' => function ($query) {
            $query->orderBy('position', 'asc');
        }])
        ->whereNull('parent_id')
        ->select('id', 'nameEN', 'nameKU', 'nameAR', 'image', 'parent_id', 'position')
        ->orderBy('position', 'asc')
        ->get();

    return response()->json($categories);
}


    public function getAllCategories()
    {
        $categories = Category::select('id', 'nameEN', 'nameKU', 'nameAR', 'image', 'parent_id')
            ->get();

        return response()->json($categories);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($category);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nameEN' => 'required|string|max:255',
            'nameKU' => 'required|string|max:255',
            'nameAR' => 'required|string|max:255',
            'image' => 'required|image',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/categories'), $imageName);
            $imagePath = 'images/categories/' . $imageName;
        }

        $category = Category::create([
            'nameEN' => $request->nameEN,
            'nameKU' => $request->nameKU,
            'nameAR' => $request->nameAR,
            'image' => $imagePath,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json($category, 201);
    }

    public function updateImg(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'image' => 'required|image',
        ]);

        if ($category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images/categories'), $imageName);
        $imagePath = 'images/categories/' . $imageName;

        $category->image = $imagePath;
        $category->save();

        return response()->json($category, 200);
    }

    public function updateName(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'nameKU' => 'required|string|max:255',
            'nameEN' => 'required|string|max:255',
            'nameAR' => 'required|string|max:255',
        ]);

        $category->nameKU = $request->nameKU;
        $category->nameEN = $request->nameEN;
        $category->nameAR = $request->nameAR;
        $category->save();
        return response()->json($category, 200);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        if ($category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted'], 200);
    }
}
