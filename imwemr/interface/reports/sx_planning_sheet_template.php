<?php
	$printTemplate	=	'
		<style>
			table { border-collapse : collapse; }	
			td { border:0px; }
			.mainTable { border:0px; width:700px; font-size:14px; vertical-align:top;  margin-top:20px; }
			.font_12 { font-size:12px; }
			.font_14 { font-size:14px; }
			.font_16 { font-size:16px;}
			.font_30 { font-size:30px; }
			.height_10 { height:10px; min-height:10px; }
			.shadow { box-shadow:2px 2px 5px #ddd}
			.bordered { border:solid 1px #333; }
			.PL_10 { padding-left:10px;}
			.PR_10 { padding-right:10px;}
			.borderBottom{ border-bottom : solid 1px #333; }
			.borderRight{ border-right : solid 1px #333; }
			.borderLeft{ border-left : solid 1px #333; }
			.borderTop{ border-top: solid 1px #333; }
			.borderBottomLight{ border-bottom : solid 1px #eee; }
			.spacediff { word-spacing: 10px;}
		</style>
		<table align="center" class="mainTable" cellspacing="0" cellpadding="0" >
			<tr>
				<td colspan="3" style="width:490px; padding:5px 0px" class="bordered shadow "><b>Sx Plan Sheet</b></td>
				<td style="width:210px; padding:5px 0px;" class="bordered shadow "><b>Created On:</b> {CREATEDATE}</td>
			</tr>
			<tr>
				<td colspan="4" style="width:700px; padding:5px 0px" class="bordered shadow ">
					<table cellspacing="0" cellpadding="0">
						<tr>
							<td style="width: 225px;" ><b>Patient Name:</b> {PATIENT_NAME}</td>
							<td style="width: 165px; " ><b>DOB:</b> {DOB}</td>
							<td style="width: 100px; "><b>Age:</b> {AGE}</td>
							<td style="width: 210px;" ><b>Account:</b> {PatientId}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td colspan="4" style="width:700px;"></td></tr>
			<tr>
				<td style="width:150px; "  valign="top" align="center"   >
					<table cellspacing="0" cellpadding="0" style="height:60px;" style="width:100%">
						<tr>
							<td style="width:20%; height:60px; " align="center" class="bordered" valign="middle" ><b class="font_16">Eye</b></td>
							<td style="width:80%; background-color:#ddd;  "  class="bordered font_30" valign="middle" >{SITE}</td>
						</tr>	
					</table>	
				</td>
				<td style="width:50px; " valign="top"  >
					<table cellspacing="0" cellpadding="0" style="width:100%">
						<tr>
							<td style="height:60px; width:99%; background-color:#ddd;" valign="bottom" align="center" class="bordered"><b>REF : </b></td>
						</tr>
					</table>		
				</td>
				
				<td style="width:250px" valign="top" >
					<table cellspacing="0" cellpadding="0" style="width:100%;" align="center" >
						<tr>
							<td style="height:26px; width:40%; " valign="bottom" class="bordered">&nbsp;<b>Refraction</b></td>
							<td style="height:26px; width:50%;  background-color:#ddd; " valign="middle" class="bordered">
								&nbsp;&nbsp;Dominant Eye&nbsp;<b class="font_14">{DOMINANT_EYE}</b>
							</td>
						</tr>
						<tr><td style="height:26px; width:100%;" colspan="2" valign="bottom" class="bordered font_14"><b>{REFRACTION}</b></td></tr>
					</table>
				</td>
								
				<td style="width:250px; " valign="top"  >
					<table cellspacing="0" cellpadding="0" style="width:100%" >
						<tr>
							<td style="width:100%" >
								<table cellspacing="0" cellpadding="0"  >
									<tr>
										<td style="height:25px; width:100%;" valign="bottom" class="bordered">&nbsp;Other Eye Refraction</td>
										
										</tr>
										<tr><td style="height:26px; width:100%;" valign="bottom" class="bordered font_12">{OTHER_EYE_REFRACTION}</td></tr>
									</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td colspan="4" style="width:100%"></td></tr>
			<tr>
				<td style="width:200px; height:60px;"  valign="top" colspan="2" >
					<table cellspacing="0" cellpadding="0" style="width:100%">
						<tr>
							<td class="bordered" style="width:100%; background-color:#efefef; height:20px;" colspan="2" valign="middle" ><b>Surgery</b></td>
						</tr>
						<tr>
							<td class="bordered" style="width:30%; background-color:#efefef; height:20px;" valign="middle" ><b>Date</b></td>
							<td class="borderBottom borderRight" style="width:70%; height:20px;" valign="middle">&nbsp;{DOS}</td>
						</tr>
						<tr>
							<td  class="bordered" style="width:30%; background-color:#efefef; height:20px;" valign="middle"><b>Time</b></td>
							<td  class="borderBottom borderRight" style="width:70%; height:20px;" valign="middle">&nbsp;{SURGERY_TIME}</td>
						</tr>
					</table>	
				</td>
				
				<td style="width:250px;"  valign="top"  >
					<table cellspacing="0" cellpadding="0" align="center" style="width:100%">
						<tr>
							<td class="bordered" style="width:100%; background-color:#efefef; height:20px;" colspan="4" valign="middle" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>K\'s </b></td>
						</tr>
						<tr >
							<td class="bordered" style="width:25%; height:20px;" align="center" valign="middle"><b>Flat</b></td>
							<td class="bordered" style="width:25%; height:20px;" align="center" valign="middle"><b>Steep</b></td>
							<td class="bordered" style="width:25%; height:20px;" align="center" valign="middle"><b>Axis</b></td>
							<td class="bordered" style="width:25%; height:20px;" align="center" valign="middle"><b>Cyl</b></td>
							
						</tr>
						<tr>
							<td class="bordered" style="width:25%; height:20px;"  align="center" valign="middle">{K_FLAT}</td>
							<td class="bordered" style="width:25%; height:20px;"  align="center" valign="middle">{K_STEEP}</td>
							<td class="bordered" style="width:25%; height:20px;" align="center" valign="middle">{K_AXIS}</td>
							<td class="bordered" style="width:25%; height:20px;" align="center" valign="middle">{K_CYL}</td>
						</tr>
						
						
					</table>	
				</td>
								
				<td style="width:250px; "  valign="top" >
					<table cellspacing="0" cellpadding="0" style="width:100%">
						<tr>
							<td class="bordered" style="width:100%; background-color:#efefef; height:20px;" valign="middle" colspan="4"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other Eye K\'s </strong></td>
						</tr>
						<tr>
							<td class="bordered" style="width:25%; height:20px;" align="center" valign="middle" >Flat</td>
							<td class="bordered" style="width:25%; height:20px;" align="center" valign="middle">Steep</td>
							<td class="bordered" style="width:25%; height:20px;" align="center" valign="middle">Axis</td>
							<td class="bordered" style="width:25%; height:20px;" align="center" valign="middle">Cyl</td>
							
						</tr>
						<tr>
							<td class="bordered font_12" style="width:25%; height:20px;"  align="center" valign="middle">{OTHER_K_FLAT}</td>
							<td class="bordered font_12" style="width:25%; height:20px;"  align="center" valign="middle">{OTHER_K_STEEP}</td>
							<td class="bordered font_12" style="width:25%; height:20px;" align="center" valign="middle">{OTHER_K_AXIS}</td>
							<td class="bordered font_12" style="width:25%; height:20px;" align="center" valign="middle">{OTHER_K_CYL}</td>
						</tr>
						
						
					</table>	
				</td>
				
			</tr>	
			<tr><td colspan="4" style="width:100%"></td></tr>
			<tr>
				<td style="width:220px; "  valign="top" colspan="2"  >
					<table cellpadding="0" cellspacing="0" style="width:100%">
						<tr><td style="width:100%; height:20px; background-color:#efefef; " valign="middle" align="left" class="bordered">&nbsp;<b>Surgeon</b></td></tr>
						<tr><td style="width:100%; height:48px; " valign="middle" align="left" class="bordered">{SURGEON_NAME}</td></tr>
					</table>
				</td>
				<td style="width:480px; "  valign="top" colspan="2"  >
					<table cellpadding="0" cellspacing="0" style="width:100%">
						<tr><td colspan="2" style="width:100%; height:20px; background-color:#efefef; padding-left:5px; " valign="middle" align="left" class="bordered"><b>Procedure</b></td></tr>
						<tr><td style="width:20%; height:20px; padding-left:5px;" valign="middle" align="left" class="bordered">Primary</td><td style="width:80%; height:20px;padding-left:5px;" valign="middle" align="left" class="bordered">{PRI PROC}</td></tr>
						<tr><td style="width:20%; height:20px; padding-left:5px;" valign="middle" align="left" class="bordered">Secondary</td><td style="width:80%; height:20px;padding-left:5px;" valign="middle" align="left" class="bordered">{SEC PROC}</td></tr>
					</table>
				</td>
				
			</tr>
			<tr><td colspan="4" style="width:100%"></td></tr>
			<tr>
				<td style="width:700px; " valign="top" colspan="4" >
					<table cellspacing="0" cellpadding="0" style="width:100%">
						<tr>
							<td class="bordered borderRight" style="width:300px; background-color:#efefef; height:20px;" valign="middle" ><b>Lenses</b></td>
							<td class="bordered" style="width:400px; background-color:#efefef; height:20px;"  align="center" valign="middle">
								PrePlan Lens(Traditional IOL) differs from Primary Lens
							</td>
						</tr>
						<tr>
							<td colspan="2" style="width:700px; " valign="top"  >
								<table cellspacing="0" cellpadding="0" align="left" style="width:700px;border-collapse:collapse">
									<tr>
										<td style="width:77px; height:20px;" align="center" valign="middle" class="bordered"><b>Lens</b></td>
										<td style="width:77px; height:20px;" align="center" valign="middle" class="bordered"><b>Power</b></td>
										<td style="width:77px; height:20px;" align="center" valign="middle" class="bordered"><b>Cyl</b></td>
										<td style="width:77px; height:20px;" align="center" valign="middle" class="bordered"><b>Axis</b></td>
										<td style="width:77px; height:20px;" align="center" valign="middle" class="bordered"><b>Used</b></td>
										<td style="width:77px; height:20px;" align="center" valign="middle" class="bordered"><b>Target</b></td>
										<td style="width:77px; height:20px;" align="center" valign="middle" class="bordered"><b>Predicted</b></td>
										<td style="width:77px; height:20px;" align="center" valign="middle" class="bordered"><b>ACD/AL(%)</b></td>
										<td style="width:77px; height:20px;" align="center" valign="middle" class="bordered"><b>S/P CRS</b></td>
									</tr>
									{EXTRA_LENS_ROW}
								</table>
							</td>
						</tr>
					</table>	
				</td>
			</tr>
			<tr>
				<td style="width:700px; "  colspan="4" valign="top" >
					<table cellspacing="0" cellpadding="0" style="width:100%">
						<tr>
							<td colspan="4" class="bordered borderRight" style="width:300px; background-color:#efefef; height:20px;" valign="middle"><strong>IOL Lens Type</strong></td>
						</tr>
						<tr>
							<td class="bordered bdrlft '.$pdf_border_class.' pd" style="width:25%;height:20px;vertical-align:baseline" valign="middle" align="center"><strong>Type</strong></td>
							<td class="bordered '.$pdf_border_class.' pd" style="width:25%;height:20px;vertical-align:baseline" valign="middle" align="center"><strong>Power</strong></td>
							<td class="bordered '.$pdf_border_class.' pd" style="width:25%;height:20px;vertical-align:baseline" valign="middle" align="center"><strong>Cyl</strong></td>
							<td class="bordered '.$pdf_border_class.' pd bdrRght" style="width:25%;height:20px;vertical-align:baseline" valign="middle" align="center"><strong>Axis</strong></td>
						</tr>
						{EXTRA_IOL_MODEL_ROW}
					</table>
				</td>
			</tr>
			<tr>	
				<td style="width:700px; "  colspan="4" valign="top" >
					<table cellspacing="0" cellpadding="0" style="width:100%">
						<tr>
							<td class="bordered" style="width:50%; background-color:#efefef; height:20px;"  valign="middle" align="center" ><b>Surgeon</b></td>
							<td class="bordered" style="width:50%; background-color:#efefef; height:20px;"  valign="middle" align="center" ><b>Tech</b></td>
						</tr>
						
						<tr>
							<td class="bordered" style="width:50%; height:20px;" valign="middle" align="center" ><b></b></td>
							<td class="bordered" style="width:50%; height:20px;" valign="middle" align="center" ><b></b></td>
						</tr>						
						<tr>
							<td class="bordered" style="width:50%; height:20px;" valign="middle" align="center" ><b></b></td>
							<td class="bordered" style="width:50%; height:20px;" valign="middle" align="center" ><b></b></td>
						</tr>
						
						<tr>
							<td class="bordered" style="width:50%; height:20px;" valign="middle" align="center" ><b></b></td>
							<td class="bordered" style="width:50%; height:20px;" valign="middle" align="center" ><b></b></td>
						</tr>
						
						<tr>
							<td class="bordered" style="width:50%; height:20px;" valign="middle" align="center" ><b></b></td>
							<td class="bordered" style="width:50%; height:20px;" valign="middle" align="center" ><b></b></td>
						</tr>
					</table>	
				</td>
				
				</tr>
				
				
				<tr><td colspan="4" class="borderBottom" style="width:100%;"></td></tr>
				<tr>
					<td style="width:700px; padding-top:10px; padding-bottom:5px;"  valign="top" colspan="4" class="bordered">
						<b>Special Notes : </b>{COMMENTS} 
					</td>
				</tr>
				<tr>
					<td style="width:700px; padding-top:10px; padding-bottom:5px;"  valign="top" colspan="4" class="bordered">
						<b>CoManage DR : </b>
					</td>
				</tr>
				<tr>
					<td style="width:700px; padding-top:10px; padding-bottom:5px;"  valign="top" colspan="4" class="bordered">
						<b>Biometry Type : </b>
					</td>
				</tr>
				
		</table>
	';
	
$LensRow	=	'<tr>
							<td  class="bordered font_12" style="width:20%; background-color:#efefef; height:20px;" align="left"  valign="middle"><b>Backup{COUNTER}</b></td>
							<td  class="bordered " style="width:80%; " >
								<table>
									<tr>
										<!-- <td style="width:40%; height:20px;" class="borderRight" valign="middle" align="left">{BACKUP{COUNTER}_LENS}</td>
										<td style="width:15%; height:20px;"class="borderRight" valign="middle" align="left">{BACKUP{COUNTER}_POWER}</td>
										<td style="width:15%; height:20px;"class="borderRight" valign="middle" align="left">{BACKUP{COUNTER}_CYL}</td>
										<td style="width:15%; height:20px;"class="borderRight" valign="middle" align="left">{BACKUP{COUNTER}_AXIS}</td>
										<td style="width:15%; height:20px;" align="left" valign="middle">{BACKUP{COUNTER}_USED}</td> -->
										
										
										<td style="width:40%; height:20px;" class="borderRight" valign="middle" align="left"></td>
										<td style="width:15%; height:20px;"class="borderRight" valign="middle" align="left"></td>
										<td style="width:15%; height:20px;"class="borderRight" valign="middle" align="left"></td>
										<td style="width:15%; height:20px;"class="borderRight" valign="middle" align="left"></td>
										<td style="width:15%; height:20px;" align="left" valign="middle"></td>
										
									</tr>
								</table>	
							</td>
						</tr>';

$surgeonTechRow = '<tr>
						<td class="bordered" style="width:50%; height:20px;" valign="middle" align="center" ><b></b></td>
						<td class="bordered" style="width:50%; height:20px;" valign="middle" align="center" ><b></b></td>
				  </tr>';
?>