<?php
/**						scripts/perm_action.php
 */
if(!isset($neededperm)){$neededperm='r';}
if($perm["$neededperm"]!=1){
	$result[]=get_string('nopermissions',$book);
	$current=$cancel;
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}
unset($neededperm);
?>