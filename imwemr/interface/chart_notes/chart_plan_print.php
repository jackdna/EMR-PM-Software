<style>
.text_b_w{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
}
.paddingLeft{
	padding-left:5px;
}
.paddingTop{
	padding-top:5px;
}
.tb_subheading{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#000000;
	background-color:#f3f3f3;;
}
.tb_heading{
	font-size:11px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#FFFFFF;
	background-color:#999999;
	margin-top:10px;
}
.tb_headingHeader{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#FFFFFF;
	background-color:#4684ab;
}
.tb_dataHeader{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#FFFFFF;
	background-color:#9a9a9a;
}
.text_lable{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		background-color:#FFFFFF;
		font-weight:bold;
}
.text_value{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:100;
		background-color:#FFFFFF;
	}
.text_blue{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		color:#0000CC;
	font-weight:bold;
	}
.text_green{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		color:#006600;
		font-weight:bold;
}
.imgCon{width:325px;height:auto;}
</style>

<page backtop="5mm" backbottom="5mm">
	<page_header>
		<table style="width:100%;" border="0" cellspacing="0"  cellpadding="0">
				<tr>
					<td style="width:40%" class="tb_headingHeader"><?php echo ''.$patient_heading;?></td>
					<td style="width:30%" class="tb_headingHeader"><?php echo ''.$about_patient;?>&nbsp;</td>
				    <td style="width:30%; text-align:right" class="tb_headingHeader">Date of Service:&nbsp;<?php echo ''.$date_of_service;?>&nbsp;</td>
				</tr>
		</table>
	</page_header>
	<table style="width:100%;" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="text_b_w" style="width:30%;" align="left"></td>
				<td class="text_b_w" style="width:1%;"></td>
				<td class="text_value" style="width:69%;" align="right">Printed by:<?php echo ''.$opertator_name;?>&nbsp;on&nbsp;<?php echo ''.$printDt;?></td>
			</tr>
			<tr>
				<td class="text_b_w" style="width:100%;" colspan="3"><hr/></td>
			</tr>
		</table>
		<table style="width:100%;" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td style="width:40%" align="left" valign="top"> 
					<table style="width:100%;" border="0" cellspacing="0"  cellpadding="0">
						<tr>
							<td style="width:100%" class="text_lable"><?php echo ''.$patientName.'-'.$patientDetails['id'];?></td>
						</tr>
						<?php if(!empty($about_patient)){?>
						<tr>
							<td style="width:100%" class="text_value"><?php echo ''.$about_patient;?>&nbsp;</td>
						</tr>
						<?php } ?>
						<?php if(!empty($patientDetails['street'])){?>
						<tr>
							<td style="width:100%" class="text_value"><?php echo ''.$patientDetails['street'];?>&nbsp;</td>
						</tr>
						<?php } ?>
						<?php if(!empty($patientDetails['street2'])){?>
						<tr>
							<td style="width:100%" class="text_value"><?php echo ''.$patientDetails['street2'];?>&nbsp; </td>
						</tr>
						<?php } ?>
						<?php if(!empty($patient_address)){?>
						<tr>
							<td style="width:100%" class="text_value"><?php echo ''.$patient_address;?></td>
						</tr>
						<?php } ?>
						
						<tr>
							<td style="width:100%" class="text_value">Ph.: <?php echo ''.$patientDetails['phone_home'];?>&nbsp; </td>
						</tr>
						
					</table>
			  </td>
			  <td style="width:20%"  valign="top">&nbsp;</td>
			  <td style="width:40%" align="right" valign="top">
					<table style="width:100%;" border="0" cellspacing="0"  cellpadding="0">
						<tr>
							<td style="width:100%" class="text_lable"><?php echo ''.$groupDetails['name'];?></td>
						</tr>
						<?php if(!empty($groupDetails['group_Address1'])){?>
						<tr>
							<td style="width:100%" class="text_value"><?php echo ''.ucwords($groupDetails['group_Address1']);?></td>
						</tr>
						<?php } ?>
						<?php if(!empty($groupDetails['group_Address2'])){?>
						<tr>
							<td style="width:100%" class="text_value"><?php echo ''.ucwords($groupDetails['group_Address2']);?>&nbsp;</td>
						</tr>
						<?php } ?>
						
						<tr>
							<td style="width:100%" class="text_value"><?php echo ''.$groupDetails['group_City'].', '.$groupDetails['group_State'].' '.$groupDetails['group_Zip'];?></td>
						</tr>
						
						<?php if(!empty($groupDetails['group_Telephone'])){?>
						<tr>
							<td style="width:100%" class="text_value">Ph.:&nbsp;<?php echo ''.$groupDetails['group_Telephone'];?></td>
						</tr>
						<?php } ?>
						
						<tr>
							<td style="width:100%" class="text_value">Fax:&nbsp;<?php echo ''.$groupDetails['group_Fax'];?></td>
						</tr>
					</table>
				</td>
				</tr>
		</table>
		<table style="width:100%;" border="0" cellspacing="0"  cellpadding="0">
				<tr>
					<td style="width:100%" class="tb_dataHeader"> Ocular Medication</td>
				</tr>
			</table>
			
		<?php
		if($final_flag=='0') {
		
		?>	
			<table cellpadding="0" cellspacing="0" >
			
				<tr>
					<td style="text-align:left">
					</td>
				</tr>
			</table>		
			
		<?php
		}
		?>	
			
			
			
			
			