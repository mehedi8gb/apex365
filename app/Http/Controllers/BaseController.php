<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class BaseController
{
    /**
     * Format success response.
     *
     * @param string $message
     * @param mixed|null $data
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function sendSuccessResponse(string $message, mixed $data = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Format error response.
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function sendErrorResponse(string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }

    protected function handleApiRequest(Request $request, Builder $query)
    {
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        $sortBy = $request->query('sortBy');
        $sortDirection = $request->query('sortDirection', 'asc');
        $selectFields = $request->query('select');



        // Apply filters
        foreach ($request->query() as $key => $value) {
            if ($key !== 'page' && $key !== 'limit' && $key !== 'searchTerm' && $key !== 'sortBy' && $key !== 'sortDirection' && $key !== 'select') {
                $query->where($key, $value);
            }
        }

        // Apply search
        $searchTerm = $request->query('searchTerm');
        if ($searchTerm!== null) {
            $columns = Schema::getColumnListing($query->getModel()->getTable());
            $query->where(function ($query) use ($searchTerm, $columns) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', "%$searchTerm%");
                }
            });
        }

        // Apply sorting
        if ($sortBy) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $query->orderBy('created_at', 'desc');

        if($selectFields !== null) {
            $query->select(explode(',', $selectFields));
            $results = $query->get();
        }
        if ($limit === 'all' ) {
            $results = $query->get();
            $total = $results->count();
        } else {
            // Paginate data if limit is not 'all'
            $results = $query->paginate($limit, ['*'], 'page', $page);
            $total = $results->total();
        }
        // Paginate and format response
        // $results = $query->paginate($limit, ['*'], 'page', $page);

        // Extract relevant pagination data
        $meta = [
            'page' => $page,
            'limit' => $limit === 'all' ? $total : $limit,
            'total' => $total,
            'totalPage' => $limit === 'all' ? 1 : $results->lastPage(),
        ];

        // Format response data
        //$result = $results->items();
        $result = $results instanceof \Illuminate\Pagination\LengthAwarePaginator ? $results->items() : $results->toArray();
        return compact('meta', 'result');
    }
}
