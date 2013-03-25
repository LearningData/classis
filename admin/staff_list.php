<?php 
/**								staff_list.php
 *
 */

$choice='staff_list.php';
$action='staff_list_action.php';
if(isset($_POST['listsecid'])){$listsecid=$_POST['listsecid'];}else{$listsecid='';}
if(isset($_POST['listroles'])){$listroles=$_POST['listroles'];}else{$listroles=array();}
if(isset($_POST['listoption'])){$listoption=$_POST['listoption'];}else{$listoption='current';}

/* Super user perms for user accounts. */ 
$aperm=get_admin_perm('u',$_SESSION['uid']);

if($_SESSION['role']=='admin' or $aperm==1 or $_SESSION['role']=='office'){
	$extrabuttons['export']=array('name'=>'current','value'=>'staff_export.php');
	}
two_buttonmenu($extrabuttons);

?>

  <div class="content" id="viewcontent">


	<fieldset class="divgroup left">
	  <div class="center">
		<form name="formtoprocess2" id="formtoprocess2" method="post" novalidate action="<?php print $host; ?>">
<?php
	$sections=list_sections();
	$listlabel='section';
	$listname='listsecid';
	include('scripts/set_list_vars.php');
	$sections[]=array('id'=>'','name'=>get_string('wholeschool'));
	list_select_list($sections,$listoptions,$book);
	unset($listoptions);
?>


<div style="float:right;">
  <button style="font-size:small;"  type="submit" name="sub" value="list">
	<?php print_string('filterlist');?>
  </button>
</div>

<div class="center">
  <table class="listmenu">
	<tr><th><?php print get_string('role',$book);?></th></tr>
<?php
	$userroles=$CFG->roles;
	foreach($userroles as $userrole){
		if(in_array($userrole,$listroles)){$checked=' checked="checked" ';}else{$checked='';}
		print '<tr><td><input type="checkbox" name="listroles[]" '.$checked.' value="'.$userrole.'">'.get_string($userrole).'</input></td></tr>';
		}
?>
</table>

<?php
	if($aperm==1){
?>
		  <table class="listmenu">
			<tr>
<?php
	$options=array(array('id'=>'current','name'=>'current'),array('id'=>'previous','name'=>'previous'));
	foreach($options as $option){
		if($option['id']==$listoption){$checked=' checked="checked" ';}else{$checked='';}
		print '<td><input type="radio" name="listoption" '.$checked.' value="'.$option['id'].'">'.get_string($option['name'],$book).get_string('staff',$book).'</input></td>';
		}
?>
			</tr>
		  </table>
<?php
		}
?>

</div>
	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	  <input type="hidden" name="cancel" value="<?php print ''; ?>">
	  </form>
	</div>
  </fieldset>

  <div class="right">
  <form name="formtoprocess" id="formtoprocess" method="post" novalidate action="<?php print $host; ?>">
	<table class="listmenu">
	  <caption><?php print get_string($listoption,$book).' '.get_string('staff',$book);?></caption>
		  <tr>
			<th style="width:1em;">
			  <?php print_string('checkall'); ?>
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this,'uids[]');" />
			</th>
			<th><?php print_string('surname',$book);?></th>
			<th><?php print_string('forename',$book);?></th>
			<th><?php print_string('username');?></th>
			<th><?php print_string('email',$book);?></th>
			<th><?php print_string('role',$book);?></th>
		  </tr>

<?php


	if($aperm==1 and $listoption=='previous'){
		$nologin='1';
		}
	else{
		$nologin='0';
		}

	if($listsecid!=''){
		/* Limit staff list to one section. */
		foreach($sections as $section){
			if($section['id']==$listsecid){$selsection=$section;}
			}
		$users=(array)list_group_users_perms($selsection['gid'],$nologin);
		}
	else{
		$users=(array)list_all_users($nologin);
		}



	foreach($users as $user){
		$User=(array)fetchUser($user['uid']);
		if((in_array($user['role'],$listroles) or sizeof($listroles)==0) and $user['username']!='administrator'){
?>
		<tr>
		  <td>
			<input type="checkbox" name="uids[]" value="<?php print $user['uid'];?>" />
		  </td>
		  <td>
<?php
			if($aperm==1 or $user['uid']==$_SESSION['uid'] or $_SESSION['role']=='office'){
				print '<a href="admin.php?current=staff_details.php&cancel='.$choice.'&choice='.$choice.'&seluid='.$user['uid'].'">'.$User['Surname']['value'].'</a>';
				}
			else{
				print $User['Surname']['value'];
				}
?>
		  </td>
		  <td><?php print $User['Forename']['value'];?></td>
		  <td>
		  <?php print $User['Username']['value'];?>
		  </td>
		  <td><?php print $User['EmailAddress']['value'];?></td>
			<td><?php print get_string($User['Role']['value']);?></td>
		</tr>
<?php
					}
				}
?>

	  </table>

	  <input type="hidden" name="current" value="<?php print $action; ?>" />
	  <input type="hidden" name="choice" value="<?php print $choice; ?>" />
	  <input type="hidden" name="cancel" value="<?php print ''; ?>" />
	</form>
	</div>


	<div class="left">
<?php
	require_once('lib/eportfolio_functions.php');
	//html_document_drop('','section'.$listsecid);
?>
	</div>

  </div>
<script language="JavaScript" type="text/javascript" src="js/documentdrop.js?version=1042"></script>