<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReklamSlide;

class ReklamSlideController extends Controller
{
    /**
     * Store a newly created ReklamSlide in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|max:2048', // Max size 2MB
            'url' => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = 'images/reklam/' . $fileName;

            // Move the uploaded file to the designated directory
            $file->move(public_path('images/reklam'), $fileName);

            // Create a new ReklamSlide record
            $reklamSlide = ReklamSlide::create([
                'image_path' => $filePath,
                'url' => $request->url,
            ]);

            return response()->json([
                'message' => 'Image and link saved successfully!',
                'data' => $reklamSlide,
            ], 201);
        }

        return response()->json(['message' => 'Image upload failed.'], 400);
    }

    /**
     * Display a listing of the ReklamSlides.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $reklamSlides = ReklamSlide::all();

        return response()->json($reklamSlides, 200);
    }

    /**
     * Display the specified ReklamSlide.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $reklamSlide = ReklamSlide::find($id);

        if ($reklamSlide) {
            return response()->json($reklamSlide, 200);
        }

        return response()->json(['message' => 'ReklamSlide not found.'], 404);
    }

    /**
     * Update the specified ReklamSlide in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $reklamSlide = ReklamSlide::find($id);

        if (!$reklamSlide) {
            return response()->json(['message' => 'ReklamSlide not found.'], 404);
        }

        $validated = $request->validate([
            'image' => 'nullable|image|max:2048', // Max size 2MB
            'url' => 'nullable|url',
        ]);

        // Check if a new image is uploaded
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = 'images/reklam/' . $fileName;

            // Move the uploaded file to the designated directory
            $file->move(public_path('images/reklam'), $fileName);

            $reklamSlide->image_path = $filePath; // Update image path
        }

        // Update URL if provided
        if ($request->has('url')) {
            $reklamSlide->url = $request->url;
        }

        // Save the changes
        $reklamSlide->save();

        return response()->json([
            'message' => 'ReklamSlide updated successfully!',
            'data' => $reklamSlide,
        ], 200);
    }

    /**
     * Remove the specified ReklamSlide from the database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $reklamSlide = ReklamSlide::find($id);

        if ($reklamSlide) {
            // Optionally, delete the image file from the server
            $imagePath = public_path($reklamSlide->image_path);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $reklamSlide->delete();
            return response()->json(['message' => 'ReklamSlide deleted successfully.'], 200);
        }

        return response()->json(['message' => 'ReklamSlide not found.'], 404);
    }
}
