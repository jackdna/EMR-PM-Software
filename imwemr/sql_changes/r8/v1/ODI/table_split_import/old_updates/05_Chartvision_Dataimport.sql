SET SQL_SAFE_UPDATES=0;
SET  sql_mode = '';

/* chart_vis_master */

INSERT INTO chart_vis_master 
(
patient_id
,form_id
,status_elements
,ut_elem
)
SELECT 
IFNULL(patient_id,0)
,form_id
,IFNULL(vis_statusElements,'')
,IFNULL(ut_elem,'')
FROM chart_vision
ORDER BY vis_id;



/* chart_acuity -- index1 Distance*/
INSERT INTO chart_acuity 
(
id_chart_vis_master
,exam_date
,uid
,sec_name
,sec_indx
,snellen
,ex_desc
,sel_od
,txt_od
,sel_os
,txt_os
,sel_ou
,txt_ou
)
SELECT 
cvm.id
,CASE WHEN cv.examDateDistance IS NOT NULL AND cv.examDateDistance <> '0000-00-00' THEN cv.examDateDistance
            ELSE IFNULL(cv.exam_date,'') END
,IFNULL(cv.uid,0)
,'Distance'
,'1'
,IFNULL(cv.visSnellan,'')
,IFNULL(cv.vis_dis_desc,IFNULL(cv.vis_dis_near_desc,''))
,IFNULL(cv.vis_dis_od_sel_1,'')
,IFNULL(cv.vis_dis_od_txt_1,'')
,IFNULL(cv.vis_dis_os_sel_1,'')
,IFNULL(cv.vis_dis_os_txt_1,'')
,IFNULL(cv.vis_dis_ou_sel_1,'')
,IFNULL(cv.vis_dis_ou_txt_1,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_dis_od_sel_1,'')<>'' 
OR IFNULL(cv.vis_dis_od_txt_1,'')<>''
OR IFNULL(cv.vis_dis_os_sel_1,'')<>''
OR IFNULL(cv.vis_dis_os_txt_1,'')<>''
OR IFNULL(cv.vis_dis_ou_sel_1,'')<>''
OR IFNULL(cv.vis_dis_ou_txt_1,'')<>''
OR IFNULL(cv.vis_dis_desc,IFNULL(cv.vis_dis_near_desc,''))<>''
OR IFNULL(cv.visSnellan,'')<>''
ORDER BY cv.vis_id ;



/* chart_acuity -- index2 Distance*/
INSERT INTO chart_acuity 
(
id_chart_vis_master
,exam_date
,uid
,sec_name
,sec_indx
,snellen
,ex_desc
,sel_od
,txt_od
,sel_os
,txt_os
,sel_ou
,txt_ou
)
SELECT 
cvm.id
,CASE WHEN cv.examDateDistance IS NOT NULL AND cv.examDateDistance <> '0000-00-00' THEN cv.examDateDistance
            ELSE IFNULL(cv.exam_date,'') END
,IFNULL(cv.uid,0)
,'Distance'
,'2'
,IFNULL(cv.visSnellan,'')
,IFNULL(cv.vis_dis_desc,IFNULL(cv.vis_dis_near_desc,''))
,IFNULL(cv.vis_dis_od_sel_2,'')
,IFNULL(cv.vis_dis_od_txt_2,'')
,IFNULL(cv.vis_dis_os_sel_2,'')
,IFNULL(cv.vis_dis_os_txt_2,'')
,IFNULL(cv.vis_dis_ou_sel_2,'')
,IFNULL(cv.vis_dis_ou_txt_2,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_dis_od_sel_2,'')<>'' 
OR IFNULL(cv.vis_dis_od_txt_2,'')<>''
OR IFNULL(cv.vis_dis_os_sel_2,'')<>''
OR IFNULL(cv.vis_dis_os_txt_2,'')<>''
OR IFNULL(cv.vis_dis_ou_sel_2,'')<>''
OR IFNULL(cv.vis_dis_ou_txt_2,'')<>''
OR IFNULL(cv.vis_dis_desc,IFNULL(cv.vis_dis_near_desc,''))<>''
OR IFNULL(cv.visSnellan,'')<>''
ORDER BY cv.vis_id ;


/* chart_acuity -- index3 Distance*/
INSERT INTO chart_acuity 
(
id_chart_vis_master
,exam_date
,uid
,sec_name
,sec_indx
,snellen
,ex_desc
,sel_od
,txt_od
,sel_os
,txt_os
,sel_ou
,txt_ou
)
SELECT 
cvm.id
,CASE WHEN cv.examDateDistance IS NOT NULL AND cv.examDateDistance <> '0000-00-00' THEN cv.examDateDistance
            ELSE IFNULL(cv.exam_date,'') END
,IFNULL(cv.uid,0)
,'Ad. Acuity'
,'3'
,''
,IFNULL(cv.vis_dis_act_3,'')
,IFNULL(cv.vis_dis_od_sel_3,'')
,IFNULL(cv.vis_dis_od_txt_3,'')
,IFNULL(cv.vis_dis_os_sel_3,'')
,IFNULL(cv.vis_dis_os_txt_3,'')
,IFNULL(cv.vis_dis_ou_sel_3,'')
,IFNULL(cv.vis_dis_ou_txt_3,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_dis_od_sel_3,'')<>'' 
OR IFNULL(cv.vis_dis_od_txt_3,'')<>''
OR IFNULL(cv.vis_dis_os_sel_3,'')<>''
OR IFNULL(cv.vis_dis_os_txt_3,'')<>''
OR IFNULL(cv.vis_dis_ou_sel_3,'')<>''
OR IFNULL(cv.vis_dis_ou_txt_3,'')<>''
OR IFNULL(cv.vis_dis_act_3,'')<>''
ORDER BY cv.vis_id ;


/* chart_acuity -- index4 Distance*/
INSERT INTO chart_acuity 
(
id_chart_vis_master
,exam_date
,uid
,sec_name
,sec_indx
,snellen
,ex_desc
,sel_od
,txt_od
,sel_os
,txt_os
,sel_ou
,txt_ou
)
SELECT 
cvm.id
,CASE WHEN cv.examDateDistance IS NOT NULL AND cv.examDateDistance <> '0000-00-00' THEN cv.examDateDistance
            ELSE IFNULL(cv.exam_date,'') END
,IFNULL(cv.uid,0)
,'Distance'
,'4'
,''
,IFNULL(cv.vis_dis_act_4,'')
,IFNULL(cv.vis_dis_od_sel_4,'')
,IFNULL(cv.vis_dis_od_txt_4,'')
,IFNULL(cv.vis_dis_od_sel_4,'')
,IFNULL(cv.vis_dis_os_txt_4,'')
,IFNULL(cv.vis_dis_od_sel_4,'')
,IFNULL(cv.vis_dis_ou_txt_4,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_dis_od_sel_4,'')<>'' 
OR IFNULL(cv.vis_dis_od_txt_4,'')<>''
OR IFNULL(cv.vis_dis_os_txt_4,'')<>''
OR IFNULL(cv.vis_dis_ou_txt_4,'')<>''
OR IFNULL(cv.vis_dis_act_4,'')<>''
ORDER BY cv.vis_id ;



/* chart_acuity -- index1 Near*/
INSERT INTO chart_acuity 
(
id_chart_vis_master
,exam_date
,uid
,sec_name
,sec_indx
,snellen
,ex_desc
,sel_od
,txt_od
,sel_os
,txt_os
,sel_ou
,txt_ou
)
SELECT 
cvm.id
,IFNULL(cv.exam_date,'')
,IFNULL(cv.uid,0)
,'Near'
,'1'
,IFNULL(cv.visSnellan_near,'')
,IFNULL(cv.vis_near_desc,IFNULL(cv.vis_dis_near_desc,''))
,IFNULL(cv.vis_near_od_sel_1,'')
,IFNULL(cv.vis_near_od_txt_1,'')
,IFNULL(cv.vis_near_os_sel_1,'')
,IFNULL(cv.vis_near_os_txt_1,'')
,IFNULL(cv.vis_near_ou_sel_1,'')
,IFNULL(cv.vis_near_ou_txt_1,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_near_od_sel_1,'')<>'' 
OR IFNULL(cv.vis_near_od_txt_1,'')<>''
OR IFNULL(cv.vis_near_os_sel_1,'')<>''
OR IFNULL(cv.vis_near_os_txt_1,'')<>''
OR IFNULL(cv.vis_near_ou_sel_1,'')<>''
OR IFNULL(cv.vis_near_ou_txt_1,'')<>''
OR IFNULL(cv.vis_near_desc,IFNULL(cv.vis_dis_near_desc,''))<>''
OR IFNULL(cv.visSnellan_near,'')<>''
ORDER BY cv.vis_id ;



