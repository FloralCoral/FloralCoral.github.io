<?php
	//make the query to mysql for all the data that'll be displayed to the map

	$mysqli = NEW MySQLi('localhost','root','mysql','la_covid_data');

	$myQuery= $mysqli->query("SELECT Parish, Coordinates, heat_Intensity FROM heatMapData");
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=\, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css"
      integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
      crossorigin=""
    />
    <script
      src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"
      integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg=="
      crossorigin=""
    ></script>

    

    <style>
      #parishMap {
      	margin: 0 auto;
        height: 525px;
        width: 800px;
      }

      div h1{
      	text-align:center;
      }
    </style>

    <title>Display Heat-Map of Covid Cases by Parish</title>
  </head>
  <body>
    <script src="leafletHeat.js"></script>

    <p align="right" style="vertical-align: top;">
      <a href="http://localhost/mainPage/index.php">Home Page</a>
    </p>

    <div>
    	<h1 id="pageTitle">Heat-Map of Covid Cases By Parish</h1>
    </div>

    <div id="parishMap"></div>

    <script>
      // Making a map and tiles

      //this sets the starting position of the map, the coordinates are for the center of louisiana
      const mymap = L.map('parishMap').setView([31.43,-91.63], 7.4);

      //this section gives credit to openstreetmap adds their name to the bottom right half of the map
      const attribution =
        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

      const tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
      const tiles = L.tileLayer(tileUrl, { attribution });
      tiles.addTo(mymap);

      //loads the current item from your query into $rows as an associate array(aka, a dictionary)
      <?php
  		  while($rows=$myQuery->fetch_assoc())
  		  {
    		  	//get the parish and coordinates, row by row, then display them to the map
    			$coordinates=$rows['Coordinates'];
          $heatIntensity=$rows['heat_Intensity'];
          echo "var heat = L.heatLayer([
            [$coordinates, $heatIntensity], 
          ], {radius: 26,
              minOpacity:0.0,
              gradient:{
                0.00: 'rgb(255,0,255)',
                0.14: 'rgb(0,0,255)',
                0.25: 'rgb(0,255,0)',
                0.50: 'rgb(255,255,0)',
                1.00: 'rgb(255,0,0)'
              }
             }).addTo(mymap);";
  		  }
  		  //free the memory
  		  $myQuery->close();

  		  //close the connection to the mysql database
  		  $mysqli->close();
      ?>

    </script>
  </body>
</html>