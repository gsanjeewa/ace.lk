<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 57) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}
$error = false;

if (isset($_POST['approved'])){

  if (checkPermissions($_SESSION["user_id"], 77) == "false") {
    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
    header('location:/loan/loan_list');   
    exit();
  }

  $loan_id = $_POST['loan_id'];
  $statement = $connect->prepare("SELECT loan_id FROM loan_schedules WHERE loan_id=:loan_id");
  $statement->bindParam(':loan_id', $loan_id);
  $statement->execute();
    
    if($statement->rowCount()>0){
      $error = true;
        $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Already existing.</div>';
    }

  $query = 'SELECT * FROM loan_list WHERE id="'.$_POST['loan_id'].'"';
  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  foreach($result as $plan)
  { 
    $employee_id=$plan['employee_id'];
    $paid_amount=$plan['monthly'];

    $star_date=array();
    if ($plan['loan_plan']==1) {
      $star_date=array($plan['date_effective']);
    }else{
      $start    = new DateTime($plan['date_effective']);    
      $end      = $plan['loan_plan']-1;
      $interval = DateInterval::createFromDateString('1 month');
      $period   = new DatePeriod($start, $interval, $end);

      
      foreach ($period as $dt) {
        $star_date[]=$dt->format("Y-m-d");
      }
    }

    

    for($i= 0; $i < count($star_date); $i++){
       
      if (!$error) {
        $data = array(
          ':loan_id'     =>  $_POST['loan_id'],
          ':employee_id' =>  $employee_id,
          ':paid_amount' =>  $paid_amount,
          ':date_due'    =>  $star_date[$i],
          ':status'      =>  2,        
        );

        $query = "UPDATE `loan_list` SET `status`=:status WHERE `id`=:loan_id;
        INSERT INTO `loan_schedules`(`employee_id`, `loan_id`, `date_due`, `paid_amount`) 
        VALUES (:employee_id, :loan_id, :date_due, :paid_amount);
        ";
          
        $statement = $connect->prepare($query);

        if($statement->execute($data))
        {
          header('location:/loan/loan_list');            
        }else{
            $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
        } 
      }
    }

  }
    
}

if (isset($_POST['not_approved'])){

  if (checkPermissions($_SESSION["user_id"], 77) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';   
     header('location:/loan/loan_list');   
    exit();
  }

  $data = array(
    ':loan_id'     =>  $_POST['loan_id'],
    ':status'     =>  4,
  );

  $query = "UPDATE `loan_list` SET `status`=:status WHERE `id`=:loan_id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    header('location:/loan/loan_list');            
  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
    
}

