<html>
<head>
<title>Temperature Logger</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
	var data = new google.visualization.DataTable();
	data.addColumn('datetime','Time');
	data.addColumn('number','Temperature');

        data.addRows([
<?php
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);

	class tempDB extends SQLite3 {
		function __construct() {
			$this->open('tempDB.db');
		}
	}

	$db = new tempDB();
	if (!$db) echo $db->lastErrorMsg();
	$query = "SELECT timestamp, temp FROM temps ORDER BY timestamp DESC LIMIT 2880";
	$result = $db->query($query);

	while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
		echo "[new Date(" . date('U',strtotime($row['timestamp']))*1000 . "), " . $row['temp'] . "],\n";
	}
?>	
        ]);

        var options = {
          title: 'Temperature',
	  curveType: 'function',
	  hAxis: {title: 'Date and Time'},
	  vAxis: {
	    viewWindow: {
	      min: 0,
	      max: 30
	    },
	  title: 'Temperature / \u2103'}
        };

        var chart = new google.visualization.LineChart
                        (document.getElementById('chart_div'));
	chart.draw(data, options);
      }
    </script>
</head>
<body>
<h1 style="text-align: center">Temperature Log</h1>

<div id="chart_div" style="width: 900px; height: 500px; margin: -20px auto 0 auto;"></div>

<div id="additional_components" style="margin: 10px auto 0 auto; text-align: center;">

<?php
$maxSQL = $db->querySingle("SELECT timestamp,max(temp) FROM temps", true);
$minSQL = $db->querySingle("SELECT timestamp,min(temp) FROM temps", true);

$stats = array(
	"maxTemp" => $maxSQL["max(temp)"],
	"minTemp" => $minSQL["min(temp)"],
	"maxTime" => strtotime($maxSQL["timestamp"]),
	"minTime" => strtotime($minSQL["timestamp"]),
);

?>

<div id="low">
Lowest Temperature: <strong style="color: blue;"><?php echo $stats["minTemp"]; ?>&deg;C</strong>&nbsp;
at <strong><?php echo date('G:i \o\n l jS F o', $stats["minTime"]); ?></strong>
</div>
<div id="high">
Highest Temperature: <strong style="color: red;"><?php echo $stats["maxTemp"]; ?>&deg;C</strong>&nbsp;
at <strong><?php echo date('G:i \o\n l jS F o', $stats["maxTime"]); ?></strong>
</div>

<button id="tempBtn" style="margin: 13px auto 8px auto;">Get current temperature</button>
<div id="currentTemp" style="font-weight: bold; font-size: 2em; color: green;"></div>
<script>
$("#tempBtn").click(function() {
	$.get("getTemp.php", function(temp) {
		$("#currentTemp").html(temp+'&deg;C');
		if(temp > <?php echo $stats["maxTemp"]; ?>) {
			$("#currentTemp").css("color", "red");
		} else if (temp < <?php echo $stats["minTemp"]; ?>) {
			$("#currentTemp").css("color", "blue");
		}
	});
});
</script>

</div>
</body>
</html>
