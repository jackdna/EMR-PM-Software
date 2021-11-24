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
File: test_disc_print.php
Purpose: This is print version of Fundus test.
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
						<td colspan="3" align="center" style="width:1080px" class="text_b"><b>Fundus TEST'.$finalize.'</b>
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
                            <strong>Disc&nbsp;Photos</strong><?php echo ($elem_fundusDiscPhoto == "1") ? $yes : $no ;?>&nbsp;&nbsp;
                            <strong>Macula&nbsp;Photos</strong><?php echo ($elem_fundusDiscPhoto == "2") ? $yes : $no ;?>&nbsp;&nbsp;
                            <strong>Retina &nbsp;Photos</strong><?php echo ($elem_fundusDiscPhoto == "3") ? $yes : $no ;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span class="drak_purple_color">OU</span>
                            <?php echo ($elem_photoEye == "OU") ? $yes : $no ;?>&nbsp;&nbsp;
                            <span class="blue_color">OD</span>
                            <?php echo ($elem_photoEye == "OD") ? $yes : $no ;?>&nbsp;&nbsp;
                            <span class="green_color ">OS</span>
                            <?php echo ($elem_photoEye == "OS") ? $yes : $no ;?>


                    </td>
                    <td style="width:45%" class="txt_11b alignCenter" >
                    <?php echo $elem_gla_mac_print; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="height:20px;"  class="alignLeft" >
                      <div style="width:900px;"><strong>Technician Comments</strong>&nbsp;:&nbsp;<?php echo $elem_desc; ?></div>
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
                                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"><strong>C:D</strong></td>
                                            <td colspan="6"><div style="width:430px"><?php if(!$elem_cdOd && $od_text) echo $od_text; else echo $elem_cdOd;?></div></td>
                                        </tr>
                                          <tr >
                                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"><strong>Disc</strong></td>
                                            <td>Sharp &amp; Pink
                                            <?php if($Sharp_Pink_OD==1) echo $yes; else echo $no; ?></td>
                                            <td >Pale<?php echo ($Pale_OD == "1") ? $yes : $no; ?></td>
                                            <td colspan="2" >Large Cap<?php echo ($Large_Cap_OD == "1") ? $yes : $no; ?></td>
                                            <td colspan="2" >Sloping<?php echo ($Sloping_OD == "1") ? $yes : $no; ?></td>
                                          </tr>
                                        
                                        <tr >
                                          <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                                            <td>Notch<?php if($Notch_OD==1) echo $yes; else echo $no; ?></td>
                                            <td >NVD<?php echo ($NVD_OD == "1") ? $yes : $no; ?></td>
                                          <td colspan="4" >Leakage<?php echo ($Leakage_OD == "1") ? $yes : $no; ?></td>
                                        </tr>                                        
										<tr class="alignMiddle">
                                            <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px; width:50px;"><strong>Macula</strong></td>
                                            <td ><b>BDR</b></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">T<?php if($Macula_BDR_OD_T==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+1<?php if($Macula_BDR_OD_1==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+2<?php if($Macula_BDR_OD_2==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+3<?php if($Macula_BDR_OD_3==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+4<?php if($Macula_BDR_OD_4==1) echo $yes; else echo $no; ?></td>
                                       </tr>
                                        
                                        <tr class="alignMiddle">
                                          <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"></td>
                                          <td ><b>RPE Change</b></td>
                                          <td class="alignLeft">T<?php if($Macula_Rpe_OD_T==1) echo $yes; else echo $no; ?></td>
                                          <td class="alignLeft">+1<?php if($Macula_Rpe_OD_1==1) echo $yes; else echo $no; ?></td>
                                          <td class="alignLeft">+2<?php if($Macula_Rpe_OD_2==1) echo $yes; else echo $no; ?></td>
                                          <td class="alignLeft">+3<?php if($Macula_Rpe_OD_3==1) echo $yes; else echo $no; ?></td>
                                          <td class="alignLeft">+4<?php if($Macula_Rpe_OD_4==1) echo $yes; else echo $no; ?></td>
                                        </tr> 
                                        <tr class="alignMiddle">
                                          <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"></td>
                                          <td ><b>Edema</b></td>
                                          <td class="alignLeft">T<?php if($Macula_Edema_OD_T==1) echo $yes; else echo $no; ?></td>
                                          <td class="alignLeft">+1<?php if($Macula_Edema_OD_1==1) echo $yes; else echo $no; ?></td>
                                          <td class="alignLeft">+2<?php if($Macula_Edema_OD_2==1) echo $yes; else echo $no; ?></td>
                                          <td class="alignLeft">+3<?php if($Macula_Edema_OD_3==1) echo $yes; else echo $no; ?></td>
                                          <td class="alignLeft">+4<?php if($Macula_Edema_OD_4==1) echo $yes; else echo $no; ?></td>
                                        </tr> 
                                        <tr class="alignMiddle">
                                          <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"></td>
                                          <td ><b>Drusen</b></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px" >T<?php if($Macula_Drusen_OD_T==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+1<?php if($Macula_Drusen_OD_1==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+2<?php if($Macula_Drusen_OD_2==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+3<?php if($Macula_Drusen_OD_3==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+4<?php if($Macula_Drusen_OD_4==1) echo $yes; else echo $no; ?></td>
                                        </tr>    
                                        <tr >
                                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                                            <td>SRNVM<?php if($Macula_SRNVM_OD==1) echo $yes; else echo $no; ?></td>
                                            <td colspan="3" >Scars<?php echo ($Macula_Scars_OD == "1") ? $yes : $no; ?></td>
                                            <td colspan="2" >Hemorrhage<?php echo ($Macula_Hemorrhage_OD == "1") ? $yes : $no; ?></td>
                                        </tr>
                                        <tr >
                                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                                            <td>Microaneurysm<?php if($Macula_Microaneurysm_OD==1) echo $yes; else echo $no; ?></td>
                                            <td colspan="3" >Exudates<?php echo ($Macula_Exudates_OD == "1") ? $yes : $no; ?></td>
                                            <td colspan="2" >Normal<?php echo ($Macula_Normal_OD == "1") ? $yes : $no; ?></td>
                                        </tr>                                        
             							
                                         <tr >
                                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"><strong>Periphery</strong></td>
                                            <td>Hemorrhage<?php if($Periphery_Hemorrhage_OD==1) echo $yes; else echo $no; ?></td>
                                          	<td colspan="3" >Microaneurysms<?php echo ($Periphery_Microaneurysms_OD == "1") ? $yes : $no; ?></td>
                                            <td colspan="2" >Exudates<?php echo ($Periphery_Exudates_OD == "1") ? $yes : $no; ?></td>
                                        </tr>
                                         <tr >
                                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;">&nbsp;</td>
                                            <td>Cr Scars<?php if($Periphery_Cr_Scars_OD==1) echo $yes; else echo $no; ?></td>
                                          	<td colspan="3" >NV<?php echo ($Periphery_NV_OD == "1") ? $yes : $no; ?></td>
                                            <td colspan="2" >Nevus<?php echo ($Periphery_Nevus_OD == "1") ? $yes : $no; ?></td>
                                        </tr>

                                        <tr >
                                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                                            <td colspan="6">Edema<?php if($Periphery_Edema_OD==1) echo $yes; else echo $no; ?></td>
                                        </tr>
										
                                        <tr>
                                            <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"><strong>Desc.</strong></td>
                                            <td colspan="6"><div style="width:450px;"><?php echo $elem_resDescOd; ?></div></td>
                                        </tr>
                                    </table>
                  </td>
                    <td class="alignLeft valignTop" style="border:1px solid #000000;"><table cellpadding="0" cellspacing="0" style="width:100%;" >
                      <tr >
                        <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;">&nbsp;</td>
                        <td colspan="6"><div style="width:430px">
                          <?php if(!$elem_cdOs && $os_text) echo $os_text; else echo $elem_cdOs;?>
                        </div></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;">&nbsp;</td>
                        <td>Sharp &amp; Pink
                        <?php if($Sharp_Pink_OS==1) echo $yes; else echo $no; ?></td>
                        <td >Pale<?php echo ($Pale_OS == "1") ? $yes : $no; ?></td>
                        <td colspan="2" >Large Cap<?php echo ($Large_Cap_OS == "1") ? $yes : $no; ?></td>
                        <td colspan="2" >Sloping<?php echo ($Sloping_OS == "1") ? $yes : $no; ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                        <td>Notch
                          <?php if($Notch_OS==1) echo $yes; else echo $no; ?></td>
                        <td >NVD<?php echo ($NVD_OS == "1") ? $yes : $no; ?></td>
                        <td colspan="4" >Leakage<?php echo ($Leakage_OS == "1") ? $yes : $no; ?></td>
                      </tr>
                      <tr class="alignMiddle">
                        <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px; width:5px;">&nbsp;</td>
                        <td ><b>BDR</b></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">T
                          <?php if($Macula_BDR_OS_T==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+1
                          <?php if($Macula_BDR_OS_1==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+2
                          <?php if($Macula_BDR_OS_2==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+3
                          <?php if($Macula_BDR_OS_3==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+4
                          <?php if($Macula_BDR_OS_4==1) echo $yes; else echo $no; ?></td>
                      </tr>
                      <tr class="alignMiddle">
                        <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"></td>
                        <td ><b>RPE Change</b></td>
                        <td class="alignLeft">T
                          <?php if($Macula_Rpe_OS_T==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+1
                          <?php if($Macula_Rpe_OS_1==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+2
                          <?php if($Macula_Rpe_OS_2==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+3
                          <?php if($Macula_Rpe_OS_3==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+4
                          <?php if($Macula_Rpe_OS_4==1) echo $yes; else echo $no; ?></td>
                      </tr>
                      <tr class="alignMiddle">
                        <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"></td>
                        <td ><b>Edema</b></td>
                        <td class="alignLeft">T
                          <?php if($Macula_Edema_OS_T==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+1
                          <?php if($Macula_Edema_OS_1==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+2
                          <?php if($Macula_Edema_OS_2==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+3
                          <?php if($Macula_Edema_OS_3==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+4
                          <?php if($Macula_Edema_OS_4==1) echo $yes; else echo $no; ?></td>
                      </tr>
                      <tr class="alignMiddle">
                        <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"></td>
                        <td ><b>Drusen</b></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px" >T
                          <?php if($Macula_Drusen_OS_T==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+1
                          <?php if($Macula_Drusen_OS_1==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+2
                          <?php if($Macula_Drusen_OS_2==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+3
                          <?php if($Macula_Drusen_OS_3==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+4
                          <?php if($Macula_Drusen_OS_4==1) echo $yes; else echo $no; ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                        <td>SRNVM
                          <?php if($Macula_SRNVM_OS==1) echo $yes; else echo $no; ?></td>
                        <td colspan="3" >Scars<?php echo ($Macula_Scars_OS == "1") ? $yes : $no; ?></td>
                        <td colspan="2" >Hemorrhage<?php echo ($Macula_Hemorrhage_OS == "1") ? $yes : $no; ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                        <td>Microaneurysm
                          <?php if($Macula_Microaneurysm_OS==1) echo $yes; else echo $no; ?></td>
                        <td colspan="3" >Exudates<?php echo ($Macula_Exudates_OS == "1") ? $yes : $no; ?></td>
                        <td colspan="2" >Normal<?php echo ($Macula_Normal_OS == "1") ? $yes : $no; ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;">&nbsp;</td>
                        <td>Hemorrhage
                          <?php if($Periphery_Hemorrhage_OS==1) echo $yes; else echo $no; ?></td>
                        <td colspan="3" >Microaneurysms<?php echo ($Periphery_Microaneurysms_OS == "1") ? $yes : $no; ?></td>
                        <td colspan="2" >Exudates<?php echo ($Periphery_Exudates_OS == "1") ? $yes : $no; ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;">&nbsp;</td>
                        <td>Cr Scars
                          <?php if($Periphery_Cr_Scars_OS==1) echo $yes; else echo $no; ?></td>
                        <td colspan="3" >NV<?php echo ($Periphery_NV_OS == "1") ? $yes : $no; ?></td>
                        <td colspan="2" >Nevus<?php echo ($Periphery_Nevus_OS == "1") ? $yes : $no; ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                        <td colspan="6">Edema
                          <?php if($Periphery_Edema_OS==1) echo $yes; else echo $no; ?></td>
                      </tr>
                      <tr>
                        <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                        <td colspan="6"><div style="width:450px;"><?php echo $elem_resDescOs; ?></div></td>
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
        <td colspan="2">
            <table class="table_collapse" >
                <tr>
                    <td style="width:150px; height:<?php echo $trHeight;?>;"><strong>Stable</strong><?php echo ($elem_stable == "1") ? $yes : $no;?></td>
                    <td class="alignLeft nowrap" style="width:150px; "><strong>Continue Meds</strong><?php echo ($elem_contiMeds == "1") ? $yes : $no;?></td>
                    <td class="alignLeft nowrap" style="width:150px"><strong>Monitor&nbsp;AG</strong><?php echo ($elem_monitorAg == "1") ? $yes : $no;?></td>
                    <td class="alignLeft nowrap" style="width:150px"><strong>Tech to inform Pt.</strong><?php echo ($elem_tech2InformPt == "1") ? $yes : $no;?></td>
                    <td class="alignLeft nowrap" style="width:150px"><strong>Pt&nbsp;informed&nbsp;of&nbsp;results</strong><?php echo ($elem_ptInformed == "1") ? $yes : $no;?></td>
                </tr>
                <tr>
                    <td  style="height:<?php echo $trHeight;?>;"><strong>F/U&nbsp;APA</strong><?php echo ($elem_fuApa == "1") ? $yes : $no;?> </td>
                    <td><strong>F/U&nbsp;Retina</strong><?php echo ($elem_fuRetina == "1") ? $yes : $no;?> </td>
                    <td colspan="2"><div style="width:300px"><?php if($elem_fuRetina == "1") { echo ($elem_fuRetinaDesc); }?></div></td>
                    <td><strong>Informed Pt result next visit</strong><?php echo ($elem_informedPtNv == "1") ? $yes : $no;?></td>
                </tr>
           </table> 
        </td>
    </tr>       
    <tr>
        <td class="alignLeft valignTop" colspan="2"><div style="width:900px"><strong>Comments</strong>&nbsp;:&nbsp;<?php echo $discComments; ?></div></td>
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