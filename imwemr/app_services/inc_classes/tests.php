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
?>
<?php
set_time_limit(900);
include_once(dirname(__FILE__).'/patient_app.php');
include_once(dirname(__FILE__).'/testPrintFunctions.php');

class tests extends patient_app{	
	var $arr_all_test = array(
							"Ascan"=>"surgical_tbl",
							"Bscan"=>"test_bscan",
							"Cell Count"=>"test_cellcnt",
							"External"=>"disc_external",
							"Fundus"=>"disc",
							"GDX"=>"test_gdx", 
							"HRT"=>"nfa", 
							"ICG"=>"icg", 
							"IOL Master"=>"iol_master_tbl", 
							"IVFA"=>"ivfa", 
							"Laboratories"=>"test_labs", 
							"OCT"=> "oct",
							"OCT-RNFL"=> "oct_rnfl",
							"Pacchy"=>"pachy", 
							"Topogrphy"=>"topography",
							"VF"=>"vf", 
							"VF-GL"=>"vf_gl", 
							"Other"=>"test_other",
							"Template"=>"test_other"
						);
	public function __construct(){
		parent::__construct();
	}
	
	function getTempNM($id){
		$str="";
		$sql  = "select temp_name from tests_name where id = '".$id."' ";
		$row=sqlQuery($sql);
		if($row!=false){		$str = $row["temp_name"];	}
		return $str;
	}
	
