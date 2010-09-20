<?php
/**
 *                                  student_transport.php
 */

$action='student_transport_action.php';

require_once('lib/fetch_transport.php');

three_buttonmenu();

	/*Check user has permission to view*/
	$perm=getFormPerm($Student['RegistrationGroup']['value'],$respons);
	include('scripts/perm_action.php');

?>
  <div id="heading">
	<?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center listmenu">
		<div class="center">
		<table>
		<thead>
		  <tr>
			<th colspan="4"> </th>

<?php
	$buses=list_buses();
	$days=getEnumArray('dayofweek');
	$todate=date('Y-m-d');
	$today=date('N');
	$dates=array();
	foreach($days as $day => $dayname){
		$daydiff=$day-$today;
		$date=date('Y-m-d',strtotime($daydiff.' day'));
		$dates[$day]=$date;
		if($todate==$date){$colclass='style="background-color:#cfcfcf;"';}
		else{$colclass='';}
		print '<th '.$colclass.'>'.get_string($dayname,$book).'<br />'.$date.'</th>';
		}
?>
		  </tr>
		</thead>
<?php
		print '<tr id="sid-'.$sid.'">';
   	print '<td>'.'<input type="checkbox" name="sids[]" value="'.$sid.'" />'.$rown++.'</td>';
   	print '<td colspan="2" class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_view.php&sid='.$sid.'">'.$Student['Surname']['value'].', '. $Student['Forename']['value'].'</a></td>';
   	print '<td>'.$Student['RegistrationGroup']['value'].'</td>';
		foreach($days as $day=>$dayname){
			$bookings=array();
			$bookings=(array)list_student_journey_bookings($sid,$dates[$day],$day);
			$divin='';$divout='';
			$openId=$sid.'-'.$day;
			foreach($bookings as $booking){
				$bus=get_bus($booking['bus_id']);
				$stops=list_bus_stops($booking['bus_id']);
				if($bus['direction']=='I'){$divname='divin';$divclass='midlite';}
				else{$divname='divout';$divclass='gomidlite';}
				if($$divname==''){
					$divaction='onClick="clickToEditTransport('.$sid.',\''.$dates[$day].'\',\''.$booking['id'].'\',\''.$openId.'\');"';
					if($booking['comment']!=''){$$divname='<span title="'.$booking['comment'].'">';}
					$$divname.='<div '.$divaction.' class="'.$divclass.'">'.$bus['name'].' <br /><div style="font-size:7pt;color:#909090;">'.$stops[$booking['stop_id']]['name'].'</div></div>';
					if($booking['comment']!=''){$$divname.='</span>';}
					}
				}

			//if($divin=='' and $divout==''){$divaction='';}
			if($divin==''){$divin='<div onClick="clickToEditTransport('.$sid.',\''.$dates[$day].'\',\'-1\',\''.$openId.'\');" class="lowlite">'.'ADD BUS'.'</div>';}
			if($divout==''){$divout='<div onClick="clickToEditTransport('.$sid.',\''.$dates[$day].'\',\'-2\',\''.$openId.'\');" class="lowlite">'.'ADD BUS'.'</div>';}
			print '<td class="clicktoaction">'.$divin . $divout.'</td>';
			}
		print '</tr>';
?>
		</table>
		</div>
	  </fieldset>

	  <fieldset class="center listmenu">
		<div>
		  <table>
			<tr>
<?php
?>
			</tr>
		  </table>
		</div>
	  </fieldset>


	    <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print $cancel;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
</div>
