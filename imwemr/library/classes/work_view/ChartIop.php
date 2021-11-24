<?php

class ChartIop{
	public $pid, $fid;
	public function __construct($pid, $fid){
		$this->pid = $pid;
		$this->fid = $fid;
	}

	function valNewRecordIop( $sel=" * ",$LF="0",$dt="")
	{
		global $cryfwd_form_id;
		$patient_id = $this->pid;
		$LF = ($LF == "1") ? "AND chart_master_table.finalize = '1' " : "";

		if(!empty($dt)&&$dt!="0000-00-00"){
			$tmp="";
			if(!empty($this->fid)){
				$dt=" AND (chart_master_table.date_of_service <=  '".$dt."' AND chart_master_table.id < '".$this->fid."' ) ";
			}else{
				$dt = " AND (chart_master_table.date_of_service <  '".$dt."' ) ";
			}
		}else{
			$dt ="";
		}

		//DOS BASED carry forward
		if(!empty($cryfwd_form_id) ){
			$dt = " AND (chart_master_table.id <=  '".$cryfwd_form_id."' ) ";
		}

		$qry = "SELECT ".$sel." FROM chart_master_table ".
			 "INNER JOIN chart_iop ON chart_master_table.id = chart_iop.form_id AND chart_iop.purged='0' ".
			 "WHERE chart_master_table.patient_id = '".$patient_id."' AND chart_master_table.delete_status='0' ".
			 "AND chart_master_table.purge_status='0' ".
			 "AND chart_master_table.record_validity = '1' ".
			 $LF.
			 $dt.
			 "ORDER BY update_date DESC LIMIT 0,1 ";
		$res = imw_query($qry);
		return $res;
	}

	function valPrevFinalRecordIop( $sel=" * ")
	{
		$patient_id = $this->pid;
		$formId = $this->fid;
		global $cryfwd_form_id;
		//DOS BASED carry forward
		if(!empty($cryfwd_form_id) ){
			$dt = " AND (chart_master_table.id <=  '".$cryfwd_form_id."' ) ";
		}

		$qry = "SELECT ".$sel." FROM chart_master_table ".
			 "INNER JOIN chart_iop ON chart_master_table.id = chart_iop.form_id AND chart_iop.purged='0'  ".
			 "WHERE chart_master_table.patient_id = '$patient_id' AND chart_master_table.delete_status='0' AND chart_master_table.purge_status='0' ".
			 "AND chart_master_table.record_validity = '1' ".
			 "AND chart_master_table.finalize = '1' ".
			 "AND chart_master_table.id != '$formId' ".$dt.
			 "ORDER BY update_date DESC LIMIT 0,1 ";
		$res = imw_query($qry);
		return $res;
	}

	function getIopTrgtDef($strict=0){
		$pId=$this->pid;
		$formId=$this->fid;
		$sql = "SELECT * FROM tbl_def_val WHERE ptId='".$pId."' AND form_id='".$formId."' ";
		$row = sqlQuery($sql);
		if( ($row == false) && ($formId != 0) && ($strict == 0) ){
			$sql = "SELECT * FROM tbl_def_val WHERE ptId='".$pId."' AND form_id='0' ";
			$row = sqlQuery($sql);
		}
		return $row;
	}

	function getGlucomaTargetIop(){
		$patient_id = $this->pid;
		$tOd=$tOs="";
		$sql = "SELECT iopTrgtOd,iopTrgtOs FROM glucoma_main WHERE patientId='".$patient_id."' ORDER BY glucomaId DESC ";
		$row = sqlQuery($sql);
		if($row != false){
			$tOd = $row["iopTrgtOd"];
			$tOs = $row["iopTrgtOs"];
		}
		return array($tOd,$tOs);
	}