	public function test_manager(){
		$arr_all_test = $this->arr_all_test;
		foreach($arr_all_test as $key => $this_test){ // BEGIN FOREACH
			$test_name = $this_test;
			$patientId = 'patient_id';
			if($test_name=='vf' || $test_name=='vf_gl' || $test_name=='pachy' || $test_name=='disc' || $test_name=='disc_external' || $test_name=='topography' || $test_name=='test_bscan' || $test_name == "test_other" || $test_name == "test_labs" || $test_name == "test_cellcnt"){
				$patientId = 'patientId';
			}
			$phyName = 'phyName';
			if($test_name=='ivfa' || $test_name=='icg'){
				$phyName = 'phy';
			}
			if(in_array($test_name,array('surgical_tbl','iol_master_tbl'))){
				$phyName = 'signedById';
			}
			$pkIdCol = $test_name."_id";
			$examDate = "examDate";
			if($test_name=='icg')					{$examDate 	= "exam_date";}
			else if($test_name=='ivfa')				{$pkIdCol 	= "vf_id";	$examDate = "exam_date";}
			else if($test_name=='disc_external')	{$pkIdCol 	= "disc_id";}
			else if($test_name=='topography')		{$pkIdCol 	= "topo_id";}
			else if($test_name=='test_gdx')			{$pkIdCol 	= "gdx_id";}
			else if($test_name=='surgical_tbl')		{$pkIdCol 	= "surgical_id";}
			else if($test_name=='iol_master_tbl')	{$pkIdCol 	= "iol_master_id";}
			
			//template/other--
			$str_temp_test_wh=""; $str_temp_test_get="";
			if($test_name=='test_other'){
				if($key=='Other'){
					$str_temp_test_wh = " AND test_template_id='0' ";
				}else if($key=='Template'){
					$str_temp_test_wh = " AND test_template_id > '0' ";
					$str_temp_test_get =" , ".$test_name.".test_template_id ";
				}
			}
			//template/other--
			
			$qry_tests[$key] = "SELECT '".$test_name."' AS test_name, ".$test_name.".".$pkIdCol." AS testId, DATE_FORMAT(".$test_name.".".$examDate.",'".get_sql_date_format('','y')."') AS examDate1, ".$test_name.".ordrby, ".$test_name.".".$phyName." AS phyName, 
								pd.providerID, ".$test_name.".examTime ".$str_temp_test_get."								
								FROM ".$test_name." 
								JOIN patient_data pd ON (pd.id = ".$test_name.".".$patientId.") 
								WHERE ".$test_name.".del_status = '0' AND ".$test_name.".purged = '0' AND ".$patientId."='".$this->patient."' ".$str_temp_test_wh." 
								ORDER BY ".$examDate." DESC";
			$ARR_sorted_tests = array();
			$recent_exam_date = 0;
			foreach($qry_tests as $testDname=>$q){
				$temp_res = imw_query($q);
				$temp_rs = imw_fetch_assoc($temp_res);
				$str_examtime = str_replace('-','',$temp_rs['examTime']);
				$str_examtime = str_replace(':','',$str_examtime);
				$str_examtime = floatval(str_replace(' ','',$str_examtime));
				//echo intval($str_examtime).'<br>';
				//echo floatval($str_examtime).'<br>';
				if($str_examtime > $recent_exam_date){
					$recent_exam_date = $str_examtime;
					array_unshift($ARR_sorted_tests,array($testDname=>$q));
				}else{
					array_push($ARR_sorted_tests,array($testDname=>$q));
				}
			}
	
		}//---END FOREACH
		unset($qry_tests);
		$qry_tests = array();
		foreach($ARR_sorted_tests as $i=>$arr){
			foreach($arr as $t=>$q){
				$qry_tests[$t] = $q;
			}
		}
		unset($ARR_sorted_tests);unset($recent_exam_date);
		$returnArr = array();
		$arrTests = array();
		$upload_dir = rtrim(data_path(), '/');
		foreach($qry_tests as $testDname=>$q){
			$this->db_obj->qry = $q;
			$result = $this->db_obj->get_resultset_array();	
			//pre($result);die();
			$arrDates = array();
			foreach($result as $arr){
				$testDname1 = $testDname;
				$testId = $arr['testId'];
				$test_template_nm="";
				if($testDname=='Cell Count')		{$testDname1='CellCount';}
				else if($testDname=='External')		{$testDname1='discExternal';}
				else if($testDname=='Fundus')		{$testDname1='Disc';}
				else if($testDname=='HRT')			{$testDname1='NFA';}
				else if($testDname=='IOL Master')	{$testDname1='IOL_Master';}
				else if($testDname=='Laboratories')	{$testDname1='TestLabs';}
				else if($testDname=='Other')		{$testDname1='TestOther';}
				else if($testDname=='Template')		{					
					$testDname1='TemplateTests';					
					$test_template_nm = $this->getTempNM($arr['test_template_id']); //test template
				}
				
				
				$this->db_obj->qry = "SELECT test_id,scan_id,scan_or_upload,image_name,created_date,file_type,file_path as org_file_path,CONCAT('".$this->upDir."',file_path) AS file_path, DATE_FORMAT(modi_date,'".get_sql_date_format('','y')."') AS modi_date1 
									FROM ".constant('IMEDIC_SCAN_DB').".scans s 
									WHERE s.image_form='".$testDname1."' 
										  AND  s.test_id='".$arr['testId']."' 
										  AND s.status='0' 
										  AND s.patient_id='".$this->patient."' 
									ORDER BY site DESC";
				$arrImage = $this->db_obj->get_resultset_array();
				$extArr = array("jpg","jpeg","png","gif","tif","tiff");
				foreach($arrImage as $key=>$imageArr){
					$scanPth = str_ireplace(".PDF",".pdf",$imageArr['file_path']);
					$scanPthOrg = str_ireplace(".PDF",".pdf",$imageArr['org_file_path']);
					$fileInfoArr 	= pathinfo($scanPth);
					$pdf_dir_new  	= $fileInfoArr['dirname'];
					if($imageArr['file_type'] == "application/pdf" || $imageArr['file_type'] == "pdf" || in_array(strtolower($fileInfoArr['extension']),$extArr)){
						$link_file_path = $upload_dir.$scanPth;
						$imgSrc_file 	=  rtrim(data_path(), '/').$scanPthOrg;
						if(file_exists($imgSrc_file)){
							$pdf_info = pathinfo($imgSrc_file);
							$pdf_basename 	= $pdf_info['basename'];
							$pdf_dir	  	= $pdf_info['dirname'];
							$pdf_name	  	= $pdf_info['filename'];
							$pdf_thumbnail_dest	= $pdf_dir."/thumbnail";
							$pdf_thumb_dest	= $pdf_dir."/thumb";
							$pdf_jpg_dest	= $pdf_dir."/".$pdf_name.".jpg";
							
							$pdf_jthumbnail_dest= $pdf_thumbnail_dest."/".$pdf_name.".jpg";
							$pdf_jthumb_dest= $pdf_thumb_dest."/".$pdf_name.".jpg";
							
							if(is_dir($pdf_thumb_dest) == false){
								mkdir($pdf_thumb_dest, 0777, true);
							}
							if(is_dir($pdf_thumbnail_dest) == false){
								mkdir($pdf_thumbnail_dest, 0777, true);
							}
							$source = realpath($pdf_dir."/".$pdf_basename).'[0]';
							
							$exe_path 		= $GLOBALS['IMAGE_MAGIC_PATH'];
							if(!empty($exe_path)){$exe_path .= "/";}else{$exe_path='';}
							if (!file_exists($pdf_jpg_dest) && constant("STOP_CONVERT_COMMAND")!="YES"){
								//exec($exe_path.'convert -density 300 -background white -flatten -trim "'.$source.'" -strip -quality 100 -interlace line -colorspace RGB -resize 1500 "'.$pdf_jpg_dest.'"', $output, $return_var);
								exec($exe_path.'convert -density 300 -flatten "'.$source.'" -quality 95 -thumbnail 1500 -colorspace RGB -unsharp 1.50.0+1.5+0.020  "'.$pdf_jpg_dest.'"');
							}else if(!file_exists($pdf_jpg_dest)){
								$pdf_jpg_dest = $GLOBALS['webroot'].'/library/images/test_pdf_Icon.png';
							}
							
							if (!file_exists($pdf_jthumbnail_dest) && constant("STOP_CONVERT_COMMAND")!="YES"){
								//exec($exe_path.'convert -density 300 -background white -flatten -trim "'.$source.'" -strip -quality 95 -interlace line -colorspace RGB -resize 500 "'.$pdf_jthumbnail_dest.'"', $output, $return_var);
								exec($exe_path.'convert -flatten "'.$source.'" -quality 95 -thumbnail 78 -colorspace RGB -unsharp 1.50.0+1.5+0.020  "'.$pdf_jthumbnail_dest.'"');
							}else if(!file_exists($pdf_jthumbnail_dest)){
								$pdf_jthumbnail_dest = $GLOBALS['webroot'].'/library/images/test_pdf_Icon.png';
							}
							
							if (!file_exists($pdf_jthumb_dest) && constant("STOP_CONVERT_COMMAND")!="YES"){
								//exec($exe_path.'convert -density 300 -background white -flatten -trim "'.$source.'" -strip -quality 95 -interlace line -colorspace RGB -resize 500 "'.$pdf_jthumb_dest.'"', $output, $return_var);
								exec($exe_path.'convert -flatten "'.$source.'" -quality 95 -thumbnail 500 -colorspace RGB -unsharp 1.50.0+1.5+0.020  "'.$pdf_jthumb_dest.'"');
							}else if(!file_exists($pdf_jthumb_dest)){
								$pdf_jthumb_dest = $GLOBALS['webroot'].'/library/images/test_pdf_Icon.png';
							}

						}
						$arrImage[$key]['file_path'] = substr($imageArr['file_path'],0,-4).'.jpg';//stristr($imageArr['file_path'],".pdf",true).".jpg";
						$pdf_jpg_thumb	= $pdf_dir_new."/thumbnail/".$pdf_name.".jpg";
						//$arrImage[$key]['file_path']	= str_ireplace("127.0.0.1","192.168.1.50",$arrImage[$key]['file_path']);
						//$pdf_jpg_thumb				= str_ireplace("127.0.0.1","192.168.1.50",$pdf_jpg_thumb);
						$arrImage[$key]['thumb'] = $pdf_jpg_thumb;
					}else{
						$imgSrc_file 	= $imageArr['file_path'];
						//echo "<br>";
						$img_info 		= pathinfo($imgSrc_file);//pre($img_info);
						$img_basename 	= $img_info['basename'];
						$img_dir	  	= $img_info['dirname'];
						$img_name	  	= $img_info['filename'];
						$img_jpg_dest	= $img_dir."/".$img_name.".".$img_info['extension'];
						$img_jpg_thumb	= $img_dir."/thumbnail/".$img_name.".".$img_info['extension'];
						$arrImage[$key]['thumb'] = $img_jpg_thumb;
					}
				}
				$arr['images'] = $arrImage;
			
				//$arrDates[$arr['examDate1']] = $arrImage;
				//if(count($arrImage)>0) {
					$ar_tmp = array("id"=>$testId,"date"=>$arr['examDate1'],"images"=>$arrImage);
					if(!empty($test_template_nm)){ $ar_tmp["template_name"]=$test_template_nm; }
					$arrDates[] = $ar_tmp;
				//}
				
			}
				if(count($arrDates)>0)
				$returnArr['tests'][$testDname] = $arrDates;
		}
		$returnArr['patient_data'] = $this->get_patient_data();
		return $returnArr;
	}
	
