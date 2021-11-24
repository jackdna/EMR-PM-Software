<?php
class PtScan{
	public $pid;
	public function __construct($pid){
		$this->pid = $pid;
	}

	function getFinalizedChartImagesRecords()
	{
		$patientId = $this->pid;
		$sql = "SELECT ".
			   "DATE_FORMAT(chart_note_date, '".get_sql_date_format('','y')."')  AS prev_finalized_date, ".
			   "DATE_FORMAT(chart_note_date, '".get_sql_date_format()."')  AS prev_finalized_date_FullYear, ".
			   "chart_note_date, doc_title, pdf_url, ".
			   "scan_doc_id ".
			   "FROM ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl ".
			   "WHERE patient_id ='".$patientId."' AND chart_note = 'yes' ".
			   "ORDER BY chart_note_date ASC, scan_doc_id ASC ";
		$rez = sqlStatement($sql);
		return $rez;
	}
	
}

?>