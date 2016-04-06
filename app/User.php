<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;


/**
 * Users represent the volunteers
 */
class User extends Authenticatable
{
    protected $fillable = [
        'first_name', 'last_name',
        'email', 'password', 'phone',
        'address', 'city', 'birth_date','role'
    ];

    protected $hidden = ['password', 'remember_token'];
/*
|======================================
| Organization Relations and functions
|======================================
*/
    public function subscribedOrganizations()
    {
      return $this->belongsToMany('App\Organization', 'volunteers_subscribe_organizations')
                  ->withTimestamps();
    }

    public function subscribe($organization_id)
    {
        if(!$this->subscribedOrganizations()->find($organization_id))
            $this->subscribedOrganizations()->attach($organization_id);
    }

    public function unsubscribe($organization_id)
    {
        return $this->subscribedOrganizations()->detach($organization_id);
    }

    public function recommendations()
    {
        return $this->hasMany('App\Recommendation');
    }

    public function organizationReviews()
    {
        return $this->hasMany('App\OrganizationReview');
    }

    public function reportedOrganizationReviews()
    {
        return $this->belongsToMany('App\OrganizationReview', 'organization_review_reports', 'user_id', 'review_id')
                    ->withTimestamps();
    }
/*
|======================================
| Event Relations and functions
|======================================
*/
    public function events()
    {
        return $this->belongsToMany('App\Event','volunteers_in_events')
                    ->withTimestamps()->withPivot('type');
    }

    public function followEvent($event_id)
    {
        if (!$this->events()->find($event_id))
            $this->events()->attach($event_id,['type' => 1]);
        else
        {
            $record = $this->events()->find($event_id)->pivot;
            $record->type = 1;
            $record->save();
        }
    }

    public function unfollowEvent($event_id)
    {
        $this->events()->detach($event_id);
    }

    public function followedEvents()
    {
        return $this->events()->wherePivot('type', 1);
    }

    public function registerEvent($event_id)
    {
        if (!$this->events()->find($event_id))
            $this->events()->attach($event_id,['type' => 2]);
        else
        {
            $record = $this->events()->find($event_id)->pivot;
            $record->type = 2;
            $record->save();
        }
    }

    public function unregisterEvent($event_id)
    {
        $this->events()->detach($event_id);
    }

    public function registeredEvents()
    {
        return $this->events()->wherePivot('type', 2);
    }

    public function attendEvent($event_id)
    {
        $event = $this->registeredEvents()->find($event_id);
        if($event)
        {
            $record = $event->pivot;
            $record->type = 3;
            $record->save();
        }
    }

    public function unattendEvent($event_id)
    {
        $event = $this->registeredEvents()->find($event_id);
        if($event)
        {
            $record = $event->pivot;
            $record->type = 4;
            $record->save();
        }
    }

    public function attendedEvents()
    {
        return $this->events()->wherePivot('type', 3);
    }

    public function eventReviews()
    {
        return $this->hasMany('App\EventReview');
    }

    public function reportedEventReviews()
    {
        return $this->belongsToMany('App\EventReview', 'event_review_reports', 'user_id', 'review_id')
                    ->withTimestamps();
    }

/*
|======================================
| Challenges and Notifications
|======================================
*/
    public function notifications()
    {
        return $this->belongsToMany('App\Notification', 'user_notifications')
                    ->withTimestamps()->withPivot('read');
    }

    public function challenges()
    {
        return $this->hasMany('App\Challenge');
    }

    public function currentYearChallenge()
    {
        return $this->challenges()->currentYear();
    }

    public function previousYearsChallenges()
    {
        return $this->challenges()->previousYears();
    }
}
