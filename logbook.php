<?php
	$host='logbook.php';
	$book='logbook';
	$fresh='';
	include('scripts/head_options.php');
?>
<div style="visibility:hidden;" id="hiddenbookoptions">	
</div>
<div style="visibility:hidden;" id="hiddenloginlabel">
	<?php print $tid;?>
</div>
<div style="visibility:hidden;" id="hiddensidebuttons">
	  <button onClick="helpPage();"  title="<?php print_string('help');?>">
		<img src="images/helper.png" alt="<?php print_string('help');?>" /></button>
	  <button onClick="printGenericContent();"  title="<?php print_string('print');?>">
		<img src="images/printer.png" alt="<?php print_string('print');?>" /></button>
<!--	  <button onClick="changeFont();"  title="<?php print_string('fontsize');?>">
		<img src="images/fonter.png" alt="<?php print_string('fontsize');?>" /></button>
--!>
</div>
<div style="visibility:hidden;" id="hiddenlogbook">
	<div id="logbookstripe" class="logbook"></div>
	<div id="loginworking">
	<select name="new_r" size="1" onChange="document.workingas.submit();">
		<option value="-1" 
<?php  if($r==-1){print 'selected="selected" ';} ?>
		  ><?php print_string('myclasses');?></option>
<?php 
    for($c=0;$c<(sizeof($respons));$c++){
		if($respons[$c]{'yeargroup_id'}==''){
			print '<option value="'.$c.'"';
			if(isset($r)){if($r==$c){print ' selected="selected" ';}}
			print '>'.$respons[$c]['name'].'</option>';
			}
		}
?>
	</select>
	</div>
</div>

<?php
	if($fresh!=''){
		$role=$_SESSION['role'];
		$showbooks=$books["$role"];
		foreach($showbooks as $bookhost=>$bookname){
			/* (re)loading all the books*/
?>
			<script>parent.loadBook("<?php print $bookhost; ?>")</script>
<?php
		   }
?>
	<script>parent.loadBook("aboutbook")</script>
<?php
	   }
	if($fresh=='very'){
		/*this was loaded after a new login so do some extra stuff:*/
		/*load the booktabs, update langpref, and raise firstbook*/
?>
<div style="visibility:hidden;" id="hiddennavtabs">
	<div class="booktabs">
	  <ul>
		<label id="loginlabel">
		</label>
		<li id="logbooktab"><p class="logbook" onclick="logOut();">LogOut</p></li>
		<li id="aboutbooktab"><p id="currentbook" class="aboutbook"
			onclick="viewBook(this.getAttribute('class'))">About</p></li>
<?php
		foreach($showbooks as $bookhost=>$bookname){
?>
		<li id="<?php print $bookhost.'tab';?>"><p class="<?php print $bookhost;?>"
		onclick="viewBook(this.getAttribute('class'))"><?php print $bookname;?></p></li>
<?php
			}
?>
	  </ul>
	</div>
</div>
<?php
		$firstbookpref=$_SESSION['firstbookpref'];
		update_user_language(current_language());
?>
		<script>parent.logInSuccess();</script>
		<script>setTimeout("parent.viewBook('<?php print $firstbookpref; ?>');",5000);</script>
<?php
		}
include('scripts/end_options.php');
?>