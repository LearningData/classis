<?php 
 /**		   											register.php
  *
  *	This is the hostpage for the register.
  *
  * The group of students being registered could either be identified
  * by newcomid or newcid - pastoral or academic.
  *
  */

$host='register.php';
$book='register';

include('scripts/head_options.php');
include('scripts/set_book_vars.php');
$session_vars=array('newcid','newcomid','startday','checkeveid','secid','nodays','yid');
include('scripts/set_book_session_vars.php');
if(isset($CFG->regtypes[1])){$regtype=$CFG->regtypes[1];}
else{$regtype='form';}

if($nodays==''){$nodays=8;}

  if($newcid!=''){
	  $secid=get_class_section($newcid);
	  $community=array('id'=>'','type'=>'class','name'=>$newcid);
	  $newcomid='';
	  }
  elseif($newcomid!=''){
	  //TODO: no relation between sections and other community groups
	  $community=(array)get_community($newcomid);
	  if($yid==''){$yid=array_pop(array_keys($community['groups']));}
	  $section=get_section($yid);
	  $secid=$section['id'];
	  $newcid='';
	  }

	/* If a community is already selected (by passing newcomid) then
	 * stick with that. Otherwise try to choose a relevant community to
	 * display by default form the users pastoral responsibilities. 
	 */
  if(isset($community) and is_array($community)){
	  $comid=$community['id'];
	  }
  else{
	  /* On first load select the teacher's pastoral group by default.
	   * This is made tricky because of different regtypes for
	   * different sections may or may not apply.
	   */
	  $pastorals=(array)list_pastoral_respon();
	  if(sizeof($pastorals['houses'])>0 and array_search('house',$CFG->regtypes)!==false){
		  $yid=$pastorals['houses'][0]['yeargroup_id'];
		  $section=get_section($yid);
		  if((array_key_exists($section['id'],$CFG->regtypes) and $CFG->regtypes[$section['id']]=='house') or (!array_key_exists($section['id'],$CFG->regtypes) and $CFG->regtypes[1]=='house')){
			  $community=array('id'=>$pastorals['houses'][0]['community_id'],'type'=>'house','name'=>$pastorals['houses'][0]['name']);
			  $secid=$section['id'];
			  }
		  }
	  if(!isset($community) and sizeof($pastorals['forms'])>0 and array_search('form',$CFG->regtypes)!==false){
		  $yid=$pastorals['forms'][0]['yeargroup_id'];
		  $section=get_section($yid);
		  if((array_key_exists($section['id'],$CFG->regtypes) and $CFG->regtypes[$section['id']]=='form') or (!array_key_exists($section['id'],$CFG->regtypes) and $CFG->regtypes[1]=='form')){
			  $community=array('id'=>$pastorals['forms'][0]['community_id'],'type'=>'form','name'=>$pastorals['forms'][0]['name']);
			  $secid=$section['id'];
			  }
		  }
	  if(!isset($community) and sizeof($pastorals['years'])>0 and array_search('year',$CFG->regtypes)!==false){
		  foreach($pastorals['years'] as $yid){
			  $section=get_section($yid);
			  if((array_key_exists($section['id'],$CFG->regtypes) and $CFG->regtypes[$section['id']]=='year') or (!array_key_exists($section['id'],$CFG->regtypes) and $CFG->regtypes[1]=='year')){
				  $community=array('id'=>'','type'=>'year','name'=>$yid);
				  $secid=$section['id'];
				  }
			  }
		  }
	  }
?>
  <div id="bookbox" class="registercolor">
<?php
 
	if(!isset($secid)){$secid=1;}
	$currentevent=get_currentevent($secid);

	if($current!=''){
		include($book.'/'.$current);
		}
	elseif(isset($community) and is_array($community)){
		$current='register_list.php';
		include($book.'/'.$current);
		}
?>
  </div>

  <div style="visibility:hidden;" id="hiddenbookoptions" class="bookoptions">

	  <fieldset class="register">
		<legend><?php print get_string('currentsession',$book);?></legend>
		<div style="background-color:#666655;font-size:x-small;padding:2px;">
		 <a style="color:#fff;"
			href="register.php?current=register_list.php&newcid=<?php print $newcid;?>&newcomid=<?php print $newcomid;?>&nodays=<?php print $nodays;?>&checkeveid=0&startday=" 
			target="viewregister" onclick="parent.viewBook('register');">
<?php 
			print ''.display_date($currentevent['date']).'<br />';
			print date('H:i').'<br />';
			print get_string('period',$book).': '.$currentevent['session'];
?>
		</a>
		</div>	  
	  </fieldset>

		<br />
		<br />

	  <fieldset class="register">
		<legend><?php print_string('section',$book);?></legend>

		<form id="registerchoice" name="registerchoice" method="post" action="register.php" target="viewregister">
<?php
	$onsidechange='yes';
	$listtype='section';
	$listname='newsecid';
	$selnewsecid=$secid;
	$listlabel='';
	$sections=list_sections();
	include('scripts/set_list_vars.php');
	list_select_list($sections,$listoptions,$book);
	unset($listoptions);unset($sections);
?>
		<input type="hidden" name="newcid" value="" />
		<input type="hidden" name="newcomid" value="" />
		<input type="hidden" name="current" value="completion_list.php" />
		<input type="hidden" name="nodays" value="8" />
		</form>
	  </fieldset>

	  <br />
	  <br />


	<form id="registerchoicesel" name="registerchoicesel" method="post" 
		  action="register.php" target="viewregister">
	  <fieldset class="register selery">
		<legend><?php print_string('list',$book);?></legend>
<?php
		$choices=array('absence_list.php' => 'absencelists'
					   //,'late_list.php' => 'lates' TODO
					   ,'completion_list.php' => 'completedregisters'
					   ,'statistics.php' => 'statistics'
					   );
		selery_stick($choices,$choice,$book);
?>
	  </fieldset>
	</form>

  </div>
<?php
	  include('scripts/end_options.php'); 
?>
