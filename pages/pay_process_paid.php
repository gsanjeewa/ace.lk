<?php

//process.php
include 'config.php';
/*$connect = new PDO("mysql:host=localhost; dbname=testing", "root", "");*/
$connect = pdoConnection();
if(isset($_POST["payroll_id"]))
{

 	$statement = $connect->prepare("SELECT id FROM payroll_items WHERE payroll_id ='".$_POST["payroll_id"]."' AND status=0");
  	$statement->execute();
  	$result = $statement->fetchAll();
  	foreach($result as $payroll_items_id):    
    
      $payroll_items_id=$payroll_items_id['id'];
  	

		$data = array(
	  		':id'  	=> $payroll_items_id,	  		
  			':status'  	=> 1,
	  		
	 	);

	 	$query = "	 	
	 	UPDATE payroll_items SET status=:status WHERE id=:id
	 	";

	 	$statement = $connect->prepare($query);

	 	$statement->execute($data);
	endforeach;  
 echo 'done';
 
}

?>
