<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$baza = 'moja_strona';

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);
if (!$link) {
    echo '<b>Przerwane połączenie</b>';
    exit;
}
if (!mysqli_select_db($link, $baza)) {
    echo 'Nie wybrano bazy';
    exit;
}

$login = 'zuk';
$pass = 'pepsi';
?>
