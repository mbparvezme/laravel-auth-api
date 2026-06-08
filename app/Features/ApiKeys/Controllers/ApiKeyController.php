<?php

namespace App\Features\ApiKeys\Controllers;

use App\Features\ApiKeys\Models\ApiKey;
use App\Features\ApiKeys\Requests\CreateApiKeyRequest;
use App\Http\Controllers\Controller;
use App\Services\AppLogService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    use ApiResponse;

    public function __construct(private AppLogService $log) {}

    public function index(Request $request): JsonResponse
    {
        $keys = $request->user()->apiKeys()
            ->select(['id', 'name', 'prefix', 'last_used_at', 'created_at'])
            ->latest()
            ->get();

        return $this->success(__('app.API_KEYS_FETCHED'), $keys);
    }

    public function store(CreateApiKeyRequest $request): JsonResponse
    {
        [$key, $rawToken] = ApiKey::generate($request->user()->id, $request->name);

        $this->log->log('API_KEY_CREATED', $request->user()->id);

        return $this->success(__('app.API_KEY_CREATED'), [
            'id'         => $key->id,
            'name'       => $key->name,
            'prefix'     => $key->prefix,
            'key'        => $rawToken,
            'created_at' => $key->created_at,
        ], 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $deleted = $request->user()->apiKeys()->where('id', $id)->delete();

        if (!$deleted) {
            return $this->error(__('app.API_KEY_NOT_FOUND'), [], 404);
        }

        $this->log->log('API_KEY_REVOKED', $request->user()->id);

        return $this->success(__('app.API_KEY_REVOKED'));
    }
}
