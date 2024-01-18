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
	
	?>
	
</div>
</body>

</html>