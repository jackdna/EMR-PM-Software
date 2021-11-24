<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/	
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");// always modified
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);// HTTP/1.0header("Pragma: no-cache"); 

require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_page_functions.php');
//for printing purpose
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');//to get save location
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');//function to write html
function mysql_date_format($strdate){
	if($strdate!=""){
		$date_array=explode("-",$strdate);
		$sqldate=$date_array["2"]."-".$date_array["0"]."-".$date_array["1"];//yy,mm,dd
	}else{
		$sqldate="0000-00-00";
	}
	return($sqldate);	
}
$date_c=date("Y-m-d");
/* save first availbe new direct entry using ajax */
if($_POST['saveFAForm']=='yes')
{
	list($y,$m)=explode('-', $_POST[month]);
	$providers = implode(',', $_POST['provider_id']);
	$facilities = implode(',', $_POST['facilities']);
	imw_query("insert into schedule_first_avail set sel_year='$y',
				sel_month='$m',
				sel_week='$_POST[week]',
				sel_time='$_POST[time]',
				date_of_act='".date('Y-m-d')."',
				date_time_of_act='".date('Y-m-d H:i:s')."',
				pat_id='$_POST[pt_id_hidden]',
				pat_name='$_POST[pt_name_hidden]',
				provider_id='$providers',
				facility_id='$facilities',
				procedure_id='$_POST[sel_proc_id]',
				operator_id='$_SESSION[authUserID]'")or die(imw_error());
				//die($_SERVER['PHP_SELF'] ."&msg=succ");
	$_SESSION['msg']='succ';
	header("location:".$GLOBALS['webroot']."/interface/scheduler/to_do_first_avai.php?first_avail=1");
	exit;
}
/* patient mini search using ajax */
if($_REQUEST['pt_detail']){
	$str='<table class="table table-striped table-bordered table-hover">
		<thead>
	   <tr class="section_header">
		 <th>ID</th>
		 <th width="110" align="left">Last Name </th>
		 <th width="92" align="left">First Name </th>
		 <th width="92" align="left">DOB</th>
		 <th width="92" align="left">Gendar</th>
		 <th width="92" align="left">Street 1 </th>
		 <th width="92" align="left">Street 2 </th>
	   </tr>
	   </thead>
	   ';
	   
	 
	$tr="";
	$q_field="";
	if(is_numeric($_REQUEST['pt_detail'])){
		$q_field=" id=".$_REQUEST['pt_detail'];
	}else{
		$q_field=" lname like '".$_REQUEST['pt_detail']."%'";	
	}
	$str.='<tbody>';
	$qry_insert="SELECT id,fname,lname,date_format(DOB,'%m-%d-%Y') as DOB,sex,street,street2 from patient_data where ".$q_field;
	$res_insert=imw_query($qry_insert)or die(imw_error());
	while($row_insert=imw_fetch_object($res_insert)){
		$str.='<tr onClick="javascript:getpid('.$row_insert->id.',\''.$row_insert->lname.' '.$row_insert->fname.'\');">
		 <td>'.$row_insert->id.'</td>
		 <td>'.$row_insert->lname.'</td>
		 <td>'.$row_insert->fname.'</td>
		 <td>'.$row_insert->DOB.'</td>
		 <td>'.$row_insert->sex.'</td>
		 <td>'.$row_insert->street.'</td>
		 <td>'.$row_insert->street2.'</td>
	   </tr>';
		
	}
	
	$str.='</tbody></table>';
	echo $str;
	die();
}
/* load week and week days*/
if($_REQUEST['load_wk'])
{
	list($y,$m)=explode('-',$_REQUEST['load_wk']);
	
	$weekArr=getDatesForWK($m,$y);
	
	$month=date('M', mktime(0,0,0,$m,01,$y));
	for($wk=1;$wk<=weeks_in_month($m, $y);$wk++)
	{
		$str=$weekArr[$wk];
		$str=trim($str);
		$sbstr=substr($str,0,strlen($weekArr[$wk])-2);
		
		echo'<option value="'.$wk.'"';
		echo($wk==$w)?' selected':'';
		echo'>Week '.$wk.' ['.$sbstr.' '.$month.']</option>';
	}
	die();
}

/* call function and store date array*/
$dateArr=week_array();

//scheduler object
$obj_scheduler = new appt_scheduler();
/* per page record*/
$page_limit = 200;
if(isset($_REQUEST['cur_page_no']) && trim($_REQUEST['cur_page_no']) != "")
{
	$cur_page_no = $_REQUEST['cur_page_no'];
}
else
{
	$cur_page_no = 1;	
}

$limit_follow = ($cur_page_no-1)*$page_limit;
$printData = '';
//getting all facilities name
$qryFac="select id,name from facility order by name";
$resultFac=imw_query($qryFac);
$arrFacility=array();
while($Facarr = imw_fetch_array($resultFac)){
	$arrFacility[$Facarr['id']]=$Facarr['name'];
}
//geting user list that have and enable scheduler
$qryPro = $qry = "select id, fname, lname, mname from users where Enable_Scheduler = '1' and delete_status = '0' order by lname, fname";
$resultPro = imw_query($qryPro);
$arr_users=array();
while($ProArr = imw_fetch_assoc($resultPro)){
	$arr_users[$ProArr["id"]]=core_name_format($ProArr["lname"], $ProArr["fname"], $ProArr["mname"]);
}
//getting selected facility name provider ids
$fac_id_req=$_REQUEST['facility_id'];
$prov_id_req=$_REQUEST['prov_id'];

if($_REQUEST['searchText'])
{
	$search_str=trim(imw_real_escape_string($_REQUEST[searchText]));
	if(is_numeric($_REQUEST[searchText]))
	{
		$addStr =" AND fa.pat_id LIKE '%$search_str%' "; 
		$addStr1 =" AND sa_patient_id LIKE '%$search_str%' "; 	
	}
	else
	{
		if((stristr(trim($_REQUEST[searchText]),' ')===false))
		{
			$addStr =" AND fa.pat_name LIKE '%$search_str%' "; 
			$addStr1 =" AND sa_patient_name LIKE '%$search_str%' "; 	
		}
		else
		{
			$strArr=explode(' ',$search_str);
			$addStr =" AND (
					fa.pat_name LIKE '%$strArr[0]%' 
					OR fa.pat_name LIKE '%$strArr[1]%'
					)";
					
			$addStr1 =" AND (
					sa_patient_name LIKE '%$strArr[0]%' 
					OR sa_patient_name LIKE '%$strArr[1]%'
					)"; 	
		}
	}
}

