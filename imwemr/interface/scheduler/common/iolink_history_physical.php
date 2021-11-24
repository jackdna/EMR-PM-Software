<?php

//require_once("../../../config/globals.php");

$css="<style>

table{ width:100%;border-spacing:0;}

td{
	font-size:14px;	
	font-weight:100;
	background-color:#FFFFFF;
	color:#000000;
	text-align:left;
	vertical-align:top;
	padding:0;
	border-spacing:0;	
	margin:0;
	word-wrap:break-word;
}

.pagebreak {page-break-before:always} 

.text_b_w{
	font-size:14px;	
	font-weight:bold;
	background-color:white;
	color:#000000;
}
.paddingLeft{
	padding-left:5;
}
.paddingTop{
	padding-top:5;
}
.tb_subheading{
	font-size:14px;	
	font-weight:bold;
	color:#000000;
	background-color:#f3f3f3;
}
.tb_heading{
	font-size:14px;	
	font-weight:bold;
	color:#000;
	background-color:#C0C0C0;
	margin-top:10;
	padding:3px 0px 3px 0px;
	vertical-align:middle;
	width:100%;
}
.tb_headingHeader{
	font-size:14px;	
	font-weight:bold;
	color:#FFFFFF;
	background-color:#4684ab;
}
.text_lable{
	font-size:14px;	
	background-color:#FFFFFF;
	font-weight:bold;
}
.text_value{
	font-size:14px;	
	font-weight:100;
	background-color:#FFFFFF;
}
.text_blue{
	font-size:14px;	
	color:#0000CC;
	font-weight:bold;
}
.text_green{
	font-size:14px;	
	color:#006600;
	font-weight:bold;
}

.imgCon{width:325;height:auto;}
.imgdraw{width:325;height:auto;}

.lbl{width:10%;font-weight:bold; } 
.sum{width:45%;} 

.test td{border:1px solid red;}

.conlen td { font-size:14px; }

#crgp td{ font-size:14px;}

.headtilt, .grid{ width:43%;min-height:150px;text-align:left; }
.grid table{width:100%;height:95%;border-spacing:0;border-collapse: collapse;margin-top:3px; }
.grid table td{border-right:4px solid black;border-bottom:4px solid black; width:33%; text-align:center; height:20px;}
.border{
	border:1px solid #C0C0C0;
}
.bdrbtm{
	border-bottom:1px solid #C0C0C0;
	height:13px;	
	vertical-align:top;
	padding-top:2px;
	padding-left:3px;
}
.bdrtop{
	border-top:1px solid #C0C0C0;
	height:15px;
	vertical-align:top;	
}
.pdl5{
	padding-left:10px;
		
}
.bdrright{
	border-right:1px solid #C0C0C0;
}
</style>";


require_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/print_functions_class.php');
$objParser = new CmnFunc;

