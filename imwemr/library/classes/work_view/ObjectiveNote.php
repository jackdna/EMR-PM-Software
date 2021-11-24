<?php
//ObjectiveNote.php

class ObjectiveNote extends ChartNote{
	private $examName, $tbl;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_objective_notes";
		$this->examName="SLE";
	}
	
	function get_objective_note(){
		$sql = "SELECT * FROM chart_objective_notes WHERE obj_form_id = '".$this->fid."'";
		$row = sqlQuery($sql);
		if($row != false){
			$elem_objNotes = $row["obj_notes"];	
		}
		return $elem_objNotes;
	} 
	
	

}



?>