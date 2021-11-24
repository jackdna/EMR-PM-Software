<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
/*
File: appt_cn_functions.php
Coded in PHP7 Rana
Purpose: Define Class for Lock Chart
Access Type: Direct
*/
require_once($GLOBALS['fileroot'].'/library/classes/work_view/ChartPtLock.php');
require_once($GLOBALS['fileroot'].'/library/classes/work_view/Fu.php');

class appt_chartnotes{
	private $obg_chart_locking;
	private $obg_chart_fu;
	private $obj_db;

	function __construct($u_id, $p_id){
		$this->obg_chart_locking = new ChartPtLock($u_id, $p_id);
		$this->obg_chart_fu = new Fu();
		
		//$this->obj_db = $GLOBALS["adodb"]["db"];
	}

	function release_pt_cn_locks(){
		$this->obg_chart_locking->releaseUsersPastPt();
	}

	function read_fu_xml($fu_xml){
		return $this->obg_chart_fu->fu_getXmlValsArr($fu_xml);
	}
}
?>