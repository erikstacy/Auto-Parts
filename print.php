<head>
	<title>Auto Parts Warehouse Print</title>
	<!-- Styling for this page -->
	<link rel="stylesheet" href="css/warehouse.css">
</head>
<body>
    <?php
    include('database.php');
        foreach ($_GET as $key => $value) {
            echo "<div>" . $key . " - " . $value . "</div>";

            switch ($key) {
                case "list":
                    break;
                case "invoice":
                    break;
                case "label":
                    break;
                case "shipping":
                    break;
            }
        }
    ?>
</body>