<head>
	<title>Auto Parts</title>
	<!-- Styling for this page -->
	<link rel="stylesheet" href="css/ordering.css">
</head>
<body>
	<header>
		<h1>Auto Parts</h1>
	</header>

	<form method="POST" name="submitOrder">
		<div class="customer-info">
			<div class="input-field">
				<label for="name">Name</label>
				<input type="text" name="name" placeholder="Type here...">
			</div>
			<div class="input-field">
				<label for="email">Email</label>
				<input type="text" name="email" placeholder="Type here...">
			</div>
			<div class="input-field">
				<label for="address">Address</label>
				<input type="text" name="address" placeholder="Type here...">
			</div>
			<div class="input-field">
				<label for="cc_number">CC Number</label>
				<input type="text" name="cc_number" placeholder="XXXX XXXX XXXX XXXX">
			</div>
			<div class="input-field">
				<label for="cc_expiration">CC Expiration Date</label>
				<input type="text" name="cc_expiration" placeholder="mm/YYYY">
			</div>
			<button type="submit">Submit</button>
		</div>

		<?php
		include('database.php');


			if (!empty($_POST["name"]) and 
				!empty($_POST["address"]) and
				!empty($_POST["email"]) and
				!empty($_POST["cc_number"]) and
				!empty($_POST["cc_expiration"])
				) {
				$name = $_POST["name"];
				$address = $_POST["address"];
				$email = $_POST["email"];
				$cc_number = $_POST["cc_number"];
				$cc_expiration = $_POST["cc_expiration"];
				$prodQuantities = array();

				$arrayCount = 0;
				$didBuySomething = false;
				$totalWeight = 0;
				$subPrice = 0;

				for ($x = 1; $x <= 149; $x++) {
					if (!empty($_POST["quantity" . $x])) {
						$didBuySomething = true;
						$legacyResult = queryLegacyDatabase("SELECT price, weight, description FROM parts WHERE number=$x");
						$legacyRow = $legacyResult->fetch();

						$prodQuantities[$arrayCount][0] = $x;
						$prodQuantities[$arrayCount][1] = $_POST["quantity" . $x];
						$prodQuantities[$arrayCount][2] = $legacyRow[0];
						$prodQuantities[$arrayCount][3] = $legacyRow[1];
						$prodQuantities[$arrayCount][4] = $legacyRow[2];

						$prodPrice = $prodQuantities[$arrayCount][2];
						$prodWeight = $prodQuantities[$arrayCount][3];
						$quantity = $prodQuantities[$arrayCount][1];

						$tempPrice = $prodPrice * $quantity;
						$tempWeight = $prodWeight * $quantity;

						$totalWeight += $tempWeight;
						$subPrice += $tempPrice;
						$arrayCount++;
					}
				}

				$shipping = getShippingPrice($totalWeight);
				$total = $subPrice + $shipping;

				$ourResult = queryOurDatabase("SELECT * FROM orders");
				$orderNumber = $ourResult->num_rows;
				$orderNumber += 1;
				$orderNumber = str_pad($orderNumber, 9, '0', STR_PAD_LEFT);

				// Get credit card authorization
				$price = $total;
				$vendor = 'gunslinger-joe';
				$trans = '666-' . $orderNumber . '-666';
				$url = "http://blitz.cs.niu.edu/CreditCard/";
				$data = array(
					'vendor' => $vendor,
					'trans' => $trans,
					'cc' => $cc_number,
					'name' => $name,
					'exp' => $cc_expiration,
					'amount' => $price
				);
				$options = array(
					'http' => array(
						'header' => array('Content-type: application/json', 'Accept: application/json'),
						'method' => 'POST',
						'content' => json_encode($data)
					)
				);
				$context = stream_context_create($options);
				$result = file_get_contents($url, false, $context);
				$auth = 'authorization';
				$pos = strpos($result, $auth);
				if ($pos === false) {
					echo "<div width=\"100%\">Not Authorized!</div>";
				}
				
				if ($didBuySomething == true && $pos == true) {
					insertOrder($name, $address, $email, $prodQuantities);
				}
			}
		?>

		<?php
		include('database.php');
			displayOrderRow();
		?>


	</form>
</body>

