<?php 
session_start();
include '../pages/config.php';
$connect = pdoConnection();

for ($i = 1; $i <= 12; $i++) {
  $months[] = date("Y F", strtotime(date( 'Y-m-01' )." -$i months"));
}

foreach($months as $months_pay){             

$query="SELECT COALESCE(sum(j.net),0) AS total_salary, COALESCE(count(j.employee_id),0) AS total_count FROM payroll p INNER JOIN payroll_items j ON p.id = j.payroll_id WHERE DATE_FORMAT(p.date_from, '%Y-%m') = '".date("Y-m", strtotime($months_pay))."'";
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$total_data = $statement->rowCount();
if ($total_data > 0){
  foreach($result as $row)
  {
    $total_count[] = $row['total_count'];
    $total_salary[] = floatval(number_format($row['total_salary'], 2, '.', ''));   
  }
}
}

include '../inc/header.php';
?>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
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
          if ( isset($_SESSION["msg"]) ) {
            ?>
            <div class="col-xl-12 col-md-6 mb-4">
              <?php echo $_SESSION["msg"];
              unset($_SESSION["msg"]);
               ?>
            </div>
              <?php
          }                  
        
          ?>
        </div>
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>
                  <?php
                    $query="SELECT count(e.employee_id) AS employee_count FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid WHERE j.employee_status=0 OR j.employee_status=2 ORDER BY e.employee_id DESC";
                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $result = $statement->fetchAll();
                    foreach($result as $row)
                    {
                      echo $row['employee_count'];
                    }
                    ?>
                </h3>

                <p>Total Employee</p>
              </div>
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
              <a href="/employee_list/employee" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><span>LKR 
                  <?php
                    $query="SELECT sum(j.net) AS total_salary, p.id FROM payroll p INNER JOIN payroll_items j ON p.id = j.payroll_id WHERE p.status=1 AND MONTH(p.date_from) = MONTH(NOW() - INTERVAL 1 MONTH) AND (j.status=1 OR j.status=3)";
                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $result = $statement->fetchAll();
                    $total_data = $statement->rowCount();
                    if ($total_data > 0){
                      foreach($result as $row)
                      {
                        echo number_format($row['total_salary']);
                      }
                    }
                    ?>
                </span>
                </h3>

                <p>Last Month Salary</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="/payroll_list/payroll/<?php echo $row['id']; ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>LKR 
                  <?php
                  $query="SELECT sum(net) AS total_salary, p.id FROM payroll p INNER JOIN payroll_items j ON p.id = j.payroll_id WHERE p.status=1 AND MONTH(date_from) = MONTH(NOW() - INTERVAL 1 MONTH) AND j.status=2";
                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $result = $statement->fetchAll();
                  $total_data = $statement->rowCount();
                  if ($total_data > 0){
                    foreach($result as $row)
                    {
                      
                      echo number_format($row['total_salary']);
                      
                    }
                  }else{
                    echo '0';
                  }
                  ?>
                </h3>

                <p>Halt Salary</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>
                  <?php echo $total_salary; ?>
                </h3>

                <p>Unique Visitors</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-lg-6 connectedSortable">
            
            <!-- STACKED BAR CHART -->
            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">Month Salary</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="chart">
                  <canvas id="salaryBarChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
            
          </section>
          <!-- /.Left col -->
          <!-- right col (We are only adding the ID to make the widgets sortable)-->
          <section class="col-lg-6 connectedSortable">

            <!-- STACKED BAR CHART -->
            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">Number of employees on monthly salary</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="chart">
                  <canvas id="countBarChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
           

          </section>         

          <!-- right col -->
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
<?php
include '../inc/footer.php';
?>

<script>
  $(function () {
    
    //---------------------
    //- STACKED BAR CHART -
    //---------------------
    var salaryBarChartCanvas = $('#salaryBarChart').get(0).getContext('2d')
    var areaChartData = {
      labels  : <?php echo json_encode($months); ?>,
      datasets: [
        /*{
          label               : 'Count',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : false,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : <?php echo json_encode($total_count); ?>
        },*/
        {
          label               : 'Salary',
          backgroundColor     : 'rgba(210, 214, 222, 1)',
          borderColor         : 'rgba(210, 214, 222, 1)',
          pointRadius         : false,
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : <?php echo json_encode($total_salary); ?>
        },
      ]
    }

    var salaryBarChartOptions = {
      responsive              : true,
      maintainAspectRatio     : false,
      scales: {
        xAxes: [{
          stacked: true,
        }],
        yAxes: [{
          stacked: true
        }]
      }
    }

    new Chart(salaryBarChartCanvas, {
      type: 'bar',
      data: areaChartData,
      options: salaryBarChartOptions
    })

    //---------------------
    //- STACKED BAR CHART -
    //---------------------
    var countBarChartCanvas = $('#countBarChart').get(0).getContext('2d')
    var areaChartData = {
      labels  : <?php echo json_encode($months); ?>,
      datasets: [
        {
          label               : 'Count',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : false,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : <?php echo json_encode($total_count); ?>
        },
        /*{
          label               : 'Salary',
          backgroundColor     : 'rgba(210, 214, 222, 1)',
          borderColor         : 'rgba(210, 214, 222, 1)',
          pointRadius         : false,
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : <?php echo json_encode($total_salary); ?>
        },*/
      ]
    }

    var countBarChartOptions = {
      responsive              : true,
      maintainAspectRatio     : false,
      scales: {
        xAxes: [{
          stacked: true,
        }],
        yAxes: [{
          stacked: true
        }]
      }
    }

    new Chart(countBarChartCanvas, {
      type: 'bar',
      data: areaChartData,
      options: countBarChartOptions
    })

  })
</script>