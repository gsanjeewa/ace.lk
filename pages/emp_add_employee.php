<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();

require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions to Add Employee.</div>';
    header('location:/employee_list/employee');
    exit();

}

$error = false;

if (isset($_POST['add_save'])){

  if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions to Add Employee.</div>'; 
    header('location:/employee_list/add_employee');  
    exit();

}

  /*Profile image*/
  $file_name = $_POST['file_name'];
  $base64_img = $_POST['cropped_img'];
  if (!empty($base64_img)) {
    $image_array_1 = explode(",", $base64_img);
    $data_base64 = base64_decode($image_array_1[1]);
    $ext = pathinfo($file_name,PATHINFO_EXTENSION);
    $image= time().'.'.$ext;
  }else{
    $image='';
  }

  if (!empty($_POST['sinhala_writing'])) {
      $sinhala_writing=$_POST['sinhala_writing'];
  }else{
      $sinhala_writing='';
  }

  if (!empty($_POST['sinhala_reading'])) {
      $sinhala_reading=$_POST['sinhala_reading'];
  }else{
      $sinhala_reading='';
  }

  if (!empty($_POST['sinhala_speaking'])) {
      $sinhala_speaking=$_POST['sinhala_speaking'];
  }else{
      $sinhala_speaking='';
  }

  if (!empty($_POST['english_writing'])) {
      $english_writing=$_POST['english_writing'];
  }else{
      $english_writing='';
  }

  if (!empty($_POST['english_reading'])) {
      $english_reading=$_POST['english_reading'];
  }else{
      $english_reading='';
  }

  if (!empty($_POST['english_speaking'])) {
      $english_speaking=$_POST['english_speaking'];
  }else{
      $english_speaking='';
  }

  if (!empty($_POST['tamil_writing'])) {
      $tamil_writing=$_POST['tamil_writing'];
  }else{
      $tamil_writing='';
  }

  if (!empty($_POST['tamil_reading'])) {
      $tamil_reading=$_POST['tamil_reading'];
  }else{
      $tamil_reading='';
  }
  if (!empty($_POST['tamil_speaking'])) {
      $tamil_speaking=$_POST['tamil_speaking'];
  }else{
      $tamil_speaking='';
  }

  $languages=array('sw'=>$sinhala_writing,"sr"=>$sinhala_reading,"ss"=>$sinhala_speaking,"ew"=>$english_writing,"er"=>$english_reading,"es"=>$english_speaking,"tw"=>$tamil_writing,"tr"=>$english_reading,"ts"=>$tamil_speaking);

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
$query = 'SELECT employee_id FROM employee ORDER BY employee_id DESC LIMIT 1';

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

  $employee_no=  $_POST['employee_no'];
  $statement = $connect->prepare("SELECT employee_no FROM join_status WHERE employee_no=:employee_no");
  $statement->bindParam(':employee_no', $employee_no);
  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $errMSG = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Employee no Already existing.</div>';
  }

  $statement = $connect->prepare("SELECT nic_no FROM employee WHERE nic_no=:nic_no");
  $statement->bindParam(':nic_no', $nic_no);
  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $errMSG = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>This NIC no Already existing.</div>';
  }

  $initial=strtoupper(trim($_POST['initial']));

  if ( preg_match('/\s/',$initial) ){
     $correct_initial=$initial;
  } else {
     $correct_initial=trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $initial));
  }


  if (!$error) {

    $data = array(      
      ':employee_id'        =>  $employee_id,
      ':join_id'            =>  $join_id,
      ':full_name'          =>  strtoupper(trim($_POST['full_name'])),
      ':surname'            =>  strtoupper(trim($_POST['surname'])),
      ':initial'            =>  strtoupper($correct_initial),
      ':position_id'        =>  $_POST['position_id'],
      ':birthday'           =>  $_POST['birthday'],
      ':nationality'        =>  strtoupper(trim($_POST['nationality'])),
      ':nic_no'             =>  strtoupper($nic_no),
      ':permanent_address'  =>  strtoupper(trim($_POST['permanent_address'])),
      ':temporary_address'  =>  strtoupper(trim($_POST['temporary_address'])),
      ':mobile_no'          =>  $_POST['mobile_no'],
      ':home_no'            =>  $_POST['home_no'],
      ':dis_id'             =>  $_POST['districts'],
      ':ds_id'              =>  $_POST['ds'],
      ':gramasewa'          =>  $_POST['gramasewa'],
      ':police'             =>  $_POST['police'],
      ':married_status'     =>  $_POST['married_status'],
      ':if_service'         =>  $_POST['if_service'],
      ':epf'                =>  $_POST['epf'],
      ':languages'          =>  json_encode($languages),
      ':employee_images'    =>  $image,
      ':join_date'          =>  $_POST['join_date'],
      ':employee_no'        =>  $employee_no,
      ':basic_salary'       =>  $_POST['basic_salary'],
      ':location'           =>  $_POST['department_id'],        
    );
   
    $query = "
    INSERT INTO `employee`(`employee_id`, `full_name`, `surname`, `initial`, `position_id`, `birthday`, `nationality`, `nic_no`, `permanent_address`, `temporary_address`, `mobile_no`, `home_no`, `dis_id`, `ds_id`, `gramasewa`, `police`, `married_status`, `if_service`, `epf`, `languages`, `employee_images`)
    VALUES (:employee_id, :full_name, :surname, :initial, :position_id, :birthday, :nationality, :nic_no, :permanent_address, :temporary_address, :mobile_no, :home_no, :dis_id, :ds_id, :gramasewa, :police, :married_status, :if_service, :epf, :languages, :employee_images);
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
      if(($_POST['bank_name'] != '') AND ($_POST['bank_branch'] != '') AND ($_POST['account_no'] != ''))
      {

        $bank_account = str_pad($_POST['account_no'], 12, "0", STR_PAD_LEFT);

        $data_bank = array(
          ':employee_id'        =>  $employee_id,  
          ':holder_name'          =>  strtoupper(trim($_POST['holder_name'])),
          ':bank_name'          =>  $_POST['bank_name'],
          ':branch_name'        =>  $_POST['bank_branch'],
          ':branch_no'          =>  '',
          ':account_no'         =>  $bank_account,            
        );

        $query_bank = "INSERT INTO `bank_details`(`employee_id`, `holder_name`, `bank_name`, `branch_name`, `branch_no`, `account_no`)
          VALUES (:employee_id, :holder_name, :bank_name, :branch_name, :branch_no, :account_no)
          ";

        $statement = $connect->prepare($query_bank);
        $statement->execute($data_bank);
      }      

      if (!empty($image) && !empty($data_base64)) {
        file_put_contents('../employee_image/' . $image, $data_base64);
      }
      $errMSG = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';            
    }else{
      $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
    }
  }
}