/* chart_acuity -- index2 Near*/
INSERT INTO chart_acuity 
(
id_chart_vis_master
,exam_date
,uid
,sec_name
,sec_indx
,snellen
,ex_desc
,sel_od
,txt_od
,sel_os
,txt_os
,sel_ou
,txt_ou
)
SELECT 
cvm.id
,IFNULL(cv.exam_date,'')
,IFNULL(cv.uid,0)
,'Near'
,'2'
,IFNULL(cv.visSnellan_near,'')
,IFNULL(cv.vis_near_desc,IFNULL(cv.vis_dis_near_desc,''))
,IFNULL(cv.vis_near_od_sel_2,'')
,IFNULL(cv.vis_near_od_txt_2,'')
,IFNULL(cv.vis_near_os_sel_2,'')
,IFNULL(cv.vis_near_os_txt_2,'')
,IFNULL(cv.vis_near_ou_sel_2,'')
,IFNULL(cv.vis_near_ou_txt_2,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_near_od_sel_2,'')<>'' 
OR IFNULL(cv.vis_near_od_txt_2,'')<>''
OR IFNULL(cv.vis_near_os_sel_2,'')<>''
OR IFNULL(cv.vis_near_os_txt_2,'')<>''
OR IFNULL(cv.vis_near_ou_sel_2,'')<>''
OR IFNULL(cv.vis_near_ou_txt_2,'')<>''
OR IFNULL(cv.vis_near_desc,IFNULL(cv.vis_dis_near_desc,''))<>''
OR IFNULL(cv.visSnellan_near,'')<>''
ORDER BY cv.vis_id ;


/* chart_ak */
INSERT INTO chart_ak 
(
id_chart_vis_master,
exam_date,
uid,
k_od,
slash_od,
x_od,
k_os,
slash_os,
x_os,
k_type,
ex_desc
)
SELECT 
cvm.id
,IFNULL(cv.examDateARAK,IFNULL(cv.exam_date,''))
,cv.uid
,IFNULL(cv.vis_ak_od_k,'')
,IFNULL(cv.vis_ak_od_slash,'')
,IFNULL(cv.vis_ak_od_x,'')
,IFNULL(cv.vis_ak_os_k,'')
,IFNULL(cv.vis_ak_os_slash,'')
,IFNULL(cv.vis_ak_os_x,'')
,IFNULL(cv.vis_ktype,'')
,IFNULL(cv.vis_dis_near_desc,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_ak_od_k,'')<>''
OR IFNULL(cv.vis_ak_od_slash,'')<>''
OR IFNULL(cv.vis_ak_od_x,'')<>''
OR IFNULL(cv.vis_ak_os_k,'')<>''
OR IFNULL(cv.vis_ak_os_slash,'')<>''
OR IFNULL(cv.vis_ak_os_x,'')<>''
OR IFNULL(cv.vis_ktype,'')<>''
OR IFNULL(cv.vis_dis_near_desc,'')<>''
ORDER BY cv.vis_id ;



/* chart_sca - AR*/
INSERT INTO chart_sca 
(
id_chart_vis_master,
exam_date,
uid,
sec_name,
s_od,
c_od,
a_od,
sel_od,
s_os,
c_os,
a_os,
sel_os,
ex_desc,
ar_ref_place
)
SELECT 
cvm.id
,IFNULL(cv.exam_date,'')
,cv.uid
,'AR'
,IFNULL(cv.vis_ar_od_s,'')
,IFNULL(cv.vis_ar_od_c,'')
,IFNULL(cv.vis_ar_od_a,'')
,IFNULL(cv.vis_ar_od_sel_1,'')
,IFNULL(cv.vis_ar_os_s,'')
,IFNULL(cv.vis_ar_os_c,'')
,IFNULL(cv.vis_ar_os_a,'')
,IFNULL(cv.vis_ar_os_sel_1,IFNULL(cv.vis_ar_od_sel_1,''))
,IFNULL(cv.vis_ar_ak_desc,'')
,IFNULL(cv.vis_ar_ref_place,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_ar_od_s,'')<>''
OR IFNULL(cv.vis_ar_od_c,'')<>''
OR IFNULL(cv.vis_ar_od_a,'')<>''
OR IFNULL(cv.vis_ar_os_s,'')<>''
OR IFNULL(cv.vis_ar_os_c,'')<>''
OR IFNULL(cv.vis_ar_os_a,'')<>''
OR IFNULL(cv.vis_ar_ref_place,'')<>''
OR IFNULL(cv.vis_ar_ak_desc,'')<>''
ORDER BY cv.vis_id ;


/* chart_sca - ARC*/
INSERT INTO chart_sca 
(
id_chart_vis_master,
exam_date,
uid,
sec_name,
s_od,
c_od,
a_od,
sel_od,
s_os,
c_os,
a_os,
sel_os,
ex_desc,
ar_ref_place
)
SELECT 
cvm.id
,IFNULL(cv.exam_date,'')
,cv.uid
,'ARC'
,IFNULL(cv.visCycArOdS,'')
,IFNULL(cv.visCycArOdC,'')
,IFNULL(cv.visCycArOdA,'')
,IFNULL(cv.visCycArOdSel1,'')
,IFNULL(cv.visCycArOsS,'')
,IFNULL(cv.visCycArOsC,'')
,IFNULL(cv.visCycArOsA,'')
,IFNULL(cv.visCycArOsSel1,IFNULL(cv.visCycArOdSel1,''))
,IFNULL(cv.visCycArDesc,'')
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.visCycArOdS,'')<>''
OR IFNULL(cv.visCycArOdC,'')<>''
OR IFNULL(cv.visCycArOdA,'')<>''
OR IFNULL(cv.visCycArOdSel1,'')<>''
OR IFNULL(cv.visCycArOsS,'')<>''
OR IFNULL(cv.visCycArOsC,'')<>''
OR IFNULL(cv.visCycArOsA,'')<>''
OR IFNULL(cv.visCycArOsSel1,'')<>''
OR IFNULL(cv.visCycArDesc,'')<>''
ORDER BY cv.vis_id ;



/* chart_sca - RETINOSCOPY*/
INSERT INTO chart_sca 
(
id_chart_vis_master,
exam_date,
uid,
sec_name,
s_od,
c_od,
a_od,
s_os,
c_os,
a_os,
sel_od,
sel_os
)
SELECT 
cvm.id
,IFNULL(cv.exam_date,'')
,cv.uid
,'RETINOSCOPY'
,IFNULL(cv.vis_exo_od_s,'')
,IFNULL(cv.vis_exo_od_c,'')
,IFNULL(cv.vis_exo_od_a,'')
,IFNULL(cv.vis_exo_os_s,'')
,IFNULL(cv.vis_exo_os_c,'')
,IFNULL(cv.vis_exo_os_a,'')
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_exo_od_s,'')<>''
OR IFNULL(cv.vis_exo_od_c,'')<>''
OR IFNULL(cv.vis_exo_od_a,'')<>''
OR IFNULL(cv.vis_exo_os_s,'')<>''
OR IFNULL(cv.vis_exo_os_c,'')<>''
OR IFNULL(cv.vis_exo_os_a,'')<>''
ORDER BY cv.vis_id ;


/* chart_sca - CYCLOPLEGIC*/
INSERT INTO chart_sca 
(
id_chart_vis_master,
exam_date,
uid,
sec_name,
s_od,
c_od,
a_od,
s_os,
c_os,
a_os,
sel_od,
sel_os
)
SELECT 
cvm.id
,IFNULL(cv.exam_date,'')
,cv.uid
,'CYCLOPLEGIC RETINO'
,IFNULL(cv.visCycloOdS,'')
,IFNULL(cv.visCycloOdC,'')
,IFNULL(cv.visCycloOdA,'')
,IFNULL(cv.visCycloOsS,'')
,IFNULL(cv.visCycloOsC,'')
,IFNULL(cv.visCycloOsA,'')
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.visCycloOdS,'')<>''
OR IFNULL(cv.visCycloOdC,'')<>''
OR IFNULL(cv.visCycloOdA,'')<>''
OR IFNULL(cv.visCycloOsS,'')<>''
OR IFNULL(cv.visCycloOsC,'')<>''
OR IFNULL(cv.visCycloOsA,'')<>''
ORDER BY cv.vis_id ;

