<?php
/**                    httpscripts/responsables_edit_yeargroup.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

	list($yid,$uid)=explode('-',$xmlid);
	$d_groups=mysql_query("SELECT gid FROM groups WHERE 
						course_id='' AND subject_id='' AND community_id='0' AND yeargroup_id='$yid' AND type='p';");
	$gid=mysql_result($d_groups,0);
	$perms=getYearPerm($yid);
	$Responsible=array();
	$Responsible['id_db']=$yid.'-'.$uid;
	if($perms['x']==1){
		$newperms=array('r'=>0,'w'=>0,'x'=>0);
		$result[]=update_staff_perms($uid,$gid,$newperms);
		$Responsible['exists']='false';
		}
	else{
		$Responsible['exists']='true';
		}

$returnXML=$Responsible;
$rootName='Responsible';
require_once('../../scripts/http_end_options.php');
exit;
?>

















