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
File: oct_rfnl_test_print.php
Purpose: This file provides print function in OCT RNFL test.
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
						<td colspan="3" align="center" class="text_b"><b>OCT - RNFL TEST'.$finalize.'</b>
						</td>
					</tr>
					<tr>
						<td class="text_b" align="left" style="width:350px">Ordered By '.$order_name_p.' on '.$elem_opidTestOrderedDate.'</td>
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
            <table style="width:100%" cellspacing="0" cellpadding="0">
                <tr style="height:20px">
                    <td style="width:45%" ><strong>OCT-RNFL</strong>&nbsp;&nbsp;&nbsp;&nbsp;
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
               		<td style=" text-align:left;" >
                    <?php if($elem_dilated){ echo "<b>Dilated:</b>&nbsp;".$elem_dilated;} ?>
                    </td>
                    </tr>
                    <tr>
                    <td colspan="2" style="height:20px;"  class="alignLeft" >
                      <strong>Technician Comments</strong>&nbsp;:&nbsp;
					  <?php echo $elem_techComments_print=($elem_techComments!="Technician Comments")?$elem_techComments:""; ?>
                    </td>
				</tr>                	
            </table>
        </td>
    </tr>
    <tr class="alignLeft alignMiddle"><td style="height:25px" colspan="2" >&nbsp;</td></tr>
    <tr class="alignLeft alignMiddle">
        <td style="height:25px" colspan="2" >
            <strong>Performed By</strong>&nbsp;:&nbsp;<?php echo $objTests->getPersonnal3($elem_performedBy);?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <strong>Patient Understanding &amp; Cooperation</strong> &nbsp;:&nbsp;
            Good<?php echo ($elem_ptUnderstanding == "Good") ? " (yes) " : "" ;?>&nbsp;&nbsp;
            Fair<?php echo ($elem_ptUndersatnding=="Fair") ? " (yes) " : "" ;?>&nbsp;&nbsp;
            Poor<?php echo ($elem_ptUndersatnding=="Poor") ? " (yes) " : "" ;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </td>
    </tr>
    <tr>
    	<td><strong>Diagnosis OD</strong>&nbsp;:&nbsp;<?php echo $print_val_dig;?></td>
        <td><strong>OS</strong>&nbsp;:&nbsp;<?php echo $print_os_val_dig;?></td>
    </tr>    
    <tr class="alignLeft alignMiddle" >
        <td style="height:20px;" colspan="2"><strong>Physician Interpretation</strong>: -</td>
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
                    <td class="alignLeft valignTop" style="border:1px solid #CCC;" >
                         <table cellpadding="0" cellspacing="0" style="width:100%;" >
                        <tr >
                            <td class="valignTop" style="padding-left:2px; width:90px;height:<?php echo $trHeight;?>px;">
                            	<b>Reliability</b></td>  
                             <td style="width:410px;">
                                Good<?php echo ($elem_reliabilityOd == "Good") ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                                Fair<?php echo ($elem_reliabilityOd=="Fair") ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                                Poor<?php echo ($elem_reliabilityOd=="Poor") ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr>
                          <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;">
                            	<b>Signal Strength</b></td>  
                             <td>
                         	<?php echo $SignalStrength; ?></td>
                        </tr>  
                         <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Quality :</b></td>  
                             <td>
                         	Good<?php echo ($elem_quality_od_gd) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Adequate<?php echo ($elem_quality_od_adequate) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Poor<?php echo ($elem_quality_od_poor) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr>    
                         <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Details :</b></td>  
                             <td>
                         	Algorithm Fail<?php echo $algoFail=($elem_quality_od_gd=="Good") ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Media Opacity<?php echo ($elem_detail_od_MediaOpacity) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Artifact<?php echo ($elem_detail_od_Artifact) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            <?php echo $otherprintval=trim($elem_details_od_other) ? "<br><b>Other:</b>&nbsp;&nbsp;".$elem_details_od_other : "";?>
                            </td>
                        </tr>  
                         <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;border-top:1px solid #CCC;  ">
                            	<b>Disc area :</b></td>  
                             <td style="border-top:1px solid #CCC; ">
                            <?php echo $elem_discarea_od=trim($elem_discarea_od) ? $elem_discarea_od : "";?>
                            </td>
                        </tr> 
                        <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Disc size :</b></td>  
                             <td>
                         	Large<?php echo ($elem_discsize_od_Large) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Avg<?php echo ($elem_discsize_od_Avg) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Small<?php echo ($elem_discsize_od_Small) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr>
		    <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;border-top:1px solid #CCC;  ">
                            	<b>Vertical C:D</b></td>  
                             <td style="border-top:1px solid #CCC; ">
                            <?php echo $elem_verti_cd_od=trim($elem_verti_cd_od) ? $elem_verti_cd_od : "";?>
                            </td>
                        </tr> 	
                        <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Disc edema :</b></td>  
                             <td>
                         	None<?php echo ($elem_discedema_od_No) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Mild<?php echo ($elem_discedema_od_Mild) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Mod<?php echo ($elem_discedema_od_Md) ? " (y) " : "" ;?>&nbsp;&nbsp;
                            Severe<?php echo ($elem_discedema_od_Severe) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            Superior<?php echo ($elem_discedema_od_Sup) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            Inferior<?php echo ($elem_discedema_od_Inf) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr> 
                        <tr >
                            <td class="valignTop" style="padding-left:2px; border-top:1px solid #CCC; height:<?php echo $trHeight;?>px; ">
                            	<b>RNFL :</b></td>  
                             <td style="border-top:1px solid #CCC; ">
                         	<?php echo $elem_rnfl_od_Avg=trim($elem_rnfl_od_Avg) ? "Avg ".$elem_rnfl_od_Avg." &micro;" : "";?>
                            </td>
                        </tr> 
                        <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Contour</b></td>  
                             <td>
                         
                            </td>
                        </tr> 
                        <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Overall :</b></td>  
                             <td>
                         	NL<?php echo ($elem_contour_overall_od_NL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Thick<?php echo ($elem_contour_overall_od_Thick) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Thin<?php echo ($elem_contour_overall_od_Thin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Borderline<?php echo ($elem_contour_overall_od_BL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;	
            				Very Thin<?php echo ($elem_contour_overall_od_VeryThin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr> 
                        <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Superior :</b></td>  
                             <td>
                         	NL<?php echo ($elem_contour_superior_od_NL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Thick<?php echo ($elem_contour_superior_od_Thick) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Thin<?php echo ($elem_contour_superior_od_Thin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Borderline<?php echo ($elem_contour_superior_od_BL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;		
            				Very Thin<?php echo ($elem_contour_superior_od_VeryThin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr> 
                        <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Inferior :</b></td>  
                             <td>
                         	NL<?php echo ($elem_contour_inferior_od_NL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Thick<?php echo ($elem_contour_inferior_od_Thick) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Thin<?php echo ($elem_contour_inferior_od_Thin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Borderline<?php echo ($elem_contour_inferior_od_BL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;		
            				Very Thin<?php echo ($elem_contour_inferior_od_VeryThin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr> 
                        <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Temporal :</b></td>  
                             <td>
                         	NL<?php echo ($elem_contour_temporal_od_NL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Thick<?php echo ($elem_contour_temporal_od_Thick) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Thin<?php echo ($elem_contour_temporal_od_Thin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Borderline<?php echo ($elem_contour_temporal_od_BL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;		
            				Very Thin<?php echo ($elem_contour_temporal_od_VeryThin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr>
		     <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Nasal :</b></td>  
                             <td>
                         	NL<?php echo ($elem_contour_nasal_od_NL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Thick<?php echo ($elem_contour_nasal_od_Thick) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Thin<?php echo ($elem_contour_nasal_od_Thin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Borderline<?php echo ($elem_contour_nasal_od_BL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;		
            				Very Thin<?php echo ($elem_contour_nasal_od_VeryThin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr>
		     <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>GCC :</b></td>  
                             <td>
                         	NL<?php echo ($elem_contour_gcc_od_NL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Thick<?php echo ($elem_contour_gcc_od_Thick) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Thin<?php echo ($elem_contour_gcc_od_Thin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Borderline<?php echo ($elem_contour_gcc_od_BL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;		
            				Very Thin<?php echo ($elem_contour_gcc_od_VeryThin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr>		
                        <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; bordor-top:1px solid #CCC; ">
                            	<b>Symmetric :</b></td>  
                             <td style=" bordor-top:1px solid #CCC;">
								<?php echo ($elem_symmertric_od_Yes=="Yes")?$elem_symmertric_od_Yes:"";
									echo ($elem_symmertric_od_No=="No")?$elem_symmertric_od_Yes:"";
								 ?>
                            </td>
                        </tr> 
		     <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; bordor-top:1px solid #CCC; ">
                            	<b>GPA :</b></td>  
                             <td style=" bordor-top:1px solid #CCC;">
								<?php echo ($elem_gpa_od_No=="No")?$elem_gpa_od_No:"";
									  echo ($elem_gpa_od_pos=="Possible")?$elem_gpa_od_pos:"";
									  echo ($elem_gpa_od_lp=="Like Progression")?$elem_gpa_od_lp:"";
								 ?>
                            </td>
                        </tr>	
                        <tr>
                        	<td colspan="2" style="border-top:1px solid #CCC;border-bottom:1px solid #CCC;">Interpretation</td>
                        </tr>                         
                         <tr>
                        	<td style="">Synthesis </td>
                            <td style="  width:410px;">
								<?php echo $elem_interpret_systhesis_od;?>
                            </td>
                        </tr>          
                      </table>
                    </td>
                    <td class="alignLeft valignTop" style="border:1px solid #CCC;">
                        <table cellpadding="0" cellspacing="0"  >
                           <tr >
                            <td class="valignTop" style="padding-left:2px; width:90px;height:<?php echo $trHeight;?>px;">
                            	<b>Reliability</b></td>  
                             <td style="width:410px;">
                          Good<?php echo ($elem_reliabilityOs == "Good") ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            Fair<?php echo ($elem_reliabilityOs=="Fair") ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            Poor<?php echo ($elem_reliabilityOs=="Poor") ? " (y) " : "(n)" ;?>&nbsp;&nbsp;</td>
                        </tr>
                          <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;">
                            	<b>Signal Strength</b></td>  
                             <td>
                         	<?php echo $SignalStrengthOS; ?></td>
                        </tr>  
                         <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Quality :</b></td>  
                             <td>
                         	Good<?php echo ($elem_quality_os_gd) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Adequate<?php echo ($elem_quality_os_adequate) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Poor<?php echo ($elem_quality_os_poor) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr>    
                         <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Details :</b></td>  
                             <td>
                         	Algorithm Fail<?php echo ($elem_quality_os_gd == "Good") ? " (yes) " : "(n)" ;?>&nbsp;&nbsp;
            				Media Opacity<?php echo ($elem_detail_os_MediaOpacity) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Artifact<?php echo ($elem_detail_os_Artifact) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            <?php echo $otherprintvalos=trim($elem_details_os_other) ? "<br><b>Other:</b>&nbsp;&nbsp;".$elem_details_od_other : "";?>
                            </td>
                        </tr>  
                         <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;border-top:1px solid #CCC;  ">
                            	<b>Disc area :</b></td>  
                             <td style="border-top:1px solid #CCC; ">
                            <?php echo $elem_discarea_os=trim($elem_discarea_os) ? $elem_discarea_os : "";?>
                            </td>
                        </tr> 
                        <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Disc size :</b></td>  
                             <td>
                         	Large<?php echo ($elem_discsize_os_Large) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Avg<?php echo ($elem_discsize_os_Avg) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Small<?php echo ($elem_discsize_os_Small) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr>
		     <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px;border-top:1px solid #CCC;  ">
                            	<b>Vertical C:D</b></td>  
                             <td style="border-top:1px solid #CCC; ">
                            <?php echo $elem_verti_cd_os=trim($elem_verti_cd_os) ? $elem_verti_cd_os : "";?>
                            </td>
                        </tr>		
                        <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Disc edema :</b></td>  
                             <td>
                         	None<?php echo ($elem_discedema_os_No) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Mild<?php echo ($elem_discedema_os_Mild) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Mod<?php echo ($elem_discedema_os_Md) ? " (y) " : "" ;?>&nbsp;&nbsp;
                            Severe<?php echo ($elem_discedema_os_Severe) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            Superior<?php echo ($elem_discedema_os_Sup) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            Inferior<?php echo ($elem_discedema_os_Inf) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr> 
                        <tr >
                            <td class="valignTop" style="padding-left:2px; border-top:1px solid #CCC; height:<?php echo $trHeight;?>px; ">
                            	<b>RNFL :</b></td>  
                             <td style="border-top:1px solid #CCC; ">
                         	<?php echo $elem_rnfl_os_Avg=trim($elem_rnfl_os_Avg) ? "Avg ".$elem_rnfl_os_Avg." &micro;" : "";?>
                            </td>
                        </tr> 
                        <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Contour</b></td>  
                             <td>
                         
                            </td>
                        </tr> 
                        <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Overall :</b></td>  
                             <td>
                         	NL<?php echo ($elem_contour_overall_os_NL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Thick<?php echo ($elem_contour_overall_os_Thick) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Thin<?php echo ($elem_contour_overall_os_Thin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Borderline<?php echo ($elem_contour_overall_os_BL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;		
            				Very Thin<?php echo ($elem_contour_overall_os_VeryThin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr> 
                        <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Superior :</b></td>  
                             <td>
                         	NL<?php echo ($elem_contour_superior_os_NL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Thick<?php echo ($elem_contour_overall_os_Thick) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Thin<?php echo ($elem_contour_superior_os_Thin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Borderline<?php echo ($elem_contour_superior_os_BL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;	
            				Very Thin<?php echo ($elem_contour_superior_os_VeryThin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr> 
                        <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Inferior :</b></td>  
                             <td>
                         	NL<?php echo ($elem_contour_inferior_os_NL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Thick<?php echo ($elem_contour_overall_os_Thick) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Thin<?php echo ($elem_contour_inferior_os_Thin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Borderline<?php echo ($elem_contour_inferior_os_BL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;	
            				Very Thin<?php echo ($elem_contour_inferior_os_VeryThin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr> 
                        <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Temporal :</b></td>  
                             <td>
                         	NL<?php echo ($elem_contour_temporal_os_NL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Thick<?php echo ($elem_contour_overall_os_Thick) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Thin<?php echo ($elem_contour_temporal_os_Thin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Borderline<?php echo ($elem_contour_temporal_os_BL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;	
            				Very Thin<?php echo ($elem_contour_temporal_os_VeryThin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr>
		     <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>Nasal :</b></td>  
                             <td>
                         	NL<?php echo ($elem_contour_nasal_os_NL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Thick<?php echo ($elem_contour_overall_os_Thick) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
            				Thin<?php echo ($elem_contour_nasal_os_Thin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Borderline<?php echo ($elem_contour_nasal_os_BL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;	
            				Very Thin<?php echo ($elem_contour_nasal_os_VeryThin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr>
		     <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; ">
                            	<b>GCC :</b></td>  
                             <td>
                         	NL<?php echo ($elem_contour_gcc_os_NL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Thick<?php echo ($elem_contour_overall_os_Thick) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;				
            				Thin<?php echo ($elem_contour_gcc_os_Thin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
				Borderline<?php echo ($elem_contour_gcc_os_BL) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;	
            				Very Thin<?php echo ($elem_contour_gcc_os_VeryThin) ? " (y) " : "(n)" ;?>&nbsp;&nbsp;
                            </td>
                        </tr>
                        <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; bordor-top:1px solid #CCC; ">
                            	<b>Symmetric :</b></td>  
                             <td style=" bordor-top:1px solid #CCC;">
								<?php echo ($elem_symmertric_os_Yes=="Yes")?$elem_symmertric_os_Yes:"";
									echo ($elem_symmertric_os_No=="No")?$elem_symmertric_os_Yes:"";
								 ?>
                            </td>
                        </tr>
		     <tr >
                            <td class="valignTop" style="padding-left:2px; height:<?php echo $trHeight;?>px; bordor-top:1px solid #CCC; ">
                            	<b>GPA :</b></td>  
                             <td style=" bordor-top:1px solid #CCC;">
								<?php echo ($elem_gpa_os_No=="No")?$elem_gpa_os_No:"";
									  echo ($elem_gpa_os_pos=="Possible")?$elem_gpa_os_pos:"";
									  echo ($elem_gpa_os_lp=="Like Progression")?$elem_gpa_os_lp:"";
								 ?>
                            </td>
                        </tr>		
                        <tr>
                        	<td colspan="2" style="border-top:1px solid #CCC;border-bottom:1px solid #CCC;">Interpretation</td>
                        </tr>                         
                         <tr>
                        	<td style="">Synthesis </td>
                            <td style="  width:410px;">
								<?php echo $elem_interpret_systhesis_os;?>
                            </td>
                        </tr> 

                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr> 
    <tr class="alignLeft alignMiddle"><td style="height:25px" colspan="2" >&nbsp;</td></tr>
    <tr class="alignLeft alignMiddle">
        <td style="height:20px;" colspan="2" class="txt_11b">Treatment/Prognosis : -</td>
    </tr>
    <tr>
        <td style="width:400px;">
            <b>Stable</b><?php echo (strpos($elem_interpretation_OD,"Stable")!==false) ? $yes : $no;?>&nbsp;&nbsp;<b>Not Improved</b><?php echo (strpos($elem_interpretation_OD,"Not Improve")!==false) ? $yes : $no;?>&nbsp;&nbsp;<b>Worse</b><?php echo (strpos($elem_interpretation_OD,"Worse")!==false) ? $yes : $no;?>&nbsp;&nbsp;<b>Likely progression</b><?php echo (strpos($elem_interpretation_OD,"Likely progression")!==false) ? $yes : $no;?>&nbsp;&nbsp;<b>Possible progression</b><?php echo (strpos($elem_interpretation_OD,"Possible progression")!==false) ? $yes : $no;?>
        </td>
         <td style="width:350px;">
            <b>Stable</b><?php echo (strpos($elem_interpretation_OS,"Stable")!==false) ? $yes : $no;?>&nbsp;&nbsp;<b>Not Improved</b><?php echo (strpos($elem_interpretation_OS,"Not Improve")!==false) ? $yes : $no;?>&nbsp;&nbsp;<b>Worse</b><?php echo (strpos($elem_interpretation_OS,"Worse")!==false) ? $yes : $no;?>&nbsp;&nbsp;<b>Likely progression</b><?php echo (strpos($elem_interpretation_OS,"Likely progression")!==false) ? $yes : $no;?>&nbsp;&nbsp;<b>Possible progression</b><?php echo (strpos($elem_interpretation_OS,"Possible progression")!==false) ? $yes : $no;?>
        </td>
    </tr>
<!-- 11 --*>   
  <tr>
      <td colspan="2" ><?php //echo $elem_comments_interp;."<br/>" ?> <?php /*if(strpos($elem_interpretation,"Glaucoma Stage")!==false) {  echo $yes;}else{echo $no;}*/ ?> Glaucoma Stage</td>      
  </tr>
  <tr>
      <td style="width:405px;"><?php if(strpos($elem_glaucoma_stage_opt_OD,"Unspecified")!==false || (empty($elem_glaucoma_stage_opt_OD))){echo $yes;}else{echo $no;}?> Unspecified&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_glaucoma_stage_opt_OD,"Mild")!==false) {echo $yes;}else{echo $no;}?> Mild&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_glaucoma_stage_opt_OD,"Moderate")!==false) {echo $yes;}else{echo $no;} ?>Moderate
      <br/>
      <?php if(strpos($elem_glaucoma_stage_opt_OD,"Severe")!==false) {echo $yes;}else{echo $no;} ?> Severe(not mild and moderate)&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_glaucoma_stage_opt_OD,"Indeterminate")!==false) {echo $yes;}else{echo $no;} ?> Indeterminate 
      </td>      
      <td style="width:350px;"><?php if(strpos($elem_glaucoma_stage_opt_OS,"Unspecified")!==false || (empty($elem_glaucoma_stage_opt_OS))){echo $yes;}else{echo $no;}?> Unspecified&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_glaucoma_stage_opt_OS,"Mild")!==false) {echo $yes;}else{echo $no;}?> Mild&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_glaucoma_stage_opt_OS,"Moderate")!==false) {echo $yes;}else{echo $no;} ?>Moderate
      <br/>
      <?php if(strpos($elem_glaucoma_stage_opt_OS,"Severe")!==false) {echo $yes;}else{echo $no;} ?> Severe(not mild and moderate)&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_glaucoma_stage_opt_OS,"Indeterminate")!==false) {echo $yes;}else{echo $no;} ?> Indeterminate </td>
  </tr>
<!-- 11 -->  
  <tr>
      <td colspan="2" style="width:100%px;" class="txt_11b" >Plan</td>    
  </tr>
  <tr>
      <td colspan="2" style="width:100%px;" ><?php if(strpos($elem_plan,"Pt informed of results by physician today")!==false) {echo $yes;}else{echo $no;} ?> Pt informed of results by physician today&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_plan,"to be called by technician")!==false) {echo $yes;}else{echo $no;} ?> to be called by technician&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_plan,"by letter")!==false) {echo $yes;}else{echo $no;} ?> by letter&nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_plan,"will inform next visit")!==false) {echo $yes;}else{echo $no;} ?> will inform next visit </td>
   </tr>
     <tr>
      <td colspan="2" style="width:100%px;" ><?php if(strpos($elem_plan,"Continue meds")!==false) {echo $yes;}else{echo $no;} ?> Continue meds
      &nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_plan,"Monitor findings")!==false) {echo $yes;}else{echo $no;} ?> Monitor findings
      &nbsp;&nbsp;&nbsp;&nbsp;<?php if(strpos($elem_plan,"Repeat test time")!==false) {  echo $yes;}else{echo $no;}?> Repeat test
      &nbsp;&nbsp;&nbsp;&nbsp;<?php echo (empty($elem_repeatTestVal1) || $elem_repeatTestVal1 == "Next visit") ? $elem_repeatTestVal1 : "";?>
      &nbsp;&nbsp;<?php echo ($elem_repeatTestVal2);?>
      &nbsp;&nbsp;<?php echo ($elem_repeatTestEye == "OU") ? $yes :  $no; "";?><span class="drak_purple_color" > OU</span> &nbsp;
       <?php echo ($elem_repeatTestEye == "OD") ? $yes : $no; "";?><span class="blue_color"> OD</span>&nbsp;&nbsp;
      <?php echo ($elem_repeatTestEye == "OS") ? $yes : $no; "";?> <span class="green_color">OS</span> </td>
   </tr>
  
<!-- 11 -->
    <tr>
        <td class="alignLeft valignTop" colspan="2"><div style="width:405px"><strong>Comments</strong>&nbsp;:&nbsp;<?php echo $elem_comments_od; ?></div></td>
	<td class="alignLeft valignTop" colspan="2"><div style="width:350px"><strong>Comments</strong>&nbsp;:&nbsp;<?php echo $elem_comments_os; ?></div></td>
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