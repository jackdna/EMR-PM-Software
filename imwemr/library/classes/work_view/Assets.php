<?php
class Assets{
	private $ar_cs, $vrsn, $dir_css, $dpth_cache;
	public function __construct(){
		$this->vrsn = (isset($GLOBALS['CHART_APP_VERSION']) && !empty($GLOBALS['CHART_APP_VERSION'])) ? $GLOBALS['CHART_APP_VERSION'] : 0 ;
		$this->dir_lib = $GLOBALS['fileroot']."/library";
		$this->dir_lib_w = $GLOBALS['webroot']."/library";
		$this->dpth_cache = $GLOBALS['fileroot']."/cache";
		if(!empty($this->vrsn)){$this->vrsn = $this->vrsn + 5;}
		
	}
	
	function get_main_js(){
		$ar = array(
				"jquery.min.1.12.4.js"
				
					
				);
		return $ar;	
	}
	
	function get_main_css(){
		$ar = array("jquery-ui.min.1.12.1.css",
				"bootstrap.min.css",				
				"normalize.css",				
				"bootstrap-select.css"
				);
		return $ar;		
	}
	
	function get_file_pth($v,$type){
		$file = $this->dir_lib."/".$type."/".$v;
		if(strpos($v, "amcharts")!==false){ $file = $this->dir_lib."/amcharts/".$v;  }
		if(strpos($v, "messi")!==false||strpos($v, "redactor")!==false){ $file = $this->dir_lib."/".$v;  }		
		return $file;
	}
	
	function is_cache_file_old($filenm, $ar, $type){
		$recreate_cache = 0; //default 0
		$cache_js_file = $this->dpth_cache."/".$filenm;
		if(file_exists($cache_js_file) && is_file($cache_js_file)){
			$time_cache_file = filemtime($cache_js_file);
			foreach($ar as $k => $v){
				$file = $this->get_file_pth($v,$type);			
				
				$time_include_file = filemtime($file);
				if($time_include_file > $time_cache_file){
					$recreate_cache = 1;
					break;
				}
			}
		}else{
			$recreate_cache = 1;
		}
		return $recreate_cache;
	}
	
	function create_cache_file($filenm, $ar, $type){
		$t = $type;		
		$vrsn = $this->vrsn;		
		if(count($ar)>0){			
			$dpth_cache = $this->dpth_cache;
			
			$js_all=""; $stm_all="";
			foreach($ar as $k => $v){
				$file = $this->get_file_pth($v,$type);
				
				//echo "<br/>---<br/>".$file;
				
				if(!empty($v) && file_exists($file)){
					$stm = filemtime ( $file );
					//echo "<br/>---<br/>".$stm;
					//create min
					//require_once($lib);
					$js = '';
					$tmp="";
					$tmp = file_get_contents($file);							
					if($t=="js"){ 
						if(strpos($file,"min.")===false && strpos($file,"amcharts")===false && 
							strpos($file,"normalize.css")===false && strpos($file,"bootstrap-select.css")===false &&
							strpos($file,"common.css")===false
							){$tmp = JSMin::minify($tmp);}
						
						//some hacks--
						if(strpos($file,"bootstrap.min.")!==false){ $tmp .= "var zbtn = $.fn.button.noConflict();$.fn.btn = zbtn;"; }
						
					}
					else if($t=="css"){
						if(strpos($file,"min.")===false ){
						//$tmp = CssMin::minify($tmp); 
						//$tmp = CssMin::minify($tmp); 
						}	
						
						//correct urls
						$lib = $this->dir_lib_w;
						$ar_chk = array("url('../fonts", "url(\"../../library/images", "url(../../library/images", "url(../images", "url(\"../images", "url(../fonts");
						$ar_rep = array("url('".$lib."/fonts", "url(\"".$lib."/images", "url(".$lib."/images", "url(".$lib."/images", "url(\"".$lib."/images", "url(".$lib."/fonts");
						$tmp = str_replace($ar_chk, $ar_rep, $tmp);
					}	
					$js .= $tmp;
					
					//echo "<br/>----<br/>".$js;
					
					$js_all.=$js;
					if(empty($stm_all) || $stm_all<$stm){
						$stm_all=$stm;
					}
				}
				
				if(!empty($v) && !file_exists($file)){ exit("File not found: ".$file);  }
				
			}
			
			//echo "<br/>----<br/>".$stm_all;
			
			
			$js_all.="/*VERSION NO. ".$vrsn."*/";//add version number 
			//create file	
			$cache_js_file = $dpth_cache."/".$filenm;
			$fp = fopen($cache_js_file,'w');	
			fwrite($fp,$js_all);
			fclose($fp);
			//
			if(!empty($stm_all)){touch($cache_js_file, $stm_all);}				
		}
	}
	/**
	function show_content($cache_js_file, $flg_create){
		$content = file_get_contents($cache_js_file);
		$time_cache_file = filemtime($cache_js_file);
		
		
		
		$ExpireTime = 3600*10;
		header('Content-type: text/javascript;');
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", $time_cache_file)." GMT");
		header('Cache-Control: max-age=' . $ExpireTime); // must-revalidate
		header('Expires: '.gmdate('D, d M Y H:i:s', time()+$ExpireTime).' GMT');
		$etag = md5_file($cache_js_file); 
		header("Etag: ".$etag);
		
		if((isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])==$time_cache_file) && 
		    (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) && empty($flg_create)){  	
			header('HTTP/1.1 304 Not Modified');
			//header('Connection: close');
			exit(); 
		}
		else {
			header('HTTP/1.1 200 OK');
		}
		
		ob_end_clean();
		if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){ob_start("ob_gzhandler");}else{ob_start();}
		echo $content;
		ob_end_flush();
	}
	**/	
	function get_file_exe($file_name, $file_type, $ar){
		$flg_create = $this->is_cache_file_old($file_name, $ar, $file_type);			
			
		if($flg_create){
			$this->create_cache_file($file_name, $ar, $file_type);
		}	
		
		$cache_js_file = $this->dpth_cache."/".$file_name;		
		//$this->show_content($cache_js_file, $flg_create);
		return array($cache_js_file, $flg_create, $file_type);	
	}
	
