<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 3) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;

if (isset($_POST['add_new'])){

  if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions to Add Employee.</div>'; 
    header('location:/employee_list/employee');  
    exit();

}

  $employee_id=  $_SESSION['empid'];
  $bank_name  =  $_POST['bank_name'];
  $bank_branch  =  $_POST['bank_branch'];
  $account_no =  $_POST['account_no'];
  $statement = $connect->prepare("SELECT employee_id, account_no, bank_name FROM bank_details WHERE employee_id=:employee_id AND bank_name=:bank_name AND branch_name=:bank_branch AND account_no=:account_no");
  $statement->bindParam(':employee_id', $employee_id);
  $statement->bindParam(':bank_name', $bank_name);
  $statement->bindParam(':bank_branch', $bank_branch);
  $statement->bindParam(':account_no', $account_no);

  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>This Bank Details Already existing.</div>';      
  }

  $bank_account = str_pad($_POST['account_no'], 12, "0", STR_PAD_LEFT);

  if (!$error) {

    $data = array(
      ':employee_id'        =>  $employee_id,
      ':bank_name'          =>  $bank_name,
      ':branch_name'        =>  $bank_branch,
      ':branch_no'          =>  '',
      ':account_no'         =>  $bank_account,      
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
                  
    }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
         
    }
  }
}

if (isset($_POST['add_promote'])){

  if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions to Add Employee.</div>'; 
    header('location:/employee_list/employee/'.$_GET['edit'].'');  
    exit();

}

$query = 'SELECT MAX(join_id) maxid FROM join_status WHERE employee_id="'.$_GET['edit'].'" GROUP BY employee_id';

$statement = $connect->prepare($query);
$statement->execute();
$total_data = $statement->rowCount();
$result = $statement->fetchAll();
if ($total_data > 0) {                  
  foreach($result as $row_join_id)
  {
    $join_id = $row_join_id['maxid'];
  }
}else{
  $join_id = '';
}


  if (!$error) {

    $data = array(
      ':employee_id'        =>  $join_id,
      ':position_id'          =>  $_POST['position_id'],
      ':promoted_date'        =>  $_POST['promoted_date'],
      ':promotion_pay'        =>  $_POST['promotion_pay'],

    );
   
    $query = "
    INSERT INTO `promotions`(`employee_id`, `position_id`, `promoted_date`, `promotion_pay`)
        VALUES (:employee_id, :position_id, :promoted_date, :promotion_pay)
    ";   
            
    $statement = $connect->prepare($query);

    if($statement->execute($data))
    {
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
                  
    }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
         
    }
  }
}

if (isset($_POST['save_enabled'])){

  $data = array(
    ':id'     =>  $_POST['bank_id'],
    ':status' =>  1,    
  );
 
  $query = "
  UPDATE `bank_details` SET `status`=:status WHERE `id`=:id
  ";   
          
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';                
  }else{
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Enabled.</div>';       
  }
}

if (isset($_POST['save_disabled'])){

  $data = array(
    ':id'     =>  $_POST['bank_id'],
    ':status' =>  0,    
  );
 
  $query = "
  UPDATE `bank_details` SET `status`=:status WHERE `id`=:id
  ";   
          
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';                
  }else{
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Enabled.</div>';       
  }
}

if (isset($_POST['update_absent'])){

  $data = array(
    ':id'           =>  $_POST['join_id'],
    ':absent_date'  =>  $_POST['create_date'],
    ':status'       =>  1,    
  );
 
  $query = "
  UPDATE `join_status` SET `absent_date`=:absent_date, `employee_status`=:status WHERE `join_id`=:id
  ";   
          
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';                
  }else{
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Update.</div>';       
  }
}

if (isset($_POST['update_resignation'])){

  $data = array(
    ':id'           =>  $_POST['join_id'],
    ':resignation_date'  =>  $_POST['create_date'],
    ':resignation_reason'  =>  $_POST['resignation_remark'],
    ':status'       =>  3,    
  );
 
  $query = "
  UPDATE `join_status` SET `resignation_date`=:resignation_date, `resignation_reason`=:resignation_reason, `employee_status`=:status WHERE `join_id`=:id
  ";   
          
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';                
  }else{
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Update.</div>';       
  }
}

