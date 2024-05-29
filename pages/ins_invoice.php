<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 106) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;
if (isset($_POST['add_save'])){ 

  if (checkPermissions($_SESSION["user_id"], 106) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/institution_list/institution/invoice/'.$_GET['sal'].'');
    exit();
  }

  $date_effective = date('Y-m-d', strtotime($_POST['date_effective']));
  
  $statement = $connect->prepare("SELECT department_id, position_id, no_of_shifts, date_effective FROM invoice WHERE YEAR(date_effective)= YEAR('".$date_effective."') AND MONTH(date_effective) = MONTH('".$date_effective."') AND department_id='".$_GET['sal']."' AND position_id='".$_POST['position_id']."' AND no_of_shifts='".$_POST['no_of_shifts']."'");  
  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>This shift Already existing.</div>';      
  } 

if (!$error) {
for ($i=0; $i < count($_POST['position_id']); $i++) {
  $data = array(
    ':department_id'    =>  $_GET['sal'],
    ':position_id'      =>  $_POST['position_id'][$i], 
    ':no_of_shifts'     =>  $_POST['no_of_shifts'][$i],
    ':date_effective'   =>  $date_effective,
  );
 
  $query = "
  INSERT INTO invoice(department_id, position_id, no_of_shifts, date_effective)
  VALUES (:department_id, :position_id, :no_of_shifts, :date_effective)
  ";
          
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
    header('location:/institution_list/institution/invoice/'.$_GET['sal'].'');
  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
}
} 
}

if (isset($_POST['update_save'])){ 

  if (checkPermissions($_SESSION["user_id"], 106) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/institution_list/institution/invoice/'.$_GET['sal'].'');
    exit();
  }

  $date_effective = date('Y-m-d', strtotime($_POST['date_effective']));
  
  $statement = $connect->prepare("SELECT department_id, position_id, date_effective FROM invoice WHERE YEAR(date_effective)= YEAR('".$date_effective."') AND MONTH(date_effective) = MONTH('".$date_effective."') AND position_id='".$_POST['position_id']."' AND department_id='".$_GET['sal']."'");  
  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>This Position Already existing.</div>';      
  } 

