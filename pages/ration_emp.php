
<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 52) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}
include '../inc/header.php';


?>

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Ration</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Ration</li>
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
                  <h3 class="card-title">Ration Deduction</h3>                       
                </div>
                  <!-- /.card-header -->
                <div class="card-body">  
                  <?php
                  $query = 'SELECT * FROM ration_deduction ORDER BY id DESC';

                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $total_data = $statement->rowCount();

                  $result = $statement->fetchAll();

                  ?>

                  <table id="example2" class="table table-bordered table-striped table-sm">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>                        
                        <th>Employee Name</th>
                        <th>Supplier Name</th>
                        <th>Amount</th>
                        <th>Ration Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row)
                      {                      
                        $query = 'SELECT e.employee_id, e.surname, e.initial, j.employee_no FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid WHERE j.join_id="'.$row['employee_id'].'" ORDER BY e.employee_id DESC';

                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row_employee):
                          endforeach;

                          $query = 'SELECT supplier_name FROM ration_supplier_list WHERE id="'.$row['ration_supplier'].'"';

                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row_supplier):
                          endforeach;

                          $statement = $connect->prepare('SELECT c.position_abbreviation FROM promotions a INNER JOIN position c ON a.position_id=c.position_id INNER JOIN (SELECT employee_id, MAX(id) maxid FROM promotions GROUP BY employee_id) b ON a.employee_id = b.employee_id AND a.id = b.maxid WHERE a.employee_id="'.$row['employee_id'].'"');
                        $statement->execute();
                        $total_position = $statement->rowCount();
                        $result = $statement->fetchAll();
                        if ($total_position > 0) :
                          foreach($result as $position_name):
                          
                            $position_id = $position_name['position_abbreviation'];
                          endforeach;
                          else:
                            $position_id ='';
                          endif;

                        ?>
                        <tr>
                            <td><center><?php echo $sno; ?></center></td>
                            <td><?php echo $row_employee['employee_no'].' '.$position_id.' '.$row_employee['surname'].' '.$row_employee['initial'] ?></td>
                            <td><?php echo $row_supplier['supplier_name']; ?></td>
                            <td><?php echo $row['amount']; ?></td>
                            <td><center><?php echo $row['date_effective']; ?></center></td>
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
    }, 5000);
   }
  }

  $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

 });
</script>

