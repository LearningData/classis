<?php
/**								  		yeargroup_matrix.php
 */

$choice='yeargroup_matrix.php';
$action='yeargroup_matrix_action.php';

three_buttonmenu();
?>
  <div class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post"
										action="<?php print $host; ?>" >

	<fieldset class="right">
		  <legend><?php print_string('assignyeartoteacher',$book);?></legend>

		<div class="center">
<?php $liststyle='width:95%;'; $required='yes'; include('scripts/list_teacher.php');?>
		</div>

		<div class="center">
<?php $liststyle='width:95%;'; $required='yes'; include('scripts/list_year.php');?>
		</div>

	</fieldset>

	<div class="left">
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('yeargroup');?></th>
		  <th><?php print_string('numberofstudents',$book);?></th>
		  <th><?php print_string('yearresponsible',$book);?></th>
		</tr>
<?php

	$nosidstotal=0;
	$d_year=mysql_query("SELECT * FROM yeargroup ORDER BY section_id, id");
	while($year=mysql_fetch_array($d_year,MYSQL_ASSOC)){
		$yid=$year['id'];
		$nosids=countinCommunity(array('type'=>'year','name'=>$yid));
		$nosidstotal=$nosidstotal+$nosids;
	   	print '<tr><td>';
	   		print '<a href="admin.php?current=yeargroup_edit.php&cancel='.$choice.'&choice='.$choice.'&newtid='.$tid.'&newyid='.$yid.'">'.$year['name'].'</a>';
		print '</td>';
	   	print '<td>'.$nosids.'</td><td>';
		$yearperms=array('r'=>1,'w'=>1,'x'=>1);/*head of year only*/
		$users=(array)getPastoralStaff($yid,$yearperms);
		while(list($uid,$user)=each($users)){
			if($user['role']!='office' and $user['role']!='admin'){
				print '<a href="admin.php?current=responsables_edit_pastoral.php&action='. 
				$choice.'&uid='.$uid.'&yid='.$yid.'">'.$user['username'].' '.'</a>';
				}
			}
		print '</td></tr>';
		}
?>
		  <tr>
			<th>
			  <?php print get_string('total',$book).' '.get_string('numberofstudents',$book);?>
			</th>
			<td><?php print $nosidstotal;?></td>
			<td>&nbsp;</td>
		  </tr>
	  </table>
	</div>

<?php 

	if($_SESSION['role']=='office'  or $_SESSION['role']=='admin'){

?>
	<div class="right">
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('studentsnotonroll',$book);?></th>
		  <th><?php print_string('numberofstudents',$book);?></th>
		</tr>
<?php

		$communities=array();
		$communities[]=array('type'=>'enquired','name'=>'enquired');
		$communities[]=array('type'=>'applied','name'=>'applied');
		$communities[]=array('type'=>'accepted','name'=>'accepted');
		$communities[]=array('type'=>'alumni','name'=>getCurriculumYear());
		while(list($index,$community)=each($communities)){
			$nosids=countinCommunity($community);
			print '<tr><td>';
	   		print '<a href="admin.php?current=yeargroup_edit.php&cancel='.$choice.'&choice='.$choice.'&comtype='.$community['type'].'">'.get_string($community['type'],'infobook').'</a>';
			print '</td>';
			print '<td>'.$nosids.'</td></tr>';
			}
?>
	  </table>
	</div>
<?php
	}
?>
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
  </div>