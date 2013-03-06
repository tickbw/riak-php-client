<?php

require_once __DIR__ . '/../vendor/autoload.php';

# Connect to Riak
$client = new Riak\Client('127.0.0.1', 8098);

# Choose a bucket name
$bucket = $client->bucket('test');

# Supply a key under which to store your data
$person = $bucket->newObject('riak_developer_1', array(
'name' => "John Smith",
'age' => 28,
'company' => "Facebook"
		));

# Save the object to Riak
$person->store();

# Fetch the object
$person = $bucket->get('riak_developer_1');

# Update the object
$person->data['company'] = "Google";
$person->store();

print_r($person);