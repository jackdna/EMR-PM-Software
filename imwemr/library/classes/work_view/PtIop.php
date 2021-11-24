<?php

class PtIop{
	public $pid;
	public function __construct($pid){		
		$this->pid = $pid;
		
	}
	
	function getGraph(){
		$pId = $this->pid;
		$elem_opts=$_REQUEST["elem_opts"];
		
		$series = array();
		$seriesName = array();
		$axisName = array("Date", "IOP");
		$graphTitle = " IOP Values ";
		
		$seriesColor = array();
		$seriesTime = array();

		$arr_ta_od=$arr_ta_os=$arr_tp_od=$arr_tp_os=$arr_tx_od=$arr_tx_os=$arr_tt_od=$arr_tt_os=$arr_dates=array();
		$arr_ta_od_tm=$arr_ta_os_tm=$arr_tp_od_tm=$arr_tp_os_tm=$arr_tx_od_tm=$arr_tx_os_tm=$arr_tt_od_tm=$arr_tt_os_tm=array();
		
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
			

			$ta_od=$ta_os=$tp_od=$tp_os=$tx_od=$tx_os=$tt_od=$tt_os=0;
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
					$ta_tm=$arrMP["multiplePressuer".$indx2]["elem_appTime".$indx];
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
					$tp_tm=$arrMP["multiplePressuer".$indx2]["elem_puffTime".$indx];
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
					$tx_tm=$arrMP["multiplePressuer".$indx2]["elem_xTime".$indx];
					if(!empty($v_Od)){
						$tx_od=$v_Od;
					}
				
					if(!empty($v_Os)){
						$tx_os=$v_Os;
					}				
				}
				
