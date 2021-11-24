<?php
set_time_limit(0);
$ignoreAuth = true;
include("../../../../config/globals.php");

//================START CODE TO INSERT 2018-19 NEW AAO TEMPLATES====================================
$folder_eng="Academy Handouts Fall 2019 English";
$folder_spn="Academy Handouts Fall 2019 Spanish";
$folder_path_eng=data_path()."admin_documents/upload/".$folder_eng;
$folder_path_spn=data_path()."admin_documents/upload/".$folder_spn;

if(!is_dir($folder_path_eng)){echo "<span style='color:#000;font-family:verdana;'>English Document Folder is mission in following Location: <br>Please copy folder with name <b>'Academy Handouts Fall 2018 English'<br>'".$folder_path_eng."</b>'</span><hr>";}
if(!is_dir($folder_path_spn)){echo "<span style='color:#000;font-family:verdana;;'>Spanish Document Folder is mission in following Location: <br>Please copy folder with name <b>'Academy Handouts Fall 2018 Spanish'<br>'".$folder_path_spn."</b>'</span><hr>";}
$arr_pdf_files=array();
if(is_dir($folder_path_eng)){
	foreach(glob($folder_path_eng."/*.pdf") as $pdf_file_name_eng){
		$pdf_doc_name_en=basename($pdf_file_name_eng);
		$arr_pdf_files[]="/admin_documents/upload/".$folder_eng."/".$pdf_doc_name_en;
	}	
}
if(is_dir($folder_path_spn)){
	foreach(glob($folder_path_spn."/*.pdf") as $pdf_file_name_spn){
		$pdf_doc_name_sp=basename($pdf_file_name_spn);
		$arr_pdf_files[]="/admin_documents/upload/".$folder_spn."/".$pdf_doc_name_sp;
	}	
}

if(count($arr_pdf_files)>0){
	$row_inserted=0;
	foreach($arr_pdf_files as $file_full_path){
		$pdf_file_name=basename($file_full_path,".pdf");	
		$qrySelect="SELECT id from document WHERE name='".$pdf_file_name."' and status=0 ";
		//echo $qrySelect.'<br/>';
		$resSelect=imw_query($qrySelect);
		if(imw_num_rows($resSelect)==0){
			$qry_insert_doc="INSERT INTO document set name='".$pdf_file_name."', pt_edu='1',andOrCondition='A',lab_criteria='greater',doc_from='uploadDoc',upload_doc_type='pdf',upload_doc_date=NOW(),upload_doc_file_path='".$file_full_path."'";
			$res_insert_doc=imw_query($qry_insert_doc);	
			if($res_insert_doc){$row_inserted++;}
		}
	}
}
?>

<html>
<head>
<title>Update 48 upload 2018-19 AAO Documents</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body style="font-family:verdana; color:#090; font-weight:bold;">
<br>
<?php
if($row_inserted>0 || $row_inserted!=""){
?>
Total number of record inserted <?php echo $row_inserted;  
}
?>
<br>
</body>
</html>