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
File: test_ivfa_print.php
Purpose: This file provides IVFA print version.
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
						<td colspan="3" align="center" style="width:1080px" class="text_b"><b>IVFA TEST'.$finalize.'</b>
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
                    <td style="width:55%" ><strong>IVFA</strong>&nbsp;&nbsp;&nbsp;&nbsp;
                            <span class="drak_purple_color">OU</span>
                            <?php echo ($elem_ivfa_od == "1") ? $yes : $no ;?>&nbsp;&nbsp;
                            <span class="blue_color">OD</span>
                            <?php echo ($elem_ivfa_od == "2") ? $yes : $no ;?>&nbsp;&nbsp;
                            <span class="green_color ">OS</span>
                            <?php echo ($elem_ivfa_od == "3") ? $yes : $no ;?>&nbsp;&nbsp;&nbsp;&nbsp;
                            <span>Early and late shots</span>
                            <?php echo ($elem_ivfa_early == "1") ? $yes : $no ;?>&nbsp;&nbsp;
                            <span>Extra Copy</span>
                            <?php echo ($elem_ivfa_extra == "1") ? $yes : $no ;?>


                    </td>
                    <td style="width:45%" class="txt_11b alignCenter" >
                    <?php echo $elem_gla_mac_print; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="height:20px;"  class="alignLeft" >
                      <div style="width:900px;"><strong>Technician Comments</strong>&nbsp;:&nbsp;<?php echo $elem_comments_ivfa; ?></div>
                    </td>
				</tr>                	
            </table>
        </td>
    </tr>
    <tr class="alignLeft alignMiddle">
        <td style="height:25px" colspan="2" >
            <strong>Performed By</strong>&nbsp;:&nbsp;<?php echo $objTests->getPersonnal3($elem_performedBy);?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <strong>Patient Understanding & Cooperation</strong> &nbsp;:&nbsp;
            Good<?php echo ($elem_pa_under == '' || $elem_pa_under == "Good") ? " (yes)" : "" ;?>&nbsp;&nbsp;
            Fair<?php echo ($elem_pa_under == "Fair") ? " (yes)" : "" ;?>&nbsp;&nbsp;
            Poor<?php echo ($elem_pa_under == "Poor") ? " (yes)" : "" ;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
            Good<?php echo (!$elem_pa_inter || $elem_pa_inter == "Good") ? " (yes)" : "";?>&nbsp;&nbsp;
            Fair<?php echo ($elem_pa_inter == "Fair") ? " (yes)" : "";?>&nbsp;&nbsp;
            Poor<?php echo ($elem_pa_inter == "Poor") ? " (yes)" : "";?>&nbsp;&nbsp;
        </td>
        <td style="width:45%">
            <span class="green_color ">OS</span>&nbsp;&nbsp;
            Good<?php echo (!$elem_pa_inter1 || $elem_pa_inter1 == "Good") ? " (yes)" : "";?>&nbsp;&nbsp;
            Fair<?php echo ($elem_pa_inter1 == "Fair") ? " (yes)" : "";?>&nbsp;&nbsp;
            Poor<?php echo ($elem_pa_inter1 == "Poor") ? " (yes)" : "";?>&nbsp;&nbsp;
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
                                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"><strong>Disc</strong></td>
                                            <td>Sharp & Pink<?php if($Sharp_Pink_OD==1) echo $yes; else echo $no; ?></td>
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
                                        <tr >
                                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"><strong>Retina</strong></td>
                                            <td>Hemorrhage<?php if($Retina_Hemorrhage_OD==1) echo $yes; else echo $no; ?></td>
                                          <td colspan="3" >Microaneurysms<?php echo ($Retina_Microaneurysms_OD == "1") ? $yes : $no; ?></td>
                                            <td colspan="2" >Exudates<?php echo ($Retina_Exudates_OD == "1") ? $yes : $no; ?></td>
                                        </tr>
                                        <tr >
                                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                                            <td>Laser Scars<?php if($Retina_Laser_Scars_OD==1) echo $yes; else echo $no; ?></td>
                                            <td colspan="3" >NVE<?php echo ($Retina_NEVI_OD == "1") ? $yes : $no; ?></td>
                                            <td colspan="2" >SRVNM<?php echo ($Retina_SRVNM_OD == "1") ? $yes : $no; ?></td>
                                        </tr>
                                        <tr >
                                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                                            <td>Edema<?php if($Retina_Edema_OD==1) echo $yes; else echo $no; ?></td>
                                            <td colspan="3" >Nevus<?php echo ($Retina_Nevus_OD == "1") ? $yes : $no; ?></td>
                                            <td colspan="2" >Ischemia<?php echo ($Retina_Ischemia_OD == "1") ? $yes : $no; ?></td>
                                        </tr>                                        
                                        <tr >
                                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                                            <td>BRVO<?php if($Retina_BRVO_OD==1) echo $yes; else echo $no; ?></td>
                                            <td colspan="5" >CRVO<?php echo ($Retina_CRVO_OD == "1") ? $yes : $no; ?></td>
                                        </tr>                                        

