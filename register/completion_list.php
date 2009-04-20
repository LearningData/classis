<?php
/**									completion_list.php
 *
 *   	Lists all completed registers for currentevent.
 */

$action='completion_list_action.php';
$choice='completion_list.php';

$registration_coms=list_communities('form');
$eveid=$currentevent['id'];


include('scripts/sub_action.php');
twoplusprint_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('registersthissession',$book);?></label>
  </div>
  <div id="viewcontent" class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		<table class="listmenu">
		<tr>
		  <th>
			<label id="checkall">
			  <?php print_string('checkall');?>
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
			</label>
		  </th>
		  <th><?php print_string('registrationgroup',$book); ?></th>
		  <th><?php print_string('status',$book);?></th>
		  <th><?php print_string('present',$book);?></th>
		  <th><?php print_string('absent',$book);?></th>
		</tr>
<?php


	while(list($index,$com)=each($registration_coms)){
		list($nosids,$nop,$noa)=check_communityAttendance($com,$eveid);
		if($nosids>0){
			if(($nop+$noa)==$nosids and $nosids!=0){$status='complete';$cssclass='';}
			else{$status='incomplete';$cssclass='vspecial';}
?>
		<tr>
		  <td>
			<input type="checkbox" name="comids[]" value="<?php print $com['id']; ?>" />
		  </td>
		  <td>
			<a onclick="parent.viewBook('register');" target="viewregister"  
			  href='register.php?current=register_list.php&cancel=completion_list.php&newfid=<?php print $com['name'];?>&checkeveid=0&startday='><?php print $com['displayname'];?></a>
		  </td>
		  <td class="<?php print $cssclass;?>">
			<?php print_string($status,$book);?>
		  </td>
		  <td>
			<?php print $nop;?>
		  </td>
		  <td>
			<?php print $noa;?>
		  </td>
		</tr>
<?php
			}
		}
?>
		</table>

		<input type="hidden" name="date" value="<?php print $currentevent['date'];?>" />
		<input type="hidden" name="session" value="<?php print $currentevent['session'];?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	    <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  </form>
  </div>
