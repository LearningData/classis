<?php
/**                                  ents_list.php    
 */

$current='ents_list.php';
$action='ents_list_action.php';
$host='infobook.php';


if(isset($_GET{'tagname'})){$tagname=$_GET{'tagname'};}
elseif(isset($_POST{'tagname'})){$tagname=$_POST{'tagname'};}
if(isset($_GET{'bid'})){$bid=$_GET{'bid'};}

three_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('student',$book);?></label>
	<?php print $Student['DisplayFullName']['value'];?>
  </div>

  <div class="topform">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <div class="left">
		<label for="Detail"><?php print_string('details',$book);?></label>
		<textarea name="detail" id="Detail"   
		  tabindex="<?php print $tab++;?>"
		  class="required" rows="5" cols="30"></textarea>
	  </div>
<?php
if($tagname=='Background'){
?>
	  <div class="right" >
		<label for="Category"><?php print_string('source',$book);?></label>
		<?php $cattype='bac'; include('scripts/list_category.php'); ?>
	  </div>

	  <div class="right" >
		<?php $xmldate='Entrydate'; $required='yes'; include('scripts/jsdate-form.php'); ?>
	  </div>

	  <div class="right" >
		<?php $yid=$Student['YearGroup']['value']; include('scripts/list_year.php'); ?>
	  </div>
<?php
   	}
else{
?>
	  <div class="right">
		<label><?php print_string('subjectspecific');?></label>
			   <?php $required="no"; include('scripts/list_studentsubjects.php');?>
	  </div>

	  <div class="right">
		<?php $xmldate='Entrydate'; $required='yes'; include('scripts/jsdate-form.php'); ?>
	  </div>
<?php 
	}
?>
	<input type="text" style="display:none;" id="Id_db" name="id_db" value="" />
	<input type="hidden" name="tagname" value="<?php print $tagname;?>"/>
	<input type="hidden" name="current" value="<?php print $action;?>"/>
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
 	<input type="hidden" name="choice" value="<?php print $choice;?>"/>
  </form>
  </div>

  <div class="content">
	<div class="center">
	  <table class="listmenu">
		<caption><?php print_string(strtolower($tagname),$book);?></caption>
		<thead>
		  <tr>
			<th></th>
			<th><?php print_string('yeargroup');?></th>
			<th><?php print_string('date');?></th>
			<th><?php print_string('subject');?></th>
		  </tr>
		</thead>
<?php
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid, $respons);
	$Entries=$Student['Backgrounds']["$tagname"];
	$entryno=0;
	if(is_array($Entries)){
	while(list($key,$entry)=each($Entries)){
		if($tagname=='Background' and $entry['Categories']['Category'][0]['rating']=='-1'
					and $perm['r']!=1){$entry['Detail']['value']='Confidential';}
		if(is_array($entry)){
			$rown=0;
			$entryno=$entry['id_db'];
?>
		<tbody id="<?php print $entryno;?>">
		  <tr class="rowplus" onClick="clickToReveal(this)" id="<?php print $entryno.'-'.$rown++;?>">
			<th>&nbsp</th>
<?php 
		   if(isset($entry['YearGroup']['value'])){print '<td>'.$entry['YearGroup']['value'].'</td>';}
		   else{print'<td></td>';}
		   if(isset($entry['EntryDate']['value'])){print '<td>'.$entry['EntryDate']['value'].'</td>';}
		   else{print'<td></td>';}
		   if(isset($entry['Subject']['value'])){print '<td>'.$entry['Subject']['value'].'</td>';}
		   else{print'<td></td>';}
?>
		  </tr>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="6">
			  <p>
<?php		   if(isset($entry['Detail']['value'])){
					print $entry['Detail']['value'];
					}
?>
			  </p>
			  <button class="rowaction" title="Delete this entry"
				name="current" value="delete_background.php" onClick="clickToAction(this)">
				<img class="clicktodelete" />
			  </button>
			  <button class="rowaction" title="Edit" name="Edit" onClick="clickToAction(this)">
				<img class="clicktoedit" />
			  </button>
			</td>
		  </tr>
		  <div id="<?php print 'xml-'.$entryno;?>" style="display:none;">
<?php
				xmlpreparer("$tagname",$entry);
?>
			</div>
		  </tbody>
<?php
				}
			}
		}
?>
<tr><td></td></tr>
	  </table>
	</div>
  </div>
