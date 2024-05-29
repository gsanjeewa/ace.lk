<?php 
session_start();
require_once '../pages/config.php';
require_once '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 45) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/users/list');
    exit();

}


if (isset($_POST['update_save'])){

  if (checkPermissions($_SESSION["user_id"], 45) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';   
    exit();

  }

  $array1=array();
  $array2=array();
  $query = 'SELECT permission_id FROM system_permission_to_roles WHERE role_id = "'.$_POST['role_id'].'" ORDER BY permission_id ASC';
  $statement = $connect->prepare($query);
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
  foreach($result as $row)
  {
    $array1[]=$row['permission_id'];
  }
 
  $array2=$_POST['permissions'];
  
  $diff_insert=array_diff($array2, $array1);

  $diff_delete=array_diff($array1, $array2); 

  $query_insert = "
  INSERT INTO `system_permission_to_roles`(`role_id`, `permission_id`)
  VALUES (:role_id, :permission_id)  
  ";
          
  $statement = $connect->prepare($query_insert);

foreach($diff_insert as $d) {
  if($statement->execute(array(':role_id' => $_POST['role_id'], ':permission_id' => $d)))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';            
  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
}

$query_delete = "
  DELETE FROM `system_permission_to_roles` WHERE `role_id`=:role_id AND `permission_id`=:permission_id
  ";
          
  $statement = $connect->prepare($query_delete);

foreach($diff_delete as $k) {
  if($statement->execute(array(':role_id' => $_POST['role_id'], ':permission_id' => $k)))
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
            <h1 class="m-0 text-dark">Users</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Users</li>
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
            

                  <form action="" id="" method="post">
                    <div class="card card-danger">
                      <div class="card-header">
                        <h3 class="card-title">Role & Permissions</h3>                
                      </div>
                        <!-- /.card-header -->
                      <div class="card-body">                         
                        <div class="row">
                          <div class="col-md-6">
                          <div class="form-group">
                          <label for="permissions_group">Permission Group</label>
                          <select class="form-control select2" id="permissions_group" name="permissions_group" >
                            <option value="">Select Group</option>
                            <?php
                            $query="SELECT * FROM system_permissions GROUP BY permission_group ORDER BY permission_group ASC";
                            $statement = $connect->prepare($query);
                            $statement->execute();
                            $result = $statement->fetchAll();                            
                            foreach($result as $row_group)
                            {
                              ?>
                              <option value="<?php echo $row_group['permission_group']; ?>"><?php echo $row_group['permission_group']; ?></option>
                              <?php
                               
                            }

                            ?>

                          </select>                          
                        </div>
                        <input type="hidden" name="role_id" id="role_id" value="<?php echo $_GET['edit']; ?>">
                        </div>
                      </div>
                      <div class="row">
                        <div class="table-responsive col-md-12">
                        <div class="filter_data" style="justify-content: center;"></div>
                      </div>
                      </div>                     
                        
                      </div>
                      <!-- /.card-body -->

                      <div class="card-footer">
                        <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="update_save"> Save</button>
                        <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button>
                      </div>

                    </div>
                    <!-- /.card -->
                  </form>
                 
             

            
          </div>

          
        </div>
        <!-- /.row -->
       
      
      </div><!-- /.container-fluid -->
    </section>    

<?php
include '../inc/footer.php';
?>

<script type="text/javascript">
$(document).ready(function(){

  load_data();

  function load_data(query_permissions_group = '')
  {
    var query_permissions_group = $('#permissions_group').val();
    var query_role_id = $('#role_id').val();
    
    $.ajax({
      url:"/edit_permissions",
      method:"POST",
      data:{query_permissions_group:query_permissions_group,query_role_id:query_role_id,request:1},
      success:function(data){
        $('.filter_data').html(data);
      }
    });
  }

  $('#permissions_group').change(function(){
    var query_permissions_group = $('#permissions_group').val();
    load_data(1, query_permissions_group);
    });
});
</script>