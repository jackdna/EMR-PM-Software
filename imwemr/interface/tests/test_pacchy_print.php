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
File: test_pacchy_print.php
Purpose: This file provides print version of Pachy Test.
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
						<td colspan="3" align="center" class="text_b"><b>PACHY TEST'.$finalize.'</b>
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
                    <td style="width:55%" ><strong>Pachymeter </strong>&nbsp;&nbsp;&nbsp;&nbsp;
                            <span class="drak_purple_color">OU</span>
                            <?php echo ($elem_pachyMeterEye == "OU") ? $yes : $no ;?>&nbsp;&nbsp;
                            <span class="blue_color">OD</span>
                            <?php echo ($elem_pachyMeterEye == "OD") ? "(yes)" : "" ;?>&nbsp;&nbsp;
                            <span class="green_color ">OS</span>
                            <?php echo ($elem_pachyMeterEye == "OS") ? "(yes)" : "" ;?>
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
			if($elem_diagnosis =='Other') { echo $elem_diagnosisOther; } else { echo $elem_diagnosis; } ?>
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
                         <table cellpadding="0" cellspacing="0" style="width:100%;" class="valignTop" >
                                        <tr >
                                          <td width="110" class="valignTop alignLeft" style="height:<?php echo $trHeight;?>px; width:110px;" ><strong>Pachy</strong></td>
                                          <td width="82" style="width:<?php echo $td1Width;?>px">Central<?php if($Central_OD==1) echo $yes; else echo $no; ?></td>
                                          <td width="93" style="width:<?php echo $td1Width;?>px">Nasal<?php if($Nasal_OD==1) echo $yes; else echo $no; ?></td>
                                          <td width="80" style="width:<?php echo $td1Width;?>px"><?php echo $elem_pachy_od_readings; ?></td>
                                          <td width="55" style="width:<?php echo $td1Width;?>px"><?php echo $elem_pachy_od_average; ?></td>
                                          <td width="76" style="width:<?php echo $td1Width;?>px"><?php echo $elem_pachy_od_correction_value; ?></td>
                                        </tr>
                                        <tr >
                                          <td class="valignTop alignLeft" style="height:<?php echo $trHeight;?>px;" >&nbsp;</td>
                                          <td >&nbsp;</td>
                                          <td >&nbsp;</td>
                                          <td colspan="3">&nbsp;</td>
                                        </tr>
                                        <tr >
                                          <td class="valignTop alignLeft" style="height:<?php echo $trHeight;?>px;" >&nbsp;</td>
                                          <td >Inferior<?php if($Inferior_OD==1) echo $yes; else echo $no; ?></td>
                                          <td >Temporal<?php if($Temporal_OD==1) echo $yes; else echo $no; ?></td>
                                          <td colspan="3">Superior<?php if($Superior_OD==1) echo $yes; else echo $no; ?></td>
                                        </tr>
                                        <tr>
                                          <td style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                                          <td colspan="5">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="height:<?php echo $trHeight;?>px;"><strong>Description</strong></td>
                                            <td colspan="5" style="width:400px;"><?php echo $elem_descOd; ?></td>
                                        </tr>
                      </table>
  </td>
                    <td class="alignLeft valignTop" style="border:1px solid #000000;"><table cellpadding="0" cellspacing="0" style="width:100%;" class="valignTop" >
                      <tr >
                        <td width="110" class="valignTop alignLeft" style="height:<?php echo $trHeight;?>px; width:110px;" >Central
                        <?php if($Central_OS==1) echo $yes; else echo $no; ?></td>
                        <td width="118" style="width:<?php echo $td1Width;?>px">Nasal
                          <?php if($Nasal_OS==1) echo $yes; else echo $no; ?></td>
                        <td width="86" style="width:<?php echo $td1Width;?>px"><?php echo $elem_pachy_os_readings; ?></td>
                        <td width="83" style="width:<?php echo $td1Width;?>px"><?php echo $elem_pachy_os_average; ?></td>
                        <td width="99" style="width:<?php echo $td1Width;?>px"><?php echo $elem_pachy_os_correction_value; ?></td>
                      </tr>
                      <tr >
                        <td class="valignTop alignLeft" style="height:<?php echo $trHeight;?>px;" >&nbsp;</td>
                        <td >&nbsp;</td>
                        <td colspan="3">&nbsp;</td>
                      </tr>
                      <tr >
                        <td class="valignTop alignLeft" style="height:<?php echo $trHeight;?>px;" >Inferior
                        <?php if($Inferior_OS==1) echo $yes; else echo $no; ?></td>
                        <td >Temporal
                          <?php if($Temporal_OS==1) echo $yes; else echo $no; ?></td>
                        <td colspan="3">Superior
                          <?php if($Superior_OS==1) echo $yes; else echo $no; ?></td>
                      </tr>
                      <tr>
                        <td colspan="5" style="height:<?php echo $trHeight;?>px;">&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="5" style="height:<?php echo $trHeight;?>px; width:400px;"><?php echo $elem_descOs; ?></td>
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
                    <td class="alignLeft nowrap" style="width:150px"><strong>F/U&nbsp;APA</strong><?php echo ($elem_fuApa == "1") ? $yes : $no;?></td>
                    <td class="alignLeft nowrap" style="width:150px"><strong>Tech to inform Pt.</strong><?php echo ($elem_tech2InformPt == "1") ? $yes : $no;?></td>
                    <td class="alignLeft nowrap" style="width:150px"><strong>Pt&nbsp;informed&nbsp;of&nbsp;results</strong><?php echo ($elem_ptInformed == "1") ? $yes : $no;?></td>
                </tr>
                <tr>
                    <td style="height:<?php echo $trHeight;?>;" colspan="5"><strong>Informed Pt result next visit</strong><?php echo ($elem_informedPtNv == "1") ? $yes : $no;?></td>
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