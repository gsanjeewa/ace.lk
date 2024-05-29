<?php

include 'config.php';
$connect = pdoConnection();
//process_data.php
$request = $_POST['request'];   // request
if($request == 1){
	$output = '';
	if((isset($_POST["query_permissions_group"]) && !empty($_POST["query_permissions_group"])) && (isset($_POST["query_role_id"]) && !empty($_POST["query_role_id"])))
	{
		
		$query="
		SELECT permission_id, role_id, ref_id FROM system_permission_to_roles
		WHERE role_id='".$_POST["query_role_id"]."'
		";
        $statement = $connect->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        $checked_arr=array();
        $check_id=array();
        foreach($result as $row_per)
        {
            $checked_arr[]=$row_per['permission_id'];
            $check_id[]=$row_per['ref_id'];                              
        }
    

        $query="SELECT * FROM system_permissions WHERE status=0 AND permission_group='".$_POST["query_permissions_group"]."' ORDER BY permission_group ASC";
        $statement = $connect->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        $per_id=array();
        foreach($result as $row_perm)
        {
            $per_id[]=$row_perm['permission_id'];                 
            $per_name[$row_perm['permission_id']] = $row_perm['permission_name'];
        }

		for ($i=0; $i < count($per_id); $i++){

            if (in_array($per_id[$i], $checked_arr)) {
                
                $output .='<input type="hidden" name="ref_id[]" value="'.$check_id[$i].'">
                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <div class="icheck-primary d-inline">
                            <input type="checkbox" id="permissions'.$per_id[$i].'" name="permissions[]" value="'.$per_id[$i].'" checked>
                            <label for="permissions'.$per_id[$i].'">'.$per_name[$per_id[$i]].'
                            </label>
                        </div>                           
                    </div>
                </div>';              
              
            }else{
                
                $output .='<input type="hidden" name="ref_id[]">
                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <div class="icheck-primary d-inline">
                            <input type="checkbox" id="permissions'.$per_id[$i].'" name="permissions[]" value="'.$per_id[$i].'">
                            <label for="permissions'.$per_id[$i].'">'.$per_name[$per_id[$i]].'
                            </label>
                        </div>                           
                    </div>
                </div>';
                
            }            
        }
		echo $output;
	}
	
}


?>