if(!empty($fac_id_req)){
	$addStr .=" AND FIND_IN_SET('$fac_id_req',fa.facility_id) > 0";
	$addStr1 .=" AND sa.sa_facility_id='$fac_id_req'"; 	
	//$count_qry .=" AND schedule_appointments.sa_facility_id = '$fac_id_req'"; 
}
if(!empty($prov_id_req)){
	$addStr .=" AND FIND_IN_SET('$prov_id_req',fa.provider_id) > 0";
	$addStr1 .=" AND sa.sa_doctor_id = '$prov_id_req'"; 	
}

if(!empty($from_date)){
	$from_date=mysql_date_format($from_date);
	$addStr .=" AND fa.date_of_act >= '$from_date'"; 	
	$addStr1 .=" AND ps.status_date >= '$from_date'"; 
}

if(!empty($to_date)){
	$to_date=mysql_date_format($to_date);
	$addStr .=" AND fa.date_of_act <= '$to_date'"; 	
	$addStr1 .=" AND ps.status_date <= '$to_date'"; 
}

			
		
$reqQry = "
			SELECT fa.id, fa.sch_id, fa.pat_id as sa_patient_id, fa.pat_name as sa_patient_name, fa.operator_id, 
			fa.provider_id,fa.facility_id as sa_facility_id, 
			date_format(fa.date_time_of_act,'%m-%d-%y') as entered_status_date,fa.date_time_of_act as status_time,sp.proc,
			date_format(fa.date_of_act,'%m-%d-%y') as sa_app_start_date,CONCAT(fa.sel_year,'-',fa.sel_month,'-',fa.sel_week) as sa_app_starttime
			FROM schedule_first_avail fa 
			LEFT JOIN users u ON u.id IN (fa.provider_id) 
			LEFT JOIN facility f ON f.id IN (fa.facility_id)
			LEFT JOIN slot_procedures sp ON sp.id = fa.procedure_id
		 	WHERE fa.del_status=0 and fa.sch_id=0 $addStr
			 
		 	UNION
			 
	 		SELECT ps.id, ps.sch_id,sa_patient_id, sa_patient_name, sa_madeby, 
			sa_doctor_id as provider_id,sa_facility_id,
			date_format(ps.status_date,'%m-%d-%y') as entered_status_date,ps.status_time,sp.proc,
			date_format(sa.sa_app_start_date,'%m-%d-%y') as sa_app_start_date,sa.sa_app_starttime
			FROM schedule_appointments sa 
			LEFT JOIN users u ON sa.sa_doctor_id = u.id 
			LEFT JOIN facility f ON f.id = sa.sa_facility_id 
			LEFT JOIN previous_status ps ON ps.sch_id = sa.id
			LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid
			WHERE sa.sa_patient_app_status_id = 271  
			and ps.status = 271 $addStr1
			";
		
		$reqQry.=" group by ps.sch_id order by status_time desc";
		
	$count_qry_ob = imw_query($reqQry);
	
	$total_appts = imw_num_rows($count_qry_ob);
	$total_pages = ceil($total_appts/$page_limit);
	$reqQry .=" LIMIT ".$limit_follow.", ".$page_limit;
	if($total_appts == 0)
	{
		$total_pages = 1;
		$limit_follow += -1;		
	}
	if($cur_page_no > $total_pages){$cur_page_no = $total_pages;}
	$vsql_pro1 = imw_query($reqQry);	
	//echo $qry;
	$num_pro1=imw_num_rows($vsql_pro1);
	if($_REQUEST['check_rows_todo']){
		$row_check=1;
		if($num_pro1>0){
			$row_check=2;	
		}
		echo $row_check;die();
	}
?>
<!DOCTYPE html>
<head>
<title>To-Do</title>
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/schedulemain.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.datetimepicker.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-multiselect.css" type="text/css">
    
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.datetimepicker.full.min.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-select.min.js"></script>
    
