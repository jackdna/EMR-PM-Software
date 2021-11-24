<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
header('Access-Control-Allow-Origin: *');
include_once("../common/conDb.php");
$cQry="CREATE TABLE IF NOT EXISTS `tmp_tbl_api` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `api_appt_id` varchar(255) NOT NULL,
  `api_patient_id` varchar(255) NOT NULL,
  `api_appt_dos` date NOT NULL,
  `api_pdf_file_name` varchar(255) NOT NULL,
  `api_pdf_file_path` varchar(255) NOT NULL,
  `api_save_date_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
);";
$cRes = imw_query($cQry);
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
if(!$surgeryCenterWebrootDirectoryName) { $surgeryCenterWebrootDirectoryName=$surgeryCenterDirectoryName;	}

$pdf_dir = $rootServerPath."/".$surgeryCenterDirectoryName."/admin/pdfFiles";	
/*
$pdf_get_path = "E:/PDF/test_small.pdf";	
$pdf_content = @file_get_contents($pdf_get_path);
$base_encode_pdf = base64_encode($pdf_content);
echo $base_encode_pdf;die();
*/
?>

    <?php
    $dataArr = array("api_pdf_file_name"=>"smith.pdf","api_appt_id"=>"11","api_patient_id"=>"23","api_appt_dos"=>"2019-03-15");
    $dataArr["api_pdf_content"] = "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0RFqHfNlYyjZSK4dccb3OGYy1Zpj69fvu3RRLyjpuOjJJh+JjeSVi3b8uotN/rEuYjYhgb4sjp7BtzqpBNY3Xza20o3k2hdnfI9vJqk4voWvCYaD0wE3AY/sHP4FXrSEngplbmRzdHJlYW0KZW5kb2JqCjEgMCBvYmoKPDwvVHlwZSAvUGFnZXMKL0tpZHMgWzMgMCBSIF0KL0NvdW50IDEKL01lZGlhQm94IFswIDAgNTk1LjI4IDg0MS44OV0KPj4KZW5kb2JqCjUgMCBvYmoKPDwvVHlwZSAvRm9udAovQmFzZUZvbnQgL0FyaWFsTVQKL1N1YnR5cGUgL1RydWVUeXBlCi9GaXJzdENoYXIgMzIgL0xhc3RDaGFyIDI1NQovV2lkdGhzIDYgMCBSCi9Gb250RGVzY3JpcHRvciA3IDAgUgovRW5jb2RpbmcgL1dpbkFuc2lFbmNvZGluZwo+PgplbmRvYmoKNiAwIG9iagpbMjc4IDI3OCAzNTUgNTU2IDU1NiA4ODkgNjY3IDE5MSAzMzMgMzMzIDM4OSA1ODQgMjc4IDMzMyAyNzggMjc4IDU1NiA1NTYgNTU2IDU1NiA1NTYgNTU2IDU1NiA1NTYgNTU2IDU1NiAyNzggMjc4IDU4NCA1ODQgNTg0IDU1NiAxMDE1IDY2NyA2NjcgNzIyIDcyMiA2NjcgNjExIDc3OCA3MjIgMjc4IDUwMCA2NjcgNTU2IDgzMyA3MjIgNzc4IDY2NyA3NzggNzIyIDY2NyA2MTEgNzIyIDY2NyA5NDQgNjY3IDY2NyA2MTEgMjc4IDI3OCAyNzggNDY5IDU1NiAzMzMgNTU2IDU1NiA1MDAgNTU2IDU1NiAyNzggNTU2IDU1NiAyMjIgMjIyIDUwMCAyMjIgODMzIDU1NiA1NTYgNTU2IDU1NiAzMzMgNTAwIDI3OCA1NTYgNTAwIDcyMiA1MDAgNTAwIDUwMCAzMzQgMjYwIDMzNCA1ODQgNzUwIDU1NiA3NTAgMjIyIDU1NiAzMzMgMTAwMCA1NTYgNTU2IDMzMyAxMDAwIDY2NyAzMzMgMTAwMCA3NTAgNjExIDc1MCA3NTAgMjIyIDIyMiAzMzMgMzMzIDM1MCA1NTYgMTAwMCAzMzMgMTAwMCA1MDAgMzMzIDk0NCA3NTAgNTAwIDY2NyAyNzggMzMzIDU1NiA1NTYgNTU2IDU1NiAyNjAgNTU2IDMzMyA3MzcgMzcwIDU1NiA1ODQgMzMzIDczNyA1NTIgNDAwIDU0OSAzMzMgMzMzIDMzMyA1NzYgNTM3IDMzMyAzMzMgMzMzIDM2NSA1NTYgODM0IDgzNCA4MzQgNjExIDY2NyA2NjcgNjY3IDY2NyA2NjcgNjY3IDEwMDAgNzIyIDY2NyA2NjcgNjY3IDY2NyAyNzggMjc4IDI3OCAyNzggNzIyIDcyMiA3NzggNzc4IDc3OCA3NzggNzc4IDU4NCA3NzggNzIyIDcyMiA3MjIgNzIyIDY2NyA2NjcgNjExIDU1NiA1NTYgNTU2IDU1NiA1NTYgNTU2IDg4OSA1MDAgNTU2IDU1NiA1NTYgNTU2IDI3OCAyNzggMjc4IDI3OCA1NTYgNTU2IDU1NiA1NTYgNTU2IDU1NiA1NTYgNTQ5IDYxMSA1NTYgNTU2IDU1NiA1NTYgNTAwIDU1NiA1MDAgXQplbmRvYmoKNyAwIG9iago8PC9UeXBlIC9Gb250RGVzY3JpcHRvciAvRm9udE5hbWUgL0FyaWFsTVQgL0FzY2VudCA3MjggL0Rlc2NlbnQgLTIxMCAvQ2FwSGVpZ2h0IDcxNiAvRmxhZ3MgMzIgL0ZvbnRCQm94IFstNjY1IC0zMjUgMjAwMCAxMDQwXSAvSXRhbGljQW5nbGUgMCAvU3RlbVYgNzAgL01pc3NpbmdXaWR0aCA3NTA+PgplbmRvYmoKOCAwIG9iago8PC9UeXBlIC9Gb250Ci9CYXNlRm9udCAvQXJpYWwtQm9sZE1UCi9TdWJ0eXBlIC9UcnVlVHlwZQovRmlyc3RDaGFyIDMyIC9MYXN0Q2hhciAyNTUKL1dpZHRocyA5IDAgUgovRm9udERlc2NyaXB0b3IgMTAgMCBSCi9FbmNvZGluZyAvV2luQW5zaUVuY29kaW5nCj4+CmVuZG9iago5IDAgb2JqClsyNzggMzMzIDQ3NCA1NTYgNTU2IDg4OSA3MjIgMjM4IDMzMyAzMzMgMzg5IDU4NCAyNzggMzMzIDI3OCAyNzggNTU2IDU1NiA1NTYgNTU2IDU1NiA1NTYgNTU2IDU1NiA1NTYgNTU2IDMzMyAzMzMgNTg0IDU4NCA1ODQgNjExIDk3NSA3MjIgNzIyIDcyMiA3MjIgNjY3IDYxMSA3NzggNzIyIDI3OCA1NTYgNzIyIDYxMSA4MzMgNzIyIDc3OCA2NjcgNzc4IDcyMiA2NjcgNjExIDcyMiA2NjcgOTQ0IDY2NyA2NjcgNjExIDMzMyAyNzggMzMzIDU4NCA1NTYgMzMzIDU1NiA2MTEgNTU2IDYxMSA1NTYgMzMzIDYxMSA2MTEgMjc4IDI3OCA1NTYgMjc4IDg4OSA2MTEgNjExIDYxMSA2MTEgMzg5IDU1NiAzMzMgNjExIDU1NiA3NzggNTU2IDU1NiA1MDAgMzg5IDI4MCAzODkgNTg0IDc1MCA1NTYgNzUwIDI3OCA1NTYgNTAwIDEwMDAgNTU2IDU1NiAzMzMgMTAwMCA2NjcgMzMzIDEwMDAgNzUwIDYxMSA3NTAgNzUwIDI3OCAyNzggNTAwIDUwMCAzNTAgNTU2IDEwMDAgMzMzIDEwMDAgNTU2IDMzMyA5NDQgNzUwIDUwMCA2NjcgMjc4IDMzMyA1NTYgNTU2IDU1NiA1NTYgMjgwIDU1NiAzMzMgNzM3IDM3MCA1NTYgNTg0IDMzMyA3MzcgNTUyIDQwMCA1NDkgMzMzIDMzMyAzMzMgNTc2IDU1NiAzMzMgMzMzIDMzMyAzNjUgNTU2IDgzNCA4MzQgODM0IDYxMSA3MjIgNzIyIDcyMiA3MjIgNzIyIDcyMiAxMDAwIDcyMiA2NjcgNjY3IDY2NyA2NjcgMjc4IDI3OCAyNzggMjc4IDcyMiA3MjIgNzc4IDc3OCA3NzggNzc4IDc3OCA1ODQgNzc4IDcyMiA3MjIgNzIyIDcyMiA2NjcgNjY3IDYxMSA1NTYgNTU2IDU1NiA1NTYgNTU2IDU1NiA4ODkgNTU2IDU1NiA1NTYgNTU2IDU1NiAyNzggMjc4IDI3OCAyNzggNjExIDYxMSA2MTEgNjExIDYxMSA2MTEgNjExIDU0OSA2MTEgNjExIDYxMSA2MTEgNjExIDU1NiA2MTEgNTU2IF0KZW5kb2JqCjEwIDAgb2JqCjw8L1R5cGUgL0ZvbnREZXNjcmlwdG9yIC9Gb250TmFtZSAvQXJpYWwtQm9sZE1UIC9Bc2NlbnQgNzI4IC9EZXNjZW50IC0yMTAgL0NhcEhlaWdodCA3MTYgL0ZsYWdzIDMyIC9Gb250QkJveCBbLTYyOCAtMzc2IDIwMDAgMTA1Nl0gL0l0YWxpY0FuZ2xlIDAgL1N0ZW1WIDEyMCAvTWlzc2luZ1dpZHRoIDc1MD4+CmVuZG9iagoxMSAwIG9iago8PC9UeXBlIC9YT2JqZWN0Ci9TdWJ0eXBlIC9JbWFnZQovV2lkdGggMQovSGVpZ2h0IDEKL0NvbG9yU3BhY2UgL0RldmljZVJHQgovQml0c1BlckNvbXBvbmVudCA4Ci9GaWx0ZXIgL0RDVERlY29kZQovTGVuZ3RoIDY5NT4+CnN0cmVhbQr/2P/gABBKRklGAAEBAAABAAEAAP/+AD5DUkVBVE9SOiBnZC1qcGVnIHYxLjAgKHVzaW5nIElKRyBKUEVHIHY4MCksIGRlZmF1bHQgcXVhbGl0eQr/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD3+iiigD//2QplbmRzdHJlYW0KZW5kb2JqCjIgMCBvYmoKPDwKL1Byb2NTZXQgWy9QREYgL1RleHQgL0ltYWdlQiAvSW1hZ2VDIC9JbWFnZUldCi9Gb250IDw8Ci9GMSA1IDAgUgovRjIgOCAwIFIKPj4KL1hPYmplY3QgPDwKL0kxIDExIDAgUgo+Pgo+PgplbmRvYmoKMTIgMCBvYmoKPDwKL1Byb2R1Y2VyIChGUERGIDEuNikKL0NyZWF0aW9uRGF0ZSAoRDoyMDE5MDMxNDExMTIyNikKPj4KZW5kb2JqCjEzIDAgb2JqCjw8Ci9UeXBlIC9DYXRhbG9nCi9QYWdlcyAxIDAgUgovT3BlbkFjdGlvbiBbMyAwIFIgL0ZpdEggbnVsbF0KL1BhZ2VMYXlvdXQgL09uZUNvbHVtbgo+PgplbmRvYmoKeHJlZgowIDE0CjAwMDAwMDAwMDAgNjU1MzUgZiAKMDAwMDAwMTkxNSAwMDAwMCBuIAowMDAwMDA1NDA3IDAwMDAwIG4gCjAwMDAwMDAwMDkgMDAwMDAgbiAKMDAwMDAwMDA4NyAwMDAwMCBuIAowMDAwMDAyMDAyIDAwMDAwIG4gCjAwMDAwMDIxNjMgMDAwMDAgbiAKMDAwMDAwMzA4NCAwMDAwMCBuIAowMDAwMDAzMjcwIDAwMDAwIG4gCjAwMDAwMDM0MzcgMDAwMDAgbiAKMDAwMDAwNDM1NyAwMDAwMCBuIAowMDAwMDA0NTUwIDAwMDAwIG4gCjAwMDAwMDU1MzIgMDAwMDAgbiAKMDAwMDAwNTYwOCAwMDAwMCBuIAp0cmFpbGVyCjw8Ci9TaXplIDE0Ci9Sb290IDEzIDAgUgovSW5mbyAxMiAwIFIKPj4Kc3RhcnR4cmVmCjU3MTIKJSVFT0YK";
    //$dataJson = json_encode(array("api_data"=>$dataArr));
    //echo $dataJson;die;
    //$dataJsonArr = array("demo"=>$dataJson);
	//print'<pre>';
	//print_r($dataJson);die;
	/*$req = parse_url($_SERVER['REQUEST_URI'],PHP_URL_QUERY);
	var_dump($req[0]);//print'<pre>';print_r($_POST);die('hlo '.$_SERVER['REQUEST_URI']);
    if(trim($req[0])) {
		$dataJson = $req[0];
	}*/
	
	print'<pre>';print_r($_POST);
	$output = print_r($_POST, true);
	file_put_contents($pdf_dir.'/api_test.txt', $output);
	//$dataArrNew = (object)$_POST; //WITH JQUERY

    $dataArrNew = json_decode(stripslashes($_POST['api_data'])); //WITH CURL
	print_r($dataArrNew);
    $api_pdf_file_name = stripslashes($dataArrNew->api_pdf_file_name);
    $api_appt_id = $dataArrNew->api_appt_id;
    $api_patient_id = $dataArrNew->api_patient_id;
    $api_appt_dos = $dataArrNew->api_appt_dos;
    $api_pdf_content = $dataArrNew->api_pdf_content;
    
    
    
    //die;
    $ret_result = "";
    if($api_appt_id || $api_pdf_file_name || $api_patient_id || $api_appt_dos) {
                
        $api_pdf_file_name = str_ireplace(".pdf","",$api_pdf_file_name);
		$api_pdf_file_name = trim(preg_replace("/[^A-Za-z0-9]/","",$api_pdf_file_name));
		if(!$api_pdf_file_name) {
            $api_pdf_file_name = "demo";	
        }
		$api_pdf_file_name_new = $api_pdf_file_name;
        if(strtolower(end(explode(".",$api_pdf_file_name)))!="pdf") {
            $api_pdf_file_name = $api_pdf_file_name.".pdf";	
        }
        $apiDir = $pdf_dir."/api";
        if(!is_dir($apiDir)) {
            mkdir($apiDir, 0777);	
        }
		$api_pdf_file_name_new = $api_pdf_file_name_new.date("m_d_Y_H_i_s").".pdf";
        $putPdfFilePath = $apiDir."/".$api_pdf_file_name_new;
        
        if(file_exists($putPdfFilePath)) {
            unlink($putPdfFilePath);	
        }
        $putPdfDocdata = base64_decode($api_pdf_content);
        
        file_put_contents($putPdfFilePath,$putPdfDocdata);
        
        $save_pdf_file_path = "";
        if(file_exists($putPdfFilePath)) {
            $save_pdf_file_path = "pdfFiles/api/".$api_pdf_file_name_new;
        }
    
		$qry = "INSERT INTO tmp_tbl_api 
				SET api_appt_id = '".$api_appt_id."',api_patient_id = '".$api_patient_id."', api_appt_dos = '".$api_appt_dos."', 
				api_pdf_file_name = '".$api_pdf_file_name."', api_pdf_file_path = '".$save_pdf_file_path."',api_save_date_time = '".date("Y-m-d H:i:s")."'	
				";
		$res = imw_query($qry) or die($qry. imw_error());
		if(imw_insert_id()) {
			$ret_result =  "Success";	
		}else {
			$ret_result = "Fail";	
		}
    }else {
		$ret_result = "Fail";
	}
	echo "<br>".$ret_result;
    ?>    
