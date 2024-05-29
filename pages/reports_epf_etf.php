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
   <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
      <form method="POST" target="_blank" id="add_deduction_form" action="">
     <div class="form-group">
      <div class="input-group date" id="reservationmonth" data-target-input="nearest">
        <input type="text" name="effective_date" id="effective_date" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date("Y-m"); ?>" required/>
        <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
        </div>
      </div>      
     </div>     

     <div class="form-group" align="center">
      <button type="button" name="filter" id="filter" class="btn btn-info"> <i class="fas fa-filter"> Filter</i></button>      
     </div>
     </form>
   
    </div>
    <div class="col-md-4"></div>
   </div>
   <div class="table-responsive">
    <table id="order_data" class="table table-bordered table-striped table-sm">
     <thead>
      <tr>
       <th>#</th>                        
        <th>EMP No</th>
        <th>Rank</th>
        <th>Name</th>        
        <th>For EPF</th>        
        <th>EPF 8%</th>        
        <th>EPF 12%</th>
        <th>ETF 3%</th>
      </tr>
     </thead>
     <tbody>
     </tbody>
     <tfoot>
      <tr>
        <th></th> 
        <th></th> 
        <th></th> 
       <th>Total</th>
       <th></th>        
        <th id="total_epf_8"></th>        
        <th id="total_epf_12"></th>
        <th id="total_etf"></th> 
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
  function fill_datatable(effective_date = '')
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
     url:"/epf_etf_fetch",
     type:"POST",
     data:{
      effective_date:effective_date
     }
    },
    
   "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],

    drawCallback:function(settings)
    {     
     $('#total_epf_8').html(settings.json.total_epf_8);     
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
   if(effective_date != '')
   {
    $('#order_data').DataTable().destroy();
    fill_datatable(effective_date);
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