<script type="text/javascript">
	<?php if(trim($_REQUEST['restore_act']) == 'restore_true'){echo 'window.opener.change_date(\'sel_date\');';} ?>	
	function edit_schedule(sch_id,st_id,dt,pro,fac,patid)
	{		opener.pre_load_front_desk(patid, sch_id);
			opener.focus();
			window.close();
	}
	function see_sel()
	{	
			window.location.reload();
	}
	function close_me()
	{	
				//opener.see_sel();						
				window.close();
	}	

	function strstr (haystack, needle, bool) {
		var pos = 0;
		
		haystack += '';
		pos = haystack.indexOf( needle );
		if (pos == -1) {
			return false;
		} else{
			if (bool){
				return haystack.substr( 0, pos );
			} else{
				return haystack.slice( pos );
			}
		}
	}
	function searchForTextNew()
	{
		document.facFrm.submit();
	}
	function searchForText(){
		var searchText = document.getElementById("searchText").value.toLowerCase();
		//if(searchText != ""){
			var loopTime = document.getElementsByName("searchArea[]").length;
			var f1=1;
			for(i = 0; i < loopTime; i++){			
				var hidId = document.getElementsByName("searchArea[]")[i].id;
				var tdId = "td"+hidId.substring(3);
				if(document.getElementById(tdId)){
					document.getElementById(tdId).className = "";			
				}
			}
			for(i = 0; i < loopTime; i++){			
				var hidId = document.getElementsByName("searchArea[]")[i].id;
				var txt = document.getElementById(hidId).value.toLowerCase();
				
				var tdId = "td"+hidId.substring(3);
				var anchtdId = "anchor"+hidId.substring(3);
				
				if(searchText != ""){
					if(strstr(txt,searchText)){
						document.getElementById(tdId).className = "changeTDBgColor";
						if(f1==1){
							document.getElementById("getMyLink").href="#"+anchtdId;	
							document.getElementById("getMyLink").click();
						}
						f1++;
					}
				}
			}
		//}
	}
	
	function sub_facility()
	{
	   document.facFrm.submit();
	}
	
	function chkAll(obj){
		var cbkObj = null;
		cbkObj =  document.getElementsByName('cbkPrev');
		if(obj.checked == true){
			for(var a = 0; a < cbkObj.length; a++){
				cbkObj.item(a).checked = true;
			}
		}
		else if(obj.checked == false){
			for(var a = 0; a < cbkObj.length; a++){
				cbkObj.item(a).checked = false;
			}
		}
	}
	function restore_delete_all(op){
		var cbkObj = null;
		cbkObj =  document.getElementsByName('cbkPrev');
		var arrId = new Array();
		var arrIdFA = new Array();
		if(op == "1"){
			for(var a = 0; a < cbkObj.length; a++){
				if(cbkObj.item(a).checked == true){
					var arrValue = cbkObj.item(a).value.split("-");
					arrId.push(arrValue[1]);
				}
			}
			if(arrId.length > 0){
				document.getElementById("hidAction").value = "restore";
				document.getElementById("hidShcId").value = arrId.join(",");
				top.fancyConfirm("Sure! you want to restore selected appointment(s)?","", "document.del_frm.submit()");
			}
			else{
				top.fAlert("Please select appointment for restoring!");
			}
		}else if(op=="2"){
			for(var a = 0; a < cbkObj.length; a++){
				if(cbkObj.item(a).checked == true){
				
					var arrValue = cbkObj.item(a).value.split("-");
					if(arrValue[1]>0)//if we have sch_id that mean its from scheduler otherwise its from first available
					arrId.push(arrValue[1]);
					else
					arrIdFA.push(arrValue[0]);
				}
			}
			if(arrId.length > 0 || arrIdFA.length > 0){
				document.getElementById("hidAction").value = "del";
				document.getElementById("to_do_id").value = arrId.join(",");
				document.getElementById("to_do_fa_id").value = arrIdFA.join(",");
				top.fancyConfirm("Sure! you want to delete selected appointment(s)?","", "document.del_frm.submit()");
			}
			else{
				top.fAlert("Please select appointment for deleteing!");
			}
		}
	}
	function click_page(pg_no)
	{
		document.getElementById('cur_page_no').value = pg_no;
		sub_facility();
	}
	
	function switchWin(act)
	{
		if(document.getElementById('pt_id_hidden').value)
		{
			$("#addFAContainer").modal(act);
		}
		else
		{
			top.fAlert('Please select patient first');	
		}
	}
	
	function getWks(load_wk)
	{
		if(load_wk){
		$.ajax({
			url: "to_do_first_avai.php?load_wk="+load_wk,
			success: function(resp){
				if(resp){
					//document.getElementById('week').innerHTML=resp;	
					$("#week").html(resp);
				}			
			}
		});
	}
	}
	function getpid(patid,patname){		
		if(patid!=""){
			//opener.refresh_patient_infopage(patid,'','');
			document.getElementById('pt_id_hidden').value=patid;
			document.getElementById('pt_name_hidden').value=patname;
			document.getElementById('pt_id').value=patname+' '+patid;
		}	
	}

		
	$(document).ready(function() {
		$("#success-alert").fadeTo(2000, 500).slideUp(500, function() {
		  $("#success-alert").slideUp(500);
		});
	});
</script>

