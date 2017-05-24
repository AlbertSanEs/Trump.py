<?php


$username = $_POST['username'];
$password = $_POST['password'];
$hashed_password = password_hash (($_POST["password"]), PASSWORD_DEFAULT);
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->update(
    ['username' => $username],
    ['username' => $username, 'password' => $hashed_password]
);

$manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
$result = $manager->executeBulkWrite('trump.users', $bulk, $writeConcern);

header ('location: parametres.php?upd');
?>
