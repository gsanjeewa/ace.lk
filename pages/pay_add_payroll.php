<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 29) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}
$error=false;

if (isset($_POST['add_save'])){

  if (checkPermissions($_SESSION["user_id"], 29) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/payroll_list/add_payroll');
    exit();
}

  $date_from=  $_POST['start_date'];
  $date_to=  $_POST['end_date'];
  $statement = $connect->prepare("SELECT * FROM payroll WHERE date_from=:date_from AND date_to=:date_to");
  $statement->bindParam(':date_from', $date_from);
  $statement->bindParam(':date_to', $date_to);
  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $errMSG = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>start_date & end_date Already existing.</div>';
  }
 
  $i= 1;
  while($i == 1){
    $ref_no=date('Y') .'-'. mt_rand(1,9999);
    $query = "SELECT * FROM payroll WHERE ref_no = '".$ref_no."'";
    $statement = $connect->prepare($query);
    $statement->execute();
    $total_data = $statement->rowCount();

    if($total_data <= 0){
      $i = 0;
    }
  }
  
  $data = array(
      ':ref_no'    =>  $ref_no,
      ':date_from' =>  $_POST['start_date'],
      ':date_to'   =>  $_POST['end_date'],
      ':type'      =>  $_POST['type_id'],            
  );
 
  $query = "
  INSERT INTO `payroll`(`ref_no`, `date_from`, `date_to`, `type`)
  VALUES (:ref_no, :date_from, :date_to, :type)
  ";
          
  $statement = $connect->prepare($query);

  if (!$error) {
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


include '../inc/header.php';
?>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Payroll</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Payroll</li>
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
                  <h3 class="card-title">Add Payroll</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">              
                  

                  <div class="row">
                    <div class="col-md-4">
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

                    <div class="col-md-4">
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

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="no_of_shifts">Payroll Type</label>
                        <select class="form-control select2" style="width: 100%;" name="type_id" id="type_id">
                          <option value="1">Monthly</option>
                          <option value="2">Semi-Monthly</option>                          
                        </select>
                      </div> 
                    </div>
                  </div>     

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
      start_date: {required: true},
      end_date: {required: true},
      no_of_shifts: {required: true}        
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
          document.getElementById('employee_name').value = name_with_initial;
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