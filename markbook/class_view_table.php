<?php
/**							class_view_table.php
 *
 * Generates the array $viewtable and stores as a session variable.
 *
 *
 */

$orderby=get_studentlist_order();
$d_students=mysql_query("SELECT * FROM students ORDER BY $orderby"); 
$rowno=0;
$viewtable=array();

while($student=mysql_fetch_array($d_students, MYSQL_ASSOC)){
	$sid=$student['student_id'];
	$SEN_field=fetchStudent_singlefield($sid,'SENFlag');
	$Medical_field=fetchStudent_singlefield($sid,'MedicalFlag');
	$comment=comment_display($sid);
	$Attendance=fetchcurrentAttendance($sid);
	
	if($student['preferredforename']!=''){
		$displaypfn='&nbsp;('.$student['preferredforename'].')';
		}
	else{$displaypfn='';}
	$studentrow=array('row'=>$rowno, 
					  'sen'=>$SEN_field['SENFlag']['value'],
					  'medical'=>$Medical_field['MedicalFlag']['value'],
					  'commentclass'=>$comment['class'], 
					  'commentbody'=>$comment['body'], 
					  'attstatus'=>$Attendance['Status']['value'], 
					  'attcode'=>$Attendance['Code']['value'], 
					  'attcomment'=>$Attendance['Comment']['value'], 
					  'atttime'=>$Attendance['Logtime']['value'], 
					  'sid'=>$sid,
					  'surname'=>$student['surname'],
					  'forename'=>$student['forename'],
					  'preferredforename'=>$displaypfn,
					  'form_id'=>$student['form_id'],
					  'class_id'=>$student['class_id']
					  );

	/* Compile in reverse order to allow referring back to older columns. */
	for($c=$c_marks-1;$c>-1;$c--) {
		$col_mid=$umns[$c]['id'];
		$score=array();

		/* The mark can be one of the five kinds - if a score or hw
		 * then one of a further five.
		 */
		$marktype=$umns[$c]['marktype'];
		$scoretype=$umns[$c]['scoretype'];
		$asstype=$umns[$c]['assessment'];
		$scoreclass='grade';/* Will only be overridden by a report. */
		if($marktype=='score' or $marktype=='hw'){
			if($asstype=='other'){$scoreclass.=' other';}
			$d_score=mysql_query("SELECT * FROM score 
					WHERE mark_id='$col_mid' AND student_id='$sid';");
			$score=mysql_fetch_array($d_score,MYSQL_ASSOC);				
			/*score can be one of four types: grade, value, percentage, comment*/
			if($scoretype=='grade'){
				$out=scoreToGrade($score['grade'],$scoregrades[$c]);
				$outrank=$score['grade'];
				}
			elseif($scoretype=='value'){
				$out=$score['value'];
				$outrank=$score['value'];			      
				}
			elseif($scoretype=='percentage'){
				list($out,$percent,$outrank)=scoreToPercent($score['value'],$score['outoftotal']);
				}
			elseif($scoretype=='comment'){
				$out=$score['comment'];
				$outrank=-100;
				}
			}
			/*********************************************************/
		elseif($marktype=='average'){
				/*Mark is average of several score values*/
				$scoreclass.=' derived';
				$mids=explode(' ',$midlist[$c]);
				if($umns[$c]['scoretype']=='grade'){
					$grading_grades=$scoregrades[$c];
					$gradesum=0;
					$gradecount=0;
					foreach($mids as $mid){
						if(isset($studentrow["score$mid"])){$iscore=$studentrow["score$mid"];}
						else{$iscore=array();}
						if(isset($iscore['grade'])){
							$gradesum=$gradesum+$iscore['grade'];
							$gradecount++;
							}
						}
					if($gradecount>0){
						$score_grade=$gradesum/$gradecount;
						$score_grade=round($score_grade);
						$grade=scoreToGrade($score_grade,$grading_grades);
						$out=$grade;
						$outrank=$score_grade;
						$score['grade']=$score_grade;
						}
					else{$outrank=-100;$out='';unset($score_grade);}
					}
				elseif($umns[$c]['scoretype']=='percentage'){
					$scoresum=0;
					$scorecount=0;
					foreach($mids as $mid){
						$iscore=$studentrow["score$mid"];
						if($iscore['value']){
							list($dislpay,$percent,$cent)=scoreToPercent($iscore['value'],$iscore['outoftotal']);
							$scoresum+=$cent;
							$scorecount++;
							}
						}
					if($scorecount>0){
						$scoresum=$scoresum/$scorecount;
						$score['value']=$scoresum;
						}
					list($display,$out,$outrank)=scoreToPercent($scoresum);
					}
				else{
					$scoresum=0;
					$scorecount=0;
					foreach($mids as $mid){
						$iscore=$studentrow["score$mid"];
						if($iscore['value']){
							$scoresum=$scoresum+$iscore['value'];
							$scorecount++;
							}
						}
					if($scorecount>0){
						$scoresum=$scoresum/$scorecount;
						$out=$scoresum;$outrank=$scoresum;
						$score['value']=$scoresum;
						}
					else{$out='';$outrank=-100;}
					}
			}
		
		/*********************************************************/
	   	elseif($marktype=='sum'){
			/*Mark is the sum of several score values*/
			$scoreclass.=' derived';
			$mids=explode(' ',$midlist[$c]);
			$score_value=0;
			$score_total=0;
			foreach($mids as $mid){
				$iscore=$studentrow["score$mid"];
				if($iscore['value']){$score_value+=$iscore['value']; $yesval=1;}
				if($iscore['outoftotal']){
					$score_total+=$iscore['outoftotal']; 
					$yestotal=1;
					}
				}
			if(isset($yestotal)){
				/*mark's were percentage scores*/
				list($dislpay,$out,$outrank)=scoreToPercent($score_value,$score_total);
				$score['value']=$out;
				}
			else{
				/*otherwise mark's were raw scores*/
				if(isset($yesval)){$out=$score_value; $outrank=$score_value; $score['value']=$score_value;}
				else{$out='';$outrank=-100;}
				}
			unset($yesval);
			unset($yestotal);
			}
		
		/*********************************************************/
	   	elseif($marktype=='dif'){
			/*Mark is the difference of two score values*/
			$scoreclass.=' derived';
			$mids=(array)explode(' ',$midlist[$c]);
			$score_value=0;
			$score_total=0;
			foreach($mids as $mid){
				$iscore=$studentrow["score$mid"];
				if(!isset($firstscore['value']) and $iscore['value']!==''){$firstscore=$iscore;}
				if(isset($iscore['value']) and $iscore['value']!==''){$lastscore=$iscore;}
				}
			if($firstscore['value'] and $lastscore['value']){
				$score_value=$lastscore['value']-$firstscore['value'];
				$yesval=1;
				}
			if(isset($yesval)){$out=$score_value; $outrank=$score_value; $score['value']=$score_value;}
			else{$out='';$outrank=-100;}
			unset($yesval);
			unset($yestotal);
			unset($firstscore);
			unset($lastscore);
			}

		/*********************************************************/
	   	elseif($marktype=='tally'){
			/* Mark tallies (counts) the number of grades obtained */
			$scoreclass.=' derived';
			$mids=explode(' ',$midlist[$c]);
			$score_value=0;
			foreach($mids as $mid){
				$iscore=$studentrow["score$mid"];
				if($iscore['value']){$score_value++; $yesval=1;}
				}
			if(isset($yesval)){$out=$score_value; $outrank=$score_value; $score['value']=$score_value;}
			else{$out='';$outrank=-100;}
			unset($yesval);
			}

		/*********************************************************/
	   	elseif($marktype=='level'){
			$scoreclass.=' derived';
			$mid=$midlist[$c];
			$iscore=$studentrow["score$mid"];
			/*then mark is the levelled grade of a score*/
			list($out,$outrank)=scoreToLevel($iscore['value'],$iscore['outoftotal'],$levels[$c]);
			}

		/*********************************************************/
	   	elseif($marktype=='oldcompound'){
			/*Mark is a compound column*/
			$mids=explode(' ',$midlist[$c]);
			$yesval=0;
			foreach($mids as $mid){
				$iscore=$studentrow["score$mid"];
				if($iscore['value']){$yesval++;}
				}
			if($yesval<sizeof($mids)){$out='c';}else{$out='C';}
			$outrank=-100;
			}

		/*********************************************************/
	   	elseif($marktype=='compound'){
			/*Mark is a compound column*/
			$scoreclass='derived';
			$rep=checkReportEntryCat($umns[$c]['midlist'],$sid,$bid[0],$umns[$c]['component']);
			$out='<div class="'.$rep['class'].'"><a href="markbook.php?current=new_edit_reports.php&cancel=class_view.php&midlist='.$umns[$c]['midlist'].'&pid='.$umns[$c]['component'].'&sid='.$sid.'&bid='.$bid[0].'&nextrow='.$rowno.'">'.$rep['result'].'</a></div>';
			$score['value']=$rep['result'];
			$score['outoftotal']=100;
			$outrank=$rep['value'];
			}

		/*********************************************************/
	   	elseif($marktype=='report'){
			/*Mark is a compound report column*/
			$reportentryn=checkReportEntry($umns[$c]['midlist'],$sid,$bid[0],$umns[$c]['component']);
			$out='<a href="markbook.php?current=new_edit_reports.php&cancel=class_view.php&midlist='.$umns[$c]['midlist'].'&pid='.$umns[$c]['component'].'&sid='.$sid.'&bid='.$bid[0].'&nextrow='.$rowno.'">R'.$reportentryn.'</a>';
			if($reportentryn>0){$scoreclass='report vspecial';}else{$scoreclass='report';};
			$outrank=-100;
			}

		/********finished with this mark*******************/
		/*		 If no $out set then the mark must be faulty......*/
		if(!isset($out)){$out=''; $outrank=-100;}
		/* .....in case!!!!! */

		/*three entries for each score in the student's row in the $viewtable*/
		$studentrow[$col_mid]=$out;
		/*displayed on the screen*/
		$studentrow["rank$col_mid"]=$outrank;
		/*the criteria used to sort by should the column be ranked*/
		if(!isset($score['outoftotal'])){$score['outoftotal']='';}
		if(!isset($score['value'])){$score['value']='';}
		if(!isset($score['grade'])){$score['grade']='';}
		if(!isset($score['comment'])){$score['comment']='';}
		if(!isset($score['extra'])){$score['extra']='';}

		$totals[$col_mid]['grade']+=$score['grade'];
		$totals[$col_mid]['value']+=$score['value'];
		$totals[$col_mid]['no']++;

		$score['scoreclass']=$scoreclass;
		$studentrow["score$col_mid"]=$score;
		/*and score values from the database to be used by column_scripts*/
		}

	array_push($viewtable, $studentrow);
	$rowno++;
	}


/**************************************************************
 *		Rank order the table according to $umnrank choice     
 */	

if($umnrank=='surname'){
	/*
	  Already sorted by mysql so not needed
	  and sortx doesn't support utf8!
	*/
	}
else{
	$sort_array[0]['name']="rank$umnrank";
	$sort_array[0]['sort']='DESC';
	$sort_array[0]['case']=TRUE;
	sortx($viewtable, $sort_array);
	}

/*	All finished.*/
$_SESSION['viewtable']=$viewtable;
$_SESSION['umns']=$umns;
?>
