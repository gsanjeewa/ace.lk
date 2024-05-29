<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 43) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;

if(isset($_POST['add_new']))
{
  if (checkPermissions($_SESSION["user_id"], 41) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/users/list');
    exit();
}
  $username=  $_POST['username'];
  $statement = $connect->prepare("SELECT username FROM system_users WHERE username=:username");
  $statement->bindParam(':username', $username);
  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>User Name Already existing.</div>';
  }

   if (!$error) {

  $data = array(
    ':first_name' =>  strtoupper($_POST['first_name']),
    ':last_name'  =>  strtoupper($_POST['last_name']),
    ':username'   =>  $_POST['username'],     
    ':password'   =>  md5($_POST['password']),
  );
 
  $query = "
  INSERT INTO `system_users`(`first_name`, `last_name`, `username`, `password`) 
  VALUES (:first_name, :last_name, :username, :password)  
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
    if (checkPermissions($_SESSION["user_id"], 42) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/users/list');
    exit();
}

  $data = array(
    ':user_id' =>  $_SESSION['editbid'],
    ':first_name' =>  strtoupper($_POST['first_name']),
    ':last_name'  =>  strtoupper($_POST['last_name']),
    ':status'   =>  $_POST['status'],     
  );

  $query = "UPDATE `system_users` SET `first_name`=:first_name, `last_name`=:last_name, `status`=:status WHERE `user_id`=:user_id";
    
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

if (isset($_POST['add_role_new'])){

  if (checkPermissions($_SESSION["user_id"], 45) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
    header('location:/users/list');  
    exit();

  }

  $role_id=  $_POST['role_name'];
  $user_id  =  $_SESSION['role_user_id'];
  $statement = $connect->prepare("SELECT * FROM system_users_to_roles WHERE user_id=:user_id AND role_id=:role_id");
  $statement->bindParam(':role_id', $role_id);
  $statement->bindParam(':user_id', $user_id);
  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Role Already existing.</div>';
  }

   if (!$error) {
  $data = array(
    ':role_id' =>  $_POST['role_name'],
    ':user_id'  =>  $_SESSION['role_user_id'],
    
  );
 
  $query = "
  INSERT INTO `system_users_to_roles`(`user_id`, `role_id`) 
  VALUES (:user_id, :role_id)  
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


if(isset($_POST['update_role']))
{
    if (checkPermissions($_SESSION["user_id"], 45) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/users/list');
    exit();
}

  $role_id=  $_POST['role_name'];
  $user_id  =  $_SESSION['role_user_id'];
  $statement = $connect->prepare("SELECT * FROM system_users_to_roles WHERE user_id=:user_id AND role_id=:role_id");
  $statement->bindParam(':role_id', $role_id);
  $statement->bindParam(':user_id', $user_id);
  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Role Already existing.</div>';
  }

  if (!$error) {
  $data = array(
    ':ref_id' =>  $_SESSION['role_ref_id'],
    ':user_id' =>  $_SESSION['role_user_id'],
    ':role_id'  =>  $_POST['role_name'],    
  );

  $query = "UPDATE `system_users_to_roles` SET `user_id`=:user_id, `role_id`=:role_id WHERE `ref_id`=:ref_id";
    
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

if(isset($_POST['password_reset']))
{
    if (checkPermissions($_SESSION["user_id"], 42) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/users/list');
    exit();
}

  $data = array(
    ':user_id' =>  $_POST['user_id'],
    ':password' =>  md5('PASSWORD'),
       
  );

  $query = "UPDATE `system_users` SET `password`=:password WHERE `user_id`=:user_id";
    
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
                  <h3 class="card-title">Users List</h3>
                  <button class="edit_data4 btn btn-sm bg-gradient-primary float-right" type="button" ><i class="fas fa-plus"></i> Add</button>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">  

                  <?php
                  $query = 'SELECT * FROM system_users WHERE user_id != 1 ORDER BY user_id ASC';

                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $total_data = $statement->rowCount();

                  $result = $statement->fetchAll();

                  ?>

                  <table id="example2" class="table table-bordered table-striped">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>                        
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <!-- <th>Permission</th> -->
                        <th>Status</th>
                        <th>Action</th>                                                  
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row)
                      {
                        
                        $statement = $connect->prepare('SELECT r.role_name FROM system_users_to_roles s INNER JOIN roles r ON s.role_id = r.role_id WHERE s.user_id="'.$row['user_id'].'"');
                        $statement->execute();
                        $total_data = $statement->rowCount();
                        $result = $statement->fetchAll();
                        if ($total_data >0) {
                          foreach($result as $row_role)
                          {
                            $role_name=$row_role['role_name'];
                          }
                        }else{
                          $role_name='';
                        }
                        
                        
                        ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $sno; ?></td>
                            <td><?php echo $row['first_name'];?></td>
                            <td><?php echo $row['last_name'];?></td>
                            <td><?php echo $row['username'];?></td>
                            <td><?php echo $role_name;?></td>
                            <td>
                              <center>
                                <?php if($row['status'] == 0): ?>
                                  <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                  <span class="badge badge-danger">Deactive</span>
                                <?php endif ?>
                              </center>
                            </td>
                            <td>
                              <center>
                                <form action="" method="post">
                                  <input type="hidden" name="user_id" value="<?php echo $row['user_id']?>">
                                <button class="btn btn-sm btn-outline-danger password_reset" type="submit" name="password_reset" data-toggle="tooltip" data-placement="top" title="Password Reset"><i class="fa fa-lock"></i></button>
                                <button class=" edit_data4 btn btn-sm btn-outline-primary" data-id="<?php echo $row['user_id']?>" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
                                <button class="add_role btn btn-sm btn-outline-success edit_employee" data-id="<?php echo $row['user_id']?>" type="button" data-toggle="tooltip" data-placement="top" title="Role"><i class="fa fa-address-card"></i></button> 
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

    <!--  start  modal -->
    <div id="editData4" class="modal fade">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">User Name</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="info_update4">
            <?php @include("../edit_users.php");?>
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

    <!--  start  modal -->
    <div id="edit_role" class="modal">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Role</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="info_update1">
            <?php @include("../add_role.php");?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
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
          url:"/edit_users",
          type:"post",
          data:{edit_id4:edit_id4},
          success:function(data){
            $("#info_update4").html(data);
            $("#editData4").modal('show');
          }
        });
      });

      $(document).on('click','.add_role',function(){
        $("#edit_role").modal({
            backdrop: 'static',
            keyboard: false
        });
        var edit_role_id=$(this).attr('data-id');
        $.ajax({
          url:"/add_role",
          type:"post",
          data:{edit_role_id:edit_role_id},
          success:function(data){
            $("#info_update1").html(data);
            $("#edit_role").modal('show');
          }
        });
      });

    });
  </script>
