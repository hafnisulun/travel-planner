<?php

namespace App\Http\Controllers;

use App\Http\Resources\TripResource;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TripController extends Controller
{
    /**
     * List trips.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $trips = Cache::remember(
            'users:' . Auth::id() . ':trips:pages:' . $page,
            600,
            function () use ($page) {
                return Trip::where('user_id', Auth::id())
                    ->orderBy('id', 'asc')
                    ->paginate(5);
            }
        );

        return TripResource::collection($trips);
    }

    /**
     * Store trip.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|between:3,255',
            'origin' => 'required|between:3,255',
            'destination' => 'required|between:3,255',
            'start_at' => 'required',
            'end_at' => 'required|after_or_equal:start_at',
            'type' => 'required|between:3,255',
            'description' => 'required|between:3,255',
        ]);

        $trip = new Trip();

        $trip->uuid = Str::uuid();
        $trip->user_id = Auth::id();
        $trip->title = $request->title;
        $trip->origin = $request->origin;
        $trip->destination = $request->destination;
        $trip->start_at = Carbon::parse($request->start_at)->setTimezone(env('DB_TIMEZONE'));
        $trip->end_at = Carbon::parse($request->end_at)->setTimezone(env('DB_TIMEZONE'));
        $trip->type = $request->type;
        $trip->description = $request->description;

        if (!$trip->save()) {
            return response()->json([
                'message' => 'Store trip failed.'
            ], 422);
        }

        return new TripResource($trip);
    }

    /**
     * Retrieve trip by UUID.
     */
    public function show($uuid)
    {
        $trip = Cache::remember(
            'users:' . Auth::id() . ':trips:' . $uuid,
            600,
            function () use ($uuid) {
                return Trip::where('user_id', Auth::id())
                    ->where('uuid', $uuid)
                    ->first();
            }
        );

        if (!$trip) {
            return response()->json([
                'message' => 'Trip not found.'
            ], 404);
        }

        return new TripResource($trip);
    }

    /**
     * Update the trip by UUID.
     */
    public function update(Request $request, String $uuid)
    {
        $trip = Trip::where('user_id', Auth::id())
            ->where('uuid', $uuid)
            ->first();

        if (!$trip) {
            return response()->json([
                'message' => 'Trip not found.'
            ], 404);
        }

        $trip->title = $request->title;
        $trip->origin = $request->origin;
        $trip->destination = $request->destination;
        $trip->start_at = Carbon::parse($request->start_at)->setTimezone(env('DB_TIMEZONE'));
        $trip->end_at = Carbon::parse($request->end_at)->setTimezone(env('DB_TIMEZONE'));
        $trip->type = $request->type;
        $trip->description = $request->description;

        if (!$trip->save()) {
            return response()->json([
                'message' => 'Update trip failed.'
            ], 422);
        }

        return new TripResource($trip);
    }

    /**
     * Delete the trip by UUID.
     */
    public function destroy($uuid)
    {
        $trip = Trip::where('user_id', Auth::id())
            ->where('uuid', $uuid)
            ->first();

        if (!$trip) {
            return response()->json([
                'message' => 'Trip not found.'
            ], 404);
        }

        if (!$trip->delete()) {
            return response()->json([
                'message' => 'Delete trip failed.'
            ], 422);
        }

        return response()->json([], 204);
    }
}
