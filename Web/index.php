<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Project FireWall</title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <meta name="generator" content="Geany 1.27" />
    <link href="css/index.css" rel="stylesheet" type="text/css"> </head>

<body>
    <div id="primer">
        <form action="php/login.php" name="login" method="POST">
            <input type="text" name="username" placeholder="Nom d'usuari">
            <input id="pass" type="password" name="password" placeholder="Contrasenya"> <img src="css/img/eye-icon.png" onclick="canviType()">
            <input id="boto" type="submit" value="Login">
            <div id="error">
                <?php if(isset($_GET["error"])){echo"Error de dades, torna a provar";}?>
            </div>
        </form>
    </div>
</body>
<script src="js/jquery-3.2.1.js"></script>
<script src="js/funcions.js"></script>

</html>
