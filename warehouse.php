<head>
	<title>Auto Parts Warehouse</title>
	<!-- Styling for this page -->
	<link rel="stylesheet" href="css/warehouse.css">
</head>
<body>
	<header>
		<h1>Auto Parts Warehouse</h1>
	</header>

	<div class="orders-container">
		<p>Orders ready for Shipping</p>
		<div class="row">
			<p>Order Number</p>
			<p>Packing List</p>
			<p>Invoice</p>
			<p>Shipping Label</p>
			<p>Completed Shipping</p>
		</div>

		<?php
		include('database.php');
			displayWarehouseRow();
		?>
	</div>

</body>
