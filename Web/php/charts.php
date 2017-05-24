
<?php
	$hardLimit = 10;

	if (!isset($_GET["limit"]) | $_GET["limit"] > $hardLimit) {
		$limit = $hardLimit;

	}else {
		if ($_GET["limit"] < 3) {
			$limit = 3;
		}else {
			$limit = $_GET["limit"];
		}
	}

	if (!isset($_GET["hostname"])) {
		$hostname = 'tosti-Aspire-5742G';
	}else {
		$hostname = $_GET["hostname"];
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Detailed charts about <?php echo $hostname; ?></title>
	<link rel="stylesheet" href="../css/chartist.min.css">
	<link rel="stylesheet" type="text/css" href="../css/home.css">
	<script src="../js/chartist.min.js"></script>
	<style>
		.chart {
			width: 75%;
			margin-top: 90px;
		}
		.chart svg{
			height: 150px;
		}
		.chartTitle {
			margin: auto;
			color: white;
		}
		#infochart {
			position: fixed;
			right: 0px;
			top: 5px;
		}
		.ct-grid {
			stroke: rgba(255,255,255,0.8);
		}
		.ct-label {
			color: white;
		}
		table,td,tr,th {
			border: 1px solid black;
			border-collapse: collapse;
			text-align: center;
			font-size: 14px;
			padding: 0px !important;
			padding-top: 2px;
			padding-bottom: 2px;
			box-shadow: none;
		}
		td table tr td{
			margin: 0px !important;
			padding: 5px !important;
		}

		html, body {
			overflow: auto !important;
		}
	</style>
