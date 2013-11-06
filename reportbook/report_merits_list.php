<?php
/**									report_merits_list.php
 *
 *
 *	Produce a lists students with merits awarded in the given time frame.
 *
 */

$action='report_merits_list.php';

$startdate=$_POST['date0'];
$enddate=$_POST['date1'];
if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='%';}
if($bid==''){$bid='%';}
if(isset($_POST['activity'])){$activity=$_POST['activity'];}else{$activity='%';}
if($activity==''){$activity='%';}
if(isset($_POST['core_value'])){$corevalue=$_POST['core_value'];}else{$corevalue='%';}
if($corevalue==''){$corevalue='%';}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['year'])){$year=$_POST['year'];}
if(isset($_POST['yid'])){$yid=$_POST['yid'];}else{$yid='';}
if(isset($_POST['formid']) and $_POST['formid']!=''){$comid=$_POST['formid'];}
elseif(isset($_POST['houseid']) and $_POST['houseid']!=''){$comid=$_POST['houseid'];}
else{$comid='';}
$curryear=get_curriculumyear();

if(isset($_POST['view']) and $_POST['view']!=''){$view=$_POST['view'];}else{$view='activity';}
if($view=='activity'){list($ratingnames,$catdefs)=fetch_categorydefs('mer');$val=$activity;}
if($view=='core_value'){list($ratingnames,$catdefs)=fetch_categorydefs('cor');$val=$corevalue;}

