<?php 
session_start();
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

if (isset($_POST['add_save'])){

  if (checkPermissions($_SESSION["user_id"], 25) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';  
    header('location:/dashboard');  
    exit();
  }

  $start_date=date('Y-m-d', strtotime($_POST['start_date']));
  $end_date=date('Y-m-t', strtotime($start_date));

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

  $statement = $connect->prepare("SELECT * FROM d_attendance WHERE employee_id='".$employee_id."' AND department_id='".$_GET['dep_id']."' AND position_id='".$_POST['position_id']."' AND start_date='".$start_date."' AND end_date='".$end_date."'");
  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>This details Already existing.</div>';
  }

  if (!$error) {

  $data = array(
      ':employee_id'    =>  $employee_id,
      ':department_id'  =>  $_GET['dep_id'],
      ':position_id'    =>  $_POST['position_id'],
      ':start_date'     =>  $start_date,
      ':end_date'       =>  $end_date,
      ':no_of_shifts'   =>  $_POST['no_of_shifts'], 
      ':total_ot_hrs'   =>  $_POST['total_ot_hrs'],
      ':poya_day'       =>  $_POST['poya_day'],
      ':m_day'          =>  $_POST['m_day'],
      ':m_ot_hrs'       =>  $_POST['m_ot_hrs'], 
      ':extra_ot_hrs'   =>  $_POST['extra_ot_hrs'],          
  );
 
  $query = "
  INSERT INTO d_attendance(employee_id, department_id, position_id, start_date, end_date, no_of_shifts, poya_day, m_day, m_ot_hrs, total_ot_hrs, extra_ot_hrs)
  VALUES (:employee_id, :department_id, :position_id, :start_date, :end_date, :no_of_shifts, :poya_day, :m_day, :m_ot_hrs, :total_ot_hrs, :extra_ot_hrs)
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

  $query = "DELETE FROM `d_attendance` WHERE `id`=:id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Delete Success.</div>';
    header('location:/institution_list/institution/d_attendance/'.$_GET['dep_id'].'');            
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
              <li class="breadcrumb-item active">Dummy Attendance</li>
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

            if(isset($_GET['dep_id'])):
            
              $query = 'SELECT * FROM department WHERE department_id="'.$_GET['dep_id'].'"';

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
                  <h3 class="card-title">Dummy Attendance - <?php echo $row['department_name'].'-'.$row['department_location']; ?></h3>                              
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
                      <!-- <div class="total_shifts" style="justify-content: center;" ></div> -->
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
                    $query="SELECT j.join_id, e.surname, e.initial, j.employee_no, p.position_abbreviation FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN position p ON e.position_id=p.position_id WHERE j.employee_status=0 OR j.employee_status=2 ORDER BY e.employee_id DESC";
                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $result = $statement->fetchAll();
                    foreach($result as $row)
                    {
                      ?>
                      <option value="<?php echo $row['join_id']; ?>"><?php echo $row['employee_no'].' '.$row['position_abbreviation'].' '.$row['surname'].' '.$row['initial']; ?></option>
                      <?php
                    }
                    ?>
                  </select>

                  </div> 

                  <div id="dis_emp_name" class="form-group">
                    <span id="employee_name" class="text-success"></span>
                  </div>

                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="no_of_shifts">Total Working Shifts</label>
                        <input type="text" class="form-control" id="no_of_shifts" name="no_of_shifts" autocomplete="off" >
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="total_ot_hrs">Total OT Hrs</label>
                        <input type="text" class="form-control" id="total_ot_hrs" name="total_ot_hrs" autocomplete="off" >
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="poya_day">Poya Days</label>
                        <input type="text" class="form-control" id="poya_day" name="poya_day" autocomplete="off" >
                      </div>
                    </div>
                    
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="m_day">Mercantile Days</label>
                        <input type="text" class="form-control" id="m_day" name="m_day" autocomplete="off" >
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="m_ot_hrs">Mercantile OT Hrs</label>
                        <input type="text" class="form-control" id="m_ot_hrs" name="m_ot_hrs" autocomplete="off" >
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="extra_ot_hrs">Extra OT Hrs</label>
                        <input type="text" class="form-control" id="extra_ot_hrs" name="extra_ot_hrs" autocomplete="off" >
                      </div>
                    </div>
                  </div>
                      
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                            <label for="">Position Name</label>
                          </div>
                          <div class="row">
                       
                           <?php
                              $query="SELECT position_id, position_abbreviation FROM position ORDER BY position_id";
                              $statement = $connect->prepare($query);
                              $statement->execute();
                              $result = $statement->fetchAll();
                              foreach($result as $row)
                              {
                                ?><div class="col-md-3">
                                <div class="form-group clearfix">
                                    <div class="icheck-success d-inline">
                                      <input type="radio" id="radioPrimary<?php echo $row['position_id']; ?>" name="position_id" value="<?php echo $row['position_id']; ?>">
                                      <label for="radioPrimary<?php echo $row['position_id']; ?>"><?php echo $row['position_abbreviation']; ?>
                                      </label>
                                    </div>
                                  </div>     
                                  </div>                           
                                <?php
                              }
                              ?>
                          </div>
                          <!-- <div class="filter_data" style="justify-content: center;" ></div> -->
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
            
                          <input type="hidden" id="department_id" name="department_id" value="<?php echo $_GET['dep_id'];?>">
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
                  <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="add_save"> Save</button>
                  <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button>
                </div>

              </div>
              <!-- /.card -->
            </form>
            
            <div class="row">
              <div class="col-md-12">
            <table class="table table-sm table-bordered table-striped">
            <thead>
              <tr style="text-align:center;">
                <th>#</th>
                <th>Employee Name</th>
                <th>Position Name</th>
                <th>Working Days</th>
                <th>Total OT Hrs</th>
                <th>Poya Days</th>
                <th>Mercantile Days</th>
                <th>Mercantile OT Hrs</th>
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
          url: "/check_employee_id_d",
          type: "post"
        }
      },

      nic_new1: { 
        remote: {
          url: "/check_employee_id_d",
          type: "post"
        }
      },

      nic_old1: { 
        remote: {
          url: "/check_employee_id_d",
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
total_shifts();

setInterval(function(){
    load_attendance();   
    filter_data();
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
      data:{start_date:start_date, department_id:department_id},
      success:function(data){
          $('.filter_data').html(data);
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
    data:{query:query,query_new_nic:query_new_nic,query_nic_old:query_nic_old,request:29},
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
    data:{department_id:department_id,start_date:start_date,request:15},
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
          html += '<td style="width:10%;"><center>'+serial_no+'</center></td>';
          html += '<td style="width:40%;">'+response[count].emp_name+'</td>';
          html += '<td style="width:20%;">'+response[count].position_name+'</td>';
          html += '<td style="text-align:right; width:15%;">'+response[count].no_of_shifts+'</td>'; 
          html += '<td style="text-align:right; width:15%;">'+response[count].total_ot_hrs+'</td>';
          html += '<td style="text-align:right; width:15%;">'+response[count].poya_day+'</td>';
          html += '<td style="text-align:right; width:15%;">'+response[count].m_day+'</td>';
          html += '<td style="text-align:right; width:15%;">'+response[count].m_ot_hrs+'</td>';
          html += '<td style="text-align:right; width:15%;">'+response[count].extra_ot_hrs+'</td>';
          html += '<td style="text-align:right; width:10%;"><center>'+response[count].action+'</center></td>';
          html += '</tr>'; 
          serial_no++;            
        }
      }
      else
      {
        html += '<tr><td colspan="10" class="text-center">No Data Found</td></tr>';
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