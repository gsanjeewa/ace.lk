<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
require_once 'system_permissions.php'; 
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 69) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;
if (isset($_POST['add_save'])){

if (checkPermissions($_SESSION["user_id"], 69) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/death/add_death');
    exit();
}

$statement = $connect->prepare("SELECT join_id FROM join_status WHERE employee_no = '".$_POST['employee_id']."'
      ");
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
  foreach($result as $row)
  {
    $employee_id=$row['join_id'];
  }

  $death_date=date('Y-m-d', strtotime($_POST['death_date']));

    if (!$error) {

        $data = array(
          ':employee_id' =>  $employee_id,
          ':amount' =>  $_POST['amount'],
          ':relation' =>  $_POST['relation'],
          ':due_date' =>  $death_date,
        );
       
        $query = "
        INSERT INTO `death_donation`(`employee_id`, `amount`, `relation`, `due_date`)
        VALUES (:employee_id, :amount, :relation, :due_date)
        ";
                
        $statement = $connect->prepare($query);

        if($statement->execute($data))
        {
          $errMSG = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';            
        }else{
            $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
        }
    }
}

include '../inc/header.php';
?>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Death Donation</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Death Donation</li>
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
            <?php 
            if(isset($_GET['edit']))
            {
              $query = 'SELECT * FROM inventory_product WHERE status=0 AND id="'.$_GET['edit'].'"';

              $statement = $connect->prepare($query);
              $statement->execute();
              $total_data = $statement->rowCount();
              $result = $statement->fetchAll();
              if ($total_data > 0){   
                foreach($result as $row)
                {
                  ?>
                  <form action="" id="" method="post">
                    <div class="card card-danger">
                      <div class="card-header">
                        <h3 class="card-title">Edit Product</h3>                
                      </div>
                        <!-- /.card-header -->
                      <div class="card-body">
                        <div class="form-group">
                        <label for="product">Product</label>
                        <input type="text" class="form-control" id="product" name="product" value="<?php echo $row['product_name'] ; ?>">
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
                  <?php
                }
              }else{
                ?>
                <div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>This Cannot be found.</div>
                <?php
              }
            }else{
              ?>
              <form action="" id="add_death_form" method="post">
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Add Death Donation</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">
                  <div class="form-group">
                    <label for="employee_id">Service No </label>
                    <input type="text" class="form-control" id="employee_id" name="employee_id" autofocus autocomplete="off"> 
                    <span id="employee_name" class="text-success"></span>
                  </div>
                  <!-- 
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
                        <option value="<?php echo $row['join_id']; ?>"><?php echo $row['employee_no'].' '.$row['position_abbreviation'].' '.$row['surname'].' '.$row['initial']; ?></option>
                        <?php
                      }
                      ?>
                    </select>
                  </div> -->

                  <div class="form-group">
                    <label for="relation">Relation</label>
                    <input type="text" class="form-control" id="relation" name="relation">
                  </div>

                  <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="text" class="form-control" id="amount" name="amount">
                  </div>

                  <div class="form-group">
                        <label for="death_date">Death Date</label>
                        <div class="input-group date" id="reservationmonth" data-target-input="nearest">
                          <input type="text" name="death_date" id="death_date" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date("Y-m"); ?>"/>
                          <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                          </div>
                        </div>
                      </div>

                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="add_save"> Save</button>
                  <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button>
                </div>

              </div>
              <!-- /.card -->
            </form>
              <?php
            }
            
            ?>

            
          </div>

          
        </div>
        <!-- /.row -->
       
      
      </div><!-- /.container-fluid -->
    </section>    

<?php
include '../inc/footer.php';
?>
<script>
$(function () {
  
  $('#add_death_form').validate({
    rules: {
      employee_id: { required: true,
      remote: {
          url: "/check_employee_id",
          type: "post"
          }
        },
      relation: {required: true},
      amount: {required: true, number:true},
      death_date: {required: true,date:true}      
      
    },

    messages: {
      employee_id: {
        remote: 'Wrong Employee No!'
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

<script type="text/javascript">

load_data();

function load_data(query = '')
{
  var query = $('#employee_id').val();
      $.ajax({
        url:"/employee_no2",
        method:"POST",
        data:{query:query,request:1},
        dataType: 'json',

        success:function(response)
        {
          var len = response.length;
          
          var name_with_initial='';
          
          if(len > 0){
              var name_with_initial = response[0]['name_with_initial'];
          }
          document.getElementById('employee_name').innerHTML = name_with_initial;
        }
      });

      
}
$('#employee_id').keyup(function(){
     var query = $('#employee_id').val();
       load_data(1, query);
     });


</script>