if (isset($_POST['update_enlisted'])){

  $data = array(
    ':id'               =>  $_POST['join_id'],
    ':re_enlisted_date' =>  $_POST['create_date'],
    ':status'           =>  2,    
  );
 
  $query = "
  UPDATE `join_status` SET `re_enlisted_date`=:re_enlisted_date, `employee_status`=:status WHERE `join_id`=:id
  ";   
          
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';                
  }else{
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Update.</div>';       
  }
}

if (isset($_POST['new_join'])){

  $query = 'SELECT join_id FROM join_status ORDER BY join_id DESC LIMIT 1';

$statement = $connect->prepare($query);
$statement->execute();
$total_data = $statement->rowCount();
$result = $statement->fetchAll();
if ($total_data > 0) {                  
  foreach($result as $row_join_id)
  {
    $join_id = $row_join_id['join_id']+1;
  }
}else{
  $join_id = 1;
}

  $data = array(
    ':join_id'            =>  $join_id,
    ':join_date'          =>  $_POST['create_date'],
    ':employee_id'        =>  $_GET['edit'],
    ':employee_no'        =>  $_POST['employee_no'],
    ':basic_salary'       =>  $_POST['basic_salary'],
    ':position_id'        =>  $_POST['position_id'],     
  );
 
  $query = "
  INSERT INTO `join_status`(`join_id`, `join_date`, `employee_id`, `employee_no`) 
    VALUES (:join_id, :join_date, :employee_id, :employee_no);
    INSERT INTO `salary`(`employee_id`, `basic_salary`, `increment_date`)
    VALUES (:join_id, :basic_salary, :join_date);
    INSERT INTO `promotions`(`employee_id`, `position_id`, `promoted_date`)
     VALUES (:join_id, :position_id, :join_date);
  ";   
          
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';                
  }else{
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Update.</div>';       
  }
}


include '../inc/header.php';

