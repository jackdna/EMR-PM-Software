<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
set_time_limit(900);

include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}

function download_file($file_name){
	$filename = $file_name;
	$content_type = "text/csv";
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	
	header("Cache-Control: private",false);
	header("Content-Description: File Transfer");
	
	header("Content-Type: ".$content_type."; charset=utf-8");
	//die();
	header("Content-disposition:attachment; filename=\"".$filename."\"");
	
	header("Content-Length: ".@filesize($filename));
	//echo filesize($filename);
	@readfile($filename) or die("File not found.");
	exit;	
}


$fac_qry	=	" and st.iasc_facility_id='$_SESSION[iasc_facility_id]' ";
$fac_con	=	($_SESSION['iasc_facility_id']	 ?	$fac_qry	 :	'' )	; 

if($_REQUEST['proc_save']=='yes') {
	$date1 = trim($_REQUEST["date1"]);
	$date2 = trim($_REQUEST["date2"]);
	
	list($frMnth,$frDay,$frYr)=explode("-",$date1);
	list($toMnth,$toDay,$toYr)=explode("-",$date2);
	
	$from_date 	= $frYr.'-'.$frMnth.'-'.$frDay;
	$to_date 	= $toYr.'-'.$toMnth.'-'.$toDay;
	
	$procedureArr = $_REQUEST["procedure"];
	$procedure_id = implode(",",$procedureArr);
	
	$physicianArr = $_REQUEST["physician"];
	$physicianImp = implode(",",$physicianArr);
	if(!$procedure_id) {  $procedure_id = '0';}
	if(!$physicianImp) {  $physicianImp = '0';}
	else
	{
		if($physicianImp!='all'){
		$physicianQry=" AND pc.surgeonId IN ($physicianImp)";}
	}
	 
	if(constant('STRING_SEARCH')=='YES')
	{	$proc_JOIN = "LEFT JOIN procedures ON(procedures.name = pc.patient_primary_procedure)"; 
	}else{
		$proc_JOIN = "LEFT JOIN procedures ON(procedures.procedureId = pc.patient_primary_procedure_id)"; 
	}
	
	$procIdQry = "";
	if($procedure_id!='all') {
		if(constant('STRING_SEARCH')=='YES')
		{
			$procedure_tbl=imw_query("select name from procedures where procedureId IN(".$procedure_id.") ");
			$proc=imw_fetch_array($procedure_tbl);
			$procedure_name.=($procedure_name)?",'$proc[name]'":"'$proc[name]'";	

			$procIdQry = " AND pc.patient_primary_procedure IN(".$procedure_name.") ";	
			$proc_JOIN = "LEFT JOIN procedures ON(procedures.name = pc.patient_primary_procedure)"; 
		}else{
			$procIdQry = " AND pc.patient_primary_procedure_id IN(".$procedure_id.") ";	 
			$proc_JOIN = "LEFT JOIN procedures ON(procedures.procedureId = pc.patient_primary_procedure_id)"; 
		}
			
	}
	
	$qry = "SELECT pc.ascId,pc.dos,pc.surgeonId,pc.patientId, if(pc.site=1,'Left Eye',(if(pc.site=2,'Right Eye',if(pc.site=3,'Both Eye','')))) AS ptSite,
			pdt.patient_fname,pdt.patient_mname,pdt.patient_lname,pdt.date_of_birth,
			if(pdt.sex='m','Male',(if(pdt.sex='f','Female',''))) AS ptGender,
			users.fname,users.mname,users.lname,procedures.name as proc_name
			FROM patientconfirmation pc
			INNER JOIN stub_tbl st ON(st.patient_confirmation_id=pc.patientConfirmationId AND st.patient_status!='Canceled')
			LEFT JOIN patient_data_tbl pdt ON(pc.patientId = pdt.patient_id)
			LEFT JOIN users ON(users.usersId = pc.surgeonId)
			$proc_JOIN
			WHERE (pc.dos BETWEEN '".$from_date."' AND '".$to_date."') ".$procIdQry." ".$fac_con."
			ORDER BY pc.dos,users.usersId,pc.surgery_time, pc.ascId ASC
			";	
	$res = imw_query($qry)or die(imw_error().' ----- ');
	$recExist=false;
	if(imw_num_rows($res)>0) {
		
		//$csvreport='First Name,Last Name,Middle Name,Surgery ID,Surgery Date,DOB,Implant Location,Physician ID,Physician First Name,Physician Middle Name,Physician Last Name,Gender,Surgery Description';
		$csvreport='';
		
		$file_name="admin/pdfFiles/proc_phy_reportpop.csv";
		@unlink($file_name);
		$fpH1 = fopen($file_name,'w');
		$content = '"First Name"'.','.'"Last Name"'.','.'"Middle Name"'.','.'"Surgery ID"'.','.'"Surgery Date"'.','.'"DOB"'.','.'"Implant Location"'.','.'"Physician ID"'.','.'"Physician First Name"'.','.'"Physician Last Name"'.','.'"Physician Middle Name"'.','.'"Gender"'.','.'"Surgery Description"';
		fwrite($fpH1, $content."\n");
		while($row = imw_fetch_array($res)) {
			
			if($row["surgeonId"] > 0 && (in_array($row["surgeonId"],$physicianArr) ||  $physicianImp  == "all") ) {
				$recExist=true;
				$surgery_date		= date("m-d-Y",strtotime($row["dos"]));
				$pt_DOB 			= date("m-d-Y",strtotime($row["date_of_birth"]));
				$ptFname='"'." ".'"';
				if(empty($row["patient_fname"])==false){
					$ptFname=$row['patient_fname'];
					$ptFname='"'.trim($ptFname).'"';
				}
				
				$ptLname='"'." ".'"';
				if(empty($row["patient_lname"])==false){
					$ptLname=$row["patient_lname"];
					$ptLname='"'.trim($ptLname).'"';
				}
				$ptMname='"'." ".'"';
				if(empty($row["patient_mname"])==false){
					$ptMname=$row["patient_mname"];
					$ptMname='"'.trim($ptMname).'"';
				}
				$surgeryId='"'." ".'"';
				if(empty($row["patientId"])==false){
					$surgeryId=$row["patientId"];
					$surgeryId='"'.trim($surgeryId).'"';
				}
				$surgeryDate='"'." ".'"';
				if(empty($surgery_date)==false){
					$surgeryDate=$surgery_date;
					$surgeryDate='"'.trim($surgeryDate).'"';
				}
				$ptDOB='"'." ".'"';
				if(empty($pt_DOB)==false){
					$ptDOB=$pt_DOB;
					$ptDOB='"'.trim($ptDOB).'"';
				}
				$implantLocation='"'." ".'"';
				if(empty($row["ptSite"])==false){
					$implantLocation=$row["ptSite"];
					$implantLocation='"'.trim($implantLocation).'"';
				}
				$phyId='"'." ".'"';
				if(empty($row["surgeonId"])==false){
					$phyId=$row["surgeonId"];
					$phyId='"'.trim($phyId).'"';
				}
				$phyFname='"'." ".'"';
				if(empty($row["fname"])==false){
					$phyFname=$row["fname"];
					$phyFname='"'.trim($phyFname).'"';
				}
				$phyMname='"'." ".'"';
				if(empty($row["mname"])==false){
					$phyMname=$row["mname"];
					$phyMname='"'.trim($phyMname).'"';
				}
				$phyLname='"'." ".'"';
				if(empty($row["lname"])==false){
					$phyLname=$row["lname"];
					$phyLname='"'.trim($phyLname).'"';
				}
				$ptGender='"'." ".'"';
				if(empty($row["ptGender"])==false){
					$ptGender=$row["ptGender"];
					$ptGender='"'.trim($ptGender).'"';
				}
				$surgeryDescription='"'." ".'"';
				if(empty($row["proc_name"])==false){
					$surgeryDescription=$row["proc_name"];
					$surgeryDescription='"'.trim(stripslashes($surgeryDescription)).'"';
				}
				
				$content=$ptFname.','.$ptLname.','.$ptMname.','.$surgeryId.','.$surgeryDate.','.$ptDOB.','.$implantLocation.','.$phyId.','.$phyFname.','.$phyLname.','.$phyMname.','.$ptGender.','.$surgeryDescription;
				
				fwrite($fpH1, $content."\n");
			}
		}
		if($recExist == false) {
			echo "<h2>No record</h2>";	
		}else {
			download_file($file_name);
		}
		fclose($fpH1);
	
	
	}
	else{
		?>
			<script>
				location.href="proc_phy_report.php?noRecord=yes";
			</script>
		<?php
		
		
			include("common/link_new_file.php");
			
			echo '<div class=" subtracting-head">';
			echo '<div class="head_scheduler new_head_slider padding_head_adjust_admin">';
			echo '<span>Proc CSV Report </span>';
			echo '</div>';
			echo '</div>';
			
			
			echo '<div class="row"></div>';
			echo '<div class="clearfix margin_adjustment_only"></div>';
			echo '<div class="clearfix margin_adjustment_only"></div>';
			echo '<div class="col-log-12 col-md-12 col-xs-12 col-sm-12"';
			echo '<div class="col-log-6 col-md-6 col-xs-12 col-sm-12"';
			echo '<div class="rowaudit_wrap">';
			echo '<div class="form_outer">';
			echo '<div class="clearfix margin_adjustment_only"></div>';
			
			echo '<div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">';
			echo '<div class="form_reg">';
			
			echo '<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">';
			
			echo '<div class="form_reg text-center">';
			echo '<label class="" for="text">';
			echo '<h2>No Record</h2>';
			echo '</label>';
			echo '</div>';
			
            echo '</div>';
			
			echo '</div>';
            
			echo '</div>';
			
			echo '</div>';
			
			echo '</div>';
			
			echo '</div>';
			
			echo '</div>';
	}
}
?>
