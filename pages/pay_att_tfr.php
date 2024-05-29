
<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 32) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
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
       
          <div class="form-group" id="process" style="display:none;">
        <div class="progress">
       <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="">
       </div>
      </div>
       </div>        
        
        <div class="row">
          
            <div class="col-xl-12 col-md-6 mb-4" id="success_message">
          
            </div>
          
        </div>
        <div class="row">          
          <div class="col-md-6">
            
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Attendance Trasfer</h3>                       
                </div>
                  <!-- /.card-header -->
                <div class="card-body"> 
                  <form method="POST" id="sample_form">
                    <div class="col-md-12">
                    <div class="form-group">
                      <label for="effective_date" class="control-label">Month</label>
                      <div class="input-group date" id="reservationmonth" data-target-input="nearest">
                          <input type="text" name="effective_date" id="effective_date" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date("Y-m"); ?>"/>
                          <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                          </div>
                        </div>
                    </div>
                    <div class="form-group">
                  <label for="ins_id">Institution</label>
                  <select class="form-control select2" style="width: 100%;" name="ins_id" id="ins_id">
                    <option value="">Select Institution</option>
                    <?php
                    $query="SELECT b.department_id, b.department_name, b.department_location FROM d_shifts_rate a INNER JOIN department b ON a.department_id=b.department_id WHERE a.status!=1 ORDER BY b.department_name ASC";
                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $result = $statement->fetchAll();
                    foreach($result as $row)
                    {
                      ?>
                      <option value="<?php echo $row['department_id']; ?>"><?php echo $row['department_name'].'- '.$row['department_location']; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                </div>
                <!-- checkbox -->
                    <div class="form-group clearfix">                      
                      <div class="icheck-success d-inline">
                        <input type="checkbox" id="checkboxSuccess" name="extra_ot" value="1">
                        <label for="checkboxSuccess">
                          Extra OT
                        </label>
                      </div>
                    </div>

                    </div>
                    <div class="col-md-12">                      
                      <button class="btn btn-sm btn-outline-success" name="calculate_payroll" id="calculate_payroll" type="submit" data-toggle="tooltip" data-placement="top" title="Tranfer"><i class="fas fa-calculator"></i> Transfer</button>

                    </div>
                  </form>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->  
            
          </div>          
        </div>
        <!-- /.row -->       
      
      </div><!-- /.container-fluid -->
    </section>   

 <?php
include '../inc/footer.php';
?>

<script>
 
 $(document).ready(function(){
  
  $('#sample_form').on('submit', function(event){
   event.preventDefault();   
    $.ajax({
     url:"/tfr_process",
     method:"POST",
     data:$(this).serialize(),
     beforeSend:function()
     {
      $('#calculate_payroll').attr('disabled', 'disabled');
      $('#process').css('display', 'block');
     },
     success:function(data)
     {
      
      var percentage = 0;

      var timer = setInterval(function(){
       percentage = percentage + 20;
       progress_bar_process(percentage, timer);
      }, 1000);
     
    }
   
    })
   
  });

  function progress_bar_process(percentage, timer)
  {
   $('.progress-bar').css('width', percentage + '%');
   if(percentage > 100)
   {
    clearInterval(timer);
    $('#sample_form')[0].reset();
    $('#process').css('display', 'none');
    $('.progress-bar').css('width', '0%');
    $('#calculate_payroll').attr('disabled', false);
    $('#success_message').html('<div class="alert alert-dismissible alert-success bg-gradient-success text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><span class="glyphicon glyphicon-info-sign"></span>Success.</div>');
    setTimeout(function(){
     $('#success_message').html('');
     location.reload();
    }, 2000);
   }
  }

  $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

 });
</script>

