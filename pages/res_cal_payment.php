<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 68) == "false") {
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
                <h1 class="m-0 text-dark">Resignation</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Resignation</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <?php if (isset($errMSG)) { ?>
                <div class="col-xl-12 col-md-6 mb-4">
                    <?php echo $errMSG; ?>
                </div>
            <?php }
            if (isset($_SESSION["msg"])) { ?>
                <div class="col-xl-12 col-md-6 mb-4">
                    <?php
                    echo $_SESSION["msg"];
                    unset($_SESSION["msg"]);
                    ?>
                </div>
            <?php } ?>
        </div>

        <div class="form-group" id="process" style="display:none;">
            <div class="progress">
                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 col-md-6 mb-4" id="success_message"></div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form id="add_issue_form" method="post">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Calculate</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_id">Employee</label>
                                        <select class="form-control select2" style="width: 100%;" name="employee_id" id="employee_id">
                                            <option value="">Select Employee</option>
                                            <?php
                                            $query="SELECT e.initial, e.surname, p.position_abbreviation, j.employee_no, j.join_id FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN promotions c ON j.join_id=c.employee_id INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro INNER JOIN position p ON c.position_id=p.position_id WHERE j.employee_status NOT BETWEEN 3 AND 4 ORDER BY j.employee_no DESC";
                                            $statement = $connect->prepare($query);
                                            $statement->execute();
                                            $result = $statement->fetchAll();
                                            foreach($result as $row) {
                                                echo '<option value="'.$row['join_id'].'">'.$row['employee_no'].' '.$row['position_abbreviation'].' '.$row['surname'].' '.$row['initial'].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="last_payroll">Last Payroll</label>
                                                <input type="text" class="form-control" id="last_payroll" name="last_payroll" onblur="getAmount(this.value)" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="deduction">Deduction</label>
                                                <input type="text" class="form-control" id="deduction" name="deduction" onkeyup="getAmount(this.value)" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="doc_chargers">Document Chargers</label>
                                                <input type="text" class="form-control" id="doc_chargers" name="doc_chargers" onkeyup="getAmount(this.value)">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="total_deduction">Total Deduction</label>
                                                <input type="text" class="form-control" id="total_deduction" name="total_deduction" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="paid_amount">Paid Amount</label>
                                                <input type="text" class="form-control" id="paid_amount" name="paid_amount" onkeyup="getAmount(this.value)">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reson">Reason</label>
                                                <input type="text" class="form-control" id="reson" name="reson">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="net">Net</label>
                                                <input type="text" class="form-control" id="net" name="net" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="resignation_date">Resignation Date:</label>
                                                <div class="input-group date" id="reservationjoindate" data-target-input="nearest">
                                                    <input type="text" name="resignation_date" id="resignation_date" class="form-control datetimepicker-input" data-target="#reservationjoindate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo date("Y-m-d"); ?>"/>
                                                    <div class="input-group-append" data-target="#reservationjoindate" data-toggle="datetimepicker">
                                                        <div class="input-group-text">
                                                            <i class="fa fa-calendar-alt"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead style="text-align: center;">
                                            <tr>
                                                <th style="width: 30%;">Deduction</th>
                                            </tr>
                                        </thead>
                                        <tbody id="post_data">
                                            <tr><td colspan="10" class="text-center">Select Employee</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="form-group">
                                <button class="btn btn-sm btn-outline-success" name="calculate_payroll" id="calculate_payroll" type="submit" data-toggle="tooltip" data-placement="top" title="Calculate"><i class="fas fa-calculator"></i> Calculate</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include '../inc/footer.php'; ?>

<script src="/plugins/bs-stepper/main.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        load_data();

        setInterval(function(){
            load_data();
        }, 2000);

        function load_data(query = '') {
            var query = $('#employee_id').val();
            $.ajax({
                url:"/fetch_cal_pay",
                method:"POST",
                data:{query:query},
                dataType: 'json',
                success:function(response) {
                    var html = '';
                    var last_payroll = '';
                    var deduction = '';
                    if(response.length > 0) {
                        last_payroll = response[0]['last_payroll'];
                        deduction = response[0]['total_deduction'];
                        for(var count = 0; count < response.length; count++) {
                            html += '<tr>';
                            html += '<td style="text-align: right;">'+response[count].ded_details+'</td>';
                            html += '</tr>';
                        }
                    } else {
                        html += '<tr><td colspan="6" class="text-center">Select Employee</td></tr>';
                    }
                    $('#post_data').html(html);
                    $('#last_payroll').val((Math.round(last_payroll * 100) / 100).toFixed(2));
                    $('#deduction').val((Math.round(deduction * 100) / 100).toFixed(2));
                }
            });
        }

        $('#employee_id').change(function(){
            load_data();
        });

        $('#add_issue_form').validate({
            rules: {
                employee_id: { required: true },
                doc_chargers: { required: true, number: true },
                resignation_date: { required: true, date: true }
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                $.ajax({
                    url: "/cal_process",
                    method: "POST",
                    data: $(form).serialize(),
                    beforeSend: function() {
                        $('#calculate_payroll').attr('disabled', 'disabled');
                        $('#process').css('display', 'block');
                    },
                    success: function(data) {
                        var percentage = 0;
                        var timer = setInterval(function() {
                            percentage += 20;
                            progress_bar_process(percentage, timer);
                        }, 1000);
                    }
                });
            }
        });

        function progress_bar_process(percentage, timer) {
            $('.progress-bar').css('width', percentage + '%');
            if (percentage > 100) {
                clearInterval(timer);
                $('#add_issue_form')[0].reset();
                $('#process').css('display', 'none');
                $('.progress-bar').css('width', '0%');
                $('#calculate_payroll').attr('disabled', false);
                $('#success_message').html('<div class="alert alert-dismissible alert-success bg-gradient-success text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Success.</div>');
                setTimeout(function() {
                    $('#success_message').html('');
                    location.reload();
                }, 5000);
            }
        }

        $('[data-toggle="tooltip"]').tooltip();
    });

    function getAmount(value) {
        var doc_chargers = parseFloat($('#doc_chargers').val()) || 0;
        var paid_amount = parseFloat($('#paid_amount').val()) || 0;
        var last_payroll = parseFloat($('#last_payroll').val()) || 0;
        var deduction = parseFloat($('#deduction').val()) || 0;
        var total_deduction = doc_chargers + deduction;
        var net = last_payroll - total_deduction + paid_amount;

        $('#total_deduction').val(total_deduction.toFixed(2));
        $('#net').val(net.toFixed(2));
    }
</script>