<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HelperController extends Controller
{
    public static function generateUuid()
    {
        return Str::uuid()->toString();
    }
}