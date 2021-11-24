<?php
class Paging {

			var $limit;
			var $offset;
			var $totalPages;
			var $currentPage;
			var $dbObj;
			var $query;
			
			var $totalRecords;
			var $numRecords;
			var $sort_by;
			var $sort_order;
			var $func_name;
			var $filter='';
            var $data='';


		function __construct ($perPage = 10, $page =1){
		
				$this->perPage = $perPage;
				$this->currentpage = $page;
			
		}

		function calculateTotalNoOfRecords(){
		
				
				$arReturn = array();
				if( is_array($this->data) && count($this->data) > 0 ){
                    $this->totalRecords = count($this->data);
                } else {
                    $recordSet = imw_query($this->query);
                    //echo $this->query;
                    while($row = imw_fetch_assoc($recordSet)){

                        $arReturn[] = $row;
                    }
                    $resultSet =  $arReturn;

                    $this->totalRecords = count($resultSet);
                }
		}
		

		function calculateTotalNoOfPages(){
		
				if(!isset($this->totalRecords)){
				
					$this->calculateTotalNoOfRecords();
				}	
		
			$totalPages = $this->totalRecords / $this->perPage;
			
			$this->totalPages = ceil($totalPages);
			return $this->totalPages;
		
		}


		function fetchLimitedRecords(){
		
				if(!isset($this->totalRecords)){
				
					$this->calculateTotalNoOfRecords();
				}
				
				$this->limit = $this->perPage;
				
				$this->offset = $this->limit * ($this->currentpage -1);
				
				$this->query .= " limit " . $this->offset .  ", " . $this->limit;
			
				$arReturn = array();
				$tempArr = $this->data;
                if( is_array($tempArr) && count($tempArr) > 0 ){
                    $start = (int)$this->offset; 
                    $end = $start + $this->limit;
                    $end =  $end > $this->totalRecords ? $this->totalRecords : $end;
                    $x = $start;
                    while( $x < ($end)) {
                        $arReturn[] = $tempArr[$x];
                        $x++;
                    }
                } else {
				
                    $recordSet = imw_query($this->query);
                    while($row = imw_fetch_assoc($recordSet)){
                        $arReturn[] = $row;
                    }
                }
				$resultSet = $arReturn;
				
				//$this->numRecords = $this->currentpage * $this->perPage;
				$this->numRecords = count($resultSet);
				return $resultSet;
		}



		function getPagingString(){
		
			if($this->totalRecords > 0){
			$from = $this->offset + 1;
			
			$to = $this->numRecords + $from-1;
			
			$string = "Showing " . $from ." to " . ($to >  $this->totalRecords ? $this->totalRecords : $to) ." of ".$this->totalRecords . " record(s)" ;
			}
			else{
			
			$string= '';
			}
			
			return $string;
		}
		
		
		
