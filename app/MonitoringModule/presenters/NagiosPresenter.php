<?php
/**
 * This file is part of the "iManaJUZment" - complex system for internet service providers
 *
 * Copyright (c) 2005 - 2011 Jan Dolecek (http://juzna.cz)
 *
 * iManaJUZment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with iManaJUZment.  If not, see <http://www.gnu.org/licenses/gpl.txt>.
 *
 * @license http://www.gnu.org/licenses/gpl.txt
 */


namespace MonitoringModule;

class NagiosPresenter extends \BasePresenter {
  /**
   * Get Thrift connection to Nagios server
   * @return
   */
  private function getThrift() {
    $unixPath = TMP_DIR . '/sock/nagios';

    \TBase::$allowEnumConversion = true;
    $socket = new \TSocket("unix://$unixPath", -1);
    $transport = new \TBufferedTransport($socket, 1024, 1024);
    $protocol = new \TBinaryProtocol($transport);
    $client = new \Thrift\Nagios\NagiosClient($protocol);

    $transport->open();

    return $client;
  }

  /**
   * Export all access points to Nagios server
   */
  function actionExport() {
    $client = $this->getThrift();

    // Prepare config
    $conf = new \Thrift\Nagios\Configuration;

    foreach(\AP::getRepository()->findAll() as $ap) {
      $conf->hosts[] = new \Thrift\Nagios\HostEntry(array(
        'hostName'	=> str_replace(',', '_', $ap->name),
        'ip'		    => $ap->IP,
        'parents'	  => array(($p = $ap->l3parent) ? str_replace(',', '_', $p->name) : null),
        'services'	=> array('ping','http'),
      ));
    }

    /*
      1: required string hostName,
      2: optional string hostAlias,
      3: required common.ipAddress ip,
      4: required string contactGroup,
      5: string template = "generic-host",
      6: optional string image,		// Path to image
      7: common.coordinates coords,	// Coordinates for map
      8: string url,		// Action URL
      9: list<string> groups = [ 'default' ],	// List of groups
      10: list<CheckService> services = [ ping ],	// List of services to be checked
      11: required list<string> parents,		// Lis
    */

    // Send config
    $client->updateConfiguration($conf);

    // Tell the user result and redirect
    $this->flashMessage("Export has been finished");
    $this->redirect('default');
  }

  /**
   * Callback: received new notification from nagios service
   * @param string $hostName
   * @param string $hostState
   * @return void
   */
  function actionNotification($hostName, $hostState) {
    // TODO: what to do with that?
    // TODO: configure nagios notification listener to execute this method
  }
}
