<?php 
session_start();
error_reporting(0);
include '../pages/config.php';
include '../inc/timezone.php';
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 25) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;

if ((isset($_POST['add_save'])) OR (isset($_POST['spl_save']))){

  if (checkPermissions($_SESSION["user_id"], 25) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';  
    header('location:/dashboard');  
    exit();
  }

  $start_date=date('Y-m-d', strtotime($_POST['start_date']));
  $end_date=date('Y-m-t', strtotime($start_date));

/*if (isset($_POST['add_save'])){
  $statement = $connect->prepare("SELECT no_of_shifts FROM invoice WHERE department_id='".$_GET['mark']."' AND position_id='".$_POST['position_id']."' AND YEAR(date_effective) = YEAR('".$start_date."') AND MONTH(date_effective) = MONTH('".$start_date."')
      ");
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
  if ($statement->rowCount()>0) {
    
    foreach($result as $row_invoice)
    {
      $invoice_shifts=$row_invoice['no_of_shifts'];
    }
  }else{
    $invoice_shifts=0;
  }
  $statement = $connect->prepare("SELECT sum(no_of_shifts) AS total_shifts FROM attendance WHERE department_id='".$_GET['mark']."' AND position_id='".$_POST['position_id']."' AND start_date='".$start_date."' AND end_date='".$end_date."'");
  $statement->execute();
  $result = $statement->fetchAll();
  if ($statement->rowCount()>0) {  
    foreach($result as $row_att_shifts)
    {
      $total_shifts=$row_att_shifts['total_shifts'];
    }
  }else{
    $total_shifts=0;
  }

  $shifts_diff=(int)$invoice_shifts-(int)$total_shifts;

  if ($shifts_diff < $_POST['no_of_shifts']) {
      $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-info bg-gradient-info text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Less than '.$shifts_diff.' shifts or invoice data not found</div>';
    }
  }*/

  $query = "SELECT j.join_id FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid  
      ";

  if ($_POST['search_selection']=='Service_no') {

    $query .= "WHERE j.employee_no='".$_POST['employee_id']."'
      ";   
  }elseif($_POST['search_selection']=='new'){

    $query .= "WHERE e.nic_no='".$_POST['nic_new1']."'
      ";
    
  }elseif($_POST['search_selection']=='Old'){
    $query .= "WHERE e.nic_no='".$_POST['nic_old1']."'
      ";
  }elseif($_POST['search_selection']=='emp_name'){
    $query .= "WHERE j.join_id='".$_POST['emp_name_id']."'
      ";      
  }

  $query .=" ORDER BY e.employee_id DESC
      ";
  
  $statement = $connect->prepare($query);
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
  foreach($result as $row)
  {
    $employee_id=$row['join_id'];
  }

  for ($i=0; $i < count($_POST['position_id']); $i++) {
    $statement = $connect->prepare("SELECT * FROM attendance WHERE employee_id='".$employee_id."' AND department_id='".$_GET['mark']."' AND position_id='".$_POST['position_id'][$i]."' AND start_date='".$start_date."' AND end_date='".$end_date."' AND shifts_type='".$_POST['shifts_type'][$i]."'");
    $statement->execute(); 
    if($statement->rowCount()>0){
      $error = true;
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>This details Already existing.</div>';
    }
  }

  if (!$error) {

    for ($i=0; $i < count($_POST['position_id']); $i++) {
      $data = array(
          ':employee_id'    =>  $employee_id,
          ':department_id'  =>  $_GET['mark'],
          ':position_id'    =>  $_POST['position_id'][$i],
          ':start_date'     =>  $start_date,
          ':end_date'       =>  $end_date,
          ':shifts_type'   =>  $_POST['shifts_type'][$i],
          ':no_of_shifts'   =>  $_POST['no_of_shifts'][$i], 
          ':extra_ot_hrs'   =>  $_POST['extra_ot_hrs'][$i],
          ':poya_day'       =>  $_POST['poya_day'][$i],
          ':m_day'          =>  $_POST['m_day'][$i],
          ':m_ot_hrs'       =>  $_POST['m_ot_hrs'][$i],
      );
     
      $query = "
      INSERT INTO attendance(employee_id, department_id, position_id, start_date, end_date, shifts_type, no_of_shifts, poya_day, m_day, m_ot_hrs, extra_ot_hrs)
      VALUES (:employee_id, :department_id, :position_id, :start_date, :end_date, :shifts_type, :no_of_shifts, :poya_day, :m_day, :m_ot_hrs, :extra_ot_hrs);
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

    if (!empty($_POST['absent'])) {

      $data_absent = array(
        ':employee_id'    =>  $employee_id,
        ':adsent_date'  =>  $_POST['adsent_date'],      
      );
      $query_absent = "UPDATE join_status SET employee_status=1, absent_date=:adsent_date WHERE join_id=:employee_id;";
      $statement = $connect->prepare($query_absent);
      $statement->execute($data_absent);
    }  
  
  }
}

$error = false;

if (isset($_POST['add_emp'])){

  if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions to Add Employee.</div>'; 
    header('location:/employee_list/employee/'.$_GET['edit'].'');  
    exit();

}

if ($_POST['nic_no_selection']=='new') {
    $nic_no = $_POST['nic_new'];
  }elseif($_POST['nic_no_selection']=='Old'){
    $nic_no = $_POST['nic_old'];
  }elseif ($_POST['nic_no_selection']=='no') {
   $nic_no = '';
  }

  if ($_POST['emp_no_selection']=='emp') {
    $employee_no = $_POST['employee_no'];
  }else{
    $employee_no = strtoupper($_POST['temporary_no']);
  }

  //employee id
$query = 'SELECT * FROM employee ORDER BY employee_id DESC LIMIT 1';

$statement = $connect->prepare($query);
$statement->execute();
$total_data = $statement->rowCount();
$result = $statement->fetchAll();
if ($total_data > 0) {                  
  foreach($result as $row)
  {
    $employee_id = $row['employee_id']+1;
  }
}else{
  $employee_id = 1;
}

//join id

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

$initial=strtoupper(trim($_POST['initial']));

  if ( preg_match('/\s/',$initial) ){
     $correct_initial=$initial;
  } else {
     $correct_initial=trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $initial));
  }