if (isset($_POST['update_save'])){

  if (checkPermissions($_SESSION["user_id"], 2) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions to Add Employee.</div>'; 
    header('location:/employee_list/add_employee');  
    exit();

}
  
  /*Profile image*/
  $file_name = $_POST['file_name'];
  $base64_img = $_POST['cropped_img'];
  if (!empty($base64_img)) {
    $image_array_1 = explode(",", $base64_img);
    $data_base64 = base64_decode($image_array_1[1]);
    $ext = pathinfo($file_name,PATHINFO_EXTENSION);
    $image= time().'.'.$ext;
  }else{
    $image='';
  }

  if (!empty($_POST['sinhala_writing'])) {
      $sinhala_writing=$_POST['sinhala_writing'];
  }else{
      $sinhala_writing='';
  }

  if (!empty($_POST['sinhala_reading'])) {
      $sinhala_reading=$_POST['sinhala_reading'];
  }else{
      $sinhala_reading='';
  }

  if (!empty($_POST['sinhala_speaking'])) {
      $sinhala_speaking=$_POST['sinhala_speaking'];
  }else{
      $sinhala_speaking='';
  }

  if (!empty($_POST['english_writing'])) {
      $english_writing=$_POST['english_writing'];
  }else{
      $english_writing='';
  }

  if (!empty($_POST['english_reading'])) {
      $english_reading=$_POST['english_reading'];
  }else{
      $english_reading='';
  }

  if (!empty($_POST['english_speaking'])) {
      $english_speaking=$_POST['english_speaking'];
  }else{
      $english_speaking='';
  }

  if (!empty($_POST['tamil_writing'])) {
      $tamil_writing=$_POST['tamil_writing'];
  }else{
      $tamil_writing='';
  }

  if (!empty($_POST['tamil_reading'])) {
      $tamil_reading=$_POST['tamil_reading'];
  }else{
      $tamil_reading='';
  }
  if (!empty($_POST['tamil_speaking'])) {
      $tamil_speaking=$_POST['tamil_speaking'];
  }else{
      $tamil_speaking='';
  }

  $languages=array('sw'=>$sinhala_writing,"sr"=>$sinhala_reading,"ss"=>$sinhala_speaking,"ew"=>$english_writing,"er"=>$english_reading,"es"=>$english_speaking,"tw"=>$tamil_writing,"tr"=>$english_reading,"ts"=>$tamil_speaking);
 
  if ($_POST['nic_no_selection']=='new') {
    $nic_no = $_POST['nic_new2'];
  }else{
    $nic_no = $_POST['nic_old2'];
  }
  $initial=strtoupper(trim($_POST['initial']));

  if ( preg_match('/\s/',$initial) ){
     $correct_initial=$initial;
  } else {
     $correct_initial=trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $initial));
  }

    $data = array(
      ':employee_id'        =>  $_GET['edit'],
      ':full_name'          =>  strtoupper(trim($_POST['full_name'])),
      ':surname'            =>  strtoupper(trim($_POST['surname'])),
      ':initial'            =>  strtoupper($correct_initial),      
      ':birthday'           =>  $_POST['birthday'],
      ':nationality'        =>  strtoupper(trim($_POST['nationality'])),
      ':nic_no'             =>  strtoupper($nic_no),
      ':permanent_address'  =>  strtoupper(trim($_POST['permanent_address'])),
      ':temporary_address'  =>  strtoupper(trim($_POST['temporary_address'])),
      ':mobile_no'          =>  $_POST['mobile_no'],
      ':home_no'            =>  $_POST['home_no'],
      ':dis_id'             =>  $_POST['districts'],
      ':ds_id'              =>  $_POST['ds'],
      ':gramasewa'          =>  $_POST['gramasewa'],
      ':police'             =>  $_POST['police'],
      ':married_status'     =>  $_POST['married_status'],
      ':if_service'         =>  $_POST['if_service'],
      ':epf'                =>  $_POST['epf'],
      ':languages'          =>  json_encode($languages),
      ':employee_images'    =>  $image,
      ':promo_id'           =>  $_POST['promo_id'],
      ':position_id'        =>  $_POST['position_id'],
      ':join_date'          =>  $_POST['join_date'],
      ':join_id'            =>  $_POST['join_id'],
      ':location'           =>  $_POST['department_id'],
      ':basic_salary'       =>  $_POST['basic_salary'],
      ':salary_id'          =>  $_POST['salary_id'],

    );
   
    $query = "
    UPDATE `employee` SET `full_name`=:full_name,`surname`=:surname,`initial`=:initial,`birthday`=:birthday,`nationality`=:nationality,`nic_no`=:nic_no,`permanent_address`=:permanent_address,`temporary_address`=:temporary_address,`mobile_no`=:mobile_no,`home_no`=:home_no,`dis_id`=:dis_id,`ds_id`=:ds_id,`gramasewa`=:gramasewa,`police`=:police,`married_status`=:married_status,`if_service`=:if_service,`epf`=:epf,`languages`=:languages,`employee_images`=:employee_images WHERE `employee_id`=:employee_id;    
    UPDATE `join_status` SET `join_date`=:join_date, `location`=:location WHERE `join_id`=:join_id;
    UPDATE `salary` SET `basic_salary`=:basic_salary WHERE `id`=:salary_id;
    ";

    if (!empty($_POST['promo_id'])){
      $query .= "UPDATE `promotions` SET `position_id`=:position_id WHERE `id`=:promo_id;";
    }else{
      $query .= "INSERT INTO `promotions`(`employee_id`, `position_id`, `promoted_date`)
     VALUES (:join_id, :position_id, :join_date);";
    }
            
    $statement = $connect->prepare($query);

    if($statement->execute($data))
    {
      if (!empty($image) && !empty($data_base64)) {
        file_put_contents('../employee_image/' . $image, $data_base64);
      }
      header('location:/employee_list/employee');            
    }else{
      $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
    }
 
}

function fill_unit_select_box($connect)
{ 
 $output = '';
  $query="SELECT * FROM subject_ol WHERE subject_status=0 ORDER BY subject_id ASC";
  $statement = $connect->prepare($query);
  $statement->execute();

  $result = $statement->fetchAll();
  foreach($result as $row)
  {
    $output .= '<option value="'.$row['subject_id'].'">'.$row['subject'].'</option>';
  }
  return $output;
}

