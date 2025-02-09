<?php

namespace App\Http\Controllers;

use App\Http\Resources\SpinnerResource;
use App\Models\Spinner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpinnerController extends Controller
{
    public function index(): JsonResponse
    {
        $spinner = Spinner::orderBy('spin_time', 'desc')->get();

        return sendSuccessResponse('Spinner schedule retrieved', SpinnerResource::collection($spinner));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rotation_point' => 'required|integer',
            'spin_time' => 'required|date|unique:spinners,spin_time',
        ]);

        $spinner = Spinner::create($validated);

        return sendSuccessResponse('Spinner schedule created', new SpinnerResource($spinner), 201);
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
