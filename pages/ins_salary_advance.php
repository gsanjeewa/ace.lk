<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 58) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;
if (isset($_POST['add_save'])){ 

  if (checkPermissions($_SESSION["user_id"], 58) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/institution_list/institution/salary_advance/'.$_GET['sal'].'');
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

  $date_effective = date('Y-m-d', strtotime($_POST['date_effective']));

  if (!empty($_POST['addl']) != 1) {
    $statement = $connect->prepare("SELECT employee_id, date_effective FROM salary_advance WHERE YEAR(date_effective)= YEAR('".$date_effective."') AND MONTH(date_effective) = MONTH('".$date_effective."') AND employee_id='".$employee_id."'");  
    $statement->execute(); 
    if($statement->rowCount()>0){
      $error = true;
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>This Salary Advance Already existing.</div>';      
    }
  }


  if ($_POST['amount']=='2000') {
    $amount = 2000;
  }elseif($_POST['amount']=='3000'){
    $amount = 3000;
  }elseif ($_POST['amount']=='4000') {
   $amount = 4000;
  }elseif ($_POST['amount']=='5000') {
   $amount = 5000;
  }elseif ($_POST['amount']=='6000') {
   $amount = 6000;
  }elseif ($_POST['amount']=='8000') {
   $amount = 8000;
  }elseif ($_POST['amount']=='10000') {
   $amount = 10000;
  }elseif ($_POST['amount']=='15000') {
   $amount = 15000;
  }elseif ($_POST['amount']=='other') {
   $amount = $_POST['other_amount'];
  }

if (!$error) {

  $data = array(
    ':employee_id'      =>  $employee_id,
    ':amount'           =>  $amount, 
    ':department_id'    =>  $_GET['sal'],
    ':date_effective'   =>  $date_effective,
  );
 
  $query = "
  INSERT INTO `salary_advance`(`employee_id`, `department_id`, `amount`, `date_effective`)
  VALUES (:employee_id, :department_id, :amount, :date_effective)  
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

$error = false;

if (isset($_POST['approved'])){  
   
  if (checkPermissions($_SESSION["user_id"], 78) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/institution_list/institution/salary_advance/'.$_GET['sal'].'');
    exit();
  }

$query = 'SELECT * FROM salary_advance WHERE id="'.$_POST['advance_id'].'"';

$statement = $connect->prepare($query);
$statement->execute();
$total_data = $statement->rowCount();

$result = $statement->fetchAll();
foreach($result as $row_empty)
{
  if (empty($row_empty['employee_id'])) {
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Employee no is empty.</div>';
  }

  if (empty($row_empty['department_id'])) {
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Institution is empty.</div>';
  }

  if (empty($row_empty['amount'])) {
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Employee amount is empty.</div>';
  }

  if (empty($row_empty['date_effective'])) {
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Effective date is empty.</div>';
  }
 
}

if (!$error) {
  $data = array(
  ':advance_id'  =>  $_POST['advance_id'],
  ':status'      =>  2,
  
  );

  $query = "UPDATE `salary_advance` SET `status`=:status WHERE `id`=:advance_id    
  ";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    header('location:/institution_list/institution/salary_advance/'.$_GET['sal'].'');            
  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  } 
}
}

if (isset($_POST['not_approved'])){

  if (checkPermissions($_SESSION["user_id"], 78) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/institution_list/institution/salary_advance/'.$_GET['sal'].'');
    exit();
  }

  $data = array(
    ':advance_id'     =>  $_POST['advance_id'],
    ':status'       =>  3,
  );

  $query = "UPDATE `salary_advance` SET `status`=:status WHERE `id`=:advance_id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    header('location:/institution_list/institution/salary_advance/'.$_GET['sal'].'');            
  }else{
      $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
    
}

if (isset($_POST['remove_advance'])){

  if (checkPermissions($_SESSION["user_id"], 78) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/institution_list/institution/salary_advance/'.$_GET['sal'].'');
    exit();
}

  $data = array(
    ':id'      =>  $_POST['att_id']
       
  );

  $query = "DELETE FROM `salary_advance` WHERE `id`=:id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Delete Success.</div>';
    header('location:/institution_list/institution/salary_advance/'.$_GET['sal'].'');
  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
    
}

if (isset($_POST['halt'])){

  if (checkPermissions($_SESSION["user_id"], 78) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/institution_list/institution/salary_advance/'.$_GET['sal'].'');
    exit();
  }

  $data = array(
    ':advance_id'     =>  $_POST['advance_id'],
    ':status'       =>  4,
  );

  $query = "UPDATE `salary_advance` SET `status`=:status WHERE `id`=:advance_id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    header('location:/institution_list/institution/salary_advance/'.$_GET['sal'].'');            
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
            <h1 class="m-0 text-dark">Institution</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Home</a></li>
              <li class="breadcrumb-item"><a href="/institution_list/institution">Institution</a></li>
              <li class="breadcrumb-item active">Salary Advance</li>
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
          <div class="col-md-5">

            <?php 

            if(isset($_GET['sal'])):
            
              $query = 'SELECT * FROM department WHERE department_id="'.$_GET['sal'].'"';

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
            
              <form action="" id="loan_req" method="post">
              <div class="card card-secondary">
                <div class="card-header">
                  <h3 class="card-title">Salary Advance - <?php echo $row['department_name'].'-'.$row['department_location']; ?></h3>
                </div>
                  <!-- /.card-header -->
                <div class="card-body">
                 
                 <div class="form-group">
                   <label for="date_effective" class="control-label">Month</label>
                  <div class="input-group date" id="reservationmonth" data-target-input="nearest">
                      <input type="text" name="date_effective" id="date_effective" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date("Y-m"); ?>"/>
                      <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                      </div> 
                    </div>
                 
                  <div class="form-group">
                    <label for="employee_id">Service No </label>
                    <input type="text" class="form-control" id="employee_id" name="employee_id" autofocus autocomplete="off"> 
                    <span id="employee_name" class="text-success"></span>
                  </div>

                  
                </div>
                <input type="hidden" id="department_id" name="department_id" value="<?php echo $_GET['sal'];?>">

                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_2000" name="amount" value="2000">
                        <label for="radioPrimary_2000"><?php echo number_format('2000'); ?>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_3000" name="amount" value="3000">
                        <label for="radioPrimary_3000"><?php echo number_format('3000'); ?>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_4000" name="amount" value="4000">
                        <label for="radioPrimary_4000"><?php echo number_format('4000'); ?>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_5000" name="amount" value="5000">
                        <label for="radioPrimary_5000"><?php echo number_format('5000'); ?>
                        </label>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_6000" name="amount" value="6000">
                        <label for="radioPrimary_6000"><?php echo number_format('6000'); ?>
                        </label>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_8000" name="amount" value="8000">
                        <label for="radioPrimary_8000"><?php echo number_format('8000'); ?>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_10000" name="amount" value="10000">
                        <label for="radioPrimary_10000"><?php echo number_format('10000'); ?>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_15000" name="amount" value="15000">
                        <label for="radioPrimary_15000"><?php echo number_format('15000'); ?>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_other" name="amount" value="other">
                        <label for="radioPrimary_other">Other
                        </label>
                      </div>
                    </div>
                  </div>
                    <div class="col-md-3">
                  <div class="form-group" style="display: none" id="other_field">
                    <input type="text" class="form-control" id="other_amount" name="other_amount">
                  </div>
                </div>
                
                </div>    

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="checkbox" id="addl" name="addl" value="1">
                        <label for="addl">Additional Advance
                        </label>
                      </div>
                    </div>
                  </div>
                </div>              
                  
                
                <div class="filter_data" style="justify-content: center;" ></div>


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

          <div class="col-md-7">
            <table class="table table-sm table-bordered table-striped">
            <thead>
              <tr style="text-align:center;">
                <th>#</th>
                <th>Employee Name</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
                  
            <tbody id="finesdata">
              
            </tbody>
            <tfoot>
              <tr>
                <th colspan="2" style="text-align: center;">Total</th>
                <th style="text-align: right;"><b><span id="total_data_table">0.00</span></b></th>
                <th></th>
              </tr>
            </tfoot>
          </table>
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
    $("input[name='amount']").click(function () {
      if ($("#radioPrimary_other").is(":checked")) {
          $("#other_field").show();
          $('#other_amount').attr('required','');
          $('#other_amount').attr('focus', true);
          $('#other_amount').attr('data-error', 'This field is required.');
          $('#other_amount').val(''); 
                   
      } else {
          $("#other_field").hide();
          $('#other_amount').removeAttr('required');
          $('#other_amount').removeAttr('data-error');
          $('#other_amount').removeAttr('focus');
          $('#other_amount').val('');
      }

      
        
    });
      
  });
    
</script>
<script type="text/javascript">
  $(function () {
  
  $('#loan_req').validate({
    rules: {
      employee_id: { required: true,
      remote: {
          url: "/check_employee_id",
          type: "post"
          }
        },
      amount: {required: true},
      
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
$(document).ready(function(){
load_data();
load_fines();

setInterval(function(){
    load_fines();    
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

function load_fines(department_id = '' , )
{
  var department_id = $('#department_id').val();
  var start_date = $('#date_effective').val();
   
  $.ajax({
    url:"/employee_no",
    method:"POST",
    data:{department_id:department_id,start_date:start_date,request:14},
    dataType: 'json',

    success:function(response)
    {
      var html='';
      var serial_no = 1;
      
      if(response.data.length > 0)
      {
        for(var count = 0; count < response.data.length; count++)
        {
          html += '<tr>';
          html += '<td style="width:5%;"><center>'+serial_no+'</center></td>';
          html += '<td style="width:50%;">'+response.data[count].emp_name+'</td>';
          html += '<td style="width:10%; text-align:right;">'+response.data[count].amount+'</td>';
          html += '<td style="width:15%; text-align:center;">'+response.data[count].status+'</td>';
          html += '<td style="width:20%; text-align:center;"><form action="" method="post" ><input type="hidden" name="advance_id" value="'+response.data[count].id+'">'+response.data[count].action+'</form></td>';
          html += '</tr>'; 
          serial_no++;            
        }
      }
      else
      {
        html += '<tr><td colspan="5" class="text-center">No Data Found</td></tr>';
      }
      document.getElementById('finesdata').innerHTML = html;
      document.getElementById('total_data_table').innerHTML = response.total_data_table;
    }
  });  
}


$('#employee_id').keyup(function(){
     var query = $('#employee_id').val();
       load_data(1, query);
     });

$('#date_effective').change(function(){
  var start_date = $('#date_effective').val();
  load_fines(1, start_date);
  });
});
</script>

<script type="text/javascript">
$(document).ready(function(){
  setInterval(function(){
    filter_data();   
    
  }, 2000);


    function filter_data()
    {
        /*$('.filter_data').html('<div id="loading" style="" ></div>');*/
        var date_effective = $('#date_effective').val();
        var department_id = $('#department_id').val();        
        $.ajax({
            url:"/salarycount",
            method:"POST",
            data:{date_effective:date_effective, department_id:department_id},
            success:function(data){
                $('.filter_data').html(data);
            }
        });
    }
   });
</script>