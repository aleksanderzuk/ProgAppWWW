<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Administracyjny</title>
    <link rel="stylesheet" href="../css/style_admin.css">
</head>
<body>
<div class="admin-container">
	<?php
	include('../cfg.php');
	session_start();
	
	function FormularzLogowania() 
	{
	return '
	<div class="logowanie">
		<h1 class="heading">Panel CMS:</h1>
		<div class="logowanie">
			<form method="post" name="LoginForm" enctype="multipart/form-data" action="'.$_SERVER['REQUEST_URI'].'"> 
				<table class="logowanie">
					<tr><td class="log4_t">[email]</td><td><input type="text" name="login_email" class="logowanie" /></td></tr>
					<tr><td class="log4_t">[haslo]</td><td><input type="password" name="login_pass" class="logowanie" /></td></tr>
					<tr><td>&nbsp</td><td><input type="submit" name="x1_submit" class="logowanie" value="Zaloguj" /></td></tr>
				</table>
			</form>
		</div>
	</div>
	';
	return $wynik;
	}
	//Obsluga logowania
	if (isset($_POST['login_email']) && isset($_POST['login_pass'])) {
		if ($_POST['login_email'] == $login && $_POST['login_pass'] == $pass) {
			$_SESSION['loggedin'] = true;
		} else {
			echo 'Błędne dane logowania';
			echo FormularzLogowania();
			exit;
		}
	}
	if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
		echo FormularzLogowania();
		exit;
	}
	function ListaPodstron($link) {
		$query = "SELECT id, page_title FROM page_list";
		$result = mysqli_query($link, $query);
		while ($row = mysqli_fetch_assoc($result)) {
			echo 'ID: ' . $row['id'] . ', Tytuł: ' . $row['page_title'];
			echo ' <a href="admin.php?edit=' . $row['id'] . '">Edytuj</a>';
			echo ' <form method="post" action="admin.php" style="display:inline;">
						<input type="hidden" name="id" value="' . $row['id'] . '">
						<input type="submit" name="delete" value="Usuń">
						
					</form>';
			echo '<br />';
		}
		echo '<a href="admin.php?addnew=true">Dodaj Nową Podstronę</a><br />';
	}

	function EdytujPodstrone($link, $id) {
		$query = "SELECT * FROM page_list WHERE id = $id";
		$result = mysqli_query($link, $query);
		$row = mysqli_fetch_assoc($result);

		if ($row) {
			$form = '<form action="admin.php" method="post">
						<input type="hidden" name="id" value="'.$row['id'].'">
						Tytuł: <input type="text" name="page_title" value="'.$row['page_title'].'"><br>
						Treść: <textarea name="page_content">'.$row['page_content'].'</textarea><br>
						Aktywna: <input type="checkbox" name="status" '.($row['status'] ? 'checked' : '').'><br>
						<input type="submit" name="update" value="Aktualizuj">
					 </form>';
			echo $form;
		} else {
			echo 'Podstrona nie znaleziona.';
		}
	}


	function DodajNowaPodstrone() {
		$form = '<form action="admin.php" method="post">
					Tytuł: <input type="text" name="page_title"><br>
					Treść: <textarea name="page_content"></textarea><br>
					Aktywna: <input type="checkbox" name="status"><br>
					<input type="submit" name="insert" value="Dodaj">
				 </form>';
		echo $form;
	}


	function UsunPodstrone($link, $id) {
		$query = "DELETE FROM page_list WHERE id = $id";
		if (mysqli_query($link, $query)) {
			echo 'Podstrona została usunięta.';
		} else {
			echo 'Błąd przy usuwaniu podstrony.';
		}
	}
	ListaPodstron($link);
	
	//Obsluga edycji strony
	if (isset($_GET['edit'])) {
		EdytujPodstrone($link, $_GET['edit']);
	}
	//Obsługa aktualizacji strony
	if (isset($_POST['update'])) {
		$id = $_POST['id'];
		$page_title = $_POST['page_title'];
		$page_content = $_POST['page_content'];
		$status = isset($_POST['status']) ? 1 : 0;

		$query = "UPDATE page_list SET page_title = '$page_title', page_content = '$page_content', status = $status WHERE id = $id";
		if (mysqli_query($link, $query)) {
			echo 'Podstrona została zaktualizowana.';
		} else {
			echo 'Błąd przy aktualizacji podstrony.';
		}
	}
	//Obsługa dodawania
	if (isset($_POST['insert'])) {
		$page_title = $_POST['page_title'];
		$page_content = $_POST['page_content'];
		$status = isset($_POST['status']) ? 1 : 0;

		$query = "INSERT INTO page_list (page_title, page_content, status) VALUES ('$page_title', '$page_content', $status)";
		if (mysqli_query($link, $query)) {
			echo 'Nowa podstrona została dodana.';
		} else {
			echo 'Błąd przy dodawaniu nowej podstrony.';
		}
	}
	//Obsługa usuwania
	if (isset($_POST['delete'])) {
		$id = $_POST['id'];
		UsunPodstrone($link,$_POST['id']);

		$query = "DELETE FROM page_list WHERE id = $id";
		if (mysqli_query($link, $query)) {
			echo 'Podstrona została usunięta.';
		} else {
			echo 'Błąd przy usuwaniu podstrony.';
		}
		
	}
	if (isset($_GET['addnew'])) {
		DodajNowaPodstrone();
	}
	echo '<a href="admin.php?logout=true" class="logout-button">Wyloguj się</a>';

