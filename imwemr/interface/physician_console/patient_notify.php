<?php
//require_once('../common/functions.inc.php');
//$objManageData = new DataManage;
$user_id = $_SESSION['authId'];
$mainArrRes = array();

$filter = (isset($_REQUEST['filter']) && empty($_REQUEST['filter']) == false) ? $_REQUEST['filter'] : 0;

//--- UPDATE TEST STATUS ----
if(count($status)){
	$arr_keys = array_keys($status);
	for($i=0;$i<count($arr_keys);$i++){
		$key = $arr_keys[$i];
		list($table_name,$main_id) = preg_split('/__/',$key);
		$val = $status[$key];
		switch($table_name){			
			case 'ivfa':
				$fieldName = 'PatientInformed';
				$mainField = 'vf_id';
			break;
			case 'External/Anterior':
				$table_name = 'disc_external';
				$fieldName = 'ptInformed';
				$mainField = 'disc_id';
			break;
			case 'topography':
				$fieldName = 'ptInformed';
				$mainField = 'topo_id';
			break;
			case 'cell count':
				$table_name = 'test_cellcnt';
				$fieldName = 'ptInformed';
				$mainField = 'test_cellcnt_id';
			break;
			case 'vf-gl':
				$table_name = 'vf_gl';
				$fieldName = 'ptInformed';
				$mainField = 'vf_gl_id';
			break;
			case 'Disc':
				$table_name = 'disc';
				$fieldName = 'ptInformed';
				$mainField = 'disc_id';
			break;	
			default:
				$fieldName = 'ptInformed';
				$mainField = $table_name.'_id';
			break;
		}
		$dataArr = array();
		$dataArr[$fieldName] = $val;
		UpdateRecords($main_id,$mainField,$dataArr,$table_name);		
	}
}
//--- GET VF TEST -----
$sql = "select 'vf' as TEST, patient_data.lname,patient_data.fname,patient_data.mname,
		patient_data.id, date_format(vf.examDate, '".get_sql_date_format('','y')."') as examDate,
		users.lname as phyLname,users.fname as phyFname,users.mname as phyMname,
		vf.vf_id as test_id,concat(vf.Others_OD ,'<br>',vf.Others_OS) as COMMENTS,
		vf.stable as Stable, vf.fuApa as fuApa,vf.monitorIOP as monitorIOP,
		vf.contiMeds as contiMeds,vf.ptInformedNv,vf.rptTst1yr
		from vf join patient_data on patient_data.id = vf.patientId
		join users on users.id = vf.phyName
		where vf.performedBy = '$user_id' and vf.tech2InformPt > '0'
		and vf.del_status = '0' and vf.purged = '0' and vf.ptInformed = '$filter'";
//$vfQryRes = $objManageData->mysqlifetchdata();
$vfQryRes = sqlStatement($sql);

$mainArrRes = array();
for($i=0;$row=sqlFetchArray($vfQryRes);$i++){
	$prog = array();
	if($row['Stable'] > 0){
		$prog[] = 'Stable';
	}
	if($row['contiMeds'] > 0){
		$prog[] = 'Continue Meds';
	}
	if($row['monitorIOP'] > 0){
		$prog[] = 'monitor IOP';
	}	
	if($row['fuApa'] > 0){
		$prog[] = 'F/U APA';
	}
	if($row['ptInformedNv'] > 0){
		$prog[] = 'Inform Pt result next visit';
	}
	if($row['rptTst1yr'] > 0){
		$prog[] = 'Repeat test 1 year';
	}
	
	$row['Prognosis'] = join(', ',$prog);
	$mainArrRes[] = $row;
}
//$mainArrRes = array_merge($mainArrRes,$vfQryRes);


//--- GET VF-GL TEST -----
$sql = "select 'vf-gl' as TEST, patient_data.lname,patient_data.fname,patient_data.mname,
		patient_data.id, date_format(vf.examDate, '".get_sql_date_format('','y')."') as examDate,
		users.lname as phyLname,users.fname as phyFname,users.mname as phyMname,
		vf.vf_gl_id as test_id,vf.comments, vf.plan,vf.ptInformed,
		vf.stable as Stable, vf.fuApa as fuApa,vf.monitorIOP as monitorIOP,
		vf.contiMeds as contiMeds,vf.ptInformedNv,vf.rptTst1yr
		from vf_gl vf join patient_data on patient_data.id = vf.patientId
		join users on users.id = vf.phyName
		where vf.performedBy = '$user_id' and vf.tech2InformPt > '0'
		and vf.del_status = '0' and vf.purged = '0' ";