//$pid=4342;//$_REQUEST["pid"];
$arrReq=array("Medical History");
$exclusionArr=array("ocular");
$schedule_id = $_REQUEST['sch_id'];
$dos=$sa_app_start_date=$pid="";
$qry_sch="SELECT sa_app_start_date,sa_patient_id FROM schedule_appointments WHERE id='".$schedule_id."' LIMIT 0,1";
$res_sch=imw_query($qry_sch);
if(imw_num_rows($res_sch)>0){
	$row_sch=imw_fetch_assoc($res_sch);
	$sa_app_start_date=$row_sch['sa_app_start_date'];
	$pid=$row_sch['sa_patient_id'];
}
if($sa_app_start_date){
	$qry_select="SELECT date_of_service FROM chart_master_table WHERE patient_id='".$pid."' AND date_of_service<='".$sa_app_start_date."' order by date_of_service desc limit 0,1";
	
	$res_select=imw_query($qry_select);
	if(imw_num_rows($res_select)>0){
		$row_select=imw_fetch_assoc($res_select);
		$dos=$row_select['date_of_service'];	
	}
}
$patientDetails=array();
$qry ="select id,concat(lname,', ',fname,' ',mname) as name,DOB,sex,title,language,race,ethnicity,street,street2,city,state,postal_code,phone_home,TIMESTAMPDIFF(YEAR, DOB, CURDATE()) AS age,p_imagename from patient_data where id = '".$pid."'";
$res = imw_query($qry);
$patientDetails_get=imw_fetch_assoc($res);
if($patientDetails_get["DOB"]){
	$patientDetails_get["DOB"]=date("m-d-Y",strtotime($patientDetails_get["DOB"]));
}
$p_imagename=$patientDetails_get['p_imagename'];
$patientImage = "";
$p_imagename = $patientDetails_get['p_imagename'];;
if(!empty($p_imagename)){
	//$dirPath = dirname(__FILE__).'/../../main/uploaddir'.$p_imagename;				
	$dirPath = data_path(1).$p_imagename;
	$dir_real_path = realpath($dirPath);
	$img_name = substr($p_imagename,strrpos($p_imagename,'/')+1);
	if(file_exists($dir_real_path)){
		$patient_img['patient'] = $img_name;
		$fileSize = getimagesize($dir_real_path);
		if($fileSize[0]>80 || $fileSize[1]>90){						
			$patientImage = "<img src=\"".$dir_real_path."\" width=\"80\" height=\"80\" alt=\"patient Image\" />";
		}else{
			$patientImage = "<img style=\"cursor:pointer\" src=\"".$dir_real_path."\" alt=\"patient Image\"/>";
		}
		$patientDetails_get['img']=$patientImage;
	}
}

$patientDetails[]=$patientDetails_get;
$date_of_service="";
if($dos){
	$_REQUEST["chart_nopro"][]="Chart Notes";
	$date_of_service=date("m-d-Y",strtotime($dos));
    $chartDetails['date_of_service']=$sa_app_start_date;
}


ob_start();
print_hdrTopbar1($patientDetails,"",$_REQUEST["chart_nopro"],$_REQUEST['chart_exclusion']);
print_mainHeader1($patientDetails,$chartDetails,$_REQUEST["chart_nopro"],$_REQUEST['chart_exclusion']);

$arrReq[]="history_physical";

$objParser->print_all_history_physical($pid,$arrReq);
$htmloutput = ob_get_contents();
$data_dir = substr(data_path(), 0, -1);
$patientDirHistoryPhysical = "/PatientId_".$pid;
$patientDir = $patientDirHistoryPhysical;
//$iolinkDirPath = realpath(dirname(__FILE__).'/../../../addons/iOLink');	
$iolinkDirPath = $data_dir.'/iOLink';	
if(!is_dir($iolinkDirPath.$patientDir)){mkdir($iolinkDirPath.$patientDir);}

$history_physical_html_file_name ='History_Physical'.$_SESSION['authId'];
$putData = file_put_contents($fp,$patientPrintData);

$copyPdfFilePath = $iolinkDirPath.$patientDir."/History_Physical.pdf";
if(file_exists($copyPdfFilePath)){unlink($copyPdfFilePath);}

$fp = $data_dir.'/iOLink/'.$history_physical_html_file_name.'.html';
//$putData = file_put_contents($fp,"<page backtop='5'>".$css.$htmloutput."</page>");
//$htmloutput


$putData = file_put_contents($fp,"<page backtop='5'>".$css.html_entity_decode($htmloutput,ENT_QUOTES|ENT_XHTML|ENT_HTML5,'ISO-8859-1')."</page>");
ob_clean();
$htmlFlName = $history_physical_html_file_name;

$pdfFileName = 'History_Physical.pdf';
$pdfFilePath = urldecode($iolinkDirPath.$patientDir.'/'.$pdfFileName);
$arrProtocol = (explode("/",$_SERVER['SERVER_PROTOCOL']));
$arrPathPart = pathinfo($_SERVER['PHP_SELF']);
$arrPathPart = explode("/",($arrPathPart['dirname']));

