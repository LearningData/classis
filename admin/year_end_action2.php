<?php 
/**    								year_end_action2.php
 */

$action='';
$choice='';

include('scripts/sub_action.php');

	/*promote students to chosen pastoral groups*/
	$years=array();
	$c=0;
	$d_yeargroup=mysql_query("SELECT id, ncyear, section_id, name FROM
							yeargroup ORDER BY ncyear ASC");
	while($years[]=mysql_fetch_array($d_yeargroup,MYSQL_ASSOC)){
		$yid=$years[$c]['id'];
		$d_form=mysql_query("SELECT id FROM form WHERE
							yeargroup_id='$yid' ORDER BY id DESC");
		$years[$c]['fids']=array();
		while($form=mysql_fetch_array($d_form,MYSQL_ASSOC)){
			$years[$c]['fids'][]=$form['id'];
			}
		$c++;
		}

	for($c=(sizeof($years)-2);$c>-1;$c--){
		$yid=$years[$c]['id'];
		$nextpostyid=$_POST["$yid"];
		if($nextpostyid=='1000'){
			$nextyid=$yid.'-alumni-'.date('Y').'-'.date('m');
			$type='alumni';
			}
		else{
			$nextyid=$nextpostyid;
			$type='year';
			}
		mysql_query("UPDATE community SET name='$nextyid' WHERE
				type='$type' AND name='$yid';");

		while(list($index,$fid)=each($years[$c]['fids'])){
			if($nextpostyid!='1000'){
				$nextfid=$years[$c+1]['fids'][$index];
				}
			else{
				$nextfid=$fid.'-alumni-'.date('Y').'-'.date('m');
				}
			if($nextfid==''){$nextfid=$fid.'-'.date('Y').'-'.date('m');}
			mysql_query("UPDATE community SET name='$nextfid' WHERE
								type='form' AND name='$fid';");
			mysql_query("UPDATE student SET form_id='$nextfid' WHERE form_id='$fid';");
			//$result[]='Promoted form '.$fid.' to '.$nextfid;
			}

		mysql_query("UPDATE student SET yeargroup_id='$nextyid' WHERE yeargroup_id='$yid';");
		//$result[]='Promoted year '.$yid.' to '.$nextyid;
		}

	/*promote students to next stage of course or graduate to chosen next course*/
	$courses=array();
	$c=0;
	$d_course=mysql_query("SELECT id, sequence, section_id, name FROM
							course ORDER BY sequence DESC");
	while($courses[]=mysql_fetch_array($d_course,MYSQL_ASSOC)){
		$crid=$courses[$c]['id'];
		/*currently sequence of the stages for a course depends solely
			upon their alphanumeric order - means they require a numeric ending*/
		$d_stage=mysql_query("SELECT stage FROM cohort WHERE
				course_id='$crid' AND status='C' ORDER BY stage DESC");
		$courses[$c]['stages']=array();
		while($stage=mysql_fetch_array($d_stage,MYSQL_ASSOC)){
			$courses[$c]['stages'][]=array('stage'=>$stage['stage'],'newcohid'=>'');
			}
		$c++;
		}

	for($c=0;$c<sizeof($courses);$c++){
		$crid=$courses[$c]['id'];
		$nextpostcrid=$_POST["$crid"];
		$season='S';/*currently restricted to a single season value*/
		$yearnow=getCurriculumYear($crid);
		$yeargone=$yearnow-1;
		$stages=$courses[$c]['stages'];
		for($c2=0;$c2<sizeof($stages);$c2++){
			$stage=$stages[$c2]['stage'];
			$cohort=array('course_id'=>$crid,'stage'=>$stage,'year'=>$yeargone);
			$cohidgone=updateCohort($cohort);
			$cohort=array('course_id'=>$crid,'stage'=>$stage,'year'=>$yearnow);
			$stages[$c2]['newcohid']=updateCohort($cohort);
			$result[]='Updating '. $crid.' '. $stage. ' '. $yeargone
									. ' to ' .$yearnow;

			if($c2==0 and $nextpostcrid!='1000'){
				/*last stage of course are graduating to next course*/
				$d_cohort=mysql_query("SELECT id FROM cohort WHERE
						course_id='$nextpostcrid' AND year='C' ORDER BY stage ASC");
				$nextcohid=mysql_result($d_cohort,0,0);
				}
			elseif($nextpostcrid!='1000'){
				/*just promote to next stage of this course*/
				$nextcohid=$stages[$c2-1]['newcohid'];
				}
			else{
				/*last stage is graduating and leaving*/
				$nextcohid='';
				}

			/*go through each community of students who were studying
			this stage and promote them*/
			$d_cohidcomid=mysql_query("SELECT community_id FROM cohidcomid 
											WHERE cohort_id='$cohidgone'");
			while($cohidcomid=mysql_fetch_array($d_cohidcomid,MYSQL_ASSOC)){
				$comid=$cohidcomid['community_id'];
				if($nextcohid!=''){
					$result[]='Promoted community '.$comid.' from '. 
											$cohidgone. ' to '.$nextcohid;
					mysql_query("INSERT INTO cohidcomid SET
								cohort_id='$nextcohid', community_id='$comid'");
					}
				else{
					$result[]='Community '.$comid.' graduated to leave.';
					}
				}
			}
		}

	mysql_query("DELETE FROM cidsid");
	mysql_query("DELETE FROM score");
	mysql_query("DELETE FROM mark");
	mysql_query("DELETE FROM midcid");
	mysql_query("DELETE FROM eidmid");

	include('scripts/results.php');
//	include('scripts/redirect.php');
?>
