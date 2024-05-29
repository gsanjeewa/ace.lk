<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();

require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 15) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

if (isset($_POST['remove_deduction'])){

  if (checkPermissions($_SESSION["user_id"], 16) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';   
    header('location:/deduction_list/deduction'); 
    exit();
}
  $data = array(
      ':deduction_id'      =>  $_POST['deduction_id'],
      ':deduction_status'  => 1,      
  );

  $query = "UPDATE `deduction` SET `deduction_status`=:deduction_status WHERE `deduction_id`=:deduction_id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    header('location:/deduction_list/deduction');            
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
            <h1 class="m-0 text-dark">Deduction</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Deduction</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
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
        <!-- Small boxes (Stat box) -->
        <div class="row">          
          <div class="col-md-8">
            <div class="card card-outline card-danger">
              <div class="card-header">
                <h3 class="card-title">Deduction Form</h3>                
              </div>
                <!-- /.card-header -->
              <div class="card-body">
                <?php
                $query = 'SELECT * FROM deduction WHERE deduction_status = 0 ORDER BY deduction_id ASC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();
                $result = $statement->fetchAll();

                ?>
                <table class="table table-sm table-hover">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Deduction Information</th>
                      <th>Deduction Sinhala</th>
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
                        <td><?php echo $row['deduction_en'] ?>
                          </td>
                          <td>
                          <?php echo $row['deduction_si'] ?>
                        </td>
                        <td>
                          <center>
                            <form action="" method="POST">
                              <input type="hidden" name="deduction_id" value="<?php echo $row['deduction_id']?>">
                              <button class="btn btn-sm btn-outline-primary edit_deduction" data-id="<?php echo $row['deduction_id']?>" type="button"><i class="fa fa-edit"></i></button>
                              <!-- <button class="btn btn-sm btn-outline-danger" name="remove_deduction"><i class="fa fa-trash"></i></button> -->
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

      $('.edit_deduction').click(function(){
        var $id=$(this).attr('data-id');
        location.href = "/deduction_list/add_deduction/"+$id;
        
      });           

    })

</script>
