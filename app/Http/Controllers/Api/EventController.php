<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventIndexResource;
use App\Http\Resources\EventStoreResource;
use App\Models\Event;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try {
            $events = Event::latest()->simplePaginate(5);
            return EventIndexResource::collection($events);
            // Your code here
        } catch (Exception $e) {
            return $e->getMessage();
            // Handle expected exceptions
        } catch (\Throwable $e) {
            DB::rollback();
            return Response::error($e);
            // Handle any other errors
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['user_id'] = 1;

            $event = Event::create($validatedData);
            return new EventStoreResource($event);
        } catch (Exception $e) {
            return $e->getMessage();
        } catch (\Throwable $e) {
            DB::rollback();
            return Response::error($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        try {
            return new EventIndexResource($event);
            // Your code here
        } catch (Exception $e) {
            return $e->getMessage();
            // Handle expected exceptions
        } catch (\Throwable $e) {
            DB::rollback();
            return Response::error($e);
            // Handle any other errors
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {

        try {
            $event->update($request->validated());
            return new EventStoreResource($event);
            // Your code here
        } catch (Exception $e) {
            return $e->getMessage();
            // Handle expected exceptions
        } catch (\Throwable $e) {
            DB::rollback();
            return Response::error($e);
            // Handle any other errors
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        try {
            $event->delete();
            return response()->json(['message' => [
                'Event deleted successfully.' .
                    'Event ID: ' . $event->id .
                    'Event Name: ' . $event->name
            ]]);
            // Your code here
        } catch (Exception $e) {
            return $e->getMessage();
            // Handle expected exceptions
        } catch (\Throwable $e) {
            DB::rollback();
            return Response::error($e);
            // Handle any other errors
        }
    }
}