/* chart_exo - EXOPHTHALMOMETER*/
INSERT INTO chart_exo 
(
id_chart_vis_master,
exam_date,
uid,
pd,
pd_od,
pd_os
)
SELECT 
cvm.id
,IFNULL(cv.exam_date,'')
,cv.uid
,IFNULL(cv.vis_ret_pd,'')
,IFNULL(cv.vis_ret_pd_od,'')
,IFNULL(cv.vis_ret_pd_os,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_ret_pd,'')<>''
OR IFNULL(cv.vis_ret_pd_od,'')<>''
OR IFNULL(cv.vis_ret_pd_os,'')<>''
ORDER BY cv.vis_id ;



/* chart_bat - BAT*/
INSERT INTO chart_bat 
(
id_chart_vis_master,
exam_date,
uid,
nl_od,
l_od,
m_od,
h_od,
nl_os,
l_os,
m_os,
h_os,
nl_ou,
l_ou,
m_ou,
h_ou,
ex_desc
)
SELECT 
cvm.id
,IFNULL(cv.exam_date,'')
,cv.uid
,IFNULL(cv.vis_bat_nl_od,'')
,IFNULL(cv.vis_bat_low_od,'')
,IFNULL(cv.vis_bat_med_od,'')
,IFNULL(cv.vis_bat_high_od,'')
,IFNULL(cv.vis_bat_nl_os,'')
,IFNULL(cv.vis_bat_low_os,'')
,IFNULL(cv.vis_bat_med_os,'')
,IFNULL(cv.vis_bat_high_os,'')
,IFNULL(cv.vis_bat_nl_ou,'')
,IFNULL(cv.vis_bat_low_ou,'')
,IFNULL(cv.vis_bat_med_ou,'')
,IFNULL(cv.vis_bat_high_ou,'')
,IFNULL(cv.vis_bat_desc,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_bat_nl_od,'')<>''
OR IFNULL(cv.vis_bat_low_od,'')<>''
OR IFNULL(cv.vis_bat_med_od,'')<>''
OR IFNULL(cv.vis_bat_high_od,'')<>''
OR IFNULL(cv.vis_bat_nl_os,'')<>''
OR IFNULL(cv.vis_bat_low_os,'')<>''
OR IFNULL(cv.vis_bat_med_os,'')<>''
OR IFNULL(cv.vis_bat_high_os,'')<>''
OR IFNULL(cv.vis_bat_nl_ou,'')<>''
OR IFNULL(cv.vis_bat_low_ou,'')<>''
OR IFNULL(cv.vis_bat_med_ou,'')<>''
OR IFNULL(cv.vis_bat_high_ou,'')<>''
OR IFNULL(cv.vis_bat_desc,'')<>''
ORDER BY cv.vis_id ;



/* chart_pam - PAM*/
INSERT INTO chart_pam 
(
id_chart_vis_master,
exam_date,
uid,
txt1_od,
txt2_od,
txt1_os,
txt2_os,
txt1_ou,
txt2_ou,
sel1,
sel2,
ex_desc,
pam
)
SELECT 
cvm.id
,IFNULL(cv.exam_date,'')
,cv.uid
,IFNULL(cv.vis_pam_od_txt_1,'')
,IFNULL(cv.vis_pam_od_txt_2,'')
,IFNULL(cv.vis_pam_os_txt_1,'')
,IFNULL(cv.vis_pam_os_txt_2,'')
,IFNULL(cv.vis_pam_ou_txt_1,'')
,IFNULL(cv.vis_pam_ou_txt_2,'')
,IFNULL(cv.vis_pam_od_sel_1,'')
,IFNULL(cv.vis_pam_od_sel_2,'')
,IFNULL(cv.vis_pam_desc,'')
,IFNULL(cv.visPam,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_pam_od_txt_1,'')<>''
OR IFNULL(cv.vis_pam_od_txt_2,'')<>''
OR IFNULL(cv.vis_pam_os_txt_1,'')<>''
OR IFNULL(cv.vis_pam_os_txt_2,'')<>''
OR IFNULL(cv.vis_pam_ou_txt_1,'')<>''
OR IFNULL(cv.vis_pam_ou_txt_2,'')<>''
OR IFNULL(cv.vis_pam_od_sel_1,'')<>''
OR IFNULL(cv.vis_pam_od_sel_2,'')<>''
OR IFNULL(cv.vis_pam_desc,'')<>''
OR IFNULL(cv.visPam,'')<>''
ORDER BY cv.vis_id ;


/*Update pc_mr */
UPDATE chart_pc_mr pm INNER JOIN chart_vis_master vm
ON pm.form_id = vm.form_id
SET pm.id_chart_vis_master = vm.id;


/* chart_pc_mr -- PC1*/
INSERT INTO chart_pc_mr 
(
id_chart_vis_master,
exam_date,
ex_type,
ex_number,
pc_distance,
pc_near,
ex_desc,
prism_desc,
uid
)
SELECT 
cvm.id
,CASE WHEN cv.examDatePC IS NOT NULL AND cv.examDatePC <> '0000-00-00' THEN cv.examDatePC
            ELSE IFNULL(cv.exam_date,'') END
,'PC'
,'1'
,CASE WHEN LOCATE('1',cv.pc_distance)>0 THEN 1 ELSE 0 END	
,CASE WHEN cv.pc_near = 'Near' THEN 1 ELSE 0 END
,IFNULL(cv.vis_pc_desc,'')
,IFNULL(cv.visPcPrismDesc_1,'')
,IFNULL(cv.uid,0)
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_pc_od_s,'')<>'' 
OR IFNULL(cv.vis_pc_os_s,'')<>''
OR IFNULL(cv.vis_pc_od_c,'')<>''
OR IFNULL(cv.vis_pc_os_c,'')<>''
OR IFNULL(cv.vis_pc_od_a,'')<>''
OR IFNULL(cv.vis_pc_os_a,'')<>''
OR IFNULL(cv.vis_pc_od_add,'')<>''
OR IFNULL(cv.vis_pc_os_add,'')<>''
OR IFNULL(cv.vis_pc_od_p,'')<>''
OR IFNULL(cv.vis_pc_os_p,'')<>''
OR IFNULL(cv.vis_pc_od_prism,'')<>''
OR IFNULL(cv.vis_pc_os_prism,'')<>''
OR IFNULL(cv.vis_pc_od_slash,'')<>''
OR IFNULL(cv.vis_pc_os_slash,'')<>''
OR IFNULL(cv.vis_pc_od_sel_1,'')<>''
OR IFNULL(cv.vis_pc_os_sel_1,'')<>''
OR IFNULL(cv.vis_pc_od_sel_2,'')<>''
OR IFNULL(cv.vis_pc_os_sel_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_s,'')<>''
OR IFNULL(cv.vis_pc_os_overref_s,'')<>''
OR IFNULL(cv.vis_pc_od_overref_c,'')<>''
OR IFNULL(cv.vis_pc_os_overref_c,'')<>''
OR IFNULL(cv.vis_pc_od_overref_v,'')<>''
OR IFNULL(cv.vis_pc_os_overref_v,'')<>''
OR IFNULL(cv.vis_pc_od_overref_a,'')<>''
OR IFNULL(cv.vis_pc_os_overref_a,'')<>''
OR IFNULL(cv.vis_pc_od_near_txt,'')<>''
OR IFNULL(cv.vis_pc_os_near_txt,'')<>''
OR IFNULL(cv.pc_near,'')<>''
OR IFNULL(cv.vis_pc_desc,'')<>''
OR IFNULL(cv.visPcPrismDesc_1,'')<>''
ORDER BY cv.vis_id ;