	function getPrvIOPVals(){
		$rowU = array();
		$sel = "chart_iop.applanation, chart_iop.app_od, chart_iop.app_os_1, chart_iop.app_time, ".
			 "chart_iop.puff, chart_iop.puff_od, chart_iop.puff_os_1, chart_iop.puff_time, ".
			 "chart_iop.tx, chart_iop.tx_od, chart_iop.tx_os, chart_iop.tx_time, ".
			 "chart_iop.multiple_pressure,chart_iop.fieldCount ";
		$res = $this->valPrevFinalRecordIop($sel);
		if(imw_num_rows($res)>0){
			$row1 = imw_fetch_array($res);
			$rowU = unserialize($row1['multiple_pressure']);

			//Check for past data
			if(empty($rowU["multiplePressuer"]["elem_applanation"]) || (empty($rowU["multiplePressuer"]["elem_appOd"]) && empty($rowU["multiplePressuer"]["elem_appOs"])) ){
				$rowU["multiplePressuer"]["elem_applanation"] = $row1["applanation"];
				$rowU["multiplePressuer"]["elem_appOd"] = $row1["app_od"];
				$rowU["multiplePressuer"]["elem_appOs"] = $row1["app_os_1"];
				$rowU["multiplePressuer"]["elem_appTime"] = $row1["app_time"];
				}

			if(empty($rowU["multiplePressuer"]["elem_puff"]) || (empty($rowU["multiplePressuer"]["elem_puffOd"]) && empty($rowU["multiplePressuer"]["elem_puffOs"]))){
				$rowU["multiplePressuer"]["elem_puff"] = $row1["puff"];
				$rowU["multiplePressuer"]["elem_puffOd"] = $row1["puff_od"];
				$rowU["multiplePressuer"]["elem_puffOs"] = $row1["puff_os_1"];
				$rowU["multiplePressuer"]["elem_puffTime"] = $row1["puff_time"];
				}

			if(empty($rowU["multiplePressuer"]["elem_tx"]) || (empty($rowU["multiplePressuer"]["elem_appTrgtOd"]) && empty($rowU["multiplePressuer"]["elem_appTrgtOs"]))){
				$rowU["multiplePressuer"]["elem_tx"] = $row1["tx"];
				$rowU["multiplePressuer"]["elem_appTrgtOd"] = $row1["tx_od"];
				$rowU["multiplePressuer"]["elem_appTrgtOs"] = $row1["tx_os"];
				$rowU["multiplePressuer"]["elem_xTime"] = $row1["tx_time"];
			}

			$tmp_multiple_pressure = serialize($rowU);
			$rowU = $this->reFormatPressureArr($tmp_multiple_pressure);

		}

		return $rowU;
	}

	function getPrsrSum($chk,$od,$tod, $tm=""){
		$chk = trim($chk);
		$od = trim($od);
		$ods=$od;
		if(!empty($tm)){ $ods=$od." ".$tm;  }
		$strOd = $strOs = "";
		if(empty($tod)) $tod = 21;
		//if(empty($tos)) $tos = 21;
		if($chk!=""){
			if($od!=""){
				$strOd .= ($od>$tod) ? "<font color=\"red\">".$ods."</font>;&nbsp;&nbsp;" : $ods.";&nbsp;&nbsp;" ;
			}
		}
		return $strOd;
	}

