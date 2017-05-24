<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Parametres</title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <meta name="generator" content="Geany 1.27" />
    <link href="../css/index.css" rel="stylesheet" type="text/css"> </head>
    <link href="../css/home.css" rel="stylesheet" type="text/css"> </head>
	<link href="../css/para.css" rel="stylesheet" type="text/css"> </head>

<body>

<?php if (($_SESSION['id'])==0):?>
<input type='checkbox' id='menu-toggle' />
    <label id='trigger' for='menu-toggle'> </label>
    <label id='burger' for='menu-toggle'> </label>
    <ul id='menu'>
        <li> <a href='home.php'>Inici
        </a> </li>
        <li> <a href='charts.php'>Gràfiques
        </a> </li>
        <li> <a href='commands.php'>Comandes
        </a> </li>
        <li> <a href='sortir.php'>Sortir
        </a> </li>
    </ul>
	<div id="primer">
        <div id="Crear">
			<p>Crear un nou usuari</p>
            <form action="crear.php" name="login" method="POST">
            <input type="text" name="username" placeholder="Nom del nou usuari">
            <input id="pass" type="password" name="password" placeholder="Contrasenya"> <img src="../css/img/eye-icon.png" onclick="canviType()">
            <input id="boto" type="submit" value="Login">
            <div id="error">
                <?php if(isset($_GET["creat"])){echo"Nou usuari creat";}?>
                <?php if(isset($_GET["error"])){echo"Alguna cosa ha sortit malament, torna a provar";}?>
            </div>
        </form>
    </div>
		<p>Actualitzar contrasenya d'usuari</p>
		<div id="Actualitzar">
				<form action="act.php" name="login" method="POST">
						<input type="text" name="username" placeholder="Nom d'usuari a actualitzar">
						<input id="pass2" type="password" name="password" placeholder="Nova contrasenya"> <img src="../css/img/eye-icon.png" onclick="canviType2()">
						<input id="boto" type="submit" value="Login">
						<div id="error">
								<?php if(isset($_GET["upd"])){echo"Contrasenya de l'usuari actualitzada";}?>
                                <?php if(isset($_GET["updError"])){echo"Usuari no introduït";}?>
                                <?php if(isset($_GET["updError2"])){echo"Usuari no introduït";}?>
						</div>
				</form>
		</div>
	</div>
    <?php else:?>
    session_destroy();
  <p id="missatge">No tens permisos per entrar aquí!</p>
  <script type="text/javascript">
      var index = "home.php"

      function redireccionament() {
          location.href = index
      }
      setTimeout("redireccionament()", 4000);

  </script>
  <?php endif;?>


</body>

<script src="../js/jquery-3.2.1.js"></script>
<script src="../js/funcions.js"></script>

</html>