?>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Employee</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Employee</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <?php
          if ( isset($errMSG) ) {
            ?>
            <div class="col-xl-12 col-md-6 mb-4">
              <?php echo $errMSG; ?>
            </div>
              <?php
          }
          if (isset($_SESSION["msg"])) {
          ?>
            <div class="col-xl-12 col-md-6 mb-4">
              <?php
              echo $_SESSION["msg"];
              unset($_SESSION["msg"]);
              ?>
            </div>
          <?php
          }
          ?>
        </div>
        <div class="row"> 
          <div class="col-md-12">

            <?php 
            if(isset($_GET['edit']))
            {
              $query = 'SELECT * FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid WHERE e.employee_id="'.$_GET['edit'].'"';

              $statement = $connect->prepare($query);
              $statement->execute();
              $total_data = $statement->rowCount();
              $result = $statement->fetchAll();
              if ($total_data > 0){   
                foreach($result as $row):
                   
                  if (!empty($row['employee_images'])) {
                    $path='/employee_image/'.$row['employee_images'].'';
                  }else{
                    $path='/dist/img/avatar5.png';
                  }
                  
                  $query = 'SELECT * FROM position a INNER JOIN promotions b ON a.position_id=b.position_id WHERE employee_id="'.$row['join_id'].'"';

                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $total_data = $statement->rowCount();
                  $result = $statement->fetchAll();                     
                    foreach($result as $row_position):

                   endforeach;                  
                 
                      
                  ?>

            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="<?php echo $path; ?>"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?php echo $row['surname'].' '.$row['initial']; ?></h3>

                <button class="btn btn-sm bg-gradient-primary float-right edit_pro" type="button" data-toggle="modal" data-target="#promoteModal"><i class="fas fa-plus"></i> Promote</button>

                <p class="text-muted text-center"><?php echo $row_position['position_abbreviation']; ?></p>
                <div class="row">
                <div class="col-md-6">
                  <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Employee No:</b> <a class="float-right"><?php echo $row['employee_no']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>NIC:</b> <a class="float-right"><?php echo $row['nic_no']; ?></a>
                  </li>                  
                  <li class="list-group-item">
                    <b>Full Name:</b> <a class="float-right"><?php echo $row['full_name']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Permanent Address:</b> <a class="float-right"><?php echo $row['permanent_address']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Temporary Address:</b> <a class="float-right"><?php echo $row['temporary_address']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Contact No:</b> <a class="float-right"><?php echo $row['mobile_no'].', '.$row['home_no']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Old Employee No:</b> 
                    <span class="float-right">
                    <?php 
                    $statement = $connect->prepare('SELECT * FROM join_status WHERE employee_id="'.$row['employee_id'].'" AND employee_no!="'.$row['employee_no'].'" ORDER BY join_id DESC');
                    $statement->execute();
                    $total_data = $statement->rowCount();
                    $result = $statement->fetchAll();                     
                    foreach($result as $row_join):
                      echo '<a href="/employee_list/employee/'.$row['employee_id'].'/'.$row_join['join_id'].'">'.$row_join['employee_no'].'</a>,'; 
                    endforeach;
                    
                    ?>
                    </span>
                  </li>
                  <li class="list-group-item">
                    <b>Birthday:</b> <a class="float-right"><?php echo $row['birthday']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Married Status:</b> 
                    <a class="float-right text-uppercase">
                    <?php 

                    if ($row['married_status']==1) {
                      echo 'Merried';
                    }else{
                      echo 'Single';
                    }
                    ?>
                      
                    </a>
                  </li>

                  <li class="list-group-item">
                    <b>Nationality:</b> <a class="float-right"><?php echo $row['nationality']; ?></a>
                  </li>                  
                  

                </ul>
                </div>
                <div class="col-md-6">
                  <ul class="list-group list-group-unbordered mb-3">
                  
                  <li class="list-group-item">
                    <b>District:</b> <a class="float-right text-uppercase"><?php 
                    $statement = $connect->prepare('SELECT districts FROM districts WHERE dis_id="'.$row['dis_id'].'"');
                    $statement->execute();
                    $total_data = $statement->rowCount();
                    $result = $statement->fetchAll();                     
                    foreach($result as $row_dis):
                      echo $row_dis['districts']; 
                    endforeach;
                    ?></a>
                  </li>
                  
                  <li class="list-group-item">
                    <b>DS Division:</b> <a class="float-right text-uppercase"><?php 
                    $statement = $connect->prepare('SELECT ds FROM ds WHERE ds_id="'.$row['ds_id'].'"');
                    $statement->execute();
                    $total_data = $statement->rowCount();
                    $result = $statement->fetchAll();                     
                    foreach($result as $row_ds):
                      echo $row_ds['ds']; 
                    endforeach;
                     ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>GN Division:</b> <a class="float-right text-uppercase"><?php 
                    $statement = $connect->prepare('SELECT gn FROM gn WHERE gn_id="'.$row['gramasewa'].'"');
                    $statement->execute();
                    $total_data = $statement->rowCount();
                    $result = $statement->fetchAll();                     
                    foreach($result as $row_gn):
                      echo $row_gn['gn']; 
                    endforeach;
                     ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Police:</b> <a class="float-right"><?php 
                    $statement = $connect->prepare('SELECT police FROM police WHERE police_id="'.$row['police'].'"');
                    $statement->execute();
                    $total_data = $statement->rowCount();
                    $result = $statement->fetchAll();                     
                    foreach($result as $row_police):
                      echo $row_police['police']; 
                    endforeach;
                     ?></a>
                  </li>
                  
                  <li class="list-group-item">
                    <b>If any forces or other security service:</b> 
                    <a class="float-right text-uppercase">
                    <?php 
                    if ($row['mobile_no']==1) {
                      echo 'yes';
                    }else{
                      echo 'no';
                    }
                    
                    ?>
                      
                    </a>
                  </li>
                  <li class="list-group-item">
                    <b>EPF funds membership:</b> <a class="float-right text-uppercase"><?php 
                     if ($row['epf']==1) {
                      echo 'yes';
                    }else{
                      echo 'no';
                    }
                  ?></a>
                  </li>

                  <li class="list-group-item">
                    <b>Languages:</b> <a class=""><?php 
                      foreach(json_decode($row['languages']) as $lan => $vallan):
                        if ($lan == 'sw') {
                          $sw = $vallan;  
                        }       
                        if ($lan == 'sr') {
                          $sr = $vallan;
                        }
                        if ($lan == 'ss') {
                          $ss = $vallan;
                        }
                        if ($lan == 'tw') {
                          $tw = $vallan;
                        }       
                        if ($lan == 'tr') {
                          $tr = $vallan;
                        }
                        if ($lan == 'ts') {
                          $ts = $vallan;
                        }
                        if ($lan == 'ew') {
                          $ew = $vallan;
                        }       
                        if ($lan == 'er') {
                          $er = $vallan;
                        }
                        if ($lan == 'es') {
                          $es = $vallan;
                        }
                      endforeach;

                      ?>
                      
                      <div class="row">
                        <div class="col-md-3">
                          
                        </div>
                        <div class="col-md-3">
                          <label>Writing</label>
                        </div>
                        <div class="col-md-3">
                          <label>Reading</label>
                        </div>
                        <div class="col-md-3">
                          <label>Speaking</label>
                        </div>

                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label>Sinhala</label>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-primary d-inline">
                                <input type="checkbox" id="sinhala_writing" name="sinhala_writing" value="1" <?php if($sw==1) echo " checked "?> disabled>
                                <label for="sinhala_writing">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-danger d-inline">
                                <input type="checkbox" id="sinhala_reading" name="sinhala_reading" value="1" <?php if($sr==1) echo " checked "?> disabled>
                                <label for="sinhala_reading">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-success d-inline">
                                <input type="checkbox" id="sinhala_speaking" name="sinhala_speaking" value="1" <?php if($ss==1) echo " checked "?> disabled>
                                <label for="sinhala_speaking">
                                </label>
                              </div>                           
                            </div>
                        </div>

                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label>English</label>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-primary d-inline">
                                <input type="checkbox" id="english_writing" name="english_writing" value="1" <?php if($ew==1) echo " checked "?> disabled>
                                <label for="english_writing">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-danger d-inline">
                                <input type="checkbox" id="english_reading" name="english_reading" value="1" <?php if($er==1) echo " checked "?> disabled>
                                <label for="english_reading">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-success d-inline">
                                <input type="checkbox" id="english_speaking" name="english_speaking" value="1" <?php if($es==1) echo " checked "?> disabled>
                                <label for="english_speaking">
                                </label>
                              </div>                           
                            </div>
                        </div>

                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label>Tamil</label>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-primary d-inline">
                                <input type="checkbox" id="tamil_writing" name="tamil_writing" value="1" <?php if($tw==1) echo " checked "?> disabled>
                                <label for="tamil_writing">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-danger d-inline">
                                <input type="checkbox" id="tamil_reading" name="tamil_reading" value="1" <?php if($tr==1) echo " checked "?> disabled>
                                <label for="tamil_reading">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-success d-inline">
                                <input type="checkbox" id="tamil_speaking" name="tamil_speaking" value="1" <?php if($ts==1) echo " checked "?> disabled>
                                <label for="tamil_speaking">
                                </label>
                              </div>                           
                            </div>
                        </div>
                      </div>  
                    
                     </a>
                  </li>

                </ul>
                </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <form action="" method="POST" id="add_join_form">
                  <input type="hidden" name="join_id" value="<?php echo $row['join_id']; ?>">
                  <div class="form-group">
                        <label for="create_date">Date:<span class="text-danger">*</span></label>
                        <div class="input-group date" id="reservationjoindate" data-target-input="nearest">
                          <input type="text" name="create_date" id="create_date" class="form-control datetimepicker-input" data-target="#reservationjoindate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo date("Y-m-d"); ?>"/>
                          <div class="input-group-append" data-target="#reservationjoindate" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                          </div>
                        </div>
                      </div>
                      <?php
                      if ($row['employee_status']!=3): 
                        ?>
                      <div class="form-group">
                        <label for="resignation_remark">Resignation Remark:</label>
                        <input type="text" class="form-control" id="resignation_remark" name="resignation_remark">
                      </div>

                <?php 
              endif;
                if (($row['employee_status']==0) OR ($row['employee_status']==2)):
                  ?>
                    <button class="btn btn-sm btn-danger col-sm-4" name="update_absent"> <b>Absent</b></button>
                  
                    <button class="btn btn-sm btn-success col-sm-4" name="update_resignation"> <b>Resignation</b></button>
                  
                  <?php                  
                elseif ($row['employee_status']==1):
                  $absent_date=date('Y-m-d', strtotime('+1 month', strtotime($row['absent_date'])));
                  $today=date("Y-m-d");
                  
                  if ($absent_date <= $today):
                    ?>                 
                    <button class="btn btn-sm btn-warning col-sm-4" name="update_enlisted"> <b>Re Enlist</b></button>
                    <button class="btn btn-sm btn-success col-sm-4" name="update_resignation"> <b>Resignation</b></button>
                  <?php
                  else:
                    ?>                  
                    <button class="btn btn-sm btn-warning col-sm-4" name="update_enlisted"> <b>Re Enlist</b></button>

                    <button class="btn btn-sm btn-success col-sm-4" name="update_resignation"> <b>Resignation</b></button>
                  <?php
                  endif;

                 elseif ($row['employee_status']==3): 
                  ?>
                  <?php 
                      $query_no="SELECT employee_no FROM join_status WHERE employee_no REGEXP '^-?[0-9]+$' ORDER BY CAST(employee_no AS int) DESC LIMIT 1";
                      $statement = $connect->prepare($query_no);
                      $statement->execute();
                      $result = $statement->fetchAll();
                      foreach($result as $row_no)
                      {
                        $new_employee_no=$row_no['employee_no']+1;                      
                      }
                      ?>
                      <div class="form-group">
                      <label for="employee_no">Employee No:<span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="employee_no" name="employee_no" value="<?php echo $new_employee_no; ?>">
                    </div>
                    <div class="form-group">
                      <label for="">Rank<span class="text-danger">*</span></label>
                      <select class="form-control select2" style="width: 100%;" name="position_id" id="position_id">
                        <option value="">Select Rank</option>
                        <?php
                        $query="SELECT * FROM position ORDER BY position_id";
                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row_rank)
                        {
                          ?>
                          <option value="<?php echo $row_rank['position_id']; ?>"><?php echo $row_rank['position_abbreviation']; ?></option>
                          <?php
                        }
                        ?>
                      </select>
                    </div>
                    <div class="form-group">
                        <label for="basic_salary">Basic Salary:<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="basic_salary" name="basic_salary">
                      </div>

                    <button class="btn btn-sm btn-primary col-sm-4" name="new_join"> <b>Join New</b></button>
                    
                  <?php
                endif;
                ?>
              </form>
                  </div>
                </div>
                
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <?php endforeach; 
          }
        }?>
            
            
          </div>
          </div>
          <div class="row"> 
            <div class="col-md-6">
          <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">Increment</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php 
                $query = 'SELECT * FROM salary WHERE employee_id="'.$row['join_id'].'" ORDER BY id DESC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();

                $result = $statement->fetchAll();
                ?>

                <table id="example1" class="table table-bordered table-sm table-striped" >
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Salary</th>
                      <th>Increment Date</th>                      
                    </tr>
                  </thead>
                  <tbody>
                    <?php                     
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row_inc)
                      {                        
                    ?>

                    <tr>
                      <td><center><?php echo $sno; ?></center></td>
                      <td style="text-align: right;"><?php echo number_format($row_inc['basic_salary']);?></td>
                      <td><center><?php echo date('Y-m', strtotime($row_inc['increment_date']));?></center></td>
                    </tr>
                    <?php
                        $sno ++;
                      }
                      ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->      
          </div>
          <div class="col-md-6">
            <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">ETF / EPF</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php 
                $query = 'SELECT * FROM payroll_items WHERE employee_id="'.$row['join_id'].'" ORDER BY id ASC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();

                $result = $statement->fetchAll();
                ?>

                <table id="example2" class="table table-bordered table-sm table-striped" >
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Month</th>
                      <th>EPF (8%)</th>
                      <th>EPF (12%)</th>
                      <th>ETF (3%)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php                     
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row_epf)
                      {
                        $payroll_id=$row_epf['payroll_id'];

                        $query = 'SELECT * FROM payroll WHERE id="'.$payroll_id.'" ORDER BY id ASC';

                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row_month)
                        {
                          $month=date('Y F', strtotime($row_month['date_from']));
                        }
                    ?>

                    <tr>
                      <td><center><?php echo $sno; ?></center></td>
                      <td><?php echo $month;?></td>
                      <td style="text-align: right;"><?php if ($row_epf['employee_epf'] >0){ echo number_format($row_epf['employee_epf']);}?></td>
                      <td style="text-align: right;"><?php if ($row_epf['employer_epf'] >0){ echo number_format($row_epf['employer_epf']);}?></td>
                      <td style="text-align: right;"><?php if ($row_epf['employer_etf'] >0) {echo number_format($row_epf['employer_etf']);}?></td>
                    </tr>
                    <?php
                        $sno ++;
                      }
                      ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <div class="col-md-6">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Death Donation</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php 
                $query = 'SELECT * FROM death_donation WHERE employee_id="'.$row['join_id'].'" ORDER BY id DESC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();

                $result = $statement->fetchAll();
                ?>

                <table id="example_death" class="table table-bordered table-sm table-striped" >
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Relation</th>
                      <th>Amount</th>
                      <th>Death Date</th>                      
                    </tr>
                  </thead>
                  <tbody>
                    <?php                     
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row_death)
                      {
                        
                    ?>

                    <tr>
                      <td><center><?php echo $sno; ?></center></td>
                      <td><?php echo $row_death['relation'];?></td>
                      <td style="text-align: right;"><?php echo number_format($row_death['amount']);?></td>
                      <td><center><?php echo $row_death['due_date'];?></center></td>
                    </tr>
                    <?php
                        $sno ++;
                      }
                      ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-6">
            <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">Advance</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php 
                $query = 'SELECT * FROM salary_advance WHERE employee_id="'.$row['join_id'].'" ORDER BY id DESC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();

                $result = $statement->fetchAll();
                ?>

                <table id="example3" class="table table-bordered table-sm table-striped" >
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Amount</th>
                      <th>Date Effective</th>
                      <th>Status</th>                      
                    </tr>
                  </thead>
                  <tbody>
                    <?php                     
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row_advance)
                      {
                        
                    ?>

                    <tr>
                      <td><center><?php echo $sno; ?></center></td>
                      <td style="text-align: right;"><?php echo number_format($row_advance['amount']);?></td>
                      <td><center><?php echo $row_advance['date_effective'];?></center></td>
                      <td><center>
                        <?php if($row_advance['status'] == 0): ?>
                          <span class="badge badge-warning">to be paid by</span>
                        <?php elseif($row_advance['status'] == 1): ?>
                          <span class="badge badge-success">Salary deduct</span>
                        <?php elseif($row_advance['status'] == 2): ?>
                          <span class="badge badge-primary">approved</span>
                        <?php elseif($row_advance['status'] == 3): ?>
                          <span class="badge badge-danger">not approved</span>
                        <?php endif ?>
                      </center></td>
                    </tr>
                    <?php
                        $sno ++;
                      }
                      ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <div class="col-md-6">
            <div class="card card-warning">
              <div class="card-header">
                <h3 class="card-title">Loan Details</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php 
                $query = 'SELECT * FROM loan_list WHERE employee_id="'.$row['join_id'].'" ORDER BY id DESC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();

                $result = $statement->fetchAll();
                ?>

                <table id="example4" class="table table-bordered table-sm table-striped" >
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Loan Amount</th>
                      <th>Salary Deduct</th>
                      <th>Status</th>
                      <th>Loan Close Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php                     
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row_loan)
                      {
                        $query = 'SELECT sum(paid_amount) AS deduct_amount FROM loan_schedules WHERE loan_id="'.$row_loan['id'].'" AND employee_id="'.$row['join_id'].'" AND status=1';

                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row_ded)
                        {
                          
                        }

                        $query = 'SELECT date_due FROM loan_schedules WHERE loan_id="'.$row_loan['id'].'" AND employee_id="'.$row['join_id'].'" ORDER BY id DESC LIMIT 1';

                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row_close)
                        {
                          
                        }
                    ?>

                    <tr>
                      <td><center><?php echo $sno; ?></center></td>
                      <td style="text-align: right;"><?php echo number_format($row_loan['loan_amount']);?></td>
                      <td style="text-align: right;"><?php echo number_format($row_ded['deduct_amount']);?></td>
                      <td><?php ?></td>
                      <td><center><?php echo $row_close['date_due']; ?></center></td>
                    </tr>
                    <?php
                        $sno ++;
                      }
                      ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <div class="col-md-6">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Equipment</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php 
                $query = 'SELECT * FROM inventory_issue WHERE employee_id="'.$row['join_id'].'" AND status = 1 ORDER BY id DESC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();

                $result = $statement->fetchAll();
                ?>

                <table id="example6" class="table table-bordered table-sm table-striped" >
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Product</th>
                      <th>Qty</th>
                      <th>Price</th>
                      <!-- <th>Loan Close Date</th> -->
                    </tr>
                  </thead>
                  <tbody>
                    <?php                     
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row_eqpt)
                      {
                        $query = 'SELECT * FROM inventory_product WHERE id="'.$row_eqpt['product_id'].'" ';

                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row_product)
                        {
                          
                        }
                       
                    ?>

                    <tr>
                      <td><center><?php echo $sno; ?></center></td>
                      <td><?php echo $row_product['product_name'];?></td>
                      <td><center><?php echo $row_eqpt['qty'];?></center></td>
                      <td style="text-align: right;"><?php echo number_format($row_eqpt['total']);?></td>
                      
                    </tr>
                    <?php
                        $sno ++;
                      }
                      ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

          </div>
          <!-- /.col -->          
           
        
          <div class="col-md-6">
            <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Pay Details</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php 
                $query = 'SELECT * FROM payroll_items WHERE employee_id="'.$row['join_id'].'" ORDER BY id DESC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();

                $result = $statement->fetchAll();
                ?>

                <table id="example5" class="table table-bordered table-sm table-striped" >
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Month</th>
                      <th>Net Salary</th>
                      <th>Status</th>                      
                    </tr>
                  </thead>
                  <tbody>
                    <?php                     
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row_pay)
                      {
                        $payroll_id=$row_pay['payroll_id'];

                        $query = 'SELECT * FROM payroll WHERE id="'.$payroll_id.'" ORDER BY id ASC';

                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row_month)
                        {
                          $month=date('Y F', strtotime($row_month['date_from']));
                        }
                    ?>

                    <tr>
                      <td><center><?php echo $sno; ?></center></td>
                      <td><?php echo $month;?></td>
                      <td style="text-align: right;"><?php echo number_format($row_pay['net']);?></td>
                      <td><center>
                        <?php if($row_pay['status'] == 0): ?>
                          <span class="badge badge-warning">Calculated</span>
                        <?php elseif($row_pay['status'] == 1): ?>
                          <span class="badge badge-success">Approved</span>
                        <?php elseif($row_pay['status'] == 2): ?>
                          <span class="badge badge-danger">Halt</span>
                        <?php elseif($row_pay['status'] == 3): ?>
                          <span class="badge badge-primary">Re-approved</span>
                        <?php endif ?>
                      </center></td>                      
                    </tr>
                    <?php
                        $sno ++;
                      }
                      ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <div class="col-md-6">

            <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Bank Details</h3>
                <button class="edit_data4 btn btn-sm btn-primary float-right" data-id="<?php echo $_GET['edit'];?>" type="button" data-toggle="tooltip" data-placement="top" title="Add Bank"><i class="fa fa-bank"></i></button>

              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php 
                $statement = $connect->prepare("SELECT a.id, a.account_no, a.status, b.bank_name, b.bank_no, c.branch_name, c.branch_no FROM bank_details a INNER JOIN bank_name b ON a.bank_name=b.id INNER JOIN bank_branch c ON a.branch_name=c.id WHERE a.employee_id='".$_GET['edit']."'");
                $statement->execute();
                $total_bank = $statement->rowCount();
                $result = $statement->fetchAll();
                ?>

                <table id="example_bank" class="table table-bordered table-sm table-striped">
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Bank Name</th>
                      <th>Account No</th>
                      <th>Status</th>                      
                    </tr>
                  </thead>
                  <tbody>
                    <?php                     
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row_b)
                      {                        
                        
                    ?>

                    <tr>
                      <td><center><?php echo $sno; ?></center></td>
                      <td>
                        <dl>
                          <dt><?php echo $row_b['bank_name'].' ('.$row_b['bank_no'].')'; ?></dt>
                          <dd><?php echo $row_b['branch_name'].' ('.$row_b['branch_no'].')'; ?></dd>
                        </dl>
                      </td>
                      <td><?php echo str_pad($row_b['account_no'], 12, "0", STR_PAD_LEFT);?></td>
                      <td><center>
                        <form action="" method="POST">
                            <input type="hidden" name="bank_id" value="<?php echo $row_b['id'];?>">
                        <?php if($row_b['status'] == 0): ?>
                          
                            <button class="btn btn-sm bg-gradient-danger" name="save_enabled">Disabled</button>                          
                          <!-- <span class="badge badge-success">Enabled</span> -->
                        <?php elseif($row_b['status'] == 1): ?>
                          
                          <button class="btn btn-sm bg-gradient-success" name="save_disabled">Enabled</button>                        
                          <!-- <span class="badge badge-danger">Disabled</span> -->
                        <?php endif ?>
                        </form>
                      </center></td>                      
                    </tr>
                    <?php
                        $sno ++;
                      }
                      ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          
        </div>
        <!-- /.row -->
        
       
      
      </div><!-- /.container-fluid -->
    </section>    

