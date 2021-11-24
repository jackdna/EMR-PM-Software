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

ob_start();

$yes = ' (y)'; 	$no = ' (n)';	
$trHeight = "15";
$td1Width = "56";

if($finalize_flag == 1)
{
	$finalize = "&nbsp;&nbsp;&nbsp;&nbsp;(Finalized)";
}

$testCSS = '
	<style>
		table{ font-size:12px; font-weight:normal;}
		.tb_heading
		{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			font-weight:bold;
			color:#000000;
			background-color:#BCD5E1;
		}
		.text_b
		{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			font-weight:bold;
			color:#000;
			background-color:#BCD5E1;
		}
		.text
		{
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
					<td colspan="3" align="center" style="width:1080px" class="text_b"><b>VF TEST'.$finalize.'</b>
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
                    <td style="width:55%" ><strong>VF</strong>&nbsp;&nbsp;&nbsp;&nbsp;
                            <span class="drak_purple_color">OU</span>
                            <?php echo ($elem_vfEye == "OU") ? $yes : $no ;?>&nbsp;&nbsp;
                            <span class="blue_color">OD</span>
                            <?php echo ($elem_vfEye == "OD") ? "(yes)" : "" ;?>&nbsp;&nbsp;
                            <span class="green_color ">OS</span>
                            <?php echo ($elem_vfEye == "OS") ? "(yes)" : "" ;?>
                    </td>
                    <td style="width:45%" class="txt_11b alignCenter" >
                    <?php echo $elem_gla_mac_print; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="height:20px;"  class="alignLeft" >
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
           <strong>Diagnosis</strong>&nbsp;:&nbsp;<?php echo $elem_diagnosis;?>
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
                    <td style="width:500px;" class="" >
						<span class="blue_color" style="margin-left:200px;padding:0px;position:block;width:100px;height:20px;border:1px solid red;">OD</span>
						<?php echo $elem_tapeuntaped_od;?>
					</td>
                    <td style="width:60px;" rowspan="2" class="drak_purple_color alignCenter alignMiddle">BL</td>
                    <td style="width:500px;" class=""><span class="green_color" style="margin-left:200px;padding:0px;border:1px solid red;">OS</span>&nbsp;&nbsp;
					<?php echo $elem_tapeuntaped_os;?>
					</td>
                </tr>
                <tr>
                    <td class="alignLeft valignTop" style="border:1px solid #000000;" >
						<table cellpadding="0" cellspacing="0" style="width:100%;" >
							<tr>
								<td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"><strong>Normal</strong></td>
								<td><?php if($Normal_OD_T==1) echo $yes; else echo $no; ?></td>
								<td colspan="5" ><strong>Poor Study</strong><?php echo ($elem_normal_poorStudy_od == "1") ? $yes : $no; ?></td>
							</tr>
							<tr>
								<td colspan="2" class="valignTop alignLeft" style="height:<?php echo $trHeight;?>px;" ><strong>Border Line Defect</strong></td>
								<td>T<?php if($BorderLineDefect_OD_T==1) echo $yes; else echo $no; ?></td>
								<td>+1<?php if($BorderLineDefect_OD_1==1) echo $yes; else echo $no; ?></td>
								<td>+2<?php if($BorderLineDefect_OD_2==1) echo $yes; else echo $no; ?></td>
								<td>+3<?php if($BorderLineDefect_OD_3==1) echo $yes; else echo $no; ?></td>
								<td>+4<?php if($BorderLineDefect_OD_4==1) echo $yes; else echo $no; ?></td>
							</tr>
							<tr>
								<td class="txt_11b valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;" colspan="2"><strong>Abnormal</strong></td>
								<td>T<?php if($Abnormal_OD_T==1) echo $yes; else echo $no; ?></td>
								<td>+1<?php if($Abnormal_OD_1==1) echo $yes; else echo $no; ?></td>
								<td>+2<?php if($Abnormal_OD_2==1) echo $yes; else echo $no; ?></td>
								<td>+3<?php if($Abnormal_OD_3==1) echo $yes; else echo $no; ?></td>
								<td>+4<?php if($Abnormal_OD_4==1) echo $yes; else echo $no; ?></td>
							</tr>
							<tr class="alignMiddle">
								<td class="txt_11b alignLeft valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; width:110px;"><strong>Nasal Step</strong></td>
								<td style="width:50px;"><b>Superior</b><?php if($NasalSteep_OD_Superior==1) echo $yes; else echo $no; ?></td>
								<td class="alignLeft" style="width:<?php echo $td1Width;?>;px" >T<?php if($NasalSteep_OD_S_T==1) echo $yes; else echo $no; ?></td>
								<td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+1<?php if($NasalSteep_OD_S_1==1) echo $yes; else echo $no; ?></td>
								<td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+2<?php if($NasalSteep_OD_S_2==1) echo $yes; else echo $no; ?></td>
								<td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+3<?php if($NasalSteep_OD_S_3==1) echo $yes; else echo $no; ?></td>
								<td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+4<?php if($NasalSteep_OD_S_4==1) echo $yes; else echo $no; ?></td>
							</tr>    
							<tr class="valignTop">
								<td style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
								<td><b>Inferior</b><?php if($NasalSteep_OD_Inferior==1) echo $yes; else echo $no; ?></td>
								<td>T<?php if($NasalSteep_OD_I_T==1) echo $yes; else echo $no;?></td>
								<td>+1<?php if($NasalSteep_OD_I_1==1) echo $yes; else echo $no; ?></td>
								<td>+2<?php if($NasalSteep_OD_I_2==1) echo $yes; else echo $no; ?></td>
								<td>+3<?php if($NasalSteep_OD_I_3==1) echo $yes; else echo $no; ?></td>
								<td>+4<?php if($NasalSteep_OD_I_4==1) echo $yes; else echo $no; ?></td>
							</tr>
							<tr class="valignTop">
								<td  class="txt_11b alignLeft valignTop" style="padding-left:2px;height:<?php echo $trHeight;?>px;"><strong>Arcuate defect</strong></td>
								<td style="width:78px;"><b>Superior</b><?php if($Arcuatedefect_OD_Superior==1) echo $yes; else echo $no; ?></td>
								<td class="alignLeft">T<?php if($Arcuatedefect_OD_S_T==1) echo $yes; else echo $no; ?></td>
								<td>+1<?php if($Arcuatedefect_OD_S_1==1) echo $yes; else echo $no; ?></td>
								<td>+2<?php if($Arcuatedefect_OD_S_2==1) echo $yes; else echo $no;?></td>
								<td>+3<?php if($Arcuatedefect_OD_S_3==1) echo $yes; else echo $no; ?></td>
								<td>+4<?php if($Arcuatedefect_OD_S_4==1) echo $yes; else echo $no; ?></td>
							</tr>
							<tr class="valignTop" >
								<td style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
								<td><b>Inferior</b><?php if($Arcuatedefect_OD_Inferior==1) echo $yes; else echo $no; ?></td>
								<td>T<?php if($Arcuatedefect_OD_I_T==1) echo $yes; else echo $no; ?></td>
								<td>+1<?php if($Arcuatedefect_OD_I_1==1) echo $yes; else echo $no; ?></td>
								<td>+2<?php if($Arcuatedefect_OD_I_2==1) echo $yes; else echo $no; ?></td>
								<td>+3<?php if($Arcuatedefect_OD_I_3==1) echo $yes; else echo $no; ?></td>
								<td>+4<?php if($Arcuatedefect_OD_I_4==1) echo $yes; else echo $no; ?></td>
							</tr>
							<tr class="valignTop">
								<td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"><strong>Defect</strong></td>
								<td>Central<?php if($Defect_OD_Central==1) echo $yes; else echo $no; ?></td>
								<td>Superior<?php if($Defect_OD_Superior==1) echo $yes; else echo $no; ?></td>
								<td colspan="2">Inferior<?php if($Defect_OD_Inferior==1) echo $yes; else echo $no; ?></td>
								<td colspan="2">Scattered<?php if($Defect_OD_Scattered==1) echo $yes; else echo $no; ?></td>
							</tr>
							<tr class="valignTop">
								<td style="height:<?php echo $trHeight;?>px;"></td>
								<td></td>
								<td>T<?php if($Defect_OD_T==1) echo $yes; else echo $no; ?></td>
								<td>+1<?php if($Defect_OD_1==1) echo $yes; else echo $no; ?></td>
								<td>+2<?php if($Defect_OD_2==1) echo $yes; else echo $no; ?></td>
								<td>+3<?php if($Defect_OD_3==1) echo $yes; else echo $no; ?></td>
								<td>+4<?php if($Defect_OD_4==1) echo $yes; else echo $no; ?></td>
							 </tr>
							<tr>
								<td colspan="2" style="height:<?php echo $trHeight;?>px;">No Sig. Change<?php echo ($elem_noSigChange_OD == "1") ? $yes : $no; ?></td>
								<td colspan="2">Improved<?php echo ($elem_improved_OD == "1") ? $yes : $no; ?></td>
								<td colspan="3">Inc. Abn<?php echo ($elem_incAbn_OD == "1") ? $yes : $no; ?></td>
							</tr>
							<tr>
								<td  class="txt_11b alignLeft" style="height:<?php echo $trHeight;?>px;"><strong></strong></td>
								<td colspan="6"><?php echo $elem_targetIop_OD;?></td>
							</tr>
							<tr>
								<td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"><strong>Other</strong></td>
								<td colspan="6" style="width:200px;"><?php echo $Others_OD; ?></td>
							</tr>
						</table>
                    </td>
                    <td class="alignLeft valignTop" style="border:1px solid #000000;">
                        <table cellpadding="0" cellspacing="0"  >
                            <tr>
                                <td class="valignTop" style="height:<?php echo $trHeight;?>px;"><?php if($Normal_OS_T==1) echo $yes; else echo $no; ?></td>
                                <td colspan="5">Poor Study<?php echo ($elem_normal_poorStudy_os == "1") ? $yes : $no; ?></td>
                            </tr>
                            <tr>
                                <td style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                                <td>T<?php if($BorderLineDefect_OS_T==1) echo $yes; else echo $no; ?></td>
                                <td>+1<?php if($BorderLineDefect_OS_1==1) echo $yes; else echo $no; ?></td>
                                <td>+2<?php if($BorderLineDefect_OS_2==1) echo $yes; else echo $no; ?></td>
                                <td>+3<?php if($BorderLineDefect_OS_3==1) echo $yes; else echo $no; ?></td>
                                <td>+4<?php if($BorderLineDefect_OS_4==1) echo $yes; else echo $no; ?></td>
                            </tr>
                            <tr>
                                <td style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                                <td>T<?php if($Abnormal_OS_T==1) echo $yes; else echo $no; ?></td>
                                <td>+1<?php if($Abnormal_OS_1==1) echo $yes; else echo $no; ?></td>
                                <td>+2<?php if($Abnormal_OS_2==1) echo $yes; else echo $no; ?></td>
                                <td>+3<?php if($Abnormal_OS_3==1) echo $yes; else echo $no;?></td>
                                <td>+4<?php if($Abnormal_OS_4==1) echo $yes; else echo $no; ?></td>
                            </tr>
                            <tr class="alignMiddle">
                                <td style="height:<?php echo $trHeight;?>px; width:120px"><b>Superior</b><?php if($NasalSteep_OS_Superior==1) echo $yes; else echo $no; ?></td>
                                <td style="width:<?php echo $td1Width;?>px;">T<?php if($NasalSteep_OS_S_T==1) echo $yes; else echo $no; ?></td>
                                <td style="width:<?php echo $td1Width;?>px;">+1<?php if($NasalSteep_OS_S_1==1) echo $yes; else echo $no; ?></td>
                                <td style="width:<?php echo $td1Width;?>px;">+2<?php if($NasalSteep_OS_S_2==1) echo $yes; else echo $no; ?></td>
                                <td style="width:<?php echo $td1Width;?>px;">+3<?php if($NasalSteep_OS_S_3==1) echo $yes; else echo $no; ?></td>
                                <td style="width:<?php echo $td1Width;?>px;">+4<?php if($NasalSteep_OS_S_4==1) echo $yes; else echo $no; ?></td>
                            </tr>
                            <tr class="valignTop">
                                <td style="height:<?php echo $trHeight;?>px;"><b>Inferior</b><?php if($NasalSteep_OS_Inferior==1) echo $yes; else echo $no; ?></td>
                                <td>T<?php if($NasalSteep_OS_I_T==1)  echo $yes; else echo $no;?></td>
                                <td>+1<?php if($NasalSteep_OS_I_1==1)  echo $yes; else echo $no; ?></td>
                                <td>+2<?php if($NasalSteep_OS_I_2==1)  echo $yes; else echo $no; ?></td>
                                <td>+3<?php if($NasalSteep_OS_I_3==1)  echo $yes; else echo $no; ?></td>
                                <td>+4<?php if($NasalSteep_OS_I_4==1) echo $yes; else echo $no; ?></td>
                            </tr>
                            <tr class="valignTop" >
                                <td style="height:<?php echo $trHeight;?>px;"><b>Superior</b><?php if($Arcuatedefect_OS_Superior==1) echo $yes; else echo $no; ?> </td>
                                <td>T<?php if($Arcuatedefect_OS_S_T==1) echo $yes; else echo $no; ?></td>
                                <td>+1<?php if($Arcuatedefect_OS_S_1==1) echo $yes; else echo $no; ?></td>
                                <td>+2<?php if($Arcuatedefect_OS_S_2==1) echo $yes; else echo $no; ?></td>
                                <td>+3<?php if($Arcuatedefect_OS_S_3==1) echo $yes; else echo $no; ?></td>
                                <td>+4<?php if($Arcuatedefect_OS_S_4==1) echo $yes; else echo $no; ?></td>
                            </tr>
                            <tr class="valignTop">
                                <td style="height:<?php echo $trHeight;?>px;"><b>Inferior</b><?php if($Arcuatedefect_OS_Inferior==1) echo $yes; else echo $no; ?></td>
                                <td>T<?php if($Arcuatedefect_OS_I_T==1) echo $yes; else echo $no; ?></td>
                                <td>+1<?php if($Arcuatedefect_OS_I_1==1) echo $yes; else echo $no; ?></td>
                                <td>+2<?php if($Arcuatedefect_OS_I_2==1) echo $yes; else echo $no; ?></td>
                                <td>+3<?php if($Arcuatedefect_OS_I_3==1) echo $yes; else echo $no; ?></td>
                                <td>+4<?php if($Arcuatedefect_OS_I_4==1) echo $yes; else echo $no; ?></td>
                            </tr>
                            <tr class="valignTop">
                                <td style="height:<?php echo $trHeight;?>px;">Central<?php if($Defect_OS_Central==1) echo $yes; else echo $no; ?></td>
                                <td>Superior<?php if($Defect_OS_Superior==1) echo $yes; else echo $no; ?></td>
                                <td colspan="2">Inferior<?php if($Defect_OS_Inferior==1) echo $yes; else echo $no; ?></td>
                                <td colspan="2">Scattered<?php if($Defect_OS_Scattered==1) echo $yes; else echo $no; ?></td>
                            </tr>
                            <tr class="valignTop" >
                                <td style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                                <td>T<?php if($Defect_OS_T==1) echo $yes; else echo $no; ?></td>
                                <td>+1<?php if($Defect_OS_1==1) echo $yes; else echo $no; ?></td>
                                <td>+2<?php if($Defect_OS_2==1) echo $yes; else echo $no; ?></td>
                                <td>+3<?php if($Defect_OS_3==1) echo $yes; else echo $no; ?></td>
                                <td>+4<?php if($Defect_OS_4==1) echo $yes; else echo $no; ?></td>
                            </tr>
                            <tr > 
                                <td colspan="2" style="height:<?php echo $trHeight;?>px;">No Sig. Change<?php echo ($elem_noSigChange_OS == "1") ? $yes : $no; ?></td>
                                <td colspan="2">Improved<?php echo ($elem_improved_OS == "1") ? $yes : $no; ?></td>
                                <td colspan="2">Inc. Abn<?php echo ($elem_incAbn_OS == "1") ? $yes : $no; ?></td>
                            </tr>
                            <tr>
                                 <td colspan="6" style="height:<?php echo $trHeight;?>px;"><?php echo $elem_targetIop_OS;?></td>
                             </tr>
                            <tr>
                                <td colspan="6" style="height:<?php echo $trHeight;?>px; width:200px;"><?php echo $Others_OS; ?></td>
                            </tr>
						</table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr class="alignLeft alignMiddle">
        <td style="height:20px;" colspan="2" class="txt_11b">Treatment/Prognosis : -</td>
    </tr>
    <tr>
        <td colspan="2">
            <table class="table_collapse" >
                <tr>
                    <td style="width:150px; height:<?php echo $trHeight;?>;"><strong>Stable</strong><?php echo ($elem_stable == "1") ? $yes : $no;?></td>
                    <td class="alignLeft nowrap" style="width:150px; "><strong>Continue Meds</strong><?php echo ($elem_contiMeds == "1") ? $yes : $no;?></td>
                    <td class="alignLeft nowrap" style="width:150px"><strong>Monitor&nbsp;IOP</strong><?php echo ($elem_monitorIOP == "1") ? $yes : $no;?></td>
                    <td class="alignLeft nowrap" style="width:150px"><strong>Tech to inform Pt.</strong><?php echo ($elem_tech2InformPt == "1") ? $yes : $no;?></td>
                    <td class="alignLeft nowrap" style="width:150px"><strong>Pt&nbsp;informed&nbsp;of&nbsp;results</strong><?php echo ($elem_ptInformed == "1") ? $yes : $no;?></td>
                </tr>
                <tr>
                    <td style="height:<?php echo $trHeight;?>;"><strong>F/U&nbsp;APA</strong><?php echo ($elem_fuApa == "1") ? $yes : $no;?></td>
                    <td colspan="2"><strong>Informed Pt result next visit</strong><?php echo ($elem_informedPtNv == "1") ? $yes : $no;?> </td>
                    <td colspan="2"><strong>Repeat test 1 year</strong><?php echo ($elem_rptTst1yr == "1") ? $yes : $no;?></td>
                </tr>
           </table> 
        </td>
    </tr>       
	<tr>
		<td colspan="2" style="height:10px;" ></td>
   </tr>
	<tr>                                                                
		<td colspan="2" class="alignLeft" >
			Visual field in the <b class="blue_color">Right Eye</b> shows loss of the superior (<?php echo ($elem_loss_sup_degree_od)?$elem_loss_sup_degree_od:'n'; ?>) degrees. This improves by (<?php echo ($elem_improve_degree_od)?$elem_improve_degree_od:'n'; ?>) degrees when the lid is taped in the elevated position. This documents functional ptosis in the <b class="blue_color">Right Eye</b>.
		</td>
	</tr> 
	<tr>
		<td colspan="2" class="alignLeft" >
			Visual field in the <b class="green_color">Left Eye</b> shows loss of the superior (<?php echo ($elem_loss_sup_degree_os)?$elem_loss_sup_degree_os:'n'; ?>) degrees. This improves by (<?php echo ($elem_improve_degree_os)?$elem_improve_degree_os:'n'; ?>) degrees when the lid is taped in the elevated position. This documents functional ptosis in the <b class="green_color">Left Eye</b>.
		</td>
	</tr> 
	<tr>
		<td colspan="2" style="height:10px;"></td>
	</tr>	 
    <tr>
        <td class="alignLeft valignTop" colspan="2">
			<div style="width:900px"><strong>Comments</strong>&nbsp;:&nbsp;<?php echo $elem_comments; ?></div>
		</td>
    </tr>
    <tr>
        <td colspan="2" style="height:10px;" ></td>
    </tr>
    <tr>  
       <td  class="alignLeft" ><strong>Interpreted By:</strong> &nbsp;&nbsp;
            <?php
                if($elem_phyName)
				{
                    $getPersonnal3 = $objTests->getPersonnal3($elem_phyName);
                }
				else
				{
                    $getPersonnal3 = '';
                }
                
				echo $getPersonnal3
            ?>
        </td>
        <td><strong>Future Appointments:</strong>&nbsp;&nbsp;<?php $data = $objTests->getFutureApp($patient_id);echo $data;?></td>
    </tr>
    <?php echo $sign_path_print; ?>
</table>
</page>

<?php
$strPrint = $STRPRINT;

if($strPrint!='')
{
	echo $testHeadFoot;	
	echo $strPrint;
	echo "</page>";
}
 
$testPrint = $testCSS.$testHeadFoot;
$testPrint.= ob_get_contents();
//-------DATA INPUT INTO HTML FILE-------
file_put_contents($final_html_file_name_path.".html",$testPrint);

ob_end_clean(); 
?>