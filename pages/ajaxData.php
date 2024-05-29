<?php 
// Include the database config file 
include_once 'config.php'; 
 
if(!empty($_POST["bank_name_id"])){ 
    // Fetch state data based on the specific country 
    $query = "SELECT * FROM bank_branch WHERE bank_name_id = ".$_POST['bank_name_id']." ORDER BY id ASC"; 
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
         
    // Generate HTML of state options list 
    if($result > 0){ 
        echo '<option value="">Select Branch</option>'; 
        foreach($result as $row)
        {
            echo '<option value="'.$row['id'].'">'.$row['branch_name'].' | '.$row['branch_no'].'</option>';    
        }
        
    }else{ 
        echo '<option value="">Model not available</option>'; 
    } 
}
?>