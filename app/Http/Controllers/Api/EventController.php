<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventIndexResource;
use App\Http\Resources\EventShowResource;
use App\Http\Resources\EventStoreResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;


class EventController extends Controller
{
    use CanLoadRelationships;

    private array $relations = ['user', 'attendees', 'attendees.user'];
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $query = $this->loadRelationships(Event::query());
            $events = $query->latest()->paginate(5);
            return EventIndexResource::collection($events);
        } catch (Exception $e) {
            return $e->getMessage();
        } catch (\Throwable $e) {
            DB::rollback();
            return Response::error($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['user_id'] = $request->user()->id;
            $event = Event::create($validatedData);

            return new EventStoreResource($this->loadRelationships($event));
            
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
            $event->load('user', 'attendees');
            return new EventShowResource($this->loadRelationships($event));
        } catch (Exception $e) {
            return $e->getMessage();
        } catch (\Throwable $e) {
            DB::rollback();
            return Response::error($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        try {
            if (Gate::denies('update', $event)) {
                abort(403, 'You are not authorized to update this event.');
            }

            $event->update($request->validated());
            return new EventStoreResource($this->loadRelationships($event));
        } catch (Exception $e) {
            return $e->getMessage();
        } catch (\Throwable $e) {
            DB::rollback();
            return Response::error($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        try {
            if (Gate::denies('delete', $event)) {
                abort(403, 'You are not authorized to delete this event.');
            }
            $event->delete();
            return response()->json(['message' => [
                'Event deleted successfully.' .
                    'Event ID: ' . $event->id .
                    'Event Name: ' . $event->name
            ]]);
        } catch (Exception $e) {
            return $e->getMessage();
        } catch (\Throwable $e) {
            DB::rollback();
            return Response::error($e);
        }
    }
}