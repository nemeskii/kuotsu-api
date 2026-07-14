<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    // Donor: view their own donation history
    public function index(Request $request)
    {
        $donations = $request->user()->donations()->latest()->get();

        return response()->json($donations);
    }

    // Donor: log/request a new donation
    public function store(Request $request)
    {
        $validated = $request->validate([
            'blood_group' => 'required|string',
            'units' => 'required|integer|min:1',
            'donation_date' => 'required|date',
            'location' => 'nullable|string|max:255',
        ]);

        $donation = $request->user()->donations()->create($validated);

        return response()->json([
            'message' => 'Donation logged successfully',
            'donation' => $donation,
        ], 201);
    }
}