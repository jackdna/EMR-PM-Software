<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
	session_start();
	include("common/conDb.php");
	if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
		echo '<script>top.location.href="index.php"</script>';
	}
  include_once('common/user_agent.php');
	include_once("admin/classObjectFunction.php");
	$objManageData = new manageData;
	
	// query part to exclude surgeons in vcna reports
	$usersExclude =	'';
	$surgeonExclude = '';
	if( defined('VCNA_SURGEON_EXCLUDE') && constant('VCNA_SURGEON_EXCLUDE') )
	{
		$usersExclude = 'And usersId NOT IN ('.constant('VCNA_SURGEON_EXCLUDE').') ';	
		$surgeonExclude = 'And pc.surgeonId NOT IN ('.constant('VCNA_SURGEON_EXCLUDE').')';	
	}
	
  $fac_qry	=	" and st.iasc_facility_id='$_SESSION[iasc_facility_id]' ";
	$fac_con	=	($_SESSION['iasc_facility_id']	 ?	$fac_qry	 :	'' ); 

$reqstPhyArr = array();
if($_REQUEST['proc_save']=='yes') {
	$date1 = trim($_REQUEST["date1"]);
	$date2 = trim($_REQUEST["date2"]);
	
	$reqstProcedureArr = array();
	if($_REQUEST["procedure"]) {
		$reqstProcedureArr = is_array($_REQUEST["procedure"]) ? $_REQUEST["procedure"] : explode(",",$_REQUEST["procedure"]);
	}
	
	if($_REQUEST["physician"]) {
		$reqstPhyArr = is_array($_REQUEST["physician"]) ? $_REQUEST["physician"] : explode(",",$_REQUEST["physician"]);
	}
	
	$from_date 	= $objManageData->changeDateYMD($date1);
	$to_date 	= $objManageData->changeDateYMD($date2);
	
	$procedureIdTmp = implode(",", $reqstProcedureArr);
	$tmpReqProcArr 	= array_values(array_unique(array_filter(explode(",",$procedureIdTmp))));
	$procedure_id 	= implode(",", $tmpReqProcArr);
	
	//$physicianArr = $_REQUEST["physician"];
	$physicianImp = implode(",",$reqstPhyArr);
	if(!$procedure_id) {  $procedure_id = '0';}
	if(!$physicianImp) {  $physicianImp = '0';} 
	else
	{
		if($physicianImp!='all'){
		$physicianQry=" AND pc.surgeonId IN ($physicianImp)";}
		
		if($physicianImp =='all'){
		$physicianQry = $surgeonExclude;}
	}
	$proc_JOIN = "";
	if( defined('STRING_SEARCH') && constant('STRING_SEARCH')=='YES')
	{	$proc_JOIN = "LEFT JOIN procedures ON(procedures.name = pc.cost_procedure_name)"; 
	}else{
		$proc_JOIN = "LEFT JOIN procedures ON(procedures.procedureId = pc.cost_procedure_id)"; 
	}
	
	$procIdQry = "";
	if($procedure_id!='all') {
		if( defined('STRING_SEARCH') && constant('STRING_SEARCH')=='YES')
		{
			$procedure_tbl=imw_query("select procedureId, name from procedures where procedureId IN(".$procedure_id.") ");
			$procedure_name = array();
			while($proc=imw_fetch_array($procedure_tbl)){
				array_push($procedure_name,"'".$proc['name']."'");
			}
			$procNameImplode = implode(",",$procedure_name);
			$procIdQry = " AND pc.cost_procedure_name IN(".$procNameImplode.") ";	
			$proc_JOIN = "LEFT JOIN procedures ON(procedures.name = pc.cost_procedure_name)"; 
		}else{
			$procIdQry = " AND pc.cost_procedure_id IN(".$procedure_id.") ";	 
			$proc_JOIN = "LEFT JOIN procedures ON(procedures.procedureId = pc.cost_procedure_id)"; 
		}
			
	}
	
	$qry = "SELECT pc.surgeon_time_in_mins, pc.surgery_time_in_mins, pc.cost_procedure_id as proc_id, pc.cost_procedure_name as proc_name, pc.patientConfirmationId,
			users.fname, users.mname, users.lname,
			procedures.name as proc_name
			FROM patientconfirmation pc
			INNER JOIN stub_tbl st ON(st.patient_confirmation_id=pc.patientConfirmationId AND st.patient_status!='Canceled')
			LEFT JOIN patient_data_tbl pdt ON(pc.patientId = pdt.patient_id)
			LEFT JOIN users ON(users.usersId = pc.surgeonId)
			$proc_JOIN
			WHERE (pc.dos BETWEEN '".$from_date."' AND '".$to_date."') ".$fac_con."
			AND pc.cost_procedure_id > 0
			AND pc.finalize_status='true'
			AND pc.surgeon_time_in_mins>0 ".$physicianQry;		
	$res = imw_query($qry)or die(imw_error().' ----- ');
	$recExist=false;//die($qry);
	$superBillProcIdArr = $objManageData->superBillProcIdArrFun();	
	if(imw_num_rows($res)>0) {
		while($row = imw_fetch_array($res)) {
			$pConfId = $row["patientConfirmationId"];
			if(count(array_intersect($superBillProcIdArr[$pConfId], $tmpReqProcArr)) == count($tmpReqProcArr) && count($superBillProcIdArr[$pConfId]) == count($tmpReqProcArr)){
				$recExist = true;
				break;
			}
		}
	}
	
	if(imw_num_rows($res)==0 || $recExist == false) {
		$_REQUEST['noRecord'] = "yes";
	}
	else
	{
		//redirect on pop up window	
		?>
		<form name="or_time_report_sub" id="or_time_report_sub" method="post" action="or_time_reportpop.php" target="or_time_report_win" >
			<input type="hidden" name="proc_save" value="<?php echo $_REQUEST["proc_save"];?>">
			<input type="hidden" name="date1" value="<?php echo $_REQUEST["date1"];?>">
			<input type="hidden" name="date2" value="<?php echo $_REQUEST["date2"];?>">
			<input type="hidden" name="physician" value="<?php echo $physicianImp;?>">
			<input type="hidden" name="procedure" value="<?php echo $procedure_id;?>">
		</form>
		
        <script>
			var parWidth = parent.document.body.clientWidth;
			var parHeight = parent.document.body.clientHeight;
			var win=window.open('or_time_reportpop.php','or_time_report_win','width='+parWidth+',height='+parHeight+' top=10,left=10,resizable=yes,scrollbars=1');
			win.focus();
			document.getElementById('or_time_report_sub').submit();
			/*
			var win=window.open('or_time_reportpop.php?<?php echo "proc_save=$_REQUEST[proc_save]&date1=$_REQUEST[date1]&date2=$_REQUEST[date2]&physician=$physicianImp&procedure=$procedure_id";?>','Report_result','width='+parWidth+',height='+parHeight+' top=10,left=10,resizable=yes,scrollbars=1');
			win.focus();
			*/
		</script>
        <?php
	}
}
   ?>
