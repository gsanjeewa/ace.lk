<?php

include 'config.php';

$connect = pdoConnection();
$today = date("Y-m-d");

$employee_ids = [];
$basic_salaries = [];
$increment_dates = [];
$ids = [];

// Query to fetch the required data
$query = '
    SELECT 
        (a.basic_salary + c.rate) AS total, 
        a.employee_id, 
        a.increment_date, 
        a.id 
    FROM salary a 
    INNER JOIN promotions j ON a.employee_id = j.employee_id 
    INNER JOIN (
        SELECT employee_id, MAX(id) AS maxid_p 
        FROM promotions 
        GROUP BY employee_id
    ) b ON j.employee_id = b.employee_id AND j.id = b.maxid_p 
    INNER JOIN increment_rate c ON j.position_id = c.position_id 
    INNER JOIN (
        SELECT position_id, MAX(id) AS maxid 
        FROM increment_rate 
        GROUP BY position_id
    ) d ON c.position_id = d.position_id AND c.id = d.maxid 
    WHERE 
        MONTH(a.increment_date) <= MONTH(CURDATE()) 
        AND YEAR(a.increment_date) <= YEAR(CURDATE() - INTERVAL 1 YEAR) 
        AND a.status = 0
';

$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();

foreach ($result as $row) {
    $employee_ids[] = $row['employee_id'];
    $basic_salaries[] = $row['total'];
    $increment_dates[] = date('Y-m-d', strtotime('+1 year', strtotime($row['increment_date'])));
    $ids[] = $row['id'];
}

// Insert new salary records
$insertQuery = "
    INSERT INTO salary (employee_id, basic_salary, increment_date) 
    VALUES (:employee_id, :basic_salary, :increment_date)
";
$insertStatement = $connect->prepare($insertQuery);

for ($i = 0; $i < count($employee_ids); $i++) {
    $data = [
        ':employee_id' => $employee_ids[$i],
        ':basic_salary' => $basic_salaries[$i],
        ':increment_date' => $increment_dates[$i]
    ];
    try {
        $insertStatement->execute($data);
    } catch (Exception $e) {
        echo 'Error inserting data: ' . $e->getMessage();
    }
}

// Update the status of processed salary records
$updateQuery = "
    UPDATE salary 
    SET status = :status 
    WHERE id = :id
";
$updateStatement = $connect->prepare($updateQuery);

for ($i = 0; $i < count($ids); $i++) {
    $data = [
        ':id' => $ids[$i],
        ':status' => 1
    ];
    try {
        $updateStatement->execute($data);
    } catch (Exception $e) {
        echo 'Error updating data: ' . $e->getMessage();
    }
}

?>
