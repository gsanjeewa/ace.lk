<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 65) == "false") {

  $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
  header('location:/dashboard');
  exit();
}

if (isset($_POST['remove_product'])){

  if (checkPermissions($_SESSION["user_id"], 64) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/inventory/product_list');
    exit();
  }

  $data = array(
      ':id'      =>  $_POST['id'],
      ':status'  => 1,      
  );

  $query = "UPDATE `inventory_product` SET `status`=:status WHERE `id`=:id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    header('location:/inventory/product_list');            
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
            <h1 class="m-0 text-dark">Inventory</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Inventory</li>
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
                <h3 class="card-title">Inventory Form</h3>                
              </div>
                <!-- /.card-header -->
              <div class="card-body">
                <?php
                $query = 'SELECT * FROM inventory_product WHERE status = 0 ORDER BY id ASC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();
                $result = $statement->fetchAll();

                ?>
                <table class="table table-sm table-hover" id="example2">
                  <thead>
                    <tr>
                      <th><center>#</center></th>
                      <th>Product</th>
                      <th><center>Action</center></th>
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
                        <td><?php echo $row['product_name'] ?></td>
                        <td>
                          <center>
                            <form action="" method="POST">
                              <input type="hidden" name="id" value="<?php echo $row['id']?>">
                              <button class="btn btn-sm btn-outline-primary edit_product" data-id="<?php echo $row['id']?>" type="button"><i class="fa fa-edit"></i></button>
                              <button class="btn btn-sm btn-outline-danger" name="remove_product" ><i class="fa fa-trash"></i></button>
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
      "paging": false,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": false,
      "scrollX": true,
    });
        

      $('.edit_product').click(function(){
        var $id=$(this).attr('data-id');
        location.href = "/inventory/add_product/"+$id;
        
      });           

    })

</script>