/* chart_pc_mr_values -- PC-OD1*/
INSERT INTO chart_pc_mr_values 
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
ovr_s,
ovr_c,
ovr_v,
ovr_a,
txt_1,
txt_2,
sel2v
)
SELECT 
MAX(cpm.id)
,'OD'
,IFNULL(cv.vis_pc_od_s,'')
,IFNULL(cv.vis_pc_od_c,'')
,IFNULL(cv.vis_pc_od_a,'')
,IFNULL(cv.vis_pc_od_add,'')
,IFNULL(cv.vis_pc_od_p,'')
,IFNULL(cv.vis_pc_od_prism,'')
,IFNULL(cv.vis_pc_od_slash,'')
,IFNULL(cv.vis_pc_od_sel_1,'')
,IFNULL(cv.vis_pc_od_sel_2,'')
,IFNULL(cv.vis_pc_od_overref_s,'')
,IFNULL(cv.vis_pc_od_overref_c,'')
,IFNULL(cv.vis_pc_od_overref_v,'')
,IFNULL(cv.vis_pc_od_overref_a,'')
,IFNULL(cv.vis_pc_od_near_txt,'')
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_pc_od_s,'')<>'' 
OR IFNULL(cv.vis_pc_od_c,'')<>''
OR IFNULL(cv.vis_pc_od_a,'')<>''
OR IFNULL(cv.vis_pc_od_add,'')<>''
OR IFNULL(cv.vis_pc_od_p,'')<>''
OR IFNULL(cv.vis_pc_od_prism,'')<>''
OR IFNULL(cv.vis_pc_od_slash,'')<>''
OR IFNULL(cv.vis_pc_od_sel_1,'')<>''
OR IFNULL(cv.vis_pc_od_sel_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_s,'')<>''
OR IFNULL(cv.vis_pc_od_overref_c,'')<>''
OR IFNULL(cv.vis_pc_od_overref_v,'')<>''
OR IFNULL(cv.vis_pc_od_overref_a,'')<>''
OR IFNULL(cv.vis_pc_od_near_txt,'')<>''
GROUP BY cpm.id_chart_vis_master 
ORDER BY cv.vis_id ;


/* chart_pc_mr_values -- PC-OS1*/
INSERT INTO chart_pc_mr_values 
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
ovr_s,
ovr_c,
ovr_v,
ovr_a,
txt_1,
txt_2,
sel2v
)
SELECT 
MAX(cpm.id)
,'OS'
,IFNULL(cv.vis_pc_os_s,'') 
,IFNULL(cv.vis_pc_os_c,'')
,IFNULL(cv.vis_pc_os_a,'')
,IFNULL(cv.vis_pc_os_add,'')
,IFNULL(cv.vis_pc_os_p,'')
,IFNULL(cv.vis_pc_os_prism,'')
,IFNULL(cv.vis_pc_os_slash,'')
,IFNULL(cv.vis_pc_os_sel_1,'')
,IFNULL(cv.vis_pc_os_sel_2,'')
,IFNULL(cv.vis_pc_os_overref_s,'')
,IFNULL(cv.vis_pc_os_overref_c,'')
,IFNULL(cv.vis_pc_os_overref_v,'')
,IFNULL(cv.vis_pc_os_overref_a,'')
,IFNULL(cv.vis_pc_os_near_txt,'')
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_pc_os_s,'')<>'' 
OR IFNULL(cv.vis_pc_os_c,'')<>''
OR IFNULL(cv.vis_pc_os_a,'')<>''
OR IFNULL(cv.vis_pc_os_add,'')<>''
OR IFNULL(cv.vis_pc_os_p,'')<>''
OR IFNULL(cv.vis_pc_os_prism,'')<>''
OR IFNULL(cv.vis_pc_os_slash,'')<>''
OR IFNULL(cv.vis_pc_os_sel_1,'')<>''
OR IFNULL(cv.vis_pc_os_sel_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_s,'')<>''
OR IFNULL(cv.vis_pc_os_overref_c,'')<>''
OR IFNULL(cv.vis_pc_os_overref_v,'')<>''
OR IFNULL(cv.vis_pc_os_overref_a,'')<>''
OR IFNULL(cv.vis_pc_os_near_txt,'')<>''
GROUP BY cpm.id_chart_vis_master 
ORDER BY cv.vis_id ;


/* chart_pc_mr -- PC2*/
INSERT INTO chart_pc_mr 
(
id_chart_vis_master,
exam_date,
ex_type,
ex_number,
pc_distance,
pc_near,
ex_desc,
prism_desc,
uid
)
SELECT 
cvm.id
,CASE WHEN cv.examDatePC IS NOT NULL AND cv.examDatePC <> '0000-00-00' THEN cv.examDatePC
            ELSE IFNULL(cv.exam_date,'') END
,'PC'
,'2'
,CASE WHEN LOCATE('2',cv.pc_distance)>0 THEN 1 ELSE 0 END	
,CASE WHEN cv.pc_near_2 = 'Near' THEN 1 ELSE 0 END
,IFNULL(cv.vis_pc_desc_2,'')
,IFNULL(cv.visPcPrismDesc_2,'')
,IFNULL(cv.uid,0)
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_pc_od_s_2,'')<>'' 
OR IFNULL(cv.vis_pc_os_s_2,'')<>''
OR IFNULL(cv.vis_pc_od_c_2,'')<>''
OR IFNULL(cv.vis_pc_os_c_2,'')<>''
OR IFNULL(cv.vis_pc_od_a_2,'')<>''
OR IFNULL(cv.vis_pc_os_a_2,'')<>''
OR IFNULL(cv.vis_pc_od_add_2,'')<>''
OR IFNULL(cv.vis_pc_os_add_2,'')<>''
OR IFNULL(cv.vis_pc_od_p_2,'')<>''
OR IFNULL(cv.vis_pc_os_p_2,'')<>''
OR IFNULL(cv.vis_pc_od_prism_2,'')<>''
OR IFNULL(cv.vis_pc_os_prism_2,'')<>''
OR IFNULL(cv.vis_pc_od_slash_2,'')<>''
OR IFNULL(cv.vis_pc_os_slash_2,'')<>''
OR IFNULL(cv.vis_pc_od_sel_1_2,'')<>''
OR IFNULL(cv.vis_pc_os_sel_1_2,'')<>''
OR IFNULL(cv.vis_pc_od_sel_2_2,'')<>''
OR IFNULL(cv.vis_pc_os_sel_2_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_s_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_s_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_c_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_c_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_v_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_v_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_a_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_a_2,'')<>''
OR IFNULL(cv.vis_pc_od_near_txt_2,'')<>''
OR IFNULL(cv.vis_pc_os_near_txt_2,'')<>''
OR IFNULL(cv.pc_near_2,'')<>''
OR IFNULL(cv.vis_pc_desc_2,'')<>''
OR IFNULL(cv.visPcPrismDesc_2,'')<>''
ORDER BY cv.vis_id ;

/* chart_pc_mr_values -- PC-OD2*/
INSERT INTO chart_pc_mr_values 
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
ovr_s,
ovr_c,
ovr_v,
ovr_a,
txt_1,
txt_2,
sel2v
)
SELECT 
MAX(cpm.id)
,'OD'
,IFNULL(cv.vis_pc_od_s_2,'') 
,IFNULL(cv.vis_pc_od_c_2,'')
,IFNULL(cv.vis_pc_od_a_2,'')
,IFNULL(cv.vis_pc_od_add_2,'')
,IFNULL(cv.vis_pc_od_p_2,'')
,IFNULL(cv.vis_pc_od_prism_2,'')
,IFNULL(cv.vis_pc_od_slash_2,'')
,IFNULL(cv.vis_pc_od_sel_1_2,'')
,IFNULL(cv.vis_pc_od_sel_2_2,'')
,IFNULL(cv.vis_pc_od_overref_s_2,'')
,IFNULL(cv.vis_pc_od_overref_c_2,'')
,IFNULL(cv.vis_pc_od_overref_v_2,'')
,IFNULL(cv.vis_pc_od_overref_a_2,'')
,IFNULL(cv.vis_pc_od_near_txt_2,'')
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_pc_od_s_2,'')<>'' 
OR IFNULL(cv.vis_pc_od_c_2,'')<>''
OR IFNULL(cv.vis_pc_od_a_2,'')<>''
OR IFNULL(cv.vis_pc_od_add_2,'')<>''
OR IFNULL(cv.vis_pc_od_p_2,'')<>''
OR IFNULL(cv.vis_pc_od_prism_2,'')<>''
OR IFNULL(cv.vis_pc_od_slash_2,'')<>''
OR IFNULL(cv.vis_pc_od_sel_1_2,'')<>''
OR IFNULL(cv.vis_pc_od_sel_2_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_s_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_c_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_v_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_a_2,'')<>''
OR IFNULL(cv.vis_pc_od_near_txt_2,'')<>''
GROUP BY cpm.id_chart_vis_master 
ORDER BY cv.vis_id ;

