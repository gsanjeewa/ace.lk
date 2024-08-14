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
$query = "SELECT department_id, department_name, department_location FROM department ORDER BY department_name ASC";
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row)
{
 $institution .= '<option value="'.$row['department_id'].'">'.$row['department_name'].' - '.$row['department_location'].'</option>';
}

include '../inc/header.php';

?>

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Report</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Report</li>
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
   <form method="POST" target="_blank" id="add_deduction_form" action="/reports/payroll_print_depatment">
   <div class="row">    
    <div class="col-md-4">
      <div class="form-group">
      <div class="input-group date" id="reservationmonth" data-target-input="nearest">
        <input type="text" name="effective_date" id="effective_date" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date("Y-m"); ?>" required/>
        <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
        </div>
      </div>      
     </div>
    </div>
    <div class="col-md-4">
     <select name="status" id="status" class="form-control select2">
       <option value="">Select status</option>
       <option value="all">All</option>
       <option value="1">Approvel</option>
       <option value="3">Re Approvel</option>
       <option value="2">Halt</option>
       <option value="4">Resignation</option>
      </select>
   
    </div>
    <div class="col-md-4">
      <div class="form-group" align="center">
      <button type="button" name="filter" id="filter" class="btn btn-info"> <i class="fas fa-filter"> Filter</i></button>
      <button class="btn btn-primary"><i class="fas fa-print"> Print</i></button>
     </div>
    </div>
    
   </div>
 </form>
   <div class="table-responsive">
    <table id="order_data" class="table table-bordered table-striped table-sm">
     <thead>
      <tr>
       <th>#</th>                        
        <th>EMP No</th>
        <th>Name</th>
        <th>Rank</th>
        <th>Bank</th>
        <th>Account No</th>
        <th>Total Shifts</th>
        <th>OT Hrs</th>
        <th>Ex OT Hrs</th>
        <th>Basic</th>
        <th>For EPF</th>
        <th>Over Time</th>
        <th>Incentive</th>
        <th>Extra OT</th>
        <th>Service Allowance</th>
        <th>Rewards</th>
        <th>Chairman Allowance</th>
        <th>Training and Be</th>
        <th>Pending Payments</th>
        <th>Gross</th>
        <th>EPF 8%</th>
        <th>No Pay Days</th>
        <th>No Pay</th>
        <th>Salary Advance</th>
        <th>Uniforms</th>
        <th>Ration</th>
        <th>Hostel</th>
        <th>Fines</th>
        <th>Death Donation</th>
        <th>Pending Deductions</th>
        <th>Total Deductions</th>
        <th>Net</th>
        <th>EPF 12%</th>
        <th>ETF 3%</th>
        <th>Status</th>
      </tr>
     </thead>
     <tbody>
     </tbody>
     <tfoot>
      <tr>
       <th>Total</th>
       <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th id="total_shifts"></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th id="total_svc"></th>
        <th id="total_rewards"></th>
        <th id="total_chairman"></th>
        <th></th>
        <th></th>
        <th id="total_gross"></th>
        <th id="total_epf_8"></th>
        <th></th>
        <th></th>
        <th id="total_advance"></th>
        <th id="total_uniforms"></th>
        <th id="total_ration"></th>
        <th id="total_hostel"></th>
        <th id="total_fines"></th>
        <th id="total_death"></th>
        <th></th>
        <th></th>
        <th id="total_net"></th>
        <th id="total_epf_12"></th>
        <th id="total_etf"></th>
        <th></th>
      </tr>
     </tfoot>
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

  fill_datatable();
  function fill_datatable(effective_date = '', status='')
  {
   var dataTable = $('#order_data').DataTable({
    "processing" : true,
    "serverSide" : true,
    "searching" : false,
    "lengthChange": false,
    "paging": false,
    "info": false,
    "order" : [],
    "ajax" : {
     url:"/reports_payroll_fetch",
     type:"POST",
     data:{
      effective_date:effective_date,status:status
     }
    },
    
   "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],

    drawCallback:function(settings)
    {
     $('#total_shifts').html(settings.json.total_shifts);
     $('#total_svc').html(settings.json.total_svc);
     $('#total_rewards').html(settings.json.total_rewards);
     $('#total_chairman').html(settings.json.total_chairman);
     $('#total_gross').html(settings.json.total_gross);
     $('#total_epf_8').html(settings.json.total_epf_8);
     $('#total_advance').html(settings.json.total_advance);
     $('#total_uniforms').html(settings.json.total_uniforms);
     $('#total_ration').html(settings.json.total_ration);
     $('#total_hostel').html(settings.json.total_hostel);
     $('#total_fines').html(settings.json.total_fines);
     $('#total_death').html(settings.json.total_death);
     $('#total_net').html(settings.json.total_net);
     $('#total_epf_12').html(settings.json.total_epf_12);
     $('#total_etf').html(settings.json.total_etf);     
    },
    dom: 'Bfrtip',
    buttons: [
      {
        extend:'excelHtml5',
        title:'Payroll_'+$('#effective_date').val(),
        footer:true
      }
   ]
   });
}

$('#filter').click(function(){
   var effective_date = $('#effective_date').val();
   var status = $('#status').val();
   if(effective_date != '' && status !='')
   {
    $('#order_data').DataTable().destroy();
    fill_datatable(effective_date, status);
   }
   else
   {
    alert('Select Both filter option');
    $('#order_data').DataTable().destroy();
    fill_datatable();
   }
  }); 
    
  
});

</script>