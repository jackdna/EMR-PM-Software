<?php
class PhyPtNotes{
	private $phy_note_titles, $pid;
	public function __construct($pid){
		$this->phy_note_titles	= array("Ocular Sx."=>"Ocular Sx", "Ocular Dx."=>"Diagnosis", "Consult"=>"Consult", "Med Dx."=>"Med Dx");
		$this->pid = $pid;
	}
	
	function getPhyNotesConcate($title){
		$ocular_phrase = "";
		$pid = $this->pid;
		if(!empty( $pid ) && !empty( $title )){
			$qry_ocular = "SELECT * FROM pnotes WHERE pid='$pid' and title = '$title' ORDER by id DESC";
			$res_ocular = imw_query($qry_ocular);
			$num_rows_ocular = imw_num_rows($res_ocular);
			if($num_rows_ocular>0){
				$cntr = "";
				while($row=imw_fetch_array($res_ocular)){
					//$cntr = $cntr+1;
					if(!empty($row["body"])){
						$ocular_phrase .= $row["body"]."\n";
					}
					//$ocular_id =  (empty($ocular_id)) ? $row["id"] : $ocular_id ;
				}
			}
		}
		return $ocular_phrase;		
	}
	
	function save_phy_pt_notes($op, $title, $show_title, $ocu_phrase){
		if($op=="update"){
			$sql = "UPDATE pnote_cat set showTitle='".sqlEscStr($show_title)."', pnotes='".sqlEscStr($ocu_phrase)."' WHERE pid='".$this->pid."' and title = '".sqlEscStr($title)."' ";
			$row = sqlQuery($sql);
			
		}else if($op=="insert"){
			$sql = "INSERT INTO pnote_cat( id, pid, title, showTitle,pnotes ) VALUES( NULL, '".$this->pid."', '".sqlEscStr($title)."', '".sqlEscStr($show_title)."', '".sqlEscStr($ocu_phrase)."' ) ";
			$row = sqlQuery($sql);
		}
		if( !empty($show_title) ){ $this->phynote_showtitle($title, $show_title);  }
	}
	
	function phynote_showtitle($title, $showtitle=""){	
		if(!empty($showtitle)){
			$sql = "UPDATE chart_phy_note_title SET showTitle='".imw_real_escape_string($showtitle)."' WHERE title='".imw_real_escape_string($title)."'   ";
			sqlQuery($sql);		
		}else{
			$t = $title;
			$sql = "SELECT title, showTitle FROM chart_phy_note_title WHERE title='".imw_real_escape_string($title)."' ";
			$row = sqlQuery($sql);
			if($row!=false){
				$t = trim($row["showTitle"]);
				if(empty($t)){
					$t = trim($row["title"]);	
				}
			}
			return $t;
		}
	}
	
