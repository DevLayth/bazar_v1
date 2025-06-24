<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Handle the PDF upload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:2048', // max 2MB
        ]);

        $fileName = time() . '_' . $request->file('file')->getClientOriginalName();
        $path = 'invoices/' . $fileName;

        $request->file('file')->move(public_path('invoices'), $fileName);

        $url = asset($path);

        // Optionally, save the path to the database if needed
        // Invoice::create(['path' => $path]);

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => $url,
            'message' => 'PDF uploaded successfully!',
        ]);
    }

}
