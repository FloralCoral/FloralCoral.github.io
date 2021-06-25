<?php
  //make the query to mysql for all the data that'll be displayed to the map

  $mysqli = NEW MySQLi('localhost','root','mysql','la_covid_data');

  $myQuery= $mysqli->query("SELECT * FROM stateCoords");
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=\, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    <!-- load leaflet style sheet, needs to be before the leaflet js file -->
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css"
      integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
      crossorigin=""
    />

    <!-- load the leaflet js file, don't move this -->
    <script
      src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"
      integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg=="
      crossorigin=""
    ></script>

    <!-- the map needs to have a defined pixel amount, or else it won't load. can't set height and width to auto -->
    <style>
      #usMap {
      	margin: 0 auto;
        height: 500px;
        width: 800px;
      }

      div h1{
      	text-align:center;
      }

      #choiceContainer{
        text-align: center;
      }

      .colorScale{
        display: block;
        margin-left: auto;
        margin-right: auto;
      }

      .myButtons{
        display: block;
        margin-left: auto;
        margin-right: auto;
      }
    </style>

    <title>Display Covid Cases By state and date</title>
  </head>
  <body>

    <!--Load external javascript libraries-->
    <script type="text/javascript" src="jquery.min.js"></script>
    <script src="leafletHeat.js"></script>

    <!--Add a link back to the home page-->
    <p align="right" style="vertical-align: top;">
      <a href="http://localhost/mainPage/index.php">Home Page</a>
    </p>

    <div>
    	<h1 id="pageTitle">New Covid Cases By State and Date</h1>
    </div>

    <div>
      <img src="gradientScale.jpg" alt="Color Scale" width="350" height="50" class="colorScale">
    </div>

    <div id="usMap"></div>

    <script>
      var stateArray=[];
      var maxCases=0;

      //this sets the starting position of the map, the coordinates are for the center of the US
      const mymap = L.map('usMap').setView([38.5,-98], 4);

      //this section gives credit to openstreetmap adds their name to the bottom right half of the map
      const attribution =
        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

      const tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
      const tiles = L.tileLayer(tileUrl, { attribution });
      tiles.addTo(mymap);

      var layerGroup=L.layerGroup().addTo(mymap);

      var stateCoordinateDict={};

      //loads the current item from your query into $rows as an associate array(aka, a dictionary)
      <?php
        while($rows=$myQuery->fetch_assoc())
          {
            //get the state and coordinate , row by row, then adds them to the stateCoordinateDict
            $stateName=$rows['State'];
            $coordinates=$rows['Coordinates'];

            //the state is the key, the coordinate is the value.
            echo "stateCoordinateDict[\"$stateName\"]=\"$coordinates\";";
            echo"stateArray.push(\"$stateName\");";
          }
        //free the memory
        $myQuery->close();

        //close the connection to the mysql database
        $mysqli->close();
      ?>
    </script>

    <div id='statContainer'>
      
        <script type="text/javascript">
          
        </script>
      </select>
    </div>

   <!--This is where the code for the date selection starts-->
   <div id='choiceContainer'>
    <p>Choose new or total cases</p>
    <select name='statChoice' id='statistics'>
      <option value="new_results_reported">New results</option>
      <option value="total_results_reported">Total Results</option>
    </select>

    <p>Choose the date to display</p>
    <select name='dateChoice' id='dateTime'>
      <script type="text/javascript">
        $(document).ready(function(){
          var submitButton=document.getElementById('submit');
          submitButton.addEventListener('click', apiQuery);
        });
        

        var dateArray=[];
        var stateCoordandCases=[];

        //this function is used to make the displayed value for the date more readable, but doesn't change the actual value for the option
        function formatDate(dateInput){
          return (String(dateInput).split('T'))[0];
        }

        function populateDate(){

          //I'm using one state to check for the date values, because presumably they're the same for all states.
          //By only checking for one state, I'm saving time it would take to check for the 49 other states' dates
          var api_url='https://healthdata.gov/resource/j8mb-icvb.json?state_name=Alabama&overall_outcome=Positive';

          //makes the call to the api to get all the dates, used to populate the choices for the drop down menu
          $.getJSON(api_url,function(jsonData){
            $.each(jsonData,function(key,value){
              dateArray.push(value.date);
              $("#dateTime").append("<option value="+"'"+ value.date + "'>" + formatDate(value.date) + "</option>");
              
            });
          });
        }

        populateDate();

        function apiQuery(){

            //selects the value that the user has chosen for the date and statistic selection whenever submit is pressed
            var chosenDate=document.getElementById('dateTime').value;
            var statSelection=document.getElementById('statistics').value;

            //queries the API for positive cases for the chosen date, all it needs is the state name
            var apiBaseSearch='https://healthdata.gov/resource/j8mb-icvb.json?overall_outcome=Positive&date=' + chosenDate + '&state_name=';

            //for each state in stateArray, call the api to get the number of new positive cases for the chosen date and current state
            stateArray.forEach(function(state){

              //fetch the data from the api for the given state, then get json data of the response, then get the results reported from that state
              apiData=fetch(apiBaseSearch + state).then(response => response.json()).then(jsonData =>
              $.each(jsonData,function(key,value){
                
                //using if else statements because it won't let me do value.${statSelection}
                if(statSelection=="new_results_reported"){
                  numberOfCases=parseInt(value.new_results_reported);
                }
                else{
                  numberOfCases=parseInt(value.total_results_reported);
                }

                if(numberOfCases>maxCases){
                  maxCases=numberOfCases;
                }

                var coord=(stateCoordinateDict[state]).split(",");
                var lat=parseFloat(coord[0]);
                var long=parseFloat(coord[1]);

                stateCoordandCases.push([lat,long,numberOfCases]);

              }) );
            });
            
        }

        function plotPoints(){
          //for each tuple of lat, long, and numberOfCases, display the heat map data to the map
          stateCoordandCases.forEach(function(stateTuple){
            var stateLat=stateTuple[0];
            var stateLong=stateTuple[1];
            var stateCases=stateTuple[2];

            var heat = L.heatLayer([
                        [stateLat,stateLong, stateCases/maxCases], 
                      ], {radius: 38,
                          minOpacity:0.25,
                          gradient:{
                            0.00: 'rgb(255,0,255)',
                            0.25: 'rgb(0,0,255)',
                            0.50: 'rgb(0,255,0)',
                            0.75: 'rgb(255,255,0)',
                            1.00: 'rgb(255,0,0)'
                          }
                         }).addTo(layerGroup);
          });
        }

      </script>
    </select>

    <button id="submit" class="myButtons">Make Query</button>
   </div>

   <!--Button to clear the map of its points-->
   <div>
    <button id="displayMap" class="myButtons">Display Map</button>
    <button id="mapClear" class="myButtons">Clear Map</button>
   </div>

   <script type="text/javascript">
     $(document).ready(function(){
       var clearButton=document.getElementById('mapClear');
       clearButton.addEventListener('click', clearMap);

       var displayButton=document.getElementById('displayMap');
       displayButton.addEventListener('click', plotPoints);
     });

     //refreshing the page was used instead of clearing the layergroup because the browser seems to keep some memory of the previous map points.
     //after several selections, the page would slow down and the map points would overlap. So, reloading the page solves those problems
     function clearMap(){
      location.reload();
     }
   </script>

  </body>
</html>