//$vfQryRes = $objManageData->mysqlifetchdata();
$vfQryRes = sqlStatement($sql);
for($i=0;$row=sqlFetchArray($vfQryRes);$i++){
	
	//and vf.ptInformed = '$filter'
	if(!empty($filter)){
		if(strpos($row['plan'],"Pt informed of results by physician today")!==false || !empty($row['ptInformed'])){  }else{ continue; }
	}else{
		if(strpos($row['plan'],"Pt informed of results by physician today")===false && empty($row['ptInformed'])){  }else{ continue; }
	}
	
	
	$prog = array();
	if($row['Stable'] > 0){
		$prog[] = 'Stable';
	}
	if($row['contiMeds'] > 0){
		$prog[] = 'Continue Meds';
	}
	if($row['monitorIOP'] > 0){
		$prog[] = 'monitor IOP';
	}	
	if($row['fuApa'] > 0){
		$prog[] = 'F/U APA';
	}
	if($row['ptInformedNv'] > 0){
		$prog[] = 'Inform Pt result next visit';
	}
	if($row['rptTst1yr'] > 0){
		$prog[] = 'Repeat test 1 year';
	}
	if(!empty($row['plan'])){ $prog[] = $row['plan']; }
	$row['comments'] = trim($row['comments']);
	if(!empty($row['comments']) && $row['comments'] != "!~!"){  $row['COMMENTS'] = str_replace('!~!', '<br/>', $row['comments']); }
	
	$row['Prognosis'] = join(', ',$prog);
	$mainArrRes[] = $row;
}
//$mainArrRes = array_merge($mainArrRes,$vfQryRes);


//--- GET NFA TEST -----
$sql = "select 'nfa' as TEST, patient_data.lname,patient_data.fname,patient_data.mname,
		patient_data.id, date_format(nfa.examDate, '".get_sql_date_format('','y')."') as examDate,
		users.lname as phyLname,users.fname as phyFname,users.mname as phyMname,
		nfa.nfa_id as test_id,concat(nfa.Others_OD ,'<br>',nfa.Others_OS) as COMMENTS,
		nfa.stable as Stable, nfa.fuApa as fuApa, nfa.monitorIOP as monitorIOP,
		nfa.contiMeds as contiMeds,nfa.ptInformedNv,nfa.rptTst1yr
		from nfa join patient_data on patient_data.id = nfa.patient_id
		join users on users.id = nfa.phyName
		where nfa.performBy = '$user_id' and nfa.tech2InformPt > '0'
		and nfa.del_status = '0' and nfa.purged = '0' and nfa.ptInformed = '$filter'";
//$nfaQryRes = $objManageData->mysqlifetchdata();
$nfaQryRes = sqlStatement($sql);
for($i=0;$row=sqlFetchArray($nfaQryRes);$i++){
	$prog = array();
	if($row['Stable'] > 0){
		$prog[] = 'Stable';
	}
	if($row['contiMeds'] > 0){
		$prog[] = 'Continue Meds';
	}
	if($row['monitorIOP'] > 0){
		$prog[] = 'monitor IOP';
	}	
	if($row['fuApa'] > 0){
		$prog[] = 'F/U APA';
	}
	if($row['ptInformedNv'] > 0){
		$prog[] = 'Inform Pt result next visit';
	}
	if($row['rptTst1yr'] > 0){
		$prog[] = 'Repeat test 1 year';
	}
	
	$row['Prognosis'] = join(', ',$prog);
	$mainArrRes[] = $row;
}
//$mainArrRes = array_merge($mainArrRes,$nfaQryRes);

//--- GET OCT TEST -----
$sql = "select 'oct' as TEST, patient_data.lname,patient_data.fname,patient_data.mname,
		patient_data.id, date_format(oct.examDate, '".get_sql_date_format('','y')."') as examDate,
		users.lname as phyLname,users.fname as phyFname,users.mname as phyMname,
		oct.oct_id as test_id,concat(oct.Others_OD ,'<br>',oct.Others_OS) as COMMENTS,
		oct.stable as Stable, oct.fuApa as fuApa, oct.monitorIOP as monitorIOP,
		oct.contiMeds as contiMeds,oct.ptInformedNv
		from oct join patient_data on patient_data.id = oct.patient_id
		join users on users.id = oct.phyName
		where oct.performBy = '$user_id' and oct.tech2InformPt > '0'
		and oct.del_status = '0' and oct.purged = '0' and oct.ptInformed = '$filter'";