	// new function for app
	
	
	public function test_manager_app(){
		$arr_all_test = $this->arr_all_test;
		foreach($arr_all_test as $key => $this_test){ // BEGIN FOREACH
			$test_name = $this_test;
			$patientId = 'patient_id';
			if($test_name=='vf' || $test_name=='vf_gl' || $test_name=='pachy' || $test_name=='disc' || $test_name=='disc_external' || $test_name=='topography' || $test_name=='test_bscan' || $test_name == "test_other" || $test_name == "test_labs" || $test_name == "test_cellcnt"){
				$patientId = 'patientId';
			}
			$phyName = 'phyName';
			if($test_name=='ivfa' || $test_name=='icg'){
				$phyName = 'phy';
			}
			if(in_array($test_name,array('surgical_tbl','iol_master_tbl'))){
				$phyName = 'signedById';
			}
			$pkIdCol = $test_name."_id";
			$examDate = "examDate";
			if($test_name=='icg')					{$examDate 	= "exam_date";}
			else if($test_name=='ivfa')				{$pkIdCol 	= "vf_id";	$examDate = "exam_date";}
			else if($test_name=='disc_external')	{$pkIdCol 	= "disc_id";}
			else if($test_name=='topography')		{$pkIdCol 	= "topo_id";}
			else if($test_name=='test_gdx')			{$pkIdCol 	= "gdx_id";}
			else if($test_name=='surgical_tbl')		{$pkIdCol 	= "surgical_id";}
			else if($test_name=='iol_master_tbl')	{$pkIdCol 	= "iol_master_id";}
			
			//template/other--
			$str_temp_test_wh=""; $str_temp_test_get="";
			if($test_name=='test_other'){
				if($key=='Other'){
					$str_temp_test_wh = " AND test_template_id='0' ";
				}else if($key=='Template'){
					$str_temp_test_wh = " AND test_template_id > '0' ";
					$str_temp_test_get =" , ".$test_name.".test_template_id ";
				}
			}
			//template/other--
			
			$qry_tests[$key] = "SELECT '".$test_name."' AS test_name, ".$test_name.".".$pkIdCol." AS testId, DATE_FORMAT(".$test_name.".".$examDate.",'".get_sql_date_format('','y')."') AS examDate1, DATE_FORMAT(".$test_name.".".$examDate.",'%m-%d-%Y') AS examDate_app, ".$test_name.".ordrby, ".$test_name.".".$phyName." AS phyName, 
								pd.providerID, ".$test_name.".examTime ".$str_temp_test_get."								
								FROM ".$test_name." 
								JOIN patient_data pd ON (pd.id = ".$test_name.".".$patientId.") 
								WHERE ".$test_name.".del_status = '0' AND ".$test_name.".purged = '0' AND ".$patientId."='".$this->patient."' ".$str_temp_test_wh." 
								ORDER BY ".$examDate." DESC";
			$ARR_sorted_tests = array();
			$recent_exam_date = 0;
			foreach($qry_tests as $testDname=>$q){
				$temp_res = imw_query($q);
				$temp_rs = imw_fetch_assoc($temp_res);
				$str_examtime = str_replace('-','',$temp_rs['examTime']);
				$str_examtime = str_replace(':','',$str_examtime);
				$str_examtime = floatval(str_replace(' ','',$str_examtime));
				//echo intval($str_examtime).'<br>';
				//echo floatval($str_examtime).'<br>';
				if($str_examtime > $recent_exam_date){
					$recent_exam_date = $str_examtime;
					array_unshift($ARR_sorted_tests,array($testDname=>$q));
				}else{
					array_push($ARR_sorted_tests,array($testDname=>$q));
				}
			}
	
		}//---END FOREACH
		unset($qry_tests);
		$qry_tests = array();
		foreach($ARR_sorted_tests as $i=>$arr){
			foreach($arr as $t=>$q){
				$qry_tests[$t] = $q;
			}
		}
		unset($ARR_sorted_tests);unset($recent_exam_date);
		$returnArr = array();
		$arrTests = array();
		$upload_dir = rtrim(data_path(), '/');
		foreach($qry_tests as $testDname=>$q){
			$this->db_obj->qry = $q;
			$result = $this->db_obj->get_resultset_array();	
			//pre($result);die();
			
			$arrDates = array();
			foreach($result as $arr){
			
				$testDname1 = $testDname;
				$testId = $arr['testId'];
				
				$test_template_nm="";
				if($testDname=='Cell Count')		{$testDname1='CellCount';}
				else if($testDname=='External')		{$testDname1='discExternal';}
				else if($testDname=='Fundus')		{$testDname1='Disc';}
				else if($testDname=='HRT')			{$testDname1='NFA';}
				else if($testDname=='IOL Master')	{$testDname1='IOL_Master';}
				else if($testDname=='Laboratories')	{$testDname1='TestLabs';}
				else if($testDname=='Other')		{$testDname1='TestOther';}
				else if($testDname=='Template')		{					
					$testDname1='TemplateTests';					
					$test_template_nm = $this->getTempNM($arr['test_template_id']); //test template
				}
				
				
				$this->db_obj->qry = "SELECT image_form,test_id, file_type,CONCAT('".$this->upDir."',file_path) AS file_path, DATE_FORMAT(modi_date,'".get_sql_date_format('','y')."') AS modi_date1 ,DATE_FORMAT(modi_date,'%m-%d-%Y') AS modi_date1_app
									FROM ".constant('IMEDIC_SCAN_DB').".scans s 
									WHERE s.image_form='".$testDname1."' 
										  AND  s.test_id='".$arr['testId']."' 
										  AND s.status='0' 
										  AND s.patient_id='".$this->patient."' 
									ORDER BY site DESC";
				$arrImage = $this->db_obj->get_resultset_array();	
				
				foreach($arrImage as $key=>$imageArr){
					if($imageArr['file_type'] == "application/pdf" || $imageArr['file_type'] == "pdf"){
						$scanPth = str_replace(".PDF",".pdf",$imageArr['file_path']);
						$scanPthOrg = str_replace(".PDF",".pdf",$imageArr['org_file_path']);
						$link_file_path = $upload_dir.$scanPth;
						
						$imgSrc_file 	=  rtrim(data_path(), '/').$scanPthOrg;
						if(file_exists($imgSrc_file)){
							$pdf_info 		= pathinfo($scanPth);
							$pdf_basename 	= $pdf_info['basename'];
							$pdf_dir	  	= $pdf_info['dirname'];
							$pdf_name	  	= $pdf_info['filename'];
							$pdf_jpg_dest	= $pdf_dir."/".$pdf_name.".jpg";
							$source 		= realpath($pdf_dir."/".$pdf_basename).'[0]';
							$exe_path 		= $GLOBALS['IMAGE_MAGIC_PATH'];
							if (!file_exists($pdf_jpg_dest)){
								exec($exe_path.'convert -density 300 -trim "'.$source.'" -strip -quality 100 -interlace line -colorspace RGB -resize 1500 "'.$pdf_jpg_dest.'"', $output, $return_var);
							}
						}
						$arrImage[$key]['file_path'] = strstr($imageArr['file_path'],".pdf",true).".jpg";
						$pdf_jpg_thumb	= $pdf_dir."/thumbnail/".$pdf_name.".jpg";
						$arrImage[$key]['thumb'] = $pdf_jpg_thumb;
					}else{
						$imgSrc_file 	= $imageArr['file_path'];
						//echo "<br>";
						$img_info 		= pathinfo($imgSrc_file);//pre($img_info);
						$img_basename 	= $img_info['basename'];
						$img_dir	  	= $img_info['dirname'];
						$img_name	  	= $img_info['filename'];
						$img_jpg_dest	= $img_dir."/".$img_name.".".$img_info['extension'];
						$img_jpg_thumb	= $img_dir."/thumbnail/".$img_name.".".$img_info['extension'];
						$arrImage[$key]['thumb'] = $img_jpg_thumb;
					}
				}
				
				
				
		
				
				$arr['images'] = $arrImage;
				
				$i=0;
				foreach($arrImage as $key => $val){
					$arrImage[$i]['image_form'] = $testDname; 
					$get_size = getimagesize($val['file_path']);	
					$arrImage[$i]['width'] = $get_size[0];
					$arrImage[$i]['height'] = $get_size[1];
					$i++;
				}
				
			$get_size_static = getimagesize($GLOBALS['php_server']."/library/images/image.jpg");
			
			
				if(empty($arrImage)){ 
				
				$arrImage=array(
				array(
				"test_id" => $testId,
				"image_form"=>$testDname,
                "file_type" => "application/jpg",
                "file_path" => $GLOBALS['php_server']."/library/images/image.jpg",
				"modi_date1"=>$arr['examDate1'],
				"modi_date1_app"=>$arr['examDate_app'],
				
            "thumb" => $GLOBALS['php_server']."/library/images/image-thumb.jpg",
			"width" => $get_size_static[0],
			"height" => $get_size_static[1]
			));
			
				}
				
				
				//$arrDates[$arr['examDate1']] = $arrImage;
			$ar_tmp = array("id"=>$testId,"name"=>$testDname,"date"=>$arr['examDate1'],"images"=>$arrImage);
			
			
			
				if(!empty($test_template_nm)){ $ar_tmp["template_name"]=$test_template_nm; }
				$arrDates[] = $ar_tmp;
				
				
			}
			
				if(count($arrDates)>0)
				$returnArr['tests'][$testDname] = $arrDates;
				
		}
		
		//$returnArr['patient_data'] = $this->get_patient_data();
		return $returnArr;
	}
	
