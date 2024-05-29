<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();

require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 11) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

if (isset($_POST['remove_allowances'])){

  if (checkPermissions($_SESSION["user_id"], 12) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/allowance_list/emp_allowance');
    exit();
}

  $data = array(
      ':id'      =>  $_POST['id'],     
  );

  $query = "DELETE FROM `employee_allowances` WHERE `id`=:id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    header('location:/allowance_list/emp_allowance');            
  }else{
      $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
    
}

include '../inc/header.php';
?>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Allowance</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Allowance</li>
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
          <div class="col-md-8">
            <div class="card card-outline card-danger">
              <div class="card-header">
                <h3 class="card-title">Employee Allowances</h3>                
              </div>
                <!-- /.card-header -->
              <div class="card-body">
                <?php
                $query = 'SELECT DISTINCT employee_id FROM employee_allowances WHERE MONTH(effective_date) >= MONTH(curdate()) -1 ORDER BY id ASC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();
                $result = $statement->fetchAll();

                ?>
                <table class="table table-sm table-hover" id="example2">
                  <thead>
                    <tr>
                      <th><center>#</center></th>
                      <th>Employee Name</th>
                      <th>Allowance</th>                      
                    </tr>                    
                  </thead>
                  <tbody>
                    <?php
                    $startpoint =0;
                    $sno = $startpoint + 1;
                    foreach($result as $row)
                    {
                      $query = 'SELECT e.employee_id, e.surname, e.initial, j.employee_no FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN position p ON e.position_id=p.position_id WHERE j.join_id="'.$row['employee_id'].'" ORDER BY e.employee_id DESC';

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
                        <td><?php echo $sno; ?></td>
                        <td><?php echo $row_employee['employee_no'].' '.$position_id.' '.$row_employee['surname'].' '.$row_employee['initial'] ?></td>
                        <td><table><?php 
                       $query = 'SELECT b.allowances_en, a.amount, a.id, a.effective_date FROM employee_allowances a INNER JOIN allowances b ON a.allowances_id=b.allowances_id WHERE a.employee_id="'.$row['employee_id'].'" AND MONTH(effective_date) >= MONTH(curdate()) -1';

                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $total_data = $statement->rowCount();
                        $result = $statement->fetchAll();
                        
                        foreach($result as $row):
                          ?>
                          
                            <tr>
                              <td><?php echo $row['allowances_en'];?></td>
                              <td><?php echo date('Y-m', strtotime($row['effective_date']));?></td>
                              <td><?php echo number_format($row['amount']);?></td>
                              <td>
                                <center>
                                  <form action="" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $row['id']?>">
                                    <button class="btn btn-sm btn-outline-danger" name="remove_allowances" ><i class="fa fa-trash"></i></button>
                                  </form>
                                </center>
                              </td>
                            </tr>
                          
                            <?php
                          
                          
                          endforeach;
                        ?></table></td>
                        
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
                  if(isset($_GET['edit']))
                  {
                    $query = 'SELECT * FROM allowances WHERE allowances_id="'.$_GET['edit'].'"';

                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $total_data = $statement->rowCount();

                    $result = $statement->fetchAll();
                    foreach($result as $row)
                    {

                    }
                  }
                      ?>

    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <h4 class="modal-title">Allowances Form</h4>              
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="">Allowance English</label>
                <input type="text" class="form-control" id="" name="allowances_en" value="<?php echo isset($row['allowances_en']) ? $row['allowances_en'] : ""; ?>">
              </div>

              <div class="form-group">
                <label for="">Allowance Sinhala</label>
                <input type="text" class="form-control" id="" name="allowances_si" value="<?php echo isset($row['allowances_si']); ?>">
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button class="btn btn-sm btn-primary col-sm-3 offset-md-3"> Save</button>
              <button class="btn btn-sm btn-default col-sm-3" type="button" onclick="_reset()"> Cancel</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->


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

      $('.edit_allowance').click(function(){
        var $id=$(this).attr('data-id');
        location.href = "/allowance_list/add_allowance/"+$id;
        
      });           

    })

</script>
