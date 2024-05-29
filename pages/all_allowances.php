<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();

require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 7) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

if (isset($_POST['remove_allowances'])){

  if (checkPermissions($_SESSION["user_id"], 8) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/allowance_list/allowance');
    exit();
}

  $data = array(
    ':allowances_id'      =>  $_POST['allowances_id'],
    ':allowances_status'  => 1,      
  );

  $query = "UPDATE `allowances` SET `allowances_status`=:allowances_status WHERE `allowances_id`=:allowances_id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    header('location:/allowance_list/allowance');            
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
                <h3 class="card-title">Allowances Form</h3>                
              </div>
                <!-- /.card-header -->
              <div class="card-body">
                <?php
                $query = 'SELECT * FROM allowances WHERE allowances_status = 0 ORDER BY allowances_id ASC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();
                $result = $statement->fetchAll();

                ?>
                <table class="table table-sm table-hover">
                  <thead>
                    <tr>
                      <th><center>#</center></th>
                      <th>Allowance Information</th>
                      <th>Allowance Sinhala</th>
                      <th>Action</th>
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
                        <td><?php echo $sno; ?></td>
                        <td><?php echo $row['allowances_en'] ?></td>
                        <td><?php echo $row['allowances_si'] ?></td>
                        <td>
                          <center>
                            <form action="" method="POST">
                              <input type="hidden" name="allowances_id" value="<?php echo $row['allowances_id']?>">
                              <button class="btn btn-sm btn-outline-primary edit_allowance" data-id="<?php echo $row['allowances_id']?>" type="button"><i class="fa fa-edit"></i></button>
                              <!-- <button class="btn btn-sm btn-outline-danger" name="remove_allowances" ><i class="fa fa-trash"></i></button> -->
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

      $('.edit_allowance').click(function(){
        var $id=$(this).attr('data-id');
        location.href = "/allowance_list/add_allowance/"+$id;
        
      });           

    })

</script>
