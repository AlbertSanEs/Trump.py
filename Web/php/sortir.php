<?php
session_start();
session_destroy();
header ('location: ../');
  //Simplement acaba amb les sessions i ens retorna a l'inici.
?>
