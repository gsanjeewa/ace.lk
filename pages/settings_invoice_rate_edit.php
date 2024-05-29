<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 84) == "false") {

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
        $sql2="SELECT * from invoice_rate where id=:eid";
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
                    <label class="col-sm-12 pl-0 pr-0">Institution</label>
                    <div class="col-sm-12 pl-0 pr-0">
                        <select class="form-control select2" style="width: 100%;" name="department_id" id="department_id">
                        <option value="">Select Institution</option>
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
                    <label class="col-sm-12 pl-0 pr-0">Position</label>
                    <div class="col-sm-12 pl-0 pr-0">
                        <div class="row">
                        <?php
                      $query="SELECT position_id, position_abbreviation FROM position ORDER BY position_id";
                      $statement = $connect->prepare($query);
                      $statement->execute();
                      $result = $statement->fetchAll();
                      foreach($result as $row_position)
                      {
                        ?><div class="col-md-3">
                        <div class="form-group clearfix">
                            <div class="icheck-success d-inline">
                              <input type="radio" id="radioPrimary<?php echo $row_position['position_id']; ?>" name="position_id" value="<?php echo $row_position['position_id']; ?>" <?php if ($row_position['position_id']==$row->position_id) { echo "checked";} ?>>
                              <label for="radioPrimary<?php echo $row_position['position_id']; ?>"><?php echo $row_position['position_abbreviation']; ?>
                              </label>
                            </div>
                          </div>     
                          </div>                           
                        <?php
                      }
                      ?>
                      </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-12">
                    <label class="col-sm-12 pl-0 pr-0">Rate</label>
                    <div class="col-sm-12 pl-0 pr-0">
                        <input type="text" name="invoice_rate" id="invoice_rate" class="form-control text-uppercase" value="<?php echo $row->payment;?>" />
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
                    <label class="col-sm-12 pl-0 pr-0">Institution</label>
                    <div class="col-sm-12 pl-0 pr-0">
                        <select class="form-control select2" style="width: 100%;" name="department_id" id="department_id">
                        <option value="">Select Institution</option>
                        <?php
                        $query="SELECT * FROM department ORDER BY department_id";
                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row)
                        {
                          ?>
                          <option value="<?php echo $row['department_id']; ?>"><?php echo $row['department_name'].'-'.$row['department_location']; ?></option>
                          <?php
                        }
                        ?>
                      </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label class="col-sm-12 pl-0 pr-0">Position</label>
                    <div class="col-sm-12 pl-0 pr-0">
                        <div class="row">
                        <?php
                      $query="SELECT position_id, position_abbreviation FROM position ORDER BY position_id";
                      $statement = $connect->prepare($query);
                      $statement->execute();
                      $result = $statement->fetchAll();
                      foreach($result as $row)
                      {
                        ?><div class="col-md-3">
                        <div class="form-group clearfix">
                            <div class="icheck-success d-inline">
                              <input type="radio" id="radioPrimary<?php echo $row['position_id']; ?>" name="position_id" value="<?php echo $row['position_id']; ?>">
                              <label for="radioPrimary<?php echo $row['position_id']; ?>"><?php echo $row['position_abbreviation']; ?>
                              </label>
                            </div>
                          </div>     
                          </div>                           
                        <?php
                      }
                      ?>
                      </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-12">
                    <label class="col-sm-12 pl-0 pr-0">Rate</label>
                    <div class="col-sm-12 pl-0 pr-0">
                        <input type="text" name="invoice_rate" id="invoice_rate" class="form-control text-uppercase"  />
                    </div>
                </div>
            </div>
                   
                    <button type="submit" name="add_new" class="btn btn-primary btn-fw mr-2" style="float: left;">Add</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </form>

        <?php
    }
         ?>

</div>

<script>
$(function () {
  
  $('#add_bank_form').validate({
    rules: {
      bank_name: { required: true},
      bank_code: {required: true, number: true}           
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