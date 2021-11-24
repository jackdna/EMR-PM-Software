<?php 
class dropDown{
	
		public $type = '';
		public $arr = array();
		public $str = '';
		
		function __construct($type=""){
			$this->type = $type;
		}
		public function get_frame_brand_arr($parentSelector){
			if($parentSelector!=""){
				$brand_whr = " and bm.manufacture_id = '".$parentSelector."'";
				$sql = "select fs.frame_source, fs.id, bm.brand_id from in_brand_manufacture as bm inner join in_frame_sources as fs on fs.id=bm.brand_id where fs.del_status = '0' $brand_whr order by fs.frame_source asc";
			}
			else{
				$sql = "SELECT frame_source,id FROM in_frame_sources WHERE del_status = 0 ORDER BY frame_source ASC";
			}
			
			$res = imw_query($sql);
			$arr = array();
			while($row = imw_fetch_assoc($res)){
				$id = $row['id'];
				$arr[$id] = $row['frame_source'];
			}
			return $arr;
		}
		
		public function get_frame_style_arr(){
			$sql = "SELECT style_name,id FROM in_frame_styles WHERE del_status = 0 ORDER BY style_name ASC";
			$res = imw_query($sql);
			$arr = array();
			while($row = imw_fetch_assoc($res)){
				$id = $row['id'];
				$arr[$id] = $row['style_name'];
			}
			return $arr;
		}
		
		public function get_frame_shape_arr(){
			$sql = "SELECT shape_name,id FROM in_frame_shapes WHERE del_status = 0 ORDER BY shape_name ASC";
			$res = imw_query($sql);
			$arr = array();
			while($row = imw_fetch_assoc($res)){
				$id = $row['id'];
				$arr[$id] = $row['shape_name'];
			}
			return $arr;
		}
				
		public function get_frame_manu_arr(){
			$sql = "SELECT manufacturer_name,id FROM  in_manufacturer_details WHERE del_status = '0' AND frames_chk='1' ORDER BY manufacturer_name ASC";
			$res = imw_query($sql);
			$arr = array();
			while($row = imw_fetch_assoc($res)){
				$id = $row['id'];
				$arr[$id] = $row['manufacturer_name'];
			}
			return $arr;
		}
		
		public function get_frame_color_arr(){
			$sql = "SELECT color_name,id FROM in_frame_color WHERE del_status = 0 ORDER BY color_name ASC";
			$res = imw_query($sql);
			$arr = array();
			while($row = imw_fetch_assoc($res)){
				$id = $row['id'];
				$arr[$id] = $row['color_name'];
			}
			return $arr;
		}
		
		public function drop_down($type="",$selected="",$parentSelector="",$count=1){
			$this->type = $type;
			$this->str = "";
			$this->arr = array();
			switch($this->type){
				case "brand_id":
					if($parentSelector!="")
						$this->arr = $this->get_frame_brand_arr($parentSelector);
				break;
				
				case "style_id":
					$this->arr = $this->get_frame_style_arr();
				break;
				
				case "shape_id":
					$this->arr = $this->get_frame_shape_arr();
				break;
				
				case "manufacturer_id":
					$this->arr = $this->get_frame_manu_arr();
				break;
				
				case "color_id":
					$this->arr = $this->get_frame_color_arr();
				break;
				
				default:
					$this->arr = array();
				break;
			}
			
			$onclik = "";
			if($type=="manufacturer_id")
			{
				$onclik = "onChange='get_manufacture_brand(this.value,0,$count);'";
			}
			elseif($type=="brand_id")
			{
				$onclik = "onChange='get_brand_style(this.value,0, ".$count.");'";
			}
			
			if(count($this->arr)){
				$this->str = "<select ".$onclik." id='".$type."_".$count."' name='".$type."_".$count."' class=".$type.">";
				$this->str .= "<option value='0'>Select</option>";
				foreach($this->arr as $id=>$val){
					$selStr = ($selected == $id)?" selected":"";
					$this->str .= "<option value='".$id."'$selStr>".ucwords($val)."</option>";
				}
				$this->str .= "</select>";
			}else{
				$this->str = "<select ".$onclik." id='".$type."_".$count."' name='".$type."_".$count."' class=".$type.">";
				$this->str .= "<option value='0'>Select</option>";
				$this->str .= "</select>";
			}
			return $this->str;
		}
}

?>