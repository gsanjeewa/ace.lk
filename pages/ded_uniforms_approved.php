<?php

//process.php
include 'config.php';
/*$connect = new PDO("mysql:host=localhost; dbname=testing", "root", "");*/
$connect = pdoConnection();
 	$statement = $connect->prepare("SELECT id FROM inventory_deduction WHERE status=2");
  	$statement->execute();
  	$result = $statement->fetchAll();
  	foreach($result as $uniforms_id):    
    
      $uniforms_id=$uniforms_id['id'];
  	
		$data = array(
	  		':id'  	=> $uniforms_id,	  		
  			':status'  	=> 0,
	  		
	 	);

	 	$query = "	 	
	 	UPDATE inventory_deduction SET status=:status WHERE id=:id
	 	";

	 	$statement = $connect->prepare($query);

	 	$statement->execute($data);
	endforeach;  
 
 echo 'done';

?>
