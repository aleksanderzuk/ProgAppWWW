<?php
function PokazKontakt() {
    return '
    <form action="contact.php" method="post">
        Temat: <input type="text" name="temat"><br>
        Treść: <textarea name="tresc"></textarea><br>
        Twój email: <input type="email" name="email"><br>
        <input type="submit" name="wyslij" value="Wyślij">
    </form>
    ';
}

function WyslijMailKontakt($odbiorca) {
    if(empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        echo '[nie_wypelniles_pola]';
        echo PokazKontakt(); 
    } else {
        $mail['subject'] = $_POST['temat'];
        $mail['body'] = $_POST['tresc'];
        $mail['sender'] = $_POST['email'];
        $mail['recipient'] = $odbiorca;
        $header = "From: Formularz kontaktowy <".$mail['sender'].">\n";
        $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
        $header .="X-Sender: <".$mail['sender'].">\n";
        $header.="X-Mailer: PHP mail 1.2\n";
        $header.="X-Priority: 3\n";
        $header.="Return-Path:<".$mail['sender'].">\n";
        mail($mail['recipient'], $mail['subject'], $mail['body'], $header);
        echo '[wiadomosc_wyslana]';
    }
}

function PrzypomnijHaslo() {

    $odbiorca = 'admin@admin.com'; 
    $_POST['temat'] = 'Przypomnienie hasła';
    $_POST['tresc'] = 'Twoje hasło do panelu administracyjnego to: pepsi';
    $_POST['email'] = 'admin@admin.com';
    WyslijMailKontakt($odbiorca);
}


if (isset($_POST['wyslij'])) {
    WyslijMailKontakt('admin@admin.com'); 
}

if (isset($_POST['wyslij'])) {
    WyslijMailKontakt('admin@admin.com'); 
}


if (isset($_POST['przypomnij_haslo'])) {
    PrzypomnijHaslo();
}


echo '
<form action="contact.php" method="post">
    <input type="submit" name="przypomnij_haslo" value="Przypomnij Hasło">
</form>
';


echo PokazKontakt();
?>