<head>
	<title>Auto Parts Warehouse Print</title>
	<!-- Styling for this page -->
	<link rel="stylesheet" href="css/warehouse.css">
</head>
<body>
    <?php
    include('database.php');
        foreach ($_GET as $key => $value) {
            switch ($key) {
                case "list":
                    $ourResult = queryOurDatabase("SELECT * FROM orderprod WHERE OrderId=$value");
                    $ourRow = $ourResult->fetch_all();
                    echo "<h1>Packing List</h1>";
                    foreach($ourRow as $x) {
                        echo "<div>Product ID: " . $x[2] . "</div>";
                        echo "<div>Product Name: " . $x[3] . "</div>";
                        echo "<div>Quantity: " . $x[6] . "</div>";
                        echo "<br>";
                    }
                    break;
                case "invoice":
                    $orderResult = queryOurDatabase("SELECT * FROM orders WHERE OrderId=$value");
                    $orderRow = $orderResult->fetch_assoc();
                    $ourResult = queryOurDatabase("SELECT * FROM orderprod WHERE OrderId=$value");
                    $ourRow = $ourResult->fetch_all();
                    echo "<h1>Invoice</h1>";
                    echo "<div>Name: <b>" . $orderRow["CustName"] . "</b></div>";
                    echo "<div>Address: <b>" . $orderRow["CustAddress"] . "</b></div>";
                    echo "<div>Email: <b>" . $orderRow["CustEmail"] . "</b></div>";
                    echo "<hr>";
                    echo "<div class=\"item-row\">";
                        echo "<div class=\"item-detail\"><b>Item Name</b></div>";
                        echo "<div class=\"item-detail\"><b>Item Price</b></div>";
                        echo "<div class=\"item-detail\"><b>Quantity</b></div>";
                        echo "<div class=\"item-detail\"><b>Total Item Price</b></div>";
                    echo "</div>";
                    $i = 1;
                    $subtotal = 0;
                    $totalWeight = 0;
                    foreach($ourRow as $x) {
                        $itemTotal = $x[4] * $x[6];
                        $subtotal += $itemTotal;
                        $totalWeight += ($x[5] * $x[6]);
                        echo "<div class=\"item-row\">";
                            echo "<div class=\"item-detail\">$x[3]</div>";
                            echo "<div class=\"item-detail\">\$$x[4]</div>";
                            echo "<div class=\"item-detail\">$x[6]</div>";
                            echo "<div class=\"item-detail\">\$$itemTotal</div>";
                        echo "</div>";
                        $i++;
                    }
                    echo "<hr>";
                    $outputSub = number_format((float)$subtotal, 2, '.', '');
                    echo "<div class=\"item-row\">";
                        echo "<div class=\"item-detail\"></div>";
                        echo "<div class=\"item-detail\"></div>";
                        echo "<div class=\"item-detail\"><b>Subtotal</b></div>";
                        echo "<div class=\"item-detail\">\$$outputSub</div>";
                    echo "</div>";
                    $shippingPrice = getShippingPrice($totalWeight);
                    echo "<div class=\"item-row\">";
                        echo "<div class=\"item-detail\"></div>";
                        echo "<div class=\"item-detail\"></div>";
                        echo "<div class=\"item-detail\"><b>Shipping and Handling</b></div>";
                        echo "<div class=\"item-detail\">\$$shippingPrice</div>";
                    echo "</div>";
                    $totalPrice = $subtotal + $shippingPrice;
                    $outputTotal = number_format((float)$totalPrice, 2, '.', '');
                    echo "<div class=\"item-row\">";
                        echo "<div class=\"item-detail\"></div>";
                        echo "<div class=\"item-detail\"></div>";
                        echo "<div class=\"item-detail\"><b>Total</b></div>";
                        echo "<div class=\"item-detail\">\$$outputTotal</div>";
                    echo "</div>";
                    break;
                case "label":
                    $orderResult = queryOurDatabase("SELECT * FROM orders WHERE OrderId=$value");
                    $orderRow = $orderResult->fetch_assoc();
                    echo "<div><b>SHIP TO:</b></div>";
                    echo "<div>" . $orderRow["CustName"] . "</div>";
                    echo "<div>" . $orderRow["CustAddress"] . "</div>";
                    echo "<div>" . $orderRow["CustEmail"] . "</div>";
                    break;
                case "shipping":
                    modifyOurDatabase("UPDATE orders SET Status=\"Shipped\" WHERE OrderId=$value");
                    echo "<div>Shipped to customer.</div>";
                    echo "<div><button><a href=\"./warehouse.php\">Back</a></button></div>";
                    break;
                case "details":
                    $orderResult = queryOurDatabase("SELECT * FROM orders WHERE OrderId=$value");
                    $orderRow = $orderResult->fetch_assoc();
                    echo "<div><b>Order Information</b></div>";
                    echo "<br>";
                    echo "<div>" . "Order Id: " . $orderRow["OrderId"] . "</div>";
                    echo "<div>" . "Customer Name: " . $orderRow["CustName"] . "</div>";
                    echo "<div>" . "Customer Address: " . $orderRow["CustAddress"] . "</div>";
                    echo "<div>" . "Customer Email: " . $orderRow["CustEmail"] . "</div>";
                    echo "<div>" . "Date: " . $orderRow["Date"] . "</div>";
                    echo "<div>" . "Status: " . $orderRow["Status"] . "</div>";
                    echo "<div>" . "Total Price: $" . $orderRow["TotalPrice"] . "</div>";
                    echo "<hr>";
                    $orderResult = queryOurDatabase("SELECT * FROM orderprod WHERE OrderId=$value");
                    $orderRow = $orderResult->fetch_all();
                    $productCount = 1;
                    echo "<div><b>Product Information</b></div>";
                    echo "<br>";
                    foreach ($orderRow as $x) {
                        echo "<div>Product $productCount</div>";
                        echo "<div>" . "Product Id: " . $x[2] . "</div>";
                        echo "<div>" . "Product Name: " . $x[3] . "</div>";
                        echo "<div>" . "Product Price: $" . $x[4] . "</div>";
                        echo "<div>" . "Product Weight: " . $x[5] . "</div>";
                        echo "<div>" . "Quantity Purchased: " . $x[6] . "</div>";
                        echo "<br>";
                    }
                    break;
            }
        }
    ?>
</body>