<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
require_once 'system_permissions.php'; 
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 27) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

if (isset($_POST['remove_attendance'])){

  if (checkPermissions($_SESSION["user_id"], 28) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/attendance_list/attendance');
    exit();
}

  $data = array(
    ':id'      =>  $_POST['att_id']
       
  );

  $query = "DELETE FROM `attendance` WHERE `id`=:id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Delete Success.</div>';
    header('location:/attendance_list/attendance');            
  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
    
}

if (isset($_POST['update'])){

  if (checkPermissions($_SESSION["user_id"], 26) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/attendance_list/attendance');
    exit();
}

  $data = array(
    ':id'           =>  $_SESSION['attendanceeditbid'],
    ':position_id'  =>  $_POST['position_id'],
    ':no_of_shifts' =>  $_POST['no_of_shifts'],
    ':extra_ot_hrs' =>  $_POST['extra_ot_hrs']

       
  );

  $query = "UPDATE `attendance` SET `position_id`=:position_id, `no_of_shifts`=:no_of_shifts, `extra_ot_hrs`=:extra_ot_hrs WHERE `id`=:id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Delete Success.</div>';
    header('location:/attendance_list/attendance');            
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
            <h1 class="m-0 text-dark">Attendance List</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Attendance List</li>
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
                  <h3 class="card-title">Attendance List</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">  
                  <?php
                  $query = 'SELECT DISTINCT employee_id FROM attendance WHERE YEAR(start_date) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(start_date) = MONTH(CURDATE() - INTERVAL 1 MONTH)';
                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $total_data = $statement->rowCount();
                  $result = $statement->fetchAll();
                  ?>

                  <table id="example2" class="table table-bordered table-striped table-sm">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>                        
                        <th>Employee Name</th>
                        <th>No of Shift</th>                                                
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row)
                      {
                        $query = 'SELECT j.join_id, e.surname, e.initial, j.employee_no, p.position_abbreviation FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN position p ON e.position_id=p.position_id WHERE j.join_id="'.$row['employee_id'].'" AND (j.employee_status = 0 OR j.employee_status = 2) ORDER BY e.employee_id DESC';

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

                  
                        ?>
                        <tr>
                            <td><center><?php echo $sno; ?></center></td>
                            <td style="text-align: left;"><?php echo $row_employee['employee_no'].' '.$position_id.' '.$row_employee['surname'].' '.$row_employee['initial'] ?></td>
                            <td>
                              
                              <table><?php 
                       $query = 'SELECT b.department_name, a.no_of_shifts, a.id, c.position_abbreviation FROM attendance a INNER JOIN department b ON a.department_id=b.department_id INNER JOIN position c ON a.position_id=c.position_id WHERE a.employee_id="'.$row['employee_id'].'" AND YEAR(a.start_date) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(a.start_date) = MONTH(CURDATE() - INTERVAL 1 MONTH)';

                        $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();
                $result = $statement->fetchAll();
                
                foreach($result as $row_department):
                  ?>
                  
                    <tr>
                      <td style="width:50%;"><?php echo $row_department['department_name'];?></td>
                      <td style="width:20%;"><?php echo $row_department['position_abbreviation'];?></td>
                      <td style="width:10%; text-align: right;"><?php echo $row_department['no_of_shifts'];?></td>
                      <td style="width:20%;"><center>
                        <form action="" method="POST">  
                        <input type="hidden" name="att_id" value="<?php echo $row_department['id'];?>">
                                                
                        <button class="edit_data4 btn btn-sm btn-outline-primary" data-id="<?php echo $row_department['id'];?>" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>

                        <button class="btn btn-sm btn-outline-danger float-right" name="remove_attendance"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button>
                        </form>
                          </center>
                        </td>
                    </tr>
                  
                  <?php
                  
                  
                  endforeach;
                        ?></table>

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
       

       <!--  start  modal -->
    <div id="editData4" class="modal fade">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Attendance</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="info_update4">
            <?php @include("/edit_attendance");?>
          </div>
          <div class="modal-footer ">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
    </div>
    <!--   end modal --> 


       <!-- Bank Details  -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form action="" method="POST" id="formattendance">
        <div class="modal-content">
          <div class="modal-body">
            <div class="form-group">
                            <label for="employee_id">Service No </label>
                            <input type="text" class="form-control" id="employee_id" name="employee_id" autofocus autocomplete="off"> 
                            <span id="employee_name" class="text-success"></span>
                          </div>
                          <div class="form-group">
                            <label for="">Position Name</label>
                          </div>
                          <div class="row">
                       
                           <?php
                              $query="SELECT * FROM position ORDER BY position_id";
                              $statement = $connect->prepare($query);
                              $statement->execute();
                              $result = $statement->fetchAll();
                              foreach($result as $row)
                              {
                                ?><div class="col-md-3">
                                <div class="form-group clearfix">
                                    <div class="icheck-success d-inline">
                                      <input type="radio" id="radioPrimary<?php echo $row['position_id']; ?>" name="position_id" value="<?php echo $row['position_id']; ?>">
                                      <label for="radioPrimary<?php echo $row['position_id']; ?>"><?php echo $row['position_abbreviation']; ?>
                                      </label>
                                    </div>
                                  </div>     
                                  </div>                           
                                <?php
                              }
                              ?>
                          </div>
                           

                    <!-- <div class="form-group">
                            <label for="">Position Name</label>
                            <select class="form-control select2" style="width: 100%;" name="position_id" id="position_id">
                              <?php
                              $query="SELECT * FROM position ORDER BY position_id";
                              $statement = $connect->prepare($query);
                              $statement->execute();
                              $result = $statement->fetchAll();
                              foreach($result as $row)
                              {
                                ?>
                                <option value="<?php echo $row['position_id']; ?>"><?php echo $row['position_abbreviation']; ?></option>
                                <?php
                              }
                              ?>
                            </select>
                          </div> -->

                          <div class="form-group">
                        <label for="no_of_shifts">No of Shifts</label>
                        <input type="text" class="form-control" id="no_of_shifts" name="no_of_shifts" autocomplete="off" >
                      </div>

                      <div class="form-group">
                        <label for="extra_ot_hrs">Extra OT Hrs</label>
                        <input type="text" class="form-control" id="extra_ot_hrs" name="extra_ot_hrs" autocomplete="off" >
                      </div>
          </div>
          <div style="clear:both;"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal"> Close</button>
            <button name="add_emp" class="btn btn-primary"> Save</button>
          </div>
        </div>
      </form>
    </div>
  </div>
      
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
      "responsive": true,
    });


      $('.edit_employee').click(function(){
        var $id=$(this).attr('data-id');
        location.href = "/employee_list/add_employee/"+$id;
        
      });
      $('.view_employee').click(function(){
        var $id=$(this).attr('data-id');
        location.href = "/employee_list/employee/"+$id;
        
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
          url:"/edit_attendance",
          type:"post",
          data:{edit_id4:edit_id4},
          success:function(data){
            $("#info_update4").html(data);
            $("#editData4").modal('show');
          }
        });
      });     

    });
  </script>