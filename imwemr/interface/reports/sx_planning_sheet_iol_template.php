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
				<td colspan="3" style="width:490px; padding:5px 0px" class="bordered shadow "><b>IOL Master Sheet</b></td>
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
				<td colspan="4" style="width:700px">
					<table cellspacing="0" cellpadding="0" style="width:100%;" >
						<tr style="">
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
										<td style="height:60px; width:99%; background-color:#ddd;" valign="bottom" align="center" class="bordered"><b>MR : </b></td>
									</tr>
								</table>		
							</td>
							
							<td colspan="2" style="width:500px" valign="top" >
								<table cellspacing="0" cellpadding="0" style="width:500px">
									<tr>
										<td style="height:26px; width:100px;text-align:center " valign="bottom" class="bordered"><b>S</b></td>
										<td style="height:26px; width:100px;text-align:center " valign="bottom" class="bordered"><b>C</b></td>
										<td style="height:26px; width:100px;text-align:center " valign="bottom" class="bordered"><b>A</b></td>
										<td style="height:26px; width:100px;text-align:center " valign="bottom" class="bordered"><b>Vision</b></td>
										<td style="height:26px; width:100px;text-align:center " valign="bottom" class="bordered"><b>Glare</b></td>
									</tr>
									{MRVALUES}
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>	
				
			<tr><td colspan="4" style="width:100%"></td></tr>
			
			<tr>
				<td colspan="4" style="width:700px">
					<table cellspacing="0" cellpadding="0" style="width:100%;" align="center" >
						<tr style="">
							<td class="bordered borderRight" style="width:10%; height:20px;background-color:#efefef;"  valign="top">
								<b>K Values</b>
							</td>
							
							<td class="bordered" style="width:30%; height:20px;background-color:#efefef;"  valign="top" align="center">
								<b>{KHEADING1}</b>
							</td>
							
							<td class="bordered" style="width:30%; height:20px;background-color:#efefef;"  valign="top" align="center">
								<b>{KHEADING2}</b>
							</td>
							
							<td class="bordered" style="width:30%; height:20px;background-color:#efefef;"  valign="top" align="center">
								<b>{KHEADING3}</b>
							</td>
						</tr>
						{KVALUES}
						<tr>
							<td class="bordered borderRight" colspan="2" style="width:40%;height:20px" valign="top">Performed By : {KVALUESPERFORMED}</td>
							<td class="bordered" colspan="2" style="width:60%;height:20px" valign="top">Date : {KVALUESDATE}</td>
						</tr>
					</table>
				</td>
			</tr>
			
			<tr><td colspan="4" style="width:100%"></td></tr>
			
			<tr>
				<td colspan="4" style="width:700px;">
					<table cellspacing="0" cellpadding="0" style="width:100%;" align="center" >
						<tr>
							<td class="bordered borderRight" style="width:20%; height:20px;background-color:#efefef;"  valign="top" align="center">
								<b>IOL</b>
							</td>
							
							<td class="bordered" style="width:20%; height:20px;background-color:#efefef;"  valign="top" align="center">
								<b>{LENSHEADING1}</b>
							</td>
							
							<td class="bordered"  style="width:20%; height:20px;background-color:#efefef;"  valign="top" align="center">
								<b>{LENSHEADING2}</b>
							</td>
							
							<td class="bordered"  style="width:20%; height:20px;background-color:#efefef;"  valign="top" align="center">
								<b>{LENSHEADING3}</b>
							</td>
							
							<td class="bordered"  style="width:20%; height:20px;background-color:#efefef;"  valign="top" align="center">
								<b>{LENSHEADING4}</b>
							</td>	
						</tr>
						{IOLLENSROW}
						<tr>
							<td class="bordered borderRight" colspan="5" style="width:100%;height:20px" valign="top">Performed By : {LENSPERFORMED}</td>
						</tr>
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
			<tr>
				<td style="width:175px;text-align:left"></td>
				<td style="width:175px;text-align:left"></td>
				<td style="width:175px;text-align:left"></td>
				<td style="width:175px;text-align:left"></td>
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