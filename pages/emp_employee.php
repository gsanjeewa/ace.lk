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

if (isset($_POST['add_epf'])){

  if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions to Add Employee.</div>'; 
    header('location:/employee_list/employee/'.$_GET['edit'].'');  
    exit();
  }

  if (!$error) {

    $data = array(
      ':employee_id'    =>  $_SESSION["empid"],
      ':from_date'    =>  date('Y-m-d', strtotime($_POST['from_date'])),
      ':to_date'  =>  date('Y-m-d', strtotime($_POST['to_date'])),
    );
   
    $query = "
    INSERT INTO epf_excluded(employee_id, from_date, to_date)
        VALUES (:employee_id, :from_date, :to_date);
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

// Function to fetch employee details
function fetchEmployeeDetails($connect) {
  $query = "SELECT e.employee_id, e.surname, e.initial, j.employee_no, e.nic_no, e.permanent_address, e.mobile_no, j.employee_status, p.position_abbreviation, j.join_id, j.join_date, j.location 
            FROM employee e 
            INNER JOIN join_status j ON e.employee_id = j.employee_id 
            INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
            INNER JOIN promotions c ON j.employee_id=c.employee_id 
            INNER JOIN (SELECT employee_id, MAX(id) maxproid FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxproid 
            INNER JOIN position p ON c.position_id=p.position_id 
            ORDER BY ABS(j.employee_no) DESC";
  $statement = $connect->prepare($query);
  $statement->execute();
  return $statement->fetchAll();
}

// Function to fetch position abbreviation
function fetchPositionAbbreviation($connect, $employee_id) {
  $query = "SELECT c.position_abbreviation 
            FROM promotions a 
            INNER JOIN position c ON a.position_id=c.position_id 
            INNER JOIN (SELECT employee_id, MAX(id) maxid FROM promotions GROUP BY employee_id) b ON a.employee_id = b.employee_id AND a.id = b.maxid 
            WHERE a.employee_id = :employee_id";
  $statement = $connect->prepare($query);
  $statement->bindParam(':employee_id', $employee_id);
  $statement->execute();
  return $statement->fetchColumn();
}

// Function to fetch bank details
function fetchBankDetails($connect, $employee_id) {
  $query = "SELECT a.account_no, b.bank_name, b.bank_no, c.branch_name, c.branch_no 
            FROM bank_details a 
            INNER JOIN bank_name b ON a.bank_name=b.id 
            INNER JOIN bank_branch c ON a.branch_name=c.id 
            WHERE a.employee_id = :employee_id";
  $statement = $connect->prepare($query);
  $statement->bindParam(':employee_id', $employee_id);
  $statement->execute();
  return $statement->fetch();
}

// Function to fetch basic salary
function fetchBasicSalary($connect, $employee_id) {
  $query = "SELECT basic_salary 
            FROM salary a 
            INNER JOIN (SELECT employee_id, MAX(id) maxid FROM salary GROUP BY employee_id) b ON a.employee_id = b.employee_id AND a.id = b.maxid 
            WHERE a.employee_id = :employee_id";
  $statement = $connect->prepare($query);
  $statement->bindParam(':employee_id', $employee_id);
  $statement->execute();
  return $statement->fetchColumn();
}

// Function to fetch department location
function fetchDepartmentLocation($connect, $location) {
  $query = "SELECT department_name, department_location 
            FROM department 
            WHERE department_id = :location";
  $statement = $connect->prepare($query);
  $statement->bindParam(':location', $location);
  $statement->execute();
  return $statement->fetch();
}

// Fetching employee details
$employees = fetchEmployeeDetails($connect);


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
                  <table id="example2" class="table table-bordered table-striped">
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
                                <?php
                                $sno = 1;
                                foreach ($employees as $employee) {
                                    $position = fetchPositionAbbreviation($connect, $employee['join_id']);
                                    $bankDetails = fetchBankDetails($connect, $employee['employee_id']);
                                    $basicSalary = fetchBasicSalary($connect, $employee['join_id']);
                                    $departmentLocation = fetchDepartmentLocation($connect, $employee['location']);

                                    $joinDate = new DateTime($employee['join_date']);
                                    $currentDate = new DateTime();
                                    $interval = $currentDate->diff($joinDate);

                                    echo '<tr>
                                        <td>' . $sno . '</td>
                                        <td style="text-align: left;">' . $employee['employee_no'] . ' ' . $position . ' ' . $employee['surname'] . ' ' . $employee['initial'] . '</td>
                                        <td>' . $employee['nic_no'] . '</td>
                                        <td><center>
                                            <dt>' . $employee['join_date'] . '</dt>
                                            <dd>' . $interval->y . 'Y ' . $interval->m . 'M ' . $interval->d . 'D</dd></center></td>
                                        <td style="text-align:right;">' . number_format($basicSalary) . '</td>
                                        <td>' . $departmentLocation['department_name'] . '-' . $departmentLocation['department_location'] . '</td>
                                        <td><dl>
                                            <dt>' . $bankDetails['bank_name'] . ' (' . $bankDetails['bank_no'] . ')</dt>
                                            <dd>' . $bankDetails['branch_name'] . ' (' . str_pad($bankDetails['branch_no'], 3, "0", STR_PAD_LEFT) . ')</dd>
                                            <dd>' . str_pad($bankDetails['account_no'], 12, "0", STR_PAD_LEFT) . '</dd>
                                        </dl></td>
                                        <td>' . $employee['permanent_address'] . '</td>
                                        <td>' . $employee['mobile_no'] . '</td>
                                        <td>
                                            <center>
                                                <span class="badge badge-' . ($employee['employee_status'] == 0 ? 'success">Present' : ($employee['employee_status'] == 1 ? 'danger">Absent' : ($employee['employee_status'] == 2 ? 'warning">Re-Enlisted' : ($employee['employee_status'] == 3 ? 'warning">Resignation' : 'secondary">Disable')))) . '</span>
                                            </center>
                                        </td>
                                        <td>
                                            <center>
                                                <a href="/employee_list/employee/' . $employee['employee_id'] . '" class="btn btn-sm btn-outline-warning" data-toggle="tooltip" data-placement="left" title="View Profile"><i class="fa fa-eye"></i></a>
                                                <button class="edit_data4 btn btn-sm btn-outline-success" data-id="' . $employee['employee_id'] . '" type="button" data-toggle="tooltip" data-placement="top" title="Add Bank"><i class="fa fa-bank"></i></button>
                                                <button class="edit_promote btn btn-sm btn-outline-secondary" data-id="' . $employee['join_id'] . '" type="button" data-toggle="tooltip" data-placement="top" title="Promote"><i class="fa fa-plus"></i></button>
                                                <a href="/employee_list/add_employee/' . $employee['employee_id'] . '" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>
                                                <button class="edit_epf btn btn-sm btn-outline-info" data-id="' . $employee['join_id'] . '" type="button" data-toggle="tooltip" data-placement="top" title="EPF Excluded "><i class="fa fa-bank"></i></button>
                                                <form action="" method="POST" enctype="multipart/form-data">
                                                    <input type="hidden" name="row_id" value="' . $employee['join_id'] . '">
                                                    <button class="btn btn-sm btn-outline-' . ($employee['employee_status'] == 4 ? 'success" type="submit" data-toggle="tooltip" data-placement="top" title="Enable" name="employee_enable"><i class="fas fa-toggle-off"></i></button>' : 'danger" type="submit" data-toggle="tooltip" data-placement="top" title="Disable" name="employee_disable"><i class="fas fa-toggle-on"></i></button>') . '
                                                </form>
                                            </center>
                                        </td>
                                    </tr>';
                                    $sno++;
                                }
                                ?>
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

    <!--  start  modal -->
    <div id="editepf" class="modal fade">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">EPF Excluded</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="info_epf">
            <?php @include("/epf_excluded_edit");?>
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
  $(document).ready(function() {

    $('#example2').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "scrollX": false,
    });

 
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

  });
</script>
<script type="text/javascript">

    $(document).ready(function(){
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

      $(document).on('click','.edit_epf',function(){
        $("#editepf").modal({
            backdrop: 'static',
            keyboard: false
        });
        var edit_epf_id=$(this).attr('data-id');
        $.ajax({
          url:"/epf_excluded_edit",
          type:"post",
          data:{edit_epf_id:edit_epf_id},
          success:function(data){
            $("#info_epf").html(data);
            $("#editepf").modal('show');
          }
        });
      });
    });
  </script>