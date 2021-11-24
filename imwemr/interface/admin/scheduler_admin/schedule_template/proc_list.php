<?php
/*
File: proc_list.php
Purpose: List procedure template
Access Type: Direct
*/

require_once("../../../../config/globals.php");
require_once('../../../../library/classes/admin/scheduler_admin_func.php');

function getRefinedValue($strValue){
    $arrValue = explode("~~~",$strValue);
    return $arrValue[0];
}
if(!isset($procedureid))$procedureid=0;

if($_REQUEST['strSelectType'] == "custom"){
    $strAttribs = substr($_REQUEST['strAttribs'],0,-1);
    $arrAttribs = explode("~:~",$strAttribs);
	
	$res = imw_query("select id,acronym from slot_procedures where proc!='' and (procedureId=id || procedureId=0) and  active_status='yes' and source='' group by proc order by acronym ASC");          
    $arrProc = array();
    while($row = imw_fetch_array($res)){
        $arrProc[$row['acronym']]=$row['id'];
    }
	
	foreach($arrAttribs as $key=>$val)
	{
		list($id,$text)=explode('~~~',$val);
		$text=str_replace('~:','',$text);
		if($id==0)
		{
			$id=$arrProc[$text];
		}
		$arrAttribs[$key]="$id~~~$text";
	}
    $arrAttribs2 = array_map("getRefinedValue",$arrAttribs);
    $strAttribs2 = implode(",",$arrAttribs2);

    $strOPtions="<select size=\"6\" id=\"availableOptions\" name=\"availableOptions\" multiple class=\"form-control\">";
    $res = imw_query("SELECT id, acronym, proc, proc_color FROM slot_procedures WHERE doctor_id = 0 AND proc != '' AND active_status = 'yes' and source='' group by proc order by acronym ASC"); /*and id NOT IN ($strAttribs2)*/
    $arrDefaultName = array();
    $arrDefaultId = array();
    while($row = imw_fetch_object($res)){
        $arrDefaultName[] = $row->proc."~~".$row->acronym;
        $arrDefaultId[] = $row->id;
    }
    $arrResult = @array_combine($arrDefaultId, $arrDefaultName);
    //@asort($arrResult);
    $intAvailable = 0;
    if(count($arrResult) > 0){
        $intAvailable = 1;  
			if(!empty($arrResult ))
			{
        		foreach($arrResult as $variable => $value){
          		 $sel="";
           				 if($variable==$procedureid)
               			 { 
              	     	 $sel='selected'; 
                    	$blSelected = true;
                		}
           		 $vall=explode("~~",$value);
           		 $showProc = $vall[1];
          		  $strOPtions.="<option $sel value=\"$variable~~~$showProc\">".$showProc."</option>";
      		  	}
			}
    } 
    $strOPtions .= "</select>";
    $strOPtions .= "[{(^)}]";
    $strOPtions .= "<select size=\"6\" id=\"selectedOptions\" name=\"selectedOptions\" multiple class=\"form-control\">";
    $res = imw_query("select id,proc,acronym from slot_procedures where proc!='' and (procedureId=id || procedureId=0) and  active_status='yes' and id IN ($strAttribs2) group by proc order by acronym ASC");                
    $arrDefaultName = array();
    $arrDefaultId = array();
    while($row = imw_fetch_array($res)){
        $arrDefaultName[] = $row['proc']."~~".$row['acronym'];
        $arrDefaultId[] = $row['id'];
    }
    $arrResult = array_combine($arrDefaultId, $arrDefaultName);
    //asort($arrResult);
    $intSelected = 0;
    if(count($arrResult) > 0){
        $intSelected = 1;
        foreach($arrResult as $variable => $value){
            $sel="";
            if($variable==$procedureid)
                { 
                    $sel='selected'; 
                    $blSelected = true;
                }
            $vall=explode("~~",$value);
            $showProc = $vall[1];
            $strOPtions.="<option $sel value=\"$variable~~~$showProc\">".$showProc."</option>";
        }
    }  
    $strOPtions .= "</select>[{(^)}]";
    
    $strOPtions .= $intAvailable."[{(^)}]".$intSelected;
}else{
    if($_REQUEST['strSelectType'] == "available"){
        $strOPtions="<select size=\"6\" id=\"availableOptions\" name=\"availableOptions\" multiple class=\"form-control\">";
    }else{
        $strOPtions="<select size=\"6\" id=\"selectedOptions\" name=\"selectedOptions\" multiple class=\"form-control\">"; 
    }

    $res = imw_query("select id,proc,acronym from slot_procedures where proc!='' and (procedureId=id || procedureId=0) and  active_status='yes' and source='' group by proc order by acronym ASC");                
    $arrDefaultName = array();
    $arrDefaultId = array();
    while($row = imw_fetch_object($res)){
        $arrDefaultName[] = $row->proc."~~".$row->acronym;
        $arrDefaultId[] = $row->id;
    }
    $arrResult = array_combine($arrDefaultId, $arrDefaultName);
    //asort($arrResult);
    if(count($arrResult) > 0){

        foreach($arrResult as $variable => $value){
            $sel="";
            if($variable==$procedureid)
                { 
                    $sel='selected'; 
                    $blSelected = true;
                }
            $vall=explode("~~",$value);
            $showProc = $vall[1];
            $strOPtions.="<option $sel value=\"$variable~~~$showProc\">".$showProc."</option>";
        }
    }

    $strOPtions .= "</select>";

    $strOPtions .= "[{(^)}]<select size=\"6\" id=\"availableOptions\" name=\"availableOptions\" multiple class=\"form-control\">
                                                            <option value=\"\"></option>
                                                        </select>";
                                                        
    $strOPtions .= "[{(^)}]<select size=\"6\" id=\"selectedOptions\" name=\"selectedOptions\" multiple class=\"form-control\">                                
                                                            <option value=\"\"></option>
                                                        </select>";
}
print $strOPtions;
?>