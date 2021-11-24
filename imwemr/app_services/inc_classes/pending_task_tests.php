<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
include_once(dirname(__FILE__).'/user_app.php');
class pending_task_tests extends user_app{	
	public function __construct(){
		parent::__construct();
	}
	public function show_pending_tasks(){
		$this->db_obj->qry  =  "SELECT sdt.scan_doc_id, 
								pd.pid AS patient_id, 
								concat(pd.lname,', ',pd.fname,' ',pd.mname) AS patient_name2, 
								concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.pid) as patient_name, 
								fc.folder_name,
								if(TRIM(sdt.doc_title)='',sdt.pdf_url,sdt.doc_title) AS doc_title, 
								sdt.pdf_url, 
								if(TRIM(doc_upload_type)='scan',DATE_FORMAT(sdt.upload_date,'%m-%d-%Y %h:%i %p'), 		
									if(TRIM(doc_upload_type)='upload',DATE_FORMAT(sdt.upload_docs_date,'%m-%d-%Y %h:%i %p'),'')) 
									AS doc_upload_date,
								if(TRIM(doc_upload_type)='scan', sdt.scandoc_comment, 		
									if(TRIM(doc_upload_type)='upload',upload_comment,'')) 
									AS doc_comments,
								DATE_FORMAT(sdt.task_review_date,'%m-%d-%Y %h:%i %p') 
									AS task_review_date_new,
								CONCAT('".$this->upDir."',sdt.file_path) AS file_path,	
								sdt.doc_type, 
								sdt.doc_upload_type as doc_upload_type	
							FROM ".$this->imedic_scan_db.".scan_doc_tbl sdt 
							INNER JOIN ".$this->dBase.".patient_data pd ON (pd.pid=sdt.patient_id) 
							LEFT JOIN ".$this->imedic_scan_db.".folder_categories fc ON (fc.folder_categories_id=sdt.folder_categories_id) 
							WHERE sdt.task_physician_id='".$this->authId."' 
								AND sdt.task_status!=2  
								AND sdt.task_status!=''
							ORDER BY scan_doc_id ";			
				
