<?php


$username = $_POST['username'];
$password = $_POST['password'];



    include ('conectar.php');

    foreach ($rows as $doc) {

    }

    if(!empty($_POST)){
        if(isset($_POST["username"]) &&isset($_POST["password"])){
            if($_POST["username"]!=""&&$_POST["password"]!=""){
            if ($username == $doc->username){
                    
                    if (password_verify($password, $doc->password)) {
                      print ("<br>");
                        session_start();
                        $_SESSION['username']=$username;
                        $_SESSION['id'] = $doc->_id;
                        header ('location: home.php');
                    } else {
                      header ('location: ../index.php?error');
                    }

                }else{
                    header ('location: ../index.php?error');
                }
            }else{
                header ('location: ../index.php?error');
            }
        }
    }


?>
