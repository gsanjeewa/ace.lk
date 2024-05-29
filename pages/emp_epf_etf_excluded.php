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

if (isset($_POST['add_epf'])){

  if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions to Add Employee.</div>'; 
    header('location:/employee_list/employee/'.$_GET['edit'].'');  
    exit();
  }

  if (!$error) {

    $data = array(
      ':employee_id'    =>  $_POST['employee_id'],
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
                  <h3 class="card-title">EPF/ETF Excluded List</h3> 
                  <button class="edit_epf btn btn-sm bg-gradient-primary float-right" type="button" data-toggle="tooltip" data-placement="top" title="EPF Excluded "><i class="fas fa-bank" ></i> Add</button>
                </div>
                  <!-- /.card-header -->
                <div class="card-body">  
                  <?php
                  
                  $query="SELECT e.employee_id, e.surname, e.initial, j.employee_no, p.position_abbreviation, j.join_id, a.from_date, a.to_date FROM epf_excluded a
                  INNER JOIN join_status j ON a.employee_id = j.join_id 
                  INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
                  INNER JOIN employee e ON j.employee_id=e.employee_id                  
                  INNER JOIN promotions c ON j.employee_id=c.employee_id INNER JOIN (SELECT employee_id, MAX(id) maxproid FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxproid 
                  INNER JOIN position p ON c.position_id=p.position_id ORDER BY ABS(j.employee_no) DESC";

                  /*$query = 'SELECT * FROM employee ORDER BY employee_id ASC';*/

                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $total_data = $statement->rowCount();
                  $result = $statement->fetchAll();

                  ?>

                  <table id="example2" class="table table-bordered table-striped">
                    <thead style="text-align: center; width: 100%;">
                      <tr>
                        <th>#</th>                        
                        <th>Employee Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Action</th>                                                  
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row)
                      {
                        $statement = $connect->prepare('SELECT c.position_abbreviation FROM promotions a INNER JOIN position c ON a.position_id=c.position_id INNER JOIN (SELECT employee_id, MAX(id) maxid FROM promotions GROUP BY employee_id) b ON a.employee_id = b.employee_id AND a.id = b.maxid WHERE a.employee_id="'.$row['join_id'].'"');
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

                          if (!empty($row['employee_no'])) {
                              $employee_epf=$row['employee_no'];
                          }else{
                            $employee_epf='';
                          }

                                                  
                        ?>
                        <tr>
                            <td><center><?php echo $sno; ?></center></td>
                            <td style="text-align: left;"><?php echo $employee_epf.' '.$position_id.' '.$row['surname'].' '.$row['initial'];?></td>
                            <td><center><?php echo date('Y F', strtotime($row['from_date']));?></center></td>
                            <td><center><?php echo date('Y F', strtotime($row['to_date']));?></center></td>                          
                            <td>
                              <center>

								                <!-- <a href="/employee_list/employee/<?php echo $row['employee_id']?>" class="btn btn-sm btn-outline-warning" data-toggle="tooltip" data-placement="left" title="View Profile"><i class="fa fa-eye"></i></a>
                                
                                <button class="edit_data4 btn btn-sm btn-outline-success" data-id="<?php echo $row['employee_id'];?>" type="button" data-toggle="tooltip" data-placement="top" title="Add Bank"><i class="fa fa-bank"></i></button>

                                <button class="edit_promote btn btn-sm btn-outline-secondary" data-id="<?php echo $row['join_id'];?>" type="button" data-toggle="tooltip" data-placement="top" title="Promote"><i class="fa fa-plus"></i></button>

                                <a href="/employee_list/add_employee/<?php echo $row['employee_id']?>" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>

                                <button class="edit_epf btn btn-sm btn-outline-info" data-id="<?php echo $row['join_id'];?>" type="button" data-toggle="tooltip" data-placement="top" title="EPF Excluded "><i class="fa fa-bank"></i></button>

                                <form action="" method="POST" enctype="multipart/form-data">
                                  <input type="hidden" name="row_id" value="<?php echo $row['join_id']; ?>">

                                  <?php if ($row['employee_status']==4): ?>
                                    <button class="btn btn-sm btn-outline-success" type="submit" data-toggle="tooltip" data-placement="top" title="Enable" name="employee_enable"><i class="fas fa-toggle-off"></i></button>
                                  
                                
                                  <?php else:?>
                                    
                                    <button class="btn btn-sm btn-outline-danger" type="submit" data-toggle="tooltip" data-placement="top" title="Disable" name="employee_disable"><i class="fas fa-toggle-on"></i></button>
                                    

                                  <?php endif ?>
								                                              
								                </form> -->
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