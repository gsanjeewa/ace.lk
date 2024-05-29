<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 69) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;

if (isset($_POST['add_save'])){

  if (checkPermissions($_SESSION["user_id"], 69) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';  
    header('location:/dashboard');  
    exit();
  }

 
  $start_date=date('Y-m-d', strtotime($_POST['start_date']));
  $employee_id_att=array();
  $employee_id_ded=array();
  
  $statement = $connect->prepare("SELECT employee_id FROM (SELECT employee_id FROM attendance WHERE YEAR(start_date)= YEAR('".$start_date."') AND MONTH(start_date) = MONTH('".$start_date."') GROUP BY employee_id Having SUM(no_of_shifts) > 5) indebted
      ");
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
  foreach($result as $row_att)
  {
    $employee_id_att[]=$row_att['employee_id'];
  }

  $statement = $connect->prepare("SELECT employee_id FROM employee_deductions WHERE YEAR(effective_date)= YEAR('".$start_date."') AND MONTH(effective_date) = MONTH('".$start_date."') AND deduction_id=4
      ");
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
  foreach($result as $row_ded)
  {
    $employee_id_ded[]=$row_ded['employee_id'];
  }


  $diff_insert=array_diff($employee_id_att, $employee_id_ded);

  $diff_delete=array_diff($employee_id_ded, $employee_id_att);

 $query_insert = "
  INSERT INTO `employee_deductions`(`employee_id`, `deduction_id`, `type`, `amount`, `effective_date`) 
  VALUES (:employee_id, :deduction_id, :type_id, :amount, :effective_date) 
  ON DUPLICATE KEY UPDATE  `amount`=:amount
  ";
          
  $statement = $connect->prepare($query_insert);

foreach($diff_insert as $d) {
  if($statement->execute(array(':employee_id' => $d, ':deduction_id' => 4, ':type_id' => 3, ':amount' => $_POST['amount'], ':effective_date' => $start_date)))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';            
  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
}

$query_delete = "
  DELETE FROM `employee_deductions` WHERE `employee_id`=:employee_id AND `deduction_id`=:deduction_id AND `type`=:type AND `effective_date`=:effective_date
  ";
          
  $statement = $connect->prepare($query_delete);

foreach($diff_delete as $k) {
  if($statement->execute(array(':employee_id' => $k, ':deduction_id' => 4, ':type_id' => 3, ':effective_date' => $start_date)))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';            
  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
}



  /*for ($l = 0; $l < count($employee_id); $l++) {

  $data = array(
      ':employee_id'    =>  $employee_id[$l],
      ':deduction_id'   =>  4,
      ':effective_date' => $start_date,
      ':type_id'        =>  3,
      ':amount'         =>  $_POST['amount'],          
  );
 
  $query = "
  INSERT INTO `employee_deductions`(`employee_id`, `deduction_id`, `type`, `amount`, `effective_date`) 
      VALUES (:employee_id, :deduction_id, :type_id, :amount, :effective_date)
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
  }*/
}

include '../inc/header.php';
?>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Death Donation</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Home</a></li>
              <li class="breadcrumb-item active">Death Donation</li>
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
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Add Death Donation to Employee</h3>
                </div>
                  <!-- /.card-header -->
                <div class="card-body">

                  <div class="row">
                    <div class="col-md-6">
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

                    <div class="col-md-6">
                      <div class="total_shifts" style="justify-content: center;" ></div>
                    </div>
                   
                  </div>
                                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="text" class="form-control" id="amount" name="amount" autocomplete="off" >
                      </div>
                    </div>                    
                  </div>

                  <div class="row">

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
            <div class="row">

              
            </div>
            <div class="row">
              <div class="col-md-12">
            <table class="table table-sm table-bordered table-striped">
            <thead>
              <tr style="text-align:center;">
                <th>#</th>
                <th>Employee Name</th>
                <th>Amount</th>
              </tr>
            </thead>
                  
            <tbody id="finesdata">
              
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
  
  $('#formattendance').validate({
    rules: {
      amount: {required: true, number: true},
    },

    messages: {  
    
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
load_death();
total_shifts();

setInterval(function(){
    load_death();   
    total_shifts();
  }, 2000);


function load_death(start_date = '' , )
{
  var start_date = $('#start_date').val();
   
  $.ajax({
    url:"/employee_no",
    method:"POST",
    data:{start_date:start_date,request:18},
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
          html += '<td style="width:20%; text-align:right;">'+response[count].amount+'</td>';                     
          html += '</tr>'; 
          serial_no++;            
        }
      }
      else
      {
        html += '<tr><td colspan="4" class="text-center">No Data Found</td></tr>';
      }
      document.getElementById('finesdata').innerHTML = html;
    }
  });  
}

function total_shifts(start_date = '')
{
  var start_date = $('#start_date').val();
  
  $.ajax({
    url:"/employee_no",
    method:"POST",
    data:{start_date:start_date,request:19},
    success:function(data){
      $('.total_shifts').html(data);
    }
  });
}

$('#start_date').change(function(){
  var start_date = $('#start_date').val();
  load_death(1, start_date);
  });
});
</script>