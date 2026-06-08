<?php

namespace App\Features\IpRules\Controllers;

use App\Features\IpRules\Models\IpRule;
use App\Features\IpRules\Requests\StoreIpRuleRequest;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class IpRuleController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        return $this->success(__('app.IP_RULES_FETCHED'), IpRule::latest()->get());
    }

    public function store(StoreIpRuleRequest $request): JsonResponse
    {
        $rule = IpRule::create($request->validated());

        return $this->success(__('app.IP_RULE_ADDED'), $rule, 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = IpRule::destroy($id);

        if (!$deleted) {
            return $this->error(__('app.IP_RULE_NOT_FOUND'), [], 404);
        }

        return $this->success(__('app.IP_RULE_REMOVED'));
    }
}
