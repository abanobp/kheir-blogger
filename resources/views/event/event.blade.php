@extends('layouts.app')
@section('content')
    <div class="container">
       <h1 class="col-md-7">
           {{$event->name}}
       </h1>
        <br>
        <div class="col-md-5">
            @if($event->timing < Carbon\Carbon::now())
                <h3>
                    This event was on  {{$event->timing->format('Y-m-d')}}
                </h3>
                <h4>( {{$event->timing->diffForHumans()}} ).</h4>
            @else
                <h3>
                    the event will be held on  {{$event->timing->format('Y-m-d')}}
                </h3>
                <h4>{{$event->timing->diffForHumans()}}</h4>
            @endif
        </div>
        <br><br>
        <h1 class="text-center"> Description</h1>
        <p class="text-center">
            {{$event->description}}
        </p>
        <div class="col-md-6">
            <h1>Announcements</h1>
            @if($announcement->count()==0)
             <h3 class="alert-info">This Event has no announcements</h3>
            @else
             @foreach($announcement as $element)
                 <ul>
                     <li>{{$element->description}}</li>
                 </ul>
             @endforeach
            @endif
        </div>

        <div class="col-md-6">
            <h1>Questions and answers</h1>
            @if($questions->count()==0)
                <h3 class="alert-info">This Event has no answered Questions</h3>
            @else
            @foreach($questions as $question)
                <ul>
                    <li>{{$question->question_body .'?'}}</li>
                    <h6>by {{\App\User::findOrFail($question->user_id)->name}}</h6>
                     <h4>{{$question->answer}}</h4>
                </ul>
            @endforeach
            @endif
        </div>
        <div class="col-md-6">
            <h1>Reviews</h1>
            @if($reviews->count()==0)
                <h3 class="alert-info">This Event has no Reviews</h3>
            @else
            @foreach($reviews as $review)
                <div class="jumbotron">

                    <h3>
                        {{$review->review}}
                    </h3>
                        <h5>By {{\App\User::findOrFail($review->user_id)->name}}</h5>

                </div>
            @endforeach
            @endif
        </div>

        <div class ="col-md-6">

        </div>
    </div>
@stop