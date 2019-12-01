<head>
	<title>Auto Parts</title>
	<!-- Styling for this page -->
	<link rel="stylesheet" href="css/ordering.css">
</head>
<body>
	<header>
		<h1>Auto Parts</h1>
	</header>

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
		<button>Submit</button>
	</div>
	<form>
	<?php
	include('sql.php');
		try {
			$dsn = "mysql:host=er7lx9km02rjyf3n.cbetxkdyhwsb.us-east-1.rds.amazonaws.com;dbname=b25oudnru9u3blk4";
			$pdo = new PDO($dsn, "rs0czd6o8w8e8r3j", "w1ffboir25orrcs4");

			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOexception $e) {
			echo "<div>Connection to database failed: ".$e->getMessage();
		}

		$sql = "SELECT * FROM parts";

		$rows = $pdo->query($sql);

		displayRow($rows);
	?>
	</form>
</body>