/*$employee_no=  $_POST['employee_no'];
  $statement = $connect->prepare("SELECT employee_no FROM join_status WHERE employee_no=:employee_no");
  $statement->bindParam(':employee_no', $employee_no);
  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $errMSG = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Employee no Already existing.</div>';
  }*/

  if (!$error) {

    $data = array(      
      ':employee_id'        =>  $employee_id,
      ':join_id'            =>  $join_id,
      ':surname'            =>  strtoupper(trim($_POST['surname'])),
      ':initial'            =>  strtoupper($correct_initial),
      ':position_id'        =>  $_POST['position_id'],
      ':nic_no'             =>  strtoupper($nic_no),
      ':epf'                =>  1,
      ':join_date'          =>  $_POST['join_date'],
      ':employee_no'        =>  $employee_no,
      ':basic_salary'       =>  $_POST['basic_salary'],
      ':location'           =>  $_POST['department_id'],        
    );
   
    $query = "
    INSERT INTO `employee`(`employee_id`, `surname`, `initial`, `position_id`, `nic_no`, `epf`)
    VALUES (:employee_id, :surname, :initial, :position_id, :nic_no, :epf);
    INSERT INTO `join_status`(`join_id`, `join_date`, `employee_id`, `employee_no`, `location`) 
    VALUES (:join_id, :join_date, :employee_id, :employee_no, :location);
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
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
         
    }
  }
}

if (isset($_POST['remove_attendance'])){

  if (checkPermissions($_SESSION["user_id"], 28) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/institution_list/institution/'.$_GET['mark'].''); 
    exit();
}

  $data = array(
    ':id'      =>  $_POST['att_id']
       
  );

  $query = "DELETE FROM `attendance` WHERE `id`=:id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Delete Success.</div>';
    // header('location:/institution_list/institution/'.$_GET['mark'].'');            
  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
    
}

