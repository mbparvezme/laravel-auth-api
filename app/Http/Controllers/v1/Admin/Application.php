<?php

namespace App\Http\Controllers\v1\Admin;
use App\Http\Controllers\Controller;

class Application extends Controller
{
    public function dashboard()
    {
        return ["data" => "Hello world"];
    }

    public function profile()
    {
        return ["data" => "Profile"];
    }
}
