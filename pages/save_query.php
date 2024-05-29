<?php 
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();

require_once 'system_permissions.php';

$error = false;

if (isset($_POST['add_bank'])){

  if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions to Add Employee.</div>'; 
    header('location:/employee_list/employee');  
    exit();

}

  $employee_id=  2;
  $bank_name  =  $_POST['bank_name'];
  $account_no =  $_POST['account_no'];
  $statement = $connect->prepare("SELECT employee_id, account_no, bank_name FROM bank_details WHERE employee_id=:employee_id AND bank_name=:bank_name AND account_no=:account_no");
  $statement->bindParam(':employee_id', $employee_id);
  $statement->bindParam(':bank_name', $bank_name);
  $statement->bindParam(':account_no', $account_no);

  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>This Bank Details Already existing.</div>';
    header('location:/employee_list/employee');   
  }

  if (!$error) {

    $data = array(
      ':employee_id'        =>  2,
      ':bank_name'          =>  $_POST['bank_name'],
      ':branch_name'        =>  $_POST['bank_branch'],
      ':branch_no'          =>  $_POST['bank_branch_no'],
      ':account_no'         =>  $_POST['account_no'],      
    );
   
    $query = "
    INSERT INTO `bank_details`(`employee_id`, `bank_name`, `branch_name`, `branch_no`, `account_no`)
        VALUES (:employee_id, :bank_name, :branch_name, :branch_no, :account_no)
    ";   
            
    $statement = $connect->prepare($query);

    if($statement->execute($data))
    {
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
      header('location:/employee_list/employee');              
    }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
      header('location:/employee_list/employee');   
    }
  }
}

?>