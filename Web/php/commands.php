<?php
	session_start();
	/*if (!isset($_SESSION["username"])) {
		$_SESSION["username"] = "Administrador";
	}*/
    if (!isset($_GET["hostname"])){
        $_GET["hostname"] = 'tosti-Aspire-5742G';
    }
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $_GET["hostname"]?> Terminal</title>
	<link rel="stylesheet" type="text/css" href="../css/home.css">
	<link rel="stylesheet" type="text/css" href="../css/com.css">
	<style type="text/css">
		<?php
			echo ".terminal .commandLine::before {
			    content: '" . $_SESSION["username"] . "@" . $_GET["hostname"] . ":~$';
			}";
		?>

	</style>
	<?php if (isset($_SESSION['username'])):?>
	<?php
		$hostname = $_GET["hostname"];

		$m = new MongoDB\Driver\Manager("mongodb://172.16.3.24:27017");

		$filter = [
			'hostname' => $hostname
		];

		$options = [
			'sort' => [ 'timestamp' => -1 ]
		];

		$query = new MongoDB\Driver\Query($filter, $options);

		$readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);

		$rows = $m->executeQuery('trump.commands', $query, $readPreference);

		if (isset($_POST["command"])) {
			$date = getdate();
			$stamp = $date["year"] . "-";

			if ($date["mon"] < 10) {
				$stamp .= "0" . $date["mon"] . "-";
			}else {
				$stamp .= $date["mon"] . "-";
			}

			if ($date["mday"] < 10) {
				$stamp .= "0" . $date["mday"] . " ";
			}else {
				$stamp .= $date["mday"] . " ";
			}

			if ($date["hours"] < 10) {
				$stamp .= "0" . $date["hours"] . ":";
			}else {
				$stamp .= $date["hours"] . ":";
			}

			if ($date["minutes"] < 10) {
				$stamp .= "0" . $date["minutes"] . ":";
			}else {
				$stamp .= $date["minutes"] . ":";
			}

			if ($date["seconds"] < 10) {
				$stamp .= "0" . $date["seconds"];
			}else {
				$stamp .= $date["seconds"];
			}

			$bulk = new MongoDB\Driver\BulkWrite;
			$doc = ['hostname' => $_POST["hostname"], 'timestamp' => $stamp, 'command'=> $_POST["command"], 'user' => $_SESSION["username"], 'status' => 'pending'];

			$_id1 = $bulk->insert($doc);
			$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
			$result = $m->executeBulkWrite('trump.commands', $bulk, $writeConcern);
		}
	?>
<body>
	<input type='checkbox' id='menu-toggle' />
    <label id='trigger' for='menu-toggle'> </label>
    <label id='burger' for='menu-toggle'> </label>
    <ul id='menu'>
        <li> <a href='home.php'>Inici
        </a> </li>
        <li> <a href='charts.php'>Gràfiques
        </a> </li>
        <li> <a href='parametres.php'>Paràmetres
        </a> </li>
        <li> <a href='sortir.php'>Sortir
        </a> </li>
    </ul>
	<div class="terminal">
			<p style="font-size: 2px; margin: 0px">panini</p>
		<pre>
            _|_F_|___|___|___|___|___|___|_R_|___|___|___|___|___|___|___|___|___|
            ___|___|___|___|___|___|___|___|___|___|___|___|___|___|_M_|___|___|__
            _|_I_|___|___|___|___|___|___|_E_|___|___|_S_|___|___|___|___|___|___|
            ___|___|___|___|___|___|___|___|___|___|___|___|___|___|_A_|___|___|__
            _|_R_|___|___|___|___|___|___|_M_|___|___|_C_|___|___|___|___|___|___|
            ___|___|___|___|___|___|___|___|___|___|___|___|___|___|_N_|___|___|__
            _|_E_|___|___|___|___|___|___|_O_|___|___|_H_|___|___|___|___|___|___|
            ___|___|___|___|___|___|___|___|___|___|___|___|___|___|_A_|___|___|__
            _|___|___|___|___|___|___|___|_T_|___|___|_E_|___|___|___|___|___|___|
            ___|___|___|___|___|___|___|___|___|___|___|___|___|___|_G_|___|___|__
            _|_W_|___|___|___|___|___|___|_E_|___|___|_U_|___|___|___|___|___|___|
            ___|___|___|___|___|___|___|___|___|___|___|___|___|___|_E_|___|___|__
            _|_A_|___|___|___|___|___|___|___|___|___|_D_|___|___|___|___|___|___|
            ___|___|___|___|___|___|___|___|___|___|___|___|___|___|_R_|___|___|__
            _|_L_|___|___|___|___|___|___|___|___|___|_L_|___|___|___|___|___|___|
            ___|___|___|___|___|___|___|___|___|___|___|___|___|___|___|___|___|__
            _|_L_|___|___|___|___|___|___|___|___|___|_E_|___|___|___|___|___|___|
            ___|___|___|___|___|___|___|___|___|___|___|___|___|___|___|___|___|__
		</pre>
		<form method="post">
			<span class="commandLine">
				<input style="display:none;" type="text" name="hostname" value=<?php echo $hostname ?>>
				<input type="text" name="command" placeholder="Input your command here and press enter to send it!">
				<input style="display: none" type="submit" value="Submit">
			</span>
		</form>
	</div>
	<div class="oldCommands">
		<?php
			print("<table>
					<tr>
						<th>Status</th>
						<th>Username</th>
						<th>Command</th>
						<th>Timestamp</th>
					</tr>
				");
			if (isset($_POST["command"])){
				print("<tr>
						<td>" . "pending" . "</td>
						<td>" . $_SESSION["username"] . "</td>
						<td>" . $_POST["command"] . "</td>
						<td>" . $stamp . "</td>
					</tr>");
			}
			foreach ($rows as $data) {
				print("<tr>
						<td>" . $data->status . "</td>
						<td>" . $data->user . "</td>
						<td>" . $data->command . "</td>
						<td>" . $data->timestamp . "</td>
					</tr>");
			}
			print("</table>");
		?>
	</div>
<?php else:?>
session_destroy();
<p id="missatge">Ups!! Sembla que no has iniciat sessió, ara et tornarem a l'inici on prodràs iniciar la teva sessió!</p>
<script type="text/javascript">
		var index = "../"

		function redireccionament() {
				location.href = index
		}
		setTimeout("redireccionament()", 4000);

		function easter() {
				location.href = "easter.php";
		}
</script>
<?php endif;?>
</body>
</html>
