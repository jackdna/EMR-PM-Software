
/*Updating idoc_drawing */
UPDATE imw_dev_scan.idoc_drawing I INNER JOIN chart_drawings D ON I.drawing_for_master_id = IFNULL(D.import_id,0)
AND I.drawing_for = CASE  D.exam_name WHEN 'LA' THEN 'LA'
																	 WHEN 'FundusExam' THEN 'Fundus_Exam'
																	 WHEN 'SLE' THEN 'SLE'
                                                                     END
SET I.drawing_for_master_id = D.id;

/* Drop column from drawig table*/
ALTER TABLE chart_drawings DROP COLUMN import_id;