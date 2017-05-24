<?php

$username = $_POST['username'];
$password = $_POST['password'];



    include ('conectar.php');

    foreach ($rows as $doc) {

    }

    if(!empty($_POST)){
        if(isset($_POST["username"]) &&isset($_POST["password"])){
            if($_POST["username"]!=""&&$_POST["password"]!=""){
            
                    $hashed_password = password_hash (($_POST["password"]), PASSWORD_DEFAULT);
    //$crypted_password = crypt($password);
                    $m = new MongoDB\Driver\Manager("mongodb://127.0.0.1:27017");

                    $bulk = new MongoDB\Driver\BulkWrite;

                    $document1 = ['username' => $username, 'password' => $hashed_password];

                    $_id1 = $bulk->insert($document1);

                    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
                    $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
                    $result = $manager->executeBulkWrite('trump.users', $bulk, $writeConcern);

                    header ('location: parametres.php?creat');

                    

                
            }else{
                header ('location: parametres.php?error');
            }
        }
    }

    
?>
