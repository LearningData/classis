<?php
/**                                  fees_remittance_list.php
 */

$action='fees_remittance_list_action.php';

if(isset($_GET['month']) and $_GET['month']!=""){$month=$_GET['month'];}else{$month="";}
if(isset($_GET['year']) and $_GET['year']!=""){$year=$_GET['year'];}else{$year="";}

$monthoptions="";$yearoptions="";
$d_c=mysql_query("SELECT id FROM fees_remittance GROUP BY issuedate,year ORDER BY issuedate DESC;");
while($remittance=mysql_fetch_array($d_c)){
	$remid=$remittance['id'];
	$Remittance=fetchRemittance($remid);
	$date_parts=explode("-",$Remittance['IssueDate']['value']);
	$ayear=$Remittance['Year']['value'];
	if($month==""){$month=$date_parts[1];}
	if($year==""){$year=$ayear;}
	if($year==$ayear){$selectedyear="selected";}else{$selectedyear="";}
	$years[$ayear]="<option value='$ayear' $selectedyear>".($ayear-1)."-".$ayear."</option>";
	if($month==$date_parts[1]){$selectedmonth="selected";}else{$selectedmonth="";}
	$monthNum=sprintf("%02s", $date_parts[1]);
	$monthName=date("F", mktime(0, 0, 0, $monthNum, 10));
	$months[$date_parts[1]]="<option value='$date_parts[1]' $selectedmonth>".$monthName."</option>";
	}
foreach($months as $monthoption){
	$monthoptions.=$monthoption;
	}
foreach($years as $yearoption){
	$yearoptions.=$yearoption;
	}

$feeyear=$_POST['feeyear'];

include('scripts/sub_action.php');

$extrabuttons['newremittance']=array('name'=>'current','value'=>'fees_new_remittance.php');

two_buttonmenu($extrabuttons,$book);
?>
  <div id="heading">
	<select id="month" onchange="document.location.href = 'admin.php?current=fees_remittance_list.php&month='+this.value+'&year='+$('#year').val();">
		<?php print $monthoptions;?>
	</select>
	<select id="year" onchange="document.location.href = 'admin.php?current=fees_remittance_list.php&month='+$('#month').val()+'&year='+this.value;">
		<?php print $yearoptions;?>
	</select>
  </div>

  <div id="viewcontent" class="content">

  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
	  <table class="listmenu">
		<caption><?php print_string('remittance',$book);?></caption>
		<tr><th></th><th style="width:60%;">&nbsp;</th><th><?php print get_string('issue',$book).' '.get_string('date',$book);?></th><th><?php print get_string('payment',$book).' '.get_string('date',$book);?></th><th><?php print_string('account',$book);?></th></tr>
<?php
		$entryno=0;
		$d_c=mysql_query("SELECT id FROM fees_remittance WHERE ((issuedate>='$year-$month-01' and issuedate<='$year-$month-31') or (issuedate>='".($year-1)."-$month-01' and issuedate<='".($year-1)."-$month-31')) and year='$year' ORDER BY issuedate DESC;");
		while($remittance=mysql_fetch_array($d_c)){
			$remid=$remittance['id'];
			$Remittance=fetchRemittance($remid);
			$entryno++;
			$actionbuttons=array();
			$imagebuttons=array();
			$rown=0;
?>
		<tbody id="<?php print $entryno;?>">
		  <tr class="rowplus" onClick="clickToReveal(this)" id="<?php print $entryno.'-'.$rown++; ?>">
			<th>&nbsp</th>
			<td style="font-size:medium;">
			  <?php print $Remittance['Name']['value'];?>
			</td>
			<td>
			  <?php print display_date($Remittance['IssueDate']['value']);?>
			</td>
			<td>
			  <?php print display_date($Remittance['PaymentDate']['value']);?>
			</td>
			<td>
			  <?php print $Remittance['Account']['BankName']['value'];?>
			</td>
		  </tr>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="5">
			  <div class="center">
				<ul class="listmenu">
<?php 
			$total=0;
			$total_paid=0;
			$total_notpaid=0;
			foreach($Remittance['Concepts'] as $Concept){
				print '<li class="lowlite"><a  href="admin.php?current=fees_remittance_view.php&cancel='.$choice.'&choice='.$choice.'&remid='.$Remittance['id_db'].'&conid='.$Concept['id_db'].'">'.$Concept['Name']['value'].' - '.display_money($Concept['TotalAmount']['value']).'</a></li>';
				$total+=$Concept['TotalAmount']['value'];
				$total_paid+=$Concept['AmountPaid']['value'];
				$total_notpaid+=$Concept['AmountNotPaid']['value'];
				}
?>
				</ul>
			  </div>
			  <div class="center">
				<ul class="listmenu">
<?php
			foreach($Remittance['TotalAmounts'] as $paytype => $TotalAmount){
				print '<li class="lowlite"><a href="admin.php?current=fees_remittance_view.php&cancel=fees_remittance_list.php&remid='.$remid.'&paymenttype='.$TotalAmount['paymenttype'].'"><label>'.get_string($TotalAmount['label'],$book).'</label> '.display_money($TotalAmount['value']).'</a></li>';
				$actionbuttons[$TotalAmount['label']]=array('name'=>'process',
											 'value'=>'list:::'.$TotalAmount['paymenttype']);
				}
?>
				</ul>
			  </div>
<?php

			$actionbuttons['invoice']=array('name'=>'process',
											'value'=>'invoice');

			$actionbuttons['bankexport']=array('name'=>'process',
											   'value'=>'export');

			$imagebuttons['clicktodelete']=array('name'=>'process',
												 'value'=>'delete',
												 'title'=>'delete');

			if($total_paid==0 and $total_notpaid==0){
				$imagebuttons['clicktoedit']=array('name'=>'process',
												   'value'=>'edit',
												   'title'=>'edit');
				}

			print '<div class="center nolite" style="margin-top:4px;">';
			print '<div class="left">';
			print '<a href="admin.php?current=fees_remittance_view.php&cancel=fees_remittance_list.php&remid='.$remid.'"><label>'.get_string('total',$book).'</label> '.display_money($total).'</a>';			print '</div>';
			print '<div class="right"><a  href="admin.php?current=fees_remittance_view.php&cancel='.$choice.'&choice='.$choice.'&remid='.$Remittance['id_db'].'&payment=1"><label>'.get_string('paid',$book).'</label> '.display_money($total_paid).'</a>'.'</div>';
			print '<div class="right"><a  href="admin.php?current=fees_remittance_view.php&cancel='.$choice.'&choice='.$choice.'&remid='.$Remittance['id_db'].'&payment=2"><label>'.get_string('notpaid',$book).'</label> '.display_money($total_notpaid).'</a>'.'</div>';
			print '</div>';


			rowaction_buttonmenu($imagebuttons,$actionbuttons,$book);
?>
			</td>
		  </tr>
		  <div id="<?php print 'xml-'.$entryno;?>" style="display:none;"><?php xmlechoer('Remittance',$Remittance);?></div>
		</tbody>
<?php
			}
?>
	  </table>
	</div>
	
	<input type="hidden" name="feeyear" value="<?php print $feeyear;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>

