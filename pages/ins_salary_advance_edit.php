<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 59) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;
if (isset($_POST['add_save'])){ 

  if (checkPermissions($_SESSION["user_id"], 59) == "false") {

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
  
  /*$statement = $connect->prepare("SELECT employee_id, date_effective FROM salary_advance WHERE YEAR(date_effective)= YEAR('".$date_effective."') AND MONTH(date_effective) = MONTH('".$date_effective."') AND employee_id='".$employee_id."'");  
  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>This Salary Advance Already existing.</div>';      
  }*/

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
    ':id'               =>  $_GET['edit'],
  );
 
  $query = "
  UPDATE `salary_advance` SET `employee_id`=:employee_id, `department_id`=:department_id, `amount`=:amount, `date_effective`=:date_effective WHERE `id`=:id  
  ";
          
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';  
    header('location:/institution_list/institution/salary_advance/'.$_GET['sal'].'');
    exit();

  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
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
          <div class="col-md-6">

            <?php 

            if((isset($_GET['sal'])) && (isset($_GET['edit']))):
            
              $query = 'SELECT * FROM department WHERE department_id="'.$_GET['sal'].'"';

              $statement = $connect->prepare($query);
              $statement->execute();
              $total_data = $statement->rowCount();
              $result = $statement->fetchAll();
              if ($total_data > 0):  
                foreach($result as $row_department):
                  
                  endforeach;
                endif;
                            
              $statement = $connect->prepare('SELECT j.employee_no, s.amount FROM salary_advance s INNER JOIN join_status j ON s.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid WHERE s.department_id="'.$_GET['sal'].'" AND s.id="'.$_GET['edit'].'"');
              $statement->execute();
              $total_advance = $statement->rowCount();
              $result = $statement->fetchAll();
              if ($total_advance > 0):  
                foreach($result as $row):
                  
                  if ($row['amount']==2000):
                    $amount2=$row['amount'];
                  elseif($row['amount']==3000):
                    $amount3=$row['amount'];
                  elseif($row['amount']==4000):
                    $amount4=$row['amount'];
                  elseif($row['amount']==5000):
                    $amount5=$row['amount'];
                  elseif($row['amount']==6000):
                    $amount6=$row['amount'];
                  elseif($row['amount']==8000):
                    $amount8=$row['amount'];
                  elseif($row['amount']==10000):
                    $amount10=$row['amount'];                    
                  elseif($row['amount']==15000):
                    $amount15=$row['amount'];
                  else:
                    $other=$row['amount'];
                  endif;

                  endforeach;
                endif;
              endif;


                  ?>
            
              <form action="" id="loan_req" method="post">
              <div class="card card-secondary">
                <div class="card-header">
                  <h3 class="card-title">Salary Advance - <?php echo $row_department['department_name']; ?></h3>
                </div>
                  <!-- /.card-header -->
                <div class="card-body">
                 
                 
                  <div class="form-group">
                    <label for="employee_id">Service No </label>
                    <input type="text" class="form-control" id="employee_id" name="employee_id" autofocus autocomplete="off" value="<?php echo $row['employee_no']; ?>"> 
                    <span id="employee_name" class="text-success"></span>
                  </div>

                  <!--<div class="form-group">
                   <label for="date_effective" class="control-label">Start Deduct Date</label> -->
                  <div class="input-group date" id="reservationmonth" data-target-input="nearest">
                      <input type="hidden" name="date_effective" id="date_effective" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date("Y-m"); ?>"/>
                      <!-- <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                      </div> 
                    </div>-->
                </div>
                <input type="hidden" id="department_id" name="department_id" value="<?php echo $_GET['sal'];?>">



                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_2000" name="amount" value="2000" <?php if (!empty($amount2)) { echo "checked";} ?>>
                        <label for="radioPrimary_2000"><?php echo number_format('2000'); ?>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_3000" name="amount" value="3000" <?php if (!empty($amount3)) { echo "checked";} ?>>
                        <label for="radioPrimary_3000"><?php echo number_format('3000'); ?>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_4000" name="amount" value="4000" <?php if (!empty($amount4)) { echo "checked";} ?>>
                        <label for="radioPrimary_4000"><?php echo number_format('4000'); ?>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_5000" name="amount" value="5000" <?php if (!empty($amount5)) { echo "checked";} ?>>
                        <label for="radioPrimary_5000"><?php echo number_format('5000'); ?>
                        </label>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_6000" name="amount" value="6000" <?php if (!empty($amount6)) { echo "checked";} ?>>
                        <label for="radioPrimary_6000"><?php echo number_format('6000'); ?>
                        </label>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_8000" name="amount" value="8000" <?php if (!empty($amount8)) { echo "checked";} ?>>
                        <label for="radioPrimary_8000"><?php echo number_format('8000'); ?>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_10000" name="amount" value="10000" <?php if (!empty($amount10)) { echo "checked";} ?>>
                        <label for="radioPrimary_10000"><?php echo number_format('10000'); ?>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_15000" name="amount" value="15000" <?php if (!empty($amount15)) { echo "checked";} ?>>
                        <label for="radioPrimary_15000"><?php echo number_format('15000'); ?>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group clearfix">
                      <div class="icheck-secondary d-inline">
                        <input type="radio" id="radioPrimary_other" name="amount" value="other" <?php if (!empty($other)) { echo "checked";} ?>>
                        <label for="radioPrimary_other">Other
                        </label>
                      </div>
                    </div>
                  </div>
                    

                    </div>
                  
                  <div class="form-group" <?php if (!empty($other)) { echo 'style="display: block"';}else{ echo 'style="display: none"';} ?> id="other_field">
                    <input type="text" class="form-control" id="other_amount" name="other_amount" value="<?php echo $other; ?>">
                  </div>
                
                <div class="filter_data" style="justify-content: center;" ></div>

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

load_data();

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
$('#employee_id').keyup(function(){
     var query = $('#employee_id').val();
       load_data(1, query);
     });


</script>

<script>
$(document).ready(function(){
  setInterval(function(){
    filter_data();   
    
  }, 5000);


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
