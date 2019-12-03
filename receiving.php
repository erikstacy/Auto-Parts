<head>
	<title>Auto Parts Receiving</title>
	<!-- Styling for pis page -->
	<link rel="stylesheet" href="css/receiving.css">
</head>
<body>
	<header>
		<h1>Auto Parts Receiving</h1>
	</header>
	<div class="form-container">
		<form method="GET" name="descriptionSubmit">
			<div>
				<label for="descriptionString">Description</label>
				<input type="text" name="descriptionString">
				<label for="descriptionInventory">Add Inventory: </label>
				<input type="text" name="descriptionInventory">
				<button type="submit">Submit</button>
			</div>
		</form>
		<form method="GET" name="numberSubmit">
			<div>
				<label for="numberString">Part Number: </label>
				<input type="text" name="numberString">
				<label for="numberInventory">Add Inventory: </label>
				<input type="text" name="numberInventory">
				<button type="submit">Submit</button>
			</div>
		</form>
	</div>

	<?php
	include('database.php');
		$descriptionString = "";
		$descriptionInventory = "";
		$numberString = "";
		$numberInventory = "";
        foreach ($_GET as $key => $value) {
			if ($key == "descriptionString") {
				$descriptionString = $value;
			}
			if ($key == "descriptionInventory") {
				$descriptionInventory = $value;
			}
			if ($key == "numberString") {
				$numberString = $value;
			}
			if ($key == "numberInventory") {
				$numberInventory = $value;
			}
		}

		if ($descriptionString != "" and $descriptionInventory != "") {
			$legacyResult = queryLegacyDatabase("SELECT * FROM parts WHERE description LIKE \"" . $descriptionString . "\"");
			foreach ($legacyResult as $x) {
				$ourResult = queryOurDatabase("SELECT * FROM inventory WHERE ProdId=\"" . $x[0] . "\"");
				$ourRow = $ourResult->fetch_assoc();
				$newQuantity = $ourRow["QuantityAvail"] + $descriptionInventory;

				queryOurDatabase("UPDATE inventory 
					SET QuantityAvail=$newQuantity
					WHERE ProdId=" . $x[0] . ";
				");
			}
		}

		if ($numberString != "" and $numberInventory != "") {
			$ourResult = queryOurDatabase("SELECT * FROM inventory WHERE ProdId=\"" . $numberString . "\"");
			$ourRow = $ourResult->fetch_assoc();
			$newQuantity = $ourRow["QuantityAvail"] + $numberInventory;

			queryOurDatabase("UPDATE inventory 
				SET QuantityAvail=$newQuantity
				WHERE ProdId=" . $numberString . ";
			");
		}

	?>
	
</body>
