<?php


$m = new MongoDB\Driver\Manager("mongodb://127.0.0.1:27017");

$query = new MongoDB\Driver\Query(array(username=>$username));
$rows = $m->executeQuery('trump.users', $query);

$query2 = new MongoDB\Driver\Query(array(password=>$password));
$rows = $m->executeQuery('trump.users', $query);


?>
