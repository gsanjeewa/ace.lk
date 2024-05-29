<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
    header('location:/settings/bank_list');   
    exit();
}

$statement = $connect->prepare("SELECT invoice_no FROM inventory_create_invoice ORDER BY invoice_no DESC LIMIT 1");
                $statement->execute();
                $result = $statement->fetchAll();
                if ($statement->rowCount()>0) {
                  foreach($result as $invoice_no){
                    $expNum = explode('-', $invoice_no['invoice_no']);             
                      
                    if ($expNum[0]==date('Y')) {
                      $ref_no=date('Y').'-'.str_pad($expNum[1]+1, 4, "0", STR_PAD_LEFT);
                    }else{
                      $ref_no=date('Y').'-'.str_pad(1, 4, "0", STR_PAD_LEFT);
                    }
                  }
                }
                else{
                  $ref_no=date('Y').'-'.str_pad(1, 4, "0", STR_PAD_LEFT);
                }

?>
<div class="card-body">  
  <?php
if(isset($_POST['edit_id4']))
    {
        $eid=$_POST['edit_id4'];
        $sql2="SELECT * from d_payroll where id=:eid AND status=0";
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

                <form action="" method="post" enctype="multipart/form-data" id="formattendance">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="start_date" class="control-label">Start Date</label>
                        <div class="input-group date" id="reservationstartdate" data-target-input="nearest">
                            <input type="text" name="start_date" id="start_date" class="form-control datetimepicker-input" data-target="#reservationstartdate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo $row->date_from ?>" />
                            <div class="input-group-append" data-target="#reservationstartdate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="end_date" class="control-label">End Date</label>
                        <div class="input-group date" id="reservationenddate" data-target-input="nearest">
                            <input type="text" name="end_date" id="end_date" class="form-control datetimepicker-input" data-target="#reservationenddate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo $row->date_to ?>"/>
                            <div class="input-group-append" data-target="#reservationenddate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">

                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="no_of_shifts">Payroll Type</label>
                        <select class="form-control select2" style="width: 100%;" name="type_id" id="type_id">
                          <option value="1" <?php if ($row->type ==1){ echo "selected";} ?>>Monthly</option>
                          <option value="2" <?php if ($row->type ==2){ echo "selected";} ?>>Semi-Monthly</option>                          
                        </select>
                      </div> 
                    </div>
                  </div>      

       
        <button type="submit" name="add_save" class="btn btn-primary btn-fw mr-2" style="float: left;">Add</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
    </form>
                <?php 
            }
        }
    }else{
        ?> 

      <form action="" method="post" enctype="multipart/form-data" id="formattendance">
        <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="location_id">Location</label>
                        <select class="form-control select2 location_id" style="width: 100%;" name="location_id" id="location_id">
                          <option value="">Select Location</option>
                          <?php
                          $query="SELECT * FROM inventory_location ORDER BY location ASC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row)
                          {
                            ?>
                            <option value="<?php echo $row['id']; ?>" ><?php echo $row['location']; ?></option>
                            <?php
                          }
                          ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="employee_id">Employee</label>
                        <select class="form-control select2 employee_id" style="width: 100%;" name="employee_id" id="employee_id">
                          <option value="">Select Employee</option>
                          <?php
                          $query="SELECT j.join_id, e.surname, e.initial, j.employee_no, p.position_abbreviation FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN position p ON e.position_id=p.position_id WHERE j.employee_status=0 OR j.employee_status=2 ORDER BY e.employee_id DESC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row)
                          {
                            ?>
                            <option value="<?php echo $row['join_id']; ?>"><?php echo $row['employee_no'].' '.$row['surname'].' '.$row['initial']; ?></option>
                            <?php
                          }
                          ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="invoice_no">Invoice No</label>
                        <input type="text" class="form-control" id="invoice_no" name="invoice_no" value="<?php echo $ref_no; ?>" readonly>
                      </div>
                    </div>
                  </div>      

       
        <button type="submit" name="add_save" class="btn btn-primary btn-fw mr-2" style="float: left;">Add</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
    </form>
<?php
} 
?>
</div>

<script>
$(function () {
  
  $('#formattendance').validate({
    rules: {      
      start_date: {required: true},
      end_date: {required: true},
      no_of_shifts: {required: true}        
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
 

$('[data-mask]').inputmask()
$('.select2').select2()
$('#reservationstartdate').datetimepicker({
  format: 'YYYY-MM-DD'
});
$('#reservationenddate').datetimepicker({
    format: 'YYYY-MM-DD'
  });
});

</script>