if (!$error) {

  $data = array(
    ':id'               =>  $_GET['inv_id'],
    ':position_id'      =>  $_POST['position_id'], 
    ':no_of_shifts'     =>  $_POST['no_of_shifts'],
    ':date_effective'   =>  $date_effective,
  );
 
  $query = "
  UPDATE invoice SET position_id=:position_id, no_of_shifts=:no_of_shifts, date_effective=:date_effective WHERE id=:id 
  ";
          
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
    header('location:/institution_list/institution/invoice/'.$_GET['sal'].'');
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
              <li class="breadcrumb-item active">Invoice</li>
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

              if(isset($_GET['inv_id'])):
            
              $query_invoice = 'SELECT * FROM invoice WHERE id="'.$_GET['inv_id'].'"';

              $statement = $connect->prepare($query_invoice);
              $statement->execute();
              $total_data = $statement->rowCount();
              $result = $statement->fetchAll();
              if ($total_data > 0):  
                foreach($result as $row_invoice):
                  ?>
                  <form action="" id="loan_req" method="post">
              <div class="card card-danger">
                <div class="card-header">
                  <h3 class="card-title">Invoice - <?php echo $row['department_name'].'-'.$row['department_location']; ?></h3>
                </div>
                  <!-- /.card-header -->
                <div class="card-body">
                  <div class="form-group">
                   <label for="date_effective" class="control-label">Month</label>
                  <div class="input-group date" id="reservationmonth" data-target-input="nearest">
                      <input type="text" name="date_effective" id="date_effective" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php if (!empty($row_invoice['date_effective'])): echo date('Y-m', strtotime($row_invoice['date_effective'])); else: echo date('Y-m', strtotime("-1 month")); endif; ?>"/>
                      <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                      </div> 
                    </div>
                </div>


                      <div class="form-group">
                            <label for="">Position Name</label>
                          </div>
                          <div class="row">

                       
                           <?php
                              $query="SELECT b.position_id, b.position_abbreviation, a.position_payment FROM position_pay a INNER JOIN position b ON a.position_id=b.position_id WHERE department_id='".$_GET['sal']."' ORDER BY position_id";
                              $statement = $connect->prepare($query);
                              $statement->execute();
                              $result = $statement->fetchAll();
                              foreach($result as $row)
                              {
                                ?><div class="col-md-3">
                                <div class="form-group clearfix">
                                    <div class="icheck-danger d-inline">
                                      <input type="radio" id="radioPrimary<?php echo $row['position_id']; ?>" name="position_id" value="<?php echo $row['position_id']; ?>" <?php if ($row['position_id']==$row_invoice['position_id']): echo 'checked'; else: endif; ?>>
                                      <label for="radioPrimary<?php echo $row['position_id']; ?>"><?php echo $row['position_abbreviation']; ?>
                                      </label>
                                    </div>
                                  </div>     
                                  </div>                           
                                <?php
                              }
                              ?>
                          </div>
                          
                 
                  <div class="form-group">
                    <label for="no_of_shifts">No of Shifts</label>
                    <input type="text" class="form-control" id="no_of_shifts" name="no_of_shifts" autofocus autocomplete="off" value="<?php echo $row_invoice['no_of_shifts']; ?>">                    
                  </div>

                  <!-- <div class="form-group">
                    <label for="pay_shifts">Payment per Shifts</label>
                    <input type="text" class="form-control" id="pay_shifts" name="pay_shifts" autocomplete="off">                    
                  </div> -->
                  
                <!-- <input type="hidden" id="department_id" name="department_id" value="<?php echo $_GET['sal'];?>"> -->

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
                  endforeach;
                endif;
              else:
                ?>
                <form action="" id="loan_req" method="post">
              <div class="card card-info">
                <div class="card-header">
                  <h3 class="card-title">Invoice - <?php echo $row['department_name'].'-'.$row['department_location']; ?></h3>
                </div>
                  <!-- /.card-header -->
                <div class="card-body">
                  <div class="form-group">
                   <label for="date_effective" class="control-label">Month</label>
                  <div class="input-group date" id="reservationmonth" data-target-input="nearest">
                      <input type="text" name="date_effective" id="date_effective" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php if (!empty($row_invoice['date_effective'])): echo date('Y-m', strtotime($row_invoice['date_effective'])); else: echo date('Y-m', strtotime("-1 month")); endif; ?>"/>
                      <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                      </div> 
                    </div>
                </div>


                      <div class="form-group">
                            <label for="">Position Name</label>
                          </div>
                          <div class="row">

                            <?php
                      $query="SELECT b.position_id, b.position_abbreviation, a.position_payment FROM position_pay a INNER JOIN position b ON a.position_id=b.position_id WHERE department_id='".$_GET['sal']."' ORDER BY position_id";
                      $statement = $connect->prepare($query);
                      $statement->execute();
                      $result = $statement->fetchAll();
                      foreach($result as $row)
                      {
                        ?>
                        
                          <div class="col-md-4">
                            <div class="form-group clearfix">
                              <div class="icheck-success d-inline">
                                <input type="checkbox" id="checkboxPrimary<?php echo $row['position_id']; ?>" name="position_id[]" value="<?php echo $row['position_id']; ?>" onchange="toggleInput(this)">
                                <label for="checkboxPrimary<?php echo $row['position_id']; ?>"><?php echo $row['position_abbreviation']; ?>
                                </label>
                              </div>
                              </div>
                            </div>
                                <div class="col-md-2">
                                <div class="form-group ">
                        <input type="text" class="form-control" id="no_of_shifts<?php echo $row['position_id']; ?>" name="no_of_shifts[]" autocomplete="off" disabled >
                      </div>
                              </div>
                          
                        <?php
                      }
                      ?>

<!-- 

                       
                           <?php
                              $query="SELECT * FROM position ORDER BY position_id";
                              $statement = $connect->prepare($query);
                              $statement->execute();
                              $result = $statement->fetchAll();
                              foreach($result as $row)
                              {
                                ?><div class="col-md-3">
                                <div class="form-group clearfix">
                                    <div class="icheck-info d-inline">
                                      <input type="radio" id="radioPrimary<?php echo $row['position_id']; ?>" name="position_id" value="<?php echo $row['position_id']; ?>" <?php if ($row['position_id']==$row_invoice['position_id']): echo 'checked'; else: endif; ?>>
                                      <label for="radioPrimary<?php echo $row['position_id']; ?>"><?php echo $row['position_abbreviation']; ?>
                                      </label>
                                    </div>
                                  </div>     
                                  </div>                           
                                <?php
                              }
                              ?> -->
                          </div>
                          
                 
                  <!-- <div class="form-group">
                    <label for="no_of_shifts">No of Shifts</label>
                    <input type="text" class="form-control" id="no_of_shifts" name="no_of_shifts" autofocus autocomplete="off" value="<?php echo $row_invoice['no_of_shifts']; ?>">                    
                  </div> -->

                  <!-- <div class="form-group">
                    <label for="pay_shifts">Payment per Shifts</label>
                    <input type="text" class="form-control" id="pay_shifts" name="pay_shifts" autocomplete="off">                    
                  </div> -->

                  
                <!-- <input type="hidden" id="department_id" name="department_id" value="<?php echo $_GET['sal'];?>"> -->

                </div>
                <!-- /.card-body -->

                <dir class="card-footer">
                  <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="add_save"> Save</button>
                  <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button>
                </dir>

              </div>
              <!-- /.card -->
            </form>
                <?php
              endif;

                  ?>
            
          </div>

          <div class="col-md-6">
            <table class="table table-sm table-bordered table-striped">
            <thead>
              <tr style="text-align:center;">
                <th>#</th>
                <th>Position</th>
                <th>No of Shifts</th>
                <th>Action</th>
              </tr>
            </thead>
                  
            <tbody id="invoicedata">
              
            </tbody>            
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
  
  $('#loan_req').validate({
    rules: {
      no_of_shifts: { required: true,},
      position_id: {required: true},
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

setInterval(function(){
    load_data();   
    
  }, 2000);

function load_data(department_id = '' , )
{
  var department_id = <?php echo $_GET['sal']; ?>;
  var date_effective = $('#date_effective').val();  
 
      $.ajax({
        url:"/invoice_data",
        method:"POST",
        data:{department_id:department_id,date_effective:date_effective,request:1},
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
              html += '<td style="width:30%;">'+response[count].position_name+'</td>';
              html += '<td style="text-align:right; width:25%;"><center>'+response[count].no_of_shifts+'</center></td>';
              html += '<td style="text-align:right; width:35%;"><center>'+response[count].action+'</center></td>';
              html += '</tr>'; 
              serial_no++;            
            }
          }
          else
          {
            html += '<tr><td colspan="4" class="text-center">No Data Found</td></tr>';
          }
          document.getElementById('invoicedata').innerHTML = html;
        }
      });  
}

function toggleInput(checkbox) {
        var inputFieldId = 'no_of_shifts' + checkbox.value;
        var inputField = document.getElementById(inputFieldId);
        
        // Enable/disable the input field based on checkbox state
        inputField.disabled = !checkbox.checked;
        inputField.focus();
    }

</script>