<style>
	.changeTDBgColor{
		background: #FE9C53;
		color: #000000;
	}
	.pg_format_class{padding:0px 4px;background-color:#fff;color:#000;margin-right:10px;}
	.pg_sel_class{padding:0px 4px;margin-right:10px;background-color:#333;color:#fff;}
	.pg_sel_class:hover{background-color:#fff;color:#000;}
	.grythead {line-height:30px}


.highlight
{
	background:#009500;
	color:#000;	
}
.alert {
    padding: 10px!important;
    margin-bottom: 0px!important; 
}
</style>
</head>

<body> 
<div class="container-fluid tolist">   
    
    <div class="whtbox">
 <div class="pd10"> 
       <div class="row"> <div class="col-sm-3">  <ul class="nav nav-tabs" style="border-bottom:none">
        <li id="first_available" class="active lead"><a href="to_do_first_avai.php">First Available</a></li>
        <li id="re_schedule" class="lead"><a href="to_do.php">Re-Schedule List</a></li>
    </ul></div>
    <div class="col-sm-9">
    <div class="row ">
                    <div class="col-sm-3 text-left">
                    <?php
					if($_SESSION[msg]=='succ'){echo"<div class='alert alert-info' id='success-alert'>Record saved successfully.</div>";}
					elseif($_SESSION[deleted]=='success'){echo"<div class='alert alert-info' id='success-alert'>Records deleted successfully.</div>";}
					elseif(trim($_SESSION['restore_act'])){
						$total=$_REQUEST['not_restored']+$_REQUEST['restored'];
						echo"<div class='alert alert-info' id='success-alert'>$_REQUEST[restored]/$total Records restored successfully.</div>";
					}
					
					unset($_SESSION['msg'], $_SESSION['deleted'], $_SESSION['restore_act']);
					?>
                    </div>
                    <div class="col-sm-9 text-right">
                        <form name="facFrm" method="post" action="">
                         <input type="hidden" name="tab_name" id="tab_name" value="re_schedule">
                         <input type="hidden" id="cur_page_no" name="cur_page_no" value="" />
                        <div class="row form-inline">
                            <div class="col-sm-5 text-right">
                                <div class="form-group multiselect">
                                <?php
                                    $option="<option value=''>Facility All</option>";
                                    foreach($arrFacility as $fac_id => $facilityname){
                                        $select='';
                                        if($_REQUEST['facility_id']==$fac_id){
                                            $select="selected='selected'";
                                        }
                                        $option.="<option ".$select." value='".$fac_id."'>".$facilityname."</option>";
                                    }
                                ?>
                                <select onChange="sub_facility()" name="facility_id" class="form-control minimal" style="width: 150px">
                                <?php echo $option;?></select>
                                </div>
                                <div class="form-group multiselect">
                                <?php
                                    $option="<option value=''>Provider All</option>";
                                    foreach($arr_users as $user_id => $username){
                                        $select='';
                                        if($_REQUEST['prov_id']==$user_id){
                                            $select="selected='selected'";
                                        }
                                        $option.="<option ".$select." value=".$user_id.">".$username."</option>";
                                    }
                                ?>
                                <select onChange="sub_facility()" name="prov_id" class="form-control minimal" style="width: 150px"><?php echo $option;?></select>
                                </div>
                            </div>
                            <div class="col-sm-2 text-right">
                                <input type="text" id="searchText" name="searchText" value="<?php echo $_REQUEST[searchText];?>" title="Patient ID OR Patient Name" placeholder="Pt. ID or Pt. Name" class="form-control" style="width: 100%">
                            </div>	
                            <div class="col-sm-2 text-right">
								<div class="input-group">
									<input type="text" name="from_date" placeholder="Added Date From" style="font-size: 12px;" id="from_date" value="<?php echo $_REQUEST['from_date'];?>" class="form-control date-pick" autocomplete="off">
									<div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
								</div>
							</div>	
                            <div class="col-sm-2 text-right">
                           		<div class="input-group">
									<input type="text" name="to_date" placeholder="Added Date To" style="font-size: 12px;" id="to_date" value="<?php echo $_REQUEST['to_date'];?>" class="form-control date-pick" autocomplete="off">
									<div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
								</div>
							</div>
                            <div class="col-sm-1 text-left">
                                <button tabindex="1" type="button" value="Search" onClick="javascript:searchForTextNew();" class="stnsrch">Search</button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
    
    </div></div>
    
    
    <a href="#" id="getMyLink"></a>		
       <div id="first_appt" class="row">
            <div class="col-sm-12">
                
                <div class="showrst"><div class="row  ">
                    <div class="col-sm-6 text-left">  
                        Showing Appointments <?php $limit_appt = $cur_page_no*$page_limit; if($limit_appt>$total_appts){$limit_appt = $total_appts;} echo ($limit_follow+1).' - '.($limit_appt); ?> of <?php echo $total_appts; ?>
                    </div>  
                    <div class="col-sm-6 text-right">
                        <?php 
                        $next = $cur_page_no + 1;
                        $previous = $cur_page_no - 1;
                        $mid_value = 10;
                        $start_point = $cur_page_no- floor($mid_value/2);
                        $end_point = $cur_page_no+ floor($mid_value/2);
                        if($start_point <= 0)
                        {
                            $end_point += abs($start_point); 
                            $start_point = 1;												
                        }
    
                        if($end_point > $total_pages)
                        {
                            $start_point -= ($end_point-$total_pages);
                            if($start_point <= 0){$start_point = 1;}
                            $end_point = $total_pages;
                        }
                        if($previous > 0)
                        {
                            echo '<a class="pg_format_class" style="cursor:pointer;" onclick="click_page('.$previous.')"> Previous </a>';														
                        }											
                        for($p=$start_point;$p<=$end_point;$p++)
                        {
                            $sel_pg = '';
                            $pg_class = 'pg_format_class';
                            if($p==$cur_page_no){$pg_class = 'pg_sel_class';}
                            echo '<a class="'.$pg_class.'" style="cursor:pointer;" onclick="click_page('.$p.')">'.$p.'</a>';												
                        }
                        if($total_pages > $end_point+1)
                        {
                            echo ' ... ';
                        }
                        if($total_pages > $end_point)
                        {
                            echo '<a class="pg_format_class" style="cursor:pointer;" onclick="click_page('.$total_pages.')">'.$total_pages.'</a>';												
                        }
                        if($next <= $total_pages)
                        {
                            echo '<a class="pg_format_class" style="cursor:pointer;" onclick="click_page('.$next.')"> Next </a>';													
                        }
                        ?>
                    </div>
                </div>   </div>             	
                <div style="height:580px;overflow-x:hidden;overflow-y:auto;">
                    <div class="row">
                        <div class="col-sm-12"> 
                            <table  class="table table-striped table-bordered table-hover to_do adminnw">
                                <thead>
                                    <tr>
                                        <th>All</th>
                                        <th>
                                        	<div class="checkbox checkbox-inline">
                                                <input type="checkbox" id="cbkChkAll" name="cbkChkAll" onClick="chkAll(this)">
                                                <label for="cbkChkAll"></label>
                                            </div>
                                        </th>
                                        <th>Patient Name</th>
                                        <th>Phone#</th>
                                        <th>Provider</th>
                                        <th>Facility</th>
                                        <th>Procedure</th>
                                        <th>Date - Added to List</th>
                                        <th>Date of Appt</th>
                                        <th>Desired Time</th>
                                        <th>Operator</th>
                                    </tr>
                               </thead>
                        <?php
                        $result_obj = imw_query($reqQry)or die(imw_error());
                        $printD=false;
                        $printData='<table width="100%" border="0" cellspacing="0" cellpadding="0">';
                        if(imw_num_rows($result_obj) > 0){
                            $k=$limit_follow+1;					$printD=true;
                            while ($result_fa_row = imw_fetch_assoc($result_obj)) {
                            
                            $patient_id= $result_fa_row['sa_patient_id'];
                            $pat_det = patient_data($result_fa_row['sa_patient_id']);
                            $sch_id = $result_fa_row['sch_id'];
                            $del_id = $result_fa_row['id'];
                            if($result_fa_row['sch_id'])$sch_det = sch_data($result_fa_row['sch_id']);
                            else
                            {
                                //get operator detail
                                $tt_provider=" SELECT fname,mname,lname,user_type FROM `users` WHERE id=$result_fa_row[operator_id]";							
                                $sqltt_provider=@imw_query($tt_provider);	
                                $vrs_tcurr=imw_fetch_array($sqltt_provider);
                                $operator=substr($vrs_tcurr['fname'],0,1)."".substr($vrs_tcurr['lname'],0,1);
                            }
                            if($sch_det[2]!='')$operator=$sch_det[2];
                            
                            $to_do_list_time = strtotime($result_fa_row['status_time']);
                            $to_do_list_time = date("h:i A",$to_do_list_time);
                            $app_made_on_time = strtotime($result_fa_row['sa_app_starttime']);
                            $app_made_on_time = date("h:i A",$app_made_on_time);
                            
                            $to_do_list_date=($result_fa_row['entered_status_date']); 
                            $status_id = $sch_det[5];
                            
                            $app_made_on_date =$result_fa_row['sa_app_start_date'];
                            
                            $key=$year=$month=$week='';
                            $year=($sch_id)?desireTime($sch_id,'sch_id','other','sel_year'):desireTime($result_fa_row['id'],'id','other','sel_year');
                            $month=($sch_id)?desireTime($sch_id,'sch_id','other','sel_month'):desireTime($result_fa_row['id'],'id','other','sel_month');
                            $week=($sch_id)?desireTime($sch_id,'sch_id','other','sel_week'):desireTime($result_fa_row['id'],'id','other','sel_week');
                            $month=($month<10)?'0'.$month:$month;
                            
                            $key=$year.'-'.$month.'-'.$week;
                            $available=($highlight=='Available')?' highlight':'';
                    ?>
                        <tr id="td<?php echo $result_fa_row['id'];?>" <?php echo (!$result_fa_row["sch_id"])?'style="background-color:#EBEBEB"':'';?>>
                            <td><?php echo $k."."; ?></td>
                            <td>
                            	<div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="cbk<?php echo $del_id; ?>" value="<?php echo $del_id."-".$sch_id; ?>" name="cbkPrev">
                                    <label for="cbk<?php echo $del_id; ?>"></label>
                                </div>
                            </td>
                            <td><?php
                            echo("<a name='anchor".$result_fa_row['id']."' id='anchor".$result_fa_row['id']."'></a>");
                            if($result_fa_row["sch_id"])
                            {
                             echo "<a href='javascript:edit_schedule(\"$sch_id\",\"$status_id\",\"$to_do_list_date\",\"$sch_det[3]\",\"$sch_det[5]\",\"$patient_id\");' class='text_11'>". $pat_det[0]." - ".$result_fa_row['sa_patient_id']."</a>"; 
                             }
                             else
                             {
                                echo $pat_det[0]." - ".$result_fa_row['sa_patient_id'];
                             }
                             ?>
                                <input type="hidden" id="hid<?php echo $result_fa_row['id'];?>" name="searchArea[]" value="<?php echo str_replace("&nbsp;"," ",$pat_det[0]);?>">
                            </td>
                            <td><?php echo core_phone_format($pat_det[8]);?></td>
                            <td>
                            	<?php 
                            		$sql_query_provider = "SELECT GROUP_CONCAT(CONCAT(u.fname, ', ', u.lname, ' ', u.mname) SEPARATOR ' - ') AS provider FROM users u WHERE id IN (".$result_fa_row['provider_id'].")";
                            		$result = imw_fetch_assoc(imw_query($sql_query_provider));
                            		echo $result['provider'];
                        		?>
                			</td>
                            <td>
                            	<?php 
                            		$sql_query_facility = "SELECT GROUP_CONCAT(f.name SEPARATOR ' - ') AS fac_name FROM facility f WHERE id IN (".$result_fa_row['sa_facility_id'].")";
                            		$result = imw_fetch_assoc(imw_query($sql_query_facility));
                            		echo $result['fac_name'];
                        		?>
                    		</td>
                            <td><?php echo $result_fa_row["proc"]; ?></td>
                            <td><?php echo $to_do_list_date." ".$to_do_list_time;?></td>
                            <td>
                            <?php 
                            if($result_fa_row["sch_id"])
                            {
                                echo $app_made_on_date." ".$app_made_on_time; 
                            }
                            else echo '--';	
                            ?>
                            
                            </td>
                            <td class="<?php echo $available;?>" title="<?php echo $hover;?>"> <?php
                            echo $dStr=($sch_id)?desireTime($sch_id,'sch_id','detail'):desireTime($result_fa_row['id'],'id','detail');
                            ?></td>
                            <td><?php echo $operator;?> </td>
                        </tr>
                        <?php
                            $printData.='
                                    <tr>
                                        <td height="30" width="30" valign="top" >&nbsp;'.
                                            $k.'.
                                        </td>
                                        <td height="30" width="175" valign="top" align="left" >'.
                                            $result_fa_row["sa_patient_name"].' - '.$result_fa_row["sa_patient_id"].'
                                        </td>
                                        <td valign="top" width="122" align="center" nowrap class="text_11">&nbsp;'.core_phone_format($pat_det[8]).'</td>
                                        <td valign="top" width="155" nowrap="nowrap" class="text_11">'.$result_fa_row["provider"].'</td>
                                        <td valign="top" width="85" nowrap="nowrap" class="text_11">'.$result_fa_row['fac_name'].'</td>
                                        <td valign="top" width="90" nowrap="nowrap" class="text_11">'.$result_fa_row["proc"].'</td>
                                        <td valign="top" width="100" align="center" nowrap="nowrap" class="text_11">'.$to_do_list_date." ".$to_do_list_time.'</td>
                                        <td valign="top" width="100" align="center"  nowrap="nowrap" class="text_11">';
                                        if($result_fa_row["sch_id"])
                                        {
                                            $printData.= $app_made_on_date." ".$app_made_on_time; 
                                        }
                                        else $printData.= '-- ';	
                                        
                                        $printData.='</td><td valign="top" width="100" align="center"  nowrap="nowrap" class="text_11 '.$available.'">';
                                        $printData.= $dStr.' ';
                                        
                                    
                                        $printData.='</td>
                                        <td valign="top" width="80" align="center" class="text_11">'.$operator.'</td>
                                    </tr>';					
                        
                                $k++;}
                            }
                            else
                            {
                                echo '<tr><td colspan="11" style="color:red;padding:10px; height:30px; text-align:center;">No record found</td></tr>';
                            }
                            $printData.='</table></page>';
        
                        ?>
                            </table>
                        </div>
                    </div>
                </div>	
            </div>
        </div></div>
        
    </div>
    <div class="row">
        <div class="col-sm-12 text-right">
            <?php
            if($num_pro1 > 0){
            ?>
            <button id="btRestore" class="btn btn-success" name="btRestore" value="Restore" onClick="restore_delete_all('1');">Restore</button>
            <button id="btPrint" class="btn btn-default" name="btPrint" value="Print" onClick="common_print_fun('<?php echo 'print_pdf_file_fa'.$_SESSION['authId']; ?>');"><span class="glyphicon glyphicon-print"></span> Print</button>
            <button id="btDelete" class="btn btn-danger" name="but_close" value="Delete" onClick="restore_delete_all('2');">Delete</button>
            <?php 
            }
            ?>
            <button id="add_butt" class="btn btn-success" name="but_add" value="Add New" onClick="switchWin('show');">Add New</button>
            <button id="save_butt" class="btn btn-danger" name="but_close" value="Close" onClick="close_me();">Close</button>
        </div>
    </div>
</div>
<?php
$ArrCreatedBy = getUserDetails($_SESSION['authId']);
$createdBy = strtoupper(substr($ArrCreatedBy['fname'],0,1).substr($ArrCreatedBy['lname'],0,1));
$createdOn = date('m-d-Y H:i A');

$selFacility = 'All';
if($_REQUEST['facility_id']!=''){
	$qryFac="select  name from facility WHERE id = '".$_REQUEST['facility_id']."'";
	$rsFac=imw_query($qryFac);
	$resFac = imw_fetch_row($rsFac);
	$selFacility = $resFac[0];
}

$strCSS= '<style>
				.text_b_w{
					font-size:11px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#BCD5E1;
					border-style:solid;
					border-color:#FFFFFF;
					border-width: 1px; 
				}
				.tb_heading{
					font-size:11px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#FE8944;
				}
				.text_b{
					font-size:11px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#FFFFFF;
					background-color:#4684AB;
				}
				.text_10b{
					font-size:11px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					background-color:#FFFFFF;
				}
				.text_b_date{
					font-size:11px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#F3F3F3;
				}				
				.text{
					font-size:11px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FFFFFF;
				}
				.text_10{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					background:#FFFFFF;
				}
				.report_head_text{
					font-size:11px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FFFFFF;
					color:#4684ab;
					font-weight:bold;
				}
				.highlight
				{
					background:#009500;
					color:#000;	
				}
			</style>
			
			<page backtop="12mm" backbottom="7mm">';

			$pdfFooter.='<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>';
			
			$pdfHeader.='<page_header>
			<table style="width:100%;" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="5"  class="text_b_w" align="left">&nbsp;<b>First Available List &nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Facilities :&nbsp;</b>'.$selFacility.'</td>
					<td colspan="5" nowrap="nowrap" class="text_b_w" style="text-align:right;">Created by '.$createdBy.' on '.$createdOn.'</td>
				</tr>
				<tr>
					<td width="25" valign="top" nowrap class="text_b_w">S.No.</td>
					<td width="173" valign="top" nowrap class="text_b_w">Patient Name</td>
					<td width="120" align="center" valign="top" nowrap class="text_b_w">Phone#</td>
					<td width="150" valign="top" nowrap class="text_b_w">Provider</td>
					<td width="80" valign="top" nowrap class="text_b_w">Facility</td>
					<td width="90" valign="top" nowrap class="text_b_w">Procedure</td>
					<td width="100" align="center" valign="top" class="text_b_w">Date - Added to List</td>
					<td width="100" align="center" valign="top" class="text_b_w">Date of Appt</td>
					<td width="100" align="center" valign="top" class="text_b_w">Desired Time</td>
					<td width="80" align="center" valign="top" class="text_b_w">Operator</td>
				</tr>
			</table>	
			</page_header>';			

if($printD==true){
	$printHTML= $strCSS.$pdfFooter.$pdfHeader.$printData;
	$file_location = write_html($printHTML);
	//$html_file_name = 'print_pdf_file_fa'.$_SESSION['authId'];
	//file_put_contents('../reports/new_html2pdf/'.$html_file_name.'.html',$printHTML);
}

?>
<script type="text/javascript">
	
	function common_print_fun(html_name){		
		//window.open('../reports/new_html2pdf/createPdf.php?op=l&file_name='+html_name,'print_pdf','menubar=0,resizable=yes');
		top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
		html_to_pdf('<?php echo $file_location; ?>','l');//p=portrait,l=landscape
	} 
	
	function validateFA(obj)
	{
		if(obj.pt_id.value=='')
		{
			top.fAlert('Please select patient.');
			return false;	
		}	
	}
	$(function(){		
		$('.date-pick').datetimepicker({
			timepicker:false,
			format:'m-d-Y',
			formatDate:'Y-m-d'
		});
	});
</script>

 <!-- add first available -->
<!--modal wrapper class is being used to control modal design-->
<div class="common_modal_wrapper">
 <!-- Modal -->
<div id="addFAContainer" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add First Available</h4>
            </div>
            <div class="modal-body">
                <form name="save_first_available" method="post" action="" onSubmit="return validateFA(this)">
                 <input type="hidden" name="saveFAForm" id="saveFAForm" value="yes">
                 <div class="row">
                 	<div class="col-sm-12">
                    	<div class="form-group">
                        	<label>Patient</label>
                            <?php if($_SESSION['pid'])$patArr=patient_name($_SESSION['pid']); ?>
                       		<input type="text" readonly name="pt_id" value="<?php echo (strlen(trim($patArr[2]))>0)?$patArr[2].'-'.$_SESSION['pid']:''; ?>" id="pt_id" class="form-control">
                            <input type="hidden" name="pt_id_hidden" id="pt_id_hidden" value="<?php echo $_SESSION['pid'];?>">
                        	<input type="hidden" name="pt_name_hidden" id="pt_name_hidden" value="<?php echo $patArr[2];?>">
                        </div>
                    </div>
                 </div>
                 <div class="row">
                 	<div class="col-sm-6">
                    	<div class="form-group">
                        	<label>Provider</label>
                            <select id="provider_id" name="provider_id[]" class="form-control minimal selectpicker" multiple>
                                <?php echo $obj_scheduler->load_providers("OPTIONS", $sel_prov);?>
                       		</select>
                        </div>
                    </div>
                 	<div class="col-sm-6">
                    	<div class="form-group">
                        	<label>Facility</label>
                            <select id="facilities" name="facilities[]" class="form-control minimal selectpicker" multiple>
                                <?php echo $obj_scheduler->load_facilities($_SESSION["authId"], "OPTIONS", $sel_fac);?>
                       		</select>
                        </div>
                    </div>
                 </div>    
                 <div class="row">
                 	<div class="col-sm-12">
                    	<div class="form-group">
                        	<label>Procedure</label>
                            <select name="sel_proc_id" id="sel_proc_id" onChange="show_test(this.value,'<?php echo $_REQUEST["sch_id"];?>');" class="form-control minimal" >
                                <option value="">-Procedure-</option>
                                <?php	
                                $selected_proc = "";
                                if($arr_appt !== false){
                                    $selected_proc = $arr_appt[0]["procedureid"];
                                }else{
                                    $selected_proc = $_REQUEST["force_proc"];
                                }
                                $default_procedure_id = $obj_scheduler->doctor_proc_to_default_proc($selected_proc);
                                list($list_of_procs, $arr_proc_names) = $obj_scheduler->load_procedures($default_procedure_id);
                                echo $list_of_procs;
                                ?>
                            </select>
                        </div>
                    </div>
                 </div>
                 <div class="row">
                 	<div class="col-sm-6">
                    	<div class="form-group">
                        	<label>Month</label>
                            <?php 
							//get present appointment time
							list($date,$time)= explode(' ',date('Y-m-d H:i:s'));
							//get date parameter
							list($y,$m,$d)= explode('-',$date);
							$w=date('W',mktime(0,0,0,$m,$d,$y)); ?>
                            <select name="month" id="month" class="form-control minimal" onChange="getWks(this.value)">
                                <option value="">Month</option>
                                <?php
                                $curr_mon=$month_g." ".$c_yy;
                                $load_date=$c_yy."-".$c_mm."-01";
                                $curn_year=date('Y');
                                $current_month=(date('m')+1);
                                $loop_end=($current_month+11);//(12+(12-($current_month)));
                                
                                for($mon=$current_month;$mon<=$loop_end;$mon++){
                                    $month_v=date("F", mktime(0, 0, 0, $mon, 0, $curn_year));
                                    $c_mon=($mon);
                                    $c_mon=($c_mon-1);
                                    if($c_mon>12){$c_mon=($c_mon-12);}
                                    if($c_mon>12){$c_mon=($c_mon-12);}
                                    
                                    //if($curn_year=='2016'){echo $date_vla;echo "<br>";die();}
                                    if(strlen($c_mon)==1){$c_mon="0".$c_mon;}
                                    $date_vla=$curn_year."-".$c_mon;
                                    $sel_load_date="";
                                    if($c_mon==date('m')){
                                        $sel_load_date=" SELECTED ";
                                    }
                                    
                                    $select_month.="<option ".$sel_load_date." value=\"".$date_vla."\">".$month_v." ".$curn_year."</option>";
                                    if(strtolower($month_v)=="december"){
                                        $curn_year++;
                                    }
                                }
                                echo $select_month;
                                ?>
                            </select>
                        </div>
                    </div>
                 	<div class="col-sm-6">
                    	<div class="form-group">
                        	<label>Week</label>
							<?php $weekArr=getDatesForWK($m,$y);?>
                            <select name="week" id="week" class="form-control minimal">
                                <?php
                                for($wk=1;$wk<=weeks_in_month($m, $y);$wk++)
                                {
                                    $str=$weekArr[$wk];
                                    $str=trim($str);
                                    $sbstr=substr($str,0,strlen($weekArr[$wk])-2);
                                    $month=date('M', mktime(0,0,0,$m,01,$y));
                                    
                                    echo'<option value="'.$wk.'"';
                                    echo($wk==$w)?' selected':'';
                                    echo'>Week '.$wk.' ['.$sbstr.' '.$month.']</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                 </div>    
                 <div class="row">
                 	<div class="col-sm-6">
                    	<div class="form-group">
                        	<label>Time</label>
                            <select name="time" id="time" class="form-control minimal">
                                <option value="AM">AM</option>
                                <option value="PM">PM</option>
                            </select>
                        </div>
                    </div>
                 </div>    
                </form>
            </div>
            <div class="modal-footer" style="overflow:visible">
            	<button type="submit" name="saveFA" id="saveFA" value="Save" class="btn btn-success" onClick="document.save_first_available.submit();">Save</button>
            	<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>
<!--modal wrapper class end here -->
        
<form name="del_frm" action="del_to_do.php?todo_avai=1" method="post" >
	<input type="hidden" id="to_do_id" name="to_do_id" value="" />
	<input type="hidden" id="to_do_fa_id" name="to_do_fa_id" value="" />
    <input type="hidden" id="hidAction" name="hidAction" value="" />
    <input type="hidden" id="hidShcId" name="hidShcId" value="" />
</form>

	
</body>
</html>