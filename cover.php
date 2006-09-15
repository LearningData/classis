<?php
include ('../school.php');
include ('classdata.php');
print '<?xml version="1.0" encoding="utf-8"?'.'>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002, 2003, 2004, 2005, 2006
	Stuart Thomas Johnson. All trademarks acknowledged. All rights reserved." />
<meta name="version" content="<?php print $version; ?>" />
<meta name="license" content="GNU General Public License version 2" />
<link id="parentstyle" href="css/viewstyle.css" rel="stylesheet" type="text/css" />
<link id="parentstyle" href="css/logbook.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div id="coverbox">

<div class="background">
	<img src="../images/gradient.png" style="width:95%;height:100%;float:right;" />
</div>

<div id="branded">
	<img onClick="window.open('http://classforschools.com','CfS');"
		src="../images/cfs-banner-blue.png" style="height:22px"
		alt="ClaSSforSchools.com" />
</div>

<div id="schoollogo">
	<img src="../images/<?php print $CFG->schoollogo;?>" />
</div>

</div>

</body>
</html>
