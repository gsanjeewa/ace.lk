<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
require_once 'system_permissions.php';

$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 82) == "false") {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

include '../inc/header.php';

function fetchEmployeeData($connect, $employeeId) {
    $query = 'SELECT e.nic_no, e.surname, e.initial, j.employee_no, j.join_date 
              FROM employee e 
              INNER JOIN join_status j ON e.employee_id = j.employee_id               
              WHERE j.join_id = :employee_id 
              ORDER BY e.employee_id DESC';

    $statement = $connect->prepare($query);
    $statement->execute([':employee_id' => $employeeId]);
    return $statement->fetch();
}

function fetchETFData($connect, $fromDate, $toDate) {
    $query = "SELECT a.employee_id, a.employee_no 
              FROM payroll_items a 
              INNER JOIN payroll b ON a.payroll_id = b.id 
              WHERE DATE_FORMAT(date_from, '%Y-%m') BETWEEN :from_date AND :to_date AND employer_etf > 0 
              GROUP BY a.employee_id";

    $statement = $connect->prepare($query);
    $statement->execute([':from_date' => $fromDate, ':to_date' => $toDate]);
    return $statement->fetchAll();
}

function fetchMonthlyETF($connect, $employeeId, $month) {
    $query = "SELECT a.employer_etf 
              FROM payroll_items a 
              INNER JOIN payroll b ON a.payroll_id = b.id 
              WHERE DATE_FORMAT(date_from, '%Y-%m') = :month AND a.employee_id = :employee_id";

    $statement = $connect->prepare($query);
    $statement->execute([':month' => $month, ':employee_id' => $employeeId]);
    $result = $statement->fetch();
    return $result ? $result['employer_etf'] : 0;    
}

function fetchMonthlyEarnings($connect, $employeeId, $month) {
    $query = "SELECT a.basic_epf 
              FROM payroll_items a 
              INNER JOIN payroll b ON a.payroll_id = b.id 
              WHERE DATE_FORMAT(date_from, '%Y-%m') = :month AND a.employee_id = :employee_id";

    $statement = $connect->prepare($query);
    $statement->execute([':month' => $month, ':employee_id' => $employeeId]);
    $result = $statement->fetch();    
    return $result ? $result['basic_epf'] : '-';
}

?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Payment</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Payment</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <?php if (isset($errMSG)): ?>
                <div class="col-xl-12 col-md-6 mb-4">
                    <?= $errMSG; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION["msg"])): ?>
                <div class="col-xl-12 col-md-6 mb-4">
                    <?= $_SESSION["msg"]; unset($_SESSION["msg"]); ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">ETF</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <form method="GET" action="">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="from_date" class="control-label">From Month</label>
                                        <div class="input-group date" id="from_date" data-target-input="nearest">
                                            <input type="text" name="from_date" id="from_date" class="form-control datetimepicker-input" data-target="#from_date" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask/>
                                            <div class="input-group-append" data-target="#from_date" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="to_date" class="control-label">To Month</label>
                                        <div class="input-group date" id="to_date" data-target-input="nearest">
                                            <input type="text" name="to_date" id="to_date" class="form-control datetimepicker-input" data-target="#to_date" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask/>
                                            <div class="input-group-append" data-target="#to_date" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                                            </div>
                                        </div>
                                    </div>
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
                                            <th rowspan="2">NAME OF MEMBER</th>
                                            <th rowspan="2">MEMBER's NUMBER</th>
                                            <th rowspan="2">NIC NO</th>
                                            <th rowspan="2">TOTAL CONTRIBUTION</th>
                                            <?php
                                            if (isset($_GET['from_date']) && isset($_GET['to_date'])) {
                                                $start = new DateTime($_GET['from_date']);
                                                $end = new DateTime($_GET['to_date']);
                                                $interval = DateInterval::createFromDateString('1 month');
                                                $period = new DatePeriod($start, $interval, $end->add($interval));
                                                foreach ($period as $dt) {
                                                    echo '<th colspan="2">' . strtoupper($dt->format("M")) . '</th>';
                                                }
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <?php
                                            if (isset($_GET['from_date']) && isset($_GET['to_date'])) {
                                                $start = new DateTime($_GET['from_date']);
                                                $end = new DateTime($_GET['to_date']);
                                                $interval = DateInterval::createFromDateString('1 month');
                                                $period = new DatePeriod($start, $interval, $end->add($interval));
                                                foreach ($period as $dt) {
                                                    echo '<th>EARNINGS</th><th>CONTRIBUTION</th>';
                                                }
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_GET['from_date']) && isset($_GET['to_date'])) {
                                            $from_date = date('Y-m', strtotime($_GET['from_date']));
                                            $to_date = date('Y-m', strtotime($_GET['to_date']));
                                            $result = fetchETFData($connect, $from_date, $to_date);
                                            foreach ($result as $row) {
                                                $employeeData = fetchEmployeeData($connect, $row['employee_id']);
                                                echo "<tr>
                                                    <td style='text-align: left;'>{$employeeData['surname']} {$employeeData['initial']}</td>
                                                    <td style='text-align: right;'>{$row['employee_no']}</td>
                                                    <td style='text-align: left;'>{$employeeData['nic_no']}</td>";

                                                $totalETF = 0;
                                                foreach ($period as $dt) {
                                                    $month = $dt->format("Y-m");
                                                    $etf = fetchMonthlyETF($connect, $row['employee_id'], $month);
                                                    $totalETF += $etf;
                                                }
                                                echo "<td style='text-align: right;'>$totalETF</td>";

                                                foreach ($period as $dt) {
                                                    $month = $dt->format("Y-m");
                                                    $etf = fetchMonthlyETF($connect, $row['employee_id'], $month);
                                                    $earnings = fetchMonthlyEarnings($connect, $row['employee_id'], $month);
                                                    echo "<td>$earnings</td><td>$etf</td>";
                                                }
                                                echo "</tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
?>

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
        "responsive": false,
        "scrollY": true,
        "scrollX": true,
        "dom": 'Bfrtip',
        "buttons": [
            {
                extend: 'excelHtml5',
                title: function() {
                    return 'ETF_<?php echo $from_date; ?>-<?php echo $to_date; ?>';
                },
                footer: true
            }
        ]
    }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('#from_date').datetimepicker({
            format: 'YYYY-MM'
        });
        $('#to_date').datetimepicker({
            format: 'YYYY-MM'
        });
    });
});

</script>
