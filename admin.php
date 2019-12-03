<head>
	<title>Auto Parts Admin</title>
	<!-- Styling for pis page -->
	<link rel="stylesheet" href="css/admin.css">
</head>
<body>
	<header>
		<h1>Auto Parts Admin</h1>
	</header>
	<div class="brackets-container">
		<form method="GET" name="updateWeight">
			<?php
			include("database.php");

				if (!empty($_GET) AND array_key_exists("weight1", $_GET) == true) {
					$sql = "SELECT * from orders";
					$ourResult = queryOurDatabase("SELECT * FROM shipping");
					$ourRow = $ourResult->fetch_all();
					$rows = $ourResult->num_rows;
					$count = 0;
					foreach ($ourRow as $x) {
						$currentInput = $count + 1;
						if ($x[1] != $_GET["weight" . $currentInput]) {
							insertOurDatabase("UPDATE shipping SET weight=\"" . $_GET["weight" . $currentInput] . "\" WHERE ShipId=\"$currentInput\"");
						}
						if ($x[2] != $_GET["price" . $currentInput]) {
							insertOurDatabase("UPDATE shipping SET price=\"" . $_GET["price" . $currentInput] . "\" WHERE ShipId=\"$currentInput\"");
						}
						$count++;
					}
				}

				// Create paramaters
				$weightInfo = array();

				$ourResult = queryOurDatabase("SELECT * FROM shipping");
				$ourRow = $ourResult->fetch_all();
				$rows = $ourResult->num_rows;
				$count = 0;
				foreach ($ourRow as $x) {
					$weightInfo[$count][0] = $x[1];
					$weightInfo[$count][1] = $x[2];
					$count++;
				}
			?>
			<p>Weight Brackets</p>
			<div class="weights">
				<div class="row">
					<p>Weight</p>
					<p>Price</p>
				</div>
				<div class="row">
					<input name="weight1" type="number" step="0.01" value="<?php echo $weightInfo[0][0]; ?>">
					<input name="price1" type="number" step="0.01" value="<?php echo $weightInfo[0][1]; ?>">
				</div>
				<div class="row">
					<input name="weight2" type="number" step="0.01" value="<?php echo $weightInfo[1][0]; ?>">
					<input name="price2" type="number" step="0.01" value="<?php echo $weightInfo[1][1]; ?>">
				</div>
				<div class="row">
					<input name="weight3" type="number" step="0.01" value="<?php echo $weightInfo[2][0]; ?>">
					<input name="price3" type="number" step="0.01" value="<?php echo $weightInfo[2][1]; ?>">
				</div>
			</div>
			<button type="submit">Save</button>
		</form>
	</div>
	<div class="line"></div>
	<form method="GET" name="getOrders">
		<div class="orders-container">
			<p>Orders</p>
			<div class="order-search">
				<div class="input-field">
					<label for="start-date">Start Date</label>
					<input type="date" name="start-date">
					<label for="end-date">End Date</label>
					<input type="date" name="end-date">
				</div>
				<div class="input-field">
					<label for="status">Status</label>
					<select name="status">
						<option value="none">None</option>
						<option value="authorized">Authorized</option>
						<option value="shipped">Shipped</option>
					</select>
				</div>
				<div class="input-field">
					<label for="start-price">Price Range</label>
					<input type="number" name="start-price" step="0.01">
					<label for="end-price">End Date</label>
					<input type="number" name="end-price" step="0.01">
				</div>
				<button type="submit">Search</button>
			</div>
			<div class="orders-list">
				<div class="order">
					<p>Order Number</p>
					<p>Date</p>
					<p>Status</p>
					<p>Total Price</p>
					<p>View Details</p>
				</div>
				<?php
				include("database.php");

					if (empty($_GET)) {
						$sql = "SELECT * from orders";
					} elseif (array_key_exists("status", $_GET) == false) {
						$sql = "SELECT * from orders";
					} else {
						$changed = 0;
						$sql = "SELECT * FROM orders WHERE ";

						if ($_GET["status"] == "authorized") {
							if ($changed != 0) {
								$sql .= " AND ";
							}
							$changed++;
							$sql .= "Status=\"Authorized\" ";
						}
						if ($_GET["status"] == "shipped") {
							if ($changed != 0) {
								$sql .= " AND ";
							}
							$changed++;
							$sql .= "Status=\"Shipped\" ";
						}
						if ($_GET["status"] == "none") {
							if ($changed != 0) {
								$sql .= " AND ";
							}
							$changed++;
							$sql .= "(Status=\"Shipped\" OR Status=\"Authorized\") ";
						}
						if ($_GET["start-price"] != "" AND $_GET["end-price"] != "") {
							if ($changed != 0) {
								$sql .= " AND ";
							}
							$changed++;
							$sql .= "TotalPrice BETWEEN " . $_GET["start-price"] . " AND " . $_GET["end-price"] . " ";
						}
						if ($_GET["start-date"] != "" AND $_GET["end-date"] != "") {
							$startDate = $_GET["start-date"];
							$endDate = $_GET["end-date"];
							$startDate = date("Y-m-d", strtotime($startDate));
							$endDate = date("Y-m-d", strtotime($endDate));
							if ($changed != 0) {
								$sql .= " AND ";
							}
							$changed++;
							//$sql .= "Date BETWEEN " . $startDate . " AND " . $endDate . " ";
							$sql .= "Date>=\"" . $startDate . "\" AND Date<=\"" . $endDate . "\" ";
						}

					}

					displayAdminRow($sql);
				?>
			</div>
		</div>
	</form>
</body>