include '../inc/header.php';
?>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Institution</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Home</a></li>
              <li class="breadcrumb-item"><a href="/institution_list/institution">Institution</a></li>
              <li class="breadcrumb-item active">Attendance</li>
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

            if(isset($_GET['mark'])):
            
              $query = 'SELECT * FROM department WHERE department_id="'.$_GET['mark'].'"';

              $statement = $connect->prepare($query);
              $statement->execute();
              $total_data = $statement->rowCount();
              $result = $statement->fetchAll();
              if ($total_data > 0):  
                foreach($result as $row):

                  endforeach;
                endif;
              endif;
                  ?>
            
              <form action="" id="formattendance" method="post">
              <div class="card card-warning">
                <div class="card-header">
                  <h3 class="card-title">Mark Attendance - <?php echo $row['department_name'].'-'.$row['department_location']; ?></h3> 
                  <button class="edit_data4 btn btn-sm bg-gradient-primary float-right" type="button" ><i class="fas fa-user"></i> Add</button>            
                </div>
                  <!-- /.card-header -->
                <div class="card-body">

                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="start_date" class="control-label">Month</label>
                        <div class="input-group date" id="reservationmonth" data-target-input="nearest">
                            <input type="text" name="start_date" id="start_date" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date('Y-m', strtotime("-1 month")); ?>" />
                            <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                            </div>
                          </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group clearfix">
                        <div class="icheck-success d-inline">
                          <input type="radio" id="12_hrs" name="shifts_type" value="1" checked>
                          <label for="12_hrs">12 Hrs
                          </label>
                        </div>

                        <div class="icheck-success d-inline">
                          <input type="radio" id="8_hrs" name="shifts_type" value="2">
                          <label for="8_hrs">8 Hrs
                          </label>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-3">
                      <div class="total_shifts" style="justify-content: center;" ></div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group clearfix">
                      <div class="icheck-primary d-inline">
                        <input type="radio" id="service_no" name="search_selection" value="Service_no" checked>
                        <label for="service_no">Service No
                        </label>
                      </div>

                      <div class="icheck-primary d-inline">
                        <input type="radio" id="nic_no_new1" name="search_selection" value="new">
                        <label for="nic_no_new1">New NIC
                        </label>
                      </div>
                      <div class="icheck-primary d-inline">
                        <input type="radio" id="nic_no_old1" name="search_selection" value="Old">
                        <label for="nic_no_old1">Old NIC
                        </label>
                      </div>
                      <div class="icheck-primary d-inline">
                        <input type="radio" id="emp_name" name="search_selection" value="emp_name">
                        <label for="emp_name">Name
                        </label>
                      </div>                    
                  </div>

                  <div class="form-group" id="service_no_field">                    
                    <input type="text" class="form-control" id="employee_id" name="employee_id" autofocus autocomplete="off"> 
                    
                  </div>
                  
                  <div class="form-group" style="display: none" id="nic_no_new_field1">
                    <input type="text" class="form-control" id="nic_new1" name="nic_new1" autocomplete="off" data-inputmask='"mask": "999999999999"' data-mask>
                  </div>

                  <div class="form-group" style="display: none" id="nic_no_old_field1">
                    <input type="text" class="form-control text-uppercase" id="nic_old1" name="nic_old1" autocomplete="off" data-inputmask='"mask": "999999999*"' data-mask>
                  </div>
                  
                  <div class="form-group" style="display: none" id="emp_name_field">
                    
                    <select class="form-control select2" style="width: 100%;" name="emp_name_id" id="emp_name_id">
                    <option value="">Select Employee</option>
                    <?php
                    $query="SELECT e.initial, e.surname, p.position_abbreviation, j.employee_no, j.employee_status, j.location, j.join_id FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN promotions c ON j.join_id=c.employee_id INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro INNER JOIN position p ON c.position_id=p.position_id WHERE j.employee_status!=4 ORDER BY e.employee_id DESC";
                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $result = $statement->fetchAll();
                    foreach($result as $row)
                    {
                      if($row['employee_status'] == 0):
                        $status='<span class="badge badge-success">Present</span>';
                      elseif($row['employee_status'] == 1):
                        $status='<span class="badge badge-danger">Absent</span>';
                      elseif($row['employee_status'] == 2):
                        $status='<span class="badge badge-warning">Re-Enlisted</span>';
                      elseif($row['employee_status'] == 3):
                        $status='<span class="badge badge-warning">Resignation</span>';
                      endif;
                      ?>
                      <option value="<?php echo $row['join_id']; ?>"><?php echo $row['employee_no'].' '.$row['position_abbreviation'].' '.$row['surname'].' '.$row['initial'].' '.$status; ?></option>
                      <?php
                    }
                    ?>
                  </select>

                  </div> 

                  <div id="dis_emp_name" class="form-group">
                    <span id="employee_name" class="text-success"></span>
                  </div>

                  <div class="row">
                    
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="extra_ot_hrs">Extra OT Hrs</label>
                        <input type="text" class="form-control" id="extra_ot_hrs" name="extra_ot_hrs[]" autocomplete="off" >
                        
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="m_day">Merc Days</label>
                        <input type="text" class="form-control" id="m_day" name="m_day[]" autocomplete="off" >
                        
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="m_ot_hrs">Merc OT Hrs</label>
                        <input type="text" class="form-control" id="m_ot_hrs" name="m_ot_hrs[]" autocomplete="off" >
                        
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="poya_day">Poya Days</label>
                        <input type="text" class="form-control" id="poya_day" name="poya_day[]" autocomplete="off" >
                        
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    
                    <div class="col-md-3">
                      <div class="form-group clearfix">
                    <div class="icheck-primary d-inline">
                      <input type="checkbox" id="absent" name="absent" value="1">
                      <label for="absent">Absent
                      </label>
                    </div>                    
                  </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="adsent_date">Absent Date:</label>
                        <div class="input-group date" id="reservationjoindate" data-target-input="nearest">
                          <input type="text" name="adsent_date" id="adsent_date" class="form-control datetimepicker-input" data-target="#reservationjoindate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo date("Y-m-d"); ?>"/>
                          <div class="input-group-append" data-target="#reservationjoindate" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                      
                    </div>
                    <div class="col-md-6">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="">Position Name</label>
                          </div>
                        </div>
                      </div>
                      
                          <div class="row">
                       
                           <?php
                      $query="SELECT b.position_id, b.position_abbreviation, a.position_payment FROM position_pay a INNER JOIN position b ON a.position_id=b.position_id WHERE department_id='".$_GET['mark']."' ORDER BY position_id";
                      $statement = $connect->prepare($query);
                      $statement->execute();
                      $result = $statement->fetchAll();
                      foreach($result as $row)
                      {
                        ?>
                        
                          <div class="col-md-4">
                            <div class="form-group clearfix">
                              <div class="icheck-success d-inline">
                                <input type="checkbox" id="checkboxPrimary<?php echo $row['position_id']; ?>" name="position_id[]" value="<?php echo $row['position_id']; ?>" onchange="toggleInput(this)">
                                <label for="checkboxPrimary<?php echo $row['position_id']; ?>"><?php echo $row['position_abbreviation'].'-'.$row['position_payment']; ?>
                                </label>
                              </div>
                              </div>
                            </div>
                                <div class="col-md-2">
                                <div class="form-group ">
                        <input type="text" class="form-control" id="no_of_shifts<?php echo $row['position_id']; ?>" name="no_of_shifts[]" autocomplete="off" disabled >

                        <input type="hidden" class="form-control" id="extra_ot_hrs" name="extra_ot_hrs[]" autocomplete="off" >
                        <input type="hidden" class="form-control" id="m_day" name="m_day[]" autocomplete="off" >
                        <input type="hidden" class="form-control" id="m_ot_hrs" name="m_ot_hrs[]" autocomplete="off" >
                        <input type="hidden" class="form-control" id="poya_day" name="poya_day[]" autocomplete="off" >
                      </div>
                              </div>
                          
                        <?php
                      }
                      ?>
                          </div>

                          
                          <div class="row">
                            <div class="col-md-12 col-sm-6 col-12">
                              <div class="filter_data" style="justify-content: center;" ></div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12 col-sm-6 col-12">
                              <div class="info-box bg-info">
                                <div class="info-box-content">
                                  
                                 <div class="last_data " style="justify-content: center;" ></div>
                                  
                                </div>
                                <!-- /.info-box-content -->
                              </div>
                              <!-- /.info-box -->
                            </div>
                            <!-- /.col -->
                            
                          </div>
                    </div>
                  </div>
                  <div class="row">

                  </div>                  

                    <!-- <div class="form-group">
                            <label for="">Position Name</label>
                            <select class="form-control select2" style="width: 100%;" name="position_id" id="position_id">
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
                          </div> -->
            
                          <input type="hidden" id="department_id" name="department_id" value="<?php echo $_GET['mark'];?>">
                  <!-- 

                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="end_date" class="control-label">End Date</label>
                        <div class="input-group date" id="reservationenddate" data-target-input="nearest">-->
                            <input type="hidden" name="end_date" id="end_date" class="form-control datetimepicker-input" data-target="#reservationenddate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo date('Y-m-d', strtotime("last day of -1 month")); ?>" /><!--
                            <div class="input-group-append" data-target="#reservationenddate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                            </div>
                          </div>
                      </div>
                    </div>

                    <div class="col-md-3">
                       
                    </div>
                      -->
                  
                </div>
                <!-- /.card-body -->

                <div class="card-footer change_btn" >
                  <!-- <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="add_save"> Save</button>
                  <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button> -->
                </div>

              </div>
              <!-- /.card -->
            </form>
            <div class="row">

              <div class="col-md-4">
                <div class="card card-success">
                  <div class="card-header">
                    <h3 class="card-title">Allowances</h3>
                  </div>
                  <div class="card-body">
                    <table class="table table-sm table-bordered table-striped">
                  
                      <tbody id="allowances"></tbody>
                    </table>

                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>

              <div class="col-md-4">
                <div class="card card-secondary">
                  <div class="card-header">
                    <h3 class="card-title">Deductions</h3>
                  </div>
                  <div class="card-body">
                    <table class="table table-sm table-bordered table-striped">
                  
                      <tbody id="deductions"></tbody>                      
                    </table>

                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
              <div class="col-md-4">
                <div class="card card-info">
                  <div class="card-header">
                    <h3 class="card-title">Working Place</h3>
                  </div>
                  <div class="card-body">
                    <table class="table table-sm table-bordered table-striped">
                  
                      <tbody id="working_place"></tbody>                      
                    </table>

                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div> 
              
            </div>
            <div class="row">
              <div class="col-md-12">
                <table class="table table-sm table-bordered table-striped">
                  <thead>
                    <tr style="text-align:center;">
                      <th>#</th>
                      <th>Employee Name</th>
                      <th>Position Name</th>
                      <th>Shifts Type</th>
                      <th>No of Shifts</th>
                      <th>Mercantile Days</th>
                      <th>Mercantile OT Hrs</th>
                      <th>Poya Days</th>
                      <th>Extra OT</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                        
                  <tbody id="invoicedata">
                    
                  </tbody>
                </table>
              </div>
          
            </div>
                       
          </div>

          
        </div>
        <!-- /.row -->


        <!--  start  modal -->
    <div id="editData4" class="modal fade">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Employee</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="info_update4">
            <?php @include("/ins_add_emp");?>
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

                
      </div><!-- /.container-fluid -->
    </section>    

