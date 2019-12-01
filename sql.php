
<?php
function displayRow($rows) {

	foreach($rows as $x) {
		echo "<div class=\"item\">";
			echo "<div class=\"item-section\">";
				echo "<img src=\"" . $x[4] . "\" />";
			echo "</div>";
			echo "<div class=\"item-section\">";
				echo "<p>" . $x[1] . "</p>";
			echo "</div>";
			echo "<div class=\"item-section\">";
				echo "<p>" . $x[2] . "</p>";
				echo "<p>" . "Quantity Availble: 10" . "</p>";
			echo "</div>";
			echo "<div class=\"add-item\">";
				echo "<form>";
					echo "<label>Quantitiy:</label>";
					echo "<input type=\"text\" placeholder=\"0\" size=\"1\">";
				echo "</form>";
			echo "</div>";
		echo "</div>";
	}
}

?>
