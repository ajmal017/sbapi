<?php

namespace App\Events;

class BroadcastEvent extends Event
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
    	$client = new \GuzzleHttp\Client();
    	$res = $response = $client->request('POST', 'localhost:8088/storeUpdate',
    		['json' => [ 
              'data' => \App\Instrument::getAll()->get()->keyBy('code'),
            ]]
    	);
		echo $res->getStatusCode();
    }
}