//$octQryRes = $objManageData->mysqlifetchdata();
$octQryRes = sqlStatement($sql);
for($i=0;$row=sqlFetchArray($octQryRes);$i++){
	$prog = array();
	if($row['Stable'] > 0){
		$prog[] = 'Stable';
	}
	if($row['contiMeds'] > 0){
		$prog[] = 'Continue Meds';
	}
	if($row['monitorIOP'] > 0){
		$prog[] = 'monitor IOP';
	}	
	if($row['fuApa'] > 0){
		$prog[] = 'F/U APA';
	}
	if($row['ptInformedNv'] > 0){
		$prog[] = 'Inform Pt result next visit';
	}
	$row['Prognosis'] = join(', ',$prog);
	$mainArrRes[] = $row;
}
//$mainArrRes = array_merge($mainArrRes,$octQryRes);

//--- GET PACHY TEST -----
$sql = "select 'pachy' as TEST, patient_data.lname,patient_data.fname,patient_data.mname,
		patient_data.id, date_format(pachy.examDate, '".get_sql_date_format('','y')."') as examDate,
		users.lname as phyLname,users.fname as phyFname,users.mname as phyMname,
		pachy.pachy_id as test_id,pachy.comments as COMMENTS,
		pachy.stable as Stable, pachy.fuApa as fuApa,
		pachy.contiMeds as contiMeds,pachy.ptInformedNv
		from pachy join patient_data on patient_data.id = pachy.patientId
		join users on users.id = pachy.phyName
		where pachy.performedBy = '$user_id' and pachy.tech2InformPt > '0'
		and pachy.del_status = '0' and pachy.purged = '0' and pachy.ptInformed = '$filter'";
//$pachyQryRes = $objManageData->mysqlifetchdata();
$pachyQryRes = sqlStatement($sql);
for($i=0;$row=sqlFetchArray($pachyQryRes);$i++){
	$prog = array();
	if($row['Stable'] > 0){
		$prog[] = 'Stable';
	}
	if($row['contiMeds'] > 0){
		$prog[] = 'Continue Meds';
	}
	if($row['fuApa'] > 0){
		$prog[] = 'F/U APA';
	}
	if($row['ptInformedNv'] > 0){
		$prog[] = 'Inform Pt result next visit';
	}
	$row['Prognosis'] = join(', ',$prog);
	$mainArrRes[] = $row;
}
//$mainArrRes = array_merge($mainArrRes,$pachyQryRes);

//--- GET IVFA TEST -----
$sql = "select 'ivfa' as TEST, patient_data.lname,patient_data.fname,patient_data.mname,
		patient_data.id, date_format(ivfa.exam_date, '".get_sql_date_format('','y')."') as examDate,
		users.lname as phyLname,users.fname as phyFname,users.mname as phyMname,
		ivfa.vf_id as test_id,ivfa.ivfaComments as COMMENTS,
		ivfa.stable as Stable, ivfa.FuApa as fuApa, ivfa.ContinueMeds as contiMeds,
		ivfa.MonitorAg as MonitorAg,ivfa.ArgonLaser 
		from ivfa join patient_data on patient_data.id = ivfa.patient_id
		join users on users.id = ivfa.phy
		where ivfa.performed_by = '$user_id' and ivfa.tech2InformPt > '0'
		and ivfa.del_status = '0' and ivfa.purged = '0' and ivfa.PatientInformed = '$filter'";
//$ivfaQryRes = $objManageData->mysqlifetchdata();
$ivfaQryRes = sqlStatement($sql);
for($i=0;$row=sqlFetchArray($ivfaQryRes);$i++){
	$prog = array();
	if($row['Stable'] > 0){
		$prog[] = 'Stable';
	}
	if($row['contiMeds'] > 0){
		$prog[] = 'Continue Meds';
	}
	if($row['MonitorAg'] > 0){
		$prog[] = 'Monitor AG';
	}	
	if($row['fuApa'] > 0){
		$prog[] = 'F/U APA';
	}
	if($row['ArgonLaser'] > 0){
		$prog[] = 'Argon Laser Surgery';
	}
	
	$row['Prognosis'] = join(', ',$prog);
	$mainArrRes[] = $row;
}
//$mainArrRes = array_merge($mainArrRes,$ivfaQryRes);

