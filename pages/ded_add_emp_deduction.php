<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();

require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 17) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;

if (isset($_POST['add_save'])){

  if (checkPermissions($_SESSION["user_id"], 17) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
    header('location:/deduction_list/add_emp_deduction');    
    exit();
}
    
  /*$employee_id=  $_POST['employee_id'];
  $deduction_id=  $_POST['deduction_id'];
  $statement = $connect->prepare("SELECT employee_id, deduction_id FROM employee_deductions WHERE employee_id=:employee_id AND deduction_id=:deduction_id");
  $statement->bindParam(':employee_id', $employee_id);
  $statement->bindParam(':deduction_id', $deduction_id);

  $statement->execute();
  
  if($statement->rowCount()>0){
    $error = true;
      $errMSG = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Already existing.</div>';
  }*/

  $statement = $connect->prepare("SELECT join_id FROM join_status WHERE employee_no = '".$_POST['employee_id']."'
      ");
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
  foreach($result as $row)
  {
    $employee_id=$row['join_id'];
  }

  $effective_date=date('Y-m-d', strtotime($_POST['effective_date']));

  if (!$error){

      $data = array(
          ':employee_id'    =>  $employee_id,
          ':deduction_id'   =>  $_POST['deduction_id'],
          ':type_id'        =>  3,
          ':effective_date' =>  $effective_date,
          ':amount'         =>  $_POST['amount'],          
      );
     
      $query = "
      INSERT INTO `employee_deductions`(`employee_id`, `deduction_id`, `type`, `amount`, `effective_date`) 
      VALUES (:employee_id, :deduction_id, :type_id, :amount, :effective_date)
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


if (isset($_POST['remove_deduction'])){

  if (checkPermissions($_SESSION["user_id"], 17) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/deduction_list/add_emp_deduction'); 
    exit();
}

  $data = array(
    ':id'      =>  $_POST['deduction_id']
       
  );

  $query = "DELETE FROM `employee_deductions` WHERE `id`=:id";
    
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
            <h1 class="m-0 text-dark">Deduction</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Deduction</li>
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
          <div class="col-md-8">
              <form action="" id="add_deduction_form" method="post">
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Add Employee Deduction</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">
                  <div class="form-group">
                  <label for="employee_id">Service No </label>
                  <input type="text" class="form-control" id="employee_id" name="employee_id" autofocus autocomplete="off"> 
                  <span id="employee_name" class="text-success"></span>
                </div>

                <!-- 
                  <div class="form-group">
                  <label for="employee_id">Employee</label>
                  <select class="form-control select2" style="width: 100%;" name="employee_id" id="employee_id">
                    <option value="">Select Employee</option>
                    <?php
                    $query="SELECT j.join_id, e.surname, e.initial, j.employee_no FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN position p ON e.position_id=p.position_id WHERE j.employee_status=0 OR j.employee_status=2 ORDER BY e.employee_id DESC";
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
                </div> -->
                <label for="deduction_id">Deduction</label>
                <div class="row">
                <?php
                    $query="SELECT * FROM deduction WHERE deduction_id >= 3 ORDER BY deduction_id";
                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $result = $statement->fetchAll();
                    foreach($result as $row)
                    {
                      ?>
                      <div class="col-md-4">
                        <div class="form-group clearfix">
                            <div class="icheck-success d-inline">
                              <input type="radio" id="radioPrimary<?php echo $row['deduction_id']; ?>" name="deduction_id" value="<?php echo $row['deduction_id']; ?>">
                              <label for="radioPrimary<?php echo $row['deduction_id']; ?>"><?php echo $row['deduction_en']; ?>
                              </label>
                            </div>
                          </div>     
                          </div>                      
                      <?php
                    }
                    ?>
                
                </div>
                

                <!-- <div class="form-group">
                  <label for="deduction_id">Deduction</label>
                  <select class="form-control select2" style="width: 100%;" name="deduction_id" id="deduction_id">
                    <option value="">Select Deduction</option>
                    <?php
                    $query="SELECT * FROM deduction ORDER BY deduction_id";
                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $result = $statement->fetchAll();
                    foreach($result as $row)
                    {
                      ?>
                      <option value="<?php echo $row['deduction_id']; ?>"><?php echo $row['deduction_en']; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                </div> -->

                <!-- <div class="form-group">
                  <label for="type_id">Type</label>
                  <select class="form-control select2" style="width: 100%;" name="type_id" id="type_id">
                    <option value="1">Monthly</option>
                    <option value="2">Semi-Monthly</option>
                    <option value="3">Once</option>
                  </select>
                </div> -->
               
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="effective_date" class="control-label">Effective Date</label>
                      <div class="input-group date" id="reservationmonth" data-target-input="nearest">
                          <input type="text" name="effective_date" id="effective_date" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date('Y-m', strtotime("-1 month")); ?>"/>
                          <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                          </div>
                        </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="amount">Amount</label>
                      <input type="text" class="form-control" id="amount" name="amount">
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
           
          </div>

          
        </div>
        <!-- /.row -->
        <div class="row">
              <div class="col-md-12">
                <table class="table table-sm table-bordered table-striped">
                  <thead>
                    <tr style="text-align:center;">
                      <th>#</th>
                      <th>Employee Name</th>
                      <th>Deduction</th>
                      <th>Amount</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                        
                  <tbody id="invoicedata">
                    
                  </tbody>
                </table>
              </div>
          
            </div>
      
      </div><!-- /.container-fluid -->
    </section>    

<?php
include '../inc/footer.php';
?>
<script type="text/javascript">
    $(function () {
        $("#type_id").change(function () {
            if ($(this).val() == 3) {
                $("#dfield").show();
                $('#effective_date').attr('required','');
                $('#effective_date').attr('data-error', 'This field is required.');            
            } else {
                $("#dfield").hide();
                $('#effective_date').removeAttr('required');
                $('#effective_date').removeAttr('data-error');
                $('#effective_date').val('');
            }
          
        });
    });
</script>

<script>
$(function () {
  
  $('#add_deduction_form').validate({
    rules: {
      employee_id: { required: true,
      remote: {
          url: "/check_employee_id",
          type: "post"
          }
        },
      deduction_id: {required: true},
      effective_date: {required: true},
      amount: {required: true, number:true}
    },
    
    messages: {  
    
      employee_id: {
        remote: 'Wrong Employee No!'
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

load_deduction();

setInterval(function(){
  load_deduction();    
}, 2000);

function load_data(query = '')
{
  var query = $('#employee_id').val();
      $.ajax({
        url:"/employee_no2",
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

      
}

function load_deduction(effective_date = '' , )
{
  var deduction_id = $('input[name="deduction_id"]:checked').val();
  var effective_date = $('#effective_date').val();
   
  $.ajax({
    url:"/employee_no",
    method:"POST",
    data:{deduction_id:deduction_id,effective_date:effective_date,request:25},
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
          html += '<td style="width:45%;">'+response[count].emp_name+'</td>';
          html += '<td style="width:30%;">'+response[count].deduction_name+'</td>';
          html += '<td style="text-align:right; width:10%;"><center>'+response[count].amount+'</center></td>';
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

$('#employee_id').keyup(function(){
     var query = $('#employee_id').val();
       load_data(1, query);
     });

     $('#effective_date').change(function(){
  var effective_date = $('#effective_date').val();
  load_deduction(1, effective_date);
});

$('input[type="radio"][name="deduction_id"]').change(function(){
  var deduction_id = $(this).val();
  load_deduction(1, deduction_id);
});



</script>