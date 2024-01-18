<?php
    include 'cfg.php';
	include 'showpage.php';
?>

<!DOCTYPE html>
<html lang="pl-PL>
<head>

<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="pl" />
<meta name="Author" content="Aleksander Żuk" />
<title>Piłka nożna okiem amatora</title>
<script src="../js/kolorujtlo.js" type="text/javascript"></script>
<script src="../js/timedate.js" defer></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<link rel="stylesheet" href="css/style.css"/>
</head>

<body style="background-color:lightgray;">

<div id = menu>
<table>
<tr>
<th> <a href="index.php?idp"> Menu </th>
<th> <a href="index.php?idp=Na2Nozke"> Na2Nóżkę </th>
<th> <a href="index.php?idp=PremierLeague"> Premier League </th>
<th> <a href="index.php?idp=MlodePerelki"> Młode Perelki </th>
<th> <a href="index.php?idp=oMnie"> O mnie </th>
<th> <a href="index.php?idp=Kontakt"> Kontakt </th>
<th> <a href="index.php?idp=Java"> Java </th>
<th> <a href="index.php?idp=Filmy"> Filmy </th>
</tr>
</table>
</div>


<?php 
  error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

  $strona='';
  

 

  if ($_GET['idp']=='') {
    $strona = './html/glowna.html';
  } 
  if ($_GET['idp'] == 'Na2Nozke') {
    $strona = './html/Na2Nozke.html';
  }
  if ($_GET['idp'] == 'PremierLeague') {
    $strona = './html/PremierLeague.html';
  }
  if ($_GET['idp'] == 'MlodePerelki') {
    $strona = './html/MlodePerelki.html';
  }
  if ($_GET['idp'] == 'oMnie') {
    $strona = './html/oMnie.html';
  }
  if ($_GET['idp'] == 'Kontakt') {
    $strona = './html/Kontakt.html';
  }
  if ($_GET['idp'] == 'Java') {
    $strona = './html/Java.html';
  }
  if ($_GET['idp'] == 'Filmy') {
    $strona = './html/Filmy.html';
  }
  
   if(file_exists($strona))
        {
        	include($strona);
        }
        
	$nr_indeksu= '173700';
	$nrGrupy= '3';
	
	echo'Autor:Aleksander Żuk'.$nr_indeksu.'grupa'.$nrGrupy.'<br/><br/>';
?>
</body>

</html>


