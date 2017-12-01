<?php
/* licence                                                           */
/*
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
/* ***************************************************************** */
/* configuration                                                     */

$critical = 5; // use thresold for turning on reports
$to = 'your@email.com'; // report destination email  address
$from = 'your@email.com'; // report sender email address

/* ***************************************************************** */

$report = ''; // text report, to be displayed or sent over e-mail

$hostname = exec('hostname'); // reads host name from system
$res =  exec('df -h', $output); // get human readable disk use for all partitions

foreach($output as $k => $line){
	// if partition matches a filesystem mount point
	if (preg_match('/(\/[a-z0-9\/\-]+).+ ([0-9]+)\%/msi', $line, $use)){ 
		// if use is greater than critical thresold
		if ($use[2]>=$critical){ 		
			$report .= $use[1]; // device
			$report .= ': use is now ';
			$report .= $use[2]; // use %
			$report .= "%\n";
		}
	}	
}

// if report features at least one critical partition
if ($report != ''){ 
	$subject = $hostname.' df alert';
	$report = $subject."\n".$report;
	
	echo $report;
	
	// turn on email reporting if $to is set
	if (!empty($to)){
		$headers = 'From: '.$from . "\r\n" .
		'Reply-To: ' . $from ."\r\n" .
		'X-Mailer: PHP/' . phpversion();

		mail($to, $subject, $report, $headers);
	}
}