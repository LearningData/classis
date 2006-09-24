<?php
/*								demoiser.php

Update the database tables to match with entries from the curriculum
files. It does not (as yet) remove any data fro mthe database even if 
it has been removed from the curriculum files.
*/

$host='admin.php';
$current='demoiser.php';
$choice='demoiser.php';

function tableRead($table){
	$trows=array();
	$d_table=mysql_query("SELECT * FROM $table");
   	while($row=mysql_fetch_array($d_table,MYSQL_ASSOC)){
		$trows[]=$row;
		}
	return $trows;
	}

function tableClear($table){
	$d_table=mysql_query("DELETE FROM $table");
	}


function generate_random_name($gender){
	if($gender=='M'){
		$start=array('John','Paul','David','James','Eric','Ryan','Christopher','Mark','Edward',
					 'Chris','Luke','Robert','Terence');
		$middle=array('John','Paul','David','James','Eric','Ryan','Christopher','Mark','Edward',
					  'Chris','Luke','Robert','Terence');
		}
	else{
		$start=array('Emma','Claire','Tracy','Jane','Ann','Fiona','Lara','Sophie','Rachel',
					 'Louise','Jessica','Pamela');
		$middle=array('Emma','Claire','Tracy','Jane','Ann','Fiona','Lara','Sophie','Rachel',
					  'Louise','Jessica','Pamela');
		}
	$end=array('Smith','Lee','Patrick','Nunn','Bowman','Stewart','Jenkins',
			   'White','Kirkpatrick','Ibbotson','Owen','Davidson','Rowell','Phillips',
			   'Wainwright','Robson','Ball','Quinn','Davis','Johnson','Hope','Blair',
			   'Fawcett','Lawrence','Whitehead','Robinson','Wylie','McCartney','Collins',
			   'West','Anderson','Carter','Mitchell','Main','Mander','Royal','Welsh','Roy',
			   'Robertson','Riley','Newman','Turner','Hardy','Dene','Poll','Wright','Malick',
			   'Montgomery','Oscar','Forrest','Hughes','Reid','Murray','Hurley','Ashurst');
	$name=array();
    srand((double)microtime()*1000000);
	$name[]=$start[(rand() %  count($start))];
	$name[]=$middle[(rand() % count($middle))];
	$name[]=$end[(rand() %    count($end))];
    return($name);
	}


	$table='address';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['id'];
		if(mysql_query("UPDATE $table SET building='The house',
	streetno='23', street='Longstreet', neighbourhood='Housing
	estate', town='Small town', county='County Big', postcode='SG4 9PQ', country='England'
					 WHERE id='$id'")){}
		else{$error[]=mysql_error();}
		}

	$table='background';	
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['id'];
		if(mysql_query("UPDATE $table SET detail='A specific piece of
			background information from outside school.', entrydate='2000-01-01'
					 WHERE id='$id'")){}
		else{$error[]=mysql_error();}
		}

	$table='comments';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['id'];
		if(mysql_query("UPDATE $table SET detail='A specific comment
			about positive or negative progress.'
					 WHERE id='$id'")){}
		else{$error[]=mysql_error();}
		}

	$table='exclusions';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['student_id'];
		if(mysql_query("UPDATE $table SET reason='The reason for the exclusion.',
					startdate='2000-01-01', enddate='2000-01-02' WHERE student_id='$id'")){}
		else{$error[]=mysql_error();}
		}

	$table='fails';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['id'];
		if(mysql_query("UPDATE $table SET detail='The exam failed', entrydate='2005-04-01' WHERE id='$id'")){}
		else{$error[]=mysql_error();}
		}

	$table='guardian';
	$trows=array();
	$trows=tableRead($table);
	while (list($index, $row) = each($trows)) {
		$id=$row['id'];
		$d_gidsid=mysql_query("SELECT relationship FROM gidsid 
				WHERE guardian_id='$id'");
		$rel=mysql_result($d_gidsid,0);
		if($rel=='PAF'){$gender='M';}else{$gender='F';}
		$name=generate_random_name($gender);
		if(mysql_query("UPDATE $table SET surname='$name[2]', forename='$name[0]', middlenames='$name[1]'
				WHERE id='$id'")){}
		else{$error[]=mysql_error();}
		}

	$table='history';
	$trows=tableClear($table);

	$table='incidents';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['id'];
		if(mysql_query("UPDATE $table SET detail='The nature of the incident.',
					entrydate='2006-04-01' WHERE id='$id'")){}
		else{$error[]=mysql_error();}
		}

	$table='info';
	$trows=array();
	$trows=tableRead($table);
	while (list($index, $row) = each($trows)) {
		$id=$row['student_id'];
		if(mysql_query("UPDATE $table SET formerupn='20987',
			entrydate='2001-04-01', leavingdate='', nationality='British', medical='N'
				WHERE student_id='$id'")){}
		else{$error[]=mysql_error();}
		}

	$table='phone';
	$trows=array();
	$trows=tableRead($table);
	while (list($index, $row) = each($trows)) {
		$id=$row['id'];
		if(mysql_query("UPDATE $table SET number='1907893333'
				WHERE id='$id'")){}
		else{$error[]=mysql_error();}
		}

	$table='prizes';
	$trows=array();
	$trows=tableRead($table);
	while (list($index, $row) = each($trows)) {
		$id=$row['id'];
		if(mysql_query("UPDATE $table SET detail='A special
				achievement.', teacher_id=''
				WHERE id='$id'")){}
		else{$error[]=mysql_error();}
		}

	$table='sencurriculum';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['senhistory_id'];
		if(mysql_query("UPDATE $table SET comments='The background.',
					targets='To improve.', outcome='The result.' WHERE
					senhistory_id='$id' AND  subject_id='General'")){}
		else{$error[]=mysql_error();}
		}

	$table='student';
	$trows=array();
	$trows=tableRead($table);
	while(list($index, $row)=each($trows)){
		$id=$row['id'];
		if($row['gender']=='M'){$gender='F';}else{$gender='M';};
		$name=generate_random_name($gender);
		if(mysql_query("UPDATE $table SET surname='$name[2]',
				forename='$name[0]', middlenames='$name[1]', dob='1998-04-01'
				WHERE id='$id'")){}
		else{$error[]=mysql_error();}
		}

	$table='users';
	$trows=tableRead($table);
	while (list($index, $row) = each($trows)) {
		$id=$row['uid'];
		$username=$row['username'];
		if($username!='administrator' and $username!='office'){
			$nun='Prof'.$index;
			$passwd=md5('guest');
			if(mysql_query("UPDATE $table SET username='$nun',
			forename='P', surname='Prof', email='', logcount='0',
			passwd='$passwd', ip=''
				WHERE uid='$id'")){}
			else{$error[]=mysql_error();}
			if(mysql_query("UPDATE tidcid SET teacher_id='$nun'
			WHERE teacher_id='$username'")){}
			else{$error[]=mysql_error();}
			if(mysql_query("UPDATE reportentry SET teacher_id='$nun'
			WHERE teacher_id='$username'")){}
			else{$error[]=mysql_error();}
			if(mysql_query("UPDATE comments SET teacher_id='$nun'
			WHERE teacher_id='$username'")){}
			else{$error[]=mysql_error();}
			if(mysql_query("UPDATE incidents SET teacher_id='$nun'
			WHERE teacher_id='$username'")){}
			else{$error[]=mysql_error();}
			if(mysql_query("UPDATE grading SET author='$nun'
			WHERE author='$username'")){}
			else{$error[]=mysql_error();}
			if(mysql_query("UPDATE form SET teacher_id='$nun'
			WHERE teacher_id='$username'")){}
			else{$error[]=mysql_error();}
			if(mysql_query("UPDATE markdef SET author='$nun'
			WHERE author='$username'")){}
			else{$error[]=mysql_error();}
			}
		elseif($username=='administrator'){
			$passwd=md5('demoadmin');
			if(mysql_query("UPDATE users SET username='administrator',
			email='stj@laex.org', logcount='0',
			passwd='$passwd', ip=''
				WHERE uid='$id'")){}
			else{$error[]=mysql_error();}
			}
		elseif($username=='office'){
			$passwd=md5('demooffice');
			if(mysql_query("UPDATE users SET username='office',
			email='', logcount='0',
			passwd='$passwd', ip=''
				WHERE uid='$id'")){}
			else{$error[]=mysql_error();}
			}
		}

	$table='reportentry';
	if(mysql_query("UPDATE $table SET comment='A constructive comment from a subject teacher.'")){$error[]=mysql_error();}

	$table='score';
	if(mysql_query("UPDATE $table SET comment=''")){$error[]=mysql_error();}

	$table='form';
	$trows=tableRead($table);
	$name=array('D','C','A','B');
	$i=0;
	$yid='';
	while(list($index,$row)=each($trows)){
		if($yid!=$row['yeargroup_id']){$i=0; $yid=$row['yeargroup_id'];}
		$id=$row['id'];
		$nid=$yid.'-'.$name[$i];
		if(mysql_query("UPDATE $table SET id='$nid', name='$nid' 
				WHERE id='$id'")){}
		else{print mysql_error();}
		if(mysql_query("UPDATE student SET form_id='$nid'
				WHERE form_id='$id'")){}
		else{print mysql_error();}
		if(mysql_query("UPDATE community SET name='$nid'
				WHERE name='$id' AND type='form'")){}
		else{print mysql_error();}
		$i++;
		}

	$table='classes';
	$trows=tableRead($table);
	$name=array('D','C','A','B');
	while(list($index,$row)=each($trows)){
		if($row['generate']=='forms'){
			$bid=$row['subject_id'];
			$crid=$row['course_id'];
			$stage=$row['stage'];
			$d_class=mysql_query("SELECT * FROM class WHERE stage='$stage'
				AND course_id='$crid' AND subject_id='$bid'");
			$i=0;
			while($row=mysql_fetch_array($d_class,MYSQL_ASSOC)){
				$cid=$row['id'];
				$ncid=$bid . $stage . $name[$i];
				$i++;
				if(mysql_query("UPDATE class SET id='$ncid'
				WHERE id='$cid'")){}
				else{print mysql_error();}
				if(mysql_query("UPDATE tidcid SET class_id='$ncid'
				WHERE class_id='$cid'")){}
				else{print mysql_error();}
				if(mysql_query("UPDATE midcid SET class_id='$ncid'
				WHERE class_id='$cid'")){}
				else{print mysql_error();}
				if(mysql_query("UPDATE cidsid SET class_id='$ncid'
				WHERE class_id='$cid'")){}
				else{print mysql_error();}
				}
			}
		}
$result[]='You\'ve been demoised!';
include('scripts/results.php');
?>