	function load_phy_pt_notes(){
		
		$ar_hdr_cls=array("panel-primary","panel-info","panel-warning","panel-danger");
		$ijk=0;
		foreach($this->phy_note_titles as $title => $show_title){
		
			$elem_showTitle = $ocular_phrase = "";
			$sql = "SELECT showTitle,title,pnotes FROM pnote_cat WHERE pid='".$this->pid."' and title = '".$title."' ";
			$row=sqlQuery($sql);
			if( $row != false ){
				//$elem_showTitle = trim($row["showTitle"]);
				//if(empty($elem_showTitle)){	$elem_showTitle = trim( $row["title"] ); }
				$elem_showTitle = $this->phynote_showtitle($title);
				$ocular_phrase = $row["pnotes"];
			}else{
				$elem_showTitle = $this->phynote_showtitle($title); //$show_title;
				$ocular_phrase = $this->getPhyNotesConcate( $title );
				//Insert into Pnote_cat
				$this->save_phy_pt_notes("insert",$title,$elem_showTitle,$ocular_phrase);								
			}

			//
			if($title == "Ocular Dx."){
				//get from ocular
				$omedhx = new MedHx($this->pid);
				$arrPtChroCond = $omedhx->getOcularEyeInfo_v2();				
				if(count($arrPtChroCond) > 0){
					foreach($arrPtChroCond as $key => $val){
						$strInsert = "".$val;		
						if(!empty($strInsert) && strpos($ocular_phrase,$strInsert) === false){
							$ocular_phrase .= "\r\n".$strInsert;
						}
					}
				}				
			}
			
			$var =  str_replace(" ", "", $show_title); //
			
			//--$ar_hdr_cls[$i++]
			
			if($ijk==0){
				$str.='<div class="row">';
			}

			$str.='<div class="col-sm-6">
				<div class="panel '.$ar_hdr_cls[$ijk++].' ">
				  <div class="panel-heading"><input type="text" name="elem_showTitle'.$var.'" id="elem_showTitle'.$var.'" value="'.$elem_showTitle.'" class="form-control" ></div>
				  <div class="panel-body"><textarea name="elem_ocular_phrase'.$var.'" id="elem_ocular_phrase'.$var.'" class="form-control">'.$ocular_phrase.'</textarea></div>
				</div>
				</div>';
			
			//--	
			if($ijk==2){$str.='</div><div class="row">';}	
		}
		
		$str.='</div>'; //end row
		
		$str='<script type="text/javascript" src="'.$GLOBALS['webroot'].'/library/js/work_view/typeahead.js" ></script>
			<!-- Modal -->
		<div id="pt_physician_notesModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">

		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
				<div class="row">
				<div class="col-sm-10">
				<h4 class="modal-title"><strong>Physician Notes</strong> </h4>
				</div>
				<div class="col-sm-1">
					<span class="glyphicon glyphicon-resize-small clickable " data-toggle="collapse" data-target=".pn_mb" aria-expanded="false" title="Collapse/Full"></span>
				</div>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
		      </div>
		      <div class="modal-body collapse in pn_mb" >
			<form id="frm_phy_notes"><input type="hidden" name="elem_saveForm" value="physician_notes">
			
			'.$str.'
			
			</form>
		      </div>
		      <div class="modal-footer collapse in pn_mb">
			<center>
			<button type="button" id="save_phy_note" class="btn btn-success" >Done</button>
			<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</center>
		      </div>
		    </div>

		  </div>
		</div>';		
		echo $str;
	}
	
	function isPtPnoteExists(){
		$r=0;
		$sql = " SELECT count(*) as num FROM pnote_cat WHERE pid='".$this->pid."' AND pnotes!='' ";
		$row = sqlQuery($sql);
		if($row!=false && $row["num"]>0){
			$r=1;
		}
		return $r;
	}	
	
	function savePhyNote(){		
		
		foreach($this->phy_note_titles as $title => $show_title){
			$var =  str_replace(" ", "", $show_title); //
			
			if(isset($_POST["elem_showTitle".$var]) && isset($_POST["elem_ocular_phrase".$var])){
			
				$tmp_title = $_POST["elem_showTitle".$var];
				$tmp_op = $_POST["elem_ocular_phrase".$var];				
				$this->save_phy_pt_notes('update', $title, $tmp_title, $tmp_op);
			}
			
		}
		$ret="0";
		$ret=$this->isPtPnoteExists();		
		echo "".$ret;
	}

	function get_notes_popup(){
		$str='';
		$sql = " SELECT showTitle,title,pnotes FROM pnote_cat WHERE pid='".$this->pid."' AND pnotes!='' ";
		$rez = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){
			$p=trim($row["pnotes"]);
			
			if(!empty($p)){
				$t=$row["showTitle"];	
				if(empty($t)){  $t=$this->phy_note_titles[$row["title"]]; }
				$pnl = "";
				if($row["title"]=="Ocular Sx."){ $pnl = "panel-primary"; }
				else if($row["title"]=="Ocular Dx."){ $pnl = "panel-info"; }
				else if($row["title"]=="Consult"){ $pnl = "panel-warning";  }
				else if($row["title"]=="Med Dx."){ $pnl = "panel-danger"; }
					
				
				$str.='<div class="row">
					<div class="panel '.$pnl.' ">
					  <div class="panel-heading">'.$t.'</div>
					  <div class="panel-body">'.nl2br($p).'</div>
					</div>
					</div>
					';
			}
		}
		
		$str ="<div>".$str."</div>"; //
		
		echo $str;
	}
}
?>