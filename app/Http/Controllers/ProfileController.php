<?php

namespace App\Http\Controllers;

use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    public function __construct(
        protected ProfileService $profileService
    ) {}

    public function index(Request $request)
    {
        return $this->profileService->get($request);
    }

    public function update(Request $request)
    {
        return $this->profileService->update($request);
    }

    public function updateEmail(Request $request)
    {
        return $this->profileService->updateEmail($request);
    }

    public function verifyNewEmail(Request $request)
    {
        return $this->profileService->verifyNewEmail($request);
    }

    public function accountStatus(Request $request, $status)
    {
        return $this->profileService->accountStatus($status, $request);
    }

}