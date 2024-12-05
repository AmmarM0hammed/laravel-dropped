<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Map;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function index()
    {
        $maps = Map::all();

        return response()->json($maps);
    }
    public function show($id)
    {
        $map = Map::find($id);

        if (!$map) {
            return response()->json(['message' => 'Map not found'], 404);
        }

        return response()->json($map);
    }

    public function update(Request $request, $id)
    {
        $map = Map::find($id);

        if (!$map) {
            return response()->json(['message' => 'Map not found'], 404);
        }

        // Validate input, including file validation for the image
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate file type and size
            'link' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // If there is a new image, handle the upload
        if ($request->hasFile('image')) {
            // Delete the old image if needed (optional)
            if (file_exists(storage_path('app/public/' . $map->image))) {
                unlink(storage_path('app/public/' . $map->image));
            }

            // Store the new image
            $imagePath = $request->file('image')->store('images', 'public');
            $validated['image'] = $imagePath; // Save the image path
        }

        // Update the map with validated data
        $map->update($validated);

        return response()->json($map);
    }

    public function destroy($id)
    {
        $map = Map::find($id);

        if (!$map) {
            return response()->json(['message' => 'Map not found'], 404);
        }

        $map->delete();

        return response()->json(['message' => 'Map deleted successfully']);
    }

    public function store(Request $request)
    {
        // Validate input, including file validation for the image
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate file type and size
            'link' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public'); // Store image in public disk
        }

        // Create a new map with validated data and the image path
        $map = Map::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => "storage/".$imagePath,  // Save the image path
            'link' => $validated['link'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        return response()->json($map, 201);
    }


    public function incrementUserPoints(Request $request)
    {
        // Assuming the user is authenticated, you can access the authenticated user like this
        $user = Auth::user();


        // Check if the user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Increment the user's points by 1
        DB::table('users')
            ->where('id', $user->id)
            ->increment('point', 1);


        return response()->json(['message' => 'Points increased successfully', 'points' => $user->point]);
    }
}