	function get_wv_css(){
		$css="";
		$file_name = "wv_css_cache".$this->vrsn.".css";			
		$t = $this->get_main_css();
		$ar = array("okayNav.css",	
				"common.css", 
				"workview.css", 
				"wv_landing.css", 
				"superbill.css", 
				"epost.css");
		$ar = array_merge($t,$ar);
		return $this->get_file_exe($file_name, "css", $ar);
	}
	
	function get_wv_css_exm(){
		$css="";
		$file_name = "wv_css_exm_cache".$this->vrsn.".css";			
		$t = $this->get_main_css();
		$ar = array("messi/messi.css",	
				"common.css", 
				"workview.css", 
				"wv_landing.css", 
				"superbill.css",
				"drawing.css"
				);
		$ar = array_merge($t,$ar);
		return $this->get_file_exe($file_name, "css", $ar);
	}
	
	function get_wv_css_proc(){
		$css="";
		$file_name = "wv_css_proc_cache".$this->vrsn.".css";			
		$t = $this->get_main_css();
		$ar = array("messi/messi.css",
				
				"common.css", 
				"workview.css", 
				"style.css",
				"wv_landing.css", 
				"drawing.css",
				"superbill.css"
				);
		$ar = array_merge($t,$ar);
		return $this->get_file_exe($file_name, "css", $ar);
	}
	
	function get_wv_js(){
		$css="";
		$file_name = "wv_js_cache".$this->vrsn.".js";			
		
		//"amcharts.js",
		
		$ar = array("jquery-ui.min.1.11.2.js",
				"bootstrap.min.js",
				"bootstrap-select.min.js",
				"bootstrap-typeahead.min.js",
				"jquery.okayNav.min.js",
				
				"common.js", 
				"icd10_autocomplete.js", 
				"work_view/typeahead.js", 
				"work_view/fu_section.js", 
				"work_view/superbill.js",
				"work_view/js_gen.js",
				"work_view/work_view.js",
				"work_view/js_qry.js",
				"buttons.js",
				"work_view/orders_cpoe.js",
				"epost.js",
				"work_view/referring_phy.js",
				"work_view/contact_lens.js",
				"work_view/work_view_landing.js"
				);
		return $this->get_file_exe($file_name, "js", $ar);
	}
	
	function get_wv_js_exm(){
		$css="";
		$file_name = "wv_js_exam_cache".$this->vrsn.".js";			
		
		//"amcharts.js",
		$t = $this->get_main_js();		
		$ar = array("jquery-ui.min.1.11.2.js",
				"work_view/fabric.min.js",
				"bootstrap.min.js",
				"bootstrap-select.min.js",				
				"messi/messi.js",
				"work_view/fabric.more.js",
				"icd10_autocomplete.js",
				"work_view/js_gen.js",
				"work_view/work_view.js",
				"work_view/typeahead.js", 
				"work_view/eventIndicator.js",
				"work_view/drawing_new.js",				
				"work_view/js_qry.js",
				"work_view/correction.js",	
				"work_view/chart_exam.js"	
				);
		$ar = array_merge($t,$ar);		
		return $this->get_file_exe($file_name, "js", $ar);
	}
	
	function get_wv_js_proc(){
		$css="";
		$file_name = "wv_js_proc_cache".$this->vrsn.".js";
		//"amcharts.js",
		$t = $this->get_main_js();		
		$ar = array("jquery-ui.min.1.11.2.js",
				"work_view/fabric.min.js",
				"common.js",
				"bootstrap.min.js",
				"bootstrap-select.min.js",
				"messi/messi.js",				
				"work_view/fabric.more.js",
				"work_view/js_gen.js",
				"work_view/work_view.js",
				"work_view/typeahead.js", 
				"work_view/eventIndicator.js",
				"icd10_autocomplete.js",
				"work_view/chart_exam.js",
				"work_view/js_qry.js",
				"work_view/procedures.js",
				"work_view/superbill.js"				
				);
		$ar = array_merge($t,$ar);		
		return $this->get_file_exe($file_name, "js", $ar);
	}
	
	function get_wv_js_main(){
		$css="";
		$file_name = "wv_js_main_cache".$this->vrsn.".js";			
		$t = $this->get_main_js();
		$ar = array();
		$ar = array_merge($t,$ar);
		return $this->get_file_exe($file_name, "js", $ar);
	}
	
	public function main(){
		$ar = array();
		switch($_GET['op']) {
			case 'wvproccss':
				$ar = $this->get_wv_css_proc();
			break;
			
			case 'wvexmcss':
				$ar = $this->get_wv_css_exm();
			break;	
			
			case 'wvcss':
				$ar = $this->get_wv_css();
			break;
			
			case 'wvjs':
				$ar = $this->get_wv_js();
			break;
			
			case 'wvjsmain':
				$ar = $this->get_wv_js_main();
			break;
			
			case 'wvjsexm':
				$ar = $this->get_wv_js_exm();
			break;
			
			case 'wvjsproc':
				$ar = $this->get_wv_js_proc();
			break;
		
		}
		return $ar;
	}
}
?>