<?php

//process.php
include 'config.php';
/*$connect = new PDO("mysql:host=localhost; dbname=testing", "root", "");*/
$connect = pdoConnection();

$today=date("Y-m-d");
$employee_id=array();
$basic_salary=array();
$id=array();
/*$query = 'SELECT (a.basic_salary+c.rate) AS total, a.join_id, a.increment_date, a.id FROM salary a INNER JOIN employee b ON a.employee_id=b.employee_id INNER JOIN increment_rate c ON b.position_id = c.position_id INNER JOIN (SELECT position_id, MAX(id) maxid FROM increment_rate GROUP BY position_id) d ON c.position_id = d.position_id AND c.id = d.maxid WHERE a.increment_date = (CURDATE() - INTERVAL 1 YEAR) AND a.status=0';*/

/*$query = 'SELECT (a.basic_salary+c.rate) AS total, a.employee_id, a.increment_date, a.id FROM salary a INNER JOIN (SELECT j.join_id join_id, e.position_id position_id FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) f ON j.employee_id = f.employee_id AND j.join_id = f.maxid WHERE j.employee_status=0 OR j.employee_status=2) b ON a.employee_id=b.join_id INNER JOIN increment_rate c ON b.position_id = c.position_id INNER JOIN (SELECT position_id, MAX(id) maxid FROM increment_rate GROUP BY position_id) d ON c.position_id = d.position_id AND c.id = d.maxid WHERE MONTH(a.increment_date) = MONTH(CURDATE()) AND YEAR(a.increment_date) = YEAR(CURDATE() - INTERVAL 1 YEAR) AND a.status=0';
*/
$query = 'SELECT (a.basic_salary+c.rate) AS total, a.employee_id, a.increment_date, a.id FROM salary a INNER JOIN promotions j ON a.employee_id = j.employee_id INNER JOIN (SELECT position_id, MAX(id) maxid_p FROM promotions GROUP BY employee_id) b ON j.position_id=b.position_id AND j.id=b.maxid_p INNER JOIN increment_rate c ON b.position_id = c.position_id INNER JOIN (SELECT position_id, MAX(id) maxid FROM increment_rate GROUP BY position_id) d ON c.position_id = d.position_id AND c.id = d.maxid WHERE MONTH(a.increment_date) = MONTH(CURDATE()) AND YEAR(a.increment_date) = YEAR(CURDATE() - INTERVAL 1 YEAR) AND a.status=0';

$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row):

$employee_id[] =  $row['employee_id'];
$basic_salary[] = $row['total'];
$increment_date=date('Y-m-d', strtotime('+1 year', strtotime($row['increment_date'])));
$id[]=$row['id'];
endforeach;

for ($l = 0; $l < count($employee_id); $l++) {

$data = array(
		':employee_id'  	=> $employee_id[$l],
		':basic_salary'  	=> $basic_salary[$l],
		':increment_date'  	=> $increment_date,  				  		
	);

	$query = "
	INSERT INTO `salary`(`employee_id`, `basic_salary`, `increment_date`) 
	VALUES (:employee_id, :basic_salary, :increment_date)    
	";

$statement = $connect->prepare($query);

$statement->execute($data);

}

for ($k = 0; $k < count($id); $k++) {  

      $data_attendance = array(
        ':id'     =>  $id[$k],
        ':status' =>  1,
      );

      $query_attendance = "
      UPDATE `salary` SET `status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query_attendance);
      $statement->execute($data_attendance);
    }

    
?>
