<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EventRequest;

use Carbon\Carbon;
use App\Event;
use App\Organization;
use App\Question;
use App\Notification;

use Auth;

class EventController extends Controller
{
	public function __construct(){

        $this->middleware('auth_volunteer', ['only' => [
            // Add all functions that are allowed for volunteers only
            'askQuestion', 'storeQuestion'

        ]]);

        $this->middleware('auth_organization', ['only' => [
            // Add all functions that are allowed for organizations only
            'create', 'store', 'answerQuestion', 'viewUnansweredQuestions'
        ]]);

        $this->middleware('auth_both', ['only' => [
            // Add all functions that are allowed for volunteers/organizations only

        ]]);
    }

	/**
	 * show the event's page
	 */
	public function show($id){
		// TODO: show the event's page (Hossam Ahmad)
        // hint: to display question use the scope methods in the Question model
		return Event::find($id);
	}

	/**
	 * returns a form to create a new event
	 */
	public function create(){

		return view('event.create');
	}

	/**
	 * store the created event in the database
	 */
	public function store(EventRequest $request){

		$organization = auth()->guard('organization')->user();
		$event = $organization->createEvent($request);
		//TODO: notify subscribers and nearby volunteers (Esraa)
		$subscribers = $organization->subscribers();
		$notification_description = $organization->name." created a new event ".$request->name;
		notify($subscribers,$event,$notification_description, url("/events/", $event->id));
		return redirect()->action('EventController@show', [$event_id]);
	}

	public function follow($id){

		// TODO: a volunteer can follow an unfollowed event (Hatem)
		//
		return redirect()->action('EventController@show', [$id]);
	}

	public function unfollow($id){

		// TODO: a volunteer can unfollow an already followed event (Hatem)
		//
		return redirect()->action('EventController@show', [$id]);
	}

	public function register($id){

		// TODO: a volunteer can regiseter for an event only once (Hatem)
		//
		return redirect()->action('EventController@show', [$id]);
	}

	public function unregister($id){

		// TODO: a volunteer can unregiser from an already registered event (Hatem)
		//
		return redirect()->action('EventController@show', [$id]);
	}

	public function askQuestion($id)
	{
		return view('event.ask', compact('id'));
	}

    public function storeQuestion(Request $request, $id)
    {
		$this->validate($request, [ 'question' => 'required' ]);

        $question = new Question($request->all());
        $question->user_id = Auth::user()->id;
		Event::findOrFail($id)->questions()->save($question);

        return redirect()->action('EventController@show', [$id]);
    }

    public function answerQuestion(Request $request, $id, $q_id)
    {
	 	$this->validate($request, [ 'answer' => 'required' ]);

        $question = Question::findorfail($q_id);

        if($question->event()->organization()->id != auth()->guard('organization')->user()->id){
			return redirect()->action('EventController@show', [$id])
							 ->withErrors(['Permission' => 'You do not have Permission to answer this question']);
        }

		$question->answer = $request->get('answer');
		$question->answered_at = Carbon::now();
		$question->save();

		Notification::notify(array($question->user_id), $question->event(), "Your question has been answered", url("/events/", $question->event_id, "/", $question->id));

		return redirect()->action('EventController@viewQuestions', [$id]);
    }

    public function viewUnansweredQuestions($id)
    {

        $event = Event::findorfail($id);
		if(auth()->guard('organization')->user()->id == $event->organization_id)
		{
        	$questions = $event->questions()->Unanswered()->get();
        	return view("event.answer", compact('questions'));
        }
		return redirect()->action('EventController@show', [$id])
						 ->withErrors(['Permission' => 'You do not have Permission to answer these questions']);
    }
}