include('scripts/sub_action.php');

	if($comid!=''){
		$com=get_community($comid);
		if($yid!=''){
			$d_m=mysql_query("SELECT * FROM merits WHERE merits.date >= '$startdate' AND
					merits.date<='$enddate'  AND merits.$view LIKE '$val' AND merits.year='$curryear'
					AND merits.student_id=ANY(SELECT student.id FROM student JOIN comidsid AS a ON a.student_id=student.id
					WHERE student.yeargroup_id='$yid' AND a.community_id='$comid' 
					AND (a.leavingdate>'$enddate' OR a.leavingdate='0000-00-00' OR a.leavingdate IS NULL));");
			$d_total=mysql_query("SELECT SUM(value) FROM merits WHERE merits.year='$curryear'
					AND merits.student_id=ANY(SELECT student.id FROM student JOIN comidsid AS a ON a.student_id=student.id
					WHERE student.yeargroup_id='$yid' AND a.community_id='$comid' 
					AND (a.leavingdate>'$enddate' OR a.leavingdate='0000-00-00' OR a.leavingdate IS NULL));");
			}
		else{
			$d_m=mysql_query("SELECT * FROM merits JOIN comidsid AS a ON a.student_id=merits.student_id WHERE
			a.community_id='$comid' AND (a.leavingdate>'$enddate' OR a.leavingdate='0000-00-00' OR a.leavingdate IS NULL)
			AND merits.date >= '$startdate' AND merits.date<='$enddate'  AND merits.$view LIKE '$val' AND merits.year='$curryear';");
			$d_total=mysql_query("SELECT SUM(value) FROM merits WHERE merits.year='$curryear'
					AND merits.student_id=ANY(SELECT student.id FROM student JOIN comidsid AS a ON a.student_id=student.id
					WHERE a.community_id='$comid' 
					AND (a.leavingdate>'$enddate' OR a.leavingdate='0000-00-00' OR a.leavingdate IS NULL));");

			}
		}
	elseif($yid!=''){
		$d_m=mysql_query("SELECT * FROM merits JOIN
			student ON student.id=merits.student_id WHERE
			merits.date>='$startdate' AND merits.date<='$enddate' 
			AND student.yeargroup_id LIKE '$yid'  
			AND merits.$view LIKE '$val' AND merits.year='$curryear';");
		}
	else{
		if($rcrid=='%'){
			/* User has a subject not a course responsibility selected. */
			$d_course=mysql_query("SELECT DISTINCT cohort.course_id FROM
				cohort JOIN component ON component.course_id=cohort.course_id WHERE
				component.subject_id='$rbid' AND component.id='' AND cohort.stage='$stage' AND cohort.year='$year';");
			$rcrid=mysql_result($d_course,0);
			}
		$d_community=mysql_query("SELECT community_id FROM cohidcomid JOIN
				cohort ON cohidcomid.cohort_id=cohort.id WHERE
			    cohort.stage='$stage' AND cohort.year='$year' AND
				cohort.course_id='$rcrid' LIMIT 1;");
		$comid=mysql_result($d_community,0);
		$d_m=mysql_query("SELECT * FROM merits JOIN
				comidsid ON comidsid.student_id=merits.student_id
				WHERE merits.date >= '$startdate' AND
				merits.date<='$enddate' AND
				merits.subject_id LIKE '$bid' AND merits.$view LIKE '$val' 
				AND merits.year='$curryear' AND comidsid.community_id='$comid';");
		}

	if(mysql_num_rows($d_m)==0){
		$error[]=get_string('nonefound',$book);
		$action='report_merits.php';
    	include('scripts/results.php');
	    include('scripts/redirect.php');
		exit;
		}

	$summarys=array();
	$sids=array();
	$range=0;
	$totaltotal=0;
	while($merit=mysql_fetch_array($d_m,MYSQL_ASSOC)){
		$summary=array();
		$sid=$merit['student_id'];
		if(!in_array($sid,$sids)){
			$sids[]=$sid;
			$summary=array('sid'=>$sid);
			}
		else{
			$summary=$summarys[$sid];
			}
		$cat=$merit[$view];
		$value=$merit['value'];
		if(isset($summary[$cat]['value'])){$summary[$cat]['value']+=$value;}
		else{$summary[$cat]['value']=$value;}
		if(isset($summary['total'])){$summary['total']+=$value;}
		else{$summary['total']=$value;}
		if($summary['total']>$range){$range=$summary['total'];}
		$totaltotal+=$value;
		$summarys[$sid]=$summary;
		}

$sort_array[0]['name']="total";
$sort_array[0]['sort']='DESC';
$sort_array[0]['case']=FALSE;
sortx($summarys, $sort_array);
$range=$range*0.4;

$extrabuttons=array();
$extrabuttons['previewselected']=array('name'=>'current',
									   'value'=>'report_merits_print.php',
									   'onclick'=>'checksidsAction(this)');
two_buttonmenu($extrabuttons,$book);
?>
<div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 

	  <div id="xml-checked-action" style="display:none;">
		<period>
		  <startdate><?php print $startdate;?></startdate>
		  <enddate><?php print $enddate;?></enddate>
		</period>
	  </div>
<?php
if(isset($com)){
?>
	  <fieldset class="right divgroup">
			<div class="left">
				<label><?php print_string($com['type']);?></label>
				<?php print $com['name'];?>
			</div>
			<div class="left">
			  <?php print '<label>'.get_string('total').' '.get_string('year').'</label> '.mysql_result($d_total,0);?>
			</div>
			<div class="right">
			  <?php print '<label>'.get_string('total').' '.get_string('period','register').'</label> '.$totaltotal;?>
			</div>
	  </fieldset>

<?php
}
?>
<div class="center">
	  <table class="listmenu sidtable">
		<tr id="sid-<?php print $sid;?>">
		  <th colspan="1">
			<label id="checkall"><?php print_string('checkall');?>
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll	(this);" />
			</label>
		  </th>
		  <th colspan="2" style="width:30%;">
		  	<?php print_string('student');?>
		  	<div class="rowaction" style="width:30%">
			  	<label><?php print_string('activities');?></label>
				<input type="radio" <?php if($view=='activity'){print 'checked';}?> name='view' onchange="processContent(this);" value="activity" name="umnfilter" title="[[filter]]">
			</div>
			<div class="rowaction" style="width:30%">
				<label><?php print_string('corevalues');?></label>
				<input type="radio" <?php if($view=='core_value'){print 'checked';}?> name='view' onchange="processContent(this);" value="core_value" name="umnfilter" title="[[filter]]">
		  	</div>
		  </th>
		  <th>
<?php 
		print get_string('sum').'<br />'. '('. 
			display_date($startdate).')';
?>
		  </th>
		  <th>
			<?php print get_string('total').'<br />'.get_string('year');?>
		  </th>
<?php
		reset($catdefs);
		while(list($catid,$catdef)=each($catdefs)){
			print '<th>'.$catdef['name'].'</th>';
			}
?>
		</tr>
<?php
	$rown=0;
	foreach($summarys as $summary){
		$rown++;
		$sid=$summary['sid'];
		$Student=fetchStudent_short($sid);
		$house=get_student_house($sid);
		$Merits=fetchMerits($sid,1,$bid,'%',$curryear);
?>
		<tr>
		  <td>
			<input type='checkbox' name='sids[]' value='<?php print $sid; ?>' />
			<?php print $rown;?>
		  </td>
		  <td>
			<?php print $house;?>
		  </td>
		  <td class="student">
			<a href="infobook.php?current=student_view.php&sid=<?php
			  print $sid;?>&sids[]=<?php print $sid;?>"  target="viewinfobook"
			  onclick="parent.viewBook('infobook');"> 
			  <?php print $Student['DisplayFullSurname']['value'] .' ('.$Student['RegistrationGroup']['value'].')'; ?>
			</a>
		  </td>
		  <td>
			<?php print $summary['total'];?>
		  </td>
		  <td>
			<?php print $Merits['Total']['Sum']['value'];?>
		  </td>
<?php
		reset($catdefs);
		while(list($catid,$catdef)=each($catdefs)){
			if(!isset($summary[$catid]['value'])){
				$colourclass='';$summary[$catid]['count']='';
				$summary[$catid]['value']='';
				}
			elseif($summary[$catid]['value']==0){$colourclass='nolite';}
			elseif($summary[$catid]['value']<-($range)){$colourclass='hilite';}
			elseif($summary[$catid]['value']<0){$colourclass='midlite';}
			elseif($summary[$catid]['value']>$range){$colourclass='golite';}
			elseif($summary[$catid]['value']>0){$colourclass='gomidlite';}
			print '<td class="'.$colourclass.'">&nbsp;'. 
					$summary[$catid]['value'].'</td>';
			}
?>
		</tr>
<?php	
		}
?>
	  </table>
</div>
	<input type="hidden" name="date0" value="<?php print $startdate;?>" />
 	<input type="hidden" name="date1" value="<?php print $enddate;?>" />
 	<input type="hidden" name="yid" value="<?php print $yid;?>" />
 	<input type="hidden" name="year" value="<?php print $year;?>" />
 	<input type="hidden" name="formid" value="<?php print $formid;?>" />
 	<input type="hidden" name="houseid" value="<?php print $comid;?>" />
 	<input type="hidden" name="stage" value="<?php print $stage;?>" />
 	<input type="hidden" name="bid" value="<?php print $bid;?>" />

 	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
 	<input type="hidden" name="choice" value="<?php print $choice;?>" />
 	<input type="hidden" name="current" value="<?php print $action;?>" />
	</form>
  </div>
