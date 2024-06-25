<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendeeRequest;
use App\Http\Resources\AttendeeResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Attendee;
use App\Models\Event;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AttendeeController extends Controller
{
    use CanLoadRelationships;

    private array $relations = ['user'];
    /**
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {
        try {
            $attendees = $this->loadRelationships(
                $event->attendees()->latest()
            );

            return AttendeeResource::collection(
                $attendees->paginate(5)
            );
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
    public function store(StoreAttendeeRequest $request, Event $event)
    {
        try {
            $attendee = $this->loadRelationships(
                $event->attendees()->create([
                    'user_id' => 1
                ])
            );
    
            return new AttendeeResource($attendee);
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
     * Display the specified resource.
     */
    public function show(Event $event, Attendee $attendee)
    {
        try {
            return new AttendeeResource(
                $this->loadRelationships($attendee)
            );
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
    public function update(Request $request, Attendee $attendee)
    {
        try {
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
    public function destroy(string $event, Attendee $attendee)
    {
        try {
            $attendee->delete();
            return response(status: 204);
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