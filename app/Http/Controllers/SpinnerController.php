<?php

namespace App\Http\Controllers;

use App\Http\Resources\SpinnerItemsResource;
use App\Http\Resources\SpinnerResource;
use App\Http\Resources\SpinnerResourceCollection;
use App\Models\Spinner;
use App\Models\SpinnerItems;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpinnerController extends Controller
{
    public function index(): JsonResponse
    {
        $spinner = Spinner::orderBy('spin_time', 'desc')->get();

        return sendSuccessResponse('Spinner schedule retrieved', SpinnerResourceCollection::make($spinner));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rotation_point' => 'required|integer',
            'spin_time' => 'required|unique:spinners,spin_time',
        ]);

        $spinner = Spinner::create($validated);

        return sendSuccessResponse('Spinner schedule created', new SpinnerResource($spinner), 201);
    }

    public function storeItems(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
        ]);

        if (SpinnerItems::count() > 0) {
            return sendErrorResponse('Spinner items already exist', 409);
        }

        $spinnerItems = SpinnerItems::create($validated);

        return sendSuccessResponse('Spinner items created', new SpinnerItemsResource($spinnerItems), 201);
    }
    public function updateItems(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'nullable|array',
        ]);

        $spinnerItems = SpinnerItems::findOrFail(1);

        // Get the updated items with original indexes preserved
        $updatedItems = $validated['items'] ?? [];

        // Update the JSON column directly (since it's cast as an array, no need for json_encode)
        $spinnerItems->update(['items' => $updatedItems]);

        return sendSuccessResponse('Spinner items updated', [
            'items' => $spinnerItems->items,  // This ensures the response maintains indexes
        ], 201);
    }



    public function show($id): JsonResponse
    {
        $spinner = Spinner::findOrFail($id);

        return sendSuccessResponse('Spinner schedule retrieved', new SpinnerResource($spinner));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'rotation_point' => 'nullable|integer',
            'spin_time' => 'nullable|date|unique:spinners,spin_time',
        ]);

        $spinner = Spinner::findOrFail($id);
        $spinner->update($validated);

        return sendSuccessResponse('Spinner schedule updated', new SpinnerResource($spinner));
    }

    public function destroy($id): JsonResponse
    {
        $spinner = Spinner::findOrFail($id);
        $spinner->delete();

        return sendSuccessResponse('Spinner schedule deleted');
    }
}
