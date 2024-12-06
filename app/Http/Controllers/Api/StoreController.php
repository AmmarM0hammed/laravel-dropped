<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CouponHistory;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StoreController extends Controller
{


    public function index()
    {
        $stores = Store::all();

        return response()->json($stores);
    }
    public function store(Request $request)
    {
        // Validate input data
        $request->validate([
            'title' => 'required|string|max:255',
            'images' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // validate file type and size
            'price' => 'required|integer|min:0',
        ]);

        if ($request->hasFile('images') && $request->file('images')->isValid()) {
            // Store the image file in the 'public/stores' directory and get the file path
            $imagePath = $request->file('images')->store('stores', 'public');
        } else {
            return response()->json(['message' => 'Image upload failed'], 400);
        }

        $store = Store::create([
            'title' => $request->title,
            'images' => "storage/" . $imagePath,
            'price' => $request->price,
        ]);

        // Return the newly created store
        return response()->json($store, 201);
    }

    public function destroy($id)
    {
        // Find the store by ID
        $store = Store::find($id);

        // Check if the store exists
        if (!$store) {
            return response()->json(['message' => 'Store not found'], 404);
        }

        // Delete the store
        $store->delete();

        // Return a success message
        return response()->json(['message' => 'Store deleted successfully'], 200);
    }


    public function pay(Request $request, $storeId)
    {
        // Get the authenticated user
        $user = Auth::user();
        // Check if the user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Find the store by its ID
        $store = Store::find($storeId);

        // Check if the store exists
        if (!$store) {
            return response()->json(['message' => 'Store not found'], 404);
        }

        // Check if the user has enough points
        if ($user->point < $store->price) {
            return response()->json(['message' => 'ماكو فلوس'], 400);
        }

        // Deduct points from the user's account
        $user->point -= $store->price;

        $user_table = User::find($user->id);

        if ($user_table) {
            $user_table->point = $user->point;
            $user_table->save();
        }

        $couponCode = Str::random(10);

        $couponHistory = new CouponHistory();
        $couponHistory->user_id = $user->id;
        $couponHistory->coupon_code = $couponCode;
        $couponHistory->save();


        return response()->json([
            'message' => 'Payment successful',
            'price' => $store->price,
            'remaining_points' => $user->point,
            'coupon' => $couponCode
        ]);
    }


    public function getCoupons()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Retrieve all coupons for the authenticated user
        $coupons = CouponHistory::where('user_id', $user->id)->get();

        // Check if the user has any coupons
        if ($coupons->isEmpty()) {
            return response()->json(['message' => 'No coupons found for this user'], 404);
        }

        // Return the list of coupons
        return response()->json($coupons);
    }
}
