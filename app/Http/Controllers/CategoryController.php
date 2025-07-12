<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{



    public function index()
    {
        $categories = Category::select('id', 'nameEN','nameKU','nameAR', 'image')->get();
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
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images/categories'), $imageName);
            $imagePath = 'images/categories/' . $imageName;
        }

        $category = Category::create([
            'nameEN' => $request->nameEN,
            'nameKU' => $request->nameKU,
            'nameAR' => $request->nameAR,
            'image' => $imagePath,
        ]);

        return response()->json($category, 201);
    }







// Update an existing category image
public function updateImg(Request $request, $id)
{
    $category = Category::findOrFail($id);

    $request->validate([
        'image' => 'required|image',
    ]);

    // Delete old image
    if ($category->image && Storage::disk('public')->exists($category->image)) {
        Storage::disk('public')->delete($category->image);
    }

    // Store new image
    $imagePath = $request->file('image')->store('categories', 'public');

    $category->image = 'storage/' . $imagePath;
    $category->save();

    return response()->json($category, 200);
}




// Update an existing category name
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



    // Delete a category
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
