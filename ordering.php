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
				<input type="text" name="cc_number" placeholder="Type here...">
			</div>
			<div class="input-field">
				<label for="cc_expiration">CC Expiration Date</label>
				<input type="text" name="cc_expiration" placeholder="Type here...">
			</div>
			<button type="submit">Submit</button>
		</div>

		<?php
		include('database.php');


			if (!empty($_POST["name"]) and 
				!empty($_POST["address"]) and
				!empty($_POST["email"])
				) {
				$name = $_POST["name"];
				$address = $_POST["address"];
				$email = $_POST["email"];
				$prodQuantities = array();

				$arrayCount = 0;
				for ($x = 1; $x <= 149; $x++) {
					if (!empty($_POST["quantity" . $x])) {
						$legacyResult = queryLegacyDatabase("SELECT price, weight, description FROM parts WHERE number=$x");
						$legacyRow = $legacyResult->fetch();

						$prodQuantities[$arrayCount][0] = $x;
						$prodQuantities[$arrayCount][1] = $_POST["quantity" . $x];
						$prodQuantities[$arrayCount][2] = $legacyRow[0];
						$prodQuantities[$arrayCount][3] = $legacyRow[1];
						$prodQuantities[$arrayCount][4] = $legacyRow[2];
						$arrayCount++;
					}
				}

				insertOrder($name, $address, $email, $prodQuantities);
			}

		?>

		<?php
		include('database.php');
			displayOrderRow();
		?>


	</form>
</body>

