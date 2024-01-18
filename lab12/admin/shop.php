<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Sklep</title>
    <link rel="stylesheet" href="../css/style_shop.css">
</head>
<body>
<div class="shop-container">
	<?php
	session_start();

	include('../cfg.php');

	class ShoppingCart
	{
		private $dbLink;

		public function __construct($link)
		{
			$this->dbLink = $link;
			if (!isset($_SESSION['cart'])) {
				$_SESSION['cart'] = [];
			}
		}

		public function addToCart($productId, $quantity)
		{
			if(isset($_SESSION['cart'][$productId])) {
				$_SESSION['cart'][$productId]['quantity'] += $quantity;
			} else {
				$_SESSION['cart'][$productId] = ['quantity' => $quantity];
			}
		}

		public function removeFromCart($productId)
		{
			if(isset($_SESSION['cart'][$productId])) {
				unset($_SESSION['cart'][$productId]);
			}
		}

		public function updateQuantity($productId, $quantity)
		{
			if(isset($_SESSION['cart'][$productId])) {
				$_SESSION['cart'][$productId]['quantity'] = $quantity;
			}
		}

		 public function showCart()
		{
			$total = 0;
			echo "<h2>Koszyk</h2>";
			echo "<table border='1'><tr><th>ID Produktu</th><th>Ilość</th><th> Nazwa</th><th>Cena Netto</th><th>Podatek VAT</th><th>Cena Brutto (za sztukę)</th><th>Łączna Cena Brutto</th><th>Akcje</th></tr>";
			foreach ($_SESSION['cart'] as $productId => $product) {
				$query = "SELECT tytul, cena_netto, podatek_vat FROM produkty WHERE id = $productId";
				$result = mysqli_query($this->dbLink, $query);
				if ($row = mysqli_fetch_assoc($result)) {
					$cenaNetto = $row['cena_netto'];
					$nazwa = $row['tytul'];
					$podatekVat = $row['podatek_vat'];
					$cenaBrutto = $cenaNetto + ($cenaNetto * $podatekVat);
					$lacznaCenaBrutto = $cenaBrutto * $product['quantity'];
					$total += $lacznaCenaBrutto;

					echo "<tr>
							<td>$productId</td>
							<td>
								<form method='post'>
									<input type='hidden' name='product_id' value='$productId'>
									<input type='number' name='quantity' value='{$product['quantity']}' min='1'>
									<input type='submit' name='update_cart' value='Aktualizuj'>
								</form>
							</td>
							<td>$nazwa</td>
							<td>$cenaNetto</td>
							<td>$podatekVat</td>
							<td>$cenaBrutto</td>
							<td>$lacznaCenaBrutto</td>
							<td>
								<form method='post'>
									<input type='hidden' name='product_id' value='$productId'>
									<input type='submit' name='remove_from_cart' value='Usuń'>
								</form>
							</td>
						  </tr>";
				}
			}
			echo "<tr><td colspan='6'>Suma:</td><td>$total</td></tr>";
			echo "</table>";
		}



		public function calculateTotal()
		{
			$total = 0;
			foreach ($_SESSION['cart'] as $productId => $product) {
				$query = "SELECT cena_netto, podatek_vat FROM produkty WHERE id = $productId";
				$result = mysqli_query($this->dbLink, $query);
				if ($row = mysqli_fetch_assoc($result)) {
					$total += ($row['cena_netto'] + ($row['cena_netto'] * $row['podatek_vat'])) * $product['quantity'];
				}
			}
			return $total;
		}
	}

	$cart = new ShoppingCart($link);
	// Obsługa żądania dodania produktu do koszyka
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (isset($_POST['update_cart'])) {
			$productId = $_POST['product_id'];
			$quantity = $_POST['quantity'];
			$cart->updateQuantity($productId, $quantity);
		} elseif (isset($_POST['remove_from_cart'])) {
			$productId = $_POST['product_id'];
			$cart->removeFromCart($productId);
		}
		elseif	(isset($_POST['add_to_cart'])) {
			// Dodanie produktu do koszyka
			$productId = $_POST['product_id'];
			$quantity = $_POST['quantity'];
			$cart->addToCart($productId, $quantity);}
	}

	// Wyświetlanie produktów
	$query = "SELECT * FROM produkty";
	$result = mysqli_query($link, $query);
	echo "<h2>Produkty</h2>";
	echo "<table border='1'><tr><th>ID</th><th>Tytuł</th><th>Opis</th><th>Cena Netto</th><th>Podatek VAT</th><th>Ilość sztuk</th><th> Zdjecie</th><th>Akcje</th></tr>";
	while ($row = mysqli_fetch_assoc($result)) {
		echo "<tr>";
		echo "<td>{$row['id']}</td>";
		echo "<td>{$row['tytul']}</td>";
		echo "<td>{$row['opis']}</td>";
		echo "<td>{$row['cena_netto']}</td>";
		echo "<td>{$row['podatek_vat']}</td>";
		echo "<td>{$row['ilosc_sztuk']}</td>";
		echo "<td>{$row['zdjecie']}</td>";
		echo "<td>
				<form method='post'>
					<input type='hidden' name='product_id' value='{$row['id']}'>
					<input type='number' name='quantity' min='1' value='1'>
					<input type='submit' name='add_to_cart' value='Dodaj do koszyka'>
				</form>
			  </td>";
		echo "</tr>";
	}
	echo "</table>";

	// Wyświetlanie koszyka
	$cart->showCart();
	?>
</div>
</body>
</html>