//--- GET DISC TEST -----
$sql = "select 'Disc' as TEST, patient_data.lname,patient_data.fname,patient_data.mname,
		patient_data.id, date_format(disc.examDate, '".get_sql_date_format('','y')."') as examDate,
		users.lname as phyLname,users.fname as phyFname,users.mname as phyMname,
		disc.disc_id as test_id,disc.discComments as COMMENTS,
		disc.stable as Stable, disc.fuApa as fuApa, disc.fuRetina as fuRetina,
		disc.monitorAg as MonitorAg,disc.contiMeds as contiMeds,disc.ptInformedNv
		from disc join patient_data on patient_data.id = disc.patientId
		join users on users.id = disc.phyName
		where disc.performedBy = '$user_id' and disc.tech2InformPt > '0'
		and disc.del_status = '0' and disc.purged = '0' and disc.ptInformed = '$filter'";
//$pachyQryRes = $objManageData->mysqlifetchdata();
$pachyQryRes = sqlStatement($sql);
for($i=0;$row=sqlFetchArray($pachyQryRes);$i++){
	$prog = array();
	if($row['Stable'] > 0){
		$prog[] = 'Stable';
	}
	if($row['contiMeds'] > 0){
		$prog[] = 'Continue Meds';
	}
	if($row['fuApa'] > 0){
		$prog[] = 'F/U APA';
	}
	if($row['MonitorAg'] > 0){
		$prog[] = 'Monitor AG';
	}
	if($row['fuRetina'] > 0){
		$prog[] = 'F/U Retina';
	}
	if($row['ptInformedNv'] > 0){
		$prog[] = 'Inform Pt result next visit';
	}	
	$row['Prognosis'] = join(', ',$prog);
	$mainArrRes[] = $row;
}
//$mainArrRes = array_merge($mainArrRes,$pachyQryRes);

//--- GET EXTERNAL/ANTERIOR TEST -----
$sql = "select 'External/Anterior' as TEST, patient_data.lname,
		patient_data.fname,patient_data.mname,
		patient_data.id, date_format(disc_external.examDate, '".get_sql_date_format('','y')."') as examDate,
		users.lname as phyLname,users.fname as phyFname,users.mname as phyMname,
		disc_external.disc_id as test_id,disc_external.discComments as COMMENTS,
		disc_external.stable as Stable, disc_external.fuApa as fuApa, disc_external.fuRetina as fuRetina,
		disc_external.monitorAg as MonitorAg,disc_external.contiMeds as contiMeds,disc_external.ptInformedNv
		from disc_external join patient_data on patient_data.id = disc_external.patientId
		join users on users.id = disc_external.phyName
		where disc_external.performedBy = '$user_id' and disc_external.tech2InformPt > '0'
		and disc_external.del_status = '0' and disc_external.purged = '0' and disc_external.ptInformed = '$filter'";
//$discExQryRes = $objManageData->mysqlifetchdata();
$discExQryRes = sqlStatement($sql);
for($i=0;$row=sqlFetchArray($discExQryRes);$i++){
	$prog = array();
	if($row['Stable'] > 0){
		$prog[] = 'Stable';
	}
	if($row['contiMeds'] > 0){
		$prog[] = 'Continue Meds';
	}
	if($row['fuApa'] > 0){
		$prog[] = 'F/U APA';
	}
	if($row['MonitorAg'] > 0){
		$prog[] = 'Monitor AG';
	}
	if($row['fuRetina'] > 0){
		$prog[] = 'F/U Retina';
	}
	if($row['ptInformedNv'] > 0){
		$prog[] = 'Inform Pt result next visit';
	}	
	$row['Prognosis'] = join(', ',$prog);
	$mainArrRes[] = $row;
}
//$mainArrRes = array_merge($mainArrRes,$discExQryRes);

//--- GET TOPOGRAPHY TEST -----
$sql = "select 'topography' as TEST, patient_data.lname,
		patient_data.fname,patient_data.mname,
		patient_data.id, date_format(topography.examDate, '".get_sql_date_format('','y')."') as examDate,
		users.lname as phyLname,users.fname as phyFname,users.mname as phyMname,
		topography.topo_id as test_id,topography.comments as COMMENTS,
		topography.prog,topography.treat,topography.ptInformedNv
		from topography join patient_data on patient_data.id = topography.patientId
		join users on users.id = topography.phyName
		where topography.performedBy = '$user_id' and topography.tech2InformPt > '0'
		and topography.del_status = '0' and topography.purged = '0' and topography.ptInformed = '$filter'";
