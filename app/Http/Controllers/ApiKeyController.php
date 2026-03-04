<?php

namespace App\Http\Controllers;

use App\Services\ApiKeyService;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{

    public function __construct(
        protected ApiKeyService $apiKeyService
    ) {}

    public function index(Request $request)
    {
        return $this->apiKeyService->allKeys($request);
    }

    public function store(Request $request)
    {
        return $this->apiKeyService->store($request);
    }

    public function regenerate(Request $request, $id)
    {
        return $this->apiKeyService->regenerate($request, $id);
    }

    public function destroy(Request $request, $id)
    {
        return $this->apiKeyService->destroy($request, $id);
    }

}