function fill_al_select_box($connect)
{ 
 $output = '';
  $query="SELECT * FROM subject_al WHERE subject_status=0 ORDER BY subject_id ASC";
  $statement = $connect->prepare($query);
  $statement->execute();

  $result = $statement->fetchAll();
  foreach($result as $row)
  {
    $output .= '<option value="'.$row['subject_id'].'">'.$row['subject'].'</option>';
  }
  return $output;
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
              $query = 'SELECT * FROM employee WHERE employee_id="'.$_GET['edit'].'"';

              $statement = $connect->prepare($query);
              $statement->execute();
              $total_data = $statement->rowCount();
              $result = $statement->fetchAll();
              if ($total_data > 0){   
                foreach($result as $row)
                {

                  $statement = $connect->prepare('SELECT a.position_id, a.id FROM promotions a INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) b ON a.employee_id = b.employee_id AND a.id = b.maxid_pro INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) c ON a.employee_id = c.maxid WHERE c.employee_id="'.$row['employee_id'].'"');
                $statement->execute();
                $total_position = $statement->rowCount();
                $result = $statement->fetchAll();
                
                  foreach($result as $position_name):
                                      
                  endforeach;

                  $statement = $connect->prepare('SELECT j.location, j.join_date, a.basic_salary, j.join_id, a.id, j.employee_no FROM salary a INNER JOIN (SELECT employee_id, MAX(id) maxid_sal FROM salary GROUP BY employee_id) b ON a.employee_id = b.employee_id AND a.id = b.maxid_sal INNER JOIN join_status j ON a.employee_id = j.join_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) c ON j.employee_id = c.employee_id AND j.join_id = c.maxid WHERE j.employee_id="'.$row['employee_id'].'"');
                  $statement->execute();
                  $total_position = $statement->rowCount();
                  $result = $statement->fetchAll();
                
                  foreach($result as $row_join):
                                      
                  endforeach;

                  if (!empty($row['employee_images'])) {
                    $path='/employee_image/'.$row['employee_images'].'';
                  }else{
                    $path='/dist/img/avatar5.png';
                  }
                  ?>
                  <form action="" id="add_emp_form" method="post">
                    <div class="card card-danger">
                      <div class="card-header">
                        <h3 class="card-title">Edit Employee - <span class="right badge badge-success"><?php echo $row_join['employee_no'] ?></span></h3>                
                      </div>
                        <!-- /.card-header -->
                      <div class="card-body">

                        <div class="row">
                      <div class="col-md-5">
                       <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                          <div class="input-group">
                            <div class="image_area ">
                              <label for="upload_image">
                                <img src="<?php echo $path; ?>" id="uploaded_image" class="img-responsive img-circle" />
                                <div class="overlay">
                                  <div class="text"><!-- Click to  -->Add Employee Image</div>
                                </div>
                                <input type="file" name="image" class="image" id="upload_image" style="display:none" />
                              </label>
                              <input type="hidden" name="cropped_img" id="cropped_img">
                              <input type="hidden" name="file_name" id="file_name">
                            </div>
                          </div>
                        </div>
                      </div>
                      </div>


                    </div>

                        <div class="row">
                      <div class="col-md-3">                        
                        <label for="nic_no">1. NIC NO (ජා: හැ: අංකය):</label>
                        <div class="form-group clearfix">
                        <div class="icheck-primary d-inline">
                          <input type="radio" id="nic_no_new" name="nic_no_selection" value="new" <?php if (strlen($row['nic_no'])==12) { echo "checked";} ?>>
                          <label for="nic_no_new">New NIC
                          </label>
                        </div>
                        <div class="icheck-primary d-inline">
                          <input type="radio" id="nic_no_old" name="nic_no_selection" value="Old" <?php if (strlen($row['nic_no'])!=12) { echo "checked";} ?>>
                          <label for="nic_no_old">Old NIC
                          </label>
                        </div>                      
                      </div>
                      <div class="form-group">
                        <div class="form-group" id="nic_no_new_field" <?php if (strlen($row['nic_no'])==12) { echo 'style="display: block"';}else{ echo 'style="display: none"';} ?>>
                          <input type="text" class="form-control" id="nic_new2" name="nic_new2" autocomplete="off" data-inputmask='"mask": "999999999999"' data-mask autofocus value="<?php if (strlen($row['nic_no'])==12) { echo $row['nic_no'];} ?>">
                        </div>

                        <div class="form-group" <?php if (strlen($row['nic_no'])!=12) { echo 'style="display: block"';}else{ echo 'style="display: none"';} ?>  id="nic_no_old_field">
                          <input type="text" class="form-control text-uppercase" id="nic_old2" name="nic_old2" autocomplete="off" data-inputmask='"mask": "999999999*"' data-mask autofocus value="<?php if (strlen($row['nic_no'])!=12) { echo $row['nic_no'];} ?>">
                        </div>                                              
                          
                        </div>
                      </div>

                      <div class="col-md-3">
                        <div class="form-group">
                            <label for="epf">EPF funds membership (අරමුදල් සමාජිකත්වය):</label>
                            
                            <div class="form-group clearfix">
                          <div class="icheck-primary d-inline">
                            <input type="radio" id="epf_yes" name="epf" value="1" <?php if ($row['epf']==1) { echo "checked";} ?>>
                            <label for="epf_yes">ඇත
                            </label>
                          </div>
                          <div class="icheck-primary d-inline">
                            <input type="radio" id="epf_no" name="epf" value="2" <?php if ($row['epf']==2) { echo "checked";} ?>>
                            <label for="epf_no">නැත
                            </label>
                          </div>                      
                        </div>                      
                      </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="position_id">Rank<span class="text-danger">*</span></label>
                          <select class="form-control select2" style="width: 100%;" name="position_id" id="position_id">
                            <option value="">Select Rank</option>
                            <?php
                            $query="SELECT * FROM position WHERE position_status=0 ORDER BY position_id ASC";
                            $statement = $connect->prepare($query);
                            $statement->execute();
                            $result = $statement->fetchAll();
                            foreach($result as $row_position_id)
                            {
                              ?>
                              <option value="<?php echo $row_position_id['position_id'];?>" <?php if ($row_position_id['position_id']==$position_name['position_id']){ echo "SELECTED";}?>><?php echo $row_position_id['position_abbreviation']; ?></option>
                              <?php
                            }
                            ?>
                          </select>
                        </div>
                        <input type="hidden" name="promo_id" value="<?php echo $position_name['id'] ?>">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-7">
                        <div class="form-group">
                          <label for="full_name">2. Full Name (සම්පුර්ණ නම):</label>
                          <input type="text" class="form-control text-uppercase" id="full_name" name="full_name" value="<?php echo $row['full_name'] ; ?>">
                        </div>
                      </div>

                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="surname">Surname (වාසගම):<span class="text-danger">*</span></label>
                          <input type="text" class="form-control text-uppercase" id="surname" name="surname" value="<?php echo $row['surname'] ; ?>">
                        </div>
                      </div>

                      <div class="col-md-2">
                        <div class="form-group">
                          <label for="initial">Initials (මුලකරු):<span class="text-danger">*</span></label>
                          <input type="text" class="form-control text-uppercase" id="initial" name="initial" value="<?php echo $row['initial'] ; ?>">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="birthday">3. Birthday (උපන් දිනය):</label>
                          <div class="input-group date" id="reservationdate" data-target-input="nearest">
                            <input type="text" name="birthday" id="birthday" class="form-control datetimepicker-input" data-target="#reservationdate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo $row['birthday'] ; ?>"/>
                            <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="nationality">4. Nationality (ජාතිය):</label>
                          <input type="text" class="form-control text-uppercase" id="nationality" name="nationality" value="<?php echo $row['nationality'] ; ?>">
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="contact_no">5. Contact No (දුරකථන අංකය):</label>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="input-group">
                                <div class="input-group-prepend">
                                  <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                </div>
                                <input type="text" class="form-control" data-inputmask='"mask": "9999999999"' data-mask name="mobile_no" id="mobile_no" value="<?php echo $row['mobile_no'] ; ?>">
                              </div>
                              <!-- /.input group -->
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <div class="input-group-prepend">
                                  <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                </div>
                                <input type="text" class="form-control" data-inputmask='"mask": "9999999999"' data-mask name="home_no" id="home_no" value="<?php echo $row['home_no'] ; ?>">
                              </div>
                            <!-- /.input group -->
                            </div>
                          </div>
                        </div>
                      </div> 
                                      
                    </div>

                    <div class="row">                  
                      
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="permanent_address">6. Permanent Address (ස්ථිර ලිපිනය):</label>
                          <input type="text" class="form-control text-uppercase" id="permanent_address" name="permanent_address" value="<?php echo $row['permanent_address'] ; ?>">
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="temporary_address">7. Temparaty Address (තාවකාලික ලිපිනය):</label>
                          <input type="text" class="form-control text-uppercase" id="temporary_address" name="temporary_address" value="<?php echo $row['temporary_address'] ; ?>">
                        </div>
                      </div>                  
                    </div>

                    <div class="row">
                      <div class="col-md-4">
                      <div class="form-group">
                        <label for="districts">8. District (දිස්ත්‍රික්කය):</label>
                        <select class="form-control select2" style="width: 100%;" id="districts" name="districts">
                          <option value="">Select Districts</option>
                          <?php
                          $query="SELECT * FROM districts ORDER BY dis_id ASC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row_dis)
                          {
                            ?>
                            <option value="<?php echo $row_dis['dis_id'];?>"<?php if ($row_dis['dis_id']==$row['dis_id']){ echo "SELECTED";}?>><?php echo $row_dis['districts']; ?></option>
                            <?php
                          }
                          ?>
                        </select>                        
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="ds">8. DS Division (ප්‍රාදේශිය ලේකම් කාර්යාලය):</label>
                        <select class="form-control select2" style="width: 100%;" id="ds" name="ds">
                          <option value="">Select DS</option> 
                          <?php
                          $query="SELECT * FROM ds ORDER BY ds_id ASC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row_ds)
                          {
                            ?>
                            <option value="<?php echo $row_ds['ds_id'];?>"<?php if ($row_ds['ds_id']==$row['ds_id']){ echo "SELECTED";}?>><?php echo $row_ds['ds']; ?></option>
                            <?php
                          }
                          ?>                         
                        </select>                        
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="gramasewa">10. GN & No (ග්‍රා. නි. කොටිඨාශය සහ අංකය):</label>
                        <select class="form-control select2" style="width: 100%;" id="gramasewa" name="gramasewa">
                          <option value="">Select GN</option> 
                          <?php
                          $query="SELECT * FROM gn ORDER BY gn_id ASC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row_gn)
                          {
                            ?>
                            <option value="<?php echo $row_gn['gn_id'];?>"<?php if ($row_gn['gn_id']==$row['gramasewa']){ echo "SELECTED";}?>><?php echo $row_gn['gn']; ?></option>
                            <?php
                          }
                          ?>                         
                        </select>
                       
                      </div>
                    </div>

                    </div>
                                    
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="police">11. Nearest Police Station (ළඟම පොලිස් ස්ථානය):</label>
                        <select class="form-control select2" style="width: 100%;" id="police" name="police">
                          <option value="">Select Police</option>
                          <?php
                          $query="SELECT * FROM police ORDER BY police_id ASC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row_police)
                          {
                            ?>
                            <option value="<?php echo $row_police['police_id'];?>" <?php if ($row_police['police_id']==$row['police']){ echo "SELECTED";}?>><?php echo $row_police['police']; ?></option>
                            <?php
                          }
                          ?>
                        </select>                        
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="married_status">12. Married Status (විවාහක අවිවාහක බව):</label>
                        <div class="form-group clearfix">
                          <div class="icheck-primary d-inline">
                            <input type="radio" id="single" name="married_status" value="1" <?php if ($row['married_status']==1) { echo "checked";} ?>>
                            <label for="single">Single
                            </label>
                          </div>
                          <div class="icheck-primary d-inline">
                            <input type="radio" id="married" name="married_status" value="2" <?php if ($row['married_status']==2) { echo "checked";} ?>>
                            <label for="married">Merried
                            </label>
                          </div>                      
                        </div>
                      </div>
                    </div>
                    <div class="col-md-5">
                      <div class="form-group">
                        <label for="if_service">13. If any forces or other security service (ත්‍රිවිධ හමුදාවේ හෝ පොලීසියේ හෝ වෙනත් ආරක්‍ෂක අංශයක සේවය කර):</label>
                        <div class="form-group clearfix">
                          <div class="icheck-primary d-inline">
                            <input type="radio" id="service" name="if_service" value="1" <?php if ($row['if_service']==1) { echo "checked";} ?>>
                            <label for="service">Yes
                            </label>
                          </div>
                          <div class="icheck-primary d-inline">
                            <input type="radio" id="non_service" name="if_service" value="2" <?php if ($row['if_service']==2) { echo "checked";} ?>>
                            <label for="non_service">No
                            </label>
                          </div>                      
                        </div> 
                      </div>
                    </div>
                  </div>                                

                  <div class="row">
                    <div class="col-md-5">
                      <div class="row">
                        <div class="col-md-12">
                          <label>15. Language proficiency (භාෂා ප්‍රවීණතාවය):</label>
                        </div>                      
                      </div>
                      <?php
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
                          <label>Writing (ලිවීම)</label>
                        </div>
                        <div class="col-md-3">
                          <label>Reading (කියවීම)</label>
                        </div>
                        <div class="col-md-3">
                          <label>Speaking (කථා කිරිම)</label>
                        </div>

                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label>Sinhala (සිංහල)</label>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-primary d-inline">
                                <input type="checkbox" id="sinhala_writing" name="sinhala_writing" value="1" <?php if($sw==1) echo " checked "?>>
                                <label for="sinhala_writing">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-danger d-inline">
                                <input type="checkbox" id="sinhala_reading" name="sinhala_reading" value="1" <?php if($sr==1) echo " checked "?>>
                                <label for="sinhala_reading">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-success d-inline">
                                <input type="checkbox" id="sinhala_speaking" name="sinhala_speaking" value="1" <?php if($ss==1) echo " checked "?>>
                                <label for="sinhala_speaking">
                                </label>
                              </div>                           
                            </div>
                        </div>

                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label>English (ඉංග්‍රීසි)</label>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-primary d-inline">
                                <input type="checkbox" id="english_writing" name="english_writing" value="1" <?php if($ew==1) echo " checked "?>>
                                <label for="english_writing">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-danger d-inline">
                                <input type="checkbox" id="english_reading" name="english_reading" value="1" <?php if($er==1) echo " checked "?>>
                                <label for="english_reading">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-success d-inline">
                                <input type="checkbox" id="english_speaking" name="english_speaking" value="1" <?php if($es==1) echo " checked "?>>
                                <label for="english_speaking">
                                </label>
                              </div>                           
                            </div>
                        </div>

                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label>Tamil (දෙමළ)</label>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-primary d-inline">
                                <input type="checkbox" id="tamil_writing" name="tamil_writing" value="1" <?php if($tw==1) echo " checked "?>>
                                <label for="tamil_writing">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-danger d-inline">
                                <input type="checkbox" id="tamil_reading" name="tamil_reading" value="1" <?php if($tr==1) echo " checked "?>>
                                <label for="tamil_reading">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-success d-inline">
                                <input type="checkbox" id="tamil_speaking" name="tamil_speaking" value="1" <?php if($ts==1) echo " checked "?>>
                                <label for="tamil_speaking">
                                </label>
                              </div>                           
                            </div>
                        </div>
                      </div>  
                    </div>

                    <div class="col-md-7">
                      <div class="row">
                        <div class="col-md-6">
                      <div class="form-group">
                        <label for="join_date">Join Date:</label>
                        <input type="hidden" name="join_id" value="<?php echo $row_join['join_id']; ?>">
                        <div class="input-group date" id="reservationjoindate" data-target-input="nearest">
                          <input type="text" name="join_date" id="join_date" class="form-control datetimepicker-input" data-target="#reservationjoindate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo $row_join['join_date']; ?>"/>
                          <div class="input-group-append" data-target="#reservationjoindate" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="basic_salary">Basic Salary (මුලික වැටුප):</label>
                        <input type="text" class="form-control" id="basic_salary" name="basic_salary" value="<?php echo $row_join['basic_salary']; ?>">
                        <input type="hidden" name="salary_id" value="<?php echo $row_join['id']; ?>">
                      </div>
                    </div>
                      </div>
                      <div class="row">
                        <div class="col-md-8">
                      <div class="form-group">
                        <label for="department_id">Location (රාජකාරි ස්ථානය):</label>
                        <select class="form-control select2" style="width: 100%;" name="department_id" id="department_id">
                          <option value="">Select Location</option>
                          <?php
                          $query="SELECT * FROM department ORDER BY department_id";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row)
                          {
                            ?>
                            <option value="<?php echo $row['department_id']; ?>" <?php if ($row['department_id']==$row_join['location']){ echo "SELECTED";}?>><?php echo $row['department_name'].'-'.$row['department_location']; ?></option>
                            <?php
                          }
                          ?>
                        </select>                        
                      </div>
                    </div>
                      </div>
                    </div>                  
                      </div>    
                      
                    </div>                  
                      <!-- /.card-body -->

                      <div class="card-footer">
                        <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" type="submit" name="update_save"><i class="fas fa-save"></i> Save</button>
                        <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button>
                      </div>

                    </div>
                    <!-- /.card -->
                  </form>
                  <?php
                }
              }else{
                ?>
                <div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>This Cannot be found.</div>
                <?php
              }
            }else{
              ?>
              
              <form action="" id="add_emp_form" method="post">
                <div class="card card-success">
                  <div class="card-header">
                    <h3 class="card-title">Add Employee</h3>                
                  </div>
                    <!-- /.card-header -->
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-5">
                       <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                          <div class="input-group">
                            <div class="image_area ">
                              <label for="upload_image">
                                <img src="/dist/img/avatar5.png" id="uploaded_image" class="img-responsive img-circle" />
                                <div class="overlay">
                                  <div class="text"><!-- Click to  -->Add Employee Image</div>
                                </div>
                                <input type="file" name="image" class="image" id="upload_image" style="display:none" />
                              </label>
                              <input type="hidden" name="cropped_img" id="cropped_img">
                              <input type="hidden" name="file_name" id="file_name">
                            </div>
                          </div>
                        </div>
                      </div>
                      </div>


                    </div>
                    
                    <div class="row">
                      <div class="col-md-4">                        
                        <label for="nic_no">1. NIC No (ජා: හැ: අංකය):<span class="text-danger">*</span></label>
                        <div class="form-group clearfix">
                        <div class="icheck-primary d-inline">
                          <input type="radio" id="nic_no_new" name="nic_no_selection" value="new" checked>
                          <label for="nic_no_new">New NIC
                          </label>
                        </div>
                        <div class="icheck-primary d-inline">
                          <input type="radio" id="nic_no_old" name="nic_no_selection" value="Old">
                          <label for="nic_no_old">Old NIC
                          </label>
                        </div>
                        <div class="icheck-primary d-inline">
                          <input type="radio" id="no_nic" name="nic_no_selection" value="no">
                          <label for="no_nic">No NIC
                          </label>
                        </div>                     
                      </div>
                      <div class="form-group">
                        <div class="form-group" id="nic_no_new_field">
                          <input type="text" class="form-control" id="nic_new" name="nic_new" autocomplete="off" data-inputmask='"mask": "999999999999"' data-mask autofocus>
                        </div>

                        <div class="form-group" style="display: none" id="nic_no_old_field">
                          <input type="text" class="form-control text-uppercase" id="nic_old" name="nic_old" autocomplete="off" data-inputmask='"mask": "999999999*"' data-mask autofocus>
                        </div>

                        <!-- <div class="form-group" style="display: none" id="nic_no_old_field">
                          <input type="text" class="form-control" id="nic_old" name="nic_old" data-inputmask='"mask": "999999999 V"' data-mask autocomplete="off">
                        </div>  -->                         
                          
                        </div>
                      </div>                      

                      <div class="col-md-3">
                        <div class="form-group">
                      <label for="employee_no">Employee No:<span class="text-danger">*</span></label>
                      <div class="form-group clearfix">
                        <div class="icheck-primary d-inline">
                          <input type="radio" id="emp_no" name="emp_no_selection" value="emp" checked>
                          <label for="emp_no">Emp No
                          </label>
                        </div>
                        <div class="icheck-primary d-inline">
                          <input type="radio" id="temp_no" name="emp_no_selection" value="temp">
                          <label for="temp_no">Temp No
                          </label>
                        </div>                                             
                      </div>

                      
                    </div>

                    <?php 
                      $query_no="SELECT employee_no FROM join_status WHERE employee_no REGEXP '^-?[0-9]+$' ORDER BY ABS(employee_no) DESC LIMIT 1";
                      $statement = $connect->prepare($query_no);
                      $statement->execute();
                      $result = $statement->fetchAll();
                      foreach($result as $row_no)
                      {
                        $new_employee_no=$row_no['employee_no']+1;                      
                      }
                      ?>

                      

                    <div class="form-group" id="emp_no_field">
                          <input type="text" class="form-control" id="employee_no" name="employee_no" value="<?php echo $new_employee_no; ?>">
                        </div>
                        <div class="form-group" id="temp_no_field" style="display: none" >
                          <input type="text" class="form-control text-uppercase" id="temporary_no" name="temporary_no">
                        </div>
                      </div>

                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="epf">EPF funds membership (අරමුදල් සමාජිකත්වය):</label>
                          <div class="form-group clearfix">
                            <div class="icheck-primary d-inline">
                              <input type="radio" id="epf_yes" name="epf" value="1" checked>
                              <label for="epf_yes">Yes
                              </label>
                            </div>
                            <div class="icheck-primary d-inline">
                              <input type="radio" id="epf_no" name="epf" value="2">
                              <label for="epf_no">No
                              </label>
                            </div>                      
                          </div>                      
                        </div>
                      </div>

                      <div class="col-md-2">
                        <div class="form-group">
                          <label for="">Rank<span class="text-danger">*</span></label>
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
                      </div>

                    </div>
                    <div class="row">
                      <div class="col-md-7">
                        <div class="form-group">
                          <label for="full_name">2. Full Name (සම්පුර්ණ නම):</label>
                          <input type="text" class="form-control text-uppercase" id="full_name" name="full_name">
                        </div>
                      </div>

                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="surname">Surname (වාසගම):<span class="text-danger">*</span></label>
                          <input type="text" class="form-control text-uppercase" id="surname" name="surname">
                        </div>
                      </div>                    

                      <div class="col-md-2">
                        <div class="form-group">
                          <label for="initial">Initials (මුලකරු):<span class="text-danger">*</span></label>
                          <input type="text" class="form-control text-uppercase" id="initial" name="initial">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="birthday">3. Birthday (උපන් දිනය):</label>
                          <div class="input-group date" id="reservationdate" data-target-input="nearest">
                            <input type="text" name="birthday" id="birthday" class="form-control datetimepicker-input" data-target="#reservationdate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask/>
                            <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="nationality">4. Nationality (ජාතිය):</span></label>
                          <input type="text" class="form-control text-uppercase" id="nationality" name="nationality">
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="contact_no">5. Contact No (දුරකථන අංකය):</label>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="input-group">
                                <div class="input-group-prepend">
                                  <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                </div>
                                <input type="text" class="form-control" data-inputmask='"mask": "9999999999"' data-mask name="mobile_no" id="mobile_no">
                              </div>
                              <!-- /.input group -->
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <div class="input-group-prepend">
                                  <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                </div>
                                <input type="text" class="form-control" data-inputmask='"mask": "9999999999"' data-mask name="home_no" id="home_no">
                              </div>
                            <!-- /.input group -->
                            </div>
                          </div>
                        </div>
                      </div> 
                                    
                    </div>

                    <div class="row">                  
                      
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="permanent_address">6. Permanent Address (ස්ථිර ලිපිනය):</label>
                          <input type="text" class="form-control text-uppercase" id="permanent_address" name="permanent_address">
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="temporary_address">7. Temparaty Address (තාවකාලික ලිපිනය):</label>
                          <input type="text" class="form-control text-uppercase" id="temporary_address" name="temporary_address">
                        </div>
                      </div>                  
                    </div>

                    <div class="row">
                      <div class="col-md-4">
                      <div class="form-group">
                        <label for="districts">8. District (දිස්ත්‍රික්කය):</label>
                        <select class="form-control select2" style="width: 100%;" id="districts" name="districts">
                          <option value="">Select Districts</option>
                          <?php
                          $query="SELECT * FROM districts ORDER BY dis_id ASC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row_dis)
                          {
                            ?>
                            <option value="<?php echo $row_dis['dis_id'];?>"><?php echo $row_dis['districts']; ?></option>
                            <?php
                          }
                          ?>
                        </select>                        
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="ds">8. DS Division (ප්‍රාදේශිය ලේකම් කාර්යාලය):</label>
                        <select class="form-control select2" style="width: 100%;" id="ds" name="ds">
                          <option value="">First Select District</option>                          
                        </select>                        
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="gramasewa">10. GN & No (ග්‍රා. නි. කොටිඨාශය සහ අංකය):</label>
                        <select class="form-control select2" style="width: 100%;" id="gramasewa" name="gramasewa">
                          <option value="">First Select DS</option>                          
                        </select>                        
                      </div>
                    </div>

                  </div>
                                      
                  <div class="row">                   

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="police">11. Nearest Police Station (ළඟම පොලිස් ස්ථානය):</label>
                        <select class="form-control select2" style="width: 100%;" id="police" name="police">
                          <option value="">Select Police</option>
                          <!-- <?php
                          $query="SELECT * FROM police ORDER BY police_id ASC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row_police)
                          {
                            ?>
                            <option value="<?php echo $row_police['police_id'];?>"><?php echo $row_police['police']; ?></option>
                            <?php
                          }
                          ?> -->
                        </select>                        
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="married_status">12. Married Status (විවාහක අවිවාහක බව):</label>
                        <div class="form-group clearfix">
                          <div class="icheck-primary d-inline">
                            <input type="radio" id="single" name="married_status" value="1" checked>
                            <label for="single">Single
                            </label>
                          </div>
                          <div class="icheck-primary d-inline">
                            <input type="radio" id="married" name="married_status" value="2">
                            <label for="married">Merried
                            </label>
                          </div>                      
                        </div>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <label for="if_service">13. If any forces or other security service (ත්‍රිවිධ හමුදාවේ හෝ පොලීසියේ හෝ වෙනත් ආරක්‍ෂක අංශයක සේවය කර):</label>
                        <div class="form-group clearfix">
                          <div class="icheck-primary d-inline">
                            <input type="radio" id="service" name="if_service" value="1" checked>
                            <label for="service">Yes
                            </label>
                          </div>
                          <div class="icheck-primary d-inline">
                            <input type="radio" id="non_service" name="if_service" value="2">
                            <label for="non_service">No
                            </label>
                          </div>                      
                        </div>                        
                      </div>
                    </div>      
                  </div>                  

                  <div class="row">
                    <div class="col-md-12">
                    <div class="info-box bg-secondary">
                        <div class="info-box-content">
                          <label>14. Bank Detals (බැංකු විස්තරය):</label>
                          <div class="row">
                            <div class="col-md-3">
                      <div class="form-group">
                        <label for="holder_name">Holder Name:</label>
                        <input type="text" class="form-control" id="holder_name" name="holder_name">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="bank_name">Bank Name (බැංකුවේ නම):</label>
                        <select class="form-control select2" style="width: 100%;" id="bank_name" name="bank_name">
                          <option value="">Select Bank</option>
                          <?php
                          $query="SELECT * FROM bank_name ORDER BY bank_name ASC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row_bank)
                          {
                            ?>
                            <option value="<?php echo $row_bank['id'];?>"><?php echo $row_bank['bank_name'].' ('.$row_bank['bank_no'].')'; ?></option>
                            <?php
                          }
                          ?>
                        </select>                        
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="bank_branch">Branch (ශාඛාව):</label>
                        <select class="form-control select2" style="width: 100%;" id="bank_branch" name="bank_branch">
                          <option value="">Select Branch</option>                          
                        </select>                        
                      </div>
                    </div>
                    
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="account_no">Account no (ගිණුම් අංකය):</label>
                        <input type="text" class="form-control" id="account_no" name="account_no">
                      </div>
                    </div>
                    </div>
                  </div>
                </div>
              </div>
              </div>
              

                  <div class="row">



                    <div class="col-md-5">
                      <div class="row">
                        <div class="col-md-12">
                          <label>15. Language proficiency (භාෂා ප්‍රවීණතාවය):</label>
                        </div>                      
                      </div>
                      
                      <div class="row">
                        <div class="col-md-3">
                          
                        </div>
                        <div class="col-md-3">
                          <label>Writing (ලිවීම)</label>
                        </div>
                        <div class="col-md-3">
                          <label>Reading (කියවීම)</label>
                        </div>
                        <div class="col-md-3">
                          <label>Speaking (කථා කිරිම)</label>
                        </div>

                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label>Sinhala (සිංහල)</label>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-primary d-inline">
                                <input type="checkbox" id="sinhala_writing" name="sinhala_writing" value="1">
                                <label for="sinhala_writing">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-danger d-inline">
                                <input type="checkbox" id="sinhala_reading" name="sinhala_reading" value="1">
                                <label for="sinhala_reading">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-success d-inline">
                                <input type="checkbox" id="sinhala_speaking" name="sinhala_speaking" value="1">
                                <label for="sinhala_speaking">
                                </label>
                              </div>                           
                            </div>
                        </div>

                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label>English (ඉංග්‍රීසි)</label>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-primary d-inline">
                                <input type="checkbox" id="english_writing" name="english_writing" value="1">
                                <label for="english_writing">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-danger d-inline">
                                <input type="checkbox" id="english_reading" name="english_reading" value="1">
                                <label for="english_reading">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-success d-inline">
                                <input type="checkbox" id="english_speaking" name="english_speaking" value="1">
                                <label for="english_speaking">
                                </label>
                              </div>                           
                            </div>
                        </div>

                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label>Tamil (දෙමළ)</label>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-primary d-inline">
                                <input type="checkbox" id="tamil_writing" name="tamil_writing" value="1">
                                <label for="tamil_writing">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-danger d-inline">
                                <input type="checkbox" id="tamil_reading" name="tamil_reading" value="1">
                                <label for="tamil_reading">
                                </label>
                              </div>                           
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group clearfix">
                              <div class="icheck-success d-inline">
                                <input type="checkbox" id="tamil_speaking" name="tamil_speaking" value="1">
                                <label for="tamil_speaking">
                                </label>
                              </div>                           
                            </div>
                        </div>
                      </div>  
                    </div>

                    <div class="col-md-7">
                      <div class="row">
                        <div class="col-md-6">
                      <div class="form-group">
                        <label for="join_date">Join Date:</label>
                        <div class="input-group date" id="reservationjoindate" data-target-input="nearest">
                          <input type="text" name="join_date" id="join_date" class="form-control datetimepicker-input" data-target="#reservationjoindate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo date("Y-m-d"); ?>"/>
                          <div class="input-group-append" data-target="#reservationjoindate" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="basic_salary">Basic Salary (මුලික වැටුප):</label>
                        <input type="text" class="form-control" id="basic_salary" name="basic_salary">
                      </div>
                    </div>
                      </div>
                      <div class="row">
                        <div class="col-md-8">
                      <div class="form-group">
                        <label for="department_id">Location (රාජකාරි ස්ථානය):</label>
                        <select class="form-control select2" style="width: 100%;" name="department_id" id="department_id">
                          <option value="">Select Location</option>
                          <?php
                          $query="SELECT * FROM department ORDER BY department_id";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row)
                          {
                            ?>
                            <option value="<?php echo $row['department_id']; ?>"><?php echo $row['department_name'].'-'.$row['department_location']; ?></option>
                            <?php
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                      </div>
                    </div>

                    


                  </div>                  
                  
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" type="submit" name="add_save"><i class="fas fa-save"></i> Save</button>
                  <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button>
                </div>

              </div>
              <!-- /.card -->
            </form>
              <?php
            }
            
            ?>            
          </div>          
        </div>
        <!-- /.row -->

        <!-- Profile Modal-->

      <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Crop Image Before Upload</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">×</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="img-container">
                      <div class="row">
                          <div class="col-md-8 col-sm-8 col-xs-6">
                              <img src="" id="sample_image" />
                          </div>
                          <div class="col-md-4 col-sm-4 col-xs-2">
                              <div class="preview"></div>
                          </div>
                      </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" id="crop" class="btn btn-primary" data-dismiss="modal">Crop</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
          </div>
      </div>  

       
      
      </div><!-- /.container-fluid -->
    </section>    

