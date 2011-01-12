<?php
/**
*
*/

@list(, $hostIndex, $hostIP, $hostCommunity) = $_SERVER['argv'];
if(!isset($hostIP) || !isset($hostCommunity)) die("Arguments not given\n");

require_once(__DIR__ . '/../bootstrap.php');
define('LOG_NORMAL', APP_DIR . "/logs/snmp/$hostIndex.log");
define('LOG_ERROR', APP_DIR . "/logs/snmp/$hostIndex.err");


// Load data
$snmp_time = time();
$time_start = microtime(true);
$snmpLoader = new SNMP($hostIP, $hostCommunity);
$snmp_data = $snmpLoader->mikrotikGetAll(true);
$time_elapsed = ($time_end = microtime(true)) - $time_start;

echo "Got data\n";

// Save interfaces
foreach($snmp_data['if'] as $if) {
	RRDTool::save('interface', array(
		'ap'	=> $hostIndex,
		'if'	=> $if['nazev'],
		'in'	=> $if['in-octets'],
		'out'	=> $if['out-octets'],
		'inerr'	=> $if['in-error'],
		'outerr'=> $if['out-error'],
		'queue'	=> $if['out-queue'],
	));
}

// Regtable
foreach($snmp_data['regtable'] as $mac => $reg) {
	unset($reg['mac-address']);
	$reg['mac'] = Net::getMac($mac, ''),
	$reg['ap'] = $hostIndex;
	
	RRDTool::save('regtable', $reg);
}


// CPU + uptime
{
	$uptime = $snmp_data['uptime'];
	is_numeric($cpu = $snmp_data['cpu']) or list(, $cpu) = explode(': ', $snmp_data['cpu']);
	
	
	// Prevedeme na tick-count
	if(is_numeric($uptime));
	elseif(preg_match('/[(]([^)]+)[)]/', $uptime, $match)) $uptime = $match[1];
	else $uptime = null;
	
	RRDTool::save('uptime', array(
		'ap'	=> $hostIndex,
		'uptime'=> $uptime,
		'cpu'	=> $cpu,
		'snmp_load' => $time_elapsed,
	));
}

// Systemove zdroje
foreach($snmp_data['resource'] as $row) {
	RRDTool::save('resource', array(
		'ap'	=> $hostIndex,
		'typ'	=> $row['nazev'],
		'size'	=> $row['size'],
		'used'	=> $row['used'],
		'failures' => $row['failures'],
	));
}

