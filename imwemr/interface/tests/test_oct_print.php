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
File: test_oct_print.php
Purpose: This file provides OCT test's print version.
Access Type : Include file
*/
?>
<?php
ob_start();

$yes = ' (y)'; 	$no = ' (n)';	
$trHeight = "20";
$td1Width = "56";
$divWidth = "260";
$finalize = '';

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
						<td colspan="3" align="center" class="text_b"><b>OCT TEST'.$finalize.'</b>
						</td>
					</tr>
					<tr>
						<td class="text_b" align="left" style="width:350px">Ordered By '.$orderedBy.' on '.$elem_opidTestOrderedDate.'</td>
						<td style="width:450px;" class="text_b" align="center">Patient Name:&nbsp;&nbsp;'.$patientName.' - ('.$patient_id.')</td>
						<td class="text_b" align="right" style="width:280px">DOS:&nbsp;&nbsp;'.$elem_examDate.'</td>
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
                    <td style="width:55%" ><strong>OCT</strong>&nbsp;&nbsp;&nbsp;&nbsp;
                            Anterior&nbsp;Segment<?php echo ($elem_scanLaserOct == "1") ? $yes : $no ;?>&nbsp;&nbsp;
                            Optic&nbsp;Nerve<?php echo ($elem_scanLaserOct == "2") ? $yes : $no ;?>&nbsp;&nbsp;
                            Retina<?php echo ($elem_scanLaserOct == "3") ? $yes : $no ;?>&nbsp;&nbsp;
                            <span class="drak_purple_color">OU</span>
                            <?php echo ($elem_scanLaserEye == "OU") ? $yes : $no ;?>&nbsp;&nbsp;
                            <span class="blue_color">OD</span>
                            <?php echo ($elem_scanLaserEye == "OD") ? "(yes)" : "" ;?>&nbsp;&nbsp;
                            <span class="green_color ">OS</span>
                            <?php echo ($elem_scanLaserEye == "OS") ? "(yes)" : "" ;?>
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
            Good<?php echo ($elem_ptUndersatnding == '' || $elem_ptUnderstanding == "Good") ? " (yes)" : "" ;?>&nbsp;&nbsp;
            Fair<?php echo ($elem_ptUnderstanding == "Fair") ? " (yes)" : "" ;?>&nbsp;&nbsp;
            Poor<?php echo ($elem_ptUnderstanding == "Poor") ? " (yes)" : "" ;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <strong>Diagnosis</strong>&nbsp;:&nbsp;<?php if(trim(strtolower($elem_diagnosis))=="other"){ $elem_diagnosis=$elem_diagnosisOther;}echo $elem_diagnosisOther;?>
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
                                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"><strong>Normal</strong></td>
                                            <td><?php if($Normal_OD_T==1) echo $yes; else echo $no; ?></td>
                                            <td colspan="4" ><strong>Poor Study</strong><?php echo ($elem_normal_poorStudy_od == "1") ? $yes : $no; ?></td>
                                        </tr>
                                        <tr >
                                          <td class="valignTop alignLeft" style="height:<?php echo $trHeight;?>px; width:110px;" ><strong>Border Line Defect</strong></td>
                                          <td style="width:<?php echo $td1Width;?>px">T<?php if($BorderLineDefect_OD_T==1) echo $yes; else echo $no; ?></td>
                                          <td style="width:<?php echo $td1Width;?>px">+1<?php if($BorderLineDefect_OD_1==1) echo $yes; else echo $no; ?></td>
                                          <td style="width:<?php echo $td1Width;?>px">+2<?php if($BorderLineDefect_OD_2==1) echo $yes; else echo $no; ?></td>
                                          <td style="width:<?php echo $td1Width;?>px">+3<?php if($BorderLineDefect_OD_3==1) echo $yes; else echo $no; ?></td>
                                          <td style="width:<?php echo $td1Width;?>px">+4<?php if($BorderLineDefect_OD_4==1) echo $yes; else echo $no; ?></td>
                                        </tr>
                                        <tr>
                                          <td class="txt_11b valignTop" style="height:<?php echo $trHeight;?>px;"><strong>Abnormal</strong></td>
                                            <td>T<?php if($Abnorma_OD_T==1) echo $yes; else echo $no; ?></td>
                                            <td>+1<?php if($Abnorma_OD_1==1) echo $yes; else echo $no; ?></td>
                                            <td>+2<?php if($Abnorma_OD_2==1) echo $yes; else echo $no; ?></td>
                                            <td>+3<?php if($Abnorma_OD_3==1) echo $yes; else echo $no; ?></td>
                                            <td>+4<?php if($Abnorma_OD_4==1) echo $yes; else echo $no; ?></td>
                                        </tr>
                                        <tr class="alignMiddle">
                                          <td>&nbsp;</td>
                                          <td class="alignLeft" colspan="6">
                                          	<div style="height:<?php echo $trHeight;?>px; width:<?php echo $divWidth;?>px;">
                                            <strong>CME</strong><?php if(in_array("CME",$testRes_OD)) echo $yes; else echo $no; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <strong>Intra Retinal Fluid</strong><?php if(in_array("Intra Retinal Fluid",$testRes_OD)) echo $yes; else echo $no; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <strong>ERM</strong><?php if(in_array("ERM",$testRes_OD)) echo $yes; else echo $no; ?>
                                            </div>
                                            <div style="height:<?php echo $trHeight;?>px; width:<?php echo $divWidth;?>px;">
                                            <strong>Drusen</strong><?php if(in_array("Drusen",$testRes_OD)) echo $yes; else echo $no; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <strong>Vitreous Macula Traction</strong><?php if(in_array("Vitreous Macula Traction",$testRes_OD)) echo $yes; else echo $no; ?>
                                            </div>
                                            <div style="height:<?php echo $trHeight;?>px; width:<?php echo $divWidth;?>px;">
                                            <strong>Sub Retinal Fluid</strong><?php if(in_array("Sub Retinal Fluid",$testRes_OD)) echo $yes; else echo $no; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <strong>Sub RPE Fovial</strong><?php if(in_array("Sub RPE Fovial",$testRes_OD)||in_array("Sub RPE Foveal",$testRes_OD)) echo $yes; else echo $no; ?>
                                            </div>
                                            <div style="height:<?php echo $trHeight;?>px; width:<?php echo $divWidth;?>px;">
                                            <strong>CNV</strong><?php if(in_array("CNV",$testRes_OD)) echo $yes; else echo $no; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <strong>RPE Detach</strong><?php if(in_array("RPE Detach",$testRes_OD)) echo $yes; else echo $no; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <strong>RPE Rip</strong><?php if(in_array("RPE Rip",$testRes_OD)) echo $yes; else echo $no; ?>
                                            </div>
                                          </td>
                                        </tr>    
                                        <tr>
                                            <td colspan="2" style="height:<?php echo $trHeight;?>px;"><strong>No Sig. Change</strong><?php echo ($elem_noSigChange_OD == "1") ? $yes : $no; ?></td>
                                            <td colspan="2"><strong>Improved</strong><?php echo ($elem_improved_OD == "1") ? $yes : $no; ?></td>
                                            <td colspan="2"><strong>Inc. Abn</strong><?php echo ($elem_incAbn_OD == "1") ? $yes : $no; ?></td>
                                        </tr>

                                        <tr>
                                          <td style="height:<?php echo $trHeight;?>px;"><strong>Foveal Thickness</strong></td>
                                          <td colspan="5"><?php echo $elem_foveaThick_OD; ?></td>
                                        </tr>
                                        <tr>
                                          <td style="height:<?php echo $trHeight;?>px;"><strong>AVG NFL Thickness</strong></td>
                                          <td colspan="5"><?php echo $elem_avg_nfl_Thick_OD; ?></td>
                                        </tr>

                                        <tr>
                                            <td  class="txt_11b alignLeft" style="height:<?php echo $trHeight;?>px;"><strong>IOP Target</strong></td>
                                            <td colspan="5"><?php echo $elem_targetIop_OD;?></td>
                                        </tr>
                                        <tr>
                                            <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"><strong>Other</strong></td>
                                            <td colspan="5"><div style="width:260px"><?php echo $Others_OD; ?></div></td>
                                        </tr>
                      </table>
                    </td>
                    <td class="alignLeft valignTop" style="border:1px solid #000000;">
                        <table cellpadding="0" cellspacing="0"  >
                            <tr>
                                <td class="valignTop" style="height:<?php echo $trHeight;?>px;"><?php if($Normal_OS_T==1) echo $yes; else echo $no; ?></td>
                                <td colspan="4">Poor Study<?php echo ($elem_normal_poorStudy_os == "1") ? $yes : $no; ?></td>
                            </tr>
                            <tr>
                                <td style="height:<?php echo $trHeight;?>px; width:<?php echo $td1Width;?>;px">T<?php if($BorderLineDefect_OS_T==1) echo $yes; else echo $no; ?></td>
                                <td style="width:<?php echo $td1Width;?>px">+1<?php if($BorderLineDefect_OS_1==1) echo $yes; else echo $no; ?></td>
                                <td style="width:<?php echo $td1Width;?>px">+2<?php if($BorderLineDefect_OS_2==1) echo $yes; else echo $no; ?></td>
                                <td style="width:<?php echo $td1Width;?>px">+3<?php if($BorderLineDefect_OS_3==1) echo $yes; else echo $no; ?></td>
                                <td style="width:<?php echo $td1Width;?>px">+4<?php if($BorderLineDefect_OS_4==1) echo $yes; else echo $no; ?></td>
                            </tr>
                            <tr>
                                <td style="height:<?php echo $trHeight;?>px;">T<?php if($Abnorma_OS_T==1) echo $yes; else echo $no; ?></td>
                                <td>+1<?php if($Abnorma_OS_1==1) echo $yes; else echo $no; ?></td>
                                <td>+2<?php if($Abnorma_OS_2==1) echo $yes; else echo $no; ?></td>
                                <td>+3<?php if($Abnorma_OS_3==1) echo $yes; else echo $no;?></td>
                                <td>+4<?php if($Abnorma_OS_4==1) echo $yes; else echo $no; ?></td>
                            </tr>
                            <tr class="alignMiddle">
                              <td class="alignLeft" colspan="5">
                                <div style="height:<?php echo $trHeight;?>px; width:<?php echo $divWidth;?>px;">
                                <strong>CME</strong><?php if(in_array("CME",$testRes_OS)) echo $yes; else echo $no; ?>&nbsp;&nbsp;
                                <strong>Intra Retinal Fluid</strong><?php if(in_array("Intra Retinal Fluid",$testRes_OD)) echo $yes; else echo $no; ?>&nbsp;&nbsp;
                                <strong>ERM</strong><?php if(in_array("ERM",$testRes_OS)) echo $yes; else echo $no; ?>
                                </div>
                                <div style="height:<?php echo $trHeight;?>px; width:<?php echo $divWidth;?>px;">
                                <strong>Drusen</strong><?php if(in_array("Drusen",$testRes_OS)) echo $yes; else echo $no; ?>&nbsp;&nbsp;
                                <strong>Vitreous Macula Traction</strong><?php if(in_array("Vitreous Macula Traction",$testRes_OS)) echo $yes; else echo $no; ?>
                                </div>
								<div style="height:<?php echo $trHeight;?>px; width:<?php echo $divWidth;?>px;">
                                <strong>Sub Retinal Fluid</strong><?php if(in_array("Sub Retinal Fluid",$testRes_OS)) echo $yes; else echo $no; ?>&nbsp;&nbsp;
                                <strong>Sub RPE Fovial</strong><?php if(in_array("Sub RPE Fovial",$testRes_OS)||in_array("Sub RPE Foveal",$testRes_OS)) echo $yes; else echo $no; ?>&nbsp;&nbsp;
                                </div>
                                <div style="height:<?php echo $trHeight;?>px; width:<?php echo $divWidth;?>px;">
                                <strong>CNV</strong><?php if(in_array("CNV",$testRes_OS)) echo $yes; else echo $no; ?>&nbsp;&nbsp;
                                <strong>RPE Detach</strong><?php if(in_array("RPE Detach",$testRes_OS)) echo $yes; else echo $no; ?>&nbsp;&nbsp;
                                <strong>RPE Rip</strong><?php if(in_array("RPE Rip",$testRes_OS)) echo $yes; else echo $no; ?>
                                </div>
                              </td>
                            </tr>    
                            <tr > 
                                <td colspan="2" style="height:<?php echo $trHeight;?>px;"><strong>No Sig. Change</strong><?php echo ($elem_noSigChange_OS == "1") ? $yes : $no; ?></td>
                                <td><strong>Improved</strong><?php echo ($elem_improved_OS == "1") ? $yes : $no; ?></td>
                                <td colspan="2"><strong>Inc. Abn</strong><?php echo ($elem_incAbn_OS == "1") ? $yes : $no; ?></td>
                            </tr>

                            <tr class="alignMiddle">
                                <td class="alignLeft" colspan="5" style="height:<?php echo $trHeight;?>px;"><?php $elem_foveaThick_OS; ?></td>
                            </tr>    

                            <tr>
                                 <td colspan="5" style="height:<?php echo $trHeight;?>px;"><?php echo $elem_avg_nfl_Thick_OS;?></td>
                          </tr>
                            <tr>
                                 <td colspan="5" style="height:<?php echo $trHeight;?>px;"><?php echo $elem_targetIop_OS;?></td>
                          </tr>
                            <tr>
                                <td colspan="5" style="height:<?php echo $trHeight;?>px;"><div style="width:260px"><?php echo $Others_OS; ?></div></td>
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
		 <td class="alignLeft nowrap" style="width:150px"><strong>Monitor&nbsp;findings</strong><?php echo ($elem_monitor_finding == "1") ? $yes : $no;?></td>   
                    <td class="alignLeft nowrap" style="width:auto"><strong>Tech to inform Pt.</strong><?php echo ($elem_tech2InformPt == "1") ? $yes : $no;?></td>                    
                </tr>
                <tr>
		 <td class="alignLeft nowrap" colspan="2"><strong>Pt&nbsp;informed&nbsp;of&nbsp;results</strong><?php echo ($elem_ptInformed == "1") ? $yes : $no;?></td>
                    <td style="height:<?php echo $trHeight;?>;"><strong>F/U&nbsp;APA</strong><?php echo ($elem_fuApa == "1") ? $yes : $no;?></td>
                    <td colspan="2"><strong>Informed Pt result next visit</strong><?php echo ($elem_informedPtNv == "1") ? $yes : $no;?> </td>
                </tr>
           </table> 
        </td>
    </tr>       
    <tr>
        <td class="alignLeft valignTop" colspan="2"><div style="width:900px"><strong>Comments</strong>&nbsp;:&nbsp;<?php echo $elem_comments; ?></div></td>
    </tr>
          
    <tr>
        <td colspan="2" style="height:10px;" ></td>
    </tr>
    <tr>  
       <td  class="alignLeft" ><strong>Interpreted By:</strong> &nbsp;&nbsp;
            <?php
                if($elem_phyName){
                    $getPersonnal3 = $objTests->getPersonnal3($elem_phyName);
                }else{
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
if($strPrint!='') {
echo $testHeadFoot;
echo $strPrint;
echo "</page>";
}
 
$testPrint = $testCSS.$testHeadFoot;
$testPrint.= ob_get_contents();
file_put_contents($final_html_file_name_path.".html",$testPrint);

ob_end_clean(); 
?>