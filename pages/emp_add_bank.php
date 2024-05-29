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

  $employee_id=  $_GET['bank'];
  $bank_name  =  $_POST['bank_name'];
  $account_no =  $_POST['account_no'];
  $statement = $connect->prepare("SELECT employee_id, account_no, bank_name FROM bank_details WHERE employee_id=:employee_id AND bank_name=:bank_name AND account_no=:account_no");
  $statement->bindParam(':employee_id', $employee_id);
  $statement->bindParam(':bank_name', $bank_name);
  $statement->bindParam(':account_no', $account_no);

  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $errMSG = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>This Bank Details Already existing.</div>';
  }

  if (!$error) {

    $data = array(
      ':employee_id'        =>  $_GET['bank'],
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


    $data = array(
      ':employee_id'        =>  $_GET['edit'],
      ':full_name'          =>  $_POST['full_name'],
      ':name_with_initial'  =>  $_POST['name_with_initial'],
      ':position_id'           =>  $_POST['position_id'],
      ':birthday'           =>  $_POST['birthday'],
      ':birth_place'        =>  $_POST['birth_place'],
      ':nationality'        =>  $_POST['nationality'],
      ':citizenship'        =>  $_POST['citizenship'],
      ':nic_no'             =>  $nic_no,
      ':permanent_address'  =>  $_POST['permanent_address'],
      ':temporary_address'  =>  $_POST['temporary_address'],
      ':mobile_no'          =>  $_POST['mobile_no'],
      ':home_no'            =>  $_POST['home_no'],
      ':e_name'             =>  $_POST['e_name'],
      ':e_address'          =>  $_POST['e_address'],
      ':e_contact_no'       =>  $_POST['e_contact_no'],
      ':e_relation'         =>  $_POST['relationship'],
      ':gramasewa'          =>  $_POST['gramasewa'],
      ':police'             =>  $_POST['police'],
      ':married_status'     =>  $_POST['married_status'],
      ':if_service'         =>  $_POST['if_service'],
      ':languages'              =>  json_encode($languages),
      ':epf'                      =>  $_POST['epf'],
    );
   
    $query = "
    UPDATE `employee` SET `full_name`=:full_name, `name_with_initial`=:name_with_initial, `position_id`=:position_id, `birthday`=:birthday, `birth_place`=:birth_place, `nationality`=:nationality, `citizenship`=:citizenship, `nic_no`=:nic_no, `permanent_address`=:permanent_address, `temporary_address`=:temporary_address, `mobile_no`=:mobile_no, `home_no`=:home_no, `e_name`=:e_name, `e_address`=:e_address, `e_contact_no`=:e_contact_no, `e_relation`=:e_relation,  `gramasewa`=:gramasewa, `police`=:police, `married_status`=:married_status, `if_service`=:if_service, `relation_name`=:relation_name, `children_siblings`=:children_siblings, `gceol`=:gceol, `gceal`=:gceal, `c_organization`=:c_organization, `c_etf_no`=:c_etf_no, `c_epf_no`=:c_epf_no, `relatives_work_company`=:relatives_work_company, `convicted_court`=:convicted_court, `legal_checks`=:legal_checks, `disability`=:disability, `languages`=:languages, `professional_qualification`=:professional_qualification, `like_work_district`=:like_work_district, `like_work_place`=:like_work_place, `epf`=:epf WHERE `employee_id`=:employee_id
    ";
            
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
                  ?>
                  <form action="" id="" method="post">
                    <div class="card card-danger">
                      <div class="card-header">
                        <h3 class="card-title">Edit Employee</h3>                
                      </div>
                        <!-- /.card-header -->
                      <div class="card-body">
                        <div class="row">
                      <div class="col-md-3">                        
                        <label for="nic_no">1. ජා: හැ: අංකය:</label>
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
                          <input type="text" class="form-control" id="nic_new" name="nic_new" autocomplete="off" data-inputmask='"mask": "999999999999"' data-mask autofocus value="<?php if (strlen($row['nic_no'])==12) { echo $row['nic_no'];} ?>">
                        </div>

                        <div class="form-group" <?php if (strlen($row['nic_no'])!=12) { echo 'style="display: block"';}else{ echo 'style="display: none"';} ?>  id="nic_no_old_field">
                          <input type="text" class="form-control" id="nic_old" name="nic_old" autocomplete="off" data-inputmask='"mask": "999999999 V"' data-mask autofocus value="<?php if (strlen($row['nic_no'])!=12) { echo $row['nic_no'];} ?>">
                        </div>                                              
                          
                        </div>
                      </div>

                      <div class="col-md-3">
                        <div class="form-group">
                            <label for="epf">EPF අරමුදල් සමාජිකත්වය</label>
                            
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
                          <label for="position_id">Rank*</label>
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
                              <option value="<?php echo $row_position_id['position_id'];?>" <?php if ($row_position_id['position_id']==$row['position_id']){ echo "SELECTED";}?>><?php echo $row_position_id['position_abbreviation']; ?></option>
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
                          <label for="full_name">2. සම්පුර්ණ නම:</label>
                          <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $row['full_name'] ; ?>">
                        </div>
                      </div>

                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="surname">වාසගම:</label>
                          <input type="text" class="form-control" id="surname" name="surname" value="<?php echo $row['surname'] ; ?>">
                        </div>
                      </div>

                      <div class="col-md-2">
                        <div class="form-group">
                          <label for="initial">මුලකරු:</label>
                          <input type="text" class="form-control" id="initial" name="initial" value="<?php echo $row['initial'] ; ?>">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="birthday">3. උපන් දිනය:</label>
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
                          <label for="nationality">4. ජාතිය:</label>
                          <input type="text" class="form-control" id="nationality" name="nationality" value="<?php echo $row['nationality'] ; ?>">
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="contact_no">5. දුරකථන අංකය:</label>
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
                          <label for="permanent_address">6. ස්ථිර ලිපිනය:</label>
                          <input type="text" class="form-control" id="permanent_address" name="permanent_address" value="<?php echo $row['permanent_address'] ; ?>">
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="temporary_address">7. තාවකාලික ලිපිනය:</label>
                          <input type="text" class="form-control" id="temporary_address" name="temporary_address" value="<?php echo $row['temporary_address'] ; ?>">
                        </div>
                      </div>                  
                    </div>

                    <div class="row">
                      <div class="col-md-4">
                      <div class="form-group">
                        <label for="districts">8. දිස්ත්‍රික්කය:*</label>
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
                        <label for="ds">8. ප්‍රාදේශිය ලේකම් කාර්යාලය:*</label>
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
                        <label for="gramasewa">10. ග්‍රාමසේවා කොටිඨාශය සහ අංකය:*</label>
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
                            <option value="<?php echo $row_gn['gn_id'];?>"<?php if ($row_gn['gn_id']==$row['gn_id']){ echo "SELECTED";}?>><?php echo $row_gn['gn']; ?></option>
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
                        <label for="police">11. ළඟම පොලිස් ස්ථානය:</label>
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
                        <label for="married_status">12. විවාහක අවිවාහක බව:</label>
                        <div class="form-group clearfix">
                          <div class="icheck-primary d-inline">
                            <input type="radio" id="single" name="married_status" value="1" <?php if ($row['married_status']==1) { echo "checked";} ?>>
                            <label for="single">අවිවාහක
                            </label>
                          </div>
                          <div class="icheck-primary d-inline">
                            <input type="radio" id="married" name="married_status" value="2" <?php if ($row['married_status']==2) { echo "checked";} ?>>
                            <label for="married">විවාහක
                            </label>
                          </div>                      
                        </div>
                      </div>
                    </div>
                    <div class="col-md-5">
                      <div class="form-group">
                        <label for="if_service">13. ත්‍රිවිධ හමුදාවේ හෝ පොලීසියේ හෝ වෙනත් ආරක්‍ෂක අංශයක සේවය කර:</label>
                        <div class="form-group clearfix">
                          <div class="icheck-primary d-inline">
                            <input type="radio" id="service" name="if_service" value="1" <?php if ($row['if_service']==1) { echo "checked";} ?>>
                            <label for="service">ඇත
                            </label>
                          </div>
                          <div class="icheck-primary d-inline">
                            <input type="radio" id="non_service" name="if_service" value="2" <?php if ($row['if_service']==2) { echo "checked";} ?>>
                            <label for="non_service">නැත
                            </label>
                          </div>                      
                        </div> 
                      </div>
                    </div>
                  </div>                                

                  <div class="row">
                    <div class="col-md-4">
                      <div class="row">
                        <div class="col-md-12">
                          <label>14. භාෂා ප්‍රවීණතාවය</label>
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
                          <label>ලිවීම</label>
                        </div>
                        <div class="col-md-3">
                          <label>කියවීම</label>
                        </div>
                        <div class="col-md-3">
                          <label>කථා කිරිම</label>
                        </div>

                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label>සිංහල</label>
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
                          <label>ඉංග්‍රීසි</label>
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
                          <label>දෙමළ</label>
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
                      </div>    
                      
                    </div>                  
                      <!-- /.card-body -->

                      <div class="card-footer">
                        <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="update_save"> Save</button>
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
              
              <form action="" id="add_bank_form" method="post">
                <div class="card card-success">
                  <div class="card-header">
                    <h3 class="card-title">Add Bank Details</h3>                
                  </div>
                    <!-- /.card-header -->
                  <div class="card-body">   

                    <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="bank_name">Bank Name:</label>
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
                        <label for="bank_branch">Branch:</label>                        
                        <input type="text" class="form-control" id="bank_branch" name="bank_branch">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="bank_branch_no">Branch No:</label>
                        <input type="text" class="form-control" id="bank_branch_no" name="bank_branch_no">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="account_no">Account No:</label>
                        <input type="text" class="form-control" id="account_no" name="account_no">
                      </div>
                    </div>
                    </div> 
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="add_save"> Save</button>
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
       
      
      </div><!-- /.container-fluid -->
    </section>    

<?php
include '../inc/footer.php';
?>

<script src="/plugins/bs-stepper/main.js"></script>
<script>
$(function () {
  
  $('#add_bank_form').validate({
    rules: {
      bank_name: { required: true},
      branch_name: {required: true},
      branch_no: {required: true},
      account_no: {required: true}  
      
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
          $('#ds').html('<option value="">Select Districts</option>');
          $('#police').html('<option value="">Select Districts</option>');
          $('#gramasewa').html('<option value="">Select DS</option>'); 
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
</script>