<tr class="alignMiddle">
                                            <td class="txt_11b alignLeft valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; width:50px;"></td>
                                            <td ><b>BDR</b></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px" >T<?php if($Retina_BDR_OD_T==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+1<?php if($Retina_BDR_OD_1==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+2<?php if($Retina_BDR_OD_2==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+3<?php if($Retina_BDR_OD_3==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+4<?php if($Retina_BDR_OD_4==1) echo $yes; else echo $no; ?></td>
                                        </tr>    
                                        <tr class="alignMiddle">
                                          <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"></td>
                                          <td ><b>Drusen</b></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px" >T<?php if($Retina_Druse_OD_T==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+1<?php if($Retina_Druse_OD_1==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+2<?php if($Retina_Druse_OD_2==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+3<?php if($Retina_Druse_OD_3==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+4<?php if($Retina_Druse_OD_4==1) echo $yes; else echo $no; ?></td>
                                        </tr>    
                                        <tr class="alignMiddle">
                                          <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"></td>
                                          <td ><b>RPE Change</b></td>
                                          <td class="alignLeft">T<?php if($Retina_RPE_Change_OD_T==1) echo $yes; else echo $no; ?></td>
                                          <td class="alignLeft">+1<?php if($Retina_RPE_Change_OD_1==1) echo $yes; else echo $no; ?></td>
                                          <td class="alignLeft">+2<?php if($Retina_RPE_Change_OD_2==1) echo $yes; else echo $no; ?></td>
                                          <td class="alignLeft">+3<?php if($Retina_RPE_Change_OD_3==1) echo $yes; else echo $no; ?></td>
                                          <td class="alignLeft">+4<?php if($Retina_RPE_Change_OD_4==1) echo $yes; else echo $no; ?></td>
                                        </tr> 
                                        <tr >
                                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"><strong>Macula</strong></td>
                                            <td>Drusen<?php if($Druse_OD==1) echo $yes; else echo $no; ?></td>
                                            <td >RPE Changes<?php echo ($RPE_Changes_OD == "1") ? $yes : $no; ?></td>
                                            <td colspan="2" >SRNVM<?php echo ($SRNVM_OD == "1") ? $yes : $no; ?></td>
                                            <td colspan="2" >Edema<?php echo ($Edema_OD == "1") ? $yes : $no; ?></td>
                                        </tr>
                                        <tr >
                                          <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                                            <td>Scars<?php if($Scars_OD==1) echo $yes; else echo $no; ?></td>
                                            <td >Hemorrhage<?php echo ($Hemorrhage_OD == "1") ? $yes : $no; ?></td>
                                          <td colspan="4" >Microaneurysms<?php echo ($Microaneurysms_OD == "1") ? $yes : $no; ?></td>
                                        </tr>
                                        <tr >
                                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                                          <td colspan="2">Exudates<?php if($Exudates_OD==1) echo $yes; else echo $no; ?></td>
                                          <td colspan="2">PED<?php echo ($PED_OD == "1") ? $yes : $no; ?></td>
                                          <td colspan="2" >SR Heme<?php echo ($SR_Heme_OD == "1") ? $yes : $no; ?></td>
                                        </tr>
                                        <tr >
                                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                                            <td colspan="2">Classic CNV<?php if($Classic_CNV_OD==1) echo $yes; else echo $no; ?></td>
                                          <td colspan="4">Occult CNV<?php echo ($Occult_CNV_OD == "1") ? $yes : $no; ?></td>
                                        </tr>
