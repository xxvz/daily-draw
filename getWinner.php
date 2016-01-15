<?php

	/**
	* getWinner - Selects a winner for the Shibe Club Daily Draw
	*
	* @author xxvz <xxvz@shibe.club>
	*/

	// Make sure timezone is UTC
	date_default_timezone_set('UTC');

	// Load some files
	require __DIR__ . '/vendor/autoload.php';
	require __DIR__ . '/settings.php';

	// Set up DB connection
	ORM::configure('mysql:host=' . settings::DB_HOST . ';dbname=' . settings::DB_NAME);
	ORM::configure('username', settings::DB_USERNAME);
	ORM::configure('password', settings::DB_PASSWORD);

	// Get yesterday's date
	$yesterday = date("Y-m-d", strtotime("yesterday"));

	// Get draw entries
	$addresses = ORM::for_table('drawEntries')->where('draw', $yesterday)->find_many();
	if ($addresses != false)
	{
		// Not needed, much random!
		shuffle($addresses);

		// Get random number between
		$selected = mt_rand(0, count($addresses) - 1);
		$payout = mt_rand(1, 1000);

		// Get the lucky entry
		$lucky = $addresses[$selected];

		// Set the winner
		$winner = ORM::for_table('drawWinners')->create();
		$winner->address = $lucky->address;
		$winner->date = date("Y-m-d", strtotime($lucky->date));
		$winner->save();

		// Output (log)
		echo '['.$yesterday.'] The winner is: ' . $lucky->address . ' and the payout is: ' . $payout . "\n";
	}
	else
	{
		// Output (log)
		echo '['.$yesterday.'] No entries for draw' . "\n";
	}

