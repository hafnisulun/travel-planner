<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TripController extends Controller
{
    /**
     * List trips.
     */
    public function index()
    {
        return Trip::where('user_id', Auth::id())->get();
    }
}
