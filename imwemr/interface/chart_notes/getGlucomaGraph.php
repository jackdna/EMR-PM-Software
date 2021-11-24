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
/*
File: getGlucomaGraph.php
Purpose: This file provide Graph function for IOP , GFS values.
Access Type : Direct
*/
?>
<?php
 /*
     Example2 : A cubic curve graph
 */
require_once('../../config/globals.php');
 // Standard inclusions  
 $library_path = $GLOBALS['srcdir'].'';
 require($library_path."/amcharts/glaucoma_graph/graphs/pChart/pData.class");
 require($library_path."/amcharts/glaucoma_graph/graphs/pChart/pChart.class");
 
 include_once($library_path.'/classes/common_function.php');
 include_once($library_path.'/classes/work_view/wv_functions.php');
	
	$ttf = $library_path."/amcharts/glaucoma_graph/graphs/Fonts/tahoma1.ttf";
	
	function msgImg($msg){		
		
		// Create a blank image and add some text
		$im = imagecreatetruecolor(500, 100);
		$img_color = imagecolorallocate($im, 255, 255, 255);
		$text_color = imagecolorallocate($im, 0, 0, 255);
		imagefill($im, 0, 0, $img_color);
		imagestring($im, 5, 0, 30, $msg, $text_color);
		
		// Set the content type header - in this case image/jpeg
		header('Content-Type: image/jpeg');

		// Output the image
		imagejpeg($im,'',90);

		// Free up memory
		imagedestroy($im);		
		
	}	
	
	
	if(isset($_GET["operation"]) && $_GET["operation"] == "IOPGraphs"){
		//--
		//include_once(dirname(__FILE__)."/../main/main_functions.php");
		$pId = $_SESSION["patient"];
		$elem_opts=base64_decode(xss_rem($_GET["elem_opts"]));
		
		$series = array();
		$seriesName = array();
		$axisName = array("Date", "IOP");
		$graphTitle = " IOP Values ";
		
		$seriesColor = array();

		$arr_ta_od=$arr_ta_os=$arr_tp_od=$arr_tp_os=$arr_tx_od=$arr_tx_os=$arr_dates=array();
		
		$sql = "SELECT ".
				"c2.puff,c2.puff_od,c2.puff_os_1, ".
				"c2.applanation,c2.app_od,c2.app_os_1, ".
				"c2.tx,c2.tx_od,c2.tx_os,c2.fieldCount, ".
				"c2.multiple_pressure, ".
				"c2.iop_id, ".
				//"c3.date_of_service, ".
				"c1.date_of_service, ".
				"c1.create_dt,c1.update_date, c1.id ".
			   "FROM chart_master_table c1 ".
			   "LEFT JOIN chart_iop c2 ON c2.form_id=c1.id ".
			  // "LEFT JOIN chart_left_cc_history c3 ON c3.form_id=c1.id ".	
			   "WHERE c1.patient_id='".$pId."' ".
			   //"ORDER BY IFNULL(c3.date_of_service,c1.create_dt), c1.id ";
			   "ORDER BY c1.date_of_service, c1.id ";
		
		$rez=sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			
			if(empty($row["multiple_pressure"])){
				$arrMP["multiplePressuer"]["elem_applanation"] = $row["applanation"];
				$arrMP["multiplePressuer"]["elem_appOd"] = $row["app_od"];
				$arrMP["multiplePressuer"]["elem_appOs"] = $row["app_os_1"];
				
				$arrMP["multiplePressuer"]["elem_puff"] = $row["puff"];
				$arrMP["multiplePressuer"]["elem_puffOd"] = $row["puff_od"];
				$arrMP["multiplePressuer"]["elem_puffOs"] = $row["puff_os_1"];

				$arrMP["multiplePressuer"]["elem_tx"] = $row["tx"];
				$arrMP["multiplePressuer"]["elem_appTrgtOd"] = $row["tx_od"];
				$arrMP["multiplePressuer"]["elem_appTrgtOs"] = $row["tx_os"];		
				$fieldCount="0";
			}else{
				$arrMP=unserialize($row["multiple_pressure"]);
				$fieldCount=$row["fieldCount"];
			}
			
			//echo "<pre>";
			//print_r($arrMP);
			//echo "</pre>";

			$ta_od=$ta_os=$tp_od=$tp_os=$tx_od=$tx_os=0;
			$dos=$row["date_of_service"];
			if(empty($dos))$dos=$row["create_dt"];
			if(empty($dos))$dos=$row["update_date"];

			//Loop values
			$arrFC = explode(",",$fieldCount);
			$lenFC = count($arrFC);
			
			for($cnt=0,$j=1;$j<=$lenFC;$j++,$cnt++){
				
				$indx=$indx2="";
				if($j>1){
					$indx = $arrFC[$cnt];
					$indx2=$j;
				}
				

				$v_Ta=$arrMP["multiplePressuer".$indx2]["elem_applanation".$indx];				
				if(!empty($v_Ta)){
					$v_Od=$arrMP["multiplePressuer".$indx2]["elem_appOd".$indx];
					$v_Os=$arrMP["multiplePressuer".$indx2]["elem_appOs".$indx];
					
					if(!empty($v_Od)){
						$ta_od=$v_Od;
					}
				
					if(!empty($v_Os)){
						$ta_os=$v_Os;
					}
				}
				
				$v_Tp=$arrMP["multiplePressuer".$indx2]["elem_puff".$indx];
				if(!empty($v_Tp)){
					$v_Od=$arrMP["multiplePressuer".$indx2]["elem_puffOd".$indx];
					$v_Os=$arrMP["multiplePressuer".$indx2]["elem_puffOs".$indx];

					if(!empty($v_Od)){
						$tp_od=$v_Od;
					}
				
					if(!empty($v_Os)){
						$tp_os=$v_Os;
					}					
				}
				
				$v_Tx=$arrMP["multiplePressuer".$indx2]["elem_tx".$indx];
				if(!empty($v_Tx)){
					$v_Od=$arrMP["multiplePressuer".$indx2]["elem_appTrgtOd".$indx];
					$v_Os=$arrMP["multiplePressuer".$indx2]["elem_appTrgtOs".$indx];

					if(!empty($v_Od)){
						$tx_od=$v_Od;
					}
				
					if(!empty($v_Os)){
						$tx_os=$v_Os;
					}				
				}
				
			}

			if(!empty($ta_od)||!empty($ta_os)||!empty($tp_od)||!empty($tp_os)||!empty($tx_od)||!empty($tx_os)){
				
				if(strpos($elem_opts,"TAOD")!==false || $elem_opts=="All"){
					$arr_ta_od[]=$ta_od;
				}
				if(strpos($elem_opts,"TAOS")!==false || $elem_opts=="All"){
					$arr_ta_os[]=$ta_os;
				}
				if(strpos($elem_opts,"TPOD")!==false || $elem_opts=="All"){
					$arr_tp_od[]=$tp_od;
				}
				if(strpos($elem_opts,"TPOS")!==false || $elem_opts=="All"){
					$arr_tp_os[]=$tp_os;
				}
				if(strpos($elem_opts,"TXOD")!==false || $elem_opts=="All"){
					$arr_tx_od[]=$tx_od;
				}
				if(strpos($elem_opts,"TXOS")!==false || $elem_opts=="All"){
					$arr_tx_os[]=$tx_os;
				}
				$arr_dates[] = wv_formatDate($dos);
			}
		}
		
		if(count($arr_ta_od)>0){
			$series[] = $arr_ta_od;
			$seriesName [] = "TA OD";
			$seriesColor [] = array(0,0,205);
			$ckd_taod="checked=\"checked\"";
		}

		if(count($arr_ta_os)>0){
			$series[] = $arr_ta_os;
			$seriesName [] = "TA OS";
			$seriesColor [] = array(34,139,34);
			$ckd_taos="checked=\"checked\"";
		}
		if(count($arr_tp_od)>0){
			$series[] = $arr_tp_od;
			$seriesName [] = "TP OD";
			$seriesColor [] = array(255,185,15);
			$ckd_tpod="checked=\"checked\"";
		}
		if(count($arr_tp_os)>0){		
			$series[] = $arr_tp_os;
			$seriesName [] = "TP OS";
			$seriesColor [] = array(255,0,0);
			$ckd_tpos="checked=\"checked\"";
		}
		if(count($arr_tx_od)>0){	
			$series[] = $arr_tx_od;
			$seriesName [] = "TX OD";
			$seriesColor [] = array(160,32,240);
			$ckd_txod="checked=\"checked\"";
		}
		if(count($arr_tx_os)>0){		
			$series[] = $arr_tx_os;
			$seriesName [] = "TX OS";
			$seriesColor [] = array(30,144,255);
			$ckd_txos="checked=\"checked\"";
		}
		
		if(count($series)>0){
			$series[] = $arr_dates;	//Dates			
			
			$len = count($series);		
			$absLabel = "Serie".$len;	
			
		}else{
			
			$msg='Graph can not created becuase of insufficient data.';
			msgImg($msg);
			exit();
			//--
			
		}		
		
		//--
	}else{
	
	
	//--
	
	$temp_series = xss_rem($_GET["series"]);
	$series = (isset($temp_series) && !empty($temp_series)) ? unserialize(base64_decode($temp_series)) : "" ;	
	$len = count($series);
	$temp_seriesName = xss_rem($_GET["seriesName"]);
	$seriesName = (isset($temp_seriesName) && !empty($temp_seriesName)) ? unserialize(base64_decode($temp_seriesName)) : "" ;
	$temp_absLabel = xss_rem($_GET["absLabel"]);
	$absLabel = (isset($temp_absLabel) && !empty($temp_absLabel)) ? $temp_absLabel:"Serie".$len;
	$temp_axisName = xss_rem($_GET["axisName"]);
	$axisName = (isset($temp_axisName) && !empty($temp_axisName)) ? unserialize(base64_decode($temp_axisName)) : "";
	$temp_graphTitle = xss_rem($_GET["graphTitle"]);
	$graphTitle = (isset($temp_graphTitle) && !empty($temp_graphTitle)) ? $temp_graphTitle : "";
	$temp_seriesColor = xss_rem($_GET["seriesColor"]);
	$seriesColor = (isset($temp_seriesColor) && !empty($temp_seriesColor)) ? unserialize(base64_decode($temp_seriesColor)) : array();	
	
	}
	
	if( $len > 0 ){
	 // Dataset definition 
	 $DataSet = new pData;
	 
	 $odIndxId = "";
	 $osIndxId = "";
	 foreach( $series as $key => $val ){
		$DataSet->AddPoint($val,"Serie".($key+1));
		if(!empty($seriesName[$key])){
			$DataSet->SetSerieName($seriesName[$key],"Serie".($key+1));
		}
	 }	 
	 
	 $DataSet->AddAllSeries();	 
	 $DataSet->SetAbsciseLabelSerie($absLabel);
	 
	 if( count($axisName) > 0 ){
		$DataSet->SetYAxisName($axisName[1]);
		$DataSet->SetXAxisName($axisName[0]);
	 }	 
	 
	 // Initialise the graph
	 $width = 600;
	 $height = 230;
	 
	 //Increase Width
	 $sLen = count($series[0]);
	 if($sLen>20){
		$rLen = $sLen-20;
		$width = $width + ($rLen*20);
	 }
	 
	 $rgb1 = array(230,230,230);
	 $rgb2 = array(240,240,240);
	 $rgb3 = array(255,255,255);
	 $rgb4 = array(150,150,150);
	 $rgb5 = array(143,55,72);
	 $rgb6 = array(50,50,50);
	 
	 
	 $Test = new pChart($width,$height);	 
	 $Test->setFontProperties($ttf,8);
	 $Test->setGraphArea(60,30,$width-65,$height-40);
	 $Test->drawFilledRoundedRectangle(7,7,$width-7,$height-7,5,$rgb2[0],$rgb2[1],$rgb2[2]);
	 $Test->drawRoundedRectangle(5,5,$width-5,$height-5,5,$rgb1[0],$rgb1[1],$rgb1[2]);
	 $Test->drawGraphArea($rgb3[0],$rgb3[1],$rgb3[2],TRUE);
	 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,$rgb4[0],$rgb4[1],$rgb4[2],TRUE,30,2);
	 $Test->drawGrid(4,TRUE,$rgb1[0],$rgb1[1],$rgb1[2],50);
	 
	 // Set Color Palette
	 if( is_array($seriesColor) && (count($seriesColor) > 0) ){
		 foreach( $seriesColor as $key => $val ){
			$Test->setColorPalette($key,$val[0],$val[1],$val[2]);
		 }	 
	 }
	 
	 // Draw the 0 line
	 $Test->setFontProperties($ttf,6);
	 $Test->drawTreshold(0,$rgb5[0],$rgb5[1],$rgb5[2],TRUE,TRUE);
	
	 // Draw the cubic curve graph
	 $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
	 $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),2,1,$rgb3[0],$rgb3[1],$rgb3[2]);

	 // Finish the graph
	 $DataSet->RemoveSerie($absLabel);
	 $Test->setFontProperties($ttf,8);
	 $Test->drawLegend($width-60,20,$DataSet->GetDataDescription(),$rgb3[0],$rgb3[1],$rgb3[2]);
	 if(!empty($graphTitle)){
		$Test->setFontProperties($ttf,10);
		$Test->drawTitle(50,22,$graphTitle,$rgb6[0],$rgb6[1],$rgb6[2],$width-100);
	 }
	 $Test->Stroke();
	}
 
?>