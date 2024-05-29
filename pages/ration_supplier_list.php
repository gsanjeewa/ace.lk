
<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 48) == "false") {

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
                  <h3 class="card-title">Supplier List</h3>                       
                </div>
                  <!-- /.card-header -->
                <div class="card-body">  
                  <?php
                  $query = 'SELECT * FROM ration_supplier_list ORDER BY id DESC';

                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $total_data = $statement->rowCount();

                  $result = $statement->fetchAll();

                  ?>

                  <table id="example2" class="table table-bordered table-striped table-sm">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>                        
                        <th>Supplier Name</th>
                        <th>Bank Details</th>
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


                        $statement = $connect->prepare('SELECT bank_name, bank_no FROM bank_name WHERE id="'.$row['bank_id'].'"');
                          $statement->execute();
                          $total_bank = $statement->rowCount();
                          $result = $statement->fetchAll();
                          if ($total_bank > 0) :
                            foreach($result as $row_bank):
                  
                              $bank_name1 = $row_bank['bank_name'].' ('.$row_bank['bank_no'].')';        
                              $account_no1 =str_pad($row['bank_account'], 12, "0", STR_PAD_LEFT);                      
                            endforeach;
                          else:
                            $bank_name1 ='';
                            $account_no1 ='';
                          endif;

                          $statement = $connect->prepare('SELECT branch_name, branch_no FROM bank_branch WHERE id="'.$row['branch_id'].'"');
                          $statement->execute();
                          $total_bank = $statement->rowCount();
                          $result = $statement->fetchAll();
                          if ($total_bank > 0) :
                            foreach($result as $row_branch):
                  
                              $branch_name1 = $row_branch['branch_name'].' ('.str_pad($row_branch['branch_no'], 3, "0", STR_PAD_LEFT).')';
                              
                            endforeach;
                          else:
                           $branch_name1 ='';
                            
                          endif;

                          


                        ?>
                        <tr>
                            <td><center><?php echo $sno; ?></center></td>
                            <td><?php echo $row['supplier_name'];?></td>
                            <td>
                              <dl>
                                <dt><?php echo $bank_name1;?></dt>
                                <dd><?php echo $branch_name1;?></dd>
                                <dd><?php echo $account_no1;?></dd>
                              </dl>
                            </td>
                            <td>
                              <center>
                                <?php
                                if($row['status'] == 0): ?>
                                  <span class="badge badge-success">Supply</span>
                                <?php elseif($row['status'] == 1): ?>
                                  <span class="badge badge-danger">Not Supply</span>
                                <?php endif ?>
                              </center>
                            </td>
                            <td><center><button class="btn btn-sm btn-outline-primary edit_supplier" data-id="<?php echo $row['id']?>" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button></center></td>
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

  $('.edit_supplier').click(function(){
      var $id=$(this).attr('data-id');
      location.href = "/ration/add_supplier/"+$id;      
    }); 
  

  $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

 });
</script>