</head>
<body>
	<input type='checkbox' id='menu-toggle' />
    <label id='trigger' for='menu-toggle'> </label>
    <label id='burger' for='menu-toggle'> </label>
    <ul id='menu'>
        <li> <a href='home.php'>Inici
        </a> </li>
        <li> <a href='parametres.php'>Paràmetres
        </a> </li>
        <li> <a href='commands.php'>Comandes
        </a> </li>
        <li> <a href='sortir.php'>Sortir
        </a> </li>
    </ul>
	<?php

		$m = new MongoDB\Driver\Manager("mongodb://172.16.3.24:27017");

		$filter = [
			'hostname' => $hostname
		];

		$options = [
			'limit' => $limit,
			'sort' => [ 'timestamp' => -1 ]
		];

		$query = new MongoDB\Driver\Query($filter, $options);
		$readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);
		$rows = $m->executeQuery('trump.monData', $query, $readPreference);
		$cpu = [];
		$ram = [];
		$diskT = [];
		$space = [];
		$pkg = [];
		$timestamp = [];
		$users = [];
		$kernel = [];
		$network = [];
		$uptime = [];

		foreach ($rows as $doc) {
			$cpu[] = $doc->cpu;
			$ram[] = $doc->ram;
			$diskT[] = $doc->disktemps;
			$space[] = $doc->space;
			$pkg[] = $doc->pkg;
			$timestamp[] = $doc->timestamp;
			$users[] = $doc->users;
			$kernel[] = $doc->kernel;
			$network[] = $doc->network;
			$uptime[] = $doc->uptime;
		}

		$cpu = array_reverse($cpu);
		$ram = array_reverse($ram);
		$diskT = array_reverse($diskT);
		$space = array_reverse($space);
		$pkg = array_reverse($pkg);
		$timestamp = array_reverse($timestamp);
		#$users = array_reverse($users);


		$labels = "";
		$data = "";
		$cUsage = "";
		$rUsage = "";
		$packages = "";
		$connUsers = "";

		$kernel = current($kernel);
		$network = current($network);
		$uptime = current($uptime);

		print("<div id='infochart'>
				<table>
					<tr>
						<th colspan='8'>Hostname</th>
					</tr>
					<tr>
						<td colspan='8'>".$hostname."</td>
					</tr>
					<tr>
						<th colspan='8'>Powered On since</th>
					</tr>
					<tr>
						<td colspan='8'>".$uptime."</td>
					</tr>
					<tr>
						<th colspan='8'>Network</th>
					</tr>
					<tr>
						<th colspan='2'>Device</th>
						<th colspan='2'>IP</th>
						<th colspan='2'>MAC</th>
						<th colspan='2'>Netmask</th>
					</tr>");

		$nics = get_object_vars($network);

		foreach ($nics as $nic => $valor) {
			print("
				<tr>
					<td colspan='2'><b>".$nic."</b></td>
					<td colspan='2'>".$network->$nic->IP."</td>
					<td colspan='2'>".$network->$nic->MAC."</td>
					<td colspan='2'>".$network->$nic->Netmask."</td>
				</tr>");
		}

		print("<tr rowspan='4'>
				<td colspan='8'>
				<table style='overflow-y: scroll; display:block; height: 103px;'>
				<tr style='position: sticky'>
					<th colspan='8'>Logged users</th>
				</tr>
				<tr style='position: sticky;'>
					<th>user</th>
					<th>tty</th>
					<th>from</th>
					<th>login</th>
					<th>idle</th>
					<th>jcpu</th>
					<th>pcpu</th>
					<th>what</th>
				</tr>");
		foreach ($users[0] as $user) {
			print("
				<tr>
					<td>".$user[0]."</td>
					<td>".$user[1]."</td>
					<td>".$user[2]."</td>
					<td>".$user[3]."</td>
					<td>".$user[4]."</td>
					<td>".$user[5]."</td>
					<td>".$user[6]."</td>
					<td>".$user[7]."</td>
				</tr>
				");
		}

		print("</table></td></tr><tr>
						<th colspan='8'>Kernel</th>
					</tr>
					<tr>
						<th colspan='2'>Arch</th>
						<th colspan='4'>Version</th>
						<th colspan='2'>OS</th>
					</tr>
					<tr>
						<td colspan='2'>".$kernel->arch."</td>
						<td colspan='4'>".$kernel->kernel."</td>
						<td colspan='2'>".$kernel->os."</td>
					</tr>
				</table>
			</div>");
		?>
		<div id="content">
		<?php
		print("<div class='chart' id='ramChart'><h4 class='chartTitle'> RAM Usage in GB</h4></div>");
		print("<div class='chart' id='cpuTChart'><h4 class='chartTitle'>CPU Temps</h4></div>");
		print("<div class='chart' id='cpuUChart'><h4 class='chartTitle'>CPU Usage %</h4></div>");
		print("<div class='chart' id='diskTempChart'><h4 class='chartTitle'>SATA drives temps</h4></div>");
		print("<div class='chart' id='pkgChart'><h4 class='chartTitle'>Paquets instal·lats</h4></div>");
		print("<div class='chart' id='userChart'><h4 class='chartTitle'>Usuaris connectats</h4></div>");

		$i = 0;

		while ($i != $limit) {
			$cpuMedium = 0;

			$disktemps[] = "[";
			if ($i == $limit-1){
				$labels .= "'" . $timestamp[$i] . "'";
				foreach ($cpu[$i]->temps as $temp) {
					$cpuMedium += floatval($temp->temp);
				}
				$cpuMedium = $cpuMedium/count($cpu[$i]->temps);
				$data .= "'" . floatval($cpuMedium) . "'";
				$cUsage .= $cpu[$i]->usage;
				$rUsage .= $ram[$i]->used;
				$packages .= $pkg[$i]->number;
				$connUsers .= count($users[$i]);

			}else{
				$labels .= "'" . $timestamp[$i] . "',";
				foreach ($cpu[$i]->temps as $temp) {
					$cpuMedium += floatval($temp->temp);
				}
				$cpuMedium = $cpuMedium/count($cpu[$i]->temps);
				$data .= "'" . floatval($cpuMedium) . "',";
				$cUsage .= $cpu[$i]->usage . ",";
				$rUsage .= $ram[$i]->used . ",";
				$packages .= $pkg[$i]->number . ",";
				$connUsers .= count($users[$i]) . ",";

			}
			$i++;
		}

		# TEMPERATURA AQUI

		$tempArrayDisk = [];
		foreach ($diskT as $disk) {
			$disks = get_object_vars($disk);
			#print_r(count($disks) . "<br>");

			foreach ($disks as $key => $value) {
				$tempArrayDisk[$key] .= $value.",";
			}
		}

		$stringTempArray = "";
		foreach ($disks as $key => $value) {
			$tempArrayDisk[$key] = substr($tempArrayDisk[$key], 0, -1);
			$stringTempArray .= "[". $tempArrayDisk[$key] ."],";

		}
		$stringTempArray = substr($stringTempArray, 0, -1);



		print("<script>
					new Chartist.Line('#cpuTChart', {
						labels: [".$labels."],
						series: [
							[".$data."]
						]
					}, {
						fullWidth: true,
						chartPadding: {
							right: 290
						}
					});
					new Chartist.Line('#cpuUChart', {
						labels: [".$labels."],
						series: [
							[".$cUsage."]
						]
					}, {
						fullWidth: true,
						chartPadding: {
							right: 290
						}
					});
					new Chartist.Line('#ramChart', {
						labels: [".$labels."],
						series: [
							[".$rUsage."]
						]
					}, {
						fullWidth: true,
						chartPadding: {
							right: 290
						}
					});
					new Chartist.Line('#pkgChart', {
						labels: [".$labels."],
						series: [
							[".$packages."]
						]
					}, {
						fullWidth: true,
						axisY: {
							onlyInteger: true
						},
						chartPadding: {
							right: 290
						}
					});
					new Chartist.Line('#userChart', {
						labels: [".$labels."],
						series: [
							[".$connUsers."]
						]
					}, {
						fullWidth: true,
						axisY: {
							onlyInteger: true
						},
						chartPadding: {
							right: 290
						}
					});
				</script>");
		print("<script>
			new Chartist.Line('#diskTempChart', {
						labels: [".$labels."],
						series: [
						" . $stringTempArray . "
						]
					}, {
						fullWidth: true,
						chartPadding: {
							right: 290
						}
					});
					new Chartist.Line('#spaceChart', {
						labels: [".$labels."],
						series: [
							[".$stringSpaceArray."]
						]
					}, {
						fullWidth: true,
						chartPadding: {
							right: 290
						}
					});</script>");
	?>
	</div>
</body>
</html>