	function tests_summary(){
		
		//include_once($GLOBALS['incdir']."/printing/leftForms_pdf_print.php");	
		//include_once($GLOBALS['incdir']."/printing/patient_info.inc.php");	
		//include_once($GLOBALS['incdir']."/main/chartNotesPrinting.php");
		
		
		$style_css_1='<style>
					table{ width:100%;border-spacing:0;}
					
					td{
						font-size:14;
						font-weight:100;
						background-color:#FFFFFF;
						color:#000000;
						text-align:left;
						vertical-align:top;
						padding:0;
						border-spacing:0;	
						margin:0;
						word-wrap:break-word;
						font-family:verdana;
					}
					
					.pagebreak {page-break-before:always} 
					
					.text_b_w{
						font-size:14;
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
						font-size:14;
						font-weight:bold;
						color:#000000;
						background-color:#f3f3f3;
					}
					.tb_heading{
						font-size:14;
						font-weight:bold;
						color:#000;
						background-color:#C0C0C0;
						margin-top:10;
						padding:3px 0px 3px 0px;
						vertical-align:middle;
						width:100%;
					}
					.tb_headingHeader{
						font-size:14;
						font-weight:bold;
						color:#FFFFFF;
						background-color:#4684ab;
					}
					.text_lable{
						font-size:14;
						<!--background-color:#FFFFFF;-->
						color:#000;
						font-weight:bold;
					}
					.text_value{
						font-size:14;
						font-weight:100;
						color:#000;
						<!--background-color:#FFFFFF;-->
					}
					.text_blue{
						font-size:14;
						color:#0000CC;
						font-weight:bold;
					}
					.text_green{
						font-size:14;
						color:#006600;
						font-weight:bold;
					}
					
					.imgCon{width:325;height:auto;}
					.imgdraw{width:325;height:auto;}
					
					.lbl{width:10%;font-weight:bold; } 
					.sum{width:45%;} 
					
					.test td{border:1px solid red;}
					
					.conlen td { font-size:12; }
					
					#crgp td{ font-size:9; }
					
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
					</style>';
		
		$testnm=$_REQUEST["testnm"];//
		$testid=$_REQUEST["testid"];//
		$pid=$_REQUEST["patId"];
		$fid=$_REQUEST["form_id"];
		
		$arr_all_test = $this->arr_all_test;
		
		$str=array();
		
		ob_end_clean();
		
		//foreach($arr_all_test as $key => $this_test){ // BEGIN FOREACH
			//$test_name = $this_test;
			
			if(!empty($testnm) && !empty($testid)){
				$key=$testnm;
				$fid=0;
			}
			
			ob_start();
			echo $style_css_1;
			switch($key){				
				case "VF":
					print_vf($pid,$fid,array($testid));
				break;
				
				case "Ascan":
				case "A/Scan":
				
				print_ascan($pid,$fid,array($testid));
				break;
				
				case "Bscan":
				case "B-scan":
					print_bscan($pid,$fid,array($testid));
				break;
				case "Cell Count":
					print_cellcount($pid,$fid, array($testid));
				break;
				case "External":
				case "External / Anterior":
				
					print_external($pid,$fid, array($testid));
				break;
				case "Fundus":
					print_disc($pid,$fid, array($testid));
				break;
				case "GDX":
					print_gdx($pid,$fid, array($testid));
				break;
				case "HRT":
					print_hrt($pid,$fid, array($testid));
				break;
				case "ICG":
					print_icg($pid,$fid, array($testid));
				break;
				case "IOL Master":
					print_iol_master($pid,$fid, array($testid));
				break;
				case "IVFA":
					print_ivfa($pid,$fid, array($testid));
				break;
				case "Laboratories":				
					print_lab($pid,$fid, array($testid));
				break;
				case "OCT":
					print_oct($pid,$fid, array($testid));
				break;
				case "OCT-RNFL":
					print_oct_rnfl($pid,$fid, array($testid));
				break;
				case "Pacchy":
				case "Pachy":				
					print_pachy($pid,$fid, array($testid));
				break;
				case "Topogrphy":
				case "Topography":				
					print_topo($pid,$fid, array($testid));
				break;
				case "VF-GL":
					print_vf_gl_fun($pid,$fid, array($testid));
				break;
				case "Other":
				case "Test Other":				
					print_testother($pid,$fid, array($testid));
				break;
				
				default:
					//echo "bb";
				break;
			
			}
			
			$html = ob_get_contents();	
			
			//clear class and style
			if(!empty($html)){
				//*
				$html = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $html);
				$html = preg_replace('/(<[^>]+) class=".*?"/i', '$1', $html);
				$html = preg_replace('/(<[^>]+) width=".*?"/i', '$1', $html);
				$html = preg_replace('/(<[^>]+) height=".*?"/i', '$1', $html);
				//*
				$html = preg_replace('/(<[^>]+) cellpadding=".*?"/i', '$1', $html);
				$html = preg_replace('/(<[^>]+) cellspacing=".*?"/i', '$1', $html);
				$html = preg_replace('/(<[^>]+) align=".*?"/i', '$1', $html);
				$html = preg_replace('/(<[^>]+) border=".*?"/i', '$1', $html);
				//*/
			}
			
			
			die();
			//echo "<xmp>".$html."</xmp>";
			//echo "".$html."";
			//exit();
			
