<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Pàgina de gestió </title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <meta name="generator" content="Geany 1.27" />
    <link href="../css/home.css" rel="stylesheet" type="text/css"> </head>

<body>
    <?php if (isset($_SESSION['username'])):?>
    <input type='checkbox' id='menu-toggle' />
    <label id='trigger' for='menu-toggle'> </label>
    <label id='burger' for='menu-toggle'> </label>
    <ul id='menu'>
        <li> <a href='charts.php'>Gràfiques
        </a> </li>
        <li> <a href='commands.php'>Comandes
        </a> </li>
        <li> <a href='parametres.php'>Paràmetres
        </a> </li>
        <li> <a href='sortir.php'>Sortir
        </a> </li>
    </ul>
    <div id="gros">
        <div class="mitja">
            <?php

                $m = new MongoDB\Driver\Manager("mongodb://172.16.3.24:27017");

                $filter = [

                ];

                $options = [

                ];

                $query = new MongoDB\Driver\Query($filter, $options);
                $readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);
                $rows = $m->executeQuery('trump.units', $query, $readPreference);


                print "<table>

                        <th>Hostname</th>
                        <th>Timestamp</th>
                        <th>Temperatura dels nuclis </th>
                        <th>Espai de disc</th>
                        <th>Ram</th>";

                foreach($rows as $doc) {
                    $host = $doc->hostname;

                    $filter = [
                        'hostname' => $host
                    ];

                    $options = [
                       'sort' => [ 'timestamp' => -1 ], 'limit' => 1
                    ];

                    $query = new MongoDB\Driver\Query($filter, $options);
                    $readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);
                    $Urows = $m->executeQuery('trump.monData', $query, $readPreference);

                    $hostnames = "<td>";
                    foreach ($Urows as $doc) {




                    $cores = "<td>";
                    $ncores = 0;
                    foreach ($doc->cpu->temps as $core) {
                        $cores .= "Core " . $ncores . ": " . $core->temp. " ºC <br>";
                        $ncores++;
                    }


                    $discs = "<td>";
                    
                    $spaceKeys = get_object_vars($doc->space);
                    foreach ($spaceKeys as $disc => $value){
                        $discs .= $disc . ": " . $value->used."<br>";

                    }

                    $cores .= "</td>";
                    $discs .= "</td>";




                    print "
                    <tr>
                            <td>
                                <a href='charts.php?hostname=". $doc->hostname ."'> " . $doc->hostname . "</a><a href='commands.php?hostname=" . $doc->hostname . "'> <img class='term' height='16px' src='../css/img/icon-term.png'></a>
                            </td>
                            <td>" . $doc->timestamp . "</td>
                            " . $cores . "
		                      " . $discs . "
                            <td>" . $doc->ram->used . " GB Utilitzats
                            <br>" . $doc->ram->total . " GB Totals</td>
                            </tr>";
                }
                }


                print  "</table>";
            ?>
        </div>
        <div class="mitja"></div>
        <script src="../js/jquery-3.2.1.js"></script>
        <script>
            /*function loadlink() {
                $('.mitja:first-child').load(document.URL + ' table');
                setTimeout("loadlink()", 500);
            }
            setTimeout("loadlink()", 500);*/
        </script>
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
  <div id="easter " style="height: 20px; width: 20px; float: right; background-color: #292525; " ondblclick="easter(); "></div>
  <?php endif;?>
</body>

</html>
