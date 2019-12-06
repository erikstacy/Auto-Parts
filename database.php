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
			$count = 1;
			foreach($legacyResult as $x) {
				$prodid = $x[0];
				$ourResult = queryOurDatabase("SELECT * FROM inventory WHERE ProdId=$prodid");
				$ourRow = $ourResult->fetch_assoc();
				if (($count % 2) == 0) {
					echo "<div class=\"item\" id=\"grey\">";
				} else {
					echo "<div class=\"item\" id=\"orange\">";
				}
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
				$count++;
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
	
			$orderResult = queryOurDatabase("SELECT * FROM orders WHERE OrderId=$OrderId");
			$orderRow = $orderResult->fetch_assoc();
			$ourResult = queryOurDatabase("SELECT * FROM orderprod WHERE OrderId=$OrderId");
			$ourRow = $ourResult->fetch_all();
			$emailBody = emailBody($orderRow, $ourRow);
			require '/usr/share/php/libphp-phpmailer/class.phpmailer.php';
			require '/usr/share/php/libphp-phpmailer/class.smtp.php';
			$mail = new PHPMailer;
			$mail->setFrom('admin@example.com');
			$mail->addAddress($CustEmail);
			$mail->Subject = 'Auto Parts Order Confirmation';
			$mail->isHtml(true);
			$mail->Body = $emailBody;
			$mail->IsSMTP();
			$mail->SMTPSecure = 'ssl';
			$mail->Host = 'ssl://smtp.gmail.com';
			$mail->SMTPAuth = true;
			$mail->Port = 465;
			//Set your existing gmail address as user name
			$mail->Username = 'orderingGroup9A@gmail.com';
			//Set the password of your gmail address here
			$mail->Password = 'cs467pass';
			if(!$mail->send()) {
			echo 'Email is not sent.';
			echo 'Email error: ' . $mail->ErrorInfo;
			} else {
			echo 'Email has been sent.';
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
			$count = 1;
			foreach ($ourRow as $x) {
				if (($count % 2) == 0) {
					echo "<div class=\"order\" id=\"orange\">";
				} else {
					echo "<div class=\"order\" id=\"grey\">";
				}
					echo "<p>" . $x[0] . "</p>";
					echo "<p>" . $x[4] . "</p>";
					echo "<p>" . $x[5] . "</p>";
					echo "<p>$" . $x[6] . "</p>";
					echo "<div class=\"order-button\"><button><a href=\"./print.php?details=$x[0]\">Details</a></button></div>";
				echo "</div>";
				$count++;
			}
		}
	}
	if (!function_exists('emailBody')) {
		function emailBody($orderRow, $ourRow) {
			$body = "<h1>Invoice</h1>";
			$body .= "<div>Name: <b>" . $orderRow["CustName"] . "</b></div>";
			$body .= "<div>Address: <b>" . $orderRow["CustAddress"] . "</b></div>";
			$body .= "<div>Email: <b>" . $orderRow["CustEmail"] . "</b></div>";
			$body .= "<hr>";
			$i = 1;
			$subtotal = 0;
			$totalWeight = 0;
			foreach($ourRow as $x) {
				$itemTotal = $x[4] * $x[6];
				$subtotal += $itemTotal;
				$totalWeight += ($x[5] * $x[6]);
				$body .= "<div class=\"item-row\">";
				$body .= "<div class=\"item-detail\"><b>Item Name</b></div>";
					$body .= "<div class=\"item-detail\">$x[3]</div>";
				$body .= "<div class=\"item-detail\"><b>Item Price</b></div>";
					$body .= "<div class=\"item-detail\">\$$x[4]</div>";
				$body .= "<div class=\"item-detail\"><b>Quantity</b></div>";
					$body .= "<div class=\"item-detail\">$x[6]</div>";
				$body .= "<div class=\"item-detail\"><b>Total Item Price</b></div>";
					$body .= "<div class=\"item-detail\">\$$itemTotal</div>";
					$body .= "<br>";
				$body .= "</div>";
				$i++;
			}
			$body .= "<hr>";
			$outputSub = number_format((float)$subtotal, 2, '.', '');
			$body .= "<div class=\"item-row\">";
				$body .= "<div class=\"item-detail\"></div>";
				$body .= "<div class=\"item-detail\"></div>";
				$body .= "<div class=\"item-detail\"><b>Subtotal</b></div>";
				$body .= "<div class=\"item-detail\">\$$outputSub</div>";
			$body .= "</div>";
			$shippingPrice = getShippingPrice($totalWeight);
			$body .= "<div class=\"item-row\">";
				$body .= "<div class=\"item-detail\"></div>";
				$body .= "<div class=\"item-detail\"></div>";
				$body .= "<div class=\"item-detail\"><b>Shipping and Handling</b></div>";
				$body .= "<div class=\"item-detail\">\$$shippingPrice</div>";
			$body .= "</div>";
			$totalPrice = $subtotal + $shippingPrice;
			$outputTotal = number_format((float)$totalPrice, 2, '.', '');
			$body .= "<div class=\"item-row\">";
				$body .= "<div class=\"item-detail\"></div>";
				$body .= "<div class=\"item-detail\"></div>";
				$body .= "<div class=\"item-detail\"><b>Total</b></div>";
				$body .= "<div class=\"item-detail\">\$$outputTotal</div>";
			$body .= "</div>";
			return $body;
		}
	}
?>