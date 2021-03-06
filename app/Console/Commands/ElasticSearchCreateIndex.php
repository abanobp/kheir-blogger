<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Elastic\Elastic as Elasticsearch;
use Elasticsearch\ClientBuilder as elasticClientBuilder;

use App\Organization;
use App\Event;

use App\Http\Services\EventService;
use App\Http\Services\OrganizationService;

class ElasticSearchCreateIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elastic:create {index name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new index to be added to elastic search';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Elasticsearch(elasticClientBuilder::create()->build());
        $params = ['index' => $this->argument('index name')];

        $response = $client->getClient()->indices()->create($params);

        if($params['index'] == 'organizations')
            $this->seedOrganizationIndex();
        elseif($params['index'] == 'events')
            $this->seedEventIndex();

        $this->info("Index created successfully.");
    }

    /**
     * Added current organizations to index.
     */
    public function seedOrganizationIndex()
    {
        $organizations = Organization::all();
        $organizationService = new organizationService();
        foreach($organizations as $organization)
            $organizationService->indexOrganization($organization);
    }

    /**
     * Added current organizations to index.
     */
    public function seedEventIndex()
    {
        $events = Event::all();
        $eventService = new EventService();
        foreach($events as $event)
            $eventService->indexEvent($event);
    }
}
