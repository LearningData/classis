<?php
/**		   					eportfolio_accounts_check.php
 *
 */

$action='eportfolio_accounts_action.php';

include('scripts/answer_action.php');

three_buttonmenu();
?>

  <div id="heading">
	<?php print get_string('eportfolios',$book).' ';?>
	</div>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="center">
		<legend><?php print_string('eportfolios',$book); ?></legend> 
		<?php print_string('eportfoliowarning',$book); ?>
	  </fieldset>

	  <fieldset class="center divgroup">
		<legend><?php print get_string('staff',$book).' / '
		  .get_string('students',$book).' /
		  '.get_string('communities',$book) ;?></legend> 
		<p>This update
		  generates new portfolio accounts for any students or staff
		  who do not yet have one and will also repopulate all of the
		  community memberships. Refreshing first deletes all existing
		  communities and all of their associated content (blogs,
		  files, everything all get wiped!); existing users' accounts
		  and their content are left intact.
		</p>
		<div class="left">
<?php
		  $checkcaption=get_string('refreshfirst',$book); $checkname='refresh'; 
		  include('scripts/check_yesno.php');
?>
		</div>
		<div class="right">
<?php 
		$checkcaption=get_string('update',$book); 
		$checkname='staffcheck'; include('scripts/check_yesno.php');
?>
		</div>
	  </fieldset>

	  <fieldset class="center divgroup">
		<legend><?php print_string('students','infobook');?></legend>
		<p>
		  Only use this blank option if you want to completely delete all student accounts and associated content before regenerating them. All students will be issued new logins.		  
		</p>
		<div class="left">
<?php 
		$checkcaption=get_string('blankfirst',$book); 
		$checkname='studentblank'; include('scripts/check_yesno.php');
?>
		</div>
		<div class="right">
<?php 
		$checkcaption=get_string('update',$book).' '.get_string('photos',$book); 
		$checkname='photocheck'; include('scripts/check_yesno.php');
?>
		</div>
	  </fieldset>

	  <fieldset class="center divgroup">
		<legend><?php print_string('contacts','infobook');?></legend>
		<p>
		  Only use this blank option if you want to completely delete all contacts before regenerating them. All contacts will be issued new logins.		  
		</p>
		<div class="left">
<?php 
		  $checkcaption=get_string('blankfirst',$book); $checkname='contactblank'; 
		  include('scripts/check_yesno.php');
?>
		</div>
		<div class="right">
<?php 
		  $checkcaption=get_string('update',$book); $checkname='contactcheck';
		  include('scripts/check_yesno.php');
?>
		</div>
	  </fieldset>


	<input type="hidden" name="cancel" value="<?php print ''; ?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
</form> 
</div>
