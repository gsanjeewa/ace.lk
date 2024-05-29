
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
          <div class="col-md-12">

            <div class="form-group" id="process" style="display:none;">
        <div class="progress">
       <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="">
       </div>
      </div>
       </div>
        
        
        <div class="row">
          
            <div class="col-xl-12 col-md-12 mb-4" id="success_message">
          
            </div>
          
        </div>

        <div class="row">
          <div class="box-header with-border">
              <div class="pull-right">
                
              </div>
            </div>
        </div>
                    
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Pay Slip</h3>                                 
                </div>
                  <!-- /.card-header -->
                <div class="card-body">  
                  <?php
                  if(isset($_GET['view']))
                  {
                    $query = 'SELECT * FROM payroll_items WHERE id="'.$_GET['view'].'" ORDER BY id ASC';  
        $statement = $connect->prepare($query);
        $statement->execute();
        $total_data = $statement->rowCount();
        $result = $statement->fetchAll();
        foreach($result as $row)
        {
          $statement = $connect->prepare("SELECT date_from, date_to FROM payroll WHERE id ='".$row['payroll_id']."'");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $pay):
      
          $date_from=$pay['date_from'];
          $date_to=$pay['date_to'];
          
        
        endforeach;

          $query = 'SELECT surname, initial FROM employee WHERE employee_id="'.$row['employee_id'].'"';
            $statement = $connect->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll();
            foreach($result as $employee_name)
            { 
            }
          $query = 'SELECT position_abbreviation FROM position WHERE position_id="'.$row['position_id'].'"';
            $statement = $connect->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll();
          foreach($result as $position_name)
            { 
            }



            //-----------------Bank Details------------------------//

            $statement = $connect->prepare("SELECT a.account_no, b.bank_name, c.branch_name FROM bank_details a INNER JOIN bank_name b ON a.bank_name=b.id INNER JOIN bank_branch c ON a.branch_name=c.id WHERE a.id='".$row['bank_id']."'");
            $statement->execute();
            $result = $statement->fetchAll();
            if ($statement->rowCount()>0) {
              foreach($result as $bank_id){
                  $bank_account_no = $bank_id['account_no'];
                  $bank_bank_name = $bank_id['bank_name'];
                  $bank_branch_name = $bank_id['branch_name'];
              }
            }else{
              $bank_account_no='';
              $bank_bank_name ='';
              $bank_branch_name ='';
            }
            
      ?>


      <div class="page">
        <div class="col-md-12 text-center"><h4><b>ACE FRONT LINE SECURITY SOLUTIONS (PVT) LTD</b></h4></div>
        <div class="col-md-12 text-center"><h5>No:150/20, First Lane, Kumbukgahaduwa, Perliment Road, Pitakotte</h5></div>
        <div class="col-md-12 text-center"><h4>වැටුප් පත්‍රය</h4></div>
        <div class="col-md-12">
          <table>
            <tr>  
                      <td width="30%">කාල වකවානුව</td>
                      <td width="2%" align="center">:</td>
                      <td width="68%"><b><?php echo date("Y F", strtotime($date_from)); ?></b></td>
                  </tr>
                  <tr>
                      <td width="30%">සාමාජික අංකය</td>
                      <td width="2%" align="center">:</td>
                      <td width="68%"><b><?php echo $row['employee_no'] ?></b></td>
                  </tr>
                  <tr>
                      <td width="30%">නම</td>
                      <td width="2%" align="center">:</td>
                      <td width="68%"><b><?php echo $employee_name['surname'].' '.$employee_name['initial']?></b></td>                               
                  </tr>
                  <tr>
                  <td width="30%">නිලය</td>
                  <td width="2%" align="center">:</td>
                      <td width="68%"><b><?php echo $position_name['position_abbreviation'];?></b></td>
                  </tr>
                  <tr>
                  <td width="30%">ගිණුම් අංකය</td>
                  <td width="2%" align="center">:</td>
                      <td width="68%"><b><?php echo $bank_account_no;?></b></td>
                  </tr>
                  <tr>
                  <td width="30%">බැංකුව</td>
                  <td width="2%" align="center">:</td>
                      <td width="68%"><b><?php echo $bank_bank_name.' - '.$bank_branch_name;?></b></td>
                  </tr>
                  <tr>
                  <td width="30%">මුලු වැඩ මුර</td>
                  <td width="2%" align="center">:</td>
                      <td width="68%"><b><?php echo $row['no_of_shift']?></b></td>
                  </tr>
          </table>        
      </div>

      <div class="col-md-12">
        <table class="table table-sm" >
          <thead>
            <tr>
              <th style="width: 50%; text-align: center;">ඉපැයීම්</th>
              <th style="width: 50%; text-align: center;">අඩුකිරීම්</th>
            </tr>
          </thead>
          <tbody style="height: 300px;">
            <tr>
              <td>
                <table class="table table-borderless">
                  <tr> 
                                <td width="70%">මුලික වැටුප</td>
                                <td width="2%" align="center">:</td>
                                <td width="28%" align="right"><?php echo number_format($row['basic_salary'], 2)?></td> 
                            </tr>
                            <tr>
                                <td width="70%">අතිකාල දිමනාව (පැය:<?php echo $row['ot_hrs']?>)</td>
                                <td width="2%" align="center">:</td>
                                <td width="28%" align="right"><?php echo number_format($row['ot_amount'], 2)?></td> 
                            </tr>
                            <tr> 
                                <td width="70%">දිරි දීමනා</td>
                                <td width="2%" align="center">:</td>
                                <td width="28%" align="right"><?php echo number_format($row['incentive'], 2)?></td> 
                            </tr>

                            <?php
                          $query = 'SELECT * FROM allowances';

                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $total_data = $statement->rowCount();
                          $result = $statement->fetchAll();
                          foreach($result as $rows):
                              $all_arr[$rows['allowances_id']] = $rows['allowances_si'];
                          endforeach;
                          
                          foreach(json_decode($row['allowances']) as $k => $val):
                            ?>
                          <tr> 
                                  <td width="70%"><?php echo $all_arr[$val->aid] ?></td>
                                  <td width="2%" align="center">:</td>
                                  <td width="28%" align="right"><?php echo number_format($val->amount, 2)?></td> 
                              </tr>
                  <?php
                          endforeach;
                  ?>                        
                </table>
              </td>
              <td>
                <table class="table table-borderless">
                  <?php 
                            if ($row['employee_epf'] > 0){
                              ?>
                    <tr> 
                                  <td width="70%">සේ: අර්ථසාධක අරමුදල 8%</td>
                                  <td width="2%" align="center">:</td>
                                  <td width="28%" align="right">
                                  <?php
                                  echo number_format($row['employee_epf'], 2);
                                      ?>                                  
                                    </td> 
                              </tr>
                            <?php 
                            }
                                if (($row['absent_day'] > 0) && ($row['absent_amount'] > 0)):
                                    ?>
                            <tr>
                                      <td width="70%">වැටුප් රහිත දින සඳහා අඩු කිරීම් (දින:<?php echo $row['absent_day'] ?>)</td>
                                      <td width="2%" align="center">:</td>
                                      <td width="28%" align="right">
                                        <?php 
                                        
                                        echo number_format($row['absent_amount'], 2);
                                        
                                      ?></td> 
                                  </tr>
                    <?php
                            endif;

                                if ($row['loan_amount'] > 0):
                                    ?>
                            <tr>
                                      <td width="70%">ණය අඩු කිරිම්</td>
                                      <td width="2%" align="center">:</td>
                                      <td width="28%" align="right"><?php echo number_format($row['loan_amount'], 2);?></td> 
                                  </tr>
                    <?php
                            endif;

                            if ($row['advance_amount'] > 0):
                                    ?>
                            <tr>
                                      <td width="70%">වැටුප් අත්තතිකරම්</td>
                                      <td width="2%" align="center">:</td>
                                      <td width="28%" align="right"><?php echo number_format($row['advance_amount'], 2);?></td> 
                                  </tr>
                    <?php
                            endif;

                            if ($row['inventory_amount'] > 0):
                                    ?>
                            <tr>
                                      <td width="70%">නිල ඇඳුම්</td>
                                      <td width="2%" align="center">:</td>
                                      <td width="28%" align="right"><?php echo number_format($row['inventory_amount'], 2);?></td> 
                                  </tr>
                    <?php
                            endif;

                            if ($row['ration_amount'] > 0):
                                    ?>
                            <tr>
                                      <td width="70%">ආහාර</td>
                                      <td width="2%" align="center">:</td>
                                      <td width="28%" align="right"><?php echo number_format($row['ration_amount'], 2);?></td> 
                                  </tr>
                    <?php
                            endif;

                            if ($row['death_donation'] > 0):
                                    ?>
                            <tr>
                                      <td width="70%">මරණාධාර සඳහා අඩුකිරීම්</td>
                                      <td width="2%" align="center">:</td>
                                      <td width="28%" align="right"><?php echo number_format($row['death_donation'], 2);?></td> 
                                  </tr>
                    <?php
                            endif;
                            
                            $query = 'SELECT * FROM deduction';

                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $total_data = $statement->rowCount();
                          $result = $statement->fetchAll();
                          foreach($result as $rows):
                              $all_arr[$rows['deduction_id']] = $rows['deduction_si'];
                          endforeach;
                          
                          foreach(json_decode($row['deductions']) as $k => $val):
                  ?>
                          <tr> 
                                    <td width="70%"><?php echo $all_arr[$val->did] ?></td>
                                    <td width="2%" align="center">:</td>
                                    <td width="28%" align="right"><?php echo number_format($val->amount, 2)?></td> 
                                </tr>
                  <?php
                          endforeach;
                  ?>
                </table>
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <th>
                <table class="table table-borderless">
                  <tr>
                    <td width="70%"><b>දළ වැටුප</b></td>
                                    <td width="2%" align="center">:</td>
                                    <td width="28%" align="right"><span style="border-bottom: 1px solid; text-decoration: underline;"><b><?php echo number_format($row['gross'], 2);?></b></span></td>
                  </tr>
                </table>
              </th>
              <th>
                <table class="table table-borderless">
                  <tr>
                    <td width="60%"><b>අඩුකිරීම් වල එකතුව</b></td>
                                    <td width="2%" align="center">:</td>
                                    <td width="38%" align="right"><span style="border-bottom: 3px solid; text-decoration: underline;"><b><?php echo number_format($row['deduction_amount'], 2);?></b></span></td>
                  </tr>
                </table>
              </th>
            </tr>
          </tfoot>
        </table>
        
      </div>

      <div class="col-md-12">
        <table>  
                <tr> 
                    <td width="70%"><b>ශුද්ධ වැටුප</b></td>
                    <td width="2%" align="center">:</td>
                    <td width="28%" align="right"><span style="border-bottom: 1px solid; text-decoration: underline;"><b><?php echo number_format($row['net'], 2);?></b></span></td> 
                </tr>
                <tr> 
                    <td width="70%">සේ.අ. අරමුදල 12%</td>
                    <td width="2%" align="center">:</td>
                    <td width="28%" align="right"><b>
                      <?php 
                          if ($row['employer_epf']>0) {
                            echo number_format($row['employer_epf'], 2);
                          }?>
                      </b></td> 
                </tr>
                <tr> 
                    <td width="70%">සේ.නි.භාරකාර අරමුදල 3%</td>
                    <td width="2%" align="center">:</td>
                    <td width="28%" align="right"><b>
                      <?php
                                  if ($row['employer_etf']>0) {
                                    echo number_format($row['employer_etf'], 2);
                                   } ?>
                      </b></td> 
                </tr>
            </table>
      </div>
        <hr>
        <div class="col-md-12">
          <table> 
            <?php
                $query = 'SELECT * FROM department ORDER BY department_id DESC LIMIT 5' ;
                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();
                $result = $statement->fetchAll();
                foreach($result as $rows):
                    $all_arr[$rows['department_id']] = $rows['department_name'];
                endforeach;

                $query = 'SELECT * FROM position';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();
                $result = $statement->fetchAll();
                foreach($result as $rows):
                    $all_arr2[$rows['position_id']] = $rows['position_abbreviation'];
                endforeach;
              
                foreach(json_decode($row['department']) as $k => $val):
          ?>          
        
                    
                    <tr> 
                        <td width="70%"><?php echo $all_arr[$val->d_id].' - '.$all_arr2[$val->p_id].'('.$val->t_shifts.')';?></td>
                        <!-- <td width="2%" align="center">:</td>
                        <td width="28%" align="right"><?php echo $val->t_shifts?></td> --> 
                    </tr>         
            <?php
              endforeach;
              ?>
            </table>
      </div>
      <hr>
      <div class="col-md-12">
        <table> 
              <?php
              /*$query = 'SELECT * FROM death_donation WHERE due_date BETWEEN "'.$date_from.'" AND "'.$date_to.'"';*/

              $query="SELECT e.surname, e.initial, j.employee_no, d.relation FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN death_donation d ON e.employee_id = d.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid WHERE d.due_date BETWEEN '".$date_from."' AND '".$date_to."' ORDER BY e.employee_id DESC";

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();
                $result = $statement->fetchAll();
                foreach($result as $rows):

                    ?>
                    
              <tr> 
                        <td width="70%"><?php echo $rows['employee_no'].' '.$rows['surname'].' '.$rows['initial'].' '.$rows['relation']; ?></td>
                        
                    </tr>
              
                    <?php
                endforeach;

          ?>
        </table>
      </div>
        
      <hr>
        <div class="col-md-12">
          <?php
            $query = "SELECT * FROM pay_note WHERE effective_date BETWEEN '".$date_from."' AND '".$date_to."'" ;
              $statement = $connect->prepare($query);
              $statement->execute();
              $total_data = $statement->rowCount();
              $result = $statement->fetchAll();
              foreach($result as $rows):
                  echo $rows['note'];
              endforeach;
          ?>
        </div>
        <hr>
        <div class="col-md-12">
          <?php
            $query = "SELECT * FROM signature ORDER BY id DESC" ;
              $statement = $connect->prepare($query);
              $statement->execute();
              $total_data = $statement->rowCount();
              $result = $statement->fetchAll();
              foreach($result as $rows):
                  echo $rows['sig_name'];
              endforeach;
          ?>
        </div>


    </div>

    <?php
    }
    }

    ?>

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
      "responsive": false,
      "scrollX": true,
    });

  $('.view_payslip').click(function(){
      var $id=$(this).attr('data-id');
      location.href = "/payroll_list/payroll/pay_slip/"+$id;      
    });
  
  $('#sample_form').on('submit', function(event){
   event.preventDefault();   
    $.ajax({
     url:"/process_approved",
     method:"POST",
     data:$(this).serialize(),
     beforeSend:function()
     {
      $('#approved').attr('disabled', 'disabled');
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
    $('#approved').attr('disabled', false);
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

