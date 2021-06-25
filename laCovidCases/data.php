<?php
	//header is set to json format
	header('Content-Type: application/json');

	//Database login information
	define('DB_HOST','localhost');
	define('DB_USER','root');
	define('DB_PASSWORD','mysql');
	define('DB_NAME','la_covid_data');

	//establish connection to the database
	$sqlConnection=new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

	if(!$sqlConnection){
		die("Could not connect to database " . $sqlConnection->error);
	}

	//make the query and store the result in myResult
	$myQuery="SELECT * FROM la_cases_by_day";
	$myResult=$sqlConnection->query($myQuery);

	if(!$myResult){
		die("Query Failed");
	}

	//loop through myResult and store the value in the data array
	$data=array();

	//for each row of myResult, store the currentRow in the next available spot in data
	foreach($myResult as $currentRow){
		$data[]=$currentRow;
	}

	//free the memory
	$myResult->close();

	//close the connection to the mysql database
	$sqlConnection->close();

	//display the results of the query
	print json_encode($data);
?>