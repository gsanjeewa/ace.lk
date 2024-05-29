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

if (isset($_POST['add_save'])){

if (checkPermissions($_SESSION["user_id"], 25) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/attendance_list/add_attendance');
    exit();
}

  $employee_no  =  $_POST['employee_id'];
  $query = "SELECT join_id FROM join_status WHERE employee_no = '".$employee_no."'
      ";
  $statement = $connect->prepare($query);
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
  foreach($result as $row)
  {
    $employee_id=$row['join_id'];
  }

$start_date=date('Y-m-d', strtotime("first day of -1 month"));
$end_date=date('Y-m-d', strtotime("last day of -1 month"));

  $data = array(
      ':employee_id'    =>  $employee_id,
      ':department_id'  =>  $_POST['department_id'],
      ':position_id'    =>  $_POST['position_id'],
      ':start_date'     =>  $start_date,
      ':end_date'       =>  $end_date,
      ':no_of_shifts'   =>  $_POST['no_of_shifts'],
      ':extra_ot_hrs'   =>  $_POST['extra_ot_hrs'],            
  );
 
  $query = "
  INSERT INTO `attendance`(`employee_id`, `department_id`, `position_id`, `start_date`, `end_date`, `no_of_shifts`, `extra_ot_hrs`)
  VALUES (:employee_id, :department_id, :position_id, :start_date, :end_date, :no_of_shifts, :extra_ot_hrs)
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



include '../inc/header.php';
?>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Attendance</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
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
          <div class="col-md-6">
            
              <form action="" id="formattendance" method="post">
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Add Attendance</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">
                  <?php
                  // Today being 2012-05-31
//All the following return 2012-04-30
/*echo date('Y-m-d', strtotime("last day of -1 month"));
echo date('Y-m-d', strtotime("last day of last month"));
echo date_create("last day of -1 month")->format('Y-m-d'); */

// All the following return 2012-04-01
/*echo date('Y-m-d', strtotime("first day of -1 month")); 
echo date('Y-m-d', strtotime("first day of last month"));
echo date_create("first day of -1 month")->format('Y-m-d');*/
                  ?>

                  <div class="form-group">
                            <label for="employee_id">Service No </label>
                            <input type="text" class="form-control" id="employee_id" name="employee_id" autofocus autocomplete="off"> 
                            <span id="employee_name" class="text-success"></span>
                          </div>

                          <div class="form-group">
                            <label for="department_id">Institution</label>                            
                            <select class="form-control select2" style="width: 100%;" name="department_id" id="department_id">
                              <?php
                              $query="SELECT * FROM department ORDER BY department_id";
                              $statement = $connect->prepare($query);
                              $statement->execute();
                              $result = $statement->fetchAll();
                              foreach($result as $row)
                              {
                                ?>
                                <option value="<?php echo $row['department_id']; ?>"><?php echo $row['department_name']; ?></option>
                                <?php
                              }
                              ?>
                            </select>
                          </div>

                          <div class="form-group">
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
                          </div>

                          <div class="form-group">
                        <label for="no_of_shifts">No of Shifts</label>
                        <input type="text" class="form-control" id="no_of_shifts" name="no_of_shifts" autocomplete="off" >
                      </div>

                      <div class="form-group">
                        <label for="extra_ot_hrs">Extra OT Hrs</label>
                        <input type="text" class="form-control" id="extra_ot_hrs" name="extra_ot_hrs" autocomplete="off" >
                      </div>


                  <!-- <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="start_date" class="control-label">Start Date</label>
                        <div class="input-group date" id="reservationstartdate" data-target-input="nearest">
                            <input type="text" name="start_date" id="start_date" class="form-control datetimepicker-input" data-target="#reservationstartdate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask/>
                            <div class="input-group-append" data-target="#reservationstartdate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                            </div>
                          </div>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="end_date" class="control-label">End Date</label>
                        <div class="input-group date" id="reservationenddate" data-target-input="nearest">
                            <input type="text" name="end_date" id="end_date" class="form-control datetimepicker-input" data-target="#reservationenddate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask/>
                            <div class="input-group-append" data-target="#reservationenddate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                            </div>
                          </div>
                      </div>
                    </div>

                    <div class="col-md-3">
                       
                    </div>
                  </div>      -->

                </div>
                <!-- /.card-body -->

                <dir class="card-footer">
                  <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="add_save"> Save</button>
                  <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button>
                </dir>

              </div>
              <!-- /.card -->
            </form>
            
          </div>

          
        </div>
        <!-- /.row -->
       
      
      </div><!-- /.container-fluid -->
    </section>    

<?php
include '../inc/footer.php';
?>
<script>
$(function () {
  
  $('#formattendance').validate({
    rules: {
      employee_id: {required: true, 
        remote: {
          url: "/check_employee_id",
          type: "post"
          }
        },
      department_id: {required: true, 
        remote: {
          url: "/check_department_id",
          type: "post"
          }},
      position_id: {required: true, 
        remote: {
          url: "/check_position_id",
          type: "post"
          }},
      start_date: {required: true},
      end_date: {required: true},
      no_of_shifts: {required: true}  

      
    },

    messages: {  
    
      employee_id: {
        remote: 'Wrong Employee No!'
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

load_data();

function load_data(query = '')
{
  var query = $('#employee_id').val();
      $.ajax({
        url:"/employee_no",
        method:"POST",
        data:{query:query,request:1},
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

      var query = $('#department_id').val();
      $.ajax({
        url:"/employee_no",
        method:"POST",
        data:{query:query,request:2},
        dataType: 'json',

        success:function(response)
        {
          var len = response.length;
          
          var department_name='';
          
          if(len > 0){
              var department_name = response[0]['department_name'];
          }
          document.getElementById('institution_name').value = department_name;
        }
      });

      var query = $('#position_id').val();
      $.ajax({
        url:"/employee_no",
        method:"POST",
        data:{query:query,request:3},
        dataType: 'json',

        success:function(response)
        {
          var len = response.length;
          
          var position_id='';
          
          if(len > 0){
              var position_id = response[0]['position_id'];
          }
          document.getElementById('position_name').value = position_id;
        }
      });
}
$('#employee_id').keyup(function(){
     var query = $('#employee_id').val();
       load_data(1, query);
     });

$('#department_id').keyup(function(){
     var query = $('#department_id').val();
       load_data(1, query);
     });

$('#position_id').keyup(function(){
     var query = $('#position_id').val();
       load_data(1, query);
     });
</script>