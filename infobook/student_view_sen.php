<?php
/**                                  student_view_sen.php
 */

$cancel='student_view.php';
$action='student_view_sen1.php';
if(isset($_POST['bid'])){$selbid=$_POST['bid'];}else{$selbid='';}
if(isset($_GET['bid'])){$selbid=$_GET['bid'];}

$SEN=fetchSEN($sid);

if($Student['SENFlag']['value']=='N'){
	two_buttonmenu();

	/*Check user has permission to view*/
	$yid=$Student['YearGroup']['value'];
	$perm=getSENPerm($yid,$respons);
	include('scripts/perm_action.php');

?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="center">	
		<legend><?php print_string('notsenstudent','seneeds');?></legend>
		<button onClick="processContent(this);" name="sub" 
				value="senstatus"><?php print_string('changesenstatus','seneeds');?></button>
	  </fieldset>
	<input type="hidden" name="current" value="<?php print $action;?>"/>
	<input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
	<input type="hidden" name="choice" value="<?php print $choice;?>"/>
	</form>
  </div>
<?php
	}
else{

	$book='seneeds';
	include('seneeds/sen_view.php');
	}
?>