<!DOCTYPE html>
<html>
<head>
<title>OR Time: Surgeon Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
$spec = "
</head>
<body onLoad=\"MM_preloadImages('images/generate_report_hover.jpg','images/reset_hover.jpg')\">
";
$currDt = date("m-d-Y");
include("common/link_new_file.php");
include_once("no_record.php");
if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
	$get_http_path = 'https';
	 }
elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
{
	$get_http_path= 'http';
}									



if(!$date1) { $date1 = $currDt; }
if(!$date2) { $date2 = $currDt; }

?>							
<script>
var today = new Date();
var day = today.getDate();
var month = today.getMonth()
var year = y2k(today.getFullYear());
var mon=month+1;
if(mon<=9){
	mon='0'+mon;
}
var todaydate=mon+'-'+day+'-'+year;
function y2k(number){
	return (number < 1000)? number+1900 : number;
}
function newWindow(q){
	
	mywindow=open('mycal1.php?md='+q,'','width=200,height=250,top=200,left=300');
	mywindow.location.href = 'mycal1.php?md='+q;
	if(mywindow.opener == null)
		mywindow.opener = self;
}
function restart(q){
	fillDate = ''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year;
	if(q==8){
		if(fillDate > todaydate){
			modalAlert("Date Of Service can not be a future date")
			return false;
		}
	}
	document.getElementById("date"+q).value=fillDate;
	mywindow.close();
}
function padout(number){
return (number < 10) ? '0' + number : number;
}