/* chart_pc_mr_values -- PC-OS2*/
INSERT INTO chart_pc_mr_values 
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
ovr_s,
ovr_c,
ovr_v,
ovr_a,
txt_1,
txt_2,
sel2v
)
SELECT 
MAX(cpm.id)
,'OS'
,IFNULL(cv.vis_pc_os_s_2,'') 
,IFNULL(cv.vis_pc_os_c_2,'')
,IFNULL(cv.vis_pc_os_a_2,'')
,IFNULL(cv.vis_pc_os_add_2,'')
,IFNULL(cv.vis_pc_os_p_2,'')
,IFNULL(cv.vis_pc_os_prism_2,'')
,IFNULL(cv.vis_pc_os_slash_2,'')
,IFNULL(cv.vis_pc_os_sel_1_2,'')
,IFNULL(cv.vis_pc_os_sel_2_2,'')
,IFNULL(cv.vis_pc_os_overref_s_2,'')
,IFNULL(cv.vis_pc_os_overref_c_2,'')
,IFNULL(cv.vis_pc_os_overref_v_2,'')
,IFNULL(cv.vis_pc_os_overref_a_2,'')
,IFNULL(cv.vis_pc_os_near_txt_2,'')
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_pc_os_s_2,'')<>'' 
OR IFNULL(cv.vis_pc_os_c_2,'')<>''
OR IFNULL(cv.vis_pc_os_a_2,'')<>''
OR IFNULL(cv.vis_pc_os_add_2,'')<>''
OR IFNULL(cv.vis_pc_os_p_2,'')<>''
OR IFNULL(cv.vis_pc_os_prism_2,'')<>''
OR IFNULL(cv.vis_pc_os_slash_2,'')<>''
OR IFNULL(cv.vis_pc_os_sel_1_2,'')<>''
OR IFNULL(cv.vis_pc_os_sel_2_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_s_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_c_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_v_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_a_2,'')<>''
OR IFNULL(cv.vis_pc_os_near_txt_2,'')<>''
GROUP BY cpm.id_chart_vis_master 
ORDER BY cv.vis_id ;




/* chart_pc_mr -- PC3*/
INSERT INTO chart_pc_mr 
(
id_chart_vis_master,
exam_date,
ex_type,
ex_number,
pc_distance,
pc_near,
ex_desc,
prism_desc,
uid
)
SELECT 
cvm.id
,CASE WHEN cv.examDatePC IS NOT NULL AND cv.examDatePC <> '0000-00-00' THEN cv.examDatePC
            ELSE IFNULL(cv.exam_date,'') END
,'PC'
,'3'
,CASE WHEN LOCATE('3',cv.pc_distance)>0 THEN 1 ELSE 0 END	
,CASE WHEN cv.pc_near_3 = 'Near' THEN 1 ELSE 0 END
,IFNULL(cv.vis_pc_desc_3,'')
,IFNULL(cv.visPcPrismDesc_3,'')
,IFNULL(cv.uid,0)
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_pc_od_s_3,'')<>'' 
OR IFNULL(cv.vis_pc_os_s_3,'')<>''
OR IFNULL(cv.vis_pc_od_c_3,'')<>''
OR IFNULL(cv.vis_pc_os_c_3,'')<>''
OR IFNULL(cv.vis_pc_od_a_3,'')<>''
OR IFNULL(cv.vis_pc_os_a_3,'')<>''
OR IFNULL(cv.vis_pc_od_add_3,'')<>''
OR IFNULL(cv.vis_pc_os_add_3,'')<>''
OR IFNULL(cv.vis_pc_od_p_3,'')<>''
OR IFNULL(cv.vis_pc_os_p_3,'')<>''
OR IFNULL(cv.vis_pc_od_prism_3,'')<>''
OR IFNULL(cv.vis_pc_os_prism_3,'')<>''
OR IFNULL(cv.vis_pc_od_slash_3,'')<>''
OR IFNULL(cv.vis_pc_os_slash_3,'')<>''
OR IFNULL(cv.vis_pc_od_sel_1_3,'')<>''
OR IFNULL(cv.vis_pc_os_sel_1_3,'')<>''
OR IFNULL(cv.vis_pc_od_sel_2_3,'')<>''
OR IFNULL(cv.vis_pc_os_sel_2_3,'')<>''
OR IFNULL(cv.vis_pc_od_overref_s_3,'')<>''
OR IFNULL(cv.vis_pc_os_overref_s_3,'')<>''
OR IFNULL(cv.vis_pc_od_overref_c_3,'')<>''
OR IFNULL(cv.vis_pc_os_overref_c_3,'')<>''
OR IFNULL(cv.vis_pc_od_overref_v_3,'')<>''
OR IFNULL(cv.vis_pc_os_overref_v_3,'')<>''
OR IFNULL(cv.vis_pc_od_overref_a_3,'')<>''
OR IFNULL(cv.vis_pc_os_overref_a_3,'')<>''
OR IFNULL(cv.vis_pc_od_near_txt_3,'')<>''
OR IFNULL(cv.vis_pc_os_near_txt_3,'')<>''
OR IFNULL(cv.pc_near_3,'')<>''
OR IFNULL(cv.vis_pc_desc_3,'')<>''
OR IFNULL(cv.visPcPrismDesc_3,'')<>''
ORDER BY cv.vis_id ;


/* chart_pc_mr_values -- PC-OD3*/
INSERT INTO chart_pc_mr_values 
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
ovr_s,
ovr_c,
ovr_v,
ovr_a,
txt_1,
txt_2,
sel2v
)
SELECT 
MAX(cpm.id)
,'OD'
,IFNULL(cv.vis_pc_od_s_3,'') 
,IFNULL(cv.vis_pc_od_c_3,'')
,IFNULL(cv.vis_pc_od_a_3,'')
,IFNULL(cv.vis_pc_od_add_3,'')
,IFNULL(cv.vis_pc_od_p_3,'')
,IFNULL(cv.vis_pc_od_prism_3,'')
,IFNULL(cv.vis_pc_od_slash_3,'')
,IFNULL(cv.vis_pc_od_sel_1_3,'')
,IFNULL(cv.vis_pc_od_sel_2_3,'')
,IFNULL(cv.vis_pc_od_overref_s_3,'')
,IFNULL(cv.vis_pc_od_overref_c_3,'')
,IFNULL(cv.vis_pc_od_overref_v_3,'')
,IFNULL(cv.vis_pc_od_overref_a_3,'')
,IFNULL(cv.vis_pc_od_near_txt_3,'')
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_pc_od_s_3,'')<>'' 
OR IFNULL(cv.vis_pc_od_c_3,'')<>''
OR IFNULL(cv.vis_pc_od_a_3,'')<>''
OR IFNULL(cv.vis_pc_od_add_3,'')<>''
OR IFNULL(cv.vis_pc_od_p_3,'')<>''
OR IFNULL(cv.vis_pc_od_prism_3,'')<>''
OR IFNULL(cv.vis_pc_od_slash_3,'')<>''
OR IFNULL(cv.vis_pc_od_sel_1_3,'')<>''
OR IFNULL(cv.vis_pc_od_sel_2_3,'')<>''
OR IFNULL(cv.vis_pc_od_overref_s_3,'')<>''
OR IFNULL(cv.vis_pc_od_overref_c_3,'')<>''
OR IFNULL(cv.vis_pc_od_overref_v_3,'')<>''
OR IFNULL(cv.vis_pc_od_overref_a_3,'')<>''
OR IFNULL(cv.vis_pc_od_near_txt_3,'')<>''
GROUP BY cpm.id_chart_vis_master 
ORDER BY cv.vis_id ;

