<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
/*	
File: dhtmlgoodies_tree.class.php
Purpose: Showing consent trees
Access Type: Include 
*/
class dhtmlgoodies_tree{
	var $elementArray = array();
	var $nameOfCookie = "dhtmlgoodies_expanded"; // Name of the cookie where the expanded nodes are stored.
	function __construct()
	{
		
		
	}

	function writeCSS()
	{
		for($i=0;$i<=2;$i++) {
			$q = $i;
			if($q==0) { $q="";}
			$dhtmlStyleSheet .= '	
		
		
		<style type="text/css">
			#dhtmlgoodies_tree'.$q.' li{
				list-style-type:none;	
				font-family: arial;
				font-size:13px; line-height: 30px;
			}
			#dhtmlgoodies_topNodes'.$q.'{
				margin-left:0px;
				padding-left:0px;
			}
			#dhtmlgoodies_topNodes'.$q.' ul{
				margin-left:20px;
				padding-left:0px;
				display:none;
			}
			#dhtmlgoodies_tree'.$q.' .tree_link{
				line-height:13px;
				padding-left:7px;
				font-size:13px;
	
			}
			#dhtmlgoodies_tree'.$q.' img{
				padding-top:2px;
			}
			#dhtmlgoodies_tree'.$q.' a{
				color: #000000;
				text-decoration:none;
				white-space:nowrap;
			}
			
		</style>';
		}
		$dhtmlStyleSheet.='
			<style type="text/css">
			.body_c{
				scrollbar-face-color:#408bc4;
				scrollbar-shadow-color:#afefff;
				scrollbar-highlight-color:#afefff;
				scrollbar-3dlight-color:#000000;
				scrollbar-darkshadow-color:#006399;
				scrollbar-track-color:#bfd3e6;
				scrollbar-arrow-color:#FFFFFF;
				margin-top:0;
				margin-left:0;
				margin-right:0;
			}
			.activeNodeLink{
				background-color: #316AC5;
				color: #FFFFFF;
				font-weight:bold;
			}
			.text_10	{ 
				font-family:"verdana"; font-size:10px; color:#333333;
			}
			.text_12	{ 
				font-family:"verdana"; font-size:12px; color:#FFFFFF;
			}
			.icon-substract.tree-icon1{ position:absolute; right:0px;}
			.icon-add.tree-icon1 { position:absolute; right:0px;}
			</style>
			';
		
		echo $dhtmlStyleSheet;
		?>	
		<?php	
	}
	
	function get_docs_collapse_status()
	{
		$query = "select collapse_docs_default from copay_policies where policies_id = '1'";
		$sql = imw_query($query);
		$row = imw_fetch_assoc($sql);
		
		return (int)$row['collapse_docs_default'];	
	}
	function writeJavascript()
	{
		$cookieValue = "";
		if(isset($_COOKIE[$this->nameOfCookie]))$cookieValue = $_COOKIE[$this->nameOfCookie];		
		
		?>
		<!--<script type="text/javascript" src="../../admin/menuIncludes_menu/js/disableKeyBackspace.js"></script>-->
		<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/mootools.js"></script>
        <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/dg-filter.js"></script>
		<script type="text/javascript">
		var plusNode = 'icon-add tree-icon1';
		var minusNode = 'icon-substract tree-icon1';		
		var openFolder = 'icon-folderopen tree-icon2';
		var closeFolder = 'tree-icon2';
		var nameOfCookie = '<?php echo $this->nameOfCookie; ?>';
		var collapseDocs = <?php echo $this->get_docs_collapse_status();?>;
		var doc_name = "<?php echo $_REQUEST["doc_name"];?>";
		
		var initExpandedNodes = "<?php echo $cookieValue; ?>";

		function Get_Cookie(name) { 
		   var start = document.cookie.indexOf(name+"="); 
		   var len = start+name.length+1; 
		   if ((!start) && (name != document.cookie.substring(0,name.length))) return null; 
		   if (start == -1) return null; 
		   var end = document.cookie.indexOf(";",len); 
		   if (end == -1) end = document.cookie.length; 
		   return unescape(document.cookie.substring(len,end)); 
		} 
		// This function has been slightly modified
		function Set_Cookie(name,value,expires,path,domain,secure) { 
			expires = expires * 60*60*24*1000;
			var today = new Date();
			var expires_date = new Date( today.getTime() + (expires) );
		    var cookieString = name + "=" +escape(value) + 
		       ( (expires) ? ";expires=" + expires_date.toGMTString() : "") + 
		       ( (path) ? ";path=" + path : "") + 
		       ( (domain) ? ";domain=" + domain : "") + 
		       ( (secure) ? ";secure" : ""); 
		    document.cookie = cookieString; 
		} 
		
		function expandAll()
		{
			var w = treeObj = icons = '';
			for(var i=0; i<=3;i++) {
				treeObj = document.getElementById('dhtmlgoodies_tree'+w);
				if(treeObj) {
					icons = treeObj.getElementsByTagName('i');
					for(var no=0;no<icons.length;no++){
						if(icons[no].className == plusNode) expandNode(false,icons[no]);
					}
				}
				w=i;
			}
		}
		function collapseAll()
		{
			var w = treeObj = icons = '';
			for(var i=0; i<=3;i++) {
				treeObj = document.getElementById('dhtmlgoodies_tree'+w);
				if(treeObj) {
					icons = treeObj.getElementsByTagName('i');
					for(var no=0;no<icons.length;no++){
						if(icons[no].className==minusNode)expandNode(false,icons[no]);
					}
				}
				w=i;
			}
		}
		
		function expandNode(e,inputNode)
		{
				if(initExpandedNodes.length==0)initExpandedNodes=",";
				if(!inputNode)inputNode = this; 
				if(inputNode.tagName.toLowerCase()!='i')inputNode = inputNode.parentNode.getElementsByTagName('i')[0];	
				
				if(inputNode){
					var inputId = inputNode.id.replace(/[^\d]/g,'');			
					var parentUl = inputNode.parentNode;
					var subUl = parentUl.getElementsByTagName('UL');
		
					var inputNode_1 = inputNode.parentNode.getElementsByTagName('i')[1];
				
				
					if(subUl.length==0)return;
					if(subUl[0].style.display=='' || subUl[0].style.display=='none'){	
						subUl[0].style.display = 'block';
						inputNode_1.className = openFolder;
						inputNode.className = minusNode;
						initExpandedNodes = initExpandedNodes.replace(',' + inputId+',',',');
						initExpandedNodes = initExpandedNodes + inputId + ',';
						
						
					}else{
						subUl[0].style.display = '';
						inputNode_1.className = (jQuery(inputNode_1).data('elem-class') ? jQuery(inputNode_1).data('elem-class') : 'icon-folder' ) +' '+closeFolder;
						inputNode.className = plusNode;	
						initExpandedNodes = initExpandedNodes.replace(','+inputId+',',',');			
						
					}
				
					Set_Cookie(nameOfCookie,initExpandedNodes,60);			
				}
			
		}
		
		function hasClassJSID(selector,$this) {
			var className =	" " + selector + " ";
			if ($this.nodeType === 1 && (" " + $this.className + " ").replace('/[\n\t\r]/g', " ").indexOf(className) > -1) {
					return true;
			}
			return false;
		}
		
		
		function expandLimit()
		{
			
			//consent temmplate = 28;
			//package_template = 30;
			//surgery_consent_template = 32;
			//pt_docs_template = 34;
			if( collapseDocs )
				var arr = {'signed_consent':3,'signed_package':5,'view_consult':7,'view_ccda':9,'fax_outbox':11,'fax_inbox':13,'view_pt_docs':15,'view_operative_note': 19,'view_pt_instruction_docs':21,'multi_upload':23,'consent_template':28,'package_template':30,'surgery_consent_template':32,'pt_docs_template':34};
			else 
			var arr = {'consent_template':28,'package_template':30,'surgery_consent_template':32,'pt_docs_template':34};

			var lim = arr[doc_name] ? arr[doc_name] : 28;
			var w = treeObj = icons = '';
			for(var i=0; i<=3;i++) {
				treeObj = document.getElementById('dhtmlgoodies_tree'+w);
				if(treeObj) {
					icons = treeObj.getElementsByTagName('i');
					for(var no=0;no<lim;no++){
						if(icons[no].className == plusNode) expandNode(false,icons[no]);
					}
				}
				w=i;
			}
		}
		//var icons = parentNode.getElementsByTagName('i');
		function initTree()
		{
			// Assigning mouse events
			var w='';
			for(var i=0; i<=3;i++) {
				var parentNode = document.getElementById('dhtmlgoodies_tree'+w);
				if(parentNode) {
					var lis = parentNode.getElementsByTagName('LI'); // Get reference to all the images in the tree
					for(var no=0;no<lis.length;no++){
						var subNodes = lis[no].getElementsByTagName('UL');
						var c = lis[no].childNodes;
						if(subNodes.length>0){
							c[0].style.visibility='visible';
						}else{
							
							c[0].style.visibility='hidden';
						}
					}	
					
					var icons = parentNode.getElementsByTagName('i');
					for(var no=0;no<icons.length;no++){
						if(hasClassJSID('tree-icon1',icons[no]))icons[no].onclick = expandNode;				
					}	
					var aTags = parentNode.getElementsByTagName('A');
					var cursor = 'pointer';
					if(document.all)cursor = 'hand';
					for(var no=0;no<aTags.length;no++){
						aTags[no].onclick = expandNode;		
						aTags[no].style.cursor = cursor;		
					}
					
					var initExpandedArray = initExpandedNodes.split(',');
		
					for(var no=0;no<initExpandedArray.length;no++){
						if(document.getElementById('plusMinus' + initExpandedArray[no])){
							var obj = document.getElementById('plusMinus' + initExpandedArray[no]);	
							expandNode(false,obj);
						}
					}
				}
				w++;
			}
			//START CODE TO EXPAND FIRST NODE
			var doc_collapse = "<?php echo $_REQUEST["doc_collapse"];?>";
			if(doc_collapse !='yes') {
				for(var a=1;a<=2000;a++) {
					var pl = document.getElementById("plusMinus"+a);
					if(pl) {
						if(hasClassJSID('icon-substract',pl)) {
							//break;	
						}else if(hasClassJSID('icon-add',pl)) {
							if (pl.style.visibility=='visible') {//alert(a);
								document.getElementById("plusMinus"+a).click();
								//break;	
							}
						}
					}
				}
			}
			
			if( collapseDocs || (!collapseDocs && (doc_name=='consent_template' || doc_name=='package_template' || doc_name=='surgery_consent_template' || doc_name=='pt_docs_template'))) {
				collapseAll();
				expandLimit();
			}
			//END CODE TO EXPAND FIRST NODE
			$('.scan-icon').each(function() {
				$(this).attr('title', 'Scan/Upload');
			});
		}
		var scanDocId;
		function divOpenClose(event, id,scanId){//For mouse right click 
			scanDocId = scanId;
			if (event.button==2){
				closeAllDiv(id);
				var divName = "reNameDiv"+id;
				if(document.getElementById(divName)){
					document.getElementById(divName).style.display = "block";		
				}
			}
		}
		function scanAction(val,val2,id){			
			if (val == 'rename'){				
				closeAllDiv(id);
				var divName = "reNameDiv"+id;
				if(document.getElementById(divName)){
					document.getElementById(divName).style.display = "none";	
				}
				var txtAnchor = "mainData_"+id;
				document.getElementById(txtAnchor).style.display = "none";	
				var imgTrash = "trashid"+id;
				document.getElementById(imgTrash).style.display = "none";			
				var txtBox = "txtMainData_"+id;
				document.getElementById(txtBox).style.display = "block";	
				document.getElementById(txtBox).focus();
				var myVaue = document.getElementById(txtBox).value;
				myVaue = myVaue.split('.');
				document.getElementById(txtBox).value = myVaue[0];
			}
		}
		function GetXmlHttpObject(){
			var xmlHttp;
			if(window.ActiveXObject){
				try{
					xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch (e){
					xmlHttp = false;
				}
			}
			else{
				try{
					xmlHttp = new XMLHttpRequest();
				}
				catch (e){
				xmlHttp = false;
				}
			}
			if (!xmlHttp)
				alert("Error creating the XMLHttpRequest object.");
			else
				return xmlHttp;
		}

		var xmlHttpTemp;
		function renameOnEnter(e,id){
		var characterCode 
		if(e && e.which){ 
			e = e
			characterCode = e.which 
		}
		else{
			e = event
			characterCode = e.keyCode
		}
	
		if(characterCode == 13){ 
			var txtBox = "txtMainData_"+id;
			if(document.getElementById(txtBox)){
				document.getElementById("reName").style.display = "block";
				var txtBoxData = document.getElementById(txtBox).value;
				txtBoxData = escape(txtBoxData);
				xmlHttpTemp = GetXmlHttpObject();
				if(xmlHttpTemp==null){
					alert ("Browser does not support HTTP Request")
					return;
				}					
				var url = 'change_file_name.php?scanDocId='+scanDocId+'&fileName='+txtBoxData;			

				xmlHttpTemp.onreadystatechange = function(){
					if(xmlHttpTemp.readyState == 4 || xmlHttpTemp.readyState == 'complete'){
						var msgResponse = xmlHttpTemp.responseText	
						//alert(msgResponse);
						var arrMsg = msgResponse.split('`````');
						var msg = arrMsg[0];
						var newName = arrMsg[1];	
						var callFrom = arrMsg[2];	
						if(msg == "DONE"){
							if(callFrom == "main"){
								top.fmain.front.all_data.document.getElementById('consent_tree_id_surgery').src=top.fmain.front.all_data.document.getElementById('consent_tree_id_surgery').src;
							}
							else if(callFrom == "scheduler"){
								top.document.getElementById('patientIolinkPdfTreeId').src = top.document.getElementById('patientIolinkPdfTreeId').src;
							}							
						}
					}
				}
				xmlHttpTemp.open("GET",url,true);
				xmlHttpTemp.send(null);
				}
			}
			else{
				return true 
			}	
		}
		function renameOnBlur(id){		
			var txtBox = "txtMainData_"+id;
			if(document.getElementById(txtBox)){
				var txtBoxData = document.getElementById(txtBox).value;
				document.getElementById("reName").style.display = "block";
				txtBoxData = escape(txtBoxData);
				xmlHttpTemp = GetXmlHttpObject();
				if(xmlHttpTemp==null){
					alert ("Browser does not support HTTP Request")
					return;
				}					
				var url = 'change_file_name.php?scanDocId='+scanDocId+'&fileName='+txtBoxData;			

				xmlHttpTemp.onreadystatechange = function(){
					if(xmlHttpTemp.readyState == 4 || xmlHttpTemp.readyState == 'complete'){
						var msgResponse = xmlHttpTemp.responseText							
						var arrMsg = msgResponse.split('`````');
						var msg = arrMsg[0];
						var newName = arrMsg[1];	
						var callFrom = arrMsg[2];	
								
						if(msg == "DONE"){
							if(callFrom == "main"){
								top.fmain.front.all_data.document.getElementById('consent_tree_id_surgery').src=top.fmain.front.all_data.document.getElementById('consent_tree_id_surgery').src;
							}
							else if(callFrom == "scheduler"){
								top.document.getElementById('patientIolinkPdfTreeId').src = top.document.getElementById('patientIolinkPdfTreeId').src;
							}	
						}
					}
				}
				xmlHttpTemp.open("GET",url,true);
				xmlHttpTemp.send(null);
			}
		
		}
		function closeAllDiv(count){
			for(a=1; a < count; a++){
				var divName = "reNameDiv"+a;
				if(document.getElementById(divName)){
					document.getElementById(divName).style.display = "none";	
				}				
			}
			count = parseInt(count);
			count++;

			var divName = "reNameDiv"+count;
			if(document.getElementById(divName)){
				document.getElementById(divName).style.display = "none";	
			}	
		}	

		function disp_info(id) {
			$(".inner_document").hide();
			$("#"+id).fadeToggle('slow');
			$("#"+id).removeClass("hide");
		}
		function disp_info_out(id) {
			$(".inner_document").fadeOut('fast');
			//$("#"+id).fadeOut('slow');
			//$("#"+id).addClass("hide");
		}

		var tInfoOut;
		function closeInfo(){
			tInfoOut = setTimeout("disp_info_out()",500);
		}
		function stopCloseInfo()
		{
			clearTimeout(tInfoOut);
		}
		
		//START ADD SEARCH FILTER IN DOCS TAB
		if(doc_name == "surgery_consent_template") {
			if(typeof(top.onloadfilter) != "undefined") {
				top.onloadfilter();
				$('#doc_search_span_id',top.document).show();
				top.setFocusFilter();	
			}
		}
		//END ADD SEARCH FILTER IN DOCS TAB

		window.onload = initTree;
		</script>	
		<?php
		
	}
	
	/*This function adds elements to the array*/
	function addToArray($id,$name,$parentID,$url="",$target="",$imageIcon="icon-folder",$imageIconOptional="",$imageIconOptionalURL="",$imageIconOptionalTarget="",$imageIconOptionalAlt="",$splFunction=false,$splFunctionEvent="",$splFunctionName="",$showInfo="",$consentDateTime="",$operatorName="",$faxNumber="",$apptDt="",$additionalClass="",$popupIcon=false,$popupUrl=false,$show_alert=false,$show_alert_html=false,$showDelIcon=false){
		if(empty($parentID))$parentID=0;	
		$this->elementArray[$parentID][] = array($id,$name,$url,$target,$imageIcon,$imageIconOptional,$imageIconOptionalURL,$imageIconOptionalTarget,$imageIconOptionalAlt,$splFunction,$splFunctionEvent,$splFunctionName,$showInfo,$consentDateTime,$operatorName,$faxNumber,$apptDt,$additionalClass,$popupIcon,$popupUrl,$show_alert,$show_alert_html,$showDelIcon);
	}
	
	function drawSubNode($parentID){
		if(isset($this->elementArray[$parentID])){			
			echo "<ul>";
			for($no=0;$no<count($this->elementArray[$parentID]);$no++){
				$urlAdd = "";$popupURL="";
				if($this->elementArray[$parentID][$no][2]){
				 	$urlAdd = " href=\"".$this->elementArray[$parentID][$no][2]."\"";
					$popupURL = $this->elementArray[$parentID][$no][19] ? $this->elementArray[$parentID][$no][19] : $this->elementArray[$parentID][$no][2];
					if($this->elementArray[$parentID][$no][3])$urlAdd.=" target=\"".$this->elementArray[$parentID][$no][3]."\"";	
				}
				$imageOptional = "";
				$specialFunction = "";
				if($this->elementArray[$parentID][$no][5] == "scan-icon"){
					//<img id=\"trashid".$this->elementArray[$parentID][$no][0]."\" src=\"".$this->elementArray[$parentID][$no][5]."\" border='0' align=\"middle\" alt=\"".$this->elementArray[$parentID][$no][8]."\" title=\"".$this->elementArray[$parentID][$no][8]."\"/>
					$imageOptional = "<a href=\"".$this->elementArray[$parentID][$no][6]."\" target=\"".$this->elementArray[$parentID][$no][7]."\"><i id=\"trashid".$this->elementArray[$parentID][$no][0]."\" class=\"glyphicon ".$this->elementArray[$parentID][$no][5]." tree-icon2\" title=\"".$this->elementArray[$parentID][$no][8]."\"></i></a>";
				}
				if($this->elementArray[$parentID][$no][9] == true){
					$specialFunction = $this->elementArray[$parentID][$no][10]."=".$this->elementArray[$parentID][$no][11];
				}
				
				$infoIcon = "<i class=\"glyphicon ".$this->elementArray[$parentID][$no][4]." tree-icon2\" data-elem-class=\"".$this->elementArray[$parentID][$no][4]."\" id=\"folderopenclose".$this->elementArray[$parentID][$no][0]."\"></i>";
				
				$delAction = '';
				if( strtolower($this->elementArray[$parentID][$no][8]) == 'delete')
					$delAction = "onClick=\"".$this->elementArray[$parentID][$no][6]."\"";
				else if( strtolower($this->elementArray[$parentID][$no][8]) == 'move to trash')
					$delAction = "data-url=\"".$this->elementArray[$parentID][$no][6]."\" onClick=\"top.del_doc(this);\" data-target=\"".$this->elementArray[$parentID][$no][7]."\"";
				
				$imageHtml = "";
				if(strtolower($this->elementArray[$parentID][$no][12])== 'yes') {
					list($consentDt,$consentTm,$amPm) = explode(" ",$this->elementArray[$parentID][$no][13]);
					if( $delAction) {
						$imageHtml = "<span ".$delAction."><i class=\"glyphicon ".$this->elementArray[$parentID][$no][5]." \" title=\"".$this->elementArray[$parentID][$no][8]."\" ></i></span>";
					} else {
						$imageHtml = "<a href=\"".$this->elementArray[$parentID][$no][6]."\" target=\"".$this->elementArray[$parentID][$no][7]."\"><i class=\"glyphicon ".$this->elementArray[$parentID][$no][5]." \" title=\"".$this->elementArray[$parentID][$no][8]."\" ></i></a>";	
					}
					$infoMsg1 = "<figure><img src=\"".$GLOBALS['webroot']."/library/images/pdficon.png\" alt=\"\" style=\"\"/></figure>";
					$infoMsg2 = "<strong>Uploaded By:</strong> ".$this->elementArray[$parentID][$no][14]."&nbsp;<br>";
					$infoMsg3 = "<strong>Date :</strong> ".$consentDt;
					if($consentTm) {
						$infoMsg3 .= "  <strong>Time :</strong> ".trim($consentTm." ".$amPm)."";
					}
					$infoSpace = "";
					$additionalMsg = "";
					if(!$this->elementArray[$parentID][$no][14]) {//OPERATOR NAME
						$infoMsg2 = "";	
					}
					if($this->elementArray[$parentID][$no][16]) {//DOS
						$infoMsg2 = $additionalMsg = "<strong>Uploaded with DOS:</strong> ".$this->elementArray[$parentID][$no][16]."&nbsp;<br>";
					}
					if($this->elementArray[$parentID][$no][5] == "sendfax-icon" && !$this->elementArray[$parentID][$no][14]){
						$infoMsg2 = "<strong>Send Fax:&nbsp;&nbsp;</strong> <br>";
						$infoMsg3 = "";
						//$infoSpace = "<br>&nbsp;";
					}else if($this->elementArray[$parentID][$no][5] == "print-icon-doc" && !$this->elementArray[$parentID][$no][14]) {
						$infoMsg2 = "<strong>Print Package:&nbsp;&nbsp;</strong> <br>";
						$infoMsg3 = "";
						//$infoSpace = "<br>&nbsp;";
					}else if($this->elementArray[$parentID][$no][5] == "restore-icon" && $this->elementArray[$parentID][$no][8] == "Move To Signed Forms") {
						$infoMsg2 = $additionalMsg."<strong>Moved By:</strong> ".$this->elementArray[$parentID][$no][14]."&nbsp;<br>";
						$infoMsg3 = "<strong>Date :</strong> ".$consentDt."   <strong>Time :</strong> ".trim($consentTm." ".$amPm)."";
						//$infoSpace = "";
					}else if($this->elementArray[$parentID][$no][5] == "restore-icon" && $this->elementArray[$parentID][$no][8] == "Move to Pending") {
						$infoMsg2 = "<strong>Faxed From :</strong> ".$this->elementArray[$parentID][$no][15]."&nbsp;<br>";
						$infoMsg3 = "<strong>Date :</strong> ".$consentDt."   <strong>Time :</strong> ".trim($consentTm." ".$amPm)."";
						//$infoSpace = "";
					}else if($this->elementArray[$parentID][$no][5] == "outgoing-fax" || $this->elementArray[$parentID][$no][5] == "glyphicon-ok" || $this->elementArray[$parentID][$no][5] == "deliver-icon") {
						$infoMsg2 = "<strong>Faxed By:</strong> ".$this->elementArray[$parentID][$no][14]."&nbsp;<br>";
						$infoMsg3 = "<strong>Date :</strong> ".$consentDt."   <strong>Time :</strong> ".trim($consentTm." ".$amPm)."";
						if($this->elementArray[$parentID][$no][15]) {
							$infoMsg3 .= "<br><strong>Fax Number :</strong> ".$this->elementArray[$parentID][$no][15]."&nbsp;&nbsp;";		
						}
						//$infoSpace = "";
					}
					$infoIcon = "
					<i class=\"glyphicon ".$this->elementArray[$parentID][$no][4]." tree-icon2\" data-elem-class=\"".$this->elementArray[$parentID][$no][4]."\" onmouseover=\"disp_info('popover-content-login".$this->elementArray[$parentID][$no][0]."')\"  id=\"folderopenclose".$this->elementArray[$parentID][$no][0]."\"></i>
					<div class=\" popupdrop\" >
						<div class=\"document\">
							<div onmouseover=\"stopCloseInfo();\" onmouseout=\"closeInfo();\" id=\"popover-content-login".$this->elementArray[$parentID][$no][0]."\" class=\"hide inner_document\">
								<div class=\"popcont\" >
									".$infoMsg1.$infoMsg2.$infoMsg3."
									<div class=\"clearfix\"></div>
									<div class=\"text-left\" > ".$imageHtml.$infoSpace." </div>
								</div>
							</div>
						</div>
					</div>"; //<a href=\"#\"><img src=\"".$GLOBALS['webroot']."/library/images/revert.png\" alt=\"\"/></a> <a href=\"#\"><img src=\"".$GLOBALS['webroot']."/library/images/print_doc.png\" alt=\"\"/></a> <a href=\"#\"><img src=\"".$GLOBALS['webroot']."/library/images/fax.png\" alt=\"\"/></a>
					
				}
				
				if( $this->elementArray[$parentID][$no][18] ) { 
					$popupLink = "&nbsp;<i class=\"icon-popup-item tree-icon2\" title=\"Open seperate window\" id=\"popupLink_".$this->elementArray[$parentID][$no][0]."\" onClick=\"javascript:top.popup_win('".$popupURL."')\"></i>";
				}
				
				$alertIcon = '';
				if( $this->elementArray[$parentID][$no][20] ) { 
					$alertIcon = $this->elementArray[$parentID][$no][21]."&nbsp;";
				}
				
				$delIcon = '';
				if( $this->elementArray[$parentID][$no][22] ) {
					
					
					$delIcon = "<span ".$delAction." class=\"pointer\"><i class=\"glyphicon glyphicon-remove\" style=\"font-size:15px;\" title=\"".$this->elementArray[$parentID][$no][8]."\" ></i></span>";
				}
				
				$right_icons = '';
				if( $popupLink || $delIcon ){
					$right_icons = '<span class="doc_icons">'.$popupLink.$delIcon.'</span>';
				}
				
				echo "<li class=\"tree_node pointer\" style='z-index:1; white-space:nowrap;' id=\"node_".$this->elementArray[$parentID][$no][0]."\"><i class=\"icon-add tree-icon1\" id=\"plusMinus".$this->elementArray[$parentID][$no][0]."\"></i>".$infoIcon."<input type=\"text\"  id=\"txtMainData_".$this->elementArray[$parentID][$no][0]."\" class=\"form-control\" value=\"".$this->elementArray[$parentID][$no][1]."\" style=\"display:none; \" onKeyPress=\"renameOnEnter(event,".$this->elementArray[$parentID][$no][0].");\" onBlur=\"renameOnBlur(".$this->elementArray[$parentID][$no][0].");\"/><a id=\"mainData_".$this->elementArray[$parentID][$no][0]."\" data-toggle=\"tooltip\" title=\"".$this->elementArray[$parentID][$no][1]."\" data-placement=\"top\" class=\"tree_link ".$this->elementArray[$parentID][$no][17]."\"$urlAdd $specialFunction>".$this->elementArray[$parentID][$no][1]."</a>".$right_icons.$imageOptional;	
				echo "
					<div id=\"reNameDIv".$this->elementArray[$parentID][$no][0]."\" style=\"padding-left:5px; position:absolute;z-index:1000;width:60px; left:80px; height:auto; display:none; background-color:#CCCCCC;\"> 
						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
							<tr>
								<td id=\"tdOldname\">									
									<A class=\"btn btn-primary btn-xs\" HREF='javascript:scanAction(\"rename\",\"".$this->elementArray[$parentID][$no][1]."\",\"".$this->elementArray[$parentID][$no][0]."\");' >Rename</A><BR>
								</td>		
							</tr>
						</table>
					</div>
				";
				$this->drawSubNode($this->elementArray[$parentID][$no][0]);
				
				echo "</li>";
			}			
			echo "</ul>";			
		}		
	}
	
	function drawTree($mainTreeNum=""){
		echo "<div id=\"dhtmlgoodies_tree".$mainTreeNum."\">";
		echo "<ul id=\"dhtmlgoodies_topNodes".$mainTreeNum."\">";
		for($no=0;$no<count($this->elementArray[0]);$no++){
			$urlAdd = "";
			if($this->elementArray[0][$no][2]){
				$urlAdd = " href=\"".$this->elementArray[0][$no][2]."\"";
				if($this->elementArray[0][$no][3])$urlAdd.=" target=\"".$this->elementArray[0][$no][3]."\"";	
			}
			$imageOptional = "";
			/*<img id=\"parentOptionalid".$this->elementArray[0][$no][0]."\" src=\"".$this->elementArray[0][$no][5]."\" border='0' align=\"middle\" />*/
			if($this->elementArray[0][$no][5]){
				$imageOptional = "<a href=\"".$this->elementArray[0][$no][6]."\" target=\"".$this->elementArray[0][$no][7]."\"><i id=\"parentOptionalid".$this->elementArray[0][$no][0]."\" class=\"glyphicon ".$this->elementArray[0][$no][5]." tree2\" /></a>";
			}			
			echo "<li class=\"tree_node\" style='white-space:nowrap;' id=\"node_".$this->elementArray[0][$no][0]."\"><i class=\"icon-add tree-icon1\" id=\"plusMinus".$this->elementArray[0][$no][0]."\"></i><i class=\"glyphicon ".$this->elementArray[0][$no][4]." tree-icon2\" data-elem-class=\"".$this->elementArray[0][$no][4]."\" id=\"folderopenclose".$this->elementArray[$parentID][$no][0]."\"></i><a id=\"mainData_".$this->elementArray[0][$no][0]."\" class=\"tree_link\" $urlAdd><strong>".$this->elementArray[0][$no][1]."</strong></a>".$imageOptional;					
			$this->drawSubNode($this->elementArray[0][$no][0]);			
			echo "</li>";	
		}	
		echo "</ul>";	
		echo "</div>";	
	}
}
?>