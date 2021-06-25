<?php
	$mysqli = NEW MySQLi('localhost','root','mysql','la_covid_data');

	$myQuery= $mysqli->query("SELECT Parish FROM parish_list");
?>

<script type="text/javascript" src="jsFiles/jquery.min.js"></script>
<script type="text/javascript" src="jsFiles/chart.min.js"></script>
<script type="text/javascript" src="jsFiles/app.js"></script>

<form method="get" name="form" action="data.php">
	<select name="parishSelection">
		<?php
			while($rows=$myQuery->fetch_assoc())
			{
				$parishName=$rows['Parish'];
				echo "<option value='$parishName'>$parishName</option>";
			}
		?>
	</select>
	<input type="submit" value="Submit">
</form>

<p align="right" style="vertical-align: top;">
	<a href="http://localhost/mainPage/index.php">Home Page</a>
</p>

<div id="chart-container">
	<canvas id="myParishCanvas"></canvas>
</div>