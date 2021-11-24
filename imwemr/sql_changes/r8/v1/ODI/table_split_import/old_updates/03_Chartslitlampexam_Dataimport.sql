
INSERT INTO chart_conjunctiva (
form_id
,patient_id
,exam_date
,uid
,statusElem
,purged
,purgerId
,purgeTime
,ut_elem
,conjunctiva_od
,conjunctiva_od_summary
,conjunctiva_os
,conjunctiva_os_summary
,wnlConj
,wnlConjOd
,wnlConjOs
,posConj
,ncConj
,ncConj_od
,ncConj_os
,pen_light
,modi_note_ConjArr
,last_opr_id
,wnl_value_Conjunctiva)
SELECT
form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(ut_elem,'')
,IFNULL(conjunctiva_od,'')
,IFNULL(conjunctiva_od_summary,'')
,IFNULL(conjunctiva_os,'')
,IFNULL(conjunctiva_os_summary,'')
,IFNULL(wnlConj,0)
,IFNULL(wnlConjOd,0)
,IFNULL(wnlConjOs,0)
,IFNULL(posConj,0)
,IFNULL(ncConj,0)
,IFNULL(ncConj_od,0)
,IFNULL(ncConj_os,0)
,IFNULL(pen_light,0)
,IFNULL(modi_note_ConjArr,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_Conjunctiva,'')
FROM chart_slit_lamp_exam;


INSERT INTO chart_cornea (
form_id
,patient_id
,exam_date
,uid
,statusElem
,purged
,purgerId
,purgeTime
,ut_elem
,cornea_od
,cornea_od_summary
,cornea_os
,cornea_os_summary
,wnlCorn
,wnlCornOd
,wnlCornOs
,posCorn
,ncCorn
,ncCorn_od
,ncCorn_os
,pen_light
,modi_note_CornArr
,last_opr_id
,wnl_value_Cornea
)
SELECT
form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(ut_elem,'')
,IFNULL(cornea_od,'')
,IFNULL(cornea_od_summary,'')
,IFNULL(cornea_os,'')
,IFNULL(cornea_os_summary,'')
,IFNULL(wnlCorn,0)
,IFNULL(wnlCornOd,0)
,IFNULL(wnlCornOs,0)
,IFNULL(posCorn,0)
,IFNULL(ncCorn,0)
,IFNULL(ncCorn_od,0)
,IFNULL(ncCorn_os,0)
,IFNULL(pen_light,0)
,IFNULL(modi_note_CornArr,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_Cornea,'')
FROM chart_slit_lamp_exam;

INSERT INTO chart_ant_chamber (
form_id
,patient_id
,exam_date
,uid
,statusElem
,purged
,purgerId
,purgeTime
,ut_elem
,anf_chamber_od
,anf_chamber_od_summary
,anf_chamber_os
,anf_chamber_os_summary
,wnlAnt
,wnlAntOd
,wnlAntOs
,posAnt
,ncAnt
,ncAnt_od
,ncAnt_os
,pen_light
,modi_note_AntArr
,last_opr_id
,wnl_value_Ant
)
SELECT
form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(ut_elem,'')
,IFNULL(anf_chamber_od,'')
,IFNULL(anf_chamber_od_summary,'')
,IFNULL(anf_chamber_os,'')
,IFNULL(anf_chamber_os_summary,'')
,IFNULL(wnlAnt,0)
,IFNULL(wnlAntOd,0)
,IFNULL(wnlAntOs,0)
,IFNULL(posAnt,0)
,IFNULL(ncAnt,0)
,IFNULL(ncAnt_od,0)
,IFNULL(ncAnt_os,0)
,IFNULL(pen_light,0)
,IFNULL(modi_note_AntArr,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_Ant,'')
FROM chart_slit_lamp_exam;


