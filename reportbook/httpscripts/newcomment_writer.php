<?php
/**                    httpscripts/comment_writer.php
 */

require_once('../../scripts/http_head_options.php');
require_once($CFG->dirroot.'/lib/statementbank.php');

if(isset($_GET['sid'])){$sid=$_GET['sid'];}
elseif(isset($_POST['sid'])){$sid=$_POST['sid'];}
if(isset($_GET['rid'])){$rid=$_GET['rid'];}
elseif(isset($_POST['rid'])){$rid=$_POST['rid'];}
if(isset($_GET['bid'])){$bid=$_GET['bid'];}
elseif(isset($_POST['bid'])){$bid=$_POST['bid'];}
if(isset($_GET['pid'])){$pid=$_GET['pid'];}
elseif(isset($_POST['pid'])){$pid=$_POST['pid'];}
if(isset($_GET['entryn'])){$entryn=$_GET['entryn'];}
elseif(isset($_POST['entryn'])){$entryn=$_POST['entryn'];}
if(isset($_GET['openid'])){$openid=$_GET['openid'];}

$StatementBank=array();
$reportdef=fetch_reportdefinition($rid);
$tabindex=0;
if($reportdef['report']['commentlength']=='0'){$commentlength='';}
else{$commentlength=' maxlength="'.$reportdef['report']['commentlength'].'"';}

/* This allows a comment to be split into sub-sections and each gets
 *  its own entry box. A special type of fixed sub-comment is not for
 *  editing so is filtered out here.
 */
$subs=(array)get_report_categories($rid,$bid,$pid,'sub');
$subcomments_no=0;
$subcomments=array();
foreach($subs as $sindex => $sub){
	if($sub['subtype']=='pro'){$subcomments_fix=1;}
	else{$subcomments_no++;$subcomments[]=$sub;}
	}

$Student=fetchStudent_short($sid);
$Report['Comments']=fetchReportEntry($reportdef, $sid, $bid, $pid);
if(!isset($Report['Comments']['Comment'])  or sizeof($Report['Comments']['Comment'])==0 
   or $entryn==sizeof($Report['Comments']['Comment'])){
	/*This is a fresh comment so can do a few extra things*/
	$Comment=array('Text'=>array('value'=>'','value_db'=>''),
				   'Teacher'=>array('value'=>'ADD NEW ENTRY'));
	$inmust='yes';
	}
else{
	/*Re-editing an existing comment.*/
	$texts=array();
/*TODO: the xmlid must have the real entryn not the index!!!!*/
	$Comment=$Report['Comments']['Comment'][$entryn];
	$inmust=$Comment['id_db'];
	if($subcomments_no>0){
		$texts=split(':::',$Comment['Text']['value_db']);
		}
	else{
		$texts[]=$Comment['Text']['value_db'];
		}
	}

/**
 * Now if this report links to an assessment profile, the statement
 * bank gets all of the achieved statements.
 * TODO: We only have one working profile!
 */
if($reportdef['report']['profile_name']!='' and isset($subcomments_fix)){
		$profile_name=$reportdef['report']['profile_name'];
		/* This fromdate is just a hack needs to check for previous report maybe?*/
		//$reportyear=$reportdef['report']['year']-1;
		//$fromdate=$reportyear.'-08-15';//Does the whole academic year
		$reportyear=$reportdef['report']['year'];
		$fromdate=$reportyear.'-02-1';
		$Statements=(array)fetchProfileStatements($profile_name,$bid,$pid,$sid,$fromdate);
		$StatementBank['Area'][$pid]['Statements']=$Statements;
		$StatementBank['Area'][$pid]['Name']='FS Profile: '.$pid;
		$StatementBank['Area'][$pid]['Levels']=array();
		}

if($reportdef['report']['addcategory']=='yes'){
	trigger_error($rid.$bid.$pid.'cat',E_USER_WARNING);
	$catdefs=get_report_categories($rid,$bid,$pid,'cat');
	$ratings=$reportdef['ratings'];
	$ratingname=get_report_ratingname($reportdef,$bid);
	}

/* Now if the connection to the statementbank db is turned on then
 * grab a set of statements. 
 */
$dbstat=connect_statementbank();
if($dbstat!=''){
	$stage='';
	$Bank=fetchStatementBank($reportdef['report']['course_id'],$bid,$pid,$stage,$dbstat);
	$StatementBank=$Bank+$StatementBank;
	}
if((isset($StatementBank['Area']) and sizeof($StatementBank['Area'])>0) or $reportdef['report']['addcategory']=='yes'){
	$commentheight=180;
	}
