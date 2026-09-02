<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBonsaiTypeRequest;
use App\Models\BonsaiType;
use Illuminate\Http\JsonResponse;

class BonsaiTypeController extends Controller
{
    public function store(StoreBonsaiTypeRequest $request): JsonResponse
    {
        $bonsaiType = BonsaiType::create($request->validated());

        return response()->json($bonsaiType, 201);
    }
}
