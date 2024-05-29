<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 85) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;

if(isset($_POST['add_new']))
{
  if (checkPermissions($_SESSION["user_id"], 83) == "false") {
    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
    header('location:/settings/invoice_rate');  
    exit();
  }

  $department_id=  strtoupper($_POST['department_id']);
  $position_id=  strtoupper($_POST['position_id']);
  $statement = $connect->prepare("SELECT * FROM invoice_rate WHERE department_id=:department_id AND position_id=:position_id");
  $statement->bindParam(':department_id', $department_id);
  $statement->bindParam(':position_id', $position_id);
  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Bank Name Already existing.</div>';
  }

  if (!$error) {
    $data = array(
        ':department_id'  => $_POST['department_id'],
        ':position_id'    => $_POST['position_id'],
        ':payment'        => $_POST['invoice_rate'],
    );
   
    $query = "
    INSERT INTO `invoice_rate`(`department_id`, `position_id`, `payment`)
    VALUES (:department_id, :position_id, :payment)
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


if(isset($_POST['insert']))
{
    if (checkPermissions($_SESSION["user_id"], 84) == "false") {

        $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
        header('location:/settings/invoice_rate');   
        exit();
    }

    $data = array(
        ':id'             => $_SESSION['editbid'],
        ':department_id'  => $_POST['department_id'],
        ':position_id'    => $_POST['position_id'],
        ':payment'        => $_POST['invoice_rate'],
    );
   
    $query = "UPDATE `invoice_rate` SET `department_id`=:department_id, `position_id`=:position_id, `payment`=:payment WHERE `id`=:id";
            
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

if (isset($_POST['remove_invoice_rate'])){

  if (checkPermissions($_SESSION["user_id"], 84) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/settings/invoice_rate');
    exit();
  }

  $data = array(
      ':id'      =>  $_POST['invoice_rate_id'],     
  );

  $query = "DELETE FROM `invoice_rate` WHERE `id`=:id";
    
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



include '../inc/header.php';

?>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Settings</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Settings</li>
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
              <?php echo $_SESSION["msg"];
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
                  <h3 class="card-title">Invoice Rate</h3>
                  <button class="edit_data4 btn btn-sm bg-gradient-primary float-right" type="button" ><i class="fas fa-plus"></i> Add</button>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">  

                  <?php

                  $query = 'SELECT a.payment, b.department_name, a.department_id, b.department_location FROM invoice_rate a INNER JOIN department b ON a.department_id=b.department_id WHERE b.department_status=0 GROUP BY a.department_id ORDER BY a.department_id';

                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $total_data = $statement->rowCount();

                  $result = $statement->fetchAll();

                  ?>

                  <table id="example2" class="table table-bordered table-sm table-striped">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>                        
                        <th>Institution Name</th>
                        <th>Invoice Rate</th>                                                   
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row)
                      {
                        
                        ?>
                        <tr>
                            <td><center><?php echo $sno; ?></center></td>
                            <td><?php echo trim($row['department_name']).'-'.trim($row['department_location']); ?></td>
                            <td><table>
                            <?php 
                            $query = 'SELECT a.id, b.position_abbreviation, a.payment FROM invoice_rate a INNER JOIN position b ON a.position_id=b.position_id WHERE a.department_id="'.$row['department_id'].'"';

                                  $statement = $connect->prepare($query);
                          $statement->execute();
                          $total_data = $statement->rowCount();
                          $result = $statement->fetchAll();
                          
                          foreach($result as $row_p):
                            ?>
                            
                              <tr>
                                <td><?php echo $row_p['position_abbreviation'];?></td>
                                <td><?php echo number_format($row_p['payment']);?></td>
                                <td><center>
                                  <form action="" method="POST">  
                                  <input type="hidden" name="invoice_rate_id" value="<?php echo $row_p['id']?>">

                                <button class="edit_data4 btn btn-sm btn-outline-primary edit_loan" data-id="<?php echo $row_p['id'];?>" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
                              
                              <button class="btn btn-sm btn-outline-danger" name="remove_invoice_rate" type="submit"><i class="fa fa-trash"></i></button>                    
                                    </center>
                                                             
                              <!-- <button class="btn btn-sm btn-outline-danger" name="remove_deduction" type="submit"><i class="fa fa-trash"></i></button> -->
                              </form>
                                    
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

    <!--  start  modal -->
    <div id="editData4" class="modal fade">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Invoice Rate</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="info_update4">
            <?php @include("/edit_invoice_rate");?>
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
    }); 

    /*$('.edit_loan').click(function(){
        var $id=$(this).attr('data-id');
        location.href = "/loan/new_loan_req/"+$id;
        
      });
     
*/
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
          url:"/edit_invoice_rate",
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
