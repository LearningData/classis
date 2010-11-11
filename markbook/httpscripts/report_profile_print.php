<?php
/**
 *			   					httpscripts/report_profile_print.php
 *
 */

require_once('../../scripts/http_head_options.php');


if(isset($_GET['id'])){$xmlid=$_GET['id'];}
if(isset($_POST['id'])){$xmlid=$_POST['id'];}
if(isset($_GET['sids'])){$sids=(array)$_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}
if(isset($_GET['bid'])){$bid=$_GET['bid'];}else{$bid='%';}
if(isset($_POST['bid'])){$bid=$_POST['bid'];}
if(isset($_GET['pid'])){$pid=$_GET['pid'];}else{$pid='%';}
if(isset($_POST['pid'])){$pid=$_POST['pid'];}
if(isset($_GET['stage'])){$stage=$_GET['stage'];}else{$stage='%';}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_GET['classes'])){$classes=$_GET['classes'];}else{$classes='';}
if(isset($_POST['classes'])){$classes=$_POST['classes'];}
if(isset($_GET['eids'])){$eids=(array)$_GET['eids'];}else{$eids=array();}
if(isset($_POST['eids'])){$eids=(array)$_POST['eids'];}
if(isset($_GET['template'])){$template=$_GET['template'];}else{$template='';}
if(isset($_POST['template'])){$template=$_POST['template'];}
if(isset($_GET['year'])){$curryear=$_GET['year'];}else{$curryear='';}
if(isset($_POST['year'])){$curryear=$_POST['year'];}
if(isset($_GET['name'])){$profilename=$_GET['name'];}
if(isset($_POST['name'])){$profilename=$_POST['name'];}

if(sizeof($sids)==0){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{
	if($xmlid==-1){
		while(list($index,$eid)=each($eids)){
			$AssDef=fetchAssessmentDefinition($eid);
			$AssDefs[]=$AssDef;
			}
		}
	else{
		$profile=get_assessment_profile($xmlid);
		$crid=$profile['course_id'];
		$profilename=$profile['name'];
		if($curryear==''){$curryear=get_curriculumyear($crid);}

		/*TODO*/
		$prevyear=$curryear-1;
		$allstages=list_course_stages($crid);
		foreach($allstages as $sin => $allstage){
			if($allstage['name']==$stage){$stageno=$sin-1;}
			}
		if($stageno>-1){$prevstage=$allstages[$stageno]['name'];}
		else{$prevstage='';}
		//trigger_error('PREV: '.$prevstage,E_USER_WARNING);
		/**/

		$cohort=array('id'=>'','course_id'=>$crid,'stage'=>$stage,'year'=>$curryear);
		$prevcohort=array('id'=>'','course_id'=>$crid,'stage'=>$prevstage,'year'=>$prevyear);
		/* Allows for alternative to the profile's default template */
		if($template!=''){$profile['transform']=$template;}
		/* Allows a subset of the profile's assessments to be included */

		if(sizeof($eids)>0){
			$AssDefs=array();
			foreach($eids as $eindex => $eid){
				$AssDefs[]=fetchAssessmentDefinition($eid);
				}
			}
		else{
			$AssDefs=(array)fetch_cohortAssessmentDefinitions($cohort,$profile['id']);
			}
		}


	/* TODO: make this a property of the profile */
	if($profile['transform']!='tracking_grid'){
		$bid='%';
		$pid='%';
		}
	else{
		//trigger_error('PREV: '.$prevstage,E_USER_WARNING);
		//trigger_error(sizeof($AssDefs),E_USER_WARNING);
		$PrevAssDefs=(array)fetch_cohortAssessmentDefinitions($prevcohort,$profile['id']);
		$all_AssDefs=(array)array_merge_recursive($AssDefs + $PrevAssDefs);
		//trigger_error(sizeof($all_AssDefs),E_USER_WARNING);
		}

	/* Bands in ascending date order. */
  	$d_stats=mysql_query("SELECT DISTINCT * FROM statvalues JOIN stats ON stats.id=statvalues.stats_id 
				WHERE stats.course_id='$crid' AND stats.profile_name='$profilename' AND stats.year='$curryear' 
				AND (statvalues.stage='$stage' OR statvalues.stage='$prevstage') 
				AND (statvalues.subject_id LIKE '$bid' OR statvalues.subject_id='%')
				AND (statvalues.component_id LIKE '$pid' OR statvalues.component_id='%') 
				ORDER BY statvalues.stage, statvalues.date ASC;");
	$bands=array();
	while($stat=mysql_fetch_array($d_stats,MYSQL_ASSOC)){
		$bands[$stat['date']]=$stat;
		}

	$Students=array();

	$asstable=array();
	for($ec=0;$ec<sizeof($AssDefs);$ec++){
		unset($bdate);
		$assdate=$AssDefs[$ec]['Deadline']['value'];
		foreach($bands as $date=>$band){
			if($assdate>$date or !isset($bdate)){$bdate=$date;}
			trigger_error($profilename.':'.$assdate.': '.$date. ' -------> '. $bdate,E_USER_WARNING);
			}
		/* If the assessment is out of range then set to the last band. */
		$asstable['ass'][]=array('label'=>''.$AssDefs[$ec]['PrintLabel']['value'],
								 'date'=>''.display_date($assdate),
								 'id_db'=>''.$AssDefs[$ec]['id_db'],
								 'bands'=>array(array('name'=>'C','value'=>$bands[$bdate]['value3']),
												array('name'=>'B','value'=>$bands[$bdate]['value2']),
												array('name'=>'A','value'=>$bands[$bdate]['value1'])
												)
								 );
		}

	$Students['asstable']=$asstable;

	/* TODO: this assumes all are using same gradescheme */
	$restable=array();
	$grading_grades=$AssDefs[1]['GradingScheme']['grades'];
	$pairs=explode(';', $grading_grades);
	for($c=0;$c<sizeof($pairs);$c++){
		list($levelgrade,$level)=explode(':',$pairs[$c]);
		$restable['res'][]=array('label'=>''.$levelgrade,
								 'value'=>''.$level);
		}
	$Students['restable']=$restable;
	
	$Students['Student']=array();
	foreach($sids as $sid){
		$Student=(array)fetchStudent_short($sid);
		$Assessments['Assessment']=array();
		foreach($AssDefs as $AssDef){
			$Assessments['Assessment']=array_merge($Assessments['Assessment'],fetchAssessments_short($sid,$AssDef['id_db'],$bid,$pid));
			}
		$Student['Assessments']=xmlarray_indexed_check($Assessments,'Assessment');
		$Students['Student'][]=$Student;
		}

	$Students['Date']=date('Y-m-d');
	$Students['Paper']='landscape';
	$Students['Transform']=$profile['transform'];
	$Students['Subject']['value_db']=$bid;
	$Students['Subject']['value']=get_subjectname($bid);
	$Students['Component']['value_db']=$pid;
	$Students['Component']['value']=get_subjectname($pid);
	if($classes!=''){
		$Students['Description']['value']=$classes;
		}
	else{
		$Students['Description']['value']=$stage;
		}
	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>