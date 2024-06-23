<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 91) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;

if (isset($_POST['add_save'])){

  if (checkPermissions($_SESSION["user_id"], 91) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';  
    header('location:/dashboard');  
    exit();
  }

  $star_dates = array();

  // Ensure 'no_of_months' is cast to an integer
  $total_months = intval($_POST['no_of_months']);

  // Create the start date object from the effective date
  $start = new DateTime($_POST['date_effective']);

  if (!empty($_POST['first_ins'])) {
      // Insert the first installment into the database using the effective date
      $data = array(
          ':employee_id' => $_POST['employee_id'],
          ':monthly_ins' => $_POST['first_ins'],
          ':due_date' => $start->format("Y-m-d"),
          ':invoice_id' => $_GET['edit'],
          ':status' => 0,
      );
      $query = "
          INSERT INTO inventory_deduction(employee_id, due_date, amount, status, invoice_id)
          VALUES (:employee_id, :due_date, :monthly_ins, :status, :invoice_id)
      ";
      $statement = $connect->prepare($query);
      if ($statement->execute($data)) {
          $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>
          <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
      } else {
          $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Cannot save first installment.</div>';
      }

      // Move to the next month for subsequent installments
      $start->add(new DateInterval('P1M'));
  }

  if ($total_months > 1) {
  // Generate dates for the remaining months
  $interval = DateInterval::createFromDateString('1 month');
  $period = new DatePeriod($start, $interval, $total_months - 1); // total_months - 1 if first_ins is set

  foreach ($period as $dt) {
      $star_dates[] = $dt->format("Y-m-d");
  }

  // Insert the remaining installments into the database
  foreach ($star_dates as $due_date) {
      $data = array(
          ':employee_id' => $_POST['employee_id'],
          ':monthly_ins' => $_POST['monthly_ins'],
          ':due_date' => $due_date,
          ':invoice_id' => $_GET['edit'],
          ':status' => 0,
      );

      $query = "
          INSERT INTO inventory_deduction(employee_id, due_date, amount, status, invoice_id)
          VALUES (:employee_id, :due_date, :monthly_ins, :status, :invoice_id)
      ";
      $statement = $connect->prepare($query);

      if ($statement->execute($data)) {
          $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>
          <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
      } else {
          $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Cannot save.</div>';
      }
  }
}
// Redirect after processing
header('Location: /inventory/deduction/' . $_GET['edit']);
exit;

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
              <li class="breadcrumb-item"><a href="/">Home</a></li>              
              <li class="breadcrumb-item active">Inventory / Deduction</li>
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
          <div class="col-md-6">
          <?php
            if(isset($_GET['edit']))
            {

              $query = 'SELECT * FROM inventory_create_invoice WHERE id="'.$_GET['edit'].'"';

              $statement = $connect->prepare($query);
              $statement->execute();
              $total_data = $statement->rowCount();
              $result = $statement->fetchAll();
              if ($total_data > 0){   
                foreach($result as $row)
                {

                  $query="SELECT j.join_id, e.surname, e.initial, j.employee_no, p.position_abbreviation FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN position p ON e.position_id=p.position_id WHERE j.join_id='".$row['employee_id']."'";
                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $result = $statement->fetchAll();
                    foreach($result as $row_emp)
                    {
                      
                    }

                    $query = 'SELECT * FROM inventory_deduction WHERE invoice_id = :invoice_id';

                    $statement = $connect->prepare($query);
                    $statement->execute([':invoice_id' => $_GET['edit']]);
                    $total_data = $statement->rowCount();
                    $result = $statement->fetchAll();                 
                      

                  ?>
              <form action="" id="formattendance" method="post">
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Clothes Deduction</h3>                            
                </div>
                <?php 
                  if ($total_data > 0) {
                    ?>
                    <div class="card-body">
                    <p>You have recorded monthly payments. So can't re-enter.</p>
                    </div>

                    <?php
                       
                    }else{
                  ?>
                  <!-- /.card-header -->
                <div class="card-body">

                        
                  <div class="row">
                    <div class="col-md-12">                      

                  <div class="form-group">                    
                    <input type="hidden" class="form-control" id="employee_id" name="employee_id" value="<?php echo $row['employee_id']; ?>"> <input type="text" class="form-control" value="<?php echo $row_emp['employee_no'].' '.$row_emp['position_abbreviation'].' '.$row_emp['surname'].' '.$row_emp['initial']; ?>" readonly> 
                    
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="text" class="form-control" id="amount" name="amount" value="<?php echo $row['grand_total']; ?>" readonly>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="first_ins">First Installment</label>
                        <input type="text" class="form-control" id="first_ins" name="first_ins">
                      </div>
                    </div>
                    
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="no_of_months">No of Months</label>
                        <input type="text" class="form-control" id="no_of_months" name="no_of_months">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="date_effective" class="control-label">Start Deduct Date</label>
                        <div class="input-group date" id="reservationmonth" data-target-input="nearest">
                            <input type="text" name="date_effective" id="date_effective" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date('Y-m', strtotime("-1 month")); ?>"/>
                            <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="balance">Balance</label>
                        <input type="text" class="form-control" id="balance" name="balance" readonly>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="monthly_ins">Monthly Installment</label>
                        <input type="text" class="form-control" id="monthly_ins" name="monthly_ins" readonly>
                      </div>
                    </div>
                    </div>  
                    </div>
                    
                  </div>
                  <div class="row">

                  </div>

                  
                </div>
                <!-- /.card-body -->

                <div class="card-footer" >
                  <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="add_save"> Save</button>
                  <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button>
                </div>
              <?php }?>
              </div>
              <!-- /.card -->
            </form>
            <?php
          
                      }
                    }
                  }
                      ?>
          </div>
          <div class="col-md-6">
                <div class="card card-secondary">
                  <div class="card-header">
                    <h3 class="card-title">Pending</h3>
                  </div>
                  <div class="card-body">
                    <table class="table table-sm table-bordered table-striped">
                  
                      <tbody id="allowances"></tbody>
                    </table>

                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
        </div>
        <div class="row">
          <div class="col-md-12">            
            <!-- <form method="POST" id="sample_form" >
              <button class="btn btn-sm btn-outline-success" name="all_approved" id="approved" type="submit" data-toggle="tooltip" data-placement="top" title="All Approved" ><i class="fa fa-check"></i> All Approved</button>
            </form> -->
            <table class="table table-sm table-bordered table-striped">
            <thead>
              <tr style="text-align:center;">
                <th>#</th>
                <th>Employee Name</th>
                <th>Due Date</th>
                <th>Amount</th>
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
  
  $('#formattendance').validate({
    rules: {
      employee_id: {
        // remote: {
        //   url: "/check_employee_id",
        //   type: "post"
        // }
      },

      nic_new1: { 
        remote: {
          url: "/check_employee_id",
          type: "post"
        }
      },

      nic_old1: { 
        remote: {
          url: "/check_employee_id",
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

setInterval(function(){
    load_attendance(); 
    load_data();  
  }, 2000);

function load_data(query = '')
{
  var employee_id = <?php echo $row['employee_id']; ?>;
      
  $.ajax({
    url:"/employee_no",
    method:"POST",
    data:{employee_id:employee_id,request:27},
    dataType: 'json',

    success:function(response)
    {
      var html='';
      
      if(response.length > 0)
      {
        for(var count = 0; count < response.length; count++)
        {
          html += '<tr>';
          html += '<td style="width:75%;">'+response[count].due_date+'</td>';
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
    
}

function load_attendance()
{
  var employee_id = <?php echo $row['employee_id']; ?>;
  var invoice_id = <?php echo $_GET['edit']; ?>;

  $.ajax({
    url:"/employee_no",
    method:"POST",
    data:{employee_id:employee_id, invoice_id:invoice_id, request:28},
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
          html += '<td style="width:20%;">'+response[count].due_date+'</td>';
          html += '<td style="width:20%;">'+response[count].amount+'</td>';                       
          html += '</tr>'; 
          serial_no++;            
        }
      }
      else
      {
        html += '<tr><td colspan="5" class="text-center">No Data Found</td></tr>';
      }
      document.getElementById('invoicedata').innerHTML = html;
    }
  });  
}


$(document).on('blur', "#first_ins", function(){
    calculateTotal();
  });

$(document).on('blur', "#no_of_months", function(){
    calculateTotal();
  });

$(document).on('blur', "#amount", function(){
    calculateTotal();
  });

function calculateTotal(){
  var totalAmount = 0;
  $("#amount").each(function() {
    var amount = $('#amount').val();
    var first_ins = $('#first_ins').val();
    var no_of_months = $('#no_of_months').val();
    
    if(!first_ins) {
      first_ins = 0;
    }

    if(!no_of_months) {
      no_of_months = 1;
    }

    var balance = amount - first_ins;

    var monthly_ins = balance/no_of_months;

    $('#balance').val(parseFloat(balance));
    $('#monthly_ins').val(parseFloat(monthly_ins));
    
  });  

}

});
</script>

<script>
 
 $(document).ready(function(){

  $('#sample_form').on('submit', function(event){
    event.preventDefault();   
      $.ajax({
        url:"/uniforms_approved",
        method:"POST",
        data:$(this).serialize(),
        beforeSend:function()
        {
          $('#approved').attr('disabled', 'disabled');
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
      $('#approved').attr('disabled', false);
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