function reportpop(){
	var msg="Please fill in the following:- \n";
	var flag = 0;
	var f1 = document.proc_phy_report.procedure.value;	
	var f2 = document.proc_phy_report.physician.value;
	var f3 = document.proc_phy_report.date1.value;
	var f4 = document.proc_phy_report.date2.value;
	
	//if(f1 === '')		f1 = 'all';
	//if(f2 === '')	f2 = 'all';
	
	if(f1==''){ msg = msg+"\t Procedure\n"; ++flag; }
	if(f2==''){ msg = msg+"\t Physician\n"; ++flag; }
	if(f3==''){ msg = msg+"\t Date From\n"; ++flag; }
	if(f4==''){ msg = msg+"\t Date To\n"; ++flag; }
	if(flag > 0)
	{
		modalAlert(msg);
		return false;	
	}
	document.proc_phy_report.proc_save.value='yes';
	document.proc_phy_report.submit();
	return true;	
		
}

function resetfields() {
 
	document.proc_phy_report.procedure.value = "all";
	document.proc_phy_report.physician.value = "all";
	$('select').selectpicker('render');
	document.proc_phy_report.date1.value = "";
	document.proc_phy_report.date2.value = "";
 
}
</script>

<div class="main_wrapper">
	<form name="proc_phy_report" action="" method="post">
    	
        <input type="hidden" name="proc_save" id="proc_save" value="">
        
    	<div class="container-fluid padding_0">
        	<div class="inner_surg_middle ">
            
            		<div style="" id="" class="all_content1_slider ">	         
                    
                          <div class="wrap_inside_admin">
                          	<div class=" subtracting-head">
                            	<div class="head_scheduler new_head_slider padding_head_adjust_admin">
                                	<span>OR Time: Surgeon Report</span>
                               	</div>
                          	</div>
                            
                            <Div class="wrap_inside_admin">
                            	
								<div class="col-md-2 visible-md"></div>
                                <div class="col-lg-3 visible-lg"></div>
                                
                                <div class="col-md-8 col-sm-12 col-xs-12 col-lg-6">
                                     
                                    <div class="audit_wrap">
                                    	<div class="form_outer">
                                        	<Div class="row">
                                            	<div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
                                               		<div class="form_reg">
                                                    	
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<label for="pro_select" class="text-left">Procedures Group / Procedure</label>
                                                      	</div>
                                                        
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<select class="selectpicker form-control" name="procedure[]" id="procedure" multiple="multiple" title='Select Procedures' data-size="10" >
                                                            	<?PHP
																	$procGroup=imw_query("select * from proceduregroup where del_status=''  order by name");
																	if(imw_num_rows($procGroup)>=1)
																	{
																	echo'<optgroup label="Procedures Group">';
																	while( $groups=imw_fetch_assoc($procGroup))
																	{
																		$procedure_id	=	$groups['procedures'];
																		$sel_proc		=	'';
																		if(in_array($procedure_id,$reqstProcedureArr)) {
																			$sel_proc		=	'selected';	
																		}
																		$procedure_name= stripslashes($groups['name']);
																		echo'<option data-attending="1" value="'.$procedure_id.'" '.$sel_proc.'>'.$procedure_name.'</option>';
																	}
																	echo'</optgroup>';
																	}
																?>
                                                                
                                                                
                                                                <optgroup label="Procedures">
                                                            	
                                                            	<?PHP
																	$proc=imw_query("select * from procedures where del_status=''  order by name");
																	while( $procedure1=imw_fetch_assoc($proc))
																	{
																		$procedure_id	=	$procedure1['procedureId'];
																		$sel_proc		=	'';
																		if(in_array($procedure_id,$reqstProcedureArr)) {
																			$sel_proc		=	'selected';	
																		}
																		$procedure_name= stripslashes($procedure1['name']);
																		echo'<option data-attending="1" value="'.$procedure_id.'" '.$sel_proc.'>'.$procedure_name.'</option>';
																	}
																?>
                                                                </optgroup>
                                                          	</select>
                                                            
                                                            
                                                      	</div>
                                                		<!----------------------- Full Inout col-12    ------------------------------>
                                                        
                                                    </div>
                                               	</div>
                                                
                                                <div class="clearfix margin_adjustment_only visible-sm"></div>
                                                <div class="clearfix margin_adjustment_only visible-xs"></div>                                                    
                                                
                                                <div class="col-md-6 col-sm-12 col-lg-6 col-xs-12">
                                                	<div class="form_reg">
                                                    	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<label for="p_select" class="text-left">Physician</label>
                                                        </div>
                                                        
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	
                                                            <select class="selectpicker form-control" name="physician[]" id="physician" multiple="multiple" title="Select Physicians" >
                                                            	<option value="all" data-attending= "0" <?php if(in_array('all',$reqstPhyArr) || count($reqstPhyArr) == 0) { echo "selected";}?> > All Physicians</option>
															<?PHP
																$physician	=	imw_query("select * from users where user_type='Surgeon' ".$usersExclude." and deleteStatus!='Yes' order by lname");
																while( $physician1=imw_fetch_array($physician))
																{
																		$physician_id	=	$physician1['usersId'];
																		$physician_fname=	$physician1['fname'];
																		$physician_mname=	$physician1['mname'];
																		$physician_lname=	$physician1['lname'];
																		$physician_name	=	stripslashes($physician_lname.",".$physician_fname);
																		$sel_phy = '';
																		if(in_array($physician_id,$reqstPhyArr)) {
																			$sel_phy		=	'selected';	
																		}
															?>
                                                            			<option data-attending = "1" value="<?php echo $physician_id;?>" <?php echo $sel_phy;?>><?php echo $physician_name.' - '.$physician_id;?></option>
                                                           	<?PHP
																}
															?>
                                                            </select>
                                                            
                                                       	</div> <!----------------------- Full Inout col-12    ------------------------------>
                                                  	</div>
                                                 </div>
                                                 
                                              	<div class="clearfix margin_adjustment_only"></div>
                                                
                                                <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">
                                                	<div class="form_reg text-center">
                                                    	<label class="date_r">Select Date</label>
                                                  	</div>
                                               	</div>
                                                
                                                <div class="clearfix margin_adjustment_only  its_line"></div>
                                                <div class="clearfix margin_adjustment_only"></div>                                                  
                                                
                                                <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                                	<div class="form_reg">
                                                    	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<label for="from" class="">From</label>
                                                       	</div>	
                                                        
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<div id="datetimepicker1" class="input-group">
                                                            	<input type="text" class="form-control" tabindex="3" id="date1" name="date1" value="<?php echo $date1;?>"/>
                                                                <div class="input-group-addon datepicker">
                                                                	<a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
                                                               	</div>
                                                           	</div>
                                                       	</div>
                                                   	</div>
                                              	</div>
                                                
                                                <Div class="clearfix margin_adjustment_only visible-sm"></Div>
                                                <Div class="clearfix margin_adjustment_only visible-xs"></Div>
                                                
                                                <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                                	<div class="form_reg">
                                                    	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<label for="to" class="">To</label>
                                                       	</div>
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                        	<div id="datetimepicker2" class="input-group">
                                                            	<input type="text" class="form-control" tabindex="4" id="date2" name="date2" value="<?php echo $date2;?>" />
                                                                <div class="input-group-addon datepicker">
                                                                	<a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
                                                               	</div>
                                                          	</div>
                                                      	</div>
                                                   	</div>
                                               	</div>
                                                
                                                <div class="clearfix margin_adjustment_only"></div>
                                                
                                          	</Div>
                                       	</div>
                                 	</div>
                                    
                                    <div class="btn-footer-slider">
                                    	
                                        <a href="javascript:void(0)" class="btn btn-info" id="generate_report" onclick="return reportpop('http');">
						   <b class="fa fa-edit"></b> Generate Report 
						</a>
                        
                                        
                                        <a class="btn btn-default" href="javascript:void(0)" id="reset" onClick="return resetfields();">
                                        	<b class="fa fa-refresh"></b> Reset
                                       	</a>
                                   	</div>
                                    
                              	</div>
                            
                            </Div>
                            
                            
                    </div> 
                  </div>  
                  <!-- NEcessary PUSH     -->	 
                  <Div class="push"></Div>
                  <!-- NEcessary PUSH     -->
            </div>
        </div>
        
  	</form>
</div>

<?php 
	if($_REQUEST['noRecord'] == "yes")
	{
		?>
			<script>
				modalAlert("No Record Found !");
			</script>
		<?php
	}
?>