<!-- promotion -->
<div class="modal fade" id="promoteModal" tabindex="-1" role="dialog" aria-labelledby="promoteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form action="" method="POST" id="add_promote_form">
        <div class="modal-content">
          <div class="modal-body">
            <div class="col-md-2"></div>
            <div class="col-md-8">
              <div class="form-group">
                <label for="bank_name">Promoted Rank:</label>
                <select class="form-control select2" style="width: 100%;" name="position_id" id="position_id">
                  <option value="">Select Rank</option>
                  <?php
                  $query="SELECT * FROM position ORDER BY position_id";
                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $result = $statement->fetchAll();
                  foreach($result as $row)
                  {
                    ?>
                    <option value="<?php echo $row['position_id']; ?>"><?php echo $row['position_abbreviation']; ?></option>
                    <?php
                  }
                  ?>
                </select>                      
              </div>
              <div class="form-group">
                <label for="bank_branch">Promoted Date:</label>
                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                  <input type="text" name="promoted_date" id="promoted_date" class="form-control datetimepicker-input" data-target="#reservationdate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo date("Y-m-d"); ?>" />
                  <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                  </div>
                </div>                       
              </div>

              <div class="form-group">
                <label for="promotion_pay">Promotion Pay:</label>
                <input type="text" name="promotion_pay" id="promotion_pay" class="form-control">
              </div>            
              

            </div>
          </div>
          <div style="clear:both;"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal"> Close</button>
            <button name="add_promote" class="btn btn-primary"> Save</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  <!--  start  modal -->
    <div id="editData4" class="modal fade">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Bank Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="info_update4">
            <?php @include("/bank_edit");?>
          </div>
          <!-- <div class="modal-footer ">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div> -->
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
    </div>
    <!--   end modal --> 

