<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if ((checkPermissions($_SESSION["user_id"], 41) == "false") OR (checkPermissions($_SESSION["user_id"], 42) == "false")) {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
    header('location:/users/list');   
    exit();
}

?>
<div class="card-body">
    <?php
    if(isset($_POST['edit_id4']))
    {
        $eid=$_POST['edit_id4'];
        $sql2="SELECT * from system_users where user_id=:eid";
        $query2 = $connect -> prepare($sql2);
        $query2-> bindParam(':eid', $eid, PDO::PARAM_STR);
        $query2->execute();
        $results=$query2->fetchAll(PDO::FETCH_OBJ);
        if($query2->rowCount() > 0)
        {
            foreach($results as $row)
            {
                $_SESSION['editbid']=$row->user_id;        
                ?>

                <form action="" class="form-sample" method="post" enctype="multipart/form-data" id="add_bank_form">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">First Name</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <input type="text" name="first_name" id="first_name" class="form-control text-uppercase" value="<?php  echo $row->first_name;?>" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">Last Name</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <input type="text" name="last_name" id="last_name" class="form-control text-uppercase" value="<?php  echo $row->last_name;?>" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">Status</label>
                            <div class="col-sm-12 pl-0 pr-0">
                              <select class="form-control select2" style="width: 100%;" id="status" name="status">
                                <option>Select Status</option>     
                                <option value="0"<?php if ($row->status==0){ echo "SELECTED";}?>>Active</option>
                                <option value="1"<?php if ($row->status==1){ echo "SELECTED";}?>>Deactive</option>                                  

                              </select>                                
                            </div>
                        </div>
                    </div>


                    
                    <button type="submit" name="insert" class="btn btn-primary btn-fw mr-2" style="float: left;">Update</button>
                </form>
                <?php 
            }
        }
    }else{
        ?>
        <form action="" method="post" enctype="multipart/form-data" id="add_bank_form">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">First Name</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <input type="text" name="first_name" id="first_name" class="form-control text-uppercase"  />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">Last Name</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <input type="text" name="last_name" id="last_name" class="form-control text-uppercase"  />
                            </div>
                        </div>
                    </div>

                    <?php 
                      $query_no="SELECT username FROM system_users ORDER BY user_id DESC, username DESC LIMIT 1";
                      $statement = $connect->prepare($query_no);
                      $statement->execute();
                      $result = $statement->fetchAll();
                      if($statement->rowCount() > 0)
                      {
                        foreach($result as $row_user_id)
                        {
                          $user_id=$row_user_id['username']+1;                      
                        }
                      }else{
                        $user_id=10000;
                      }

                      ?>

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">User ID</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <input type="text" name="username" id="username" class="form-control" value="<?php echo $user_id; ?>" readonly/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">Password</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <input type="text" name="password" id="password" class="form-control" value="PASSWORD" readonly />
                            </div>
                        </div>
                    </div>             
                   
                   
                    <button type="submit" name="add_new" class="btn btn-primary btn-fw mr-2" style="float: left;">Add</button>
                </form>

        <?php
    }
         ?>

</div>

<script>
$(function () {
  
  $('#add_bank_form').validate({
    rules: {
      first_name: { required: true},
      last_name: { required: true},          
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