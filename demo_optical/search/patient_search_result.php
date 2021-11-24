<?php
/*
File: patient_search_result.php
Coded in PHP7
Purpose: Patient Search
Access Type: Include File
*/
require_once(dirname('__FILE__')."/../config/config.php"); 
if($_SESSION['default_tab']!="demographics"){
$_SESSION['default_tab']="patient_interface";
}
?>
<!DOCTYPE html>
<html><head>
<title>Optical</title>
<link rel="stylesheet" href="../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script>
$(document).unbind('keydown').bind('keydown', function (event) {
    var doPrevent = false;
    if (event.keyCode === 8) {
        var d = event.srcElement || event.target;
        if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD' || d.type.toUpperCase() === 'FILE')) 
             || d.tagName.toUpperCase() === 'TEXTAREA') {
            doPrevent = d.readOnly || d.disabled;
        }
        else {
            doPrevent = true;
        }
    }

    if (doPrevent) {
        event.preventDefault();
    }
});
</script>
</head>
<body class="body_c" onkeydown="return stopKey()">
    <div style="overflow-y:auto;">
        <table class="table_collapse cellBorder">
        <?php
            $txt_for=trim($_REQUEST['txt_for']);
            $sel_by=$_REQUEST['sel_by'];
            function getFindBy($search)
            {
               $genderSearch = "";
               $arrSearch = explode(";",$search);
               $search = trim($arrSearch[0]);
               $genderSearch = trim($arrSearch[1]);
               if(strtoupper($genderSearch) == "M"){
                    $genderSearch = "Male";
               }
               elseif(strtoupper($genderSearch) == "MALE"){
                    $genderSearch = "Male";
               }
               elseif(strtoupper($genderSearch) == "F"){
                    $genderSearch = "Female";
               }
               elseif(strtoupper($genderSearch) == "FEMALE"){
                    $genderSearch = "Female";
               }
               
               $search = trim($search);    
               $retVal = "Last";
               $ptrnSSN = '/^[0-9]{3}-[0-9]{2}-[0-9]{4}$/'; 
               $ptrnPhone = '/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/'; 
               $ptrnDate = '/^((0[1-9])|(1[012]))[-\/](0[1-9]|[12][0-9]|3[01])[-\/]((18|19|20|21)?[0-9]{2})$/'; 
               if(is_numeric($search))
               {
                 $retVal = "ID";
               }
               elseif(preg_match($ptrnSSN,$search))
               {
                 $retVal = "SSN";
               }
               elseif(preg_match($ptrnPhone,$search))
               {
                 $retVal = "phone";
               }
               elseif(preg_match($ptrnDate,$search))
               {
                 $retVal = "DOB";  
               }   
               elseif(preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2}/is',$search))
               {
                 $retVal = "email";    
               }
               elseif(preg_match('/\w+/',$search) && (preg_match('/\d+/',$search)) && (preg_match('/\s*/',$search)))
               {
                 $retVal = "street";    
               }
               
               elseif(strpos($search,",") !== false)
               {
                 $retVal = "LastFirstName";
               }
               elseif(is_string($search))
               {
                 $retVal = "Last";  
               }
               
               return $retVal;
            } 
        
            if(empty($txt_for))
            {
              $sel_by = "Nothing";      
            }
            else
            {
                if(($sel_by != "Resp.LN") && ($sel_by != "Ins.Policy") )
                {
                    $elem_status=$sel_by;
                    $sel_by=trim(getFindBy($txt_for));
                }
            }
            switch($sel_by)
            {
               case "Last":
                   $sel_by="lname";
               break;
               case "LastFirstName":
                   $sel_by="LastFirstName";
               break;
               case "street":
                   $sel_by="street";
               break;
               case "phone":
                   $sel_by="phone";
               break;
               case "First":
                   $sel_by="fname";
               break;
               case "ID":
                   $sel_by="id";
               break;
               case "DOB":
                   $sel_by="DOB";
               break;
               case "SSN":
                   $sel_by="ss";
               break;
               case "Resp.LN":
                   $sel_by="Resp.LN";
               break;
               case "Ins.Policy":
                   $sel_by="Ins.Policy";
               break;
            }
            if($elem_status==""){
                $elem_status="Active";
            }
            if($sel_by == "Resp.LN"){
                $qry = "select * from patient_data left join resp_party on
                        patient_data.id = resp_party.patient_id where
                        resp_party.lname = ".imw_real_escape_string($txt_for)."";
            }
            else if($sel_by == "Ins.Policy"){
                $qry="SELECT
                    insurance_data.policy_number,	
                    patient_data.fname,patient_data.pid,patient_data.lname,
                    patient_data.street,patient_data.phone_home,patient_data.ss,patient_data.DOB,patient_data.id
                    FROM insurance_data 
                    INNER JOIN patient_data ON insurance_data.pid = patient_data.id
                    WHERE insurance_data.policy_number LIKE '".imw_real_escape_string($txt_for)."%'
                    GROUP BY patient_data.id	
                    ORDER BY patient_data.fname";
            }else if($sel_by == "DOB"){
				$ptDob = "";
				$txt_for = str_ireplace('/','-',$txt_for);
				$ptDob = DateTime::createFromFormat('m-d-Y',$txt_for);
				$ptDob = $ptDob->format('Y-m-d');
                $qry="select * from patient_data where DOB like '%".$ptDob."%' order by fname ASC";
			}
            else{
                if(($sel_by != 'Nothing') && ($sel_by != 'LastFirstName') && ($sel_by != 'phone')){
                    $txt_for = ($sel_by != "id") ? $txt_for."%" : $txt_for;
                    $qry ="select * from patient_data where ".$sel_by." like '".imw_real_escape_string($txt_for)."' AND patientStatus='".$elem_status."' order by fname";
                }else if($sel_by == 'LastFirstName'){
                    list($txt_for1, $txt_for2) = explode(",",$txt_for);
                    $txt_for1 = trim($txt_for1);
                    $txt_for2 = trim($txt_for2);
                    $qry ="select * from patient_data where lname like '".imw_real_escape_string($txt_for1)."%' AND fname  like '".imw_real_escape_string($txt_for2)."%' AND patientStatus='".$elem_status."' order by fname";
                }else if($sel_by != 'phone'){
                    $qry = "select * from patient_data where (phone_home like '".imw_real_escape_string($txt_for)."%' OR phone_biz like '".imw_real_escape_string($txt_for)."%' OR phone_contact like '".imw_real_escape_string($txt_for)."%' OR phone_cell like '".imw_real_escape_string($txt_for)."%')  AND patientStatus='".$elem_status."' order by fname";            
                }
            }
            $pat_qry=imw_query($qry);	
            if($sel_by=="id"){
                if(imw_num_rows($pat_qry)>0){
					$_SESSION['patient_session_id'] = $txt_for;
					$_SESSION['patient_session_ins_alert'] = 0;
                    echo "<script type='text/javascript'>top.window.location.href='../index2.php'</script>";
                }else{
                    
                }
            }
            if(imw_num_rows($pat_qry)>0){
                $data = '
                    <tr>
                        <td align="center" class="listheading" width="150px">Patient ID</td>
                        <td align="center" class="listheading" width="160px">First Name</td>
                        <td align="center" class="listheading" width="155px">Last Name</td>
						<td align="center" class="listheading" width="150px">DOB</td>						
                        <td align="center" class="listheading" width="250px">Address</td>
                        <td align="center" class="listheading" width="150px">City</td>
                        <td align="center" class="listheading" width="100px">State</td>
                        <td align="center" class="listheading" width="150px">Phone</td>
                    </tr>
                ';
                while($patientData=imw_fetch_array($pat_qry))
                {
                    $id = $patientData['id'];
					$data .='
                        <tr>
                            <td align="left"><a href="../index2.php?patient_session_id='.$id.'" target="_parent" class="text_12">'.$patientData['id'].'</a>&nbsp;</td>
                            <td align="left"><a href="../index2.php?patient_session_id='.$id.'" target="_parent" class="text_12">'.$patientData['fname'].'</a>&nbsp;</td>
                            <td align="left"><a href="../index2.php?patient_session_id='.$id.'" target="_parent" class="text_12">'.$patientData['lname'].'</a>&nbsp;</td>
                            <td align="left"><a href="../index2.php?patient_session_id='.$id.'" target="_parent" class="text_12">'.date('m-d-Y',strtotime($patientData['DOB'])).'</a>&nbsp;</td>
							<td align="left"><a href="../index2.php?patient_session_id='.$id.'" target="_parent" class="text_12">'.$patientData['street'].'</a>&nbsp;</td>
                            <td align="left"><a href="../index2.php?patient_session_id='.$id.'" target="_parent" class="text_12">'.$patientData['city'].'</a>&nbsp;</td>
                            <td align="center"><a href="../index2.php?patient_session_id='.$id.'" target="_parent" class="text_12">'.$patientData['state'].'</a>&nbsp;</td>
                            <td align="left"><a href="../index2.php?patient_session_id='.$id.'" target="_parent" class="text_12">'.$patientData['phone_home'].'</a>&nbsp;</td>
                        </tr>
                    ';
                }
            }
            else
            {
                $data .= '
                    <tr>
                        <td colspan="6" class="text_12b" style="text-align:center;">
                            No Record Found.
                        </td>
                    </tr>
                ';
            }
            echo $data;
        ?>
        </table>
    </div>    
</body>
</html>
 
