<?php

namespace App\Features\MultiDevice\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $currentTokenId = $request->user()->currentAccessToken()?->id;

        $sessions = $request->user()->tokens()
            ->with('deviceSession')
            ->latest()
            ->get()
            ->map(fn ($token) => [
                'id'           => $token->id,
                'name'         => $token->name,
                'ip_address'   => $token->deviceSession?->ip_address,
                'platform'     => $token->deviceSession?->platform,
                'device'       => $token->deviceSession?->device,
                'last_used_at' => $token->last_used_at,
                'created_at'   => $token->created_at,
                'is_current'   => $token->id === $currentTokenId,
            ]);

        return $this->success(__('app.DEVICES_FETCHED'), $sessions);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $deleted = $request->user()->tokens()->where('id', $id)->delete();

        if (!$deleted) {
            return $this->error(__('app.DEVICE_NOT_FOUND'), [], 404);
        }

        return $this->success(__('app.DEVICE_REVOKED'));
    }

    public function logoutOthers(Request $request): JsonResponse
    {
        $currentTokenId = $request->user()->currentAccessToken()?->id;

        $request->user()->tokens()
            ->when($currentTokenId, fn ($q) => $q->where('id', '!=', $currentTokenId))
            ->delete();

        return $this->success(__('app.DEVICES_OTHERS_REVOKED'));
    }
}