			$str[$key] = $html;
			ob_end_clean();
		//}
		
		return $str;
	
	}
	
			// New Function  for  app// 
	function tests_summary_app(){
		
		//include_once($GLOBALS['incdir']."/printing/leftForms_pdf_print.php");	(previous file to use function)
		//include_once($GLOBALS['incdir']."/printing/patient_info.inc.php");	
		//include_once($GLOBALS['incdir']."/main/chartNotesPrinting.php");
		//include_once("leftForms_app.php");  // new file created for app for following function 
		
		
		$testnm=$_REQUEST["testnm"];//
		$testid=$_REQUEST["testid"];//
		
		
		$str=array();
		
		 
			 if(!empty($testnm) && !empty($testid)){
				$key=$testnm;
				
			}
			
			switch($key){				
				case "VF":
					$str[$key] = print_vf_app($testid);
				break;
				
				case "Ascan":
				case "A/Scan":				
					$str[$key] = print_ascan_app($testid);
				break;
				case "Bscan":
				case "B-scan":
					$str[$key] = print_bscan_app($testid);
				break;
				case "Cell Count":
					$str[$key] = print_cellcount_app($testid);
				break;
				case "External":
				case "External / Anterior":
				
					$str[$key] = print_external_app($testid);
				break;
				case "Fundus":
					$str[$key] = print_disc_app($testid);
				break;
				case "GDX":
					$str[$key] = print_gdx_app($testid);
				break;
				case "HRT":
					$str[$key] = print_hrt_app($testid);
				break;
				case "ICG":
					$str[$key] = print_icg_app($testid);
				break;
				case "IOL Master":
					$str[$key] = print_iol_master_app($testid);
				break;
				case "IVFA":
					$str[$key] = print_ivfa_app($testid);
				break;
				case "Laboratories":				
					$str[$key] = print_lab_app($testid);
				break;
				case "OCT":
					$str[$key] = print_oct_app($testid);
				break;
				case "OCT-RNFL":
					$str[$key] = print_oct_rnfl_app($testid);
				break;
				case "Pacchy":
				case "Pachy":				
					$str[$key] = print_pachy_app($testid);
				break;
				case "Topogrphy":
				case "Topography":				
					$str[$key] = print_topo_app($testid);
				break;
				case "VF-GL":
					$str[$key] = print_vf_gl_fun_app($testid);
				break;
				case "Other":
				case "Test Other":				
					$str[$key] = print_testother_app($testid);
				break;
				
				default:
					//echo "bb";
				break;
			
			}
			
			return $str;
	
	}

	
}

?>