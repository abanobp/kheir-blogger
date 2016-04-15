<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\GalleryCaptionRequest;
use App\Http\Requests\GalleryRequest;
use Illuminate\Http\Request;
use App\Http\Requests\EventRequest;

use App\Organization;
use App\Notification;
use App\Event;
use App\Photo;

use Carbon\Carbon;
use Auth;
use Input;
use Validator;
use Session;

class EventController extends Controller
{

	public function __construct()
	{
        $this->middleware('auth_volunteer', ['only' => [
			'follow', 'unfollow', 'register', 'unregister',
			'confirm', 'attend', 'unattend'
        ]]);

        $this->middleware('auth_organization', ['only' => [
            'create', 'store', 'edit', 'update', 'destroy',
        ]]);
    }

/*
|==========================================================================
| Event CRUD Functions
|==========================================================================
|
*/
	/**
	 * Show all events of a certain organization.
	 */
	public function index($organization_id)
	{
		$organization = Organization::findOrFail($organization_id);
		$events = $organization->events()->latest()->get();
		return view('event.index', compact('organization', 'events'));
	}

	/**
	 * Show Event's page.
	 */

	public function show($id)
	{
        $event = Event::findOrFail($id);
		$creator = null;
		if(Auth::guard('organization')->id() == $event->organization_id)
			$creator = true;
		$volunteerState = 0;
		if(Auth::user())
		{
			$record = Auth::user()->events()->find($id);
			if($record)
				$volunteerState = $record->pivot->type;
		}
		return view('event.show',
			compact('event', 'creator', 'volunteerState'));
	}

	/**
	 * Create a new event.
	 */
	public function create()
	{
		return view('event.create');
	}

	/**
	 * Store the created event in the database.
	 */
	public function store(EventRequest $request)
	{
		$organization = auth()->guard('organization')->user();
		$event = $organization->createEvent($request);
		$notification_description = $organization->name." created a new event: ".$request->name;
		Notification::notify($organization->subscribers, 1, $event,
							$notification_description, url("/event", $event->id));
		return redirect()->action('Event\EventController@show', [$event->id]);
	}

	/**
	 * Edit the information of a certain event.
	 */
	public function edit($id)
	{
		$event = Event::findOrFail($id);
		if(auth()->guard('organization')->user()->id == $event->organization()->id)
			return view('event.edit', compact('event'));
		return redirect()->action('Event\EventController@show', [$id]);
	}

	/**
	 * Update the information of an edited event.
	 */
	public function update(EventRequest $request, $id)
	{
		$event = Event::findorfail($id);
		if(auth()->guard('organization')->user()->id == $event->organization()->id)
		{
			$event = Event::findOrFail($id);
			$event->update($request->all());
			Notification::notify($event->volunteers, $event,
								"Event ".($event->name)." has been updated", url("/event",$id));
		}
		return redirect()->action('Event\EventController@show', [$id]);
	}

	/**
	 * Cancel an event.
	 */
	public function destroy($id)
	{
		$event = Event::findOrFail($id);
		if(auth()->guard('organization')->user()->id == $event->organization()->id)
		{
			$event->delete();
			Notification::notify($event->volunteers, null,
								"Event ".($event->name)."has been cancelled", url("/"));
		}
		return redirect('/');
	}

/*
|==========================================================================
| Volunteers' Interaction with Event
|==========================================================================
|
*/
	public function follow($id)
	{
		Auth::user()->followEvent($id);
		return redirect()->action('Event\EventController@show', [$id]);
	}

	public function unfollow($id)
	{
		Auth::user()->unfollowEvent($id);
		return redirect()->action('Event\EventController@show', [$id]);
	}

	public function register($id)
	{
		$event = Event::findOrFail($id);
		if($event->timing > carbon::now())
			Auth::user()->registerEvent($id);
		return redirect()->action('Event\EventController@show', [$id]);
	}

	public function unregister($id)
	{
		Auth::user()->unregisterEvent($id);
		return redirect()->action('Event\EventController@show', [$id]);
	}

	public function attend($id)
	{
		$event = Event::findOrFail($id);
		if($event->timing < carbon::now())
			Auth::user()->attendEvent($id);
		return redirect()->action('Event\EventController@show',[$id]);
	}

	public function unattend($id)
	{
		$event = Event::findOrFail($id);
		if($event->timing < carbon::now())
			Auth::user()->unattendEvent($id);
		return redirect()->action('Event\EventController@show',[$id]);
	}
}