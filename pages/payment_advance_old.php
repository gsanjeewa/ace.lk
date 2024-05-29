<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 76) == "false") {

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
                  <h3 class="card-title">Advance</h3>                
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

                  <table id="example2" class="table table-bordered table-striped table-sm">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>                        
                        <th>Employee No</th>
                        <th></th>
                        <th>Bank No</th>
                        <th>Branch No</th>
                        <th>Bank Account</th>
                        <th>Name</th>
                        <th></th>
                        <th></th>
                        <th></th>                        
                        <th></th>
                        <th>Advance</th>
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
                        $effective_date=date('Y-m', strtotime($_GET['effective_date']));                          
                        $statement = $connect->prepare("SELECT b.employee_no, c.surname, c.initial, a.amount, b.join_id, h.bank_no, h.branch_no, h.account_no, h.holder_name FROM salary_advance a 
                          INNER JOIN join_status b ON a.employee_id=b.join_id 
                          INNER JOIN employee c ON b.employee_id=c.employee_id 
                          LEFT JOIN (SELECT d.account_no, e.bank_name, e.bank_no, f.branch_name, f.branch_no, d.employee_id, d.holder_name FROM bank_details d INNER JOIN bank_name e ON d.bank_name=e.id 
                            INNER JOIN bank_branch f ON d.branch_name=f.id 
                            INNER JOIN (SELECT employee_id, MAX(id) maxid FROM bank_details GROUP BY employee_id) g ON d.employee_id = g.employee_id AND d.id = g.maxid) h ON c.employee_id=h.employee_id 
                          WHERE (a.status=2 OR a.status=1) AND DATE_FORMAT(a.date_effective,'%Y-%m') = '".$effective_date."' ORDER BY h.bank_no ASC, cast(b.employee_no as int) ASC");
                        $statement->execute();
                        $total_data = $statement->rowCount();

                        $result = $statement->fetchAll();

                        $startpoint =0;
                        $sno = $startpoint + 1;
                        foreach($result as $row)
                        {
                               
                          if (!empty($row['bank_no'])):
                            $bank_no = $row['bank_no'];
                          else:
                            $bank_no ='';
                          endif;

                          if (!empty($row['holder_name'])):
                            $holder_name = $row['holder_name'];
                          else:
                            $holder_name =str_replace(' ', '', $row['initial']).' '.$row['surname'];
                          endif;


                          if (!empty($row['branch_no'])):
                            $branch_no = str_pad($row['branch_no'], 3, "0", STR_PAD_LEFT);
                          else:
                            $branch_no ='';
                          endif;

                          if (!empty($row['account_no'])):
                            $account_no1 =str_pad($row['account_no'], 12, "0", STR_PAD_LEFT);
                          else:
                            $account_no1 ='';
                          endif;

                          
                          $with_decimal=round($row['amount'],2);
                          $remove_decimal=$with_decimal*100;
                          if ($row['bank_no']==7010) {
                            $no_code=52;
                          }else{
                            $no_code=23;
                          }


                        ?>
                        <tr>
                            <td><center><?php echo $sno; ?></center></td>
                            <td><center><?php echo $row['employee_no']; ?></center></td>
                            <td><center>0000</center></td>
                            <td><center><?php echo $bank_no;?></center></td>
                            <td><center><?php echo $branch_no;?></center></td>
                            <td><center><?php echo $account_no1;?></center></td>
                            <td style="text-align: left;"><?php echo $holder_name; ?></td>
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
                              <td>ADVANCE PAYMENT</td>
                              <td><?php echo strtoupper(date('Y F', strtotime($_GET['effective_date']))); ?></td>
                              <td></td>                                                        
                              <td>000000</td>
                        </tr>
                        <?php
                        $sno ++;
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
      "info": true,
      "autoWidth": false,
      "responsive": false,
      "scrollY": true,
      "scrollX": true,
      "buttons": [
        {
        extend:'excelHtml5',
        title:'Salary_Advance_'+'<?php echo $_GET['effective_date']; ?>',
        footer:true
      }
        ]
    }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');

    $('.salary_excel').click(function(){
      var $id=$(this).attr('data-id');
      location.href = "/payment/salary/"+$id;
    });

    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

  });
</script>