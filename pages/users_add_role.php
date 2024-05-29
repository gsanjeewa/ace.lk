<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 45) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
    header('location:/settings/bank_list');   
    exit();
}

?>
<div class="card-body">
    <?php
    if(isset($_POST['edit_role_id']))
    {
        $eid=$_POST['edit_role_id'];
        $_SESSION['role_user_id']=$_POST['edit_role_id'];
        $sql2="SELECT * from system_users_to_roles where user_id=:eid";
        $query2 = $connect -> prepare($sql2);
        $query2-> bindParam(':eid', $eid, PDO::PARAM_STR);
        $query2->execute();
        $results=$query2->fetchAll(PDO::FETCH_OBJ);
        if($query2->rowCount() > 0)
        {
            foreach($results as $row)
            {
                $_SESSION['role_ref_id']=$row->ref_id;        
                ?>

                <form action="" class="form-sample" method="post" enctype="multipart/form-data" id="add_bank_form">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">Role Name</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <select class="form-control select2" style="width: 100%;" id="role_name" name="role_name">
                                    <option>Select Role</option>
                                    <?php
                                    $statement = $connect->prepare("SELECT * FROM roles WHERE role_id !=1 ORDER BY role_id ASC");
                                    $statement->execute();
                                    $result = $statement->fetchAll();
                                    foreach($result as $row_role)
                                    {
                                        ?>
                                        <option value="<?php echo $row_role['role_id'];?>"<?php if ($row_role['role_id']==$row->role_id){ echo "SELECTED";}?>><?php echo $row_role['role_name']; ?></option>
                                        <?php
                                    }
                                    ?>

                                </select>                                
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" name="update_role" class="btn btn-primary btn-fw mr-2" style="float: left;">Update</button>
                </form>
                <?php 
            }
        }else{
        ?>
        <form action="" method="post" enctype="multipart/form-data" id="add_bank_form">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">Role Name</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <select class="form-control select2" style="width: 100%;" id="role_name" name="role_name">
                                    <option>Select Role</option>
                                    <?php
                                    $statement = $connect->prepare("SELECT * FROM roles WHERE role_id !=1 ORDER BY role_id ASC");
                                    $statement->execute();
                                    $result = $statement->fetchAll();
                                    foreach($result as $row_role)
                                    {
                                        ?>
                                        <option value="<?php echo $row_role['role_id'];?>"><?php echo $row_role['role_name']; ?></option>
                                        <?php
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                    </div>
                   
                   <input type="hidden" name="user_id" value="<?php echo $_POST['edit_role_id']; ?>">
                   
                    <button type="submit" name="add_role_new" class="btn btn-primary btn-fw mr-2" style="float: left;">Add</button>
                </form>

        <?php
    }
}
         ?>

</div>

<script>
$(function () {
  
  $('#add_bank_form').validate({
    rules: {
      role_name: { required: true}      
    },

    messages: {      
      
      employee_no: {
        remote: 'Employee No Already existing!'
      },

      nic_new: {
        remote: 'NIC No Already existing!'
      }, 

      nic_old: {
        remote: 'NIC No Already existing!'
      }, 
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });

});
</script>