else{
	$commentheight=600;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS Comment Writer</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2010 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU General Public License version 2" />
<link rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<link rel="stylesheet" type="text/css" href="../../css/commentwriter.css" />
<script src="../../js/bookfunctions.js" type="text/javascript"></script>
<script src="../../js/qtip.js" type="text/javascript"></script>
<script src="../../js/statementbank.js" type="text/javascript"></script>
</head>
<body onload="loadRequired();">

	<div id="bookbox">
	  <?php three_buttonmenu(); ?>

	  <div id="heading">
		<label><?php print_string('student'); ?></label>
			<?php print $Student['DisplayFullName']['value'];?>
	  </div>

		<div class="content">
		<form id="formtoprocess" name="formtoprocess" method="post" 
									action="newcomment_writer_action.php">

<?php
if($reportdef['report']['addcategory']=='yes'){
?>
		  <div class="center" style="border-top:solid 1px;">
			<table class="listmenu">
<?php
			if(isset($Comment['Categories'])){$Categories=$Comment['Categories'];}
			else{
				$Categories['Category']=array();
				$Categories['ratingname']=$ratingname;
				}
			$ratings=$reportdef['ratings'][$Categories['ratingname']];

			while(list($catindex,$catdef)=each($catdefs)){
				$catid=$catdefs[$catindex]['id'];
				$catname=$catdefs[$catindex]['name'];
				print '<tr class="revealed"><td class="row"><div style="width:25%;"><p>'.$catname.'</p></div></td></tr>';

				/* Find any previously recorded value for this catid,
				   make a first guess that they will have been
				   recorded in the same order as the cats are
				   defined. But any blanks or changes will have
				   scuppered this.
				 */
				$setcat_value=-1000;
				if(isset($Categories['Category'][$catindex]) 
				   and $Categories['Category'][$catindex]['id_db']==$catid){
					$setcat_value=$Categories['Category'][$catindex]['value'];
					}
	   			else{
					foreach($Categories['Category'] as $Category){
						if($Category['id_db']==$catid){
							$setcat_value=$Category['value'];
							}
						}
					}
				if(($setcat_value==' ' or $setcat_value=='') and $setcat_value!='0'){
					$setcat_value=-1000;
					}

				print '<tr class="revealed"><td class="boundary row">';
				$divwidth=round(90/sizeof($ratings));
				foreach($ratings as $value=>$descriptor){
					$checkclass='';
					if($setcat_value==$value){$checkclass='checked';}

					print '<div class="'.$checkclass.'" style="width:'.$divwidth.'%;"><label>'.$descriptor.'</label>';
					print '<input onclick="checkRadioIndicator(this)" type="radio" name="incat'.$catid.'"
						tabindex="'.$tabindex++.'" value="'.$value.'" '.$checkclass;
					print ' /></div>';
					}
				print '</td></tr>';
				}
?>
			</table>
		</div>
		<div style="height:<?php print $commentheight;?>px;">
<?php
	}
?>
<?php
	if($subcomments_no==0){$subcomments[]['name']='Comment';$subcomments_no=1;}
	$commentheight=($commentheight/$subcomments_no)-25*$subcomments_no;/*in px*/
	if($commentheight<90){$commentheight=80;}
	if($commentheight>450){$commentheight=450;}
	for($c=0;$c<$subcomments_no;$c++){
			$commentlabel=$subcomments[$c]['name'];
?>
		  <div class="center" style="border-top:solid 1px;">
			<label style="float:right;background-color:#ffe;font-weight:600;padding:2px,6px;">
			<?php print $commentlabel;?>
			</label>
			<textarea id="Comment<?php print $c;?>"
			  style="height:<?php print $commentheight-20;?>px;"  
			  <?php print $commentlength;?> tabindex="<?php print $tabindex++;?>"  
			  name="incom<?php print $c;?>" ><?php if(isset($texts[$c])){print $texts[$c];};?></textarea>

		  </div>
<?php
			}
?>
		<input type="hidden" name="inno" value="<?php print $subcomments_no;?>"/>
		<input type="hidden" name="inmust" value="<?php print $inmust;?>"/>
		<input type="hidden" name="addcategory" value="<?php print $reportdef['report']['addcategory'];?>"/>
		<input type="hidden" name="sid" value="<?php print $sid; ?>"/>
		<input type="hidden" name="rid" value="<?php print $rid; ?>"/>
	    <input type="hidden" name="bid" value="<?php print $bid; ?>"/>
		<input type="hidden" name="pid" value="<?php print $pid; ?>"/>
		<input type="hidden" name="openid" value="<?php print $openid; ?>"/>
		</form>
		</div>
<?php
		if(isset($StatementBank['Area']) and sizeof($StatementBank['Area'])>0){ 
   //			if($commentheight<300){
?>
	<div class="content" id="statementbank">
		<div class="tinytabs" id="area">
			<ul>
<?php
				   $n=0;
				   while(list($index,$Area)=each($StatementBank['Area'])){
?>
		<li id="<?php print 'tinytab-area-'.$Area['Name'];?>"><p 
		<?php if($n==0){ print ' id="current-tinytab" ';}?>
		class="<?php print $Area['Name'];?>"
		onclick="tinyTabs(this)"><?php print $Area['Name'];?></p></li>

			<div class="hidden" id="tinytab-xml-area-<?php print $Area['Name'];?>">
				<div class="statementlevels">
				<table class="listmenu">
<?php
				   print '<tr><td abilityoption="*" onclick="filterbyAbility(this)" class="vspecial">All</td></tr>';
				   $Levels=(array)$Area['Levels'];
				   while(list($index,$Level)=each($Levels)){
					   print '<tr><td abilityoption="'.$Level['Value'].'" onclick="filterbyAbility(this)">'.$Level['Name'].'</td></tr>';
					   }
?>
				</table>
				</div>
				<table class="listmenu statements">
<?php
				   $Statements=(array)$Area['Statements'];
				   while(list($index,$Statement)=each($Statements)){
					   $Statement=personaliseStatement($Statement,$Student);
					   print '<tr><td onclick="chooseStatement(this)" ';
					   print ' ability="'.$Statement['Ability'].'" '; 
					   print ' author="'.$Statement['Author'].'" '; 
					   print ' count="'.$Statement['Counter'].'" '; 
					   print '>'.$Statement['Value'].'</td></tr>';
					   }
?>
				</table>
			</div>
<?php
				  $n++;
				  }
?>
				</ul>
			</div>
			<div id="tinytab-display-area" class="tinytab-display">
			</div>
		</div>
<?php
				}
?>

	</div>
</body>
</html>