<?php
include '../inc/footer.php';
?>

<script src="/plugins/bs-stepper/main.js"></script>
<script>
$(function () {
  
  $('#add_emp_form').validate({
    rules: {     
      surname: {required: true},
      initial: {required: true},
      nic_new: {required: true, 
        remote: {
          url: "/check_nic_no",
          type: "post"
          }},
      nic_old: {required: true,
      remote: {
          url: "/check_nic_old",
          type: "post"
          }
        },
      position_id: {required: true},      
      employee_no: {required: true, 
        remote: {
          url: "/check_employee_no",
          type: "post"
          }
        },
        basic_salary: {required: true, number:true},
        department_id: {required: true}      
      
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

<script>
$(document).ready(function(){

  var $modal = $('#modal');

  var image = document.getElementById('sample_image');

  var cropper;

  $('#upload_image').change(function(event){
    var files = event.target.files;
    $('#file_name').attr('value',this.files[0].name);

    var done = function(url){
      image.src = url;
      $modal.modal('show');
    };

    if(files && files.length > 0)
    {
      reader = new FileReader();
      reader.onload = function(event)
      {
        done(reader.result);
      };
      reader.readAsDataURL(files[0]);
    }else{
       $('#upload_image').attr('required');
    }
  });

  $modal.on('shown.bs.modal', function() {
    cropper = new Cropper(image, {
      aspectRatio: 1,
      viewMode: 3,
      preview:'.preview'
    });
  }).on('hidden.bs.modal', function(){
    cropper.destroy();
      cropper = null;
  });

  $('#crop').click(function(){
    canvas = cropper.getCroppedCanvas({
      width:400,
      height:600
    });

    canvas.toBlob(function(blob){
      url = URL.createObjectURL(blob);
      var reader = new FileReader();
      reader.readAsDataURL(blob);
      reader.onloadend = function(){
        var base64data = reader.result;
        $('#uploaded_image').attr('src', base64data);
        $('#cropped_img').attr('value', base64data);


        // $.ajax({
        //  url:'upload.php',
        //  method:'POST',
        //  data:{image:base64data},
        //  success:function(data)
        //  {
        //    $modal.modal('hide');
        //    $('#uploaded_image').attr('src', data);
        //  }
        // });
      };
    });
  });
});
</script>

<script type="text/javascript">
  $(function () {
    $("input[name='nic_no_selection']").click(function () {
      if ($("#nic_no_new").is(":checked")) {
          $("#nic_no_new_field").show();
          $('#nic_new').attr('required','');
          $('#nic_new').attr('focus', true);
          $('#nic_new').attr('data-error', 'This field is required.');
          $('#nic_new').val('');            
      } else {
          $("#nic_no_new_field").hide();
          $('#nic_new').removeAttr('required');
          $('#nic_new').removeAttr('data-error');
          $('#nic_new').removeAttr('focus');
          $('#nic_new').val('')
      }
      if ($("#nic_no_old").is(":checked")) {
          $("#nic_no_old_field").show();
          $('#nic_old').attr('required','');
          $('#nic_old').attr('focus', true);
          $('#nic_old').attr('data-error', 'This field is required.');
          $('#nic_old').val('');            
      } else {
          $("#nic_no_old_field").hide();
          $('#nic_old').removeAttr('required');
          $('#nic_old').removeAttr('focus');
          $('#nic_old').removeAttr('data-error');
          $('#nic_old').val('');          
      }
        
    });

    $("input[name='emp_no_selection']").click(function () {
      if ($("#emp_no").is(":checked")) {
          $("#emp_no_field").show();
          $('#employee_no').attr('required','');
          $('#employee_no').attr('focus', true);
          $('#employee_no').attr('data-error', 'This field is required.');
          $('#employee_no').val('');
      } else {
          $("#emp_no_field").hide();
          $('#employee_no').removeAttr('required');
          $('#employee_no').removeAttr('data-error');
          $('#employee_no').removeAttr('focus');
          $('#employee_no').val('');
      }
      if ($("#temp_no").is(":checked")) {
          $("#temp_no_field").show();
          $('#temporary_no').attr('required','');
          $('#temporary_no').attr('focus', true);
          $('#temporary_no').attr('data-error', 'This field is required.');
          $('#temporary_no').val('');            
      } else {
          $("#temp_no_field").hide();
          $('#temporary_no').removeAttr('required');
          $('#temporary_no').removeAttr('focus');
          $('#temporary_no').removeAttr('data-error');
          $('#temporary_no').val('');          
      }
        
    });   
      
  });
    
</script>

<script type="text/javascript">
  // ajax script for getting state data
   $(document).on('change','#districts', function(){
      var districtsID = $(this).val();
      if(districtsID){
          $.ajax({
              type:'POST',
              url:'/backend-script',
              data:{'districts_id':districtsID,'request':1},
              success:function(result){
                  $('#ds').html(result);
                 
              }
          });
          $.ajax({
              type:'POST',
              url:'/backend-script',
              data:{'districts_id':districtsID,'request':2},
              success:function(result){
                  $('#police').html(result);
                 
              }
          }); 
      }else{
          $('#ds').html('<option value="">First Select Districts</option>');
          $('#police').html('<option value="">First Select Districts</option>');
          $('#gramasewa').html('<option value="">First Select DS</option>'); 
      }
  });
    // ajax script for getting  city data
   $(document).on('change','#ds', function(){
      var dsID = $(this).val();
      if(dsID){
          $.ajax({
              type:'POST',
              url:'/backend-script',
              data:{'ds_id':dsID},
              success:function(result){
                  $('#gramasewa').html(result);
                 
              }
          }); 
      }else{
          $('#gramasewa').html('<option value="">Select GN</option>');
          
      }
  });

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

</script>