<?php
include '../inc/footer.php';
?>

<script type="text/javascript">
  $(function () {
    $("input[name='search_selection']").click(function () {
      if ($("#service_no").is(":checked")) {
          $("#service_no_field").show();
          $('#employee_id').attr('required','');
          $('#employee_id').attr('focus', true);
          $('#employee_id').attr('data-error', 'This field is required.');
          $('#employee_id').val(''); 
          $("#dis_emp_name").show();           
      } else {
          $("#service_no_field").hide();
          $('#employee_id').removeAttr('required');
          $('#employee_id').removeAttr('data-error');
          $('#employee_id').removeAttr('focus');
          $('#employee_id').val('');
      }

      if ($("#nic_no_new1").is(":checked")) {
          $("#nic_no_new_field1").show();
          $('#nic_new1').attr('required','');
          $('#nic_new1').attr('focus', true);
          $('#nic_new1').attr('data-error', 'This field is required.');
          $('#nic_new1').val('');
          $("#dis_emp_name").show();         
      } else {
          $("#nic_no_new_field1").hide();
          $('#nic_new1').removeAttr('required');
          $('#nic_new1').removeAttr('data-error');
          $('#nic_new1').removeAttr('focus');
          $('#nic_new1').val('');
      }
      if ($("#nic_no_old1").is(":checked")) {
          $("#nic_no_old_field1").show();
          $('#nic_old1').attr('required','');
          $('#nic_old1').attr('focus', true);
          $('#nic_old1').attr('data-error', 'This field is required.');
          $('#nic_old1').val('');
          $("#dis_emp_name").show();            
      } else {
          $("#nic_no_old_field1").hide();
          $('#nic_old1').removeAttr('required');
          $('#nic_old1').removeAttr('focus');
          $('#nic_old1').removeAttr('data-error');
          $('#nic_old1').val('');          
      }

      if ($("#emp_name").is(":checked")) {
          $("#emp_name_field").show();
          $('#emp_name_id').attr('required','');
          $('#emp_name_id').attr('focus', true);
          $('#emp_name_id').attr('data-error', 'This field is required.');
          $("#dis_emp_name").hide();
          $('#emp_name_id').val('');
          $('#employee_name').val('');
      } else {
          $("#emp_name_field").hide();
          $('#emp_name_id').removeAttr('required');
          $('#emp_name_id').removeAttr('focus');
          $('#emp_name_id').removeAttr('data-error');
          $('#emp_name_id').val(''); 
      }
        
    });    
      
  });
    
