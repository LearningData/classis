<?php	
/*												infobook/fetch_student.php

	Retrieves all infobook information about one student using only their sid.
	Returns the data in an array $Student and sets it as a session variable.
*/	


function fetchStudent_short($sid){
   	$d_student=mysql_query("SELECT * FROM student WHERE id='$sid'");
	$student=mysql_fetch_array($d_student,MYSQL_ASSOC);
   	$d_info=mysql_query("SELECT * FROM info WHERE student_id='$sid'");
	$info=mysql_fetch_array($d_info,MYSQL_ASSOC);

	$Student=array();
	$Student['id_db']=$sid;
	$Student['Surname']=array('label' => 'surname',  
							  'value' => ''.$student['surname']);
	$Student['Forename']=array('label' => 'forename', 
							   'value' => ''.$student['forename']);
   	$Student['MiddleNames']=array('label' => 'middlenames', 
								  'value' => ''.$student['middlenames']);
	$Student['PreferredForename']=array('label' => 'preferredforename',
										'value' => ''.$student['preferredforename']);
	$Student['FormerSurname']=array('label' => 'formersurname', 
									'value' => ''.$student['formersurname']);

	if($student['preferredforename']!=''){$displaypfn='('.$student['preferredforename'].') ';}
	else{$displaypfn='';}
	if($student['middlenamelast']=='Y'){
		$Student['DisplayFullName']=array('label' => 'fullname',  
										  'value' => $displaypfn. 
										  $student['forename'] . ' ' . $student['surname']
										  .' '. $student['middlenames']);
		}
	else{
		$Student['DisplayFullName']=array('label' => 'fullname',  
										  'value' => $displaypfn . 
										  $student['forename'] . ' ' .$student['middlenames']
										  . ' ' .$student['surname']);
		}

	$Student['Gender']=array('label' => 'gender', 
							 'value' => ''.$student['gender']);
   	$Student['DOB']=array('label' => 'dateofbirth', 
						  'value' => ''.$student['dob']);
   	$Student['RegistrationGroup']=array('label' => 'formgroup',  
										'value' => ''.$student['form_id']);
   	$Student['YearGroup']=array('label' => 'yeargroup', 
								'value' => ''.$student['yeargroup_id']);
	return nullCorrect($Student);
	}


function fetchStudent_singlefield($sid,$tag){
	/*this is a ad-hoc function for use by student_list only at the moment*/
	/*to quickly get at sid fields outside of  student*/
	if($tag=='Nationality'){$fieldname='nationality';}
	elseif($tag=='EnrolNumber'){$fieldname='formerupn';}
	elseif($tag=='FirstLanguage'){$fieldname='firstlanguage';}
	elseif($tag=='EntryDate'){$fieldname='entrydate';}
	elseif($tag=='FirstContactPhone'){
		/*NOT a part of the xml def for Student but useful here*/
		$Contacts=(array)fetchContacts($sid);
		$Phones=(array)$Contacts[0]['Phones'];
		$Student[$tag]=array('label' => '',
							 'value' => '');
		while(list($phoneno,$Phone)=each($Phones)){
			$Student[$tag]['value']=$Student[$tag]['value'] . 
					$Phone['PhoneNo']['value'].' ';				
			}
		}

	if(isset($fieldname)){
		$d_info=mysql_query("SELECT $fieldname FROM info WHERE student_id='$sid'");
		$info=mysql_fetch_array($d_info,MYSQL_ASSOC);
		$Student[$tag]=array('label' => '',
							 'value' => ''.$info[$fieldname]);
		}
	return $Student;
	}

