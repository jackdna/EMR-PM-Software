<?php
	$printTemplate	=	'
		<table align="left" class="mainTable" cellspacing="0" cellpadding="0" >
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
			<tr><td colspan="4"></td></tr>
			<tr>
				<td style="width:150px; "  valign="top" align="center"   >
					<table cellspacing="0" cellpadding="0" style="height:60px;" >
						<tr>
							<td style="width:50px; height:60px; " align="center" class="bordered" valign="middle" ><b class="font_16">Eye</b></td>
							<td style="width:100px; background-color:#ddd;  "  class="bordered font_30" valign="middle" >{SITE}</td>
						</tr>	
					</table>	
				</td>
				<td style="width:50px; " valign="top"  >
					<table cellspacing="0" cellpadding="0" >
						<tr>
							<td style="height:60px; width:48px; background-color:#ddd;" valign="bottom" align="center" class="bordered"><b>REF : </b></td>
						</tr>
					</table>		
				</td>
				
				<td style="width:250px" valign="top" >
					<table cellspacing="0" cellpadding="0" style="width:240px;" align="center" >
						<tr>
							<td style="height:26px; width:95px; " valign="bottom" class="bordered">&nbsp;<b>Refraction</b></td>
							<td style="height:26px; width:145px;  background-color:#ddd;padding-left:2px; " valign="middle" class="bordered">
								Dominant&nbsp;Eye&nbsp;<b class="font_14">{DOMINANT_EYE}</b>
							</td>
						</tr>
						<tr><td style="height:26px; width:230px;" colspan="2" valign="bottom" class="bordered font_14"><b>{REFRACTION}</b></td></tr>
					</table>
				</td>
								
				<td style="width:250px; " valign="top"  >
					<table cellspacing="0" cellpadding="0" style="width:250px" >
						<tr>
							<td style="width:250px" >
								<table cellspacing="0" cellpadding="0"  >
									<tr>
										<td style="height:25px; width:250px;" valign="bottom" class="bordered">&nbsp;Other Eye Refraction</td>
										
										</tr>
										<tr><td style="height:26px; width:250px;" valign="bottom" class="bordered font_12">{OTHER_EYE_REFRACTION}</td></tr>
									</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td colspan="4"></td></tr>
			<tr>
				<td style="width:200px; height:60px;"  valign="top" colspan="2" >
					<table cellspacing="0" cellpadding="0" >
						<tr>
							<td class="bordered" style="width:220px; background-color:#efefef; height:20px;" colspan="2" valign="middle" ><b>Surgery</b></td>
						</tr>
						<tr>
							<td class="bordered" style="width:45px; background-color:#efefef; height:20px;" valign="middle" ><b>Date</b></td>
							<td class="borderBottom borderRight" style="width:150px; height:20px;" valign="middle">&nbsp;{DOS}</td>
						</tr>
						<tr>
							<td  class="bordered" style="width:45px; background-color:#efefef; height:20px;" valign="middle"><b>Time</b></td>
							<td  class="borderBottom borderRight" style="width:150px; height:20px;" valign="middle">&nbsp;{SURGERY_TIME}</td>
						</tr>
						
						
					</table>	
				</td>
				
				<td style="width:250px;"  valign="top"  >
					<table cellspacing="0" cellpadding="0" align="center"  >
						<tr>
							<td class="bordered" style="width:240px; background-color:#efefef; height:20px;" colspan="4" valign="middle" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>K\'s </b></td>
						</tr>
						<tr >
							<td class="bordered" style="width:57px; height:20px;" align="center" valign="middle"><b>Flat</b></td>
							<td class="bordered" style="width:56px; height:20px;" align="center" valign="middle"><b>Steep</b></td>
							<td class="bordered" style="width:56px; height:20px;" align="center" valign="middle"><b>Axis</b></td>
							<td class="bordered" style="width:56px; height:20px;" align="center" valign="middle"><b>Cyl</b></td>
							
						</tr>
						<tr>
							<td class="bordered" style="width:57px; height:20px;"  align="center" valign="middle">{K_FLAT}</td>
							<td class="bordered" style="width:56px; height:20px;"  align="center" valign="middle">{K_STEEP}</td>
							<td class="bordered" style="width:56px; height:20px;" align="center" valign="middle">{K_AXIS}</td>
							<td class="bordered" style="width:56px; height:20px;" align="center" valign="middle">{K_CYL}</td>
						</tr>
						
						
					</table>	
				</td>
								
				<td style="width:250px; "  valign="top" >
					<table cellspacing="0" cellpadding="0"  >
						<tr>
							<td class="bordered" style="width:240px; background-color:#efefef; height:20px;" valign="middle" colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other Eye K\'s </td>
						</tr>
						<tr>
							<td class="bordered" style="width:57px; height:20px;" align="center" valign="middle" >Flat</td>
							<td class="bordered" style="width:57px; height:20px;" align="center" valign="middle">Steep</td>
							<td class="bordered" style="width:57px; height:20px;" align="center" valign="middle">Axis</td>
							<td class="bordered" style="width:57px; height:20px;" align="center" valign="middle">Cyl</td>
							
						</tr>
						<tr>
							<td class="bordered font_12" style="width:57px; height:20px;"  align="center" valign="middle">{OTHER_K_FLAT}</td>
							<td class="bordered font_12" style="width:57px; height:20px;"  align="center" valign="middle">{OTHER_K_STEEP}</td>
							<td class="bordered font_12" style="width:57px; height:20px;" align="center" valign="middle">{OTHER_K_AXIS}</td>
							<td class="bordered font_12" style="width:57px; height:20px;" align="center" valign="middle">{OTHER_K_CYL}</td>
						</tr>
						
						
					</table>	
				</td>
				
			</tr>	
			<tr><td colspan="4"></td></tr>
			<tr>
				<td style="width:220px; "  valign="top" colspan="2"  >
					<table cellpadding="0" cellspacing="0">
						<tr><td style="width:220px; height:20px; background-color:#efefef; " valign="middle" align="left" class="bordered">&nbsp;<b>Surgeon</b></td></tr>
						<tr><td style="width:220px; height:48px; " valign="middle" align="left" class="bordered">{SURGEON_NAME}</td></tr>
					</table>
				</td>
				<td style="width:485px; "  valign="top" colspan="2"  >
					<table cellpadding="0" cellspacing="0">
						<tr><td colspan="2" style="width:485px; height:20px; background-color:#efefef; padding-left:5px; " valign="middle" align="left" class="bordered"><b>Procedure</b></td></tr>
						<tr><td style="width:65px; height:20px; padding-left:5px;" valign="middle" align="left" class="bordered">Primary</td><td style="width:420px; height:20px;padding-left:5px;" valign="middle" align="left" class="bordered">{PRI PROC}</td></tr>
						<tr><td style="width:65px; height:20px; padding-left:5px;" valign="middle" align="left" class="bordered">Secondary</td><td style="width:420px; height:20px;padding-left:5px;" valign="middle" align="left" class="bordered">{SEC PROC}</td></tr>
					</table>
				</td>
				
			</tr>
			<tr><td colspan="4"></td></tr>
			<tr>
				<td style="width:450px; " valign="top" colspan="3" >
					<table cellspacing="0" cellpadding="0" >
						<tr>
							<td class="bordered borderRight" style="width:50px; background-color:#efefef; height:20px;" valign="middle" ><b>Lenses</b></td>
							<td class="bordered" style="width:400px; background-color:#efefef; height:20px;"  align="center" valign="middle">
								PrePlan Lens(Traditional IOL) differs from Primary Lens
							</td>
						</tr>
						<tr>
							<td class="bordered" style="width:50px; background-color:#efefef; height:20px;" ><b>&nbsp;</b></td>
							<td  class="bordered" style="width:400px; " valign="top"  >
								<table cellspacing="0" cellpadding="2" >
									<tr>
										<td style="width:170px; height:20px;" align="center" valign="middle"><b>Lens</b></td>
										<td style="width:60px; height:20px;" align="center" valign="middle"><b>Power</b></td>
										<td style="width:60px; height:20px;" align="center" valign="middle"><b>Cyl</b></td>
										<td style="width:60px; height:20px;" align="center" valign="middle"><b>Axis</b></td>
										<td style="width:50px; height:20px;" align="center" valign="middle"><b>Used</b></td>
										
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td  class="bordered font_12" style="width:50px; background-color:#efefef; height:20px;" align="left" valign="middle" ><b>Primary</b></td>
							<td  class="bordered" style="width:400px;  height:20px;" >
								<table cellpadding="0" cellspacing="0" >
									<tr>
										<td style="width:170px; height:20px;" class="borderRight" valign="middle" align="left">{PRIMARY_LENS}</td>
										<td style="width:60px; height:20px;" class="borderRight" valign="middle" align="left">{PRIMARY_POWER}</td>
										<td style="width:60px; height:20px;" class="borderRight" valign="middle" align="left">{PRIMARY_CYL}</td>
										<td style="width:60px; height:20px;" class="borderRight" valign="middle" align="left">{PRIMARY_AXIS}</td>
										<td style="width:50px; height:20px;" valign="middle" align="Left">{PRIMARY_USED}</td>
										
									</tr>
								</table>	
							</td>
						</tr>';
						
						for($q=1;$q<=3;$q++) {
							$printTemplate	.= '
							<tr>
								<td  class="bordered font_12" style="width:50px; background-color:#efefef; height:20px;" align="left"  valign="middle"><b>Backup'.$q.'</b></td>
								<td  class="bordered " style="width:400px; height:20px;" >
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:170px; height:20px;" class="borderRight" valign="middle" align="left">{BACKUP'.$q.'_LENS}</td>
											<td style="width:60px; height:20px;"class="borderRight" valign="middle" align="left">{BACKUP'.$q.'_POWER}</td>
											<td style="width:60px; height:20px;"class="borderRight" valign="middle" align="left">{BACKUP'.$q.'_CYL}</td>
											<td style="width:60px; height:20px;"class="borderRight" valign="middle" align="left">{BACKUP'.$q.'_AXIS}</td>
											<td style="width:50px; height:20px;" valign="middle" align="left">{BACKUP'.$q.'_USED}</td>
											
										</tr>
									</table>	
								</td>
							</tr>
							';
						}
						
						$printTemplate	.= '
						{EXTRA_LENS_ROW}
					</table>	
				</td>
				
								
				<td style="width:250px; "  valign="top" >
					<table cellspacing="0" cellpadding="0" >
						<tr>
							<td class="bordered" style="width:120px; background-color:#efefef; height:20px;"  valign="middle" align="center" ><b>Surgeon</b></td>
							<td class="bordered" style="width:120px; background-color:#efefef; height:20px;"  valign="middle" align="center" ><b>Tech</b></td>
						</tr>
						
						<tr>
							<td class="bordered" style="width:120px; height:20px;" valign="middle" align="center" ><b></b></td>
							<td class="bordered" style="width:120px; height:20px;" valign="middle" align="center" ><b></b></td>
						</tr>						
						<tr>
							<td class="bordered" style="width:120px; height:20px;" valign="middle" align="center" ><b></b></td>
							<td class="bordered" style="width:120px; height:20px;" valign="middle" align="center" ><b></b></td>
						</tr>
						
						<tr>
							<td class="bordered" style="width:120px; height:20px;" valign="middle" align="center" ><b></b></td>
							<td class="bordered" style="width:120px; height:20px;" valign="middle" align="center" ><b></b></td>
						</tr>
						
						<tr>
							<td class="bordered" style="width:120px; height:20px;" valign="middle" align="center" ><b></b></td>
							<td class="bordered" style="width:120px; height:20px;" valign="middle" align="center" ><b></b></td>
						</tr>
						
						<tr>
							<td class="bordered" style="width:120px; height:20px;" valign="middle" align="center" ><b></b></td>
							<td class="bordered" style="width:120px; height:20px;" valign="middle" align="center" ><b></b></td>
						</tr>
						{EXTRA_SURGEON_TECH_ROW}
					</table>	
				</td>
				
				</tr>
				
				<tr><td colspan="4" class="borderBottom" style="width:700px;"></td></tr>
				<tr>
					<td style="width:700px; padding-top:10px; padding-bottom:5px;"  valign="top" colspan="4" class="bordered">
						<b>Special Notes : </b>{PUPIL_DILATED} 
					</td>
				</tr>
				<tr>
					<td style="width:600px; padding-top:10px; padding-bottom:5px;"  valign="top" colspan="4" class="bordered">
						<b>CoManage DR : </b>
					</td>
				</tr>
				<tr>
					<td style="width:600px; padding-top:10px; padding-bottom:5px;"  valign="top" colspan="4" class="bordered">
						<b>Biometry Type : </b>
					</td>
				</tr>
				<tr>
					<td style="width:700px; padding-top:10px; padding-bottom:5px;"  valign="top" colspan="4" class="bordered">
						<b>Lens - as of SLE Summary : </b>{LENS_SLE_SUMMARY} 
					</td>
				</tr>
				<tr>
					<td style="width:700px; padding-top:10px; padding-bottom:5px;"  valign="top" colspan="4" class="bordered">
						<b>Comments : </b>{SX_COMMENTS}  
					</td>
				</tr>
		</table>
	';
	
