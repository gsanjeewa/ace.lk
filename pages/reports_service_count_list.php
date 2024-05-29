
<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();

require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 95) == "false") {

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
            <h1 class="m-0 text-dark">Report</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Report</li>
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
          <div class="container box">
   <h3 align="center"><?php echo $_GET['svc']; ?> Year Completed List</h3>
   <br />
   
   <div class="table-responsive">
    <?php
    if((isset($_GET['date'])) && (isset($_GET['svc'])))
    {
      $effective_date=date('Y-m-d', strtotime($_GET['date']));
    $query="WITH sevicecount AS (SELECT join_id, join_date, employee_id, employee_no, Floor(datediff('".$effective_date."', join_date)/365) as svc FROM join_status WHERE employee_status=0) SELECT a.join_id, a.join_date, CONCAT(b.surname, ' ', b.initial)AS full_name, a.employee_no, b.nic_no FROM sevicecount a INNER JOIN employee b ON a.employee_id=b.employee_id WHERE a.svc ='".$_GET['svc']."';";   

    $statement = $connect->prepare($query);
    $statement->execute();
    $total_data = $statement->rowCount();
    $result = $statement->fetchAll();

    ?>

    <table id="customer_data" class="table table-bordered table-striped table-sm">
     <thead>
      <tr>
        <th width="5%">#</th>                        
        <th width="15%">Service No</th>
        <th width="10%">Rank</th>
        <th width="40%">Name</th>
        <th width="20%">NIC No</th>
        <th width="10%">Join Date</th>
      </tr>
     </thead>
     <tbody>
        <?php
        $startpoint =0;
        $sno = $startpoint + 1;
        foreach($result as $row)
        {
          $statement = $connect->prepare('SELECT c.position_abbreviation FROM promotions a INNER JOIN position c ON a.position_id=c.position_id INNER JOIN (SELECT employee_id, MAX(id) maxid FROM promotions GROUP BY employee_id) b ON a.employee_id = b.employee_id AND a.id = b.maxid WHERE a.employee_id="'.$row['join_id'].'"');
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
              <td><?php echo $sno; ?></td>
              <td style="text-align: left;"><?php echo $row['employee_no'];?></td>
              <td style="text-align: left;"><?php echo $position_id;?></td>
              <td style="text-align: left;"><?php echo $row['full_name'];?></td>
              <td><?php echo $row['nic_no'];?></td>              
              <td><center>
          <?php if($row['join_date']!='0000-00-00'): echo $row['join_date']; endif;

            ?>
            </center></td>
          </tr>
          <?php
          $sno ++;
        }
        ?>
      </tbody>
                    
    </table>

  <?php } ?>
  
   </div>
  </div>         
        </div>
        <!-- /.row -->
       
      
      </div><!-- /.container-fluid -->
    </section>   

 <?php
include '../inc/footer.php';
?>
<script type="text/javascript" language="javascript" >
 $(document).ready(function(){
  
  $('#customer_data').DataTable({
      "lengthChange": false,
      "searching" : false,
      "paging": false,
      "info": false,
      "autoWidth": true,
      "responsive": true,
      "scrollX": true,            
      dom: 'Bfrtip',
      buttons: [
      {
          extend:'excelHtml5',
          title:<?php echo $_GET['svc']; ?>+' Year Completed List',
          footer:true
        }
     ],   
    });  
  
 });
 
</script>

