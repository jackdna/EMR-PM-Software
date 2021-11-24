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

set_time_limit(800);
require_once("../../../../config/globals.php");
require_once("../../../../library/classes/common_function.php");
$mode = ($_REQUEST['mode'])?$_REQUEST['mode']:"";
switch($mode){
	case "export":	
		$filename = 'Medicine_Data.csv';
		$fp1=fopen($filename,'w');
			$result="SELECT id,medicine_name,ocular,glucoma,ret_injection,alias,description,prescription,alert,ccda_code,fdb_id FROM medicine_data 
			WHERE del_status = '0' ORDER BY medicine_name ASC";
			$res=imw_query($result)or die(imw_error());
			$data_head[]=array('ID','Medicine','Ocular','Glaucoma','Ret Inj','Alias','Description','Rx Req','Alert','RxNorm Code','FDB Id');
			$data=array();
		while($row=imw_fetch_assoc($res)){
			$data[]=$row;
		}
		foreach ($data_head as $fields1){
			fputcsv($fp1, $fields1);	
		}
		foreach ($data as $fields){
			fputcsv($fp1, $fields);
		}
		fclose($fp1);
		$csv_text = file_get_contents($filename);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Description: File Transfer");
		header("Content-Type: application/octet-stream;");
		header("Content-disposition:attachment; filename=\"".$filename."\"");
		header("Content-Length: ".@filesize($filename));
		@readfile($filename) or die("File not found.");
		unlink($filename);
		exit;
		break;
	case "import":
		if ($_FILES['csv_file']['size'] > 0) {
			$file = $_FILES['csv_file']['tmp_name'];
			$handle = fopen($file,"r");
			$arrMeds = array();
			$data_col = array("ID"=>"id","Medicine"=>"medicine_name","Ocular"=>"ocular","Glaucoma"=>"glucoma","Ret Inj"=>"ret_injection","Alias"=>"alias","Description"=>"description","Rx Req"=>"prescription","Alert"=>"alert","RxNorm Code"=>"ccda_code","FDB Id"=>"fdb_id");
			$count = 0;
			while($data = fgetcsv($handle,1000,",","'")){
				if($count == 0){
						$headArr = $data;
				}
				else{
					for($i = 0;$i<count($data);$i++){
						$key = $headArr[$i];
						$col = $data_col[$key];
						$arrMeds[$count][$col] = $data[$i];
					}
				}
				$count++;
			} 
		}
		
		foreach($arrMeds as $record){
			if($record['id'] == "" || $record['id'] == "0"){
			if($record['medicine_name'] != "")
				AddRecords($record,"medicine_data");
			}
			else	
				UpdateRecords($record['id'],"id",$record,"medicine_data");
		}
		header("Location:index.php");
		die();
	break;
}

function fnLineBrk($str){
	return str_replace(array("\r","\n"),array("\\r","\\n"),$str);
}
?>