	function reFormatPressureArr($multiple_pressure){
			$arrRet=array();

			$fldNamesOrg = array(
        0=>"elem_applanation",
        1=>"elem_puff",
        2=>"elem_tx",

        3=>"elem_appOd",
        4=>"elem_appOs",
        5=>"elem_appTime",
        6=>"elem_descTa",

        7=>"elem_puffOd",
        8=>"elem_puffOs",
        9=>"elem_puffTime",
        10=>"elem_descTp",

        11=>"elem_appTrgtOd",
        12=>"elem_appTrgtOs",
        13=>"elem_xTime",
        14=>"elem_descTx",

        15=>"elem_tt",
        16=>"elem_tactTrgtOd",
        17=>"elem_tactTrgtOs",
        18=>"elem_ttTime",
        19=>"elem_descTt",

        20=>"elem_appMethod",
        21=>"elem_puffMethod",
        22=>"elem_tactMethod",
        23=>"elem_ttMethod"

      );

			$inc_org = 0;
			$mulPressureArr = unserialize($multiple_pressure);
			$len =  count($mulPressureArr);

			if($len>0){
				if(isset($mulPressureArr['multiplePressuer'])	){

					do
					{
			        $flg_IOP=0;
			        $inc_dv=$inc=$inc_org+1;
			        if($inc=="1"){$inc="";}
							//
			        $fldNames = $fldNamesOrg;
			        if(count($mulPressureArr['multiplePressuer'.$inc]) > 0){
			          $tmp=array();
			          foreach($fldNames as $fldNames_k => $fldNames_v){
			            if(!empty($inc)){$inc_tmp = $inc-1;}else{$inc_tmp = $inc;}
			            $tmp[$fldNames_k] = $fldNames_v.$inc_tmp;
			          }
			          $fldNames=$tmp;
			        }

							$t = trim($mulPressureArr['multiplePressuer'.$inc][$fldNames[3]].$mulPressureArr['multiplePressuer'.$inc][$fldNames[4]].
										$mulPressureArr['multiplePressuer'.$inc][$fldNames[5]].$mulPressureArr['multiplePressuer'.$inc][$fldNames[6]]);
							if(!empty($t))
							{
								$tmp_mthd = $mulPressureArr['multiplePressuer'.$inc][$fldNames[20]];
								if(empty($tmp_mthd)){ $tmp_mthd = "Applanation" ; }
								$tmpArr = array();
								$tmpArr["mthd"] = $tmp_mthd;
								$tmpArr["od"] = $mulPressureArr['multiplePressuer'.$inc][$fldNames[3]];
								$tmpArr["os"] = $mulPressureArr['multiplePressuer'.$inc][$fldNames[4]];
								$tmpArr["tm"] = $mulPressureArr['multiplePressuer'.$inc][$fldNames[5]];
								$tmpArr["dsc"] = $mulPressureArr['multiplePressuer'.$inc][$fldNames[6]];
								$arrRet[] = $tmpArr;
							}

							$t = trim($mulPressureArr['multiplePressuer'.$inc][$fldNames[7]].$mulPressureArr['multiplePressuer'.$inc][$fldNames[8]].
										$mulPressureArr['multiplePressuer'.$inc][$fldNames[9]].$mulPressureArr['multiplePressuer'.$inc][$fldNames[10]]);
							if(!empty($t))
							{
								$tmp_mthd = $mulPressureArr['multiplePressuer'.$inc][$fldNames[21]];
								if(empty($tmp_mthd)){ $tmp_mthd = "Puff" ; }
								$tmpArr = array();
								$tmpArr["mthd"] = $tmp_mthd;
								$tmpArr["od"] = $mulPressureArr['multiplePressuer'.$inc][$fldNames[7]];
								$tmpArr["os"] = $mulPressureArr['multiplePressuer'.$inc][$fldNames[8]];
								$tmpArr["tm"] = $mulPressureArr['multiplePressuer'.$inc][$fldNames[9]];
								$tmpArr["dsc"] = $mulPressureArr['multiplePressuer'.$inc][$fldNames[10]];
								$arrRet[] = $tmpArr;
							}

							$t = trim($mulPressureArr['multiplePressuer'.$inc][$fldNames[11]].$mulPressureArr['multiplePressuer'.$inc][$fldNames[12]].
										$mulPressureArr['multiplePressuer'.$inc][$fldNames[13]].$mulPressureArr['multiplePressuer'.$inc][$fldNames[14]]);
							if(!empty($t))
							{
								$tmp_mthd = $mulPressureArr['multiplePressuer'.$inc][$fldNames[22]];
								if(empty($tmp_mthd)){ $tmp_mthd = "Tactile" ; }
								$tmpArr = array();
								$tmpArr["mthd"] = $tmp_mthd;
								$tmpArr["od"] = $mulPressureArr['multiplePressuer'.$inc][$fldNames[11]];
								$tmpArr["os"] = $mulPressureArr['multiplePressuer'.$inc][$fldNames[12]];
								$tmpArr["tm"] = $mulPressureArr['multiplePressuer'.$inc][$fldNames[13]];
								$tmpArr["dsc"] = $mulPressureArr['multiplePressuer'.$inc][$fldNames[14]];
								$arrRet[] = $tmpArr;
							}

							$t = trim($mulPressureArr['multiplePressuer'.$inc][$fldNames[16]].$mulPressureArr['multiplePressuer'.$inc][$fldNames[17]].
										$mulPressureArr['multiplePressuer'.$inc][$fldNames[18]].$mulPressureArr['multiplePressuer'.$inc][$fldNames[19]]);
							if(!empty($t))
							{
								$tmp_mthd = $mulPressureArr['multiplePressuer'.$inc][$fldNames[23]];
								if(empty($tmp_mthd)){ $tmp_mthd = "TX" ; }
								$tmpArr = array();
								$tmpArr["mthd"] = $tmp_mthd;
								$tmpArr["od"] = $mulPressureArr['multiplePressuer'.$inc][$fldNames[16]];
								$tmpArr["os"] = $mulPressureArr['multiplePressuer'.$inc][$fldNames[17]];
								$tmpArr["tm"] = $mulPressureArr['multiplePressuer'.$inc][$fldNames[18]];
								$tmpArr["dsc"] = $mulPressureArr['multiplePressuer'.$inc][$fldNames[19]];
								$arrRet[] = $tmpArr;
							}

							$inc_org++;

				      if($inc_org<=$len){ $flg_IOP=1; } //run again

				      if($inc_org>50){$flg_IOP=0;}//stop

			    }while($flg_IOP);

				}
			}

			return $arrRet;
	}

}

?>