/* chart_pc_mr_values -- PC-OS3*/
INSERT INTO chart_pc_mr_values 
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
ovr_s,
ovr_c,
ovr_v,
ovr_a,
txt_1,
txt_2,
sel2v
)
SELECT 
MAX(cpm.id)
,'OS'
,IFNULL(cv.vis_pc_os_s_3,'') 
,IFNULL(cv.vis_pc_os_c_3,'')
,IFNULL(cv.vis_pc_os_a_3,'')
,IFNULL(cv.vis_pc_os_add_3,'')
,IFNULL(cv.vis_pc_os_p_3,'')
,IFNULL(cv.vis_pc_os_prism_3,'')
,IFNULL(cv.vis_pc_os_slash_3,'')
,IFNULL(cv.vis_pc_os_sel_1_3,'')
,IFNULL(cv.vis_pc_os_sel_2_3,'')
,IFNULL(cv.vis_pc_os_overref_s_3,'')
,IFNULL(cv.vis_pc_os_overref_c_3,'')
,IFNULL(cv.vis_pc_os_overref_v_3,'')
,IFNULL(cv.vis_pc_os_overref_a_3,'')
,IFNULL(cv.vis_pc_os_near_txt_3,'')
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_pc_os_s_3,'')<>'' 
OR IFNULL(cv.vis_pc_os_c_3,'')<>''
OR IFNULL(cv.vis_pc_os_a_3,'')<>''
OR IFNULL(cv.vis_pc_os_add_3,'')<>''
OR IFNULL(cv.vis_pc_os_p_3,'')<>''
OR IFNULL(cv.vis_pc_os_prism_3,'')<>''
OR IFNULL(cv.vis_pc_os_slash_3,'')<>''
OR IFNULL(cv.vis_pc_os_sel_1_3,'')<>''
OR IFNULL(cv.vis_pc_os_sel_2_3,'')<>''
OR IFNULL(cv.vis_pc_os_overref_s_3,'')<>''
OR IFNULL(cv.vis_pc_os_overref_c_3,'')<>''
OR IFNULL(cv.vis_pc_os_overref_v_3,'')<>''
OR IFNULL(cv.vis_pc_os_overref_a_3,'')<>''
OR IFNULL(cv.vis_pc_os_near_txt_3,'')<>''
GROUP BY cpm.id_chart_vis_master 
ORDER BY cv.vis_id ;



/* chart_pc_mr -- MR1*/
INSERT INTO chart_pc_mr 
(
id_chart_vis_master,
exam_date,
provider_id,
ex_type,
ex_number,
mr_none_given,
mr_cyclopegic,
mr_pres_date,
mr_ou_txt_1,
mr_type,
ex_desc,
prism_desc,
uid,
strhash
)
SELECT 
cvm.id
,CASE WHEN cv.examDateMR IS NOT NULL AND cv.examDateMR <> '0000-00-00' THEN cv.examDateMR
            ELSE IFNULL(cv.exam_date,'') END
,IFNULL(cv.provider_id,0)			
,'MR'
,'1'
,CASE WHEN TRIM(cv.vis_mr_none_given) = 'None Given' OR TRIM(cv.vis_mr_none_given) = 'None' THEN ''
      WHEN TRIM(cv.vis_mr_none_given) = 'Given' THEN 'MR 1'
	  WHEN LOCATE('MR1',cv.vis_mr_none_given)>0 THEN 'MR1'
	  ELSE '' END
,CASE WHEN LOCATE('1',cv.vis_mrcyclopegic)>0 THEN 1 ELSE 0 END
,IFNULL(cv.vis_mr_pres_dt_1,'')
,IFNULL(cv.vis_mr_ou_txt_1,'')
,IFNULL(cv.vis_mr_type1,'')
,IFNULL(cv.vis_mr_desc,'')
,IFNULL(cv.visMrPrismDesc_1,'')
,IFNULL(cv.uid,0)
,IFNULL(SUBSTRING_INDEX(SUBSTRING_INDEX(cv.mr_hash,'i:0;s:32:"',-1),'";i:1',1) ,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_mr_od_s,'') <> ''
OR IFNULL(cv.vis_mr_od_c,'') <> ''
OR IFNULL(cv.vis_mr_od_a,'') <> ''
OR IFNULL(cv.vis_mr_od_add,'') <> ''
OR IFNULL(cv.vis_mr_od_p,'') <> ''
OR IFNULL(cv.vis_mr_od_prism,'') <> ''
OR IFNULL(cv.vis_mr_od_slash,'') <> ''
OR IFNULL(cv.vis_mr_od_sel_1,'') <> ''
OR IFNULL(cv.vis_mr_od_sel_2,'') <> ''
OR IFNULL(cv.vis_mr_od_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_od_txt_2,'') <> ''
OR IFNULL(cv.visMrOdSel2Vision,'') <> ''
OR IFNULL(cv.vis_mr_os_s,'') <> ''
OR IFNULL(cv.vis_mr_os_c,'') <> ''
OR IFNULL(cv.vis_mr_os_a,'') <> ''
OR IFNULL(cv.vis_mr_os_add,'') <> ''
OR IFNULL(cv.vis_mr_os_p,'') <> ''
OR IFNULL(cv.vis_mr_os_prism,'') <> ''
OR IFNULL(cv.vis_mr_os_slash,'') <> ''
OR IFNULL(cv.vis_mr_os_sel_1,'') <> ''
OR IFNULL(cv.vis_mr_os_sel_2,'') <> ''
OR IFNULL(cv.vis_mr_os_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_os_txt_2,'') <> ''
OR IFNULL(cv.visMrOsSel2Vision,'') <> ''
OR IFNULL(cv.provider_id,0) <> 0
OR IFNULL(cv.vis_mr_ou_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_desc,'') <> ''
OR IFNULL(cv.vis_mr_none_given,'') <> ''
OR IFNULL(cv.vis_mrcyclopegic,'') <> ''
OR IFNULL(cv.vis_mr_pres_dt_1,'') <> ''
OR IFNULL(cv.visMrPrismDesc_1,'') <> ''
OR IFNULL(cv.vis_mr_type1,'') <> ''
ORDER BY cv.vis_id ;


/* chart_pc_mr -- MR - OD1*/
INSERT INTO chart_pc_mr_values 
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
txt_1,
txt_2,
sel2v,
ovr_s,
ovr_c,
ovr_v,
ovr_a
)
SELECT 
MAX(cpm.id)
,'OD'
,IFNULL(cv.vis_mr_od_s,'') 
,IFNULL(cv.vis_mr_od_c,'')
,IFNULL(cv.vis_mr_od_a,'')
,IFNULL(cv.vis_mr_od_add,'')
,IFNULL(cv.vis_mr_od_p,'')
,IFNULL(cv.vis_mr_od_prism,'')
,IFNULL(cv.vis_mr_od_slash,'')
,IFNULL(cv.vis_mr_od_sel_1,'')
,IFNULL(cv.vis_mr_od_sel_2,'')
,IFNULL(cv.vis_mr_od_txt_1,'')
,IFNULL(cv.vis_mr_od_txt_2,'')
,IFNULL(cv.visMrOdSel2Vision,'')
,''
,''
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_mr_od_s,'') <> ''
OR IFNULL(cv.vis_mr_od_c,'') <> ''
OR IFNULL(cv.vis_mr_od_a,'') <> ''
OR IFNULL(cv.vis_mr_od_add,'') <> ''
OR IFNULL(cv.vis_mr_od_p,'') <> ''
OR IFNULL(cv.vis_mr_od_prism,'') <> ''
OR IFNULL(cv.vis_mr_od_slash,'') <> ''
OR IFNULL(cv.vis_mr_od_sel_1,'') <> ''
OR IFNULL(cv.vis_mr_od_sel_2,'') <> ''
OR IFNULL(cv.vis_mr_od_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_od_txt_2,'') <> ''
OR IFNULL(cv.visMrOdSel2Vision,'') <> ''
GROUP BY cpm.id_chart_vis_master 
ORDER BY cv.vis_id ;



/* chart_pc_mr -- MR - OS1*/
INSERT INTO chart_pc_mr_values 
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
txt_1,
txt_2,
sel2v,
ovr_s,
ovr_c,
ovr_v,
ovr_a
)
SELECT 
MAX(cpm.id)
,'OS'
,IFNULL(cv.vis_mr_os_s,'') 
,IFNULL(cv.vis_mr_os_c,'')
,IFNULL(cv.vis_mr_os_a,'')
,IFNULL(cv.vis_mr_os_add,'')
,IFNULL(cv.vis_mr_os_p,'')
,IFNULL(cv.vis_mr_os_prism,'')
,IFNULL(cv.vis_mr_os_slash,'')
,IFNULL(cv.vis_mr_os_sel_1,'')
,IFNULL(cv.vis_mr_os_sel_2,'')
,IFNULL(cv.vis_mr_os_txt_1,'')
,IFNULL(cv.vis_mr_os_txt_2,'')
,IFNULL(cv.visMrOsSel2Vision,'')
,''
,''
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_mr_os_s,'') <> ''
OR IFNULL(cv.vis_mr_os_c,'') <> ''
OR IFNULL(cv.vis_mr_os_a,'') <> ''
OR IFNULL(cv.vis_mr_os_add,'') <> ''
OR IFNULL(cv.vis_mr_os_p,'') <> ''
OR IFNULL(cv.vis_mr_os_prism,'') <> ''
OR IFNULL(cv.vis_mr_os_slash,'') <> ''
OR IFNULL(cv.vis_mr_os_sel_1,'') <> ''
OR IFNULL(cv.vis_mr_os_sel_2,'') <> ''
OR IFNULL(cv.vis_mr_os_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_os_txt_2,'') <> ''
OR IFNULL(cv.visMrOdSel2Vision,'') <> ''
GROUP BY cpm.id_chart_vis_master 
ORDER BY cv.vis_id ;



