<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 3) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions to View Employee.</div>';
    header('location:/dashbpard');
    exit();

}

$error = false;
if (isset($_POST['add_new'])){

  if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions to Add Employee.</div>'; 
    header('location:/employee_list/employee');  
    exit();

}

  $employee_id=  $_SESSION['empid'];
  $holder_name  =  strtoupper(trim($_POST['holder_name']));
  $bank_name  =  $_POST['bank_name'];
  $bank_branch  =  $_POST['bank_branch'];
  $account_no =  $_POST['account_no'];
  $statement = $connect->prepare("SELECT employee_id, account_no, bank_name FROM bank_details WHERE employee_id=:employee_id AND bank_name=:bank_name AND branch_name=:bank_branch AND account_no=:account_no");
  $statement->bindParam(':employee_id', $employee_id);
  $statement->bindParam(':bank_name', $bank_name);
  $statement->bindParam(':bank_branch', $bank_branch);
  $statement->bindParam(':account_no', $account_no);

  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>This Bank Details Already existing.</div>';      
  }

  $bank_account = str_pad($_POST['account_no'], 12, "0", STR_PAD_LEFT);

  if (!$error) {

    $data = array(
      ':employee_id'        =>  $employee_id,
      ':holder_name'        =>  $holder_name,
      ':bank_name'          =>  $bank_name,
      ':branch_name'        =>  $bank_branch,
      ':branch_no'          =>  '',
      ':account_no'         =>  $bank_account,      
    );
   
    $query = "
    INSERT INTO `bank_details`(`employee_id`, `holder_name`, `bank_name`, `branch_name`, `branch_no`, `account_no`)
        VALUES (:employee_id, :holder_name, :bank_name, :branch_name, :branch_no, :account_no)
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
  }
}

if (isset($_POST['add_promote'])){

  if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions to Add Employee.</div>'; 
    header('location:/employee_list/employee/'.$_GET['edit'].'');  
    exit();

}

$query = 'SELECT id FROM salary WHERE employee_id="'.$_SESSION["empid"].'" ORDER BY id DESC LIMIT 1';

$statement = $connect->prepare($query);
$statement->execute();
$total_data = $statement->rowCount();
$result = $statement->fetchAll();
if ($total_data > 0) {                  
  foreach($result as $row)
  {
    $salary_id = $row['id'];
  }
}else{
  $salary_id = '';
}

  if (!$error) {

    $data = array(
      ':employee_id'    =>  $_SESSION["empid"],
      ':position_id'    =>  $_POST['position_id'],
      ':promoted_date'  =>  $_POST['promoted_date'],
      ':promotion_pay'  =>  $_POST['promotion_pay'],

    );
   
    $query = "
    INSERT INTO `promotions`(`employee_id`, `position_id`, `promoted_date`, `promotion_pay`)
        VALUES (:employee_id, :position_id, :promoted_date, :promotion_pay);
    ";   
    
    if ($_POST['basic_salary'] > 0){
      $data_salary = array(
        ':employee_id'    =>  $_SESSION["empid"],
        ':basic_salary'    =>  $_POST['basic_salary'],
        ':promoted_date'  =>  $_POST['promoted_date'],
        ':salary_id'    =>  $salary_id,  
    );
      $query_salary = "INSERT INTO `salary`(`employee_id`, `basic_salary`, `increment_date`) VALUES (:employee_id, :basic_salary, :promoted_date);
      UPDATE salary SET status=1 WHERE id=:salary_id;";

      $statement = $connect->prepare($query_salary);
      $statement->execute($data_salary);
    }

    $statement = $connect->prepare($query);

    if($statement->execute($data))
    {
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
                  
    }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
         
    }
  }
}

if (isset($_POST['employee_disable'])){

  if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/institution_list/institution');
    exit();
}

  $data = array(
    ':id'      =>  $_POST['row_id']
       
  );

  $query = "UPDATE `join_status` SET `employee_status`=4 WHERE `join_id`=:id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
               
  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
    
}


if (isset($_POST['employee_enable'])){

  if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/institution_list/institution');
    exit();
}

  $data = array(
    ':id'      =>  $_POST['row_id']
       
  );

  $query = "UPDATE `join_status` SET `employee_status`=0 WHERE `join_id`=:id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
               
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
            <h1 class="m-0 text-dark">Employee</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Employee</li>
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
          <div class="col-md-12">
            
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Employee List</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body"> 
                <table id="emp_data" class="table table-bordered table-striped">
                    <thead style="text-align: center; width: 100%;">
                      <tr>
                        <th>#</th>                        
                        <th>Employee Name</th>
                        <th>NIC No</th>
                        <th>Date of Join</th>
                        <th>Basic</th>
                        <th>Location</th>
                        <th>Bank Details</th>
                        <th>Permanent Address</th>
                        <th>Mobile No</th>
                        <th>Status</th>
                        <th>Action</th>                                                  
                      </tr>
                    </thead>
                    <tbody>
                      </tbody>
                  </table>

                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->  
            
          </div>          
        </div>
        <!-- /.row -->
       
      
      </div><!-- /.container-fluid -->
    </section>   

    <!--  start  modal -->
    <div id="editData4" class="modal fade">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Bank Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="info_update4">
            <?php @include("/bank_edit");?>
          </div>
          <!-- <div class="modal-footer ">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div> -->
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
    </div>
    <!--   end modal -->

    <!--  start  modal -->
    <div id="editpromote" class="modal fade">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Promotion</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="info_promote">
            <?php @include("/promote_edit");?>
          </div>
          <!-- <div class="modal-footer ">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div> -->
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
    </div>
    <!--   end modal --> 

<?php
include '../inc/footer.php';
?>

<script type="text/javascript">

    $(document).ready(function(){

      var t = $('#emp_data').DataTable({
        "processing":true,
        "serverSide":true,
        "autoWidth": true,
        "scrollX": false,
        "responsive":true,
        "order":[],
        "ajax":{
         url:"/employee_fetch",
         type:"POST"
        },
        "columnDefs":[
         {
            "searchable": false,
            "orderable": false,
            "targets": 0
        }
        ],

        order: [[1, 'asc']]

       });

      t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
      }).draw();


      $(document).on('click','.edit_data4',function(){
        $("#editData4").modal({
            backdrop: 'static',
            keyboard: false
        });
        var edit_id4=$(this).attr('data-id');
        $.ajax({
          url:"/bank_edit",
          type:"post",
          data:{edit_id4:edit_id4},
          success:function(data){
            $("#info_update4").html(data);
            $("#editData4").modal('show');
          }
        });
      });

      $(document).on('click','.edit_promote',function(){
        $("#editpromote").modal({
            backdrop: 'static',
            keyboard: false
        });
        var edit_pro_id=$(this).attr('data-id');
        $.ajax({
          url:"/promote_edit",
          type:"post",
          data:{edit_pro_id:edit_pro_id},
          success:function(data){
            $("#info_promote").html(data);
            $("#editpromote").modal('show');
          }
        });
      });

      $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

    });
  </script>