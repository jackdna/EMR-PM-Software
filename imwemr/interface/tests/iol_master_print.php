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
File: iol_master_print.php
Purpose: This is a print version of IOL master test.
Access Type : Include file
*/
?>
<?php $testPrint =''; $testHeadFoot = '';

	$testCSS = '
		<style>
			table{ font-size:10px;}
			.tb_heading{
				font-size:11px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#000000;
				background-color:#BCD5E1;
			}
			.text_b{
				font-size:11px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#000;
				background-color:#BCD5E1;
			}
			.text{
				font-size:11px;
				font-family:Arial, Helvetica, sans-serif;
				background-color:#FFFFFF;
			}
		</style>';

		$testHeadFoot='
		<page backtop="9mm" backbottom="0.5mm" orientation="landscape">		
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
						<td colspan="3" align="center" style="width:1080px" class="text_b"><strong>IOL Master TEST</strong></td>
					</tr>
					<tr>
						<td class="text_b" align="left" style="width:250px">Ordered By '.$orderedBy.' on '.$elem_opidTestOrderedDate.'</td>
						<td style="width:300px;" class="text_b" align="center">Patient Name:&nbsp;&nbsp;'.$patientName.' - ('.$patient_id.')</td>
						<td class="text_b" align="right" style="width:200px">DOS:&nbsp;&nbsp;'.get_date_format($elem_examDate).'</td>
					</tr>
			</table>
		</page_header>
		';