				$v_Tt=$arrMP["multiplePressuer".$indx2]["elem_tt".$indx];
				if(!empty($v_Tt)){
					$v_Od=$arrMP["multiplePressuer".$indx2]["elem_tactTrgtOd".$indx];
					$v_Os=$arrMP["multiplePressuer".$indx2]["elem_tactTrgtOs".$indx];
					$tt_tm=$arrMP["multiplePressuer".$indx2]["elem_ttTime".$indx];
					if(!empty($v_Od)){
						$tt_od=wv_getNumber($v_Od);
					}
				
					if(!empty($v_Os)){
						$tt_os=wv_getNumber($v_Os);
					}				
				}
				
			}

			if(!empty($ta_od)||!empty($ta_os)||!empty($tp_od)||!empty($tp_os)||!empty($tx_od)||!empty($tx_os)||!empty($tt_od)||!empty($tt_os)){
				
				if(strpos($elem_opts,"TAOD")!==false || $elem_opts=="All"){
					$arr_ta_od[]=$ta_od; $arr_ta_od_tm[]=$ta_tm;
				}
				if(strpos($elem_opts,"TAOS")!==false || $elem_opts=="All"){
					$arr_ta_os[]=$ta_os; $arr_ta_os_tm[]=$ta_tm;
				}
				if(strpos($elem_opts,"TPOD")!==false || $elem_opts=="All"){
					$arr_tp_od[]=$tp_od; $arr_tp_od_tm[]=$tp_tm;
				}
				if(strpos($elem_opts,"TPOS")!==false || $elem_opts=="All"){
					$arr_tp_os[]=$tp_os; $arr_tp_os_tm[]=$tp_tm;
				}
				if(strpos($elem_opts,"TXOD")!==false || $elem_opts=="All"){
					$arr_tx_od[]=$tx_od; $arr_tx_od_tm[]=$tx_tm;
				}
				if(strpos($elem_opts,"TXOS")!==false || $elem_opts=="All"){
					$arr_tx_os[]=$tx_os; $arr_tx_os_tm[]=$tx_tm;
				}
				if(strpos($elem_opts,"TTOD")!==false || $elem_opts=="All"){
					$arr_tt_od[]=$tt_od;$arr_tt_od_tm[]=$tt_tm;
				}
				if(strpos($elem_opts,"TTOS")!==false || $elem_opts=="All"){
					$arr_tt_os[]=$tt_os;$arr_tt_os_tm[]=$tt_tm;
				}
				$arr_dates[]=wv_formatDate($dos);
			}
		}
		
		if(count($arr_ta_od)>0){
			$series[] = $arr_ta_od;
			$seriesName [] = "TA OD";
			$seriesColor [] = array(0,0,205);
			$ckd_taod="checked=\"checked\"";
			$seriesTime[] = $arr_ta_od_tm;
		}

		if(count($arr_ta_os)>0){
			$series[] = $arr_ta_os;
			$seriesName [] = "TA OS";
			$seriesColor [] = array(34,139,34);
			$ckd_taos="checked=\"checked\"";
			$seriesTime[] = $arr_ta_os_tm;
		}
		if(count($arr_tp_od)>0){
			$series[] = $arr_tp_od;
			$seriesName [] = "TP OD";
			$seriesColor [] = array(255,185,15);
			$ckd_tpod="checked=\"checked\"";
			$seriesTime[] = $arr_tp_od_tm;
		}
		if(count($arr_tp_os)>0){		
			$series[] = $arr_tp_os;
			$seriesName [] = "TP OS";
			$seriesColor [] = array(255,0,0);
			$ckd_tpos="checked=\"checked\"";
			$seriesTime[] = $arr_tp_os_tm;
		}
		if(count($arr_tx_od)>0){	
			$series[] = $arr_tx_od;
			$seriesName [] = "TX OD";
			$seriesColor [] = array(160,32,240);
			$ckd_txod="checked=\"checked\"";
			$seriesTime[] = $arr_tx_od_tm;
		}
		if(count($arr_tx_os)>0){		
			$series[] = $arr_tx_os;
			$seriesName [] = "TX OS";
			$seriesColor [] = array(30,144,255);
			$ckd_txos="checked=\"checked\"";
			$seriesTime[] = $arr_tx_os_tm;
		}
		if(count($arr_tt_od)>0){
			$series[] = $arr_tt_od;
			$seriesName [] = "TT OD";
			$seriesColor [] = array(130,56,140);
			$ckd_ttod="checked=\"checked\"";
			$seriesTime[] = $arr_tt_os_tm;
		}
		if(count($arr_tt_os)>0){
			$series[] = $arr_tt_os;
			$seriesName [] = "TT OS";
			$seriesColor [] = array(230,44,55);
			$ckd_ttos="checked=\"checked\"";
			$seriesTime[] = $arr_tt_os_tm;
		}
		
		if(count($series)>0){
			$series[] = $arr_dates;	//Dates			
			
			$len = count($series);		
			$absLabel = "Serie".$len;	
			
		}else{
			
			$msg='Graph can not created becuase of insufficient data.';
		}		
			
		
		if( $len > 0 ){			
			$line_chart_data=$this->line_chart($seriesName,$series, array(), $seriesTime );		
			$line_pay_graph_var_arr_js=json_encode($line_chart_data['line_pay_graph_var_detail']);
			$line_payment_tot_arr_js=json_encode($line_chart_data['line_payment_tot_detail']);
		}
			
			
		$ajax_arr['line_pay_graph_var_detail']=$line_pay_graph_var_arr_js;
		$ajax_arr['line_payment_tot_detail']=$line_payment_tot_arr_js;
		echo json_encode($ajax_arr);
	}
	
	function line_chart($graph_name,$graph_data,$graph_clr=array(),$graph_tm=array()){	
		$key_i=0;$kk=0;
		$mxln = count($graph_name);
		foreach($graph_data[$mxln] as $key=>$val){
			$line_payment_tot_arr[$key]["category"]=$val;
		}

		foreach($graph_data as $key=>$val){
			if($key!=$mxln){	
				$key_i++;
				$title="";
				$title=$graph_name[$key];
				
				
				$tmp_ar=array("alphaField"=> "C",
					"balloonText"=> "[[title]] of [[category]] [[ioptm-$key_i]]: [[value]]",
					"bullet"=> "round",
					"bulletField"=> "C",
					"bulletSizeField"=> "C",
					"closeField"=> "C",
					"colorField"=> "C",
					"customBulletField"=> "C",
					"dashLengthField"=> "C",
					"descriptionField"=> "C",
					"errorField"=> "C",
					"fillColorsField"=> "C",
					"gapField"=> "C",
					"highField"=> "C",
					"id"=> "AmGraph-$key_i",
					"labelColorField"=> "C",
					"lineColorField"=> "C",
					"lowField"=> "C",
					"openField"=> "C",
					"patternField"=> "C",
					"title"=> $title,
					"valueField"=> "column-$key_i",
					"xField"=> "C",
					"yField"=> "C");
				
				if(count($graph_clr)>0){
					$tmp_clr=$graph_clr[$key];
					if(!empty($tmp_clr)){
						$tmp_ar["lineColor"] = $tmp_clr;
					}
				}
				
				$line_pay_graph_var_arr[] = $tmp_ar;	
				
				foreach($graph_data[$key] as $key2=>$val2){
					if($graph_data[$key][$key2]>0){	
						$line_payment_tot_arr[$key2]["column-".$key_i]=$graph_data[$key][$key2];
						if(!empty($graph_tm[$key][$key2])){
							$line_payment_tot_arr[$key2]["ioptm-".$key_i]=$graph_tm[$key][$key2];
						}
					}
					$kk++;
				}
			}
		}
		
		$return_arr['line_payment_tot_detail']=$line_payment_tot_arr;
		$return_arr['line_pay_graph_var_detail']=$line_pay_graph_var_arr;
		return $return_arr;
	}
	
}

?>