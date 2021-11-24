<?php
class ChartMemo extends ChartNote{	
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);		
	}
	
	function getMemoTable($arrVal, $addIndx,$dfdt){	
		
		$arrMrPersonnal=array();
		$ouser = new User();
		$arrMrPersonnal = $ouser->getUserArr("","cn_all");
		
		$nameMemoDate = "elem_memoDate".$addIndx;
		$nameMemoProvider = "elem_memoProvider".$addIndx;
		$nameMemoText = "elem_memoText".$addIndx;
		$nameMemoTextId = "elem_memoTextId".$addIndx;
		$memoId="memo".$addIndx;
		
		$memoTextId=$memoText=$memoProviderId="";
		$memoDate= $dfdt;
		if( count($arrVal) > 0 ){
			if(isset($arrVal[0])) $memoTextId = $arrVal[0];
			if(isset($arrVal[1])) $memoText= $arrVal[1];
			if(isset($arrVal[2])) $memoDate= $arrVal[2];
			if(isset($arrVal[3])) $memoProviderId= $arrVal[3];
		}

		$strSel="<select id=\"".$nameMemoProvider."\" name=\"".$nameMemoProvider."\" class=\"form-control\">
				<option value=\"\"></option>";
		if(count($arrMrPersonnal) > 0){
			foreach($arrMrPersonnal as $key=>$val){
				$tmp = !empty($memoProviderId)?$memoProviderId:$_SESSION["authId"];
				$sel = ($key==$tmp) ? "selected" : "";
				$strSel.= "<option value=\"".$key."\" ".$sel." >".$val."</option>";
			}
		}
		$strSel.="</select>";
		
		$str = "<div id=\"".$memoId."\" class=\"memo panel panel-success\" >
				<div class=\"panel-heading\">
				<div class=\"row form-inline\">
					<div class=\"col-sm-2\">
						<label>Notes</label>
					</div>
					<div class=\"col-sm-3\">
						<div class=\"form-group\">
							<label for=\"".$nameMemoProvider."\">Provider</label> ".$strSel.
						"</div>
					</div>
					<div class=\"col-sm-3\">
						<div class=\"form-group\">
							<label for=\"".$nameMemoDate."\" >Date</label> <input type=\"text\" id=\"".$nameMemoDate."\" name=\"".$nameMemoDate."\" value=\"".$memoDate."\" class=\"date-pick form-control\">
						</div>
					</div>
					<div class=\"col-sm-2\">						
						<span class=\"spnFuAdd glyphicon glyphicon-plus fontsize-big\" title=\"Insert\" onclick=\"getMemoTable(null,'','".$dfdt."')\"></span>&nbsp;&nbsp;
						<span class=\"spnFuDel glyphicon glyphicon-remove fontsize-big\" title=\"Remove\" onclick=\"remMemoTable('".$addIndx."')\"></span>						
					</div>	
				</div>				
				</div>
				<div class=\"panel-body\">	
				<label for=\"".$nameMemoText."\" >Physician notes : </label>
				<textarea id=\"".$nameMemoText."\" name=\"".$nameMemoText."\" class=\"form-control\">".$memoText."</textarea>
				<input type=\"hidden\" name=\"".$nameMemoTextId."\" value=\"".$memoTextId."\">
				</div>
				</div>
			   ";
		return $str;
	}
	
	function getChartMemo(){
		$form_id=$this->fid;
		$patient_id=$this->pid;
		
		$datamemo="";
		$sql = "SELECT ".
			 "chart_memo_text.memo_text_id, ".
			 "chart_memo_text.memo_text, ".
			 "chart_memo_text.memo_date, ".
			 "chart_memo_text.provider_id ".
			 "FROM memo_tbl ".
			 "INNER JOIN chart_memo_text ON chart_memo_text.memo_id = memo_tbl.memo_id ".
			 "WHERE form_id = '".$form_id."' AND patient_id ='".$patient_id."' AND chart_memo_text.deleted_by='0' ORDER BY memo_date DESC ";
		$rez = sqlStatement($sql);
		$i=1;
		for(;$row=sqlFetchArray($rez);$i++){
			$memo_text_id = $row['memo_text_id'];
			$memo_text = $row['memo_text'];
			$memo_date = wv_formatDate($row['memo_date']);
			$memo_providerId = $row['provider_id'];
			$datamemo .= $this->getMemoTable(array($memo_text_id,$memo_text,$memo_date,$memo_providerId),$i,date(phpDateFormat()));
		}
		
		// if not record
		if(empty($datamemo)){
			$datamemo = $this->getMemoTable(array(),1,date(phpDateFormat())); //default
		}
		
		//
		$elem_cntrTableMemo = ($i>1) ? $i-1 : 1 ;
		
		//
		$datamemo = "<div id=\"memo_panel_grp\" class=\"panel-group\">".$datamemo."</div>
					<input type=\"hidden\" name=\"elem_cntrTableMemo\" value=\"".$elem_cntrTableMemo."\">";		

		return $datamemo;
	
	}

	function add_memo(){
		$elem_cntrTableMemo = $_GET["elem_cntrTableMemo"];
		
		$datamemo = $this->getMemoTable(array(),$elem_cntrTableMemo,date(phpDateFormat())); //default
		
		echo $datamemo;
		
	}

}
?>