<tr class="alignMiddle">
                                            <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"></td>
                                            <td ><b>BDR</b></td>
                <td class="alignLeft">T<?php if($Macula_BDR_OD_T==1) echo $yes; else echo $no; ?></td>
                <td class="alignLeft">+1<?php if($Macula_BDR_OD_1==1) echo $yes; else echo $no; ?></td>
                <td class="alignLeft">+2<?php if($Macula_BDR_OD_2==1) echo $yes; else echo $no; ?></td>
                <td class="alignLeft">+3<?php if($Macula_BDR_OD_3==1) echo $yes; else echo $no; ?></td>
                <td class="alignLeft">+4<?php if($Macula_BDR_OD_4==1) echo $yes; else echo $no; ?></td>
                           </tr>
                                        <tr class="alignMiddle">
                                            <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"></td>
                                            <td ><b>SMD</b></td>
                                            <td class="alignLeft">T<?php if($Macula_SMD_OD_T==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft">+1<?php if($Macula_SMD_OD_1==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft">+2<?php if($Macula_SMD_OD_2==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft">+3<?php if($Macula_SMD_OD_3==1) echo $yes; else echo $no; ?></td>
                                            <td class="alignLeft">+4<?php if($Macula_SMD_OD_4==1) echo $yes; else echo $no; ?></td>
                                        </tr> 
<tr>
                                            <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"><strong>Other</strong></td>
                                            <td colspan="6"><div style="width:450px;"><?php echo $elem_testresults_desc_od; ?></div></td>
                                        </tr>
                                    </table>
                    </td>
                    <td class="alignLeft valignTop" style="border:1px solid #000000;"><table cellpadding="0" cellspacing="0" style="width:100%;" >
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
                      <tr >
                        <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;">&nbsp;</td>
                        <td>Hemorrhage
                          <?php if($Retina_Hemorrhage_OS==1) echo $yes; else echo $no; ?></td>
                        <td colspan="3" >Microaneurysms<?php echo ($Retina_Microaneurysms_OS == "1") ? $yes : $no; ?></td>
                        <td colspan="2" >Exudates<?php echo ($Retina_Exudates_OS == "1") ? $yes : $no; ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                        <td>Laser Scars
                          <?php if($Retina_Laser_Scars_OS==1) echo $yes; else echo $no; ?></td>
                        <td colspan="3" >NVE<?php echo ($Retina_NEVI_OS == "1") ? $yes : $no; ?></td>
                        <td colspan="2" >SRVNM<?php echo ($Retina_SRVNM_OS == "1") ? $yes : $no; ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                        <td>Edema
                          <?php if($Retina_Edema_OS==1) echo $yes; else echo $no; ?></td>
                        <td colspan="3" >Nevus<?php echo ($Retina_Nevus_OS == "1") ? $yes : $no; ?></td>
                        <td colspan="2" >Ischemia<?php echo ($Retina_Ischemia_OS == "1") ? $yes : $no; ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                        <td>BRVO
                          <?php if($Retina_BRVO_OS==1) echo $yes; else echo $no; ?></td>
                        <td colspan="5" >CRVO<?php echo ($Retina_CRVO_OS == "1") ? $yes : $no; ?></td>
                      </tr>
                      <tr class="alignMiddle">
                        <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px; width:5px;"></td>
                        <td ><b>BDR</b></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px" >T
                          <?php if($Retina_BDR_OS_T==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+1
                          <?php if($Retina_BDR_OS_1==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+2
                          <?php if($Retina_BDR_OS_2==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+3
                          <?php if($Retina_BDR_OS_3==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+4
                          <?php if($Retina_BDR_OS_4==1) echo $yes; else echo $no; ?></td>
                      </tr>
                      <tr class="alignMiddle">
                        <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"></td>
                        <td ><b>Drusen</b></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px" >T
                          <?php if($Retina_Druse_OS_T==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+1
                          <?php if($Retina_Druse_OS_1==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+2
                          <?php if($Retina_Druse_OS_2==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+3
                          <?php if($Retina_Druse_OS_3==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft" style="width:<?php echo $td1Width;?>;px">+4
                          <?php if($Retina_Druse_OS_4==1) echo $yes; else echo $no; ?></td>
                      </tr>
                      <tr class="alignMiddle">
                        <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"></td>
                        <td ><b>RPE Change</b></td>
                        <td class="alignLeft">T
                          <?php if($Retina_RPE_Change_OS_T==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+1
                          <?php if($Retina_RPE_Change_OS_1==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+2
                          <?php if($Retina_RPE_Change_OS_2==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+3
                          <?php if($Retina_RPE_Change_OS_3==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+4
                          <?php if($Retina_RPE_Change_OS_4==1) echo $yes; else echo $no; ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;">&nbsp;</td>
                        <td>Drusen
                          <?php if($Druse_OD==1) echo $yes; else echo $no; ?></td>
                        <td >RPE Changes<?php echo ($RPE_Changes_OS == "1") ? $yes : $no; ?></td>
                        <td colspan="2" >SRNVM<?php echo ($SRNVM_OS == "1") ? $yes : $no; ?></td>
                        <td colspan="2" >Edema<?php echo ($Edema_OS == "1") ? $yes : $no; ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                        <td>Scars
                          <?php if($Scars_OS==1) echo $yes; else echo $no; ?></td>
                        <td >Hemorrhage<?php echo ($Hemorrhage_OS == "1") ? $yes : $no; ?></td>
                        <td colspan="4" >Microaneurysms<?php echo ($Microaneurysms_OS == "1") ? $yes : $no; ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                        <td colspan="2">Exudates
                          <?php if($Exudates_OS==1) echo $yes; else echo $no; ?></td>
                        <td colspan="2">PED<?php echo ($PED_OS == "1") ? $yes : $no; ?></td>
                        <td colspan="2" >SR Heme<?php echo ($SR_Heme_OS == "1") ? $yes : $no; ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;"></td>
                        <td colspan="2">Classic CNV
                          <?php if($Classic_CNV_OS==1) echo $yes; else echo $no; ?></td>
                        <td colspan="4">Occult CNV<?php echo ($Occult_CNV_OS == "1") ? $yes : $no; ?></td>
                      </tr>
                      <tr class="alignMiddle">
                        <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"></td>
                        <td ><b>BDR</b></td>
                        <td class="alignLeft">T
                          <?php if($Macula_BDR_OS_T==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+1
                          <?php if($Macula_BDR_OS_1==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+2
                          <?php if($Macula_BDR_OS_2==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+3
                          <?php if($Macula_BDR_OS_3==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+4
                          <?php if($Macula_BDR_OS_4==1) echo $yes; else echo $no; ?></td>
                      </tr>
                      <tr class="alignMiddle">
                        <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;"></td>
                        <td ><b>SMD</b></td>
                        <td class="alignLeft">T
                          <?php if($Macula_SMD_OS_T==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+1
                          <?php if($Macula_SMD_OS_1==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+2
                          <?php if($Macula_SMD_OS_2==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+3
                          <?php if($Macula_SMD_OS_3==1) echo $yes; else echo $no; ?></td>
                        <td class="alignLeft">+4
                          <?php if($Macula_SMD_OS_4==1) echo $yes; else echo $no; ?></td>
                      </tr>
                      <tr>
                        <td class="txt_11b alignLeft valignTop" style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                        <td colspan="6"><div style="width:450px;"><?php echo $elem_testresults_desc_os; ?></div></td>
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
                    <td style="width:150px; height:<?php echo $trHeight;?>;"><strong>Stable</strong><?php echo ($elem_Stable == "1") ? $yes : $no;?></td>
                    <td class="alignLeft nowrap" style="width:150px; "><strong>Continue Meds</strong><?php echo ($elem_contiMeds == "1") ? $yes : $no;?></td>
                    <td class="alignLeft nowrap" style="width:150px"><strong>Monitor&nbsp;AG</strong><?php echo ($elem_MonitorAg == "1") ? $yes : $no;?></td>
                    <td class="alignLeft nowrap" style="width:150px"><strong>Tech to inform Pt.</strong><?php echo ($elem_tech2InformPt == "1") ? $yes : $no;?></td>
                    <td class="alignLeft nowrap" style="width:150px"><strong>Pt&nbsp;informed&nbsp;of&nbsp;results</strong><?php echo ($elem_PatientInformed == "1") ? $yes : $no;?></td>
                </tr>
                <tr>
                    <td colspan="2" style="height:<?php echo $trHeight;?>;"><strong>Informed Pt result next visit</strong><?php echo ($elem_informedPtNv == "1") ? $yes : $no;?> </td>
                    <td><strong>F/U&nbsp;APA</strong><?php echo ($elem_FuApa == "1") ? $yes : $no;?></td>
                    <td><strong>Argon Laser Surgery</strong><?php echo ($elem_ArgonLaser == "1") ? $yes : $no;?></td>
                    <td><strong>Repeat test 1 year</strong><?php echo ($elem_rptTst1yr == "1") ? $yes : $no;?></td>
                </tr>
                <tr>
                  <td colspan="2" style="height:<?php echo $trHeight;?>;">
					 <span class="drak_purple_color">OU</span>
					 <?php echo (!$elem_ArgonLaserEye || $elem_ArgonLaserEye == "OU")? $yes : $no; ?>&nbsp;&nbsp;
                    <span class="blue_color">OD</span>
					<?php echo ($elem_ArgonLaserEye == "OD")? $yes : $no; ?>&nbsp;&nbsp;
                    <span class="green_color">OS</span>
                    <?php echo ($elem_ArgonLaserEye == "OS")? $yes : $no; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $elem_ArgonLaserEyeOptions; ?>       
                  </td>
                  <td>F/U Retina<?php echo ($elem_rptTst1yr == "1") ? $yes : $no;?></td>
                  <td colspan="2"><div style="width:300px;"><?php echo $elem_FuRetinaComments;?></div></td>
                </tr>
           </table> 
        </td>
    </tr>       
    <tr>
        <td class="alignLeft valignTop" colspan="2"><div style="width:900px"><strong>Comments</strong>&nbsp;:&nbsp;<?php echo $ivfaComments; ?></div></td>
    </tr>
    <tr>
        <td colspan="2" style="height:10px;" ></td>
    </tr>
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