//===============================Curl Request for hit the URL=========================================//
//$myHTTPAddress = $protocol.$myInternalIP.'/'.$web_RootDirectoryName.'/interface/common/new_html2pdf/createPdf.php';
$dir = explode('/',$_SERVER['HTTP_REFERER']);
$httpPro = $dir[0];
$httpHost = $dir[2];
$httpfolder = $dir[3];
$ip = $_SERVER['REMOTE_ADDR'];
//$myHTTPAddress = $httpPro.'//'.$myInternalIP.'/'.$web_RootDirectoryName.'/interface/common/new_html2pdf/createPdf.php';
$myHTTPAddress = $httpPro.'//'.$myInternalIP.'/'.$web_RootDirectoryName.'/library/html_to_pdf/iolinkMakePdf.php';
$data1 = "";
$curNew = curl_init();
//$urlPdfFile = $myHTTPAddress."?op=p&onePage=false&saveOption=F&name=".$history_physical_html_file_name."&pdf_name=".$history_physical_html_file_name;
$urlPdfFile = $myHTTPAddress."?copyPathIolink=$pdfFilePath&pdf_name=$pdfFilePath&name=$htmlFlName";

curl_setopt($curNew, CURLOPT_URL,$urlPdfFile);
curl_setopt($curNew, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curNew, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
$data1 = curl_exec($curNew);
curl_close($curNew);

//=====================================================================================================//


//Print Header Top bar --------------
function print_hdrTopbar1($patientDetails,$date_of_service="",$arrReq=array(),$exclusion){

$patientName=$patientDetails[0]["name"];
$age=trim($patientDetails[0]["age"]);
$strAge = !empty($age) ? "&nbsp;($age)&nbsp;" : "" ;
$date_of_birth=!empty($patientDetails[0]["DOB"])&&$patientDetails[0]["DOB"]!="00-00-0000" ? $patientDetails[0]["DOB"] : "";

?>

<page_header>
<table cellspacing="0">
<tr>
<td style="width:40%" class="tb_headingHeader"><?php if(!empty($patientName)){ print $patientDetails[0]['title'].' '.$patientName."-".$patientDetails[0]['id']; }?> </td>
<td style="width:30%" class="tb_headingHeader"><?php print $patientDetails[0]['sex'];if(@!in_array("HIPPA",$arrReq)){ print($strAge.$date_of_birth); }?>&nbsp; </td>
<td style="width:30%; text-align:right" class="tb_headingHeader"><?php if(!empty($date_of_service) && (in_array("Chart Notes",$arrReq)) && !in_array("dos_facility",$exclusion)){ print 'Date of Service:&nbsp;'.$date_of_service."";} else '&nbsp;'; ?> </td>
</tr>
</table>
</page_header>

<?php 

} 


//Print main Header ------------------

function print_mainHeader1($patientDetails, $chartDetails=array(), $fAct=array(),$exclusion=array()){
	
	$patientName=$patientDetails[0]["name"];
	$age=trim($patientDetails[0]["age"]);
	$strAge = !empty($age) ? "&nbsp;($age)&nbsp;" : "" ;
	$date_of_birth=!empty($patientDetails[0]["DOB"])&&$patientDetails[0]["DOB"]!="00-00-0000" ? $patientDetails[0]["DOB"] : "";
	$patientImage=$patientDetails[0]["img"];
	
	$chartinfo="";
	/*//AK:19/01/12 DO not need DOS as it already listed in the main title bar
	if(!empty($chartDetails["date_of_service"])){
		$chartinfo.="<b>Date of Service:&nbsp;".$chartDetails["date_of_service"]."</b>&nbsp;&nbsp;&nbsp;&nbsp;";
	}*/
	
	if(!empty($chartDetails["ptVisit"]) && in_array("Chart Notes",$chartDetails)){
		$chartinfo.="<b>Visit:</b>&nbsp;".$chartDetails["ptVisit"]."&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	
	if(!empty($chartDetails["testing"])&& in_array("Chart Notes",$chartDetails)){
		$chartinfo.="<b>Testing:</b>&nbsp;".$chartDetails["testing"]."&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	
	if(!empty($elem_chartTempName)&& in_array("Chart Notes",$chartDetails)){
		$chartinfo.="<b>Template:</b>&nbsp;".$elem_chartTempName."";
	}	
	
	//<!--- New Header Appplied--->

?>
<table>
	<tr>
		<td  style="width:300px;" > 
			<table  style="width:300px;" cellpadding="0" cellspacing="0">
				<tr>
					<td style="width:300px;" class="text_lable"><?php if(!empty($patientName)){ print trim($patientDetails[0]['title'].' '.$patientName."-".$patientDetails[0]['id']); }?> </td>
				</tr>
				<tr>
					<td style="width:300px;"><?php print $patientDetails[0]['sex'];if(@!in_array("HIPPA",$fAct)){ print trim($strAge.$date_of_birth); }?>&nbsp; </td>
				</tr>
               <?php if(@!in_array("HIPPA",$fAct) && $patientDetails[0]['language']){ ?>
                <tr>
					<td><?php if(@!in_array("HIPPA",$fAct)){ print ($patientDetails[0]['language']!="")?"Language: ".($patientDetails[0]['language']):"";}?>&nbsp; </td>
				</tr>				
                <?php }
					if(@!in_array("HIPPA",$fAct) && $patientDetails[0]['race']){ ?>
                <tr>
					<td  style="width:300px;"><?php if(@!in_array("HIPPA",$fAct)){ print ($patientDetails[0]['race']!="")?"Race: ".($patientDetails[0]['race']):"";}?>&nbsp; </td>
				</tr>				
                <?php } 
				 if(@!in_array("HIPPA",$fAct) && $patientDetails[0]['ethnicity']){ ?>
                <tr>
					<td style="width:300px;"><?php if(@!in_array("HIPPA",$fAct)){ print ($patientDetails[0]['ethnicity']!="")?"Ethnicity: ".($patientDetails[0]['ethnicity']):"";}?> </td>
				</tr>				
                <?php } ?>
				<tr>
					<td style="width:300px;"><?php if(@!in_array("HIPPA",$fAct)){ print trim($patientDetails[0]['street'])."&nbsp;"; }?>&nbsp;</td>
				</tr>
                <?php if(@!in_array("HIPPA",$fAct) && trim($patientDetails[0]['street2'])){?>
				<tr>
					<td style="width:300px;"><?php  print $patientDetails[0]['street2'];?>&nbsp; </td>
				</tr>
                <?php } ?>
				<tr>
					<td style="width:300px;"><?php if(@!in_array("HIPPA",$fAct)){ print ($patientDetails[0]['city']!="")?$patientDetails[0]['city'].",":""; print("&nbsp;".$patientDetails[0]['state']."&nbsp;".$patientDetails[0]['postal_code']); }?>&nbsp; </td>
				</tr>
				<tr>
					<td style="width:300px;"><?php if(@!in_array("HIPPA",$fAct)){ print ($patientDetails[0]['phone_home']!="")?"Ph.: ".core_phone_format($patientDetails[0]['phone_home']):"";}?>&nbsp; </td>
				</tr>				
			</table>
		</td>
	<?php if(!empty($patientImage)){ ?>
		<td style="width:20%"  >
			<table rules="none" >
				 <tr>
					<td align="center"><?php print $patientImage; ?></td>
				</tr>
			</table> 
		</td>
	<?php }else{ echo('<td style="width:20%" >&nbsp;</td>');} ?>
		<td style="width:40%" align="right">
			<?php 
			//GROUP INFO
			if(!in_array("provider_name_and_contact",$exclusion)){
				$pat_id=$patientDetails[0]['id'];
				print_groupInfo1($pat_id,$chartDetails['date_of_service']);
			}
			?>
		</td>
	</tr>
	<?php if(!empty($chartinfo)){  ?>
	<tr>
		<td colspan="3"><?php  print $chartinfo; ?></td>
	</tr>
	<?php } ?>
</table>
<?php

//<!--- End New Header Appplied--->
	
}

//print pdfStyle ---------------------

//Group info --------------------------
function print_groupInfo1($patient_id,$chart_dos){
	//--- Get Default Facility Details -------
	$qry = "select default_group from facility where facility_type = 1";
	//$res = $GLOBALS['adodb']['db']->Execute($qry) or die("Error in query: ".$GLOBALS['adodb']['db']->errorMsg());
	$res = imw_query($qry);
	if($res !== false){
		$defGroup = $res->fields["default_group"];
	}
	//-- Get Group Facility from appointment table----------//
	$mm=$dd=$yy=$appt_date="";
	//list($mm,$dd,$yy)=explode("-",$chart_dos);
	//$appt_date=$yy."-".$mm."-".$dd;
    $appt_date=$chart_dos;
	if($appt_date && $patient_id){
		$qry_group_fac="select default_group from facility where id in(select sa_facility_id FROM schedule_appointments where sa_patient_id='".$patient_id."' and sa_app_start_date='".$appt_date."' and sa_facility_id!='')";
		$res_group_fac=imw_query($qry_group_fac);
		$row_group_fac=imw_fetch_assoc($res_group_fac);
		if(trim($row_group_fac['default_group'])){
			$defGroup=$row_group_fac['default_group'];
		}
	}
	//-----------------------------------------------------//
	//Group Info
	if(!empty($defGroup)){
		$qry = "select * from groups_new where gro_id = '".$defGroup."'";
	//	$res = $GLOBALS['adodb']['db']->Execute($qry) or die("Error in query: ".$GLOBALS['adodb']['db']->errorMsg());
		$row = imw_query($qry);
		$res = imw_fetch_assoc($row);
		if($row !== false){
			$groupDetails[0]['name'] = $res["name"];
			$groupDetails[0]['group_Address1'] = $res["group_Address1"];
			$groupDetails[0]['group_Address2'] = $res["group_Address2"];
			$groupDetails[0]['group_City'] = $res["group_City"];
			$groupDetails[0]['group_State'] = $res["group_State"];
			$groupDetails[0]['group_Zip'] = $res["group_Zip"];
			$groupDetails[0]['group_Telephone'] = $res["group_Telephone"];
			$groupDetails[0]['group_Fax'] = $res["group_Fax"];
		}
	}
	//--- Get Default Facility Details -------
	
	?>
	<table align="right" style="width:300px; ">
		<tr>
			<td class="text_lable" style="width:300px;text-align:right"><?php print $groupDetails[0]['name']; ?> </td>
		</tr>
		<tr>
			<td style="width:300px;text-align:right"><?php print ucwords($groupDetails[0]['group_Address1']); ?></td>
		</tr>
        <?php  if(trim($groupDetails[0]['group_Address2'])){?>
		<tr>
			<td style="width:300px;text-align:right"><?php print ucwords($groupDetails[0]['group_Address2']);?>&nbsp;</td>
		</tr>
        <?php } ?>
		<tr>
			<td style="width:300px;text-align:right"><?php print $groupDetails[0]['group_City'].', '.$groupDetails[0]['group_State'].' '.$groupDetails[0]['group_Zip']; ?>  </td>
		</tr>
        <?php if($groupDetails[0]['group_Telephone']){ ?>
		<tr>
			<td style="width:300px;text-align:right">Ph.:&nbsp;<?php print $groupDetails[0]['group_Telephone']; ?> </td>
		</tr>
        <?php }
			if($groupDetails[0]['group_Fax']){
		 ?>
		<tr>
			<td style="width:300px;text-align:right">Fax:&nbsp;<?php print $groupDetails[0]['group_Fax']; ?> </td>
		</tr>
        <?php } ?>
	</table>
	<?php 
}

?>