$LensRow	=	'<tr>
							<td  class="bordered font_12" style="width:50px; background-color:#efefef; height:20px;" align="left"  valign="middle"><b>Backup{COUNTER}</b></td>
							<td  class="bordered " style="width:400px; " >
								<table>
									<tr>
										<td style="width:170px; height:20px;" class="borderRight" valign="middle" align="left">{BACKUP{COUNTER}_LENS}</td>
										<td style="width:60px; height:20px;"class="borderRight" valign="middle" align="left">{BACKUP{COUNTER}_POWER}</td>
										<td style="width:60px; height:20px;"class="borderRight" valign="middle" align="left">{BACKUP{COUNTER}_CYL}</td>
										<td style="width:60px; height:20px;"class="borderRight" valign="middle" align="left">{BACKUP{COUNTER}_AXIS}</td>
										<td style="width:50px; height:20px;" align="left" valign="middle">{BACKUP{COUNTER}_USED}</td>
										
									</tr>
								</table>	
							</td>
						</tr>';

$surgeonTechRow = '<tr>
						<td class="bordered" style="width:120px; height:20px;" valign="middle" align="center" ><b></b></td>
						<td class="bordered" style="width:120px; height:20px;" valign="middle" align="center" ><b></b></td>
				  </tr>';
?>