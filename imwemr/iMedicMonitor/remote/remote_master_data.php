<?php 
require_once("common_functions.php");

$xml_data = '<?xml version="1.0" encoding="UTF-8" ?>
<response>';

/*--------GENERATING FACILITY DATA TAGS--------------*/
$facquery = "SELECT id, name, facility_type, waiting_timer FROM `facility`";
$facresult = imw_query($facquery);
if($facresult && imw_num_rows($facresult)>0){
$xml_data .= '
	<facs>';
	while($facrs = imw_fetch_array($facresult)){
		$xml_data .= '
		<fac>
			<id>'.$facrs['id'].'</id>
			<type>'.$facrs['facility_type'].'</type>
			<name>'.refine_input($facrs['name']).'</name>
			<wait_timer>'.$facrs['waiting_timer'].'</wait_timer>
		</fac>';
	}
$xml_data .= '
	</facs>';	
}

/*--------GENERATING PROVIDERS DATA TAGS--------------*/
$docquery = "SELECT id, lname, fname, mname, user_type, username FROM `users` WHERE delete_status='0' AND Enable_Scheduler='1' ORDER BY lname, fname";
$docresult = imw_query($docquery);
if($docresult && imw_num_rows($docresult)>0){
$xml_data .= '
	<docs>';
	while($docrs = imw_fetch_array($docresult)){
		$xml_data .= '
		<doc>
			<id>'.$docrs['id'].'</id>
			<type>'.$docrs['user_type'].'</type>
			<username>'.$docrs['username'].'</username>
			<name>'.refine_input(core_name_format($docrs['lname'], $docrs['fname'], $docrs['mname'])).'</name>
		</doc>';
	}
$xml_data .= '
	</docs>';	
}


/*--------GENERATING PROCS (PROCEDURES) DATA TAGS--------------*/
$proquery = "SELECT id, proc FROM `slot_procedures` WHERE active_status='yes' AND proc != ''";
$proresult = imw_query($proquery);
if($proresult && imw_num_rows($proresult)>0){
$xml_data .= '
	<procs>';
	while($prors = imw_fetch_array($proresult)){
		$xml_data .= '
		<proc>
			<id>'.$prors['id'].'</id>
			<name>'.refine_input($prors['proc']).'</name>
		</proc>';
	}
$xml_data .= '
	</procs>';	
}

/*--------GROUPS (NON-INSTITUTIONAL) DATA TAGS--------------*/
$groquery = "SELECT gro_id, name FROM `groups_new` WHERE `group_institution` ='0' AND del_status='0' ORDER BY gro_id LIMIT 0,1";
$groresult = imw_query($groquery);
if($groresult && imw_num_rows($groresult)>0){
$xml_data .= '
	<groups>';
	while($grors = imw_fetch_array($groresult)){
		$xml_data .= '
		<group>
			<id>'.$grors['gro_id'].'</id>
			<name>'.refine_input($grors['name']).'</name>
		</group>';
	}
$xml_data .= '
	</groups>';	
}


/*--------iMedicMonitor GROUPS (Room groups)--------------*/
$groquery2 = "SELECT id,group_name FROM imonitor_room_groups WHERE delete_status=0 ORDER BY group_order,group_name";
$groresult2 = imw_query($groquery2);
if($groresult2 && imw_num_rows($groresult2)>0){
$xml_data .= '
	<iMongroup>';
	while($grors2 = imw_fetch_assoc($groresult2)){
		$xml_data .= '
		<iMongroup>
			<id>'.$grors2['id'].'</id>
			<name>'.refine_input($grors2['group_name']).'</name>
		</iMongroup>';
	}
$xml_data .= '
	</iMongroup>';	
}

/*--------iMedicMonitor Rooms--------------*/
$groquery3 = "SELECT id, mac_address,room_no,fac_id FROM mac_room_desc WHERE room_no!='' AND delete_status=0 ORDER BY room_no";
$groresult3 = imw_query($groquery3);
if($groresult3 && imw_num_rows($groresult3)>0){
$xml_data .= '
	<iMonRooms>';
	while($grors3 = imw_fetch_assoc($groresult3)){
		$xml_data .= '
		<iMonRooms>
			<room_id>'.$grors3['id'].'</room_id>
			<room_no>'.refine_input($grors3['room_no']).'</room_no>
			<fac_id>'.$grors3['fac_id'].'</fac_id>
		</iMonRooms>';
	}
$xml_data .= '
	</iMonRooms>';
}


/*--after all data genration closing main tag--*/
$xml_data .= '
</response>';
echo $xml_data;
?>