<?php
$macs = [ "jamie" => "E8:99:C4:83:22:1B",
	  "tom" => "04:48:9A:5E:07:50",
	  "harrison" => "BC:F5:AC:F6:F9:C7",
	  "sally" => "8C:3A:E3:63:36:6E" ];
$bools = [ "jamie" => false,
	   "tom" => false,
	   "harrison" => false,
	   "sally" => false ];

$arpOut = shell_exec("sudo arp-scan -l");

foreach ($macs as $name => $mac) {
	if (stripos($arpOut,$mac) !== false) {
		$bools[$name] = true;
	}
}

function bool2str($bool) {
	if ($bool) {
		return "<span style=\"color: green; font-weight: bold;\">At home</span>";
	} else {
		return "<span style=\"color: red; font-weight: bold;\">Not at home</span>";
	}
}
?>
<html>
<head>
<title>Who's Home?</title>
</head>
<body>
<h1>Who's Home?</h1>
<ul style="font-size: 1.4em;">
<li>Harrison: <?php echo bool2str($bools["harrison"]); ?></li>
<li>Jamie:  <?php echo bool2str($bools["jamie"]); ?></li>
<li>Sally:  <?php echo bool2str($bools["sally"]); ?></li>
<li>Tom:  <?php echo bool2str($bools["tom"]); ?></li>
</ul>
</body>
</html>