/* chart_pc_mr -- MR2*/
INSERT INTO chart_pc_mr 
(
id_chart_vis_master,
exam_date,
provider_id,
ex_type,
ex_number,
mr_none_given,
mr_cyclopegic,
mr_pres_date,
mr_ou_txt_1,
mr_type,
ex_desc,
prism_desc,
uid,
strhash
)
SELECT 
cvm.id
,CASE WHEN cv.examDateMR IS NOT NULL AND cv.examDateMR <> '0000-00-00' THEN cv.examDateMR
            ELSE IFNULL(cv.exam_date,'') END
,IFNULL(cv.providerIdOther,0)			
,'MR'
,'2'
,CASE WHEN cv.vis_mr_none_given = 'None Given' OR cv.vis_mr_none_given = 'None' THEN ''
      WHEN cv.vis_mr_none_given = 'Given' THEN 'MR 1'
	  WHEN LOCATE('MR2',cv.vis_mr_none_given)>0 THEN 'MR2'
	  ELSE '' END
,CASE WHEN LOCATE('2',cv.vis_mrcyclopegic)>0 THEN 1 ELSE 0 END
,IFNULL(cv.vis_mr_pres_dt_2,'')
,IFNULL(cv.vis_mr_ou_given_txt_1,'')
,IFNULL(cv.vis_mr_type2,'')
,IFNULL(cv.vis_mr_desc_other,'')
,IFNULL(cv.visMrPrismDesc_2,'')
,IFNULL(cv.uid,0)
,IFNULL(SUBSTRING_INDEX(SUBSTRING_INDEX(cv.mr_hash,'i:1;s:32:"',-1),'";i:2',1) ,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_mr_od_given_s,'') <> ''
OR IFNULL(cv.vis_mr_od_given_c,'') <> ''
OR IFNULL(cv.vis_mr_od_given_a,'') <> ''
OR IFNULL(cv.vis_mr_od_given_add,'') <> ''
OR IFNULL(cv.vis_mr_od_given_p,'') <> ''
OR IFNULL(cv.vis_mr_od_given_prism,'') <> ''
OR IFNULL(cv.vis_mr_od_given_slash,'') <> ''
OR IFNULL(cv.vis_mr_od_given_sel_1,'') <> ''
OR IFNULL(cv.vis_mr_od_given_sel_2,'') <> ''
OR IFNULL(cv.vis_mr_od_given_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_od_given_txt_2,'') <> ''
OR IFNULL(cv.visMrOtherOdSel2Vision,'') <> ''
OR IFNULL(cv.vis_mr_os_given_s,'') <> ''
OR IFNULL(cv.vis_mr_os_given_c,'') <> ''
OR IFNULL(cv.vis_mr_os_given_a,'') <> ''
OR IFNULL(cv.vis_mr_os_given_add,'') <> ''
OR IFNULL(cv.vis_mr_os_given_p,'') <> ''
OR IFNULL(cv.vis_mr_os_given_prism,'') <> ''
OR IFNULL(cv.vis_mr_os_given_slash,'') <> ''
OR IFNULL(cv.vis_mr_os_given_sel_1,'') <> ''
OR IFNULL(cv.vis_mr_os_given_sel_2,'') <> ''
OR IFNULL(cv.vis_mr_os_given_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_os_given_txt_2,'') <> ''
OR IFNULL(cv.visMrOtherOsSel2Vision,'') <> ''
OR IFNULL(cv.providerIdOther,0) <> 0
OR IFNULL(cv.vis_mr_ou_given_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_desc_other,'') <> ''
OR IFNULL(cv.vis_mr_none_given,'') <> ''
OR IFNULL(cv.vis_mrcyclopegic,'') <> ''
OR IFNULL(cv.vis_mr_pres_dt_2,'') <> ''
OR IFNULL(cv.visMrPrismDesc_2,'') <> ''
OR IFNULL(cv.vis_mr_type2,'') <> ''
ORDER BY cv.vis_id ;


/* chart_pc_mr -- MR - OD2*/
INSERT INTO chart_pc_mr_values 
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
txt_1,
txt_2,
sel2v,
ovr_s,
ovr_c,
ovr_v,
ovr_a
)
SELECT 
MAX(cpm.id)
,'OD'
,IFNULL(cv.vis_mr_od_given_s,'') 
,IFNULL(cv.vis_mr_od_given_c,'')
,IFNULL(cv.vis_mr_od_given_a,'')
,IFNULL(cv.vis_mr_od_given_add,'')
,IFNULL(cv.vis_mr_od_given_p,'')
,IFNULL(cv.vis_mr_od_given_prism,'')
,IFNULL(cv.vis_mr_od_given_slash,'')
,IFNULL(cv.vis_mr_od_given_sel_1,'')
,IFNULL(cv.vis_mr_od_given_sel_2,'')
,IFNULL(cv.vis_mr_od_given_txt_1,'')
,IFNULL(cv.vis_mr_od_given_txt_2,'')
,IFNULL(cv.visMrOtherOdSel2Vision,'')
,''
,''
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_mr_od_given_s,'') <> ''
OR IFNULL(cv.vis_mr_od_given_c,'') <> ''
OR IFNULL(cv.vis_mr_od_given_a,'') <> ''
OR IFNULL(cv.vis_mr_od_given_add,'') <> ''
OR IFNULL(cv.vis_mr_od_given_p,'') <> ''
OR IFNULL(cv.vis_mr_od_given_prism,'') <> ''
OR IFNULL(cv.vis_mr_od_given_slash,'') <> ''
OR IFNULL(cv.vis_mr_od_given_sel_1,'') <> ''
OR IFNULL(cv.vis_mr_od_given_sel_2,'') <> ''
OR IFNULL(cv.vis_mr_od_given_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_od_given_txt_2,'') <> ''
OR IFNULL(cv.visMrOtherOdSel2Vision,'') <> ''
GROUP BY cpm.id_chart_vis_master 
ORDER BY cv.vis_id ;


/* chart_pc_mr -- MR - OS2*/
INSERT INTO chart_pc_mr_values 
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
txt_1,
txt_2,
sel2v,
ovr_s,
ovr_c,
ovr_v,
ovr_a
)
SELECT 
MAX(cpm.id)
,'OS'
,IFNULL(cv.vis_mr_os_given_s,'') 
,IFNULL(cv.vis_mr_os_given_c,'')
,IFNULL(cv.vis_mr_os_given_a,'')
,IFNULL(cv.vis_mr_os_given_add,'')
,IFNULL(cv.vis_mr_os_given_p,'')
,IFNULL(cv.vis_mr_os_given_prism,'')
,IFNULL(cv.vis_mr_os_given_slash,'')
,IFNULL(cv.vis_mr_os_given_sel_1,'')
,IFNULL(cv.vis_mr_os_given_sel_2,'')
,IFNULL(cv.vis_mr_os_given_txt_1,'')
,IFNULL(cv.vis_mr_os_given_txt_2,'')
,IFNULL(cv.visMrOtherOsSel2Vision,'')
,''
,''
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_mr_os_given_s,'') <> ''
OR IFNULL(cv.vis_mr_os_given_c,'') <> ''
OR IFNULL(cv.vis_mr_os_given_a,'') <> ''
OR IFNULL(cv.vis_mr_os_given_add,'') <> ''
OR IFNULL(cv.vis_mr_os_given_p,'') <> ''
OR IFNULL(cv.vis_mr_os_given_prism,'') <> ''
OR IFNULL(cv.vis_mr_os_given_slash,'') <> ''
OR IFNULL(cv.vis_mr_os_given_sel_1,'') <> ''
OR IFNULL(cv.vis_mr_os_given_sel_2,'') <> ''
OR IFNULL(cv.vis_mr_os_given_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_os_given_txt_2,'') <> ''
OR IFNULL(cv.visMrOtherOsSel2Vision,'') <> ''
GROUP BY cpm.id_chart_vis_master 
ORDER BY cv.vis_id ;