//$topGraQryRes = $objManageData->mysqlifetchdata();
$topGraQryRes = sqlStatement($sql);
for($i=0;$row=sqlFetchArray($topGraQryRes);$i++){
	$prog = array();
	if(trim($row['prog']) != ''){
		$prog[] = $row['prog'];
	}
	if(trim($row['treat']) != ''){
		$prog[] = $row['treat'];
	}
	if($row['ptInformedNv'] > 0){
		$prog[] = 'Inform Pt result next visit';
	}
	$row['Prognosis'] = join(', ',$prog);
	$mainArrRes[] = $row;
}
//$mainArrRes = array_merge($mainArrRes,$topGraQryRes);

//--- GET CELL COUNT TEST -----
$sql = "select 'cell count' as TEST, patient_data.lname,
		patient_data.fname,patient_data.mname,
		patient_data.id, date_format(test_cellcnt.examDate, '".get_sql_date_format('','y')."') as examDate,
		users.lname as phyLname,users.fname as phyFname,users.mname as phyMname,
		test_cellcnt.test_cellcnt_id as test_id,
		test_cellcnt.stable,test_cellcnt.fuApa,test_cellcnt.ptInformedNv,test_cellcnt.contiMeds
		from test_cellcnt join patient_data on patient_data.id = test_cellcnt.patientId
		join users on users.id = test_cellcnt.phyName
		where test_cellcnt.performedBy = '$user_id' and test_cellcnt.tech2InformPt > '0'
		and test_cellcnt.del_status = '0' and test_cellcnt.purged = '0' and test_cellcnt.ptInformed = '$filter'";
		
//$cellQryRes = $objManageData->mysqlifetchdata();
$cellQryRes = sqlStatement($sql);
for($i=0;$row=sqlFetchArray($cellQryRes);$i++){
	$prog = array();
	if($row['stable'] > 0){
		$prog[] = 'Stable';
	}	
	if($row['fuApa'] > 0){
		$prog[] = 'F/U APA';
	}
	if($row['ptInformedNv'] > 0){
		$prog[] = 'Inform Pt result next visit';
	}
	if($row['contiMeds'] > 0){
		$prog[] = 'Continue Meds';
	}	
	$row['Prognosis'] = join(', ',$prog);
	$mainArrRes[] = $row;
}
//$mainArrRes = array_merge($mainArrRes,$cellQryRes);

//echo "<pre>";
//print_r($mainArrRes);
//exit();


?>
<form name="pat_notify_frm" id="pat_notify_frm" action="" method="post">
<?php 
if(count($mainArrRes) == 0)
{
	echo '<div class="text-center text-warning"> No Record Found</div>';	 
}
else
{ 
?> 
<table class="table table-bordered">
	<thead>
	<tr class="purple_bar">
            <th>Date</th>
            <th>Test</th>
            <th>Physician Name</th>
            <th>Prognosis</th>
            <th>Comments</th>
            <th>Patient Name</th>
            <th>Status</th>
        </tr>
    </thead> 
    <tbody>         	
    <?php
		foreach($mainArrRes as $key => $notify_details_arr)
		{
	?>
    <tr height="20" bgcolor="#FFFFFF" valign="top">
    	<td class="text_10">
        	<?php echo $notify_details_arr['examDate']; ?>
        </td>
        <td class="text_10">
        	<?php echo strtoupper($notify_details_arr['TEST']); ?>        	
        </td>
        <td class="text_10">
        	<?php echo $notify_details_arr['phyLname'].', '.$notify_details_arr['phyFname'].' '.$notify_details_arr['phyMname']; ?>
        </td>
        <td class="text_10">
        	<?php echo $notify_details_arr['Prognosis']; ?>
        </td>
        <td class="text_10">
	        <?php echo $notify_details_arr['COMMENTS']; ?>
        </td>
        <td class="text_10">
        	<?php echo $notify_details_arr['lname'].', '.$notify_details_arr['fname'].' '.$notify_details_arr['mname'].' - '.$notify_details_arr['id']; ?>
        </td>
        <td class="text_10">
        	<select name="<?php echo 'status['.$notify_details_arr['TEST'].'__'.$notify_details_arr['test_id'].']'; ?>" class="form-control" onchange="pat_notify_frm_submit();" >
            	<option value="0" <?php if($filter == 0) echo 'selected'; ?>>Not Informed</option>
                <option value="1" <?php if($filter == 1) echo 'selected'; ?>>Informed</option>
            </select>
        </td>
    </tr>
<?php 
	} 
}
?>
    </tbody>
</table>
</form>