</script>
<script type="text/javascript">
$(function () {
  
  $('#formattendance').validate({
    rules: {
      employee_id: {
        remote: {
          url: "/check_employee_id",
          type: "post"
        }
      },

      nic_new1: { 
        remote: {
          url: "/check_employee_id",
          type: "post"
        }
      },

      nic_old1: { 
        remote: {
          url: "/check_employee_id",
          type: "post"
        }
      },
      
      department_id: {required: true, 
        remote: {
          url: "/check_department_id",
          type: "post"
        }
      },

      position_id: {required: true, 
        remote: {
          url: "/check_position_id",
          type: "post"
        }
      },

      start_date: {required: true},
      end_date: {required: true},
      no_of_shifts: {required: true},
      employee_no: {required: true},
      surname: {required: true},
      initial: {required: true}      
    },

    messages: {  
    
      employee_id: {
        remote: 'Wrong Employee No!'
      },

      nic_new1: {
        remote: 'Wrong New NIC No!'
      },

      nic_old1: {
        remote: 'Wrong Old NIC No!'
      },

      department_id: {
        remote: 'Wrong Institution ID!'
      },

      position_id: {
        remote: 'Wrong position id!'
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
$(document).ready(function(){
load_data();
load_attendance();
filter_data();
last_data();
total_shifts();
change_save();

setInterval(function(){
    load_attendance();   
    filter_data();
    last_data();
    total_shifts();
  }, 2000);

function filter_data()
{
  /*$('.filter_data').html('<div id="loading" style="" ></div>');*/
  var start_date = $('#start_date').val();
  var department_id = $('#department_id').val();        
  $.ajax({
      url:"/attendancecount",
      method:"POST",
      data:{start_date:start_date, department_id:department_id, request:1},
      success:function(data){
          $('.filter_data').html(data);
      }
  });
}

function last_data()
{
  var start_date = $('#start_date').val();
  var department_id = $('#department_id').val();        
  $.ajax({
      url:"/attendancecount",
      method:"POST",
      data:{start_date:start_date, department_id:department_id, request:2},
      success:function(data){
          $('.last_data').html(data);
      }
  });
}

$(document).on('click','.edit_data4',function(){
  $("#editData4").modal({
      backdrop: 'static',
      keyboard: false
  });
  var edit_id4=$(this).attr('data-id');
  $.ajax({
    url:"/ins_add_emp",
    type:"post",
    data:{edit_id4:edit_id4},
    success:function(data){
      $("#info_update4").html(data);
      $("#editData4").modal('show');
    }
  });
});

function load_data(query = '')
{
  var query = $('#employee_id').val();
  var query_new_nic = $('#nic_new1').val();
  var query_nic_old = $('#nic_old1').val();
  var query_start_date = $('#start_date').val();
  var query_end_date = $('#end_date').val();
  var query_emp_name_id = $('#emp_name_id').val();

  $.ajax({
    url:"/employee_no",
    method:"POST",
    data:{query:query,query_new_nic:query_new_nic,query_nic_old:query_nic_old,request:1},
    dataType: 'json',

    success:function(response)
    {
      var len = response.length;
      
      var name_with_initial='';
      
      if(len > 0){
          var name_with_initial = response[0]['name_with_initial'];
      }
      document.getElementById('employee_name').innerHTML = name_with_initial;
    }
  });
      
  $.ajax({
    url:"/employee_no",
    method:"POST",
    data:{query:query,query_new_nic:query_new_nic,query_nic_old:query_nic_old,query_start_date:query_start_date,query_end_date:query_end_date,query_emp_name_id:query_emp_name_id,request:4},
    dataType: 'json',

    success:function(response)
    {
      var html='';
      
      if(response.length > 0)
      {
        for(var count = 0; count < response.length; count++)
        {
          html += '<tr>';
          html += '<td style="width:75%;">'+response[count].allowances_en+'</td>';
          html += '<td style="text-align:right; width:25%;">'+response[count].amount+'</td>';
          html += '</tr>';              
        }
      }
      else
      {
        html += '<tr><td colspan="2" class="text-center">No Data Found</td></tr>';
      }
      document.getElementById('allowances').innerHTML = html;
    }
  });

  $.ajax({
    url:"/employee_no",
    method:"POST",
    data:{query:query,query_new_nic:query_new_nic,query_nic_old:query_nic_old,query_start_date:query_start_date,query_end_date:query_end_date,query_emp_name_id:query_emp_name_id,request:5},
    dataType: 'json',

    success:function(response)
    {
      var html='';
      
      if(response.length > 0)
      {
        for(var count = 0; count < response.length; count++)
        {
          html += '<tr>';
          html += '<td style="width:75%;">'+response[count].deduction_en+'</td>';
          html += '<td style="text-align:right; width:25%;">'+response[count].amount+'</td>';
          html += '</tr>';              
        }
      }
      else
      {
        html += '<tr><td colspan="2" class="text-center">No Data Found</td></tr>';
      }
      document.getElementById('deductions').innerHTML = html;
    }
  }); 

  $.ajax({
    url:"/employee_no",
    method:"POST",
    data:{query:query,query_new_nic:query_new_nic,query_nic_old:query_nic_old,query_start_date:query_start_date,query_end_date:query_end_date,query_emp_name_id:query_emp_name_id,request:6},
    dataType: 'json',

    success:function(response)
    {
      var html='';
      
      if(response.length > 0)
      {
        for(var count = 0; count < response.length; count++)
        {
          html += '<tr>';
          html += '<td style="width:65%;">'+response[count].department_name+'</td>';
          html += '<td style="text-align:right; width:20%;">'+response[count].position_abbreviation+'</td>';
          html += '<td style="text-align:right; width:15%;">'+response[count].no_of_shifts+'</td>';
          html += '</tr>';              
        }
      }
      else
      {
        html += '<tr><td colspan="2" class="text-center">No Data Found</td></tr>';
      }
      document.getElementById('working_place').innerHTML = html;
    }
  });  
}

function load_attendance(department_id = '' , )
{
  var department_id = $('#department_id').val();
  var start_date = $('#start_date').val();
   
  $.ajax({
    url:"/employee_no",
    method:"POST",
    data:{department_id:department_id,start_date:start_date,request:7},
    dataType: 'json',

    success:function(response)
    {
      var html='';
      var serial_no = 1;
      
      if(response.length > 0)
      {
        for(var count = 0; count < response.length; count++)
        {
          html += '<tr>';
          html += '<td style="width:5%;"><center>'+serial_no+'</center></td>';
          html += '<td style="width:30%;">'+response[count].emp_name+'</td>';
          html += '<td style="width:10%;"><center>'+response[count].position_name+'</center></td>';
          html += '<td style="text-align:right; width:10%;"><center>'+response[count].shifts_type+'</center></td>';
          html += '<td style="text-align:right; width:10%;"><center>'+response[count].no_of_shifts+'</center></td>';
          html += '<td style="text-align:right; width:10%;"><center>'+response[count].m_day+'</center></td>';
          html += '<td style="text-align:right; width:9%;"><center>'+response[count].m_ot_hrs+'</center></td>';
          html += '<td style="text-align:right; width:8%;"><center>'+response[count].poya_day+'</center></td>'; 
          html += '<td style="text-align:right; width:8%;"><center>'+response[count].extra_ot_hrs+'</center></td>';
          html += '<td style="text-align:right; width:10%;"><center>'+response[count].action+'</center></td>';
          html += '</tr>'; 
          serial_no++;            
        }
      }
      else
      {
        html += '<tr><td colspan="9" class="text-center">No Data Found</td></tr>';
      }
      document.getElementById('invoicedata').innerHTML = html;
    }
  });  
}

function total_shifts(department_id = '')
{
  var department_id = $('#department_id').val();
  var start_date = $('#start_date').val();
  
  $.ajax({
    url:"/employee_no",
    method:"POST",
    data:{department_id:department_id,start_date:start_date,request:8},
    success:function(data){
      $('.total_shifts').html(data);
    }
  });
}

function change_save(department_id = '')
{
  var department_id = $('#department_id').val();
  var start_date = $('#start_date').val();
  var position_id = $('input[type=radio][name=position_id]:checked').val();
  
  $.ajax({
    url:"/employee_no",
    method:"POST",
    data:{department_id:department_id,start_date:start_date,position_id:position_id,request:13},
    success:function(data){
      $('.change_btn').html(data);
    }
  });
}

$('#employee_id').keyup(function(){
  var query = $('#employee_id').val();
  load_data(1, query);
});

$('#nic_new1').keyup(function(){
  var query_new_nic = $('#nic_new1').val();
  load_data(1, query_new_nic);
});

$('#nic_old1').keyup(function(){
  var query_nic_old = $('#nic_old1').val();
  load_data(1, query_nic_old);
  });

$('#emp_name_id').change(function(){
  var query_emp_name_id = $('#emp_name_id').val();
  load_data(1, query_emp_name_id);
  });

$('input[type=radio][name=position_id]').click(function(){
  var position_id = $(this).val();
  change_save(1, position_id);
  });

$('#start_date').change(function(){
  var query_start_date = $('#start_date').val();
  change_save(1, query_start_date);
  });
});
</script>

<script>
    function toggleInput(checkbox) {
        var inputFieldId = 'no_of_shifts' + checkbox.value;
        var inputField = document.getElementById(inputFieldId);
        
        // Enable/disable the input field based on checkbox state
        inputField.disabled = !checkbox.checked;
        inputField.focus();
    }
</script>