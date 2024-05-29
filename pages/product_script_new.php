<?php
include('config.php');

// Fetching state data
$connect = pdoConnection();

if ($_POST['request'] == 2) {
    $output = 0; // Initialize output variable
    
    // Ensure that product parameter is set and not empty
    if (isset($_POST["product"]) && !empty($_POST["product"])) {
        $query = "SELECT SUM(qty) AS total_qty FROM inventory_stock WHERE status = 1 AND product_id = :product_id";

        // Prepare the query
        $statement = $connect->prepare($query);

        // Bind product ID parameter
        $statement->bindParam(':product_id', $_POST["product"], PDO::PARAM_STR);

        // If size parameter is set and not empty, add it to the query
        if (isset($_POST["size"]) && !empty($_POST["size"]) && $_POST["size"] != 'size') {
            $query .= " AND size = :size";
            $statement->bindParam(':size', $_POST["size"], PDO::PARAM_STR);
        }

        // If color parameter is set and not empty, add it to the query
        if (isset($_POST["color"]) && !empty($_POST["color"]) && $_POST["color"] != 'color') {
            $query .= " AND color = :color";
            $statement->bindParam(':color', $_POST["color"], PDO::PARAM_STR);
        }

        // If gender parameter is set and not empty, add it to the query
        if (isset($_POST["gender"]) && !empty($_POST["gender"]) && $_POST["gender"] != 'gender') {
            $query .= " AND gender = :gender";
            $statement->bindParam(':gender', $_POST["gender"], PDO::PARAM_STR);
        }

        // Execute the query
        $statement->execute();

        // Fetch the result
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // Extract the total quantity
        if ($result && isset($result['total_qty'])) {
            $output = (int)$result['total_qty'];
        }
    }

    // Output the result as JSON
    echo json_encode($output);
}
?>
