<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $validated = request()->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'level' => ['nullable', 'regex:/^[1-5](,[1-5])?$/'],
            'sort' => ['nullable', 'string', 'in:created_at'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = Courier::query();

        if (!empty($validated['search'])) {
            $keyword = preg_split('/\s+/', $validated['search'], -1, PREG_SPLIT_NO_EMPTY);
            
            foreach ($keyword as $word) {
                $query->where('name', 'like', "%{$word}%");
            }
        }

        if( !empty($validated['level'])) {
            $levels = explode(',', $validated['level']);
            $query->whereIn('level', $levels);
        }

        if (($validated['sort'] ?? null) === 'created_at') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('name', 'asc');
        }

        return response()->json(
            $query->paginate(10)->withQueryString()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Courier $courier)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:20', 'unique:couriers,phone'],
            'email' => ['required', 'email', 'max:150', 'unique:couriers,email'],
            'address' => ['nullable', 'string'],
            'level' => ['required', 'integer', 'in:1,2,3,4,5'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $courier = Courier::create($validated);

        return response()->json([
            'message' => 'Courier created successfully.',
            'data' => $courier,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Courier $courier)
    {
        return response()->json(
            $courier
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Courier $courier)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:150'],
            'phone' => ['sometimes', 'string', 'max:20', 'unique:couriers,phone,' . $courier->id],
            'email' => ['sometimes', 'email', 'max:150', 'unique:couriers,email,' . $courier->id],
            'address' => ['nullable', 'string'],
            'level' => ['sometimes', 'integer', 'in:1,2,3,4,5'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $courier->update($validated);

        return response()->json([
            'message' => 'Courier updated successfully.',
            'data' => $courier->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Courier $courier)
    {
        $courier->delete();

        return response()->json(null, 204);
    }
}
