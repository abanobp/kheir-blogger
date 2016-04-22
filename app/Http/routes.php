<?php
/*
|==========================================================================
| Application Routes
|==========================================================================
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
|    - Authentication Routes
|    - Functional Routes
|       |- Organization Routes
|       |- Volunteer Routes
|       |- Event Routes
|    - Control Routes
|    - API Routes
*/

Route::group(['middleware' => ['web']], function () {

    /**
     * Homepage for logged-in volunteers/organizations or Welcome page for others.
     */
    Route::get('/', function () {
        if(Auth::user() || auth()->guard('organization')->check())
            return view('home');
        return view('welcome');
    });

    Route::get('/home', function(){ return redirect('/'); });

/*
|==========================================================================
| Authentication Routes
|==========================================================================
|
| These routes are related to the authentication of volunteers/organizations.
|
*/
    /**
     * Organization login page.
     */
    Route::get('login_organization',function(){
        if(Auth::user() || auth()->guard('organization')->check())
            return redirect('/');
       return view('auth.login_organization');
    });

    /**
     * Organization login request.
     */
    Route::post('login_organization','Auth\OrganizationAuthController@login');

    /**
     * Organization register page.
     */
    Route::get('register_organization',function(){
        if(Auth::user() || auth()->guard('organization')->check())
            return redirect('/');
        return view('auth.register_organization');
    });

    /**
     * Organization register request.
     */
    Route::post('register_organization','Auth\OrganizationAuthController@register');

    /**
     * Organization logout request.
     */
    Route::get('logout_organization','Auth\OrganizationAuthController@logout');

    /**
     * Organization forget password.
     */
    Route::get('/password_organization/reset','Auth\OrganizationPasswordController@getEmail');
    Route::post('/password_organization/email','Auth\OrganizationPasswordController@sendResetLinkEmail');
    Route::get('/password_organization/reset/{token}','Auth\OrganizationPasswordController@getReset');
    Route::post('/password_organization/reset','Auth\OrganizationPasswordController@reset');

    /**
     *  Volunteer Authentication (register/login/logout)
     */
    Route::auth();

    /**
     *  Volunteer Login Page.
     */
    Route::get('login',function(){
        if(Auth::user() || auth()->guard('organization')->check())
            return redirect('/');
        return view('auth.login');
    });

/*
|==========================================================================
| Functional Routes
|==========================================================================
|
| These routes are related to the main actions of the applications
| associated with volunteers, organizations or events.
| For the resource, use the following functions:
|       index   => view page for all models
|       show    => view page for a single model
|       create  => view page for creating a model
|       store   => create a model with the passed request
|       edit    => view page for updating a model
|       update  => update a model with the passed request
|       destroy => delete a model
*/

    /*
    |-----------------------
    | Organization Routes
    |-----------------------
    */

    /**
     * Organization Subscription.
     */
    Route::get('organization/{id}/subscribe', 'Organization\OrganizationController@subscribe');
    Route::get('organization/{id}/unsubscribe', 'Organization\OrganizationController@unsubscribe');

    /**
     * Organization Recommendation.
     */
    Route::get('organization/{id}/recommend' , 'Organization\OrganizationController@recommend');
    Route::post('organization/{id}/recommend' , 'Organization\OrganizationController@storeRecommendation');
    Route::get('organization/{id}/recommendations', 'Organization\OrganizationController@viewRecommendations');

    /**
     * Organization Reviewing.
     */
    Route::get('organization/{id}/reviews' , 'Organization\OrganizationReviewController@index');
    Route::get('organization/{id}/review/{r_id}/report', 'Organization\OrganizationReviewController@report');
    Route::resource('organization/{id}/review', 'Organization\OrganizationReviewController');

    /**
     * Organizaton Blocking.
     */
    Route::get('organization/{id}/block','Organization\OrganizationController@block');
    Route::get('organization/{id}/unblock','Organization\OrganizationController@unblock');

    /**
     * Organization Events.
     */
    Route::get('organization/{id}/events', 'Event\EventController@index');

    /**
     * Organization CRUD.
     */
    Route::resource('organization', 'Organization\OrganizationController', ['only' => [
        'show', 'edit', 'update', 'destroy'
    ]]);

    Route::get('organization/delete/{id}' , 'Organization\OrganizationController@delete');

    /*
    |-----------------------
    | Volunteer Routes
    |-----------------------
    */

    /**
     *  Challenges Routes.
     */
    Route::get('volunteer/challenge', 'Volunteer\ChallengeController@index');
    Route::get('volunteer/challenge/create', 'Volunteer\ChallengeController@create');
    Route::post('volunteer/challenge', 'Volunteer\ChallengeController@store');
    Route::get('volunteer/challenge/edit', 'Volunteer\ChallengeController@edit');
    Route::patch('volunteer/challenge', 'Volunteer\ChallengeController@update');
    Route::get('volunteer/challenge/achieved',
                'Volunteer\ChallengeController@viewCurrentYearAttendedEvents');

    /**
     * Notification Routes.
     */
    Route::get('notifications', 'Volunteer\VolunteerController@showNotifications');
    Route::post('notifications', 'Volunteer\VolunteerController@unreadNotification');

    /**
     * Send feedback to the admin.
     */
    Route::get('feedback' , 'Volunteer\VolunteerController@createFeedback');
    Route::post('feedback' , 'Volunteer\VolunteerController@storeFeedback');

    /**
     * Volunteer dashboard.
     */
     Route::get('dashboard', 'Volunteer\VolunteerController@showDashboard');

    /**
     * Volunteer CRUD.
     */
    Route::resource('volunteer','Volunteer\VolunteerController', ['only' => [
        'show', 'edit', 'update'
    ]]);
    
     /**
     * Volunteer view his events.
     */
    Route::get('dashboard/events','Volunteer\VolunteerController@showAllEvents');

    /**
     * Volunteer dashboard.
     */
     Route::get('dashboard', 'Volunteer\VolunteerController@showDashboard');

    /*
    |-----------------------
    | Event Routes
    |-----------------------
    */

    /**
     *	Event Following.
     */
    Route::get('event/{id}/follow', 'Event\EventController@follow');
    Route::get('event/{id}/unfollow', 'Event\EventController@unfollow');

    /**
     *	Event Registeration.
     */
    Route::get('event/{id}/register', 'Event\EventController@register');
    Route::get('event/{id}/unregister', 'Event\EventController@unregister');

    /**
     * Event Attendance Confirmation.
     */
    Route::get('event/{id}/attend' , 'Event\EventController@attend');
    Route::get('event/{id}/unattend' , 'Event\EventController@unattend');

    /**
     * Event Post.
     */
    Route::resource('/event/{id}/post','Event\EventPostController');

    /**
     * Event Question.
     */
    Route::get('event/{id}/question/answer', 'Event\EventQuestionController@viewUnansweredQuestions');
    Route::post('event/{id}/question/{question}/answer', 'Event\EventQuestionController@answer');
    Route::resource('event/{id}/question', 'Event\EventQuestionController');

    /**
     * Event Gallery
     */
    Route::get('event/{id}/gallery/upload','Event\EventGalleryController@add');
    Route::post('event/{id}/gallery/upload','Event\EventGalleryController@upload');
    Route::post('event/{id}/gallery','Event\EventGalleryController@store');

    /**
     * Event Reviewing.
     */
    Route::get('event/{id}/review/{r_id}/report', 'Event\EventReviewController@report');
    Route::resource('event/{id}/review','Event\EventReviewController');

    /**
     *  Event CRUD.
     */
    Route::resource('event','Event\EventController', ['except' => 'index']);

/*
|==========================================================================
| Control Routes
|==========================================================================
|
| These routes are related to admins and validators to control
| the interactions on the website.
*/

    /**
     * Admin assign validator.
     */
    Route::get('volunteer/{id}/validate','AdminController@assignValidator');

    /**
     * Validator ban volunteer.
     */
    Route::get('volunteer/{id}/ban','AdminController@banVolunteer');

/*
|==========================================================================
| API Routes
|==========================================================================
|
| API routes are used by Android or IOS applications.
|   - Organization API Routes
|   - Volunteer API Routes
|   - Event API Routes
*/

    /*
    |--------------------------
    | Organizations API Routes
    |--------------------------
    */

    /**
    * Organization API resource.
    */
    Route::post('api/review/organization' , 'API\OrganizationReviewAPIController@store  ');

    Route::resource('api/organization','API\OrganizationAPIController', ['only' => [
        'index', 'show',
    ]]);
    Route::get('api/organization/{id}/review/{r_id}/report','API\OrganizationReviewAPIController@report');

    /*
    |-----------------------
    | Volunteer API Routes
    |-----------------------
    */

    /**
     * Feedback to Admin route
     */
    Route::post('api/feedback' , 'API\VolunteerAPIController@storeFeedback');

    /**
    * Volunteer API resource.
    */
    Route::resource('api/volunteer','API\VolunteerAPIController', ['only' => [
        'show', 'update',
    ]]);

    /*
    |--------------------------
    | Events API Routes
    |--------------------------
    */

    /**
     *	Event Following.
     */
     Route::get('api/event/follow/{id}' , 'API\EventAPIController@follow');
     Route::get('api/event/unfollow/{id}' , 'API\EventAPIController@unfollow');


    /**
     *  Event Registeration.
     */
     Route::get('api/event/register/{id}' , 'API\EventAPIController@register');
     Route::get('api/event/unregister/{id}' , 'API\EventAPIController@unregister');

    /**
     *  Event Attendance Confirmation.
     */
     Route::get('api/event/attend/{id}' , 'API\EventAPIController@attend');
     Route::get('api/event/unattend/{id}' , 'API\EventAPIController@unattend');


    /**
     * Event Reviewing.
     */
     Route::get('api/event/{id}/review/{r_id}/report' , 'API\EventReviewAPIController@report');
     Route::post('api/review/event' , 'API\EventReviewAPIController@store');

    /**
     * Event API resource.
     */
     Route::resource('api/event','API\EventAPIController', ['only' => [
         'index', 'show'
     ]]);
});
