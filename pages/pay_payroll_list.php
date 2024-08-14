
<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 32) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

if (isset($_POST['paid'])){

  if (checkPermissions($_SESSION["user_id"], 32) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/payroll_list/payroll');
    exit();
  }

  $data = array(
      ':id'      =>  $_POST['payroll_id'],     
  );

  $query = "UPDATE payroll SET status=2 WHERE `id`=:id";
    
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

include '../inc/header.php';


?>

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Payroll</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Payroll</li>
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
       
          <div class="form-group" id="process" style="display:none;">
        <div class="progress">
       <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="">
       </div>
      </div>
       </div>        
        
        <div class="row">
          
            <div class="col-xl-12 col-md-6 mb-4" id="success_message">
          
            </div>
          
        </div>
        <div class="row">          
          <div class="col-md-12">
            
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Payroll List</h3>                       
                </div>
                  <!-- /.card-header -->
                <div class="card-body"> 
                  
                  <?php        
                  $query = 'SELECT * FROM Payroll ORDER BY id DESC';

                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $total_data = $statement->rowCount();

                  $result = $statement->fetchAll();

                  ?>

                  <table id="example2" class="table table-bordered table-striped table-sm">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>                        
                        <th>Ref No</th>
                        <th>Date Form</th>
                        <th>Date To</th>
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
                        ?>
                        <tr>
                            <td><center><?php echo $sno; ?></center></td>
                            <td><center><?php echo $row['ref_no'];?></center></td>
                            <td><center><?php echo $row['date_from'];?></center></td>
                            <td><center><?php echo $row['date_to'];?></center></td>
                            <td>
                              <!-- <?php 

                              $statement = $connect->prepare("SELECT sum(amount) AS death_donation, id FROM death_donation WHERE due_date BETWEEN '".$row['date_from']."' AND '".$row['date_to']."'");
                      $statement->execute();
                      $total_death = $statement->rowCount();
                      $result = $statement->fetchAll();
                      if ($total_death > 0) {
                        foreach($result as $death_donation){             
                          $death_donation=$death_donation['death_donation'];            
                        } 
                      }else{
                        $death_donation='';
                      }

                      
                      $statement = $connect->prepare("SELECT COUNT(employee_id) AS total_count FROM (SELECT employee_id FROM attendance WHERE start_date = '".$row['date_from']."' AND end_date = '".$row['date_to']."' GROUP BY employee_id Having SUM(no_of_shifts) > 25) indebted");
                    $statement->execute();
                    $result = $statement->fetchAll();
                    foreach($result as $total_count){             

                    $total_amount=$total_count['total_count']; 
                    
                  }

                  $statement = $connect->prepare("SELECT employee_id FROM attendance WHERE start_date = '".$row['date_from']."' AND end_date = '".$row['date_to']."' GROUP BY employee_id Having SUM(no_of_shifts) > 25");
                    $statement->execute();
                    $result = $statement->fetchAll();
                    foreach($result as $row_id){             

                    $employee_id=$row_id['employee_id']; 
                    
                    if ($employee_id == 2) {
                    if ((!empty($death_donation)) && (!empty($total_amount))) {
                    echo $death_donation/$total_amount;
                    }
                    }

                    }

                    ?> -->                    

                              <center>                                           
                                <?php if($row['status'] == 0): ?>
                                  <span class="badge badge-primary">New</span>
                                <?php elseif($row['status'] == 1): ?>
                                  <span class="badge badge-success">Calculated</span>
                                <?php else: ?>
                                  <span class="badge badge-secondary">Paid</span>
                                <?php endif ?>
                              </center>
                            </td>

                            <td>
                              <center>
                                
                                <?php if($row['status'] == 0): ?>
                                  <form method="POST" id="sample_form">
                                  <input type="hidden" name="payroll_id" value="<?php echo $row['id']?>">
                                   <button class="btn btn-sm btn-outline-success" name="calculate_payroll" id="calculate_payroll" type="submit" data-toggle="tooltip" data-placement="top" title="Calculate"><i class="fas fa-calculator"></i> Calculate</button>
                                   <button class="btn btn-sm btn-outline-primary edit_payroll" data-id="<?php echo $row['id']?>" type="button" data-toggle="tooltip" data-placement="left" title="Edit Payroll"><i class="fa fa-edit"></i></button>                                  
                                  <button class="btn btn-sm btn-outline-danger" name="remove_payroll"><i class="fa fa-trash" data-toggle="tooltip" data-placement="left" title="Remove Payroll"></i></button>
                                  </form> 
                                  <?php elseif($row['status'] == 1): ?>
                                    <form method="POST" id="payForm" action="">
                  
                                    <input type="hidden" class="form-control" name="payroll_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" class="form-control" name="payroll_month" value="<?php echo $row['date_from']; ?>">
                                    <button class="btn btn-sm btn-outline-warning view_payroll" data-id="<?php echo $row['id']?>" type="button" data-toggle="tooltip" data-placement="left" title="View Payroll"><i class="fa fa-eye"></i></button>
                                   
                                    <button class="btn btn-sm btn-outline-danger" id="paid" name="paid" data-toggle="tooltip" data-placement="top" title="Paid" type="submit"><i class="fa fa-money-bill"></i></button>
                                  </form>  
                                <?php else: ?>
                                   <button class="btn btn-sm btn-outline-warning view_payroll" data-id="<?php echo $row['id']?>" type="button" data-toggle="tooltip" data-placement="left" title="View Payroll"><i class="fa fa-eye"></i></button>
                                <?php endif ?>

                                  
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

<script>
 
 $(document).ready(function(){

  $('#example2').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });

  $('.view_payroll').click(function(){
      var $id=$(this).attr('data-id');
      location.href = "/payroll_list/payroll/"+$id;      
    });
  
  $('#sample_form').on('submit', function(event){
   event.preventDefault();   
    $.ajax({
     url:"/process",
     method:"POST",
     data:$(this).serialize(),
     beforeSend:function()
     {
      $('#calculate_payroll').attr('disabled', 'disabled');
      $('#process').css('display', 'block');
     },
     success:function(data)
     {
      var percentage = 0;

      var timer = setInterval(function(){
       percentage = percentage + 20;
       progress_bar_process(percentage, timer);
      }, 1000);
     }
    })
   
  });

  function progress_bar_process(percentage, timer)
  {
   $('.progress-bar').css('width', percentage + '%');
   if(percentage > 100)
   {
    clearInterval(timer);
    $('#sample_form')[0].reset();
    $('#process').css('display', 'none');
    $('.progress-bar').css('width', '0%');
    $('#calculate_payroll').attr('disabled', false);
    $('#success_message').html('<div class="alert alert-dismissible alert-success bg-gradient-success text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><span class="glyphicon glyphicon-info-sign"></span>Success.</div>');
    setTimeout(function(){
     $('#success_message').html('');
     location.reload();
    }, 2000);
   }
  }

  $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

 });
</script>

