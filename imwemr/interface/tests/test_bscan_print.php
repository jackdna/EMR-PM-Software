<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
/*
File: test_bscan_print.php
Purpose: This file provides Print version of B-Scan test.
Access Type : Include file
*/
?>
<?php
ob_start();

$yes = ' (y)'; 	$no = ' (n)';	
$trHeight = "15";
$td1Width = "56";

if($finalize_flag == 1){
	$finalize = "&nbsp;&nbsp;&nbsp;&nbsp;(Finalized)";
}

	$testCSS = '
		<style>
			table{ font-size:12px; font-weight:normal;}
			.tb_heading{
				font-size:12px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#000000;
				background-color:#BCD5E1;
			}
			.text_b{
				font-size:12px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#000;
				background-color:#BCD5E1;
			}
			.text{
				font-size:12px;
				font-family:Arial, Helvetica, sans-serif;
				background-color:#FFFFFF;
			}
			.txt_11b { font-size:12px; font-weight:bold; }
			.alignLeft{ text-align:left; }
			.alignCenter{ text-align:center; }
			.alignRight{ text-align:right; }
			.valignTop { vertical-align:top; }
			.alignMiddle { vertical-align:middle; }
			.table_collapse { padding:0px; border-collapse:collapse; margin:0px; }
			.drak_purple_color {  color: #990099; font-weight:bold; }
			.blue_color { color: #0000FE; font-weight:bold;}
			.green_color { color: #008000; font-weight:bold;}
		</style>'; 

		$testHeadFoot='
		<page backtop="9mm" backbottom="0.5mm">		
		<page_footer>
			<table style="width:100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>	
		<page_header>
			<table border="0" cellpadding="0" cellspacing="0">				
					<tr>
						<td colspan="3" align="center" style="width:1080px" class="text_b"><b>B-Scan TEST'.$finalize.'</b>
						</td>
					</tr>
					<tr>
						<td class="text_b" align="left" style="width:350px">Ordered By '.$orderedBy.' on '.$elem_opidTestOrderedDate.'</td>
						<td style="width:450px;" class="text_b" align="center">Patient Name:&nbsp;&nbsp;'.$patientName.' - ('.$patient_id.')</td>
						<td class="text_b" align="right" style="width:280px">DOS:&nbsp;&nbsp;'.FormatDate_show($elem_examDate).'</td>
					</tr>
			</table>
		</page_header>
		';

?>

<table class="alignLeft" style="width:1080px;" >
    <tr class="alignLeft alignMiddle">
        <td colspan="2" class="txt_11b alignLeft">
            <table style="width:100%" >
                <tr style="height:20px">
                    <td style="width:55%" >
                            <strong>B-Scan</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span class="drak_purple_color">OU</span>
                            <?php echo ($elem_bscanMeterEye == "OU") ? $yes : $no ;?>&nbsp;&nbsp;
                            <span class="blue_color">OD</span>
                            <?php echo ($elem_bscanMeterEye == "OD") ? $yes : $no ;?>&nbsp;&nbsp;
                            <span class="green_color ">OS</span>
                            <?php echo ($elem_bscanMeterEye == "OS") ? $yes : $no ;?>
                    </td>
                </tr>
                <tr>
                    <td style="height:20px;"  class="alignLeft" >
                      <div style="width:900px;"><strong>Technician Comments</strong>&nbsp;:&nbsp;<?php echo $elem_techComments; ?></div>
                    </td>
				</tr>                	
            </table>
        </td>
    </tr>
    <tr class="alignLeft alignMiddle">
        <td style="height:25px" colspan="2" >
            <strong>Performed By</strong>&nbsp;:&nbsp;<?php echo $objTests->getPersonnal3($elem_performedBy);?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <strong>Patient Understanding & Cooperation</strong> &nbsp;:&nbsp;
            Good<?php echo ($elem_ptUnderstanding == '' || $elem_ptUnderstanding == "Good") ? " (yes)" : "" ;?>&nbsp;&nbsp;
            Fair<?php echo ($elem_ptUnderstanding == "Fair") ? " (yes)" : "" ;?>&nbsp;&nbsp;
            Poor<?php echo ($elem_ptUnderstanding == "Poor") ? " (yes)" : "" ;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <strong>Diagnosis</strong>&nbsp;:&nbsp;<?php 
			 if($elem_diagnosis=='Other') { echo $elem_diagnosisOther; }else { echo $elem_diagnosis;} ?>
        </td>
    </tr>
    <tr class="alignLeft alignMiddle" >
        <td style="height:20px;" colspan="2"><strong>Physician Interpretation</strong>: -</td>
    </tr>
    <tr class="alignLeft alignMiddle">
        <td style="height:20px; width:55%" >
            <strong>Reliability</strong> &nbsp;:&nbsp;
            <span class="blue_color txt_11b">OD</span>&nbsp;&nbsp;
            Good<?php echo (!$elem_reliabilityOd || $elem_reliabilityOd == "Good") ? " (yes)" : "";?>&nbsp;&nbsp;
            Fair<?php echo ($elem_reliabilityOd == "Fair") ? " (yes)" : "";?>&nbsp;&nbsp;
            Poor<?php echo ($elem_reliabilityOd == "Poor") ? " (yes)" : "";?>&nbsp;&nbsp;
        </td>
        <td style="width:45%">
            <span class="green_color ">OS</span>&nbsp;&nbsp;
            Good<?php echo (!$elem_reliabilityOs || $elem_reliabilityOs == "Good") ? " (yes)" : "";?>&nbsp;&nbsp;
            Fair<?php echo ($elem_reliabilityOs == "Fair") ? " (yes)" : "";?>&nbsp;&nbsp;
            Poor<?php echo ($elem_reliabilityOs == "Poor") ? " (yes)" : "";?>&nbsp;&nbsp;
        </td>
    </tr>
    <tr class="alignLeft alignMiddle" >
        <td style="height:20px;" colspan="2" ><strong>Test Results</strong>: -</td>
    </tr>
    <tr class="alignLeft alignMiddle">
        <td style="height:20px;" colspan="2" class="txt_11b alignLeft">
            <table cellpadding="0" cellspacing="0" >
                <tr>
                    <td style="width:500px;" class="alignCenter blue_color" >OD</td>
                    <td style="width:60px;" rowspan="2" class="drak_purple_color alignCenter alignMiddle">BL</td>
                    <td style="width:500px;" class="alignCenter green_color">OS</td>
                </tr>
                <tr>
                    <td class="alignLeft valignTop" style="border:1px solid #000000;" >
                         <table cellpadding="0" cellspacing="0" style="width:100%;" >
                                        <tr >
                                            <td class="valignTop" style="height:<?php echo $trHeight;?>px;">Normal&nbsp;</td>
                                            <td><?php if(in_array("Normal Scan", $arrTmpOD)){ echo $yes; } else { echo $no; } ?></td>
                                        </tr>
                                        <tr >
                                            <td class="valignTop" style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                                            <td>No evidence of retinal detachment<?php if(in_array("No evidence of retinal detachment", $arrTmpOD)){ echo $yes; } else { echo $no; } ?></td>
                                        </tr>
                                        <tr >
                                            <td class="valignTop" style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                                            <td>Retinal Detachment<?php if(in_array("Retinal Detachment", $arrTmpOD)){ echo $yes; } else { echo $no; } ?></td>
                                        </tr>
                                        <tr >
                                            <td class="valignTop" style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                                            <td>Vitreous Hemorrhage<?php if(in_array("Vitreous Hemorrhage", $arrTmpOD)){ echo $yes; } else { echo $no; } ?></td>
                                        </tr>
                                        <tr >
                                            <td class="valignTop" style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                                            <td>Retinal Tumor/Mass<?php if(in_array("Retinal Tumor/Mass", $arrTmpOD)){ echo $yes; } else { echo $no; } ?></td>
                                        </tr>
                                        <tr >
                                            <td class="valignTop" style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                                            <td>Optic nerve drusen/Mass<?php if(in_array("Optic nerve drusen/Mass", $arrTmpOD)){ echo $yes; } else { echo $no; } ?></td>
                                        </tr>
                                        <tr >
                                            <td class="valignTop" style="height:<?php echo $trHeight;?>px;"><strong>Comments</strong></td>
                                            <td><div style="width:400px"><?php echo $elem_descOd; ?></div></td>
                                        </tr>

                                    </table>
            </td>
                    <td class="alignLeft valignTop" style="border:1px solid #000000;"><table cellpadding="0" cellspacing="0" style="width:100%;" >
                      <tr >
                        <td class="valignTop" style="height:<?php echo $trHeight;?>px; width:5px;">&nbsp;</td>
                        <td><?php if(in_array("Normal Scan", $arrTmpOS)){ echo $yes; } else { echo $no; } ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                        <td>No evidence of retinal detachment
                          <?php if(in_array("No evidence of retinal detachment", $arrTmpOS)){ echo $yes; } else { echo $no; } ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                        <td>Retinal Detachment
                          <?php if(in_array("Retinal Detachment", $arrTmpOS)){ echo $yes; } else { echo $no; } ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                        <td>Vitreous Hemorrhage
                          <?php if(in_array("Vitreous Hemorrhage", $arrTmpOS)){ echo $yes; } else { echo $no; } ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                        <td>Retinal Tumor/Mass
                          <?php if(in_array("Retinal Tumor/Mass", $arrTmpOS)){ echo $yes; } else { echo $no; } ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                        <td>Optic nerve drusen/Mass
                          <?php if(in_array("Optic nerve drusen/Mass", $arrTmpOS)){ echo $yes; } else { echo $no; } ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                        <td><div style="width:400px"><?php echo $elem_descOd; ?></div></td>
                      </tr>
                    </table></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr class="alignLeft alignMiddle">
        <td style="height:20px;" colspan="2" class="txt_11b">Treatment/Prognosis : -</td>
    </tr>
    <tr>
        <td colspan="2"><table class="table_collapse" >
          <tr>
            <td width="150" class="alignLeft nowrap" style="width:150px; "><strong>Stable</strong><?php echo ($elem_stable == "1") ? $yes : $no;?></td>
            <td width="150" class="alignLeft nowrap" style="width:150px; "><strong>Continue Meds</strong><?php echo ($elem_contiMeds == "1") ? $yes : $no;?></td>
            <td width="150" class="alignLeft nowrap" style="width:150px; "><strong>F/U APA</strong> <?php echo ($elem_fuApa == "1") ? $yes : $no;?></td>
            <td width="152" class="alignLeft nowrap" style="width:150px"><strong>Tech to Inform Pt.</strong><?php echo ($elem_tech2InformPt == "1") ? $yes : $no;?></td>
            <td width="148" class="alignLeft nowrap" style="width:150px"><strong>Pt&nbsp;informed&nbsp;of&nbsp;results</strong><?php echo ($elem_ptInformed == "1") ? $yes : $no;?></td>
          </tr>
          <tr>
            <td colspan="3" class="alignLeft nowrap" style="width:150px; "><?php echo $elem_treat;?></td>
            <td colspan="2" class="alignLeft nowrap" style="width:150px; "><strong>Informed Pt result next visit</strong><?php echo ($elem_informedPtNv == "1") ? $yes : $no;?></td>
          </tr>
        </table></td>
    </tr>       
    <tr><td colspan="2" style="height:10px"></td></tr>
    <tr>  
       <td  class="alignLeft" ><strong>Interpreted By:</strong> &nbsp;&nbsp;
            <?php
                if($elem_physician){
                    $getPersonnal3 = $objTests->getPersonnal3($elem_physician);
                }else{
                    $getPersonnal3 = '';
                }
                echo $getPersonnal3;
            ?>
        </td>
        <td><strong>Future Appointments:</strong>&nbsp;&nbsp;<?php $data = $objTests->getFutureApp($patient_id);echo $data;?></td>
    </tr>
	<?php echo $sign_path_print; ?>
</table>
</page>

<?php
$strPrint = $STRPRINT;
if($strPrint!='') {
echo $testHeadFoot;
echo $strPrint;
echo "</page>";
} 

$testPrint = $testCSS.$testHeadFoot;
$testPrint.= ob_get_contents();
file_put_contents($final_html_file_name_path.'.html',$testPrint);

ob_end_clean(); 
?>