		function buildComponent($page){
		
			$this->currentpage = $page;
			if (($this->totalRecords <= $this->perPage) || ($this->totalRecords == 0)) 
			{
				return;
			}
		
		global $siteURL, $_REQUEST;
		$totalNumOfPage = $this->calculateTotalNoOfPages();
		
		if (!empty($_REQUEST)) {
			foreach($_REQUEST as $key=>$value) {
				if ($key != "page") {
					if (empty($paramStr)) {
						$paramStr = $key . "=" . $value;
					}
					else {
						$paramStr.= "&" . $key . "=" . $value;
					}
				}
				
			}
			if(isset($paramStr))
			$extraParams= "?" . $paramStr . "&";
			else
			$extraParams= "?";
		}
		 else {
			 $extraParams = "?";
		 }

		
		if ($page == 1) {
			$firstLink = "<font class='text'>First</font>";
			$prevLink = "<font class='text'>Previous</font>";
			/*$nextLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=" . ($page  + 1) . "' class='bold_link'>Next</a>";
			$lastLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=" . ($totalNumOfPage) . "' class='bold_link'>Last</a>";*/
			$nextLink = "<a href='#' class='a_clr1' onClick='".$this->func_name."(".($page +1 ).",\"".$this->sort_by."\",\"".$this->sort_order."\",\"".$this->filter."\")'>Next</a>";
			$lastLink = "<a href='#' class='a_clr1' onClick='".$this->func_name."(".($totalNumOfPage).",\"".$this->sort_by."\",\"".$this->sort_order."\",\"".$this->filter."\")'>Last</a>";
		}
		else if ($page == $totalNumOfPage){
			/*$firstLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=1' class='bold_link'>First</a>";
			$prevLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=" . ($page  - 1) . "' class='bold_link'>Previous</a>";
			*/
			$firstLink = "<a href='#' class='a_clr1' onClick='".$this->func_name."(1,\"".$this->sort_by."\",\"".$this->sort_order."\",\"".$this->filter."\")'>First</a>";
			$prevLink = "<a href='#' class='a_clr1' onClick='".$this->func_name."(" . ($page  - 1) . ",\"".$this->sort_by."\",\"".$this->sort_order."\",\"".$this->filter."\")'>Previous</a>";
			
			$nextLink = "<font class='text'>Next</font>";
			$lastLink = "<font class='text'>Last</font>";
		}
		else {
			/*$firstLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=1' class='bold_link'>First</a>";
			$prevLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams. "page=" . ($page  - 1) . "' class='bold_link'>Previous</a>";
			$nextLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=" . ($page  + 1) . "' class='bold_link'>Next</a>";
			$lastLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=" . ($totalNumOfPage) . "' class='bold_link'>Last</a>";*/
			$firstLink = "<a href='#' class='a_clr1' onClick='".$this->func_name."(1,\"".$this->sort_by."\",\"".$this->sort_order."\",\"".$this->filter."\")')'>First</a>";
			$prevLink = "<a href='#' class='a_clr1' onClick='".$this->func_name."(" . ($page  - 1) . ",\"".$this->sort_by."\",\"".$this->sort_order."\",\"".$this->filter."\")'>Previous</a>";
			$nextLink = "<a href='#' class='a_clr1' onClick='".$this->func_name."(" . ($page  + 1) . ",\"".$this->sort_by."\",\"".$this->sort_order."\",\"".$this->filter."\")'>Next</a>";
			$lastLink = "<a href='#' class='a_clr1' onClick='".$this->func_name."(" . ($totalNumOfPage) . ",\"".$this->sort_by."\",\"".$this->sort_order."\",\"".$this->filter."\")'>Last</a>";
		}
	
	$str = '<style>
	.no_border td{border:0px solid #eee;}
	</style>
	<table width="50%" cellpadding="0" cellspacing="0" border="0" align="right" class="no_border">
	<tr>
		<td class="lenk_text text-left">&laquo;'.$firstLink.'&nbsp;<font class="lenk_text">|</font>&nbsp;'.$prevLink.'</td>
		<td class="text-center"><select id="pageSelect" class="pagingSelect form-control minimal" OnChange = "'.$this->func_name.'(this.value,\''.$this->sort_by.'\',\''.$this->sort_order.'\',\''.$this->filter.'\');"> 
		';
		for($pageCount = 1; $pageCount <= $totalNumOfPage; $pageCount++) {
			$selectedText = ($page == $pageCount) ? 'selected': ''; 
			$str .= "<option value='". $pageCount ."' ".$selectedText.">".$pageCount ."</option>";
		}
		$str .= '</select></td>
		<td class="lenk_text text-right">'.$nextLink.'&nbsp;<font class="lenk_text">|</font>&nbsp;'.$lastLink.'&raquo;</td>
        </tr>
</table>';
	return $str;
	}
	
	
	/*Paging Builder for R8*/
	function buildComponentR8($page, $wout_btns = false){
		
		$this->currentpage = $page;
		if (($this->totalRecords <= $this->perPage) || ($this->totalRecords == 0)) 
		{
			return;
		}
		
		global $siteURL, $_REQUEST;
		$totalNumOfPage = $this->calculateTotalNoOfPages();
		
		if (!empty($_REQUEST)) {
			foreach($_REQUEST as $key=>$value) {
				if ($key != "page") {
					if (empty($paramStr)) {
						$paramStr = $key . "=" . $value;
					}
					else {
						$paramStr.= "&" . $key . "=" . $value;
					}
				}
				
			}
			if(isset($paramStr))
			$extraParams= "?" . $paramStr . "&";
			else
			$extraParams= "?";
		}
		 else {
			 $extraParams = "?";
		 }

		
		if ($page == 1) {
			$firstLink = "<a href='javascript:void(0)' class='noLionk'>First</a>";
			$prevLink = "<a href='javascript:void(0)' class='noLionk'>Previous</a>";
			/*$nextLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=" . ($page  + 1) . "' class='bold_link'>Next</a>";
			$lastLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=" . ($totalNumOfPage) . "' class='bold_link'>Last</a>";*/
			$nextLink = "<a href='#' class='a_clr1' onClick='".$this->func_name."(".($page +1 ).",\"".$this->sort_by."\",\"".$this->sort_order."\",\"".$this->filter."\")'>Next</a>";
			$lastLink = "<a href='#' class='a_clr1' onClick='".$this->func_name."(".($totalNumOfPage).",\"".$this->sort_by."\",\"".$this->sort_order."\",\"".$this->filter."\")'>Last</a>";
		}
		else if ($page == $totalNumOfPage){
			/*$firstLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=1' class='bold_link'>First</a>";
			$prevLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=" . ($page  - 1) . "' class='bold_link'>Previous</a>";
			*/
			$firstLink = "<a href='#' class='a_clr1' onClick='".$this->func_name."(1,\"".$this->sort_by."\",\"".$this->sort_order."\",\"".$this->filter."\")'>First</a>";
			$prevLink = "<a href='#' class='a_clr1' onClick='".$this->func_name."(" . ($page  - 1) . ",\"".$this->sort_by."\",\"".$this->sort_order."\",\"".$this->filter."\")'>Previous</a>";
			
			$nextLink = "<a href='javascript:void(0)' class='noLionk'>Next</a>";
			$lastLink = "<a href='javascript:void(0)' class='noLionk'>Last</a>";
		}
		else {
			/*$firstLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=1' class='bold_link'>First</a>";
			$prevLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams. "page=" . ($page  - 1) . "' class='bold_link'>Previous</a>";
			$nextLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=" . ($page  + 1) . "' class='bold_link'>Next</a>";
			$lastLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=" . ($totalNumOfPage) . "' class='bold_link'>Last</a>";*/
			$firstLink = "<a href='#' class='a_clr1' onClick='".$this->func_name."(1,\"".$this->sort_by."\",\"".$this->sort_order."\",\"".$this->filter."\")')'>First</a>";
			$prevLink = "<a href='#' class='a_clr1' onClick='".$this->func_name."(" . ($page  - 1) . ",\"".$this->sort_by."\",\"".$this->sort_order."\",\"".$this->filter."\")'>Previous</a>";
			$nextLink = "<a href='#' class='a_clr1' onClick='".$this->func_name."(" . ($page  + 1) . ",\"".$this->sort_by."\",\"".$this->sort_order."\",\"".$this->filter."\")'>Next</a>";
			$lastLink = "<a href='#' class='a_clr1' onClick='".$this->func_name."(" . ($totalNumOfPage) . ",\"".$this->sort_by."\",\"".$this->sort_order."\",\"".$this->filter."\")'>Last</a>";
		}
$str = <<<END
	<div class="col-sm-4 text-right">
		$firstLink $prevLink 
	</div>
	<div class="col-sm-2">
		<select id="pageSelect" class="selectpicker" data-width="100%" OnChange="$this->func_name(this.value,'$this->sort_by','$this->sort_order','$this->filter');">
END;
		for($pageCount = 1; $pageCount <= $totalNumOfPage; $pageCount++) {
			$selectedText = ($page == $pageCount) ? 'selected': ''; 
			$str .= "<option value='". $pageCount ."' ".$selectedText.">".$pageCount ."</option>";
		}
$str .= <<<END
		</select>
	</div>
	<div class="col-sm-4 text-left">
		$nextLink $lastLink
	</div>
END;
if($wout_btns) {
    $str = <<<END
		<select id="pageSelect" class="selectpicker" data-width="100%" OnChange="$this->func_name(this.value,'$this->sort_by','$this->sort_order','$this->filter');">
END;
		for($pageCount = 1; $pageCount <= $totalNumOfPage; $pageCount++) {
			$selectedText = ($page == $pageCount) ? 'selected': ''; 
			$str .= "<option value='". $pageCount ."' ".$selectedText.">".$pageCount ."</option>";
		}
$str .= <<<END
		</select>
END;
}
//	$str = '<table width="50%" cellpadding="0" cellspacing="0" border="0" align="right" class="no_border">
//	<tr>
//		<td class="lenk_text text-left">&laquo;'.$firstLink.'&nbsp;<font class="lenk_text">|</font>&nbsp;'.$prevLink.'</td>
//		<td class="text-center"><select id="pageSelect" class="pagingSelect form-control minimal" OnChange = "'.$this->func_name.'(this.value,\''.$this->sort_by.'\',\''.$this->sort_order.'\',\''.$this->filter.'\');"> 
//		';
//		for($pageCount = 1; $pageCount <= $totalNumOfPage; $pageCount++) {
//			$selectedText = ($page == $pageCount) ? 'selected': ''; 
//			$str .= "<option value='". $pageCount ."' ".$selectedText.">".$pageCount ."</option>";
//		}
//		$str .= '</select></td>
//		<td class="lenk_text text-right">'.$nextLink.'&nbsp;<font class="lenk_text">|</font>&nbsp;'.$lastLink.'&raquo;</td>
//        </tr>
//</table>';
	return $str;
	}
	
		
		
		
		function buildComponent2($page){
		
					$this->currentpage = $page;
				if (($this->totalRecords <= $this->perPage) || ($this->totalRecords == 0)) {
			return;
		}
		
		global $siteURL, $_GET;
		$totalNumOfPage = $this->calculateTotalNoOfPages();
		
		if (!empty($_GET)) {
			foreach($_GET as $key=>$value) {
				if ($key != "page") {
					if (empty($paramStr)) {
						$paramStr = $key . "=" . $value;
					}
					else {
						$paramStr.= "&" . $key . "=" . $value;
					}
				}
				
			}
			if(isset($paramStr))
			$extraParams= "?" . $paramStr . "&";
			else
			$extraParams= "?";
		}
		 else {
			 $extraParams = "?";
		 }

		
		if ($page == 1) {
			$firstLink = "<font class='text'>First</font>";
			$prevLink = "<font class='text'>Previous</font>";
			/*$nextLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=" . ($page  + 1) . "' class='bold_link'>Next</a>";
			$lastLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=" . ($totalNumOfPage) . "' class='bold_link'>Last</a>";*/		$nextLink = "<a href='#' class='bold_link' onClick='load_direct_messages(".($page +1 ).")'>Next</a>";
			$lastLink = "<a href='#' class='bold_link' onClick='load_direct_messages(".($totalNumOfPage).")'>Last</a>";
		}
		else if ($page == $totalNumOfPage){
			/*$firstLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=1' class='bold_link'>First</a>";
			$prevLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=" . ($page  - 1) . "' class='bold_link'>Previous</a>";*/
			$firstLink = "<a href='#' class='bold_link' onClick='load_direct_messages(1)'>First</a>";
			$prevLink = "<a href='#' class='bold_link' onClick='load_direct_messages(" . ($page  - 1) . ")'>Previous</a>";
			$nextLink = "<font class='text'>Next</font>";
			$lastLink = "<font class='text'>Last</font>";
		}
		else {
			/*$firstLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=1' class='bold_link'>First</a>";
			$prevLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams. "page=" . ($page  - 1) . "' class='bold_link'>Previous</a>";
			$nextLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=" . ($page  + 1) . "' class='bold_link'>Next</a>";
			$lastLink = "<a href='" . $_SERVER['PHP_SELF']. $extraParams . "page=" . ($totalNumOfPage) . "' class='bold_link'>Last</a>";*/
			$firstLink = "<a href='#' class='bold_link' onClick='load_direct_messages(1)'>First</a>";
			$prevLink = "<a href='#' class='bold_link' onClick='load_direct_messages(" . ($page  - 1) . ")'>Previous</a>";
			$nextLink = "<a href='#' class='bold_link' onClick='load_direct_messages(" . ($page  + 1) . ")'>Next</a>";
			$lastLink = "<a href='#' class='bold_link' onClick='load_direct_messages(" . ($totalNumOfPage) . ")'>Last</a>";
		}
	?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" align="right">
	<tr>
		<td align="left" class="text" ><  &nbsp;<?php print $prevLink ?></td>
		
		<td align="left" class="text"><?php print $nextLink ?> &nbsp;&nbsp;&gt;</td>
	</table>
	<?php
	}
		
		
		
		
		
		function returnString(){
		
		
				if(!isset($this->totalRecords)){
				
					$this->calculateTotalNoOfRecords();
				}
				
				$this->limit = $this->perPage;
				
				$this->offset = $this->limit * ($this->currentpage -1);
				
				//$this->query .= " limit " . $this->offset .  ", " . $this->limit;
			
				$this->dbObj->query = $this->query;
				//$this->numRecords = count($resultSet);
				
				return $this->dbObj->query;
		
		}

			
}

?>
