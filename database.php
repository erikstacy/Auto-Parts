
<?php

	if (!function_exists('queryLegacyDatabase')) {
		function queryLegacyDatabase($sql) {
			try {
				$dsn = "mysql:host=er7lx9km02rjyf3n.cbetxkdyhwsb.us-east-1.rds.amazonaws.com;dbname=b25oudnru9u3blk4";
				$pdo = new PDO($dsn, "rs0czd6o8w8e8r3j", "w1ffboir25orrcs4");

				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				//$sql = "SELECT * FROM parts";

				$rows = $pdo->query($sql);
				return $rows;

			} catch(PDOexception $e) {
				echo "<div>Connection to legacy database failed: ".$e->getMessage();
			}
		}
	}

	if (!function_exists('queryOurDatabase')) {
		function queryOurDatabase($sql) {
			try {
				$servername = "localhost";
				$username = "root";
				$password = "softwareengineering";
				$dbname = "newdb";
				// Create connection
				$conn = new mysqli($servername, $username, $password, $dbname);

				$result = $conn->query($sql);
				return $result;

			} catch(PDOexception $e) {
				echo "<div>Connection to our database failed: ".$e->getMessage();
			}
		}
	}

	if (!function_exists('insertOurDatabase')) {
		function insertOurDatabase($sql) {
			try {
				$servername = "localhost";
				$username = "root";
				$password = "softwareengineering";
				$dbname = "newdb";
				// Create connection
				$conn = new mysqli($servername, $username, $password, $dbname);

				if ($conn->query($sql)) {
					return $last_id = mysqli_insert_id($conn);
				}

			} catch(PDOexception $e) {
				echo "<div>Connection to our database failed: ".$e->getMessage();
			}
		}
	}

	if (!function_exists('displayOrderRow')) {
		function displayOrderRow() {

			$legacyResult = queryLegacyDatabase("SELECT * FROM parts");

			foreach($legacyResult as $x) {
				$prodid = $x[0];
				$ourResult = queryOurDatabase("SELECT * FROM inventory WHERE ProdId=$prodid");
				$ourRow = $ourResult->fetch_assoc();

				echo "<div class=\"item\">";
					echo "<div class=\"item-section\">";
						echo "<img src=\"" . $x[4] . "\" />";
					echo "</div>";
					echo "<div class=\"item-section\">";
						echo "<p>" . $x[1] . "</p>";
					echo "</div>";
					echo "<div class=\"item-section\">";
						echo "<p>" . $x[2] . "</p>";
						echo "<p>" . "Quantity Available: " . $ourRow["QuantityAvail"] . "</p>";
					echo "</div>";
					echo "<div class=\"add-item\">";
						echo "<label>Quantitiy:</label>";
						echo "<input type=\"text\" placeholder=\"0\" size=\"1\" name=\"quantity" . $ourRow["ProdId"] . "\">";
					echo "</div>";
				echo "</div>";
			}
		}
	}

	if (!function_exists('insertOrder')) {
		function insertOrder($CustName, $CustAddress, $CustEmail, $ProdQuantities) {

			$OrderId = insertOurDatabase("INSERT INTO orders (
				CustName, 
				CustAddress,
				CustEmail,
				Status
				) VALUES (
					\"$CustName\",
					\"$CustAddress\",
					\"$CustEmail\",
					\"Authorized\"
				)
			");

			for ($i = 0; $i < count($ProdQuantities); $i++) {
				insertOurDatabase("INSERT INTO orderprod (
					OrderId,
					ProdId,
					Quantity,
					ProdPrice,
					ProdWeight,
					ProdName
					) VALUES (
						\"$OrderId\",
						\"" . $ProdQuantities[$i][0] . "\",
						\"" . $ProdQuantities[$i][1] . "\",
						\"" . $ProdQuantities[$i][2] . "\",
						\"" . $ProdQuantities[$i][3] . "\",
						\"" . $ProdQuantities[$i][4] . "\"
					)
				");
			}
		}
	}

	if (!function_exists('displayWarehouseRow')) {
		function displayWarehouseRow() {

			$ourResult = queryOurDatabase("SELECT * FROM orders WHERE Status=\"Authorized\"");
			$ourRow = $ourResult->fetch_all();

			foreach($ourRow as $x) {
				echo "<div class=\"row\">";
					echo "<p>" . $x[0] . "</p>";
					echo "<div class=\"row-button\"><button type=\"submit\"><a href=\"./print.php?list=$x[0]\">Print List</a></button></div>";
					echo "<div class=\"row-button\"><button type=\"submit\"><a href=\"./print.php?invoice=$x[0]\">Print Invoice</a></button></div>";
					echo "<div class=\"row-button\"><button type=\"submit\"><a href=\"./print.php?label=$x[0]\">Print Label</a></button></div>";
					echo "<div class=\"row-button\"><button type=\"submit\"><a href=\"./print.php?shipping=$x[0]\">Shipping Confirmation</a></button></div>";
				echo "</div>";
			}
		}
	}

?>
