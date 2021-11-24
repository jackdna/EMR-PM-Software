<?php

//url_chartnote --- "http://192.168.1.102/imwemr-dev"
$GLOBALS['cndir'] = $GLOBALS['rootdir']."/chart_notes";

//Functions --
spl_autoload_register(function($class) {

    if($class=="SmartTags"){ $class="functions.smart_tags"; }
    else if($class=="CLSHoldDocument"){ $class="class.cls_hold_document"; }
    else if($class=="Direct"){ $class="direct_class"; }
    else if($class=="CLSCommonFunction"){ $class="cls_common_function";  }
     else if($class=="core_notifications"){ $class="class.cls_notifications";  }
	else if($class=="JSMin"){ $class="jsmin";  }
	else if($class=="CssMin"){ $class="cssmin-v3.0.1-minified";  }
	//else if($class=="CssMin"){ $class="/min/cssmin-v3.0.1-minified.php";  }
	
	$pt = $GLOBALS['srcdir']."/classes";	
	if(file_exists($pt."/work_view/".$class.".php")){	
	include $pt."/work_view/".$class.".php";
	}else if(file_exists($pt."/".$class.".php")){		
	include $pt."/".$class.".php";
	}else if(file_exists($GLOBALS['srcdir']."/min/".$class.".php")){	
	include $GLOBALS['srcdir']."/min/".$class.".php";	
	}
	
});

?>