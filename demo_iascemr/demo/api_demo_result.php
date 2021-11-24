<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("../common/conDb.php");
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];//print'<pre>';print_r($_SERVER);
if(!$surgeryCenterWebrootDirectoryName) { $surgeryCenterWebrootDirectoryName=$surgeryCenterDirectoryName;	}
$upDir = $rootServerPath."/".$surgeryCenterDirectoryName."/admin/";

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgerycenter EMR</title>
<link rel="stylesheet" href="../css/sfdc_header.css" type="text/css" />
<link rel="stylesheet" href="../css/jquery.webui-popover.css" />
<link rel="stylesheet" type="text/css" href="../css/style.css" />
<link rel="stylesheet" type="text/css" href="../css/font-awesome.css" />
<link rel="stylesheet" type="text/css" href="../css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="../css/bootstrap-select.css" />
<link rel="stylesheet" type="text/css" href="../css/ion.calendar.css" />
<link rel="stylesheet" type="text/css" href="../css/datepicker.css" />
<?php
include_once("../common/commonFunctions.php");
$spec = "
</head>

<body>
";
echo $spec;
//include("common/link_new_file.php");

$qry = "SELECT *,if(api_appt_dos != '0000-00-00', DATE_FORMAT(api_appt_dos, '%m-%d-%Y'),'') AS api_appt_dos_format FROM tmp_tbl_api ORDER BY id";
$res = imw_query($qry) or die($qry.imw_error());
$cnt=0;
?>
<table style="width:99%;" cellpadding="5" cellspacing="5" align="left" class="table-bordered  table-condensed cf  table-striped">
<?php
	if(imw_num_rows($res)>0) {
	?>
    
        <tr>
            <td colspan="6" align="center"><label>Saved information</label><br><br></td>
        </tr>
        <tr>
            <td><label >S.No</label></td>
            <td><label >Appointment ID</label></td>
            <td><label >Patient ID</label></td>
            <td><label >DOS</label></td>
            <td><label >PDF File Name</label></td>
            <td><label >Save Date Time</label></td>
        </tr>
<?php	
        while($row = imw_fetch_assoc($res)) {
            $cnt++;
            $api_appt_id 		= $row["api_appt_id"];
            $api_patient_id 	= $row["api_patient_id"];
            $api_appt_dos_format= $row["api_appt_dos_format"];
            $api_pdf_file_name 	= $row["api_pdf_file_name"];
            $api_pdf_file_path 	= $row["api_pdf_file_path"];
            $api_pdf_file_full_path = $upDir.$api_pdf_file_path;
            $api_pdf_file_full_path_show = "../admin/".$api_pdf_file_path;
            $api_save_date_time = $row["api_save_date_time"];
            $api_save_date_time_format = "";
            if($api_save_date_time!="0000-00-00 00:00:00") {
                $api_save_date_time_format = date("m-d-Y h:i A",strtotime($api_save_date_time));
            }
            
?>
            <tr>
                <td><?php echo $cnt; if(file_exists($api_pdf_file_full_path)) {?>&nbsp;&nbsp;<img src="../images/icon-pdf.png" style="cursor:pointer; width:25px;height:25px; " alt="<?php echo $file;?>" onClick="window.open('<?php echo $api_pdf_file_full_path_show;?>')"<?php }?>></td>
                <td><?php echo $api_appt_id;?></td>
                <td><?php echo $api_patient_id;?></td>
                <td><?php echo $api_appt_dos_format;?></td>
                <td><?php echo $api_pdf_file_name;?></td>
                <td><?php echo $api_save_date_time_format;?></td>
            </tr>
<?php
        }
	}else {
?>
		<tr>
            <td colspan="6" align="center"><label>No Record Found</label><br><br></td>
        </tr>
<?php		
	}
?>
</table>
</body>
</html>