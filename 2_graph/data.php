<?php
	//header is set to json format
	header('Content-Type: application/json');

	ob_start();

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

	//get the value from the html form to send to the query
	$parishInput=$_GET['parishSelection'];

	//Uses the value of whatever the user submitted for the parish that they want to see the graph for
	$myQuery="SELECT Lab_Collection_Date, Parish, Daily_Positive_Test_Count FROM la_table WHERE Parish='$parishInput' ORDER BY Lab_Collection_Date";
	$myResult=$sqlConnection->query($myQuery);

	if(!$myResult){
		die("Query Failed");
	}

	//loop through myResult and store the value in the data array
	$parishArray=array();

	//for each row of myResult, store the currentRow in the next available spot in data
	foreach($myResult as $currentRow){
		$parishArray[]=$currentRow;
	}

	//free the memory
	$myResult->close();

	//close the connection to the mysql database
	$sqlConnection->close();

	$jsonData=json_encode($parishArray);

	//display the results of the data
	print $jsonData;

	$fp = fopen('jsFiles/jsonData.json', 'w');
	fwrite($fp, $jsonData);
	fclose($fp);

	ob_end_clean();

	header("Refresh:0; url=showGraph.php");


?>