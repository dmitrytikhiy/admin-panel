<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ApiController extends Controller
{
    protected function getListResponse(Builder $query, Request $request)
    {
        $perPage = $request->exists('filter.id') ? count($request->input('filter.id')) : 10;
        $page = 0;

        if ($request->exists('filter')) {
            $query->filtered($request->input('filter', []));
        }

        if ($request->exists('sort')) {
            $sortOrder = $request->input('sort', ['id', 'ASC']);
            $query->ordered($sortOrder);
        }

        if ($request->exists('range')) {
            $range = $request->input('range', [0, 10]);
            $perPage = (int)$range[1] - (int)$range[0] + 1;
            $page = (int)$range[0] / $perPage + 1;
        }

        return response()->json($query->paginate($perPage, ['*'], 'page', $page));
    }

    protected function getItemResponse($item, Request $request = null)
    {
        return response()->json($item);
    }
}
