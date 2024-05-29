<?php
    include 'config.php';
    $connect = pdoConnection(); 
    $payroll_month = $_POST['payroll_month'];
    $month = date('Y F', strtotime($payroll_month));
   /* $payroll_id = $_POST['payroll_id'];*/
   /* $range = $_POST['payroll_id'];
    $ex = explode(' - ', $range);
    $from = date('Y-m-d', strtotime($ex[0]));
    $to = date('Y-m-d', strtotime($ex[1]));

    $sql = "SELECT *, SUM(amount) as total_amount FROM deductions";
    $query = $conn->query($sql);
    $drow = $query->fetch_assoc();
    $deduction = $drow['total_amount'];

    $from_title = date('M d, Y', strtotime($ex[0]));
    $to_title = date('M d, Y', strtotime($ex[1]));*/    

    require_once('../plugins/tcpdf/tcpdf.php'); 
    header('Content-type: text/html; charset=UTF-8') ;//chrome
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);  
    $pdf->SetTitle('Payslip: '.$month);  
    /*$pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  */
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
    //$pdf->SetDefaultMonospacedFont('helvetica');  
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
    $pdf->SetMargins(3, 3, 3);  
    $pdf->setPrintHeader(false);  
    $pdf->setPrintFooter(false);  
    $pdf->SetAutoPageBreak(TRUE, 10);  
    $pdf->setFontSubsetting(true);

    // convert TTF font to TCPDF format and store it on the fonts folder
    /*$fontname = TCPDF_FONTS::addTTFfont('../plugins/tcpdf/fonts/IskoolaPotaRegular.ttf', 'TrueTypeUnicode', '', 96);*/
    // use the font
    /*$pdf->SetFont($fontname, '', 10, '', false);*/

    $pdf->SetFont('freeserif', '', 10);
    $pdf->AddPage('P', 'A4'); 
    $pdf->resetColumns();
    $pdf->setEqualColumns(2, 98);  // KEY PART -  number of cols and width
    $pdf->selectColumn();
    

    $contents = '';

    $query = "SELECT * FROM payroll_items WHERE ";

    if(isset($_POST['payslip']))
    {
        $query .= "payroll_id = '".$_POST['payroll_id']."' AND status=1";
    }

    if(isset($_POST['view_payslip']))
    {
        $query .= "id = '2'";
    }

    $statement = $connect->prepare($query);
    $statement->execute();
    $total_data = $statement->rowCount();
    $result = $statement->fetchAll();
    foreach($result as $row)
    {
        $query = 'SELECT name_with_initial FROM employee WHERE employee_id="'.$row['employee_id'].'"';
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

        $query = 'SELECT * FROM bank_details WHERE employee_id="'.$row['employee_id'].'" ORDER BY id DESC LIMIT 1';
        $statement = $connect->prepare($query);
        $statement->execute();
        $total_data = $statement->rowCount();
        $result = $statement->fetchAll();
        if ($total_data > 0) {
            foreach($result as $bank_details)
            { 
                $bank_account=$bank_details['account_no'];
                $bank_name=$bank_details['bank_name'];
            }

        }else{
            $bank_account='';
            $bank_name='';
        }
       
        $contents .= '        
                        
            <table nobr="true" border="1"> 
                <tr>
                    <td style="padding-left:5px; line-height: 15px; overflow: hidden">
                        
                        <table>
                            <tr>
                            <td colspan=3>
                            <h4 align="center">ACE FRONT LINE SECURITY SOLUTIONS (PVT) LTD</h4>
                        <h5 align="center">No:150/20, First Lane, Kumbukgahaduwa, Perliment Road, Pitakotte</h5>
                        <h4 align="center">වැටුප් පතය</h4>
                            </td>
                            </tr>
                            <tr>  
                                <td width="30%">කාල වකවානුව</td>
                                <td width="2%" align="center">:</td>
                                <td width="68%"><b>'.$month.'</b></td>
                            </tr>
                            <tr>
                                <td width="30%">සාමාජික අංකය</td>
                                <td width="2%" align="center">:</td>
                                <td width="68%"><b>'.$row['employee_no'].'</b></td>
                            </tr>
                            <tr>
                                <td width="30%">නම</td>
                                <td width="2%" align="center">:</td>
                                <td width="68%"><b>'.$employee_name['name_with_initial'].'</b></td>                               
                            </tr>
                            <tr>
                            <td width="30%">නිලය</td>
                            <td width="2%" align="center">:</td>
                                <td width="68%"><b>'.$position_name['position_abbreviation'].'</b></td>
                            </tr>
                            <tr>
                            <td width="30%">ගිණුම් අංකය</td>
                            <td width="2%" align="center">:</td>
                                <td width="68%"><b>'.$bank_account.'</b></td>
                            </tr>
                            <tr>
                            <td width="30%">බැංකුව</td>
                            <td width="2%" align="center">:</td>
                                <td width="68%"><b>'.$bank_name.'</b></td>
                            </tr>
                            <tr>
                            <td width="30%">මුලු වැඩ මුර</td>
                            <td width="2%" align="center">:</td>
                                <td width="68%"><b>'.$row['no_of_shift'].'</b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding-left:5px; line-height: 15px; overflow: hidden"><b>ඉපැයීම්</b></td>
                </tr>
                <tr>
                    <td style="padding-left:5px; height: 120px; overflow: hidden">
                        <table>  
                            <tr> 
                                <td width="70%">මුලික වැටුප</td>
                                <td width="2%" align="center">:</td>
                                <td width="28%" align="right">'.number_format($row['basic_salary'], 2).'</td> 
                            </tr>
                            <tr>
                                <td width="70%">අතිකාල දිමනාව (පැය:'.$row['ot_hrs'].')</td>
                                <td width="2%" align="center">:</td>
                                <td width="28%" align="right">'.number_format($row['ot_amount'], 2).'</td> 
                            </tr>
                            <tr> 
                                <td width="70%">දිරි දීමනා</td>
                                <td width="2%" align="center">:</td>
                                <td width="28%" align="right">'.number_format($row['incentive'], 2).'</td> 
                            </tr>';

                $query = 'SELECT * FROM allowances';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();
                $result = $statement->fetchAll();
                foreach($result as $rows):
                    $all_arr[$rows['allowances_id']] = $rows['allowances_si'];
                endforeach;
                
                foreach(json_decode($row['allowances']) as $k => $val):

                $contents .='<tr> 
                                <td width="70%">'.$all_arr[$val->aid].'</td>
                                <td width="2%" align="center">:</td>
                                <td width="28%" align="right">'.number_format($val->amount, 2).'</td> 
                            </tr>';

                endforeach;

        $contents .='</table>
                    </td>
                </tr>

                <tr>
                    <td style="padding-left:5px; padding-right:5px; line-height: 20px; overflow: hidden">
                        <table>
                            <tr> 
                                <td width="70%"><b>දළ වැටුප</b></td>
                                <td width="2%" align="center">:</td>
                                <td width="28%" align="right"><span style="border-bottom: 1px solid; text-decoration: underline;"><b>'.number_format($row['gross'], 2).'</b></span></td> 
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding-left:5px; line-height: 15px; overflow: hidden"><b>අඩුකිරීම්</b></td>
                </tr>
                <tr>
                    <td style="padding-left:5px; height: 120px; overflow: hidden">
                        <table>  
                            <tr> 
                                <td width="70%">සේ: අර්ථසාධක අරමුදල 8%</td>
                                <td width="2%" align="center">:</td>
                                <td width="28%" align="right"><b>'.number_format($row['employee_epf'], 2).'</b></td> 
                            </tr>';
                            if ($row['absent_day'] != 0):
                                
                    $contents .= '<tr>
                                <td width="70%">වැටුප් රහිත දින සඳහා අඩු කිරීම් (දින:'.$row['absent_day'].')</td>
                                <td width="2%" align="center">:</td>
                                <td width="28%" align="right">'.number_format($row['absent_amount'], 2).'</td> 
                            </tr>';

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

                $contents .='<tr> 
                                <td width="70%">'.$all_arr[$val->did].'</td>
                                <td width="2%" align="center">:</td>
                                <td width="28%" align="right">'.number_format($val->amount, 2).'</td> 
                            </tr>';

                endforeach;
               
               $contents .='</table>
                    </td>
                </tr>
                <tr>
                    <td style="padding-left:5px; line-height: 20px; overflow: hidden">
                        <table>  
                            <tr> 
                                <td width="60%"><b>අඩුකිරීම් වල එකතුව</b></td>
                                <td width="2%" align="center">:</td>
                                <td width="38%" align="right"><span style="border-bottom: 3px solid; text-decoration: underline;"><b>'.number_format($row['deduction_amount'], 2).'</b></span></td> 
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding-left:10px; line-height: 15px; overflow: hidden">
                        <table>  
                            <tr> 
                                <td width="60%"><b>ශුද්ධ වැටුප</b></td>
                                <td width="2%" align="center">:</td>
                                <td width="38%" align="right"><span style="border-bottom: 1px solid; text-decoration: underline;"><b>'.number_format($row['deduction_amount'], 2).'</b></span></td> 
                            </tr>
                            <tr> 
                                <td width="60%">සේ.අ. අරමුදල 12%</td>
                                <td width="2%" align="center">:</td>
                                <td width="38%" align="right"><b>'.number_format($row['employer_epf'], 2).'</b></td> 
                            </tr>
                            <tr> 
                                <td width="60%">සේ.නි.භාරකාර අරමුදල 3%</td>
                                <td width="2%" align="center">:</td>
                                <td width="38%" align="right"><b>'.number_format($row['employer_etf'], 2).'</b></td> 
                            </tr>
                        </table>
                    </td>
                </tr>
                                
                
            </table>         
                

            <table>
                <tr>
                    <td style="padding-right:10px; height: 220px; overflow: hidden">
                        <table>';

                        $query = 'SELECT * FROM department';

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

                $contents .='<tr> 
                                <td width="70%">'.$all_arr[$val->d_id].' - '.$all_arr2[$val->p_id].'</td>
                                <td width="2%" align="center">:</td>
                                <td width="28%" align="right">'.$val->t_shifts.'</td> 
                            </tr>';

                endforeach;

                   $contents .=      '</table>
                        
                    </td>
                </tr>
                <tr>
                    <td style="padding-right:10px; line-height: 10px; overflow: hidden">
                        <b>කේ ඒ ගිහාන් සංජීව</b><br>
                        සහතික කරන නිලධාරියාගේ අත්සන
                    </td>
                </tr>
            </table>
            
            <br>
        ';
    }
    $pdf->writeHTML($contents, true, false, true, false); 
    $pdf->resetColumns();
    $pdf->Output('payslip.pdf', 'I');

?>