			$result_arr = $this->db_obj->get_resultset_array();
			return $result_arr;	
		
	}
	public function show_pending_tests(){
			$this->db_obj->qry = "select 'VF' as testDesc,vf.vf_id as main_id,
							date_format(vf.examDate,'%m-%d-%Y') as taskDate, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							vf.comments,patient_data.id AS patient_id from vf 
							join patient_data on patient_data.id = vf.patientId
							where vf.del_status = '0' and vf.purged = '0'";
			$this->db_obj->qry .=" and vf.finished = '0' and (vf.phyName = '' || vf.phyName = '0') and if(vf.ordrby=".$this->authId.",1,if(vf.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";
				$vf_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry = "select 'VF-GL' as testDesc,vf_gl.vf_gl_id as main_id,
							date_format(vf_gl.examDate,'%m-%d-%Y') as taskDate, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							vf_gl.comments,patient_data.id AS patient_id from vf_gl 
							join patient_data on patient_data.id = vf_gl.patientId
							where vf_gl.del_status = '0' and vf_gl.purged = '0'";
				$this->db_obj->qry .=" and vf_gl.finished = '0' and (vf_gl.phyName = '' || vf_gl.phyName = '0') and if(vf_gl.ordrby=".$this->authId.",1,if(vf_gl.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";
				$vf_gl_arr = $this->db_obj->get_resultset_array();
								
				
				$this->db_obj->qry = "select 'HRT' as testDesc,nfa.nfa_id as main_id,
							date_format(nfa.examDate,'%m-%d-%Y') as taskDate, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							nfa.comments,patient_data.id AS patient_id from nfa 
							join patient_data on patient_data.id = nfa.patient_id
							where nfa.del_status = '0' and nfa.purged = '0'and nfa.finished = '0'";
				$this->db_obj->qry .=" and (nfa.phyName = '' || nfa.phyName = '0') and if(nfa.ordrby=".$this->authId.",1,if(nfa.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";
				$nfa_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry = "select 'OCT' as testDesc,oct.oct_id as main_id,
							date_format(oct.examDate,'%m-%d-%Y') as taskDate, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							oct.comments ,patient_data.id AS patient_id from oct 
							join patient_data on patient_data.id = oct.patient_id
							where oct.del_status = '0' and oct.purged = '0' and oct.finished = '0' ";
				$this->db_obj->qry .=" and (oct.phyName = '' || oct.phyName = '0') and if(oct.ordrby=".$this->authId.",1,if(oct.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							
				//echo $req_qry;echo "<br>";
				$oct_arr = $this->db_obj->get_resultset_array();


				$this->db_obj->qry = "select 'OCT-RNFL' as testDesc,oct.oct_rnfl_id as main_id,
							date_format(oct.examDate,'%m-%d-%Y') as taskDate, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							oct.comments ,patient_data.id AS patient_id from oct_rnfl oct 
							join patient_data on patient_data.id = oct.patient_id
							where oct.del_status = '0' and oct.purged = '0' and oct.finished = '0' ";
				$this->db_obj->qry .=" and (oct.phyName = '' || oct.phyName = '0') and if(oct.ordrby=".$this->authId.",1,if(oct.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							
				//echo $req_qry;echo "<br>";
				$oct_rnfl_arr = $this->db_obj->get_resultset_array();	
				
			
				$this->db_obj->qry = "select 'GDX' as testDesc,test_gdx.gdx_id as main_id,
							date_format(test_gdx.examDate,'%m-%d-%Y') as taskDate, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							test_gdx.comments ,patient_data.id AS patient_id 
							From test_gdx join patient_data on patient_data.id = test_gdx.patient_id
							where test_gdx.del_status = '0' and test_gdx.purged = '0' and test_gdx.finished = '0' ";
							
				$this->db_obj->qry .=" and (test_gdx.phyName = '' || test_gdx.phyName = '0') and if(test_gdx.ordrby=".$this->authId.",1,if(test_gdx.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							
				$gdx_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry= "select 'Pachy' as testDesc,pachy.pachy_id as main_id,
							date_format(pachy.examDate,'%m-%d-%Y') as taskDate, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							pachy.comments,patient_data.id AS patient_id from pachy 
							join patient_data on patient_data.id = pachy.patientId
							where pachy.del_status = '0' and pachy.purged = '0' and pachy.finished = '0' ";
				$this->db_obj->qry .=" and (pachy.phyName = '' || pachy.phyName = '0') and if(pachy.ordrby=".$this->authId.",1,if(pachy.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";														
				$pachy_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry = "select 'IVFA' as testDesc,ivfa.vf_id as main_id,
							date_format(ivfa.exam_date,'%m-%d-%Y') as taskDate, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							ivfa.ivfaComments as comments,patient_data.id AS patient_id from ivfa 
							join patient_data on patient_data.id = ivfa.patient_id
							where ivfa.del_status = '0' and ivfa.purged = '0' and ivfa.finished = '0' ";
				$this->db_obj->qry .=" and (ivfa.phy = '' || ivfa.phy = '0') and if(ivfa.ordrby=".$this->authId.",1,if(ivfa.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";
				$ivfa_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry= "select 'Fundus' as testDesc,disc.disc_id as main_id,
							date_format(disc.examDate,'%m-%d-%Y') as taskDate, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							disc.discComments as comments,patient_data.id AS patient_id from disc 
							join patient_data on patient_data.id = disc.patientId
							where disc.del_status = '0' and disc.purged = '0' and disc.finished = '0' ";
				$this->db_obj->qry .=" and (disc.phyName = '' || disc.phyName = '0') and if(disc.ordrby=".$this->authId.",1,if(disc.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";
				$disc_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry = "select 'External / Anterior' as testDesc,
							disc_external.disc_id as main_id, date_format(disc_external.examDate,'%m-%d-%Y') as taskDate,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							disc_external.discComments as comments,patient_data.id AS patient_id 
							from disc_external join patient_data on patient_data.id = disc_external.patientId
							where disc_external.del_status = '0' and disc_external.purged = '0' and disc_external.finished = '0' ";
				$this->db_obj->qry .=" and (disc_external.phyName = '' || disc_external.phyName = '0') and if(disc_external.ordrby=".$this->authId.",1,if(disc_external.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";
				$discexternal_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry = "select 'Topography' as testDesc,topography.topo_id as main_id,
							date_format(topography.examDate,'%m-%d-%Y') as taskDate, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							topography.comments,patient_data.id AS patient_id from topography 
							join patient_data on patient_data.id = topography.patientId
							where topography.del_status = '0' and topography.purged = '0' and topography.finished = '0' ";
				$this->db_obj->qry .=" and (topography.phyName = '' || topography.phyName = '0') and if(topography.ordrby=".$this->authId.",1,if(topography.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							

				$topography_arr = $this->db_obj->get_resultset_array();
								
				$this->db_obj->qry = "select 'A/Scan' as testDesc,surgical_tbl.surgical_id as main_id,
							date_format(surgical_tbl.examDate,'%m-%d-%Y') as taskDate, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							patient_data.id AS patient_id from surgical_tbl 
							join patient_data on patient_data.id = surgical_tbl.patient_id
							where surgical_tbl.del_status = '0' and surgical_tbl.purged = '0' and surgical_tbl.finished = '0' ";
				$this->db_obj->qry .=" and (surgical_tbl.signedById = '' || surgical_tbl.signedById = '0') and if(surgical_tbl.ordrby=".$this->authId.",1,if(surgical_tbl.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";					
				$ascan_arr = $this->db_obj->get_resultset_array();

				
				$this->db_obj->qry = "select 'B-scan' as testDesc,test_bscan.test_bscan_id as main_id,
							date_format(test_bscan.examDate,'%m-%d-%Y') as taskDate, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							test_bscan.techComments as comments,patient_data.id AS patient_id from test_bscan 
							join patient_data on patient_data.id = test_bscan.patientId
							where test_bscan.del_status = '0' and test_bscan.purged = '0' and test_bscan.finished = '0' ";
				$this->db_obj->qry .=" and (test_bscan.phyName = '' || test_bscan.phyName = '0') and if(test_bscan.ordrby=".$this->authId.",1,if(test_bscan.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							
				$test_bscan_arr = $this->db_obj->get_resultset_array();

				$this->db_obj->qry = "select 'Laboratories' as testDesc,test_labs.test_labs_id as main_id,
							date_format(test_labs.examDate,'%m-%d-%Y') as taskDate, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							test_labs.techComments as comments,patient_data.id AS patient_id from test_labs  
							join patient_data on patient_data.id = test_labs.patientId
							where test_labs.del_status = '0' and test_labs.purged = '0' and test_labs.finished = '0' ";
				$this->db_obj->qry .=" and (test_labs.phyName = '' || test_labs.phyName = '0') and if(test_labs.ordrby=".$this->authId.",1,if(test_labs.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							
				$labs_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry = "select 'Test Other' as testDesc,test_other.test_other_id as main_id,
							date_format(test_other.examDate,'%m-%d-%Y') as taskDate, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							test_other.techComments as comments,patient_data.id AS patient_id from test_other  
							join patient_data on patient_data.id = test_other.patientId
							where test_other.del_status = '0' and test_other.purged = '0' and test_other.finished = '0' ";
				$this->db_obj->qry.=" and (test_other.phyName = '' || test_other.phyName = '0') and if(test_other.ordrby=".$this->authId.",1,if(test_other.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							
				
				$test_other_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry = "select 'Cell Count' as testDesc,test_cellcnt.test_cellcnt_id as main_id,
							date_format(test_cellcnt.examDate,'%m-%d-%Y') as taskDate, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							test_cellcnt.techComments as comments,patient_data.id AS patient_id from test_cellcnt  
							join patient_data on patient_data.id = test_cellcnt.patientId
							where test_cellcnt.del_status = '0' and test_cellcnt.purged = '0' and test_cellcnt.finished = '0' ";
				$this->db_obj->qry .=" and (test_cellcnt.phyName = '' || test_cellcnt.phyName = '0') and if(test_cellcnt.ordrby=".$this->authId.",1,if(test_cellcnt.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							
				$test_cellcnt_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry = "select 'ICG' as testDesc,icg.icg_id as main_id,
							date_format(icg.exam_date,'%m-%d-%Y') as taskDate, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							icg.comments_icg as comments,patient_data.id AS patient_id from icg  
							join patient_data on patient_data.id = icg.patient_id
							where icg.del_status = '0' and icg.purged = '0' and icg.finished = '0'";
				$this->db_obj->qry.=" and (icg.phy = '' || icg.phy = '0') and if(icg.ordrby=".$this->authId.",1,if(icg.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							
				
				$icg_arr = $this->db_obj->get_resultset_array();																							

				$result_arr = array_merge($vf_arr,$vf_gl_arr,$nfa_arr,$oct_arr,$oct_rnfl_arr,$gdx_arr,$pachy_arr,$ivfa_arr,$disc_arr,$discexternal_arr,$topography_arr,$ascan_arr,$test_bscan_arr,$labs_arr,$test_other_arr,$test_cellcnt_arr,$icg_arr);
				return $result_arr;	
		
	}
	// new function for app //
	public function show_pending_tests_app(){
			$this->db_obj->qry = "select 'VF' as testDesc,vf.vf_id as main_id,
							date_format(vf.examDate,'%m-%d-%Y') as taskDate,
							date_format(vf.examDate,'%Y%m%d') as taskDate1,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							vf.comments,patient_data.id AS patient_id from vf 
							join patient_data on patient_data.id = vf.patientId
							where vf.del_status = '0' and vf.purged = '0'";
			$this->db_obj->qry .=" and vf.finished = '0' and (vf.phyName = '' || vf.phyName = '0') and if(vf.ordrby=".$this->authId.",1,if(vf.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";
				$vf_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry = "select 'VF-GL' as testDesc,vf_gl.vf_gl_id as main_id,
							date_format(vf_gl.examDate,'%m-%d-%Y') as taskDate,
							date_format(vf_gl.examDate,'%Y%m%d') as taskDate1,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							vf_gl.comments,patient_data.id AS patient_id from vf_gl 
							join patient_data on patient_data.id = vf_gl.patientId
							where vf_gl.del_status = '0' and vf_gl.purged = '0'";
				$this->db_obj->qry .=" and vf_gl.finished = '0' and (vf_gl.phyName = '' || vf_gl.phyName = '0') and if(vf_gl.ordrby=".$this->authId.",1,if(vf_gl.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";
				$vf_gl_arr = $this->db_obj->get_resultset_array();
								
				
				$this->db_obj->qry = "select 'HRT' as testDesc,nfa.nfa_id as main_id,
							date_format(nfa.examDate,'%m-%d-%Y') as taskDate,
							date_format(nfa.examDate,'%Y%m%d') as taskDate1,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							nfa.comments,patient_data.id AS patient_id from nfa 
							join patient_data on patient_data.id = nfa.patient_id
							where nfa.del_status = '0' and nfa.purged = '0'and nfa.finished = '0'";
				$this->db_obj->qry .=" and (nfa.phyName = '' || nfa.phyName = '0') and if(nfa.ordrby=".$this->authId.",1,if(nfa.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";
				$nfa_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry = "select 'OCT' as testDesc,oct.oct_id as main_id,
							date_format(oct.examDate,'%m-%d-%Y') as taskDate,
							date_format(oct.examDate,'%Y%m%d') as taskDate1,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							oct.comments ,patient_data.id AS patient_id from oct 
							join patient_data on patient_data.id = oct.patient_id
							where oct.del_status = '0' and oct.purged = '0' and oct.finished = '0' ";
				$this->db_obj->qry .=" and (oct.phyName = '' || oct.phyName = '0') and if(oct.ordrby=".$this->authId.",1,if(oct.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							
				//echo $req_qry;echo "<br>";
				$oct_arr = $this->db_obj->get_resultset_array();


				$this->db_obj->qry = "select 'OCT-RNFL' as testDesc,oct.oct_rnfl_id as main_id,
							date_format(oct.examDate,'%m-%d-%Y') as taskDate,
							date_format(oct.examDate,'%Y%m%d') as taskDate1,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							oct.comments ,patient_data.id AS patient_id from oct_rnfl oct 
							join patient_data on patient_data.id = oct.patient_id
							where oct.del_status = '0' and oct.purged = '0' and oct.finished = '0' ";
				$this->db_obj->qry .=" and (oct.phyName = '' || oct.phyName = '0') and if(oct.ordrby=".$this->authId.",1,if(oct.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							
				//echo $req_qry;echo "<br>";
				$oct_rnfl_arr = $this->db_obj->get_resultset_array();	
				
			
				$this->db_obj->qry = "select 'GDX' as testDesc,test_gdx.gdx_id as main_id,
							date_format(test_gdx.examDate,'%m-%d-%Y') as taskDate,
							date_format(test_gdx.examDate,'%Y%m%d') as taskDate1,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,test_gdx.phyName,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							test_gdx.comments ,patient_data.id AS patient_id from test_gdx 
							join patient_data on patient_data.id = test_gdx.patient_id
							where test_gdx.del_status = '0' and test_gdx.purged = '0' and test_gdx.finished = '0' ";
				$this->db_obj->qry .=" and (test_gdx.phyName = '' || test_gdx.phyName = '0') and if(test_gdx.ordrby=".$this->authId.",1,if(test_gdx.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							
				$gdx_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry= "select 'Pachy' as testDesc,pachy.pachy_id as main_id,
							date_format(pachy.examDate,'%m-%d-%Y') as taskDate,
							date_format(pachy.examDate,'%Y%m%d') as taskDate1, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							pachy.comments,patient_data.id AS patient_id from pachy 
							join patient_data on patient_data.id = pachy.patientId
							where pachy.del_status = '0' and pachy.purged = '0' and pachy.finished = '0' ";
				$this->db_obj->qry .=" and (pachy.phyName = '' || pachy.phyName = '0') and if(pachy.ordrby=".$this->authId.",1,if(pachy.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";														
				$pachy_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry = "select 'IVFA' as testDesc,ivfa.vf_id as main_id,
							date_format(ivfa.exam_date,'%m-%d-%Y') as taskDate,
							date_format(ivfa.exam_date,'%Y%m%d') as taskDate1,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							ivfa.ivfaComments as comments,patient_data.id AS patient_id from ivfa 
							join patient_data on patient_data.id = ivfa.patient_id
							where ivfa.del_status = '0' and ivfa.purged = '0' and ivfa.finished = '0' ";
				$this->db_obj->qry .=" and (ivfa.phy = '' || ivfa.phy = '0') and if(ivfa.ordrby=".$this->authId.",1,if(ivfa.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";
				$ivfa_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry= "select 'Fundus' as testDesc,disc.disc_id as main_id,
							date_format(disc.examDate,'%m-%d-%Y') as taskDate,
							date_format(disc.examDate,'%Y%m%d') as taskDate1,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							disc.discComments as comments,patient_data.id AS patient_id from disc 
							join patient_data on patient_data.id = disc.patientId
							where disc.del_status = '0' and disc.purged = '0' and disc.finished = '0' ";
				$this->db_obj->qry .=" and (disc.phyName = '' || disc.phyName = '0') and if(disc.ordrby=".$this->authId.",1,if(disc.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";
				$disc_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry = "select 'External / Anterior' as testDesc,
							disc_external.disc_id as main_id, date_format(disc_external.examDate,'%m-%d-%Y') as taskDate,
							date_format(disc_external.examDate,'%Y%m%d') as taskDate1,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							disc_external.discComments as comments,patient_data.id AS patient_id 
							from disc_external join patient_data on patient_data.id = disc_external.patientId
							where disc_external.del_status = '0' and disc_external.purged = '0' and disc_external.finished = '0' ";
				$this->db_obj->qry .=" and (disc_external.phyName = '' || disc_external.phyName = '0') and if(disc_external.ordrby=".$this->authId.",1,if(disc_external.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";
				$discexternal_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry = "select 'Topography' as testDesc,topography.topo_id as main_id,
							date_format(topography.examDate,'%m-%d-%Y') as taskDate,
							date_format(topography.examDate,'%Y%m%d') as taskDate1,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							topography.comments,patient_data.id AS patient_id from topography 
							join patient_data on patient_data.id = topography.patientId
							where topography.del_status = '0' and topography.purged = '0' and topography.finished = '0' ";
				$this->db_obj->qry .=" and (topography.phyName = '' || topography.phyName = '0') and if(topography.ordrby=".$this->authId.",1,if(topography.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							

				$topography_arr = $this->db_obj->get_resultset_array();
								
				$this->db_obj->qry = "select 'A/Scan' as testDesc,surgical_tbl.surgical_id as main_id,
							date_format(surgical_tbl.examDate,'%m-%d-%Y') as taskDate,
							date_format(surgical_tbl.examDate,'%Y%m%d') as taskDate1,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							patient_data.id AS patient_id from surgical_tbl 
							join patient_data on patient_data.id = surgical_tbl.patient_id
							where surgical_tbl.del_status = '0' and surgical_tbl.purged = '0' and surgical_tbl.finished = '0' ";
				$this->db_obj->qry .=" and (surgical_tbl.signedById = '' || surgical_tbl.signedById = '0') and if(surgical_tbl.ordrby=".$this->authId.",1,if(surgical_tbl.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";					
				$ascan_arr = $this->db_obj->get_resultset_array();

				
				$this->db_obj->qry = "select 'B-scan' as testDesc,test_bscan.test_bscan_id as main_id,
							date_format(test_bscan.examDate,'%m-%d-%Y') as taskDate,
							date_format(test_bscan.examDate,'%Y%m%d') as taskDate1,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							test_bscan.techComments as comments,patient_data.id AS patient_id from test_bscan 
							join patient_data on patient_data.id = test_bscan.patientId
							where test_bscan.del_status = '0' and test_bscan.purged = '0' and test_bscan.finished = '0' ";
				$this->db_obj->qry .=" and (test_bscan.phyName = '' || test_bscan.phyName = '0') and if(test_bscan.ordrby=".$this->authId.",1,if(test_bscan.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							
				$test_bscan_arr = $this->db_obj->get_resultset_array();

				$this->db_obj->qry = "select 'Laboratories' as testDesc,test_labs.test_labs_id as main_id,
							date_format(test_labs.examDate,'%m-%d-%Y') as taskDate,
							date_format(test_labs.examDate,'%Y%m%d') as taskDate1,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							test_labs.techComments as comments,patient_data.id AS patient_id from test_labs  
							join patient_data on patient_data.id = test_labs.patientId
							where test_labs.del_status = '0' and test_labs.purged = '0' and test_labs.finished = '0' ";
				$this->db_obj->qry .=" and (test_labs.phyName = '' || test_labs.phyName = '0') and if(test_labs.ordrby=".$this->authId.",1,if(test_labs.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							
				$labs_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry = "select 'Test Other' as testDesc,test_other.test_other_id as main_id,
							date_format(test_other.examDate,'%m-%d-%Y') as taskDate,
							date_format(test_other.examDate,'%Y%m%d') as taskDate1, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							test_other.techComments as comments,patient_data.id AS patient_id from test_other  
							join patient_data on patient_data.id = test_other.patientId
							where test_other.del_status = '0' and test_other.purged = '0' and test_other.finished = '0' ";
				$this->db_obj->qry.=" and (test_other.phyName = '' || test_other.phyName = '0') and if(test_other.ordrby=".$this->authId.",1,if(test_other.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							
				
				$test_other_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry = "select 'Cell Count' as testDesc,test_cellcnt.test_cellcnt_id as main_id,
							date_format(test_cellcnt.examDate,'%m-%d-%Y') as taskDate,
							date_format(test_cellcnt.examDate,'%Y%m%d') as taskDate1,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name2,
							test_cellcnt.techComments as comments,patient_data.id AS patient_id from test_cellcnt  
							join patient_data on patient_data.id = test_cellcnt.patientId
							where test_cellcnt.del_status = '0' and test_cellcnt.purged = '0' and test_cellcnt.finished = '0' ";
				$this->db_obj->qry .=" and (test_cellcnt.phyName = '' || test_cellcnt.phyName = '0') and if(test_cellcnt.ordrby=".$this->authId.",1,if(test_cellcnt.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							
				$test_cellcnt_arr = $this->db_obj->get_resultset_array();
				
				$this->db_obj->qry = "select 'ICG' as testDesc,icg.icg_id as main_id,
							date_format(icg.exam_date,'%m-%d-%Y') as taskDate,
							date_format(icg.exam_date,'%Y%m%d') as taskDate1, 
							concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,
							icg.comments_icg as comments,patient_data.id AS patient_id from icg  
							join patient_data on patient_data.id = icg.patient_id
							where icg.del_status = '0' and icg.purged = '0' and icg.finished = '0'";
				$this->db_obj->qry.=" and (icg.phy = '' || icg.phy = '0') and if(icg.ordrby=".$this->authId.",1,if(icg.ordrby=0 && (patient_data.providerID=0 || patient_data.providerID=".$this->authId."),1,0))";							
				
				$icg_arr = $this->db_obj->get_resultset_array();
				
							
				$result_arr = array_merge($vf_arr,$vf_gl_arr,$nfa_arr,$oct_arr,$oct_rnfl_arr,$gdx_arr,$pachy_arr,$ivfa_arr,$disc_arr,$discexternal_arr,$topography_arr,$ascan_arr,$test_bscan_arr,$labs_arr,$test_other_arr,$test_cellcnt_arr,$icg_arr);
				
				$final_array = array();
				if($result_arr){
					$keys_array = array();
					
					foreach($result_arr as $key=>$values){
					
						$array_key = $values['taskDate1'].'-'.$values['patient_id'];
						$final_array[$array_key][] = $values;
						if(!in_array($array_key,$keys_array)){
							$keys_array[] = $array_key;
						}
					}
					
					$final_array["Keys"] = $keys_array;
				}
				return $final_array;	
					
	}
}

?>