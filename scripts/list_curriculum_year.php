<?php 
/**						list_curriculum_year.php
 *
 */

if(!isset($required)){$required='no';}
if(!isset($listname)){$listname='curryear';}
if(!isset($listlabel)){$listlabel='';}
if(!isset($curryear)){$selyear=get_curriculumyear();$current_curryear=$selyear;}
else{$selyear=$curryear;$current_curryear=get_curriculumyear();}
$years[]=array('id'=>$current_curryear-1,'name'=>display_curriculumyear($current_curryear-1));
$years[]=array('id'=>$current_curryear,'name'=>display_curriculumyear($current_curryear));
include('scripts/set_list_vars.php');
list_select_list($years,$listoptions,$book);
unset($listoptions);
unset($current_curryear);
?>