$testPrint = $testCSS.$testHeadFoot;
$testPrint.= '
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td >
		<table style="width:100%;" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td>
					MR&nbsp;&nbsp;&nbsp;S&nbsp;'.$vis_mr_od_s.'&nbsp;&nbsp;&nbsp;&nbsp;C&nbsp;'.$vis_mr_od_c.'&nbsp;&nbsp;&nbsp;&nbsp;A&nbsp;'.$vis_mr_od_a.'
                    &nbsp;&nbsp;&nbsp;&nbsp;Vision&nbsp;'.$visionOD.'&nbsp;&nbsp;/&nbsp;&nbsp;'.$glareOD.'
				</td>
				<td style="width:1px"></td>
				<td>
					MR&nbsp;&nbsp;&nbsp;S&nbsp;'.$vis_mr_os_s.'&nbsp;&nbsp;&nbsp;&nbsp;C&nbsp;'.$vis_mr_os_c.'&nbsp;&nbsp;&nbsp;&nbsp;A&nbsp;'.$vis_mr_os_a.'
                    &nbsp;&nbsp;&nbsp;&nbsp;Vision&nbsp;'.$visionOS.'&nbsp;&nbsp;/&nbsp;&nbsp;'.$glareOS.'
				</td>				
			</tr>
			<tr>
				<td valign="top" style="width:524px; border:1px solid #000000;">
						<table style="width:auto" cellpadding="0" cellspacing="0" border="0">
						<tr><td colspan="4" style="color:#0000FF;font-size:14px;">
							OD&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								Performed By: '.$phyTechArray[$authUserId].'&nbsp;&nbsp;
								Date:'.get_date_format($today,"mm-dd-yyyy").'
						</td></tr>	
						<tr>
							<td style="width:49px; height:15px;"></td>
							<td style="width:155px"><strong>'.$autoSelectOD.'</strong></td>
							<td style="width:155px"><strong>'.$iolMasterSelectOD.'</strong></td>
							<td style="width:155px"><strong>'.$topographerSelectOD.'</strong></td>
						</tr>
						<tr>
							<td style="height:15px;"><strong>K1</strong></td>
							<td>'.$vis_ak_od_k.'&nbsp;x&nbsp;'.$vis_ak_od_x.'</td>
							<td>'.$k1IolMaster1OD.'&nbsp;x&nbsp;'.$k1IolMaster2OD.'</td>
							<td>'.$k1Topographer1OD.'&nbsp;x&nbsp;'.$k1Topographer2OD.'</td>
						</tr>
						<tr>
							<td style="height:15px;"><strong>K2</strong></td>';

						if($k2Auto2OD!='') $data1 = $k2Auto2OD; else $data1 = $vis_ak_od_x;
						$testPrint.= '	
							<td>'.$vis_ak_od_slash.'&nbsp;x&nbsp;'.$data1.'</td>
							<td>'.$k2IolMaster1OD.'&nbsp;x&nbsp;'.$k2IolMaster2OD.'</td>
							<td>'.$k2Topographer1OD.'&nbsp;x&nbsp;'.$k2Topographer2OD.'</td>
						</tr>
						<tr>
							<td style="height:15px;"><strong>CYL</strong></td>
							<td>'.$cyl1OD.'&nbsp;@&nbsp;'.$cylAuto2OD.'</td>
							<td>'.$cylIolMaster1OD.'&nbsp;@&nbsp;'.$cylIolMaster2OD.'</td>
							<td>'.$cylTopographer1OD.'&nbsp;@&nbsp;'.$cylTopographer2OD.'</td>
						</tr>
						<tr >
							<td style="height:15px;"><strong>AVE</strong></td>
							<td>'.$aveOD1.'</td>
							<td>'.$aveIolMasterOD.'</td>
							<td>'.$aveTopographerOD.'</td>
						</tr>
						<tr><td colspan="4" style="height:3px"></td></tr>
						<tr>
							<td style="height:15px;"><strong>AXIAL</strong></td>
							<td><strong>Contact</strong></td>
							<td><strong>Immersion</strong></td>
							<td><strong>IOL Master</strong></td>
						</tr>
						<tr>
							<td style="height:15px;"><strong>Length</strong></td>
							<td>'.$contactLengthOD.'</td>
							<td>'.$immersionLengthOD.'</td>
							<td>'.$iolMasterLengthOD.'</td>
						</tr>						
						<tr>
							<td style="height:15px;"><strong>Notes</strong></td>
							<td style="width:155px;">'.$contactNotesOD.'</td>
							<td style="width:155px;">'.$immersionNotesOD.'</td>
							<td style="width:155px;">'.$iolMasterNotesOD.'</td>
						</tr>						
						</table>
				</td>';
				
				
				// OS
				$testPrint.='<td style="width:1px;"></td>
							<td valign="top" style="width:524px; border:1px solid #000000;">
						<table style="width:auto;">';
						
						if(!$dateOS) $data2 = $today; else $data2 = $dateOS;
						if($k1Auto2OS!='') $data3 = $k1Auto2OS; else $data3 = $vis_ak_os_x;						
						$testPrint.='
						<tr><td colspan="4" style="color:#0000FF;font-size:14px;">
							OS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								Performed By: '.$phyTechArray[$provider_idOS].'&nbsp;&nbsp;
								Date:'.get_date_format($data2,"mm-dd-yyyy").'
						</td></tr>	
						<tr>
							<td style="width:49px;"></td>
							<td style="width:155px"><strong>'.$autoSelectOS.'</strong></td>
							<td style="width:155px"><strong>'.$iolMasterSelectOS.'</strong></td>
							<td style="width:155px"><strong>'.$topographerSelectOS.'</strong></td>
						</tr>
						<tr>
							<td style="height:10px;"><strong>K1</strong></td>
							<td>'.$vis_ak_os_k.'&nbsp;x&nbsp;'.$data3.'</td>
							<td>'.$k1IolMaster1OS.'&nbsp;x&nbsp;'.$k1IolMaster2OS.'</td>
							<td>'.$k1Topographer1OS.'&nbsp;x&nbsp;'.$k1Topographer2OS.'</td>
						</tr>
						<tr>
							<td style="height:15px;"><strong>K2</strong></td>';

						if($k2Auto2OS!='') $data4 = $k2Auto2OS; else $data4 = $vis_ak_os_x;
						$testPrint.= '	
							<td>'.$vis_ak_os_slash.'&nbsp;x&nbsp;'.$data4.'</td>
							<td>'.$k2IolMaster1OS.'&nbsp;x&nbsp;'.$k2IolMaster2OS.'</td>
							<td>'.$k2Topographer1OS.'&nbsp;x&nbsp;'.$k2Topographer2OS.'</td>
						</tr>
						<tr>
							<td style="height:15px;"><strong>CYL</strong></td>
							<td>'.$cyl1OS.'&nbsp;@&nbsp;'.$cylAuto2OS.'</td>
							<td>'.$cylIolMaster1OS.'&nbsp;@&nbsp;'.$cylIolMaster2OS.'</td>
							<td>'.$cylTopographer1OS.'&nbsp;@&nbsp;'.$cylTopographer2OS.'</td>
						</tr>
						<tr>
							<td style="height:15px;"><strong>AVE</strong></td>
							<td>'.$aveOS1.'</td>
							<td>'.$aveIolMasterOS.'</td>
							<td>'.$aveTopographerOS.'</td>
						</tr>
						<tr><td colspan="4" style="height:3px"></td></tr>
						<tr>
							<td style="height:15px;"><strong>AXIAL</strong></td>
							<td><strong>Contact</strong></td>
							<td><strong>Immersion</strong></td>
							<td><strong>IOL Master</strong></td>
						</tr>
						<tr>
							<td style="height:15px;"><strong>Length</strong></td>
							<td>'.$contactLengthOS.'</td>
							<td>'.$immersionLengthOS.'</td>
							<td>'.$iolMasterLengthOS.'</td>
						</tr>						
						<tr>
							<td style="height:15px;"><strong>Notes</strong></td>
							<td style="width:155px;">'.$contactNotesOS.'</td>
							<td style="width:155px;">'.$immersionNotesOS.'</td>
							<td style="width:155px;">'.$iolMasterNotesOS.'</td>
						</tr>						
						</table>';

			$testPrint.='			
				</td>
			</tr>';

		$testPrint.='	
		</table>
		</td>
	</tr>
	</table>';
