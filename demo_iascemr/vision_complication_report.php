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
	include_once("admin/classObjectFunction.php");
	$objManageData = new manageData;
  include_once('common/user_agent.php');
	
	// query part to exclude surgeons in vcna reports
	$usersExclude =	'';
	$surgeonExclude = '';
	if( defined('VCNA_SURGEON_EXCLUDE') && constant('VCNA_SURGEON_EXCLUDE') )
	{
		$usersExclude = 'And usersId NOT IN ('.constant('VCNA_SURGEON_EXCLUDE').') ';	
		$surgeonExclude = 'And pc.surgeonId NOT IN ('.constant('VCNA_SURGEON_EXCLUDE').')';	
	}

?>
<!DOCTYPE html>
<html>
<head>
<title>Vision Success & Complication</title>
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


//GET LOGGED IN USER TYPE
	unset($conditionArr);
	$conditionArr['usersId'] = $_SESSION["loginUserId"];
	$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if($surgeonsDetails){
		foreach($surgeonsDetails as $usersDetail)
		{
			$loggedInUserType = $usersDetail->user_type;
			$loggedInCoordinatorType = $usersDetail->coordinator_type;	
		}
	}
	$physicianOptHidden	= true;	
	if($loggedInUserType == 'Coordinator' && $loggedInCoordinatorType == 'Master')
	{
		$physicianOptHidden	=	false;	
	}
	//END GET LOGGED IN USER TYPE
	


$date1 = $_REQUEST["date1"];
$date2 = $_REQUEST["date2"];
$reqstProcedureArr = array();
$procedureImp	=	unserialize(base64_decode($_REQUEST["procedureImp"]));
/*if($_REQUEST["procedureImp"]) {
	$reqstProcedureArr = explode(",",$_REQUEST["procedureImp"]);
}*/
$reqstPhyArr = array();
if($_REQUEST["physicianImp"]) {
	$reqstPhyArr = explode(",",$_REQUEST["physicianImp"]);
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
	
	top.$(".loader").fadeIn('fast').show('fast');
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
	<form name="proc_phy_report" action="vision_complication_reportpop.php" method="post">
  		<input type="hidden" name="proc_save" id="proc_save" value="">
      <div class="container-fluid padding_0">
        	<div class="inner_surg_middle ">
            
            		<div style="" id="" class="all_content1_slider ">	         
                    
                          <div class="wrap_inside_admin">
                          	<div class=" subtracting-head">
                            	<div class="head_scheduler new_head_slider padding_head_adjust_admin">
                                	<span>Vision Success & Complication</span>
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
																	$procGroup=imw_query("select * from proceduregroup where del_status = '' order by name");
																	if(imw_num_rows($procGroup)>=1)
																	{
																	echo'<optgroup label="Procedures Group">';
																	while( $groups=imw_fetch_assoc($procGroup))
																	{
																		$procedure_id	=	$groups['procedures'];
																		$sel_proc		=	'';
																		if(in_array($procedure_id,$procedureImp)) {
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
																	$proc=imw_query("select * from procedures where del_status = '' order by name");
																	while( $procedure1=imw_fetch_assoc($proc))
																	{
																		$procedure_id	=	$procedure1['procedureId'];
																		$sel_proc		=	'';
																		if(in_array($procedure_id,$procedureImp)) {
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
                                                    	<?php if(!$physicianOptHidden) { ?>
                                                      	<option value="all" data-attending= "0" <?php if(in_array('all',$reqstPhyArr)) { echo "selected";}?> > All Physicians</option>
                                                      <?PHP } ?>
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
																													elseif($_SESSION["loginUserId"] == $physician_id)
																													{
																															$sel_phy		=	'selected';	
																													}
																													$displayStatus	=	false;
																													if(($_SESSION["loginUserId"] == $physician_id && $physicianOptHidden) || (!$physicianOptHidden)  )
																													{
																														$displayStatus	=	true;
																													}
																													
																													if($displayStatus)
																													{
																											?>	
                              															<option <?=$disabledStatus?> data-attending = "1" value="<?php echo $physician_id;?>" <?php echo $sel_phy;?>><?php echo $physician_name.' - '.$physician_id;?></option>
                                                      <?PHP
																													}
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
	if($_REQUEST['no_rec']=='true')
	{
		?>
			<script>
				modalAlert("No Record Found !");
			</script>
		<?php
	}
?>