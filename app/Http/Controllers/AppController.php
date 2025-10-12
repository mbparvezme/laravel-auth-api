<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppController extends Controller
{
    public function dashboard(){
        return response()->json("Dashboard");
    }

    public function apiTest(){
        return response()->json(["This is protected data, can be retrieve only by API key!"]);
    }
}