/// PARTITION			

			// SECOND PART START OD
			$firstCol = "style=\"width:100px; height:15px;\"";
			$width ="style=\"width:104px; text-align:left; height:15px;\"";
			$col2Width = "style=\"width:210px; text-align:left; height:15px;\"";
			$col3Width = "style=\"width:320px; text-align:left; height:15px;\"";
			$col4Width = "style=\"width:430px; text-align:left; height:15px;\"";
			$col5Width = "style=\"width:530px; text-align:left; height:15px;\"";
			
			if($powerIolOD!='') $data5=$powerIolOD; else $data5="POWER";
			if($holladayOD!='') $data6=$holladayOD; else $data6="Holladay";
			if($srk_tOD!='') $data7=$srk_tOD; else $data7="SRK-T";
			if($hofferOD!='') $data8=$hofferOD; else $data8="HOFFER"; 
			if($notesOD=='') $data9="Notes..."; else $data9=$notesOD;
			
			$testPrint.='<table border="0" cellpadding="0" cellspacing="0">
			<tr><td colspan="3" style="height:2px"></td></tr>
			<tr>
				<td valign="top" style="width:525px; border:1px solid #000000;">';
			
			$testPrint.='	
						<table border="0" cellpadding="0" cellspacing="0">
						<tr><td colspan="5" style="color:#0000FF;font-size:14px; width:500px;">
							OD&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								Performed By: '.$phyArray[$provider_idOD].'
						</td></tr>	
						<tr>
							<td '.$firstCol.'>IOL</td>
							<td '.$width.'><strong>'.$data5.'</strong></td>
							<td '.$width.'><strong>'.$data6.'</strong></td>
							<td '.$width.'><strong>'.$data7.'</strong></td>
							<td '.$width.'><strong>'.$data8.'</strong></td>
						</tr>
						<tr>
							<td '.$firstCol.'><strong>'.$providerLensesArrOD[0].'</strong></td>
							<td '.$width.'>'.$iol1PowerOD.'</td>
							<td '.$width.'>'.$iol1HolladayOD.'</td>
							<td '.$width.'>'.$iol1srk_tOD.'</td>
							<td '.$width.'>'.$iol1HofferOD.'</td>
						</tr>
						<tr>
							<td '.$firstCol.'><strong>'.$providerLensesArrOD[1].'</strong></td>
							<td '.$width.'>'.$iol2PowerOD.'</td>
							<td '.$width.'>'.$iol2HolladayOD.'</td>
							<td '.$width.'>'.$iol2srk_tOD.'</td>
							<td '.$width.'>'.$iol2HofferOD.'</td>
						</tr>
						<tr>
							<td '.$firstCol.'><strong>'.$providerLensesArrOD[2].'</strong></td>
							<td '.$width.'>'.$iol3PowerOD.'</td>
							<td '.$width.'>'.$iol3HolladayOD.'</td>
							<td '.$width.'>'.$iol3srk_tOD.'</td>
							<td '.$width.'>'.$iol3HofferOD.'</td>
						</tr>
						<tr>
							<td '.$firstCol.'><strong>'.$providerLensesArrOD[3].'</strong></td>
							<td '.$width.'>'.$iol4PowerOD.'</td>
							<td '.$width.'>'.$iol4HolladayOD.'</td>
							<td '.$width.'>'.$iol4srk_tOD.'</td>
							<td '.$width.'>'.$iol4HofferOD.'</td>
						</tr>
						<tr>
							<td '.$firstCol.'><strong>Cell Count</strong>&nbsp;:&nbsp;'.$cellCountOD.'</td>
							<td '.$width.'><strong>Pachymetry</strong>&nbsp;:&nbsp;'.$pachymetryValOD.'&nbsp;&nbsp;'.$pachymetryCorrecOD.'</td>
							<td '.$width.'><strong>Corneal Diam</strong>&nbsp;:&nbsp;'.$cornealDiamOD.'</td>
							<td '.$width.'><strong>Dominant Eye</strong>&nbsp;:&nbsp;'.$dominantEyeOD.'</td>
							<td '.$width.'><strong>Pupil Size</strong>&nbsp;:&nbsp;'.$pupilSize1OD.'&nbsp;/&nbsp;'.$pupilSize2OD.'<br>&nbsp;&nbsp;Un-Dilated/Dilated</td>
						</tr>						
						<tr>
							<td colspan="5"><div '.$col5Width.'>'.$data9.'</div></td>
						</tr>';
						
																					
						$testPrint.='
						<tr>
							<td colspan="5"><div '.$col5Width.'><strong>CC:</strong>&nbsp;&nbsp;&nbsp;Scheduled for Intraocular lens implant A/Scan reviewed and IOL Selected.</div></td>
						</tr>';
						
						if($cataractOD==1) $data10='(yes)'; else $data10='(no)';
						if($astigmatismOD==1) $data11='(yes)'; else $data11='(no)';
						if($myopiaOD==1) $data12='(yes)'; else $data12='(no)';
						
						if($elem_proc_od_1=='checked') $data13='(yes)'; else $data13='(no)';
						if($elem_proc_od_2=='checked') $data14='(yes)'; else $data14='(no)';
						if($elem_proc_od_3=='checked') $data15='(yes)'; else $data15='(no)';

						if($elem_anes_od_1=='checked') $data16='(yes)'; else $data16='(no)';
						if($elem_anes_od_2=='checked') $data17='(yes)'; else $data17='(no)';
						
						if($elem_visc_od_1=='checked') $data18='(yes)'; else $data18='(no)';
						if($elem_visc_od_2=='checked') $data19='(yes)'; else $data19='(no)';
						if($elem_visc_od_3=='checked') $data20='(yes)'; else $data20='(no)';
						if($elem_visc_od_4=='checked') $data21='(yes)'; else $data21='(no)';						

						
						$testPrint.='
						<tr>
							<td '.$firstCol.'><strong>Assessment:</strong></td>
							<td '.$width.'>Cataract'.$data10.'</td>
							<td '.$width.'>Astigmatism'.$data11.'</td>
							<td colspan="2"><div '.$col2Width.'>Myopia'.$data12.'</div></td>
						</tr>';
						
						$testPrint.='
						<tr>
							<td '.$firstCol.'><strong>Plan:</strong></td>
							<td '.$width.'>'.$PlanOD.'</td>
							<td colspan="3"><div '.$col3Width.'><strong>Date of Surgery:</strong>&nbsp;'.$sur_dateOD.'</div></td>
						</tr>';

						$testPrint.='
						<tr>
							<td '.$firstCol.'><strong>Procedure:</strong></td>
							<td '.$width.'>Phaco'.$data13.'</td>
							<td '.$width.'>Complex Phaco'.$data14.'</td>
							<td colspan="2"><div '.$col2Width.'>Combined Procedure'.$data15.'</div></td>
						</tr>';

						$testPrint.='	
						<tr >
							<td '.$firstCol.'><strong>Anesthesia:</strong></td>
							<td '.$width.'>Local'.$data16.'</td>
							<td colspan="3"><div '.$col3Width.'>Topical'.$data17.'</div></td>
						</tr>';

						$testPrint.='
						<tr>
							<td '.$firstCol.'><strong>Viscoelastic:</strong></td>
							<td colspan="4"><div '.$col4Width.'>Discovisc'.$data18.'&nbsp;Viscoat'.$data19.'&nbsp;
							Amvisc Plus'.$data20.'&nbsp;Healon'.$data21.'&nbsp;&nbsp;'.$elem_visc_od_other.'
							</div></td>
						</tr>';

						
						$data22 = ($lriOD==1) ? '(yes)' : '(no)';
						$data23 = ($dlOD==1) ? '(yes)' : '(no)';
						$data24 = ($synechiolysisOD==1) ? '(yes)' : '(no)';
						$data25 = ($irishooksOD==1) ? '(yes)' : '(no)';	
						$data26 = ($trypanblueOD==1) ? '(yes)' : '(no)';	
						$data27 = ($flomaxOD==1) ? '(yes)' : '(no)';	

						$testPrint.='
						<tr>
							<td colspan="5"><div '.$col5Width.'>LRI'.$data22.'&nbsp;&nbsp;&nbsp;DL'.$data23.'&nbsp;&nbsp;&nbsp;Synechiolysis'.$data24.'&nbsp;&nbsp;&nbsp;IRIS Hooks'.$data25.'&nbsp;&nbsp;&nbsp;Trypan Blue'.$data26.'&nbsp;&nbsp;&nbsp;Pt. On Flomax'.$data27.'</div></td>
						</tr>';
						
						$data28 = ($elem_opts_od_1=='checked') ? '(yes)' : '(no)';
						$data29 = ($elem_opts_od_2=='checked') ? '(yes)' : '(no)';
						$data30 = ($elem_opts_od_3=='checked') ? '(yes)' : '(no)';
						$data31 = ($elem_opts_od_4=='checked') ? '(yes)' : '(no)';	
						$testPrint.='																								
						<tr>
							<td colspan="5"><div '.$col5Width.'>Malyugin ring'.$data28.'&nbsp;&nbsp;&nbsp;Shugarcaine'.$data29.'&nbsp;&nbsp;&nbsp;Capsule tension rings'.$data30.'&nbsp;&nbsp;&nbsp;
							IOL Cutter'.$data31.'&nbsp;&nbsp;&nbsp;'.$elem_opts_od_other.'</div></td>
						</tr>';
						
						$data32 = ($elem_iol2dn_od_1=='checked') ? '(yes)' : '(no)';
						$testPrint.='
						<tr>
							<td colspan="5"><div '.$col5Width.'><strong>IOL master to be done on day of surgery:</strong>'.$data32.'</div></td>
						</tr>';
						$testPrint.='
						<tr>
							<td colspan="5"><div '.$col5Width.'><strong>Notes:</strong>&nbsp;&nbsp;&nbsp;'.$notesAssesmentPlansOD.'</div></td>
						</tr>
						';	

						if($lriOD==1){
							$testPrint.='
							<tr>
								<td colspan="5"><div '.$col5Width.'><strong>Cuts:</strong>&nbsp;&nbsp;&nbsp;'.$cutsOD.'&nbsp;&nbsp;&nbsp;&nbsp;
								<b>Length:</b>&nbsp;&nbsp;&nbsp;'.$lengthOD.$lengthTypeOD.'&nbsp;&nbsp;&nbsp;&nbsp;
								<b>Axis:</b>&nbsp;&nbsp;'.$axisOD.'
								</div></td>
							</tr>';	
							
							$data33 = ($superiorOD==1) ? '(yes)' : '(no)';
							$data34 = ($inferiorOD==1) ? '(yes)' : '(no)';
							$data35 = ($nasalOD==1) ? '(yes)' : '(no)';
							$data36 = ($temporalOD==1) ? '(yes)' : '(no)';
							$data37 = ($STOD==1) ? '(yes)' : '(no)';
							$data38 = ($SNOD==1) ? '(yes)' : '(no)';
							$data39 = ($ITOD==1) ? '(yes)' : '(no)';
							$data40 = ($INOD==1) ? '(yes)' : '(no)';
							$testPrint.='
							<tr>
								<td colspan="5"><div '.$col5Width.'>superior'.$data33.'&nbsp;&nbsp;&nbsp;inferior'.$data34.'&nbsp;&nbsp;&nbsp;nasal'.$data35.'&nbsp;&nbsp;&nbsp;temporal'.$data36.'&nbsp;&nbsp;&nbsp;
								ST'.$data37.'&nbsp;&nbsp;&nbsp;SN'.$data38.'&nbsp;&nbsp;&nbsp;IT'.$data39.'&nbsp;&nbsp;&nbsp;IN'.$data40.'
								</div></td>
							</tr>';	
						}
										
						$testPrint.='
						</table>';
					
				$testPrint.='</td>';

				// SECOND PAGE
				// SECOND PART OS
				if($powerIolOS!='') $data41=$powerIolOS; else $data41="POWER";
				if($holladayOS!='') $data42=$holladayOS; else $data42="Holladay";
				if($srk_tOS!='') $data43=$srk_tOS; else $data43="SRK-T";
				if($hofferOS!='') $data44=$hofferOS; else $data44="HOFFER"; 
				if($notesOS=='') $data45="Notes..."; else $data45=$notesOS;
				
				$testPrint.='<td style="width:1px"></td>
						<td valign="top" style="width:525px; border:1px solid #000000;">';
				$testPrint.='	
						<table style="width:auto" cellpadding="0" cellspacing="0" border="0">
						<tr><td colspan="5" style="color:#0000FF;font-size:14px; width:500px;">
							OS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								Performed By: '.$phyArray[$performedIolOS].'
						</td></tr>	
						<tr>
							<td '.$firstCol.'>IOL</td>
							<td '.$width.'><strong>'.$data41.'</strong></td>
							<td '.$width.'><strong>'.$data42.'</strong></td>
							<td '.$width.'><strong>'.$data43.'</strong></td>
							<td '.$width.'><strong>'.$data44.'</strong></td>
						</tr>
						<tr>
							<td '.$firstCol.'><strong>'.$providerLensesArrOS[0].'</strong></td>
							<td '.$width.'>'.$iol1PowerOS.'</td>
							<td '.$width.'>'.$iol1HolladayOS.'</td>
							<td '.$width.'>'.$iol1srk_tOS.'</td>
							<td '.$width.'>'.$iol1HofferOS.'</td>
						</tr>
						<tr>
							<td '.$firstCol.'><strong>'.$providerLensesArrOS[1].'</strong></td>
							<td '.$width.'>'.$iol2PowerOS.'</td>
							<td '.$width.'>'.$iol2HolladayOS.'</td>
							<td '.$width.'>'.$iol2srk_tOS.'</td>
							<td '.$width.'>'.$iol2HofferOS.'</td>
						</tr>
						<tr>
							<td '.$firstCol.'><strong>'.$providerLensesArrOS[2].'</strong></td>
							<td '.$width.'>'.$iol3PowerOS.'</td>
							<td '.$width.'>'.$iol3HolladayOS.'</td>
							<td '.$width.'>'.$iol3srk_tOS.'</td>
							<td '.$width.'>'.$iol3HofferOS.'</td>
						</tr>
						<tr>
							<td '.$firstCol.'><strong>'.$providerLensesArrOS[3].'</strong></td>
							<td '.$width.'>'.$iol4PowerOS.'</td>
							<td '.$width.'>'.$iol4HolladayOS.'</td>
							<td '.$width.'>'.$iol4srk_tOS.'</td>
							<td '.$width.'>'.$iol4HofferOS.'</td>
						</tr>
						<tr >
							<td '.$firstCol.'><strong>Cell Count</strong>&nbsp;:&nbsp;'.$cellCountOS.'</td>
							<td '.$width.'><strong>Pachymetry</strong>&nbsp;:&nbsp;'.$pachymetryValOS.'&nbsp;&nbsp;'.$pachymetryCorrecOS.'</td>
							<td '.$width.'><strong>Corneal Diam</strong>&nbsp;:&nbsp;'.$cornealDiamOS.'</td>
							<td '.$width.'><strong>Dominant Eye</strong>&nbsp;:&nbsp;'.$dominantEyeOS.'</td>
							<td '.$width.'><strong>Pupil Size</strong>&nbsp;:&nbsp;'.$pupilSize1OS.'&nbsp;/&nbsp;'.$pupilSize2OS.'<br>&nbsp;&nbsp;Un-Dilated/Dilated</td>
						</tr>						
						<tr>
							<td colspan="5"><div '.$col5Width.'>'.$data45.'</div></td>
						</tr>';
						
																					
						$testPrint.='
						<tr>
							<td colspan="5"><div '.$col5Width.'><strong>CC:</strong>&nbsp;&nbsp;&nbsp;Scheduled for Intraocular lens implant A/Scan reviewed and IOL Selected.</div></td>
						</tr>';
						
						if($cataractOS==1) $data46='(yes)'; else $data46='(no)';
						if($astigmatismOS==1) $data47='(yes)'; else $data47='(no)';
						if($myopiaOS==1) $data48='(yes)'; else $data48='(no)';
						
						if($elem_proc_os_1=='checked') $data49='(yes)'; else $data49='(no)';
						if($elem_proc_os_2=='checked') $data50='(yes)'; else $data50='(no)';
						if($elem_proc_os_3=='checked') $data51='(yes)'; else $data21='(no)';

						if($elem_anes_os_1=='checked') $data52='(yes)'; else $data52='(no)';
						if($elem_anes_os_2=='checked') $data53='(yes)'; else $data53='(no)';
						
						if($elem_visc_os_1=='checked') $data54='(yes)'; else $data54='(no)';
						if($elem_visc_os_2=='checked') $data55='(yes)'; else $data55='(no)';
						if($elem_visc_os_3=='checked') $data56='(yes)'; else $data56='(no)';
						if($elem_visc_os_4=='checked') $data57='(yes)'; else $data57='(no)';						

						
						$testPrint.='
						<tr>
							<td '.$firstCol.'><strong>Assessment:</strong></td>
							<td '.$width.'>Cataract'.$data46.'</td>
							<td '.$width.'>Astigmatism'.$data47.'</td>
							<td colspan="2"><div '.$col2Width.'>Myopia'.$data48.'</div></td>
						</tr>';
						
						$testPrint.='
						<tr>
							<td '.$firstCol.'><strong>Plan:</strong></td>
							<td '.$width.'>'.$lensesProviderOSArray[$selecedIOLsOS].'</td>
							<td colspan="3"><div '.$col3Width.'><strong>Date of Surgery:</strong>&nbsp;'.$sur_dateOS.'</div></td>
						</tr>';

						$testPrint.='
						<tr>
							<td '.$firstCol.'><strong>Procedure:</strong></td>
							<td '.$width.'>Phaco'.$data49.'</td>
							<td '.$width.'>Complex Phaco'.$data50.'</td>
							<td colspan="2"><div '.$col2Width.'>Combined Procedure'.$data51.'</div></td>
						</tr>';

						$testPrint.='	
						<tr>
							<td '.$firstCol.'><strong>Anesthesia:</strong></td>
							<td '.$width.'>Local'.$data52.'</td>
							<td colspan="3"><div '.$col3Width.'>Topical'.$data53.'</div></td>
						</tr>
						<tr>
							<td '.$firstCol.'><strong>Viscoelastic:</strong></td>
							<td colspan="4"><div '.$col4Width.'>Discovisc'.$data54.'Viscoat'.$data55.'&nbsp;
							Amvisc Plus'.$data56.'&nbsp;Healon'.$data57.'&nbsp;&nbsp;'.$elem_visc_os_other.'</div>
							</td>
						</tr>';

						$data58 = ($lriOS==1) ? '(yes)' : '(no)';
						$data59 = ($dlOS==1) ? '(yes)' : '(no)';
						$data60 = ($synechiolysisOS==1) ? '(yes)' : '(no)';
						$data61 = ($irishooksOS==1) ? '(yes)' : '(no)';	
						$data62 = ($trypanblueOS==1) ? '(yes)' : '(no)';	
						$data63 = ($flomaxOS==1) ? '(yes)' : '(no)';	
						$testPrint.='																								
						<tr>
							<td colspan="5"><div '.$col5Width.'>LRI'.$data58.'&nbsp;&nbsp;&nbsp;DL'.$data59.'&nbsp;&nbsp;&nbsp;Synechiolysis'.$data60.'&nbsp;&nbsp;&nbsp;
							IRIS Hooks'.$data61.'&nbsp;&nbsp;&nbsp;Trypan Blue'.$data62.'&nbsp;&nbsp;&nbsp;Pt. On Flomax'.$data63.'</div></td>
						</tr>';

						$data64 = ($elem_opts_os_1=='checked') ? '(yes)' : '(no)';
						$data65 = ($elem_opts_os_2=='checked') ? '(yes)' : '(no)';
						$data66 = ($elem_opts_os_3=='checked') ? '(yes)' : '(no)';
						$data67 = ($elem_opts_os_4=='checked') ? '(yes)' : '(no)';	
						$testPrint.='																								
						<tr>
							<td colspan="5" ><div '.$col5Width.'>Malyugin ring'.$data64.'&nbsp;&nbsp;&nbsp;Shugarcaine'.$data65.'&nbsp;&nbsp;&nbsp;Capsule tension rings'.$data66.'&nbsp;&nbsp;&nbsp;
							IOL Cutter'.$data67.'&nbsp;&nbsp;&nbsp;'.$elem_opts_os_other.'</div></td>
						</tr>';
						
						$data68 = ($elem_iol2dn_os_1=='checked') ? '(yes)' : '(no)';
						$testPrint.='
						<tr>
							<td colspan="5"><div '.$col5Width.'><strong>IOL master to be done on day of surgery:</strong>'.$data68.'</div></td>
						</tr>';
						$testPrint.='
						<tr>
							<td colspan="5"><div '.$col5Width.'><strong>Notes:</strong>&nbsp;&nbsp;&nbsp;'.$notesAssesmentPlansOS.'</div></td>
						</tr>
						';	

						if($lriOS==1){
							$testPrint.='
							<tr>
								<td colspan="5"><div '.$col5Width.'><strong>Cuts:</strong>&nbsp;&nbsp;&nbsp;'.$cutsOS.'&nbsp;&nbsp;&nbsp;&nbsp;
								<b>Length:</b>&nbsp;&nbsp;&nbsp;'.$lengthOS.$lengthTypeOS.'&nbsp;&nbsp;&nbsp;&nbsp;
								<b>Axis:</b>&nbsp;&nbsp;'.$axisOS.'
								</div></td>
							</tr>';	
							
							$data69 = ($superiorOS==1) ? '(yes)' : '(no)';
							$data70 = ($inferiorOS==1) ? '(yes)' : '(no)';
							$data71 = ($nasalOS==1) ? '(yes)' : '(no)';
							$data72 = ($temporalOS==1) ? '(yes)' : '(no)';
							$data73 = ($STOS==1) ? '(yes)' : '(no)';
							$data74 = ($SNOS==1) ? '(yes)' : '(no)';
							$data75 = ($ITOS==1) ? '(yes)' : '(no)';
							$data76 = ($INOS==1) ? '(yes)' : '(no)';
							$testPrint.='
							<tr>
								<td colspan="5"><div '.$col5Width.'>superior'.$data69.'&nbsp;&nbsp;&nbsp;inferior'.$data70.'&nbsp;&nbsp;&nbsp;nasal'.$data71.'&nbsp;&nbsp;&nbsp;temporal'.$data72.'&nbsp;&nbsp;&nbsp;
								ST'.$data73.'&nbsp;&nbsp;&nbsp;SN'.$data74.'&nbsp;&nbsp;&nbsp;IT'.$data75.'&nbsp;&nbsp;&nbsp;IN'.$data76.'
								</div></td>
							</tr>';	
						}
						$testPrint.='
						</table>';				
				$testPrint.='</td></tr>';
			// END SECOND PART OS
			
			$data77 = (empty($signedById)) ? $authUserId : $signedById;			
			$data78 = (empty($signedByOSId)) ? $authUserId : $signedByOSId;
			$testPrint.='</table>';
			$testPrint.='<table><tr><td ><b>Physician Name:</b>&nbsp;&nbsp;'.$phyArray[$data77].'</td>
			<td style="width:1px"></td>
			<td ><b>Physician Name:</b>&nbsp;&nbsp;'.$phyArray[$data78].'</td>
			</tr>'.$sign_path_print.'</table></page>';
			
/*			$testPrint.='
			<page>'.$_SESSION['pdfData'].'</page>';
*/			
$strPrint = $STRPRINT;
if($strPrint!='') {
	$testPrint.= $testHeadFoot.$strPrint;
	$testPrint.= "</page>";
}
file_put_contents($final_html_file_name_path.".html",$testPrint);
?>