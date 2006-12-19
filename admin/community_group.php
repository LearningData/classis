<?php
/**								  		community_group.php
 */

$choice='community_group.php';
$action='community_group_action.php';

if(isset($_POST['newcomtype'])){$newcomtype=$_POST['newcomtype'];}else{$newcomtype='ACADEMIC';}

$extrabuttons['createnewgroup']=array('name'=>'current','value'=>'community_group_rename.php');
three_buttonmenu($extrabuttons);
?>
  <div class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post"
		action="<?php print $host; ?>" >

	<fieldset class="right">
		  <legend><?php print_string('changetype',$book);?></legend>

		<div class="center">
		  <?php $onchange='yes';include('scripts/list_community_type.php');?>
		</div>

	</fieldset>

	<div class="left">
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('communitytype',$book);?></th>
		  <th><?php print_string('numberofstudents',$book);?></th>
		</tr>
<?php

	$nosidstotal=0;
	$communities=listCommunities($newcomtype);
	while(list($index,$com)=each($communities)){
		$nosids=countinCommunity($com);
		$nosidstotal=$nosidstotal+$nosids;
	   	print '<tr><td>';
	   		print '<a href="admin.php?current=community_group_edit.php&cancel='.$choice.'&choice='.$choice.'&newcomtype='.$newcomtype.'&comid='.$com['id'].'">'.$com['name'].'</a>';
		print '</td>';
	   	print '<td>'.$nosids.'</td><td>';
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


	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
  </div>