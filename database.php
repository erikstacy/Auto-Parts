
<?php

if (!function_exists('getOurDatabase')) {
	function getOurDatabase() {
			try {
				$servername = "localhost";
				$username = "root";
				$password = "softwareengineering";
				$dbname = "newdb";
				// Create connection
				$conn = new mysqli($servername, $username, $password, $dbname);
				return $conn;

			} catch(PDOexception $e) {
				echo "<div>Connection to our database failed: ".$e->getMessage();
			}
	}
}

	if (!function_exists('queryLegacyDatabase')) {
		function queryLegacyDatabase($sql) {
			try {
				$dsn = "mysql:host=er7lx9km02rjyf3n.cbetxkdyhwsb.us-east-1.rds.amazonaws.com;dbname=b25oudnru9u3blk4";
				$pdo = new PDO($dsn, "rs0czd6o8w8e8r3j", "w1ffboir25orrcs4");

				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
				$conn = getOurDatabase();

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
				$conn = getOurDatabase();

				if ($conn->query($sql)) {
					return $last_id = mysqli_insert_id($conn);
				}

			} catch(PDOexception $e) {
				echo "<div>Connection to our database failed: ".$e->getMessage();
			}
		}
	}

	if (!function_exists('modifyOurDatabase')) {
		function modifyOurDatabase($sql) {
			try {
				$conn = getOurDatabase();

				if ($conn->query($sql)) {
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
						echo "<p>$" . $x[2] . "</p>";
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

			/* Insert Custom Dates
			$date = "2012-03-03";
			$date = date("Y-m-d", strtotime($date));
			*/
			$OrderId = insertOurDatabase("INSERT INTO orders (
				CustName, 
				CustAddress,
				CustEmail,
				Status,
				Date
				) VALUES (
					\"$CustName\",
					\"$CustAddress\",
					\"$CustEmail\",
					\"Authorized\",
					\"" . date("Y/m/d") . "\"
				)
			");

			$totalWeight = 0;
			$subPrice = 0;

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

				$ourResult = queryOurDatabase("SELECT * FROM inventory WHERE ProdId=\"" . $ProdQuantities[$i][0] . "\"");
				$ourRow = $ourResult->fetch_assoc();
				$newQuantity = $ourRow["QuantityAvail"] - $ProdQuantities[$i][1];

				queryOurDatabase("UPDATE inventory
					SET QuantityAvail=$newQuantity
					WHERE ProdId=" . $ourRow["ProdId"] . ";
				");

				$ourResult = queryOurDatabase("SELECT * FROM orderprod WHERE OrderId=\"$OrderId\" AND ProdId=\"" . $ProdQuantities[$i][0] . "\"");
				$ourRow = $ourResult->fetch_assoc();

				$prodPrice = $ourRow["ProdPrice"];
				$prodWeight = $ourRow["ProdWeight"];
				$quantity = $ourRow["Quantity"];

				$tempPrice = $prodPrice * $quantity;
				$tempWeight = $prodWeight * $quantity;

				$totalWeight += $tempWeight;
				$subPrice += $tempPrice;
			}

			$shipping = getShippingPrice($totalWeight);
			$total = $subPrice + $shipping;
			$updateTotal = queryOurDataBase("UPDATE orders
				SET TotalPrice=$total
				WHERE OrderId=$OrderId;
			");
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

	if (!function_exists('getShippingPrice')) {
		function getShippingPrice($totalWeight) {
			$ourResult = queryOurDatabase("SELECT * FROM shipping");
			$ourRow = $ourResult->fetch_all();

			foreach ($ourRow as $x) {
				if ($x[1] >= $totalWeight) {
					return $x[1];
				}
			}
			return $x[1];
		}
	}

	if (!function_exists('displayAdminRow')) {
		function displayAdminRow($sql) {
			$ourResult = queryOurDatabase($sql);
			$ourRow = $ourResult->fetch_all();

			foreach ($ourRow as $x) {
				echo "<div class=\"order\">";
					echo "<p>" . $x[0] . "</p>";
					echo "<p>" . $x[4] . "</p>";
					echo "<p>" . $x[5] . "</p>";
					echo "<p>$" . $x[6] . "</p>";
					echo "<div class=\"order-button\"><button><a href=\"./print.php?details=$x[0]\">Details</a></button></div>";
				echo "</div>";
			}
		}
	}

?>