function fetchStudent($sid='-1'){
   	$d_student=mysql_query("SELECT * FROM student WHERE id='$sid'");
	$student=mysql_fetch_array($d_student,MYSQL_ASSOC);
   	$d_info=mysql_query("SELECT * FROM info WHERE student_id='$sid'");
	$info=mysql_fetch_array($d_info,MYSQL_ASSOC);


/*
	Student is an xml compliant array designed for use with Serialize
	to generate xml from the values in the database. Each value from
	the database is stored in an array element identified by its
	xmltag. Various useful accompanying attributes are also stored. Of
	particular use are Label for displaying the value and _db
	attributes facilitating updates to the database when values are
	changed (the type_db for instance facilitates validation).

	$Student['xmltag']=array('label' => 'Display label', 'field_db' =>
				'ClaSSdb field name', 'type_db'=>'ClaSSdb data-type', 'value' => $student['field_db']);


   	$Student['']=array('label' => '', 'field_db' => '',
					'type_db'=>'', 'value' => $student['']);
*/

	$Student=array();
	$Student['id_db']=$sid;
	$Student['Surname']=array('label' => 'surname', 
							  'inputtype'=> 'required',
							  'table_db' => 'student', 
							  'field_db' => 'surname',
							  'type_db'=>'varchar(30)', 
							  'value' => ''.$student['surname']);
	$Student['Forename']=array('label' => 'forename', 
							   'inputtype'=> 'required',
							   'table_db' => 'student', 
							   'field_db' => 'forename',
							   'type_db'=>'varchar(30)', 
							   'value' => ''.$student['forename']);
   	$Student['MiddleNames']=array('label' => 'middlenames', 
								  'table_db' => 'student', 
								  'field_db' => 'middlenames',
								  'type_db'=>'varchar(30)', 
								  'value' => ''.$student['middlenames']);
	$Student['PreferredForename']=array('label' =>
										'preferredforename', 
										'table_db' => 'student', 
										'field_db' => 'preferredforename',
										'type_db'=>'varchar(30)', 
										'value' => ''.$student['preferredforename']);
	$Student['FormerSurname']=array('label' => 'formersurname', 
									'table_db' => 'student', 
									'field_db' => 'formersurname',
									'type_db'=>'varchar(30)', 
									'value' => ''.$student['formersurname']);

	if($student['preferredforename']!=''){$displaypfn='('.$student['preferredforename'].') ';}
	else{$displaypfn='';}
	if($student['middlenamelast']=='Y'){
		$Student['DisplayFullName']=array('label' => 'fullname',  
										  'value' => $displaypfn . 
										  $student['forename'] . ' ' .$student['surname']
										  . ' ' .$student['middlenames']);
		}
	else{
		$Student['DisplayFullName']=array('label' => 'fullname',  
										  'value' => $displaypfn . 
										  $student['forename'] . ' ' .$student['middlenames']
										  . ' ' . $student['surname']);
		}

	$Student['Gender']=array('label' => 'gender', 
							 'inputtype'=> 'required',
							 'table_db' => 'student', 
							 'field_db' => 'gender',
							 'type_db'=>'enum', 
							 'value' => ''.$student['gender']);
   	$Student['DOB']=array('label' => 'dateofbirth', 
						  'table_db' => 'student', 
						  'field_db' => 'dob',
						  'type_db'=>'date', 
						  'value' => ''.$student['dob']);
   	$Student['RegistrationGroup']=array('label' => 'formgroup', 
										'value' => ''.$student['form_id']);
   	$Student['YearGroup']=array('label' => 'yeargroup',   
								'value' => ''.$student['yeargroup_id']);
	$Student['NCyearActual']=array('label' => 'ncyear',  
								   'id_db' => ''.$student['yeargroup_id'], 
								   'value' => ''.getNCyear($student['yeargroup_id']));
   	$Student['Nationality']=array('label' => 'nationality', 
								  'table_db' => 'info', 
								  'field_db' => 'nationality', 
								  'type_db'=>'char(30)', 
								  'value' => ''.$info['nationality']);
   	$Student['MedicalFlag']=array('label' => 'medicalinformation', 
								  'value' => ''.$info['medical']);
   	$Student['SENFlag']=array('label' => 'seninformation', 
							  'value' => ''.$info['sen']);
   	$Student['Religion']=array('label' => 'religion', 
							   'table_db' => 'info', 
							   'field_db' => 'religion',
							   'type_db' => 'enum', 
							   'value' => ''.$info['religion']);
   	$Student['FirstLanguage']=array('label' => 'firstlanguage', 
									'table_db' => 'info', 
									'field_db' => 'firstlanguage',
									'type_db'=>'enum', 
									'value' => ''.$info['firstlanguage']);
   	$Student['E-Mail']=array('label' => 'email',
							 'table_db' => 'info', 
							 'field_db' => 'email',
							 'type_db'=>'varhar(50)', 
							 'value' => ''.$info['email']);
   	$Student['EnrolNumber']=array('label' => 'enrolmentnumber', 
								  'table_db' => 'info', 
								  'field_db' => 'formerupn', 
								  'type_db'=>'varchar(13)', 
								  'value' => ''.$info['formerupn']);
	$Student['EntryDate']=array('label' => 'schoolstartdate', 
								'table_db' => 'info', 
								'field_db' => 'entrydate', 
								'type_db'=>'date', 
								'value' => ''.$info['entrydate']);
	$Student['LeavingDate']=array('label' => 'schoolleavingdate', 
								  'field_db' => 'leavingdate', 
								  'type_db'=>'date', 
								  'value' => ''.$info['leavingdate']);
   	$Student['Boarder']=array('label' => 'boarder', 
							  'table_db' => 'info', 
							  'field_db' => 'boarder',
							  'type_db'=>'enum', 
							  'value' => ''.$info['boarder']);
   	$Student['PartTime']=array('label' => 'parttime', 
							   'table_db' => 'info', 
							   'field_db' => 'parttime',
							   'type_db'=>'enum', 
							   'value' => ''.$info['parttime']);
   	$Student['TransportMode']=array('label' => 'modeoftransport', 
									'value' => ''.$info['transportmode']);
   	$Student['TransportRoute']=array('label' => 'transportroute', 
									 'value' => ''.$info['transportroute']);

	/*******Contacts****/

	$Student['Contacts']=fetchContacts($sid);


	/*******SEN****/
	$SEN=array();
	$d_senhistory=mysql_query("SELECT * FROM senhistory WHERE 
				student_id='$sid' ORDER BY startdate");
	/*only working for one senhistory per student but could be */
	/*ramped up for more*/

	$SENhistory=array();
	while($senhistory=mysql_fetch_array($d_senhistory,MYSQL_ASSOC)){
		$senhid=$senhistory['id'];
		$SENhistory['id_db']=$senhid;
	   	$SENhistory['SENprovision']=array('label' => 'provision', 
										  'table_db' => 'senhistory', 
										  'field_db' => 'senprovision',
										  'type_db'=>'enum', 
										  'value' => ''.$senhistory['senprovision']);
	   	$SENhistory['StartDate']=array('label' => 'startdate', 
									   'table_db' => 'senhistory', 
									   'field_db' => 'startdate',
									   'type_db'=>'date', 
									   'value' => ''.$senhistory['startdate']);
	   	$SENhistory['NextReviewDate']=array('label' => 'nextreviewdate', 
											'table_db' => 'senhistory', 
											'field_db' => 'reviewdate',
											'type_db'=>'date', 
											'value' => ''.$senhistory['reviewdate']);
		/*only working for one senhistory_id*/

		$d_sencurriculum=mysql_query("SELECT * FROM sencurriculum
				WHERE senhistory_id='$senhid' ORDER BY subject_id");
		$NationalCurriculum=array();
		while($sencurriculum=mysql_fetch_array($d_sencurriculum,MYSQL_ASSOC)){
			$Subject=array();
		   	$Subject['Subject']=array('label' => 'subject', 
									  'table_db' => 'sencurriculum', 
									  'field_db' => 'subject_id', 
									  'type_db'=>'varchar(10)', 
									  'value' => ''.$sencurriculum['subject_id']);
			$Subject['Strengths']=array('label' => 'strengths', 
										'table_db' => 'sencurriculum', 
										'field_db' => 'comments', 
										'type_db'=>'varchar(250)', 
										'value' => ''.$sencurriculum['comments']);
		   	$Subject['Weaknesses']=array('label' => 'weaknesses', 
										 'table_db' => 'sencurriculum', 
										 'field_db' => 'targets', 
										 'type_db'=>'varchar(250)', 
										 'value' => ''.$sencurriculum['targets']);
		   	$Subject['Strategies']=array('label' => 'strategies',
										 'table_db' => 'sencurriculum', 
										 'field_db' => 'outcome', 
										 'type_db'=>'varchar(250)', 
										 'value' => ''.$sencurriculum['outcome']);
		   	$NationalCurriculum[]=$Subject;
			}
		$SEN['NationalCurriculum']=$NationalCurriculum;
		}

	$SEN['SENhistory']=$SENhistory;
	$d_sentypes=mysql_query("SELECT * FROM sentypes WHERE 
					student_id='$sid' ORDER BY senranking");
	$SENtypes=array();
	while($sentypes=mysql_fetch_array($d_sentypes,MYSQL_ASSOC)){
		$SENtype=array();
	   	$SENtype['SENtypeRank']=array('label' => 'ranking',
									  'table_db' => 'sentypes', 
									  'field_db' => 'senranking', 
									  'type_db'=>'enum', 
									  'value' => ''.$sentypes['senranking']);
	   	$SENtype['SENtype']=array('label' => 'type', 
								  'table_db' => 'sentypes', 
								  'field_db' => 'sentype', 
								  'type_db'=>'enum', 
								  'value' => ''.$sentypes['sentype']);
		$SENtypes[]=$SENtype;
		}
	$SEN['SENtypes']=$SENtypes;
	$Student['SEN']=$SEN;


	/*******Exclusions****/
	$Exclusions=array();
	$d_exclusions=mysql_query("SELECT * FROM exclusions WHERE 
				student_id='$sid' ORDER BY startdate");
	while($exclusion=mysql_fetch_array($d_exclusions,MYSQL_ASSOC)){
		$Exclusion=array();
	   	$Exclusion['id_db']=$exclusion['startdate'];
	   	$Exclusion['Category']=array('label' => 'category', 
									 'type_db'=>'enum', 
									 'value' => ''.$exclusion['category']);
	   	$Exclusion['StartDate']=array('label' => 'startdate', 
									  'type_db'=>'date', 
									  'value' => ''.$exclusion['startdate']);
	   	$Exclusion['EndDate']=array('label' => 'enddate', 
									'type_db'=>'date', 
									'value' => ''.$exclusion['enddate']);
	   	$Exclusion['Reason']=array('label' => 'reason', 
								   'type_db'=>'varchar(60)', 
								   'value' => ''.$exclusion['reason']);
		$Exclusions[]=$Exclusion;
		}
	$Student['Exclusions']=$Exclusions;

	/*******Incidents****/
	$Incidents=array();
	$d_incidents=mysql_query("SELECT * FROM incidents WHERE
		student_id='$sid' ORDER BY entrydate DESC");
	while($incident=mysql_fetch_array($d_incidents,MYSQL_ASSOC)){
		$Incident=array();
		$Incident['id_db']=$incident['id'];
	   	$Incident['Category']=array('label' => 'category', 
									'type_db' => 'varchar(30)', 
									'value' => ''.$incident['category']);
	   	$Incident['Detail']=array('label' => 'detail', 
								  'type_db'=>'varchar(250)', 
								  'value' => ''.$incident['detail']);
	   	$Incident['Subject']=array('label' => 'subject', 
								   'type_db'=>'varchar(10)', 
								   'value' => ''.$incident['subject_id']);
	   	$Incident['Outcome']=array('label' => 'outcome', 
								   'type_db'=>'varchar(200)', 
								   'value' => ''.$incident['outcome']);
	   	$Incident['EntryDate']=array('label' => 'date', 
									 'type_db'=>'date', 
									 'value' => ''.$incident['entrydate']);
	   	$Incident['YearGroup']=array('label' => 'yeargroup', 
									 'type_db'=>'enum', 
									 'value' => ''.$incident['yeargroup_id']);
		$Incidents[]=$Incident;
		}
	$Incidents=$Incidents;
	$Student['Incidents']=$Incidents;


	/*******Backgrounds****/
	$Backgrounds=array();
	$d_catdef=mysql_query("SELECT name AS tagname, 
				subject_id AS background_type FROM categorydef WHERE 
				type='ent' ORDER BY rating");
	while($back=mysql_fetch_array($d_catdef,MYSQL_ASSOC)){
		$type=$back['background_type'];
		$tagname=ucfirst($back['tagname']);

		$Entries=array();
		$d_background=mysql_query("SELECT * FROM background WHERE 
				student_id='$sid' AND type='$type' ORDER BY yeargroup_id");
		while($entry=mysql_fetch_array($d_background,MYSQL_ASSOC)){
			$Entry=array();
			$Entry['id_db']=$entry['id'];
			$Entry['Teacher']=array('label' => 'teacher', 
									'type_db'=>'varchar(14)', 
									'value' => ''.$entry['teacher_id']);
			$Categories=array();
			$Categories=array('label' => 'category', 
							  'type_db'=>'varchar(100)', 
							  'value' => ' ');
			$pairs=explode(';',$entry['category']);
			for($c3=0; $c3<sizeof($pairs)-1; $c3++){
				list($catid, $rank)=split(':',$pairs[$c3]);
				$Category=array();
				$d_categorydef=mysql_query("SELECT name FROM categorydef
									WHERE id='$catid'");
				$catname=mysql_result($d_categorydef,0);
				$Category=array('label' => 'category', 
								'value_db' => ''.$catid,
								'type_db'=> 'varchar(30)', 
								'value' => ''.$catname);
				$Category['rating']=array('value' => $rank);
				$Categories['Category'][]=$Category;
				}
			if(!isset($Categories['Category'])){
				$Category=array('label' => 'category', 
								'value_db' => ' ',
								'type_db'=>'varchar(30)', 
								'value' => ' ');
				$Categories['Category'][]=$Category;
				}
			$Entry['Categories']=$Categories;
			$Entry['EntryDate']=array('label' => 'date',
									  'value' => ''.$entry['entrydate']);
			$Entry['Detail']=array('label' => 'details', 
								   'type_db'=>'varchar(250)', 
								   'value' => ''.$entry['detail']);
			$bid=$entry['subject_id'];
			if($bid!=' '){
				$d_subject=mysql_query("SELECT name FROM subject WHERE id='$bid'");
				$subjectname=mysql_result($d_subject,0);
				}
			else{$subjectname=$bid;}
			$Entry['Subject']=array('label' => 'subject', 
									'type_db'=>'varchar(15)', 
									'value_db' => ''.$bid, 
									'value' => ''.$subjectname);
			$Entry['YearGroup']=array('label' => 'yeargroup', 
									  'type_db'=>'smallint', 
									  'value' => ''.$entry['yeargroup_id']);
			$Entries[]=$Entry;
			}
		$Backgrounds["$tagname"]=$Entries;
		}

	$Student['Backgrounds']=$Backgrounds;

	return $Student;
	}


function fetchContacts($sid='-1'){
	$Contacts=array();
	$d_gidsid=mysql_query("SELECT * FROM gidsid WHERE student_id='$sid' ORDER BY priority");
	while($gidsid=mysql_fetch_array($d_gidsid,MYSQL_ASSOC)){
		$Contacts[]=fetchContact($gidsid);
		}
	return $Contacts;
	}

function fetchContact($gidsid=array('guardian_id'=>'-1')){
	$gid=$gidsid['guardian_id'];
	$d_guardian=mysql_query("SELECT * FROM guardian WHERE id='$gid'");
	$guardian=mysql_fetch_array($d_guardian,MYSQL_ASSOC);
	$Contact=array();
	$Contact['id_db']=$gid;
	$Contact['Order']=array('label' => 'priority', 
							'inputtype'=> 'required', 
							'table_db' => 'gidsid', 
							'field_db' => 'priority',
							'type_db'=>'enum', 
							'value' => ''.$gidsid['priority']);
	$Contact['ReceivesMailing']=array('label' => 'receivesmailing', 
									  'inputtype'=> 'required',
									  'table_db' => 'gidsid', 
									  'field_db' => 'mailing',
									  'type_db'=>'enum', 
									  'value' => ''.$gidsid['mailing']);
	$Contact['Relationship']=array('label' => 'relationship', 
								   'table_db' => 'gidsid', 
								   'field_db' => 'relationship',
								   'type_db'=>'enum', 
								   'value' => ''.$gidsid['relationship']);
	$Contact['Surname']=array('label' => 'surname', 
							  'inputtype'=> 'required',
							  'table_db' => 'guardian', 
							  'field_db' => 'surname',
							  'type_db' => 'varchar(30)', 
							  'value' => ''.$guardian['surname']);
	$Contact['Forename']=array('label' => 'forename', 
							   'table_db' => 'guardian', 
							   'field_db' => 'forename',
							   'type_db' => 'varchar(30)', 
							   'value' => ''.$guardian['forename']);
	$Contact['MiddleNames']=array('label' => 'middlenames', 
								  'table_db' => 'guardian', 
								  'field_db' => 'middlenames', 
								  'type_db' => 'varchar(30)', 
								  'value' => ''.$guardian['middlenames']);

	/*******ContactsAddresses****/
	$Addresses=array();
	$d_gidaid=mysql_query("SELECT * FROM gidaid WHERE guardian_id='$gid' ORDER BY priority");
	while($gidaid=mysql_fetch_array($d_gidaid,MYSQL_ASSOC)){
		$Addresses[]=fetchAddress($gidaid);
		}
	$Contact['Addresses']=$Addresses;

	/*******ContactsPhones****/
	$Phones=array();
	$d_phone=mysql_query("SELECT * FROM phone WHERE some_id='$gid' ORDER BY phonetype");
	while($phone=mysql_fetch_array($d_phone,MYSQL_ASSOC)){
		$Phones[]=fetchPhone($phone);
		}
	$Contact['Phones']=$Phones;

	return $Contact;
	}


function fetchPhone($phone=array('id'=>'-1')){
	$Phone=array();
	$Phone['id_db']=$phone['id'];
	$Phone['PhoneNo']=array('label' => 'phonenumber', 
							'table_db' => 'phone', 
							'field_db' => 'number',
							'type_db'=>'varchar(22)', 
							'value' => ''.$phone['number']);
	$Phone['PhoneType']=array('label' => 'phonetype', 
							  'table_db' => 'phone', 
							  'field_db' => 'phonetype',
							  'type_db'=>'enum', 
							  'value' => ''.$phone['phonetype']);
	return $Phone;
	}

function fetchAddress($gidaid=array('address_id'=>'-1','addresstype'=>'')){
	$Address=array();
	$aid=$gidaid['address_id'];
	$d_address=mysql_query("SELECT * FROM address WHERE id='$aid'");
	$address=mysql_fetch_array($d_address,MYSQL_ASSOC);
	$Address['id_db']=$aid;
	/*	 $Address['Order']=array('label' => 'priority', 
									'table_db' => 'gidaid', 'field_db' => 'priority',
									'type_db'=>'enum', 'value' => $gidaid['priority']);
	*/
	$Address['AddressType']=array('label' => 'type', 
								  'table_db'=>'gidaid', 
								  'field_db' => 'addresstype',
								  'type_db'=>'enum', 
								  'value' => $gidaid['addresstype']);
	$Address['BuildingName']=array('label' => 'building', 
								   'table_db' => 'address', 
								   'field_db' => 'building',
								   'type_db'=>'varchar(60)', 
								   'value' => ''.$address['building']);
	$Address['StreetNo']=array('label' => 'streetno.', 
							   'table_db' => 'address', 
							   'field_db' => 'streetno',
							   'type_db'=>'varchar(10)', 
							   'value' => ''.$address['streetno']);
	$Address['Road']=array('label' => 'street',
						   'table_db' => 'address', 
						   'field_db' => 'street',
						   'type_db'=>'varchar(100)', 
						   'value' => ''.$address['street']);
	$Address['Neighbourhood']=array('label' => 'neighbourhood',
									'table_db' => 'address', 
									'field_db' => 'neighbourhood',
									'type_db'=>'varchar(50)', 
									'value' => ''.$address['neighbourhood']);
	$Address['Town']=array('label' => 'town/city', 
						   'table_db' => 'address', 
						   'field_db' => 'town',
						   'type_db'=>'varchar(40)', 
						   'value' => ''.$address['town']);
	$Address['County']=array('label' => 'county', 
							 'table_db' => 'address', 
							 'field_db' => 'county',
							 'type_db'=>'varchar(40)', 
							 'value' => ''.$address['county']);
	$Address['Postcode']=array('label' => 'postcode',
							   'table_db' => 'address', 
							   'field_db' => 'postcode',
							   'type_db'=>'varchar(8)', 
							   'value' => ''.$address['postcode']);
	return $Address;
	}

function fetchComments($sid,$date){
	$Comments=array();
	/*if no date set choose this academic year*/
	$crid='KS3';
	if($date==''){$date=getCurriculumYear($crid)-2;}
	$d_comments=mysql_query("SELECT * FROM comments WHERE
			student_id='$sid' AND entrydate > '$date' 
			ORDER BY yeargroup_id DESC, entrydate DESC, id DESC, subject_id");
	while($comment=mysql_fetch_array($d_comments,MYSQL_ASSOC)){
		$Comment=array();
		$Comment['id_db']=$comment['id'];
		$bid=$comment['subject_id'];
		if($bid!=' ' and $bid!=''){
			$d_subject=mysql_query("SELECT name FROM subject WHERE id='$bid'");
			$subjectname=mysql_result($d_subject,0);
			}
		else{$subjectname=$bid;}
	   	$Comment['Subject']=array('label' => 'subject',
								  'value_db' => ''.$bid, 
								  'value' => ''.$subjectname);
	   	$Comment['Teacher']=array('label' => 'teacher', 
								  'value' => ''.$comment['teacher_id']);
		$Categories=array();
		$Categories=array('label' => 'category', 
						  'value' => ' ');
		$pairs=explode(';',$comment['category']);
		for($c3=0; $c3<sizeof($pairs)-1; $c3++){
			list($catid, $rank)=split(':',$pairs[$c3]);
			$Category=array();
			$d_categorydef=mysql_query("SELECT name FROM categorydef
				WHERE id='$catid'");
			$catname=mysql_result($d_categorydef,0);
			$Category=array('label' => $catname, 
							'value' => ''.$catid);
			$Category['rating']=array('value' => ''.$rank);
			$Categories['Category'][]=$Category;
			}
		if(!isset($Categories['Category'])){
			$Category=array('label' => ' ', 
							'type_db'=>'varchar(30)', 
							'value' => ' ');
			$Categories['Category'][]=$Category;
			}
		$Comment['Categories']=$Categories;
	   	$Comment['Detail']=array('label' => 'detail', 
								 'type_db'=>'varchar(250)', 
								 'value' => ''.$comment['detail']);
	   	$Comment['EntryDate']=array('label' => 'date', 
									'type_db'=>'date', 
									'value' => ''.$comment['entrydate']);
	   	$Comment['YearGroup']=array('label' => 'yeargroup', 
									'type_db'=>'smallint', 
									'value' => ''.$comment['yeargroup_id']);
		$Comments[]=$Comment;
		}
	return nullCorrect($Comments);
	}

function commentDisplay($sid,$date,$Comments=''){
	$commentdisplay=array();
	if($Comments==''){
		$Comments=fetchComments($sid,$date);
		}
	if(is_array($Comments)){
			if($Comments[0]['Categories']['Category'][0]['rating']['value']==-1){
				$commentdisplay['class']='negative';
				}
			else{$commentdisplay['class']='positive';}
			$header=$Comments[0]['Subject']['value']. 
								' ('.$Comments[0]['EntryDate']['value'].')';
			$commentdisplay['body']=$header.'<br />'.$Comments[0]['Detail']['value'];
			}
		else{$commentdisplay['class']='';$commentdisplay['body']='';}

	return $commentdisplay;
	}

/*these are for compatibility with NCYear field as defined by the CBDS */
/*for state schools in England and Wales - they are needed for */
/* fetchStudent to work - but can otherwise be ignored*/
function fetchNCYear($sid){
	$d_student=mysql_query("SELECT yeargroup_id FROM student WHERE id='$sid'");
	$yid=mysql_result($d_student,0);
	$ncyear=getNCYear($yid);
	return $ncyear;
	}

function getNCYear($yid){
	$ncyears=array('N' => 'Nursery', 'R' => 'Reception', '1' => '1',
	'2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' =>
	'7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' =>
	'12', '13' => '13', '14' => '14');	
	$ncyear=$ncyears[$yid];
	return $ncyear;
	}
?>
