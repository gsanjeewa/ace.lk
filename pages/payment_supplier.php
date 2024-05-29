<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 74) == "false") {

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
            <h1 class="m-0 text-dark">Payment</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Payment</li>
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
            
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Supplier</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body"> 

                <div class="row">
                  <form method="GET">
                    <div class="col-md-12">
                    <div class="form-group">
                      <label for="effective_date" class="control-label">Month</label>
                      <div class="input-group date" id="reservationmonth" data-target-input="nearest">
                          <input type="text" name="effective_date" id="effective_date" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask/>
                          <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                          </div>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-12">
                      <button class="btn btn-sm btn-primary salary_excel">Submit</button>
                    </div>
                  </form>

                  </div>
                  <br>
                  <div class="row">
                    <div class="col-md-12">               

                  <table id="example2" class="table table-bordered table-striped">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>                        
                        <th></th>
                        <th>Bank No</th>
                        <th>Branch No</th>
                        <th>Bank Account</th>
                        <th>Name</th>
                        <th></th>
                        <th></th>
                        <th></th>                        
                        <th></th>
                        <th>Salary</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>  
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      
                      if(isset($_GET['effective_date']))
                      {
                        $start_date = date('Y-m-d', strtotime($_GET['effective_date']));
                        $end_date = date('Y-m-t', strtotime($start_date));
                      
                      $statement = $connect->prepare("SELECT sum(a.amount) AS total, b.supplier_name, b.bank_id, b.branch_id, b.bank_account 
                        FROM ration_deduction a 
                        INNER JOIN ration_supplier_list b ON a.supplier_id=b.id 
                        WHERE a.status=1 AND date_effective BETWEEN '".$start_date."' AND '".$end_date."' ORDER BY a.id DESC");
                      $statement->execute();
                      $total_data = $statement->rowCount();

                      $result = $statement->fetchAll();

                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row)
                      {
                        $statement = $connect->prepare("SELECT bank_no FROM bank_name WHERE id='".$row['bank_id']."'");
                          $statement->execute();
                          $total_bank = $statement->rowCount();
                          $result = $statement->fetchAll();
                          if ($total_bank > 0) :
                          foreach($result as $row_b):
                            $bank_no = $row_b['bank_no'];
                            $account_no1 =str_pad($row['bank_account'], 12, "0", STR_PAD_LEFT);
                            
                          endforeach;
                          else:
                            $bank_no ='';
                            $account_no1 ='';                             
                          endif;

                          $statement = $connect->prepare("SELECT branch_no FROM bank_branch WHERE id='".$row['branch_id']."'");
                          $statement->execute();
                          $total_bank = $statement->rowCount();
                          $result = $statement->fetchAll();
                          if ($total_bank > 0) :
                          foreach($result as $row_branch):
                            $branch_no = str_pad($row_branch['branch_no'], 3, "0", STR_PAD_LEFT);
                            
                          endforeach;
                          else:
                            $branch_no ='';
                            
                          endif;

                          $with_decimal=round($row['total'],2);
                          $remove_decimal=$with_decimal*100;
                          if ($row_b['bank_no']==7010) {
                            $no_code=52;
                          }else{
                            $no_code=23;
                          }
                                              
                        if ($row['total'] !='') {
                          
                        
                        ?>
                        <tr>
                            <td><center><?php echo $sno; ?></center></td>
                            <td><center>0000</center></td>
                            <td><center><?php echo $bank_no;?></center></td>
                            <td><center><?php echo $branch_no;?></center></td>
                            <td><center><?php echo $account_no1;?></center></td>
                            <td style="text-align: left;"><?php echo $row['supplier_name']; ?></td>
                            <td><?php echo $no_code; ?></td>
                            <td>00</td>
                            <td>0</td>
                            <td>000000</td>
                            <td style="text-align: right;"><?php echo $remove_decimal;?></td>
                              <td>SLR</td>
                              <td>7010</td>
                              <td>612</td>
                              <td>000079289055</td>
                              <td>ACE FRONT LINE</td>
                              <td>SUPPLIER PAYMENT</td>
                              <td><?php echo strtoupper(date('Y F', strtotime($_GET['effective_date']))); ?></td>
                              <td></td>                                                        
                              <td>000000</td>                         
                        </tr>
                        <?php
                        $sno ++;
                      }
                      }
                    }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
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

<script type="text/javascript">
  $(document).ready(function() {

    $('#example2').DataTable({
      "paging": false,
      "lengthChange": false,
      "searching": false,
      "ordering": false,
      "info": false,
      "autoWidth": false,
      "responsive": true,
      "scrollY": true,
      "buttons": ["excel"]
    }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');


    $('.edit_employee').click(function(){
      var $id=$(this).attr('data-id');
      location.href = "/employee_list/add_employee/"+$id;
      
    });
    
    $('.view_employee').click(function(){
      var $id=$(this).attr('data-id');
      location.href = "/employee_list/employee/"+$id;
      
    });

    $('.add_bank').click(function(){
      var $id=$(this).attr('data-id');
      location.href = "/employee_list/"+$id+"/add_bank";
      
    });           

    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

  });
</script>