if (isset($_POST['remove_loan'])){

  if (checkPermissions($_SESSION["user_id"], 77) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/loan/loan_list'); 
    exit();
}

  $data = array(
    ':id'      =>  $_POST['loan_id']       
  );

  $query = "DELETE FROM `loan_list` WHERE `id`=:id;
  DELETE FROM `loan_schedules` WHERE `loan_id`=:id;
  ";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Delete Success.</div>';
    header('location:/loan/loan_list');            
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
            <h1 class="m-0 text-dark">Loan</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Loan</li>
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
          if ( isset($_SESSION["msg"]) ) {
            ?>
            <div class="col-xl-12 col-md-6 mb-4">
              <?php echo $_SESSION["msg"]; ?>
            </div>
              <?php
          }

          ?>
        </div>
        <div class="row">          
          <div class="col-md-12">
            
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Loan List</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">  

                  <?php
                  
                  $query = 'SELECT * FROM loan_list ORDER BY status ASC';

                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $total_data = $statement->rowCount();

                  $result = $statement->fetchAll();

                  ?>

                  <table id="example2" class="table table-bordered table-striped">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>                        
                        <th>Employee Name</th>
                        <th>NIC No</th>
                        <th>Loan Plan</th>
                        <th>Loan Amount</th>
                        <th>Monthly Installment</th>
                        <th>Status</th>
                        <th>Issue Date</th>
                        <th>Start Deduct Date</th>
                        <th>Action</th>                                                  
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row)
                      {
                        $query = 'SELECT e.employee_id, e.surname, e.initial, e.nic_no, j.employee_no, p.position_abbreviation FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN position p ON e.position_id=p.position_id WHERE j.join_id="'.$row['employee_id'].'" ORDER BY e.employee_id DESC';

                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $total_data = $statement->rowCount();
                        $result = $statement->fetchAll();
                        foreach($result as $row_employee):                          
                        endforeach;

                        $statement = $connect->prepare('SELECT c.position_abbreviation FROM promotions a INNER JOIN position c ON a.position_id=c.position_id INNER JOIN (SELECT employee_id, MAX(id) maxid FROM promotions GROUP BY employee_id) b ON a.employee_id = b.employee_id AND a.id = b.maxid WHERE a.employee_id="'.$row['employee_id'].'"');
                        $statement->execute();
                        $total_position = $statement->rowCount();
                        $result = $statement->fetchAll();
                        if ($total_position > 0) :
                          foreach($result as $position_name):
                          
                            $position_id = $position_name['position_abbreviation'];
                          endforeach;
                          else:
                            $position_id ='';
                          endif;
                                      
                        if ($row['status'] == 0) {
                          $status='<span class="right badge badge-primary">Request</span>';
                        }elseif ($row['status'] == 2) {
                          $status='<span class="right badge badge-warning">Released</span>';
                        }elseif ($row['status'] == 3){
                          $status='<span class="right badge badge-success">Completed</span>';
                        }elseif ($row['status'] == 4){
                          $status='<span class="right badge badge-danger">Denied</span>';
                        }else{
                          $status='<span class="right badge badge-secondary">Unidentified</span>';
                        }
                       
                        ?>
                        <tr>
                            <td><?php echo $sno; ?></td>
                            <td><?php echo $row_employee['employee_no'].' '.$position_id.' '.$row_employee['surname'].' '.$row_employee['initial']; ?></td>
                            <td ><?php echo $row_employee['nic_no'];?></td>
                            <td><center><?php echo $row['loan_plan'];?></center></td>
                            <td style="text-align: right;"><?php echo number_format($row['loan_amount']);?></td>
                            <td><center><?php echo number_format($row['monthly']);?></center></td>
                            <td><center><?php echo $status;?></center></td>
                            <td><center><?php echo $row['request_date'];?></center></td>
                            <td><center><?php echo date('Y-m', strtotime($row['date_effective']));?></center></td>
                            <td>
                              <center>
                                <form action="" method="post" >
                                  <input type="hidden" name="loan_id" value="<?php echo $row['id'];?>">
                                <?php 
                                if ($row['status'] == 0) {
                                  ?>
                          <button class="btn btn-sm btn-outline-primary edit_loan" data-id="<?php echo $row['id'];?>" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
                          <button class="btn btn-sm btn-outline-danger not_approved" name="not_approved" data-toggle="tooltip" data-placement="top" title="Not Approved"><i class="fa fa-times"></i></button>
                          <button class="btn btn-sm btn-outline-success approved" name="approved" data-toggle="tooltip" data-placement="top" title="Approved"><i class="fa fa-check"></i></button>

                          <?php
                        }elseif ($row['status'] == 2) {
                          ?>
                          <button class="btn btn-sm btn-outline-warning view_loan" name="view_loan" id="view_loan" type="button" data-id="<?php echo $row['id']?>" data-toggle="tooltip" data-placement="top" title="View Loan"><i class="fa fa-eye"></i></button>
                          <button class="btn btn-sm btn-outline-danger float-right" name="remove_loan"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button>
                          <?php
                        }elseif ($row['status'] == 3){
                          $status='<span class="right badge badge-success">Completed</span>';
                        }elseif ($row['status'] == 4){
                          ?>
                          <button class="btn btn-sm btn-outline-success approved" name="approved" data-toggle="tooltip" data-placement="top" title="Approved"><i class="fa fa-check"></i></button>
                          <?php
                        }
                        ?>
                        </form>
                              </center>
                            </td>
                        </tr>
                        <?php
                        $sno ++;
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
      "responsive": false,
      "scrollX": true,
    }); 

    $('.edit_loan').click(function(){
        var $id=$(this).attr('data-id');
        location.href = "/loan/new_loan_req/"+$id;
        
      });

      $('.view_loan').click(function(){
        var $id=$(this).attr('data-id');
        location.href = "/loan/loan_list/"+$id;
        
      }); 

      $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });           

    });
  </script>