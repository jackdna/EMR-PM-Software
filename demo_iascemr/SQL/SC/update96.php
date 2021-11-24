<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

#IMPORT FACILITIES FROM IDOC
?>
<?php
set_time_limit(900);

//reconnect to surgery center database
include("../../common/conDb.php");
include_once("../../../".$iolinkDirectoryName."/common/conDb.php");
$constantImwFacilityIdArr = explode(",",$constantImwFacilityId);
$constantImwFacilityIdMain = $constantImwFacilityIdArr[0];
$gro_id_qry = $gro_id = "";
if($constantImwProviderId!="") { //IF CONSTANT PROVIDER ID DEFINED IN GLOBAL THEN SET INSTUTION ON 

	imw_close($link); //CLOSE SURGERYCENTER CONNECTION
	include("../../connect_imwemr.php"); // imwemr connection
	$sel_proc_qry = "select gro_id from groups_new where group_institution='1' LIMIT 0,1";
	$sel_proc_res=imw_query($sel_proc_qry) or die($sel_proc_qry.imw_error());
	$sel_proc_row=imw_fetch_array($sel_proc_res);
	$gro_id		= $sel_proc_row["gro_id"];
	if($gro_id) {
		echo $gro_id_qry = " fac_group_institution = '".$gro_id."', ";	
	}
	imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
	include("../../common/conDb.php");
}
$qCheck=imw_query("select fac_id from facility_tbl");
if(imw_num_rows($qCheck)==0)
{
	//get list of existing surgery center facilities if any
	$query=imw_query("select fac_name from facility_tbl where fac_del_status='0' order by id");
	while($data=imw_fetch_object($query))
	{
		$fac_name=strtolower(trim($data->fac_name));
		$surgeryFacilities[$fac_name]=$fac_name;	
	}
	$query=imw_query("select * from surgerycenter");
	$fac_array=imw_fetch_array($query);
		
	if(imw_num_rows($query)>=1)
	{
		if(!$surgeryFacilities[strtolower(trim($fac_array['name']))])//add only if facility is not already exist
		{
			$sql="insert into facility_tbl set `fac_name`='".$fac_array['name']."',
			`fac_address1`		= '".$fac_array['address']."',
			`fac_address2`		= '".$fac_array['address2']."',
			`fac_city`			= '".$fac_array['city']."',
			`fac_state`			= '".$fac_array['state']."',
			`fac_zip`			= '".$fac_array['zip']."',
			`fac_contact_name`	= '".$fac_array['contactName']."',
			`fac_contact_phone`	= '".$fac_array['phone']."',
			`fac_contact_fax`	= '".$fac_array['fax']."',
			`fac_contact_email`	= '".$fac_array['email']."',
			`fac_federal_ein`	= '".$fac_array['federalEin']."',
			`fac_npi`			= '".$fac_array['npi']."',
			`fac_idoc_link_id`	= '".$constantImwFacilityIdMain."',
			`fac_entered_date`	= '".date('Y-m-d')."',
			`fac_head_quater`	= '1',
			 ".$gro_id_qry."
			`fac_entered_time`	= '".date('H:i:s')."'";
			imw_query($sql)or $msg_info[] = imw_error();
		}
	}
}

imw_query("CREATE TABLE stub_tbl_bak".date("d_m_Y")." AS (SELECT * FROM stub_tbl)")or $msg_info[] = imw_error();

$sql1="UPDATE `stub_tbl` SET  iasc_facility_id = '".$constantImwFacilityIdMain."' WHERE iasc_facility_id = '0'"; 
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 96 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 96 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 96</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>