/* chart_pc_mr -- MR3*/
INSERT INTO chart_pc_mr 
(
id_chart_vis_master,
exam_date,
provider_id,
ex_type,
ex_number,
mr_none_given,
mr_cyclopegic,
mr_pres_date,
mr_ou_txt_1,
mr_type,
ex_desc,
prism_desc,
uid,
strhash
)
SELECT 
cvm.id
,CASE WHEN cv.examDateMR IS NOT NULL AND cv.examDateMR <> '0000-00-00' THEN cv.examDateMR
            ELSE IFNULL(cv.exam_date,'') END
,IFNULL(cv.providerIdOther_3,0)			
,'MR'
,'3'
,CASE WHEN cv.vis_mr_none_given = 'None Given' OR cv.vis_mr_none_given = 'None' THEN ''
      WHEN cv.vis_mr_none_given = 'Given' THEN 'MR 1'
	  WHEN LOCATE('MR3',cv.vis_mr_none_given)>0 THEN 'MR3'
	  ELSE '' END
,CASE WHEN LOCATE('3',cv.vis_mrcyclopegic)>0 THEN 1 ELSE 0 END
,IFNULL(cv.vis_mr_pres_dt_3,'')
,IFNULL(cv.visMrOtherOuTxt1_3,'')
,IFNULL(cv.vis_mr_type3,'')
,IFNULL(cv.vis_mr_desc_3,'')
,IFNULL(cv.visMrPrismDesc_3,'')
,IFNULL(cv.uid,0)
,IFNULL(SUBSTRING_INDEX(SUBSTRING_INDEX(cv.mr_hash,'i:2;s:32:"',-1),'";',1) ,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.visMrOtherOdS_3,'') <> ''
OR IFNULL(cv.visMrOtherOdC_3,'') <> ''
OR IFNULL(cv.visMrOtherOdA_3,'') <> ''
OR IFNULL(cv.visMrOtherOdAdd_3,'') <> ''
OR IFNULL(cv.visMrOtherOdP_3,'') <> ''
OR IFNULL(cv.visMrOtherOdPrism_3,'') <> ''
OR IFNULL(cv.visMrOtherOdSlash_3,'') <> ''
OR IFNULL(cv.visMrOtherOdSel1_3,'') <> ''
OR IFNULL(cv.visMrOtherOdSel2_3,'') <> ''
OR IFNULL(cv.visMrOtherOdTxt1_3,'') <> ''
OR IFNULL(cv.visMrOtherOdTxt2_3,'') <> ''
OR IFNULL(cv.visMrOtherOdSel2Vision_3,'') <> ''
OR IFNULL(cv.visMrOtherOsS_3,'') <> ''
OR IFNULL(cv.visMrOtherOsC_3,'') <> ''
OR IFNULL(cv.visMrOtherOsA_3,'') <> ''
OR IFNULL(cv.visMrOtherOsAdd_3,'') <> ''
OR IFNULL(cv.visMrOtherOsP_3,'') <> ''
OR IFNULL(cv.visMrOtherOsPrism_3,'') <> ''
OR IFNULL(cv.visMrOtherOsSlash_3,'') <> ''
OR IFNULL(cv.visMrOtherOsSel1_3,'') <> ''
OR IFNULL(cv.visMrOtherOsSel2_3,'') <> ''
OR IFNULL(cv.visMrOtherOsTxt1_3,'') <> ''
OR IFNULL(cv.visMrOtherOsTxt2_3,'') <> ''
OR IFNULL(cv.visMrOtherOsSel2Vision_3,'') <> ''
OR IFNULL(cv.providerIdOther_3,0) <> 0
OR IFNULL(cv.visMrOtherOuTxt1_3,'') <> ''
OR IFNULL(cv.vis_mr_desc_3,'') <> ''
OR IFNULL(cv.vis_mr_none_given,'') <> ''
OR IFNULL(cv.vis_mrcyclopegic,'') <> ''
OR IFNULL(cv.vis_mr_pres_dt_3,'') <> ''
OR IFNULL(cv.visMrPrismDesc_3,'') <> ''
OR IFNULL(cv.vis_mr_type3,'') <> ''
ORDER BY cv.vis_id ;


/* chart_pc_mr -- MR - OD3*/
INSERT INTO chart_pc_mr_values 
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
txt_1,
txt_2,
sel2v,
ovr_s,
ovr_c,
ovr_v,
ovr_a
)
SELECT 
MAX(cpm.id)
,'OD'
,IFNULL(cv.visMrOtherOdS_3,'')
,IFNULL(cv.visMrOtherOdC_3,'')
,IFNULL(cv.visMrOtherOdA_3,'')
,IFNULL(cv.visMrOtherOdAdd_3,'')
,IFNULL(cv.visMrOtherOdP_3,'')
,IFNULL(cv.visMrOtherOdPrism_3,'')
,IFNULL(cv.visMrOtherOdSlash_3,'')
,IFNULL(cv.visMrOtherOdSel1_3,'')
,IFNULL(cv.visMrOtherOdSel2_3,'')
,IFNULL(cv.visMrOtherOdTxt1_3,'')
,IFNULL(cv.visMrOtherOdTxt2_3,'')
,IFNULL(cv.visMrOtherOdSel2Vision_3,'')
,''
,''
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.visMrOtherOdS_3,'') <> ''
OR IFNULL(cv.visMrOtherOdC_3,'') <> ''
OR IFNULL(cv.visMrOtherOdA_3,'') <> ''
OR IFNULL(cv.visMrOtherOdAdd_3,'') <> ''
OR IFNULL(cv.visMrOtherOdP_3,'') <> ''
OR IFNULL(cv.visMrOtherOdPrism_3,'') <> ''
OR IFNULL(cv.visMrOtherOdSlash_3,'') <> ''
OR IFNULL(cv.visMrOtherOdSel1_3,'') <> ''
OR IFNULL(cv.visMrOtherOdSel2_3,'') <> ''
OR IFNULL(cv.visMrOtherOdTxt1_3,'') <> ''
OR IFNULL(cv.visMrOtherOdTxt2_3,'') <> ''
OR IFNULL(cv.visMrOtherOdSel2Vision_3,'') <> ''
GROUP BY cpm.id_chart_vis_master 
ORDER BY cv.vis_id ;



/* chart_pc_mr -- MR - OS2*/
INSERT INTO chart_pc_mr_values 
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
txt_1,
txt_2,
sel2v,
ovr_s,
ovr_c,
ovr_v,
ovr_a
)
SELECT 
MAX(cpm.id)
,'OS'
,IFNULL(cv.visMrOtherOsS_3,'')
,IFNULL(cv.visMrOtherOsC_3,'')
,IFNULL(cv.visMrOtherOsA_3,'')
,IFNULL(cv.visMrOtherOsAdd_3,'')
,IFNULL(cv.visMrOtherOsP_3,'')
,IFNULL(cv.visMrOtherOsPrism_3,'')
,IFNULL(cv.visMrOtherOsSlash_3,'')
,IFNULL(cv.visMrOtherOsSel1_3,'')
,IFNULL(cv.visMrOtherOsSel2_3,'')
,IFNULL(cv.visMrOtherOsTxt1_3,'')
,IFNULL(cv.visMrOtherOsTxt2_3,'')
,IFNULL(cv.visMrOtherOsSel2Vision_3,'')
,''
,''
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.visMrOtherOsS_3,'') <> ''
OR IFNULL(cv.visMrOtherOsC_3,'') <> ''
OR IFNULL(cv.visMrOtherOsA_3,'') <> ''
OR IFNULL(cv.visMrOtherOsAdd_3,'') <> ''
OR IFNULL(cv.visMrOtherOsP_3,'') <> ''
OR IFNULL(cv.visMrOtherOsPrism_3,'') <> ''
OR IFNULL(cv.visMrOtherOsSlash_3,'') <> ''
OR IFNULL(cv.visMrOtherOsSel1_3,'') <> ''
OR IFNULL(cv.visMrOtherOsSel2_3,'') <> ''
OR IFNULL(cv.visMrOtherOsTxt1_3,'') <> ''
OR IFNULL(cv.visMrOtherOsTxt2_3,'') <> ''
OR IFNULL(cv.visMrOtherOsSel2Vision_3,'') <> ''
GROUP BY cpm.id_chart_vis_master 
ORDER BY cv.vis_id ;