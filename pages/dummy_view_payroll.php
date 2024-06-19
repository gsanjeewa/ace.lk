
<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();

require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 95) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$institution = '';
$query = "SELECT b.department_id, b.department_name, b.department_location FROM d_shifts_rate a INNER JOIN department b ON a.department_id=b.department_id ORDER BY b.department_name ASC";
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row)
{
 $institution .= '<option value="'.$row['department_id'].'">'.$row['department_name'].'-'.$row['department_location'].'</option>';
}

include '../inc/header.php';

?>

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dummy</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dummy</li>
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
          <div class="container box">
   <h3 align="center">Payroll</h3>
   <br />

   <div class="form-group" id="process" style="display:none;">
        <div class="progress">
       <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="">
       </div>
      </div>
       </div>


   <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
      <form method="POST" target="_blank" id="add_deduction_form" action="/dummy/payroll_print_depatment">
      
     <div class="form-group">
      <div class="input-group date" id="reservationmonth" data-target-input="nearest">
        <input type="text" name="effective_date" id="effective_date" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date("Y-m"); ?>" required/>
        <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
        </div>
      </div>
      <!-- <select name="filter_gender" id="filter_gender" class="form-control" required>
       <option value="">Select Gender</option>
       <option value="Male">Male</option>
       <option value="Female">Female</option>
      </select> -->
     </div>
     <div class="form-group">
      <select name="filter_institution" id="filter_institution" class="form-control select2" required>
       <option value="">Select Institution</option>
       <?php echo $institution; ?>
      </select>
     </div>
     <div class="form-group" align="center">
      <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
      <button class="btn btn-primary"><i class="fas fa-print"> Print</i></button>
      
     </div>
   </form>
   <form method="POST" id="sample_form">
        <input type="hidden" name="payroll_id" value="<?php echo $_GET['view']?>">
        <button class="btn btn-outline-primary btn-sm" name="calculate_payroll" id="calculate_payroll" type="submit" data-toggle="tooltip" data-placement="top" title="Calculate"><i class="fas fa-calculator"></i> Re-Caclulate Payroll</button>
      </form>
    </div>
    <div class="col-md-4"></div>
   </div>
   <div class="table-responsive">
    <table id="customer_data" class="table table-bordered table-striped">
     <thead>
      <tr>
        <th>#</th>                        
        <th>EMP No</th>
        <th>Name</th>
        <th>Rank</th>
        <th>Basic Salary</th>
        <th>BRA Allowance I</th>
        <th>BRA Allowance II</th>
        <th>Total Working Days</th>
        <th>Normal Working Days</th>
        <th>Normal OT Hrs</th>
        <th>Poya Days</th>
        <th>Mercantile Days</th>
        <th>Mercantile OT Hrs</th>
        <th>Half Days</th>
        <th>Half day OT hrs</th>
        <th>Paid Leave Days</th>
        <th>Total</th>
        <th>Normal Day Earning</th>
        <th>Poya Day Payment</th>
        <th>Mercantile Payment</th>
        <th>Payment for leave days</th>
        <th>Over Time x (1.5)</th>
        <th>Over Time x (3)</th>
        <th>Performance Incentive</th>
        <th>For EPF</th>
        <th>Arrears Payment</th>
        <th>Gross Salary</th>
        <th>Employee EPF (8%)</th>
        <th>No Pay Days</th>
        <th>No Pay</th>
        <th>Salary Advance</th>
        <th>Ration</th>
        <th>Hostel</th>
        <th>Fines</th>
        <th>Total Deductions</th>
        <th>Net Salary</th>
        <th>Employer EPF (12%)</th>
        <th>Employer ETF (3%)</th>
      </tr>
     </thead>
    </table>
    <br />
    <br />
    <br />
   </div>
  </div>         
        </div>
        <!-- /.row -->
       
      
      </div><!-- /.container-fluid -->
    </section>   

 <?php
include '../inc/footer.php';
?>
<script type="text/javascript" language="javascript" >
 $(document).ready(function(){
  load_data();
  fill_datatable();

      function load_data(query = '')
{
  var query = $('#filter_institution').val();  
  
  $.ajax({
    url:"/employee_no",
    method:"POST",
    data:{query:query,request:20},
    dataType: 'json',

    success:function(response)
    {
      var len = response.length;
      
      var department_name='';
      
      if(len > 0){
          window.department_name = response[0]['department_name'];
      }      
    }
  });
      
}
  
  function fill_datatable(effective_date = '', filter_institution = '')
  {
   var dataTable = $('#customer_data').DataTable({
    "processing" : true,
    "serverSide" : true,
    "order" : [],
    "searching" : false,
    "scrollX": false,
    "lengthChange": false,
    "paging": false,    
    "ajax" : {
     url:"/payroll_fetch",
     type:"POST",
     data:{
      effective_date:effective_date, filter_institution:filter_institution
     }
    },
    dom: 'Bfrtip',
    buttons: [
      {
        extend:'excelHtml5',
        title:'Dummy Payroll '+window.department_name+' '+$('#effective_date').val(),
        footer:true
      }
   ],
   "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ]
   });
  }
  
  $('#filter').click(function(){
   var effective_date = $('#effective_date').val();
   var filter_institution = $('#filter_institution').val();
   if(effective_date != '' && filter_institution != '')
   {
    $('#customer_data').DataTable().destroy();
    fill_datatable(effective_date, filter_institution);
   }
   else
   {
    alert('Select Both filter option');
    $('#customer_data').DataTable().destroy();
    fill_datatable();
   }
  });

  $('#filter_institution').change(function(){
  var query = $('#filter_institution').val();
  load_data(1, query);
  }); 

   $('#sample_form').on('submit', function(event){
   event.preventDefault();
    $.ajax({
     url:"/d_process",
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
  
 });
 
</script>