// Logika wylogownaia się z admina
	if (isset($_GET['logout'])) {
		session_destroy();
		header("Location: admin.php"); // Przekierowanie do strony logowania
	}
	function DodajKategorie($link, $nazwa, $matka = 0) {
		$query = "INSERT INTO shop (nazwa, matka) VALUES ('$nazwa', $matka)";
		mysqli_query($link, $query);
	}

	function UsunKategorie($link, $id) {
		$query = "DELETE FROM shop WHERE id = $id";
		mysqli_query($link, $query);
	}

	function EdytujKategorie($link, $id, $nazwa, $matka) {
		$query = "UPDATE shop SET nazwa = '$nazwa', matka = $matka WHERE id = $id";
		mysqli_query($link, $query);
	}

	function PokazKategorie($link) {
		$query = "SELECT * FROM shop WHERE matka = 0"; 
		$result = mysqli_query($link, $query);

		echo "<table border='1'>"; 
		echo "<tr>"; 
		echo "<th>ID</th>";
		echo "<th>Nazwa Kategorii</th>";
		echo "<th>Matka</th>";
		echo "<th>Akcje</th>";
		echo "</tr>";

		while ($row = mysqli_fetch_assoc($result)) {
			echo "<tr>";
			echo "<td>" . $row['id'] . "</td>";
			echo "<td>" . $row['nazwa'] . "</td>";
			echo "<td>" . $row['matka'] . "</td>";

			
			$subQuery = "SELECT * FROM shop WHERE matka = " . $row['id'];
			$subResult = mysqli_query($link, $subQuery);
			
			while ($subRow = mysqli_fetch_assoc($subResult)) {
				echo $subRow['nazwa'] . "<br>";
			}
			echo "</td>";

			
			echo "<td>";
			echo '<a href="admin.php?editKategoria=' . $row['id'] . '">Edytuj</a> | ';
			echo '<form method="post" action="admin.php" style="display:inline;">
					<input type="hidden" name="id" value="' . $row['id'] . '">
					<input type="hidden" name="action" value="usunKategorie">
					<input type="submit" value="Usuń">
				  </form>';
			echo "</td>";
			echo "</tr>";
		}

		echo "</table>"; 
	}
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'zaktualizujKategorie') {
		$idKategoriiDoZaktualizowania = $_POST['id'];
		$nazwa = $_POST['nazwa'];
		$matka = $_POST['matka'];

		
		$query = "UPDATE shop SET nazwa = '$nazwa', matka = $matka WHERE id = $idKategoriiDoZaktualizowania";
		mysqli_query($link, $query);

		header("Location: admin.php");
		exit();
	}
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'usunKategorie') {
		$idKategoriiDoUsuniecia = $_POST['id'];
		$query = "DELETE FROM shop WHERE id = $idKategoriiDoUsuniecia";
		mysqli_query($link, $query);

		header("Location: admin.php");
		exit();
	}


	

	if (isset($_POST['dodajKategorie'])) {
		DodajKategorie($link, $_POST['nazwa'], $_POST['matka']);
	} elseif (isset($_POST['edytujKategorie'])) {
		EdytujKategorie($link, $_POST['id'], $_POST['nazwa'], $_POST['matka']);
	} elseif (isset($_POST['usunKategorie'])) {
		UsunKategorie($link, $_POST['id']);
	}

	
	echo '
	<form action="admin.php" method="post">
		Nazwa kategorii: <input type="text" name="nazwa"><br>
		Kategoria matka (0 dla kategorii głównej): <input type="number" name="matka" value="0"><br>
		<input type="submit" name="dodajKategorie" value="Dodaj Kategorię">
	</form>
	';

	PokazKategorie($link);
	if (isset($_GET['editKategoria'])) {
		$idKategoriiDoEdycji = $_GET['editKategoria'];
		
		
		$query = "SELECT * FROM shop WHERE id = $idKategoriiDoEdycji";
		$result = mysqli_query($link, $query);
		$kategoria = mysqli_fetch_assoc($result);

		if ($kategoria) {
			
			echo '<form action="admin.php" method="post">
					<input type="hidden" name="action" value="zaktualizujKategorie">
					<input type="hidden" name="id" value="' . $kategoria['id'] . '">
					Nazwa kategorii: <input type="text" name="nazwa" value="' . $kategoria['nazwa'] . '"><br>
					Kategoria matka (0 dla kategorii głównej): <input type="number" name="matka" value="' . $kategoria['matka'] . '"><br>
					<input type="submit" value="Zaktualizuj Kategorię">
				  </form>';
		} else {
			echo 'Nie znaleziono kategorii.';
		}
	}
	
	function PokazProdukty($link) {
		$query = "SELECT * FROM produkty";
		$result = mysqli_query($link, $query);

		echo '<h2>Lista produktów</h2>';
		echo '<table border="1">';
		echo '<tr>';
		echo '<th>ID</th>';
		echo '<th>Tytuł</th>';
		echo '<th>Opis</th>';
		echo '<th>Data Utworzenia</th>';
		echo '<th>Cena Netto</th>';
		echo '<th>Podatek VAT</th>';
		echo '<th>Ilość Sztuk</th>';
		echo '<th>Status Dostępności</th>';
		echo '<th>Kategoria</th>';
		echo '<th>Gabaryt</th>';
		echo '<th>Zdjęcie</th>';
		echo '<th>Akcje</th>';
		echo '</tr>';

		while ($row = mysqli_fetch_assoc($result)) {
			echo '<tr>';
			echo '<td>' . $row['id'] . '</td>';
			echo '<td>' . $row['tytul'] . '</td>';
			echo '<td>' . $row['opis'] . '</td>';
			echo '<td>' . $row['data_utworzenia'] . '</td>';
			echo '<td>' . $row['cena_netto'] . '</td>';
			echo '<td>' . $row['podatek_vat'] . '</td>';
			echo '<td>' . $row['ilosc_sztuk'] . '</td>';
			echo '<td>' . $row['status_dostepnosci'] . '</td>';
			echo '<td>' . $row['kategoria'] . '</td>';
			echo '<td>' . $row['gabaryt'] . '</td>';
			echo '<td>' . $row['zdjecie'] . '</td>';
			echo '<td><a href="admin.php?edit_product=' . $row['id'] . '">Edytuj</a> |';
			echo '<form method="post" action="admin.php" style="display:inline;">
				<input type="hidden" name="id" value="'.$row['id'].'">
				<input type="hidden" name="action" value="usunProdukt">
				<input type="submit" value="Usuń">
			</form>';
			echo"</td>";
			echo '</tr>';
		}

		echo '</table>';
	}
	
	function DodajProdukt($link, $tytul, $opis, $data_utworzenia, $cena_netto, $podatek_vat, $ilosc_sztuk, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie)
	{
		$query = "INSERT INTO produkty (tytul, opis, data_utworzenia, cena_netto, podatek_vat, ilosc_sztuk, status_dostepnosci, kategoria, gabaryt, zdjecie) VALUES ('$tytul', '$opis', '$data_utworzenia', $cena_netto, $podatek_vat, $ilosc_sztuk, '$status_dostepnosci', $kategoria, '$gabaryt', '$zdjecie')";
		mysqli_query($link, $query);
	}
	
	function EdytujProdukt($link, $id, $tytul, $opis, $data_utworzenia, $cena_netto, $podatek_vat, $ilosc_sztuk, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie) {
    $query = "UPDATE produkty SET tytul = '$tytul', opis = '$opis', data_utworzenia = '$data_utworzenia', cena_netto = $cena_netto, podatek_vat = $podatek_vat, ilosc_sztuk = $ilosc_sztuk, status_dostepnosci = '$status_dostepnosci', kategoria = $kategoria, gabaryt = '$gabaryt', zdjecie = '$zdjecie' WHERE id = $id";
    mysqli_query($link, $query);
}
	function UsunProdukt($link, $id) {
		$query = "DELETE FROM produkty WHERE id = $id";
		mysqli_query($link, $query);
	}
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'zaktualizujProdukt') {
		$idProduktuDoZaktualizowania = $_POST['id'];
		$tytul = $_POST['tytul'];
		$opis = $_POST['opis'];
		$data_utworzenia = $_POST['data_utworzenia'];
		$cena_netto = $_POST['cena_netto'];
		$podatek_vat = $_POST['podatek_vat'];
		$ilosc_sztuk = $_POST['ilosc_sztuk'];
		$status_dostepnosci = $_POST['status_dostepnosci'];
		$kategoria = $_POST['kategoria'];
		$gabaryt = $_POST['gabaryt'];
		$zdjecie = $_POST['zdjecie'];
		

		
		$query = "UPDATE produkty SET tytul = '$tytul', opis = '$opis', data_utworzenia = '$data_utworzenia', cena_netto = $cena_netto, podatek_vat = $podatek_vat, ilosc_sztuk = $ilosc_sztuk, status_dostepnosci = '$status_dostepnosci', kategoria = $kategoria, gabaryt = '$gabaryt', zdjecie = '$zdjecie' WHERE id = $idProduktuDoZaktualizowania";
		mysqli_query($link, $query);
		exit();
	}
	if (isset($_POST['edytujProdukt'])) {
		EdytujProdukt($link, $_POST['id'], $_POST['tytul'], $_POST['opis'], $_POST['data_utworzenia'], $_POST['cena_netto'], $_POST['podatek_vat'], $_POST['ilosc_sztuk'], $_POST['status_dostepnosci'], $_POST['kategoria'], $_POST['gabaryt'], $_POST['zdjecie']);
	}
	
	echo '
		<form action="admin.php" method="post">
			Nazwa produktu: <input type="text" name="tytul"><br>
			Opis: <input type=textarea name="opis" </textarea><br>
			Data utworzenia: <input type="datetime-local" name="data_utworzenia"><br>
			Cena netto: <input type="number" step="0.01" name="cena_netto"><br>
			Podatek VAT: <input type="number" step="0.01" name="podatek_vat"><br>
			Ilość sztuk: <input type="number" name="ilosc_sztuk"><br>
			Status dostępności: <input type="text" name="status_dostepnosci"><br>
			Kategoria: <input type="number" name="kategoria"><br>
			Gabaryt: <input type="text" name="gabaryt"><br>
			Zdjęcie: <input type="text" name="zdjecie"><br>
			<input type="submit" name="dodajProdukt" value="Dodaj produkt">
		</form>
		';

	PokazProdukty($link);
	if (isset($_GET['edit_product'])) {
    $idProduktuDoEdycji = $_GET['edit_product'];
    

    $query = "SELECT * FROM produkty WHERE id = $idProduktuDoEdycji";
    $result = mysqli_query($link, $query);
    $produkt = mysqli_fetch_assoc($result);

    if ($produkt) {

        echo '<form action="admin.php" method="post">
                <input type="hidden" name="action" value="zaktualizujProdukt">
                <input type="hidden" name="id" value="' . $produkt['id'] . '">
                Tytuł: <input type="text" name="tytul" value="' . $produkt['tytul'] . '"><br>
                Opis: <textarea name="opis">' . $produkt['opis'] . '</textarea><br>
                Data utworzenia: <input type="datetime-local" name="data_utworzenia" value="' . $produkt['data_utworzenia'] . '"><br>
                Cena netto: <input type="number" step="0.01" name="cena_netto" value="' . $produkt['cena_netto'] . '"><br>
                Podatek VAT: <input type="number" step="0.01" name="podatek_vat" value="' . $produkt['podatek_vat'] . '"><br>
                Ilość sztuk: <input type="number" name="ilosc_sztuk" value="' . $produkt['ilosc_sztuk'] . '"><br>
                Status dostępności: <input type="text" name="status_dostepnosci" value="' . $produkt['status_dostepnosci'] . '"><br>
                Kategoria: <input type="number" name="kategoria" value="' . $produkt['kategoria'] . '"><br>
                Gabaryt: <input type="text" name="gabaryt" value="' . $produkt['gabaryt'] . '"><br>
                Zdjęcie: <input type="text" name="zdjecie" value="' . $produkt['zdjecie'] . '"><br>
                <input type="submit" value="Zaktualizuj Produkt">
              </form>';
    } else {
        echo 'Nie znaleziono produktu.';
    }
}
	
	if (isset($_POST['dodajProdukt'])) {
		DodajProdukt($link, $_POST['tytul'], $_POST['opis'],$_POST['data_utworzenia'],$_POST['cena_netto'], $_POST['podatek_vat'],$_POST['ilosc_sztuk'], $_POST['status_dostepnosci'], $_POST['kategoria'], $_POST['gabaryt'], $_POST['zdjecie']);
	}
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'usunProdukt') {
		$idProduktuDoUsuniecia = $_POST['id'];
		UsunProdukt($link, $idProduktuDoUsuniecia);
		exit();
	}
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edytujProdukt') {
		$id = $_POST['id'];
		$tytul = $_POST['tytul'];
		$opis = $_POST['opis'];
		$data_utworzenia = $_POST['data_utworzenia'];
		$cena_netto = $_POST['cena_netto'];
		$podatek_vat = $_POST['podatek_vat'];
		$ilosc_sztuk = $_POST['ilosc_sztuk'];
		$status_dostepnosci = $_POST['status_dostepnosci'];
		$kategoria = $_POST['kategoria'];
		$gabaryt = $_POST['gabaryt'];
		$zdjecie = $_POST['zdjecie'];

		EdytujProdukt($link, $id, $tytul, $opis, $data_utworzenia, $cena_netto, $podatek_vat, $ilosc_sztuk, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie);

		header("Location: admin.php");
		exit();
	}
	?>
	
</div>
</body>

</html>