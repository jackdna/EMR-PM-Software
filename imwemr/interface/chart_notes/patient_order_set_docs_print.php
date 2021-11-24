<style type="text/css">
	.page_header{
		font-size:16px;
		font-weight:bold;
		text-align:right;
	}
	
	.main_td{
		font-size:16px;
		font-weight:bold;
		text-align:center;
		padding-right:10px;
	}
	
	.main_td_b{
		font-size:16px;
		font-weight:bold;
		text-align:center;
	}
	
	.tb_head{
		font-size:16px;
		font-weight:bold;
		text-align:left;
		padding-left:5px;
	}
	
	.label{
		font-size:12px;
		text-align:right;
		padding-right: 5px;
	}
	.content{
		font-size:12px;
		text-align:left;
		padding-left: 5px;
	}
	
</style>
<page backtop="5mm" backbottom="10mm">
<page_header>
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
    	<td class="page_header" style="width:100%;">Patient Orders</td>
    </tr>
</table>
</page_header>
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
    	<td class="main_td_b" style="width:100%;"><?php echo $group_name;?></td>
    </tr>
    <tr>
    	<td class="main_td" style="width:100%;"><?php echo $group_Address;?></td>
    </tr>
    <tr>
    	<td class="main_td" style="width:100%;"><?php echo $group_Telephone;?></td>
    </tr>
</table>
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
    	<td class="tb_head" style="width:130px;">Date of Order : </td>
        <td class="tb_head" style="width:600px;"><?php echo $date_of_order;?></td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
</table>
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
        <td class="tb_head" colspan="6">Patient : <?php echo $patient_name;?></td>
    </tr>
    <tr>
    	<td class="label">Gender : </td>
        <td class="content"><?php echo $gender_info;?></td>
        <td class="label">DOB : </td>
        <td class="content"><?php echo $patient_dob;?></td>
    </tr>
    <tr>
    	<td class="label">Address : </td>
        <td class="content" colspan="5"><?php echo $patient_address;?></td>
    </tr>
    <tr>
    	<td class="label" style="width:80px;">Home : </td>
        <td class="content" style="width:150px;"><?php echo $patient_home_ph;?></td>
        <td class="label" style="width:70px;">Work : </td>
        <td class="content" style="width:150px;"><?php echo $patient_work_ph;?></td>
        <td class="label" style="width:80px;">Cell : </td>
        <td class="content" style="width:150px;"><?php echo $patient_cell_ph;?></td>
    </tr>
    <tr><td colspan="6">&nbsp;</td></tr>
</table>
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
        <td class="tb_head" colspan="4">Requesting Physician : </td>
    </tr>
    <tr>
    	<td class="label">Physician : </td>
        <td class="content"><?php echo $physician_name;?></td>
    </tr>
    <tr>
        <td class="label">Practice Address : </td>
        <td class="content"><?php echo $group_Address;?></td>
    </tr>
    <tr>
    	<td class="label" style="width:100px;">Phone # : </td>
        <td class="content" style="width:255px;"><?php echo $group_Telephone;?></td>
        <td class="label" style="width:100px;">FAX # : </td>
        <td class="content" style="width:250px;"><?php echo $group_Fax;?></td>
    </tr>
    <tr><td colspan="4">&nbsp;</td></tr>
</table>
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
        <td class="tb_head" colspan="4">Diagnosis : </td>
    </tr>
    <tr>
    	<td class="content" style="width:100px;">Assesments : </td>
        <td class="content" style="width:600px;"><?php echo $assess_data;?></td>
    </tr>
    <tr><td colspan="4">&nbsp;</td></tr>
</table>
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
        <td class="tb_head" style="width:100%">Test/Labs : </td>
    </tr>
</table>
<?php echo $patient_orders;?>
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
    	<td>&nbsp;</td>
    </tr>
    <tr>
        <td class="content" style="width:100%"><img src="<?php echo $imgNme;?>" height="80" width="240"></td>
    </tr>
    <tr>
        <td class="content">&nbsp;<?php echo $physician_name;?></td>
    </tr>
</table>
</page>