<?php
include '../inc/footer.php';
?>

<script src="/plugins/bs-stepper/main.js"></script>
<script>
 
 $(document).ready(function(){

  $('#example1').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,    
  });

  $('#example2').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    
  });

  $('#example3').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    
  });

  $('#example4').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    
  });

  $('#example5').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    
  });

  $('#example6').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    
  });

  $('#example_bank').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    
  });
  $('#example_death').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    
  });

  $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

 });
</script>

<script>
$(function () {
  
  $('#add_join_form').validate({
    rules: {
      create_date: { required: true, date:true},
      employee_no: {required: true, number:true},
      position_id: {required: true},
      basic_salary: {required: true}     
    },

    messages: {      
      
      employee_no: {
        remote: 'Employee No Already existing!'
      },

      nic_new: {
        remote: 'NIC No Already existing!'
      }, 

      nic_old: {
        remote: 'NIC No Already existing!'
      }, 
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });

});
</script>

<script type="text/javascript">
  // ajax script for getting state data
  
   $(document).on('change','#bank_name', function(){
      var bank_nameID = $(this).val();
      if(bank_nameID){
          $.ajax({
              type:'POST',
              url:'/backend-script',
              data:{'bank_name_id':bank_nameID,'request':3},
              success:function(result){
                  $('#bank_branch').html(result);
                 
              }
          });          
      }else{
          $('#bank_branch').html('<option value="">First Select Bank</option>');          
      }
  });

   $(document).on('click','.edit_data4',function(){
        $("#editData4").modal({
            backdrop: 'static',
            keyboard: false
        });
        var edit_id4=$(this).attr('data-id');
        $.ajax({
          url:"/bank_edit",
          type:"post",
          data:{edit_id4:edit_id4},
          success:function(data){
            $("#info_update4").html(data);
            $("#editData4").modal('show');
          }
        });
      });

   $(document).on('click','.edit_pro',function(){
        $("#promoteModal").modal({
            backdrop: 'static',
            keyboard: false
        });        
      });

</script>