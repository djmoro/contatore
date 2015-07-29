<?php
print ("benvenuti");
//  - chmod this document to 755! -

// url to the digits
$img = "count/";

// url to the animated digits
$animated_img = "count/ani/";

// How many digits to show
$padding = 8;

// digit width and height
$width = 16;
$height = 22;

// path to the log file
$fpt = "acount.txt"; // log file - chmod it to 666

// optional configuration settings

$lock_ip 			= 1; // IP locking to avoid reloading 1=yes 0=no
$ip_lock_timeout 	= 30; // in minutes
$fpt_ip 			= "ip.txt"; // IP log file - chmod it to 666

// end configuration

function checkIP($rem_addr) {
	global $fpt_ip,$ip_lock_timeout;
	$found=0;
	$ip_array = file($fpt_ip);
	$reload_dat = fopen($fpt_ip,"a");
	$this_time = time();
	for ($i=0; $i<sizeof($ip_array); $i++)
	{
		list($ip_addr,$time_stamp) = split("\|",$ip_array[$i]);
		if ($this_time < ($time_stamp+60*$ip_lock_timeout)) {
			if ($ip_addr == $rem_addr) {
				$found=1;
			} else {
				fwrite($reload_dat,"$ip_addr|$time_stamp");
			}
		}
	}
	fwrite($reload_dat,"$rem_addr|$this_time\n");
	fclose($reload_dat);
	return ($found==1) ? 1 : 0;
}

if (!file_exists($fpt))
{
	$count_dat = fopen($fpt,"w+");
	$digits = 0;
	fwrite($count_dat,$digits);
	fclose($count_dat);
}
else
{
	$line = file($fpt);
	$digits = $line[0];
	$check = checkIP($REMOTE_ADDR);
	if ($lock_ip==0 || ($lock_ip==1 && $check==0))
	{
		$count_dat = fopen($fpt,"r+");
		$digits++;
		fwrite($count_dat,$digits);
		fclose($count_dat);
	}
}
if ($check==1)
	$digits = sprintf ("%0".$padding."d",$digits+1);
else
	$digits = sprintf ("%0".$padding."d",$digits);

$ani_digits = sprintf ("%0".$padding."d",$digits+1);

$length_digits = strlen($digits);
for ($i=0; $i < $length_digits; $i++)
{
	if (substr("$digits",$i,1) == substr("$ani_digits",$i,1) || $check==1)
	{
		$digit_pos = substr("$digits",$i,1);
		?><img src="<?php print ($img); print ($digit_pos);?>.gif" width="<?php print ($width);?>" height="<?php print ($height);?>">
		
		
		<?//php print ("cifre_cont"); print ("contatore");
	}
	else
	{
		$digit_pos = substr("$ani_digits",$i,1);
		?><img src="<?php print ($animated_img); print ($digit_pos);?>.gif" width="<?php print ($width);?>" height="<?php print ($height);?>">
		<?//php print ("cifre_ani_cont"); print ("contatore");
	}
}

if ($check == 1)
	print("L'accesso ha una durata di 30 minuti.");
?>