INSERT INTO chart_iris (
form_id
,patient_id
,exam_date
,uid
,statusElem
,purged
,purgerId
,purgeTime
,ut_elem
,iris_pupil_od
,iris_pupil_od_summary
,iris_pupil_os
,iris_pupil_os_summary
,wnlIris
,wnlIrisOd
,wnlIrisOs
,posIris
,ncIris
,ncIris_od
,ncIris_os
,pen_light
,modi_note_IrisArr
,last_opr_id
,wnl_value_Iris
)
SELECT
form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(ut_elem,'')
,IFNULL(iris_pupil_od,'')
,IFNULL(iris_pupil_od_summary,'')
,IFNULL(iris_pupil_os,'')
,IFNULL(iris_pupil_os_summary,'')
,IFNULL(wnlIris,0)
,IFNULL(wnlIrisOd,0)
,IFNULL(wnlIrisOs,0)
,IFNULL(posIris,0)
,IFNULL(ncIris,0)
,IFNULL(ncIris_od,0)
,IFNULL(ncIris_os,0)
,IFNULL(pen_light,0)
,IFNULL(modi_note_IrisArr,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_Iris,'')
FROM chart_slit_lamp_exam;


INSERT INTO chart_lens (
form_id
,patient_id
,exam_date
,uid
,statusElem
,purged
,purgerId
,purgeTime
,ut_elem
,lens_od
,lens_od_summary
,lens_os
,lens_os_summary
,wnlLens
,wnlLensOd
,wnlLensOs
,posLens
,ncLens
,ncLens_od
,ncLens_os
,pen_light
,modi_note_LensArr
,last_opr_id
,wnl_value_Lens
)
SELECT
form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(ut_elem,'')
,IFNULL(lens_od,'')
,IFNULL(lens_od_summary,'')
,IFNULL(lens_os,'')
,IFNULL(lens_os_summary,'')
,IFNULL(wnlLens,0)
,IFNULL(wnlLensOd,0)
,IFNULL(wnlLensOs,0)
,IFNULL(posLens,0)
,IFNULL(ncLens,0)
,IFNULL(ncLens_od,0)
,IFNULL(ncLens_os,0)
,IFNULL(pen_light,0)
,IFNULL(modi_note_LensArr,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_Lens,'')
FROM chart_slit_lamp_exam;


INSERT INTO chart_drawings (form_id,
patient_id,
exam_date,
drw_od_txt,
drw_os_txt,
wnlDraw,
posDraw,
ncDraw,
wnlDrawOd,
wnlDrawOs,
uid,
statusElem,
idoc_drawing_id,
ncDraw_od,
ncDraw_os,
ut_elem,
purged,
purgerId,
purgeTime,
last_opr_id,
exam_name,
exm_drawing,
drawing_insert_update_from,
import_id)
SELECT form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(cornea_od_desc_1,'')
,IFNULL(cornea_os_desc,'')
,IFNULL(wnlDraw,0)
,IFNULL(posDraw,0)
,IFNULL(ncDrawSle,0)
,IFNULL(wnlDrawOd,0)
,IFNULL(wnlDrawOs,0)
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(idoc_drawing_id,'')
,IFNULL(ncDrawSle_od,0)
,IFNULL(ncDrawSle_os,0)
,IFNULL(ut_elem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(last_opr_id,0)
,IFNULL('SLE','')
,IFNULL(conjunctiva_od_drawing,'')
,IFNULL(drawing_insert_update_from ,0)
,sle_id
FROM chart_slit_lamp_exam;

/*---div1----*/
UPDATE chart_conjunctiva
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div1_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div1_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_conjunctiva
SET statusELem = ''
WHERE statusElem =',';

/*---div2----*/

UPDATE chart_cornea
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div2_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div2_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_cornea
SET statusELem = ''
WHERE statusElem =',';


/*---div3----*/

UPDATE chart_ant_chamber
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div3_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div3_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_ant_chamber
SET statusELem = ''
WHERE statusElem =',';


/*---div4----*/

UPDATE chart_iris
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div4_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div4_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_iris
SET statusELem = ''
WHERE statusElem =',';


/*---div5----*/

UPDATE chart_lens
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div5_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div5_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_lens
SET statusELem = ''
WHERE statusElem =',';


/*---div6----*/

UPDATE chart_drawings
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div6_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div6_Os',statusElem),19),''))
WHERE statusElem <> ''
AND LOWER(IFNULL(exam_name,''))='sle' ;

UPDATE chart_drawings
SET statusELem = ''
WHERE statusElem =','
AND LOWER(IFNULL(exam_name,''))='sle' ;

