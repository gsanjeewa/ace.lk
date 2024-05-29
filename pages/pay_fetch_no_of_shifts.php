<?php

include "config.php";
$connect = pdoConnection();
$payid = $_POST['payid'];

$query = 'SELECT * FROM payroll_items WHERE id="'.$payid.'"';

$statement = $connect->prepare($query);
$statement->execute();
$total_data = $statement->rowCount();

$result = $statement->fetchAll();
$html = '<div><ul class="list-group">';
foreach($result as $row)
{	
	$query = 'SELECT * FROM department';

    $statement = $connect->prepare($query);
    $statement->execute();
    $total_data = $statement->rowCount();
    $result = $statement->fetchAll();
    foreach($result as $rows):
        $all_arr[$rows['department_id']] = $rows['department_name'];
    endforeach;
	
	$query = 'SELECT * FROM position';

		            $statement = $connect->prepare($query);
		            $statement->execute();
		            $total_data = $statement->rowCount();
		            $result = $statement->fetchAll();
		            foreach($result as $rows_p):
		                $all_arr2[$rows_p['position_id']] = $rows_p['position_abbreviation'];
		            endforeach;
    
    foreach(json_decode($row['department']) as $k => $val){
    	$html .= "<li class='d-flex justify-content-between align-items-center'>".ucwords($all_arr[$val->d_id])."<span class='badge badge-primary badge-pill'></span><span>(".$all_arr2[$val->p_id]."-".$val->t_shifts.")</span></li>";    	
    }
}

$html .= '</ul></div>';

echo $html;