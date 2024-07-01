<?php
// Start session and include configuration
session_start();
include 'config.php';
$connect = pdoConnection();
function fetchEmployeeDetails($connect) {
    $query = "SELECT e.employee_id, e.surname, e.initial, j.employee_no, e.nic_no, e.permanent_address, e.mobile_no, j.employee_status, p.position_abbreviation, j.join_id, j.join_date, j.location 
              FROM employee e 
              INNER JOIN join_status j ON e.employee_id = j.employee_id 
              INNER JOIN promotions c ON j.employee_id = c.employee_id
              INNER JOIN position p ON c.position_id = p.position_id 
              WHERE j.join_id IN (SELECT MAX(join_id) FROM join_status GROUP BY employee_id)
              AND c.id IN (SELECT MAX(id) FROM promotions GROUP BY employee_id)
              ORDER BY ABS(j.employee_no) DESC";
    $statement = $connect->prepare($query);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC); // Use associative fetch for JSON output
}

// Function to fetch bank details of an employee
function fetchBankDetails($connect, $employee_id) {
    $query = "SELECT a.account_no, b.bank_name, b.bank_no, c.branch_name, c.branch_no 
              FROM bank_details a 
              INNER JOIN bank_name b ON a.bank_name = b.id 
              INNER JOIN bank_branch c ON a.branch_name = c.id 
              WHERE a.employee_id = :employee_id";
    $statement = $connect->prepare($query);
    $statement->bindParam(':employee_id', $employee_id);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC); // Use associative fetch for JSON output
}

// Function to fetch basic salary based on join_id
function fetchBasicSalary($connect, $join_id) {
    $query = "SELECT basic_salary 
              FROM salary 
              WHERE join_id = :join_id";
    $statement = $connect->prepare($query);
    $statement->bindParam(':join_id', $join_id);
    $statement->execute();
    return $statement->fetchColumn();
}

// Function to fetch department location based on location ID
function fetchDepartmentLocation($connect, $location) {
    $query = "SELECT department_name, department_location 
              FROM department 
              WHERE department_id = :location";
    $statement = $connect->prepare($query);
    $statement->bindParam(':location', $location);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC); // Use associative fetch for JSON output
}

// Establish PDO connection
$connect = pdoConnection();

// Fetch all employees details
$employees = fetchEmployeeDetails($connect);

// Array to store formatted data for JSON output
$data = [];

// Loop through each employee and format data
foreach ($employees as $employee) {
    $bankDetails = fetchBankDetails($connect, $employee['employee_id']);
    $basicSalary = fetchBasicSalary($connect, $employee['join_id']);
    $departmentLocation = fetchDepartmentLocation($connect, $employee['location']);

    $joinDate = new DateTime($employee['join_date']);
    $currentDate = new DateTime();
    $interval = $currentDate->diff($joinDate);

    // Format data for JSON output
    $data[] = [
        'employee_name' => $employee['employee_no'] . ' ' . $employee['position_abbreviation'] . ' ' . $employee['surname'] . ' ' . $employee['initial'],
        'nic_no' => $employee['nic_no'],
        'join_date' => $employee['join_date'],
        'interval' => $interval->format('%yY %mM %dD'),
        'basic_salary' => number_format($basicSalary),
        'location' => $departmentLocation['department_name'] . '-' . $departmentLocation['department_location'],
        'bank_details' => [
            'bank_name' => $bankDetails['bank_name'] . ' (' . $bankDetails['bank_no'] . ')',
            'branch_name' => $bankDetails['branch_name'] . ' (' . str_pad($bankDetails['branch_no'], 3, "0", STR_PAD_LEFT) . ')',
            'account_no' => str_pad($bankDetails['account_no'], 12, "0", STR_PAD_LEFT)
        ],
        'permanent_address' => $employee['permanent_address'],
        'mobile_no' => $employee['mobile_no'],
        'employee_status' => $employee['employee_status'],
        'employee_id' => $employee['employee_id'],
        'join_id' => $employee['join_id']
    ];
}

// Set response content type to JSON
header('Content-Type: application/json');

// Output JSON data
echo json_encode($data);
?>
