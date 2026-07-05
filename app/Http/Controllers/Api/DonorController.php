<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DonorController extends Controller
{
    // Public: register as a donor
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:donors,email',
            'phone' => 'required|string|max:20',
            'blood_group' => ['required', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'age' => 'required|integer|min:18|max:65',
            'gender' => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'city' => 'required|string|max:255',
            'address' => 'nullable|string',
            'last_donation_date' => 'nullable|date',
        ]);

        $donor = Donor::create($validated);

        return response()->json([
            'message' => 'Registration successful. Thank you for becoming a donor!',
            'donor' => $donor,
        ], 201);
    }

    // Admin only: list all donors (with optional filters)
    public function index(Request $request)
    {
        $query = Donor::query();

        if ($request->filled('blood_group')) {
            $query->where('blood_group', $request->blood_group);
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->filled('available')) {
            $query->where('available', $request->boolean('available'));
        }

        return response()->json(
            $query->latest()->paginate(15)
        );
    }

    /**
 * Public endpoint: available donor counts per blood type.
 * Returns [{ type: 'A+', units: 1 }, { type: 'O-', units: 0 }, ...]
 */
public function inventory()
{
    $types = ['O-', 'O+', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'];

    $counts = Donor::where('available', true)
        ->selectRaw('blood_group, COUNT(*) as units')
        ->groupBy('blood_group')
        ->pluck('units', 'blood_group');

    $result = collect($types)->map(fn ($type) => [
        'type' => $type,
        'units' => $counts[$type] ?? 0,
    ]);

    return response()->json($result);
}

    // Admin only: view single donor
    public function show(Donor $donor)
    {
        return response()->json($donor);
    }

    // Admin only: update donor (e.g. mark unavailable, update last donation date)
    public function update(Request $request, Donor $donor)
    {
        $validated = $request->validate([
            'available' => 'sometimes|boolean',
            'last_donation_date' => 'sometimes|nullable|date',
            'phone' => 'sometimes|string|max:20',
            'city' => 'sometimes|string|max:255',
        ]);

        $donor->update($validated);

        return response()->json(['message' => 'Donor updated', 'donor' => $donor]);
    }

    // Admin only: delete donor
    public function destroy(Donor $donor)
    {
        $donor->delete();

        return response()->json(['message' => 'Donor removed']);
    }
}
