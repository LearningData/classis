<?php
/**			  					fees_new_concept_action.php
 */

$action='fees_concept_list.php';
$cancel='fees_concept_list.php';
$conid=$_POST['conid'];
$feeyear=$_POST['feeyear'];

$action_post_vars=array('feeyear');
include('scripts/sub_action.php');

if($sub=='Submit'){

	if($conid==-1){
		mysql_query("INSERT INTO fees_concept SET name='';");
		$conid=mysql_insert_id();
		}

	$Concept=fetchConcept();
	foreach($Concept as $index => $val){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
			if($val['table_db']=='fees_concept'){
				mysql_query("UPDATE fees_concept SET $field='$inval' WHERE id='$conid';");
				}
			}
		}

	}


include('scripts/results.php');
include('scripts/redirect.php');
?>
