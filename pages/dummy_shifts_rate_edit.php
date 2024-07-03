<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if ((checkPermissions($_SESSION["user_id"], 87) == "false") OR (checkPermissions($_SESSION["user_id"], 88) == "false")) {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
    header('location:/settings/bank_list');   
    exit();
}

?>
<div class="card-body">
    <?php
    if(isset($_POST['edit_id4']))
    {
        $eid=$_POST['edit_id4'];
        $sql2="SELECT * from d_shifts_rate where id=:eid";
        $query2 = $connect -> prepare($sql2);
        $query2-> bindParam(':eid', $eid, PDO::PARAM_STR);
        $query2->execute();
        $results=$query2->fetchAll(PDO::FETCH_OBJ);
        if($query2->rowCount() > 0)
        {
            foreach($results as $row)
            {
                $_SESSION['editbid']=$row->id;        
                ?>

                <form action="" class="form-sample" method="post" enctype="multipart/form-data" id="add_bank_form">
                    
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">Institution Name</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <select class="form-control select2" style="width: 100%;" name="department_id" id="department_id">
                                   <?php
                                    $query="SELECT * FROM department ORDER BY department_id";
                                    $statement = $connect->prepare($query);
                                    $statement->execute();
                                    $result = $statement->fetchAll();
                                    foreach($result as $row_department)
                                    {
                                      ?>
                                      <option value="<?php echo $row_department['department_id']; ?>"<?php if ($row_department['department_id']==$row->department_id){ echo "SELECTED";}?>><?php echo $row_department['department_name'].'-'.$row_department['department_location']; ?></option>
                                      <?php
                                    }
                                    ?>
                                </select>                                
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">Shifts</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <select class="form-control select2" style="width: 100%;" name="shifts" id="shifts">
                            <option value="">Select Type</option>
                            <option value="1"<?php if ($row->shifts==1){ echo "SELECTED";}?>>Nomal Rate not included Half Days</option>
                            <option value="2"<?php if ($row->shifts==2){ echo "SELECTED";}?>>Nomal Rate included Half Days</option>
                            <option value="3"<?php if ($row->shifts==3){ echo "SELECTED";}?>>20 Rate not included Half Days</option>
                            <option value="4"<?php if ($row->shifts==4){ echo "SELECTED";}?>>Nomal Rate included Half Days, Poya & Mercantile</option>
                            <option value="5"<?php if ($row->shifts==5){ echo "SELECTED";}?>>Total shifts</option>
                            <option value="6"<?php if ($row->shifts==6){ echo "SELECTED";}?>>Nomal Rate Max OT 60 & included Half Days</option>
                            <option value="7"<?php if ($row->shifts==7){ echo "SELECTED";}?>>8 Shift Rate</option>
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
                    <label class="col-sm-12 pl-0 pr-0">Institution Name</label>
                    <div class="col-sm-12 pl-0 pr-0">
                        <select class="form-control select2" style="width: 100%;" name="department_id" id="department_id">
                            <?php
                            $query="SELECT * FROM department ORDER BY department_id";
                            $statement = $connect->prepare($query);
                            $statement->execute();
                            $result = $statement->fetchAll();
                            foreach($result as $row_department)
                            {
                              ?>
                              <option value="<?php echo $row_department['department_id']; ?>"><?php echo $row_department['department_name'].'-'.$row_department['department_location']; ?></option>
                              <?php
                            }
                            ?>
                        </select>                                
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-12">
                    <label class="col-sm-12 pl-0 pr-0">Shifts Type</label>
                    <div class="col-sm-12 pl-0 pr-0">
                        <select class="form-control select2" style="width: 100%;" name="shifts" id="shifts">
                            <option value="">Select Type</option>
                            <option value="1">Nomal Rate not included Half Days</option>
                            <option value="2">Nomal Rate included Half Days</option>
                            <option value="3">20 Rate not included Half Days</option>
                            <option value="4">Nomal Rate included Half Days, Poya & Mercantile</option>
                            <option value="5">Total shifts</option>
                            <option value="6">Nomal Rate Max OT 60 & included Half Days</option>
                            <option value="7">8 Shift Rate</option>
                        </select>
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
      sector_name: { required: true},      
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

  $('.select2').select2()

});
</script>