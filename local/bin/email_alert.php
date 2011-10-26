#!/usr/bin/php

<?php

define('DEFAULT_STATUS_STRING', 'open');
define('DEFAULT_TYPE_STRING', 'Owner');
define('DEFAULT_HOURS_OLD', 48);

require_once(dirname(__FILE__) . '/RtTicketUsers.class.php');
require_once(dirname(__FILE__) . '/RtTicketMailer.class.php');

$theTickets = new RtTicketUsers();

$args = get_arg_array($argv);

if (in_array('s', array_keys($args)) === true) {
  $status_string = explode(',', $args['s']);
}
$theTickets->addParam('status', $status_string);

if (in_array('t', array_keys($args)) === true) {
  $type_string = explode(',', $args['t']);
} else {
  $type_string = DEFAULT_TYPE_STRING;
}
$theTickets->addParam('type', $type_string);

if (in_array('o', array_keys($args)) === true) {
  $hours_old_trigger = (int)$args['o'];
} else {
  $hours_old_trigger = DEFAULT_HOURS_OLD;
}
$theTickets->addParam('minHoursOld', $hours_old_trigger);

$resultSet = $theTickets->getTickets();

foreach ($resultSet AS $v){
  if ($v['emailaddress'] === null) {
    continue;
  }
  $hours_old = floor((time() - strtotime($v['lastupdated'])) / 3600);
  if ($hours_old <= 48) {
    $update_age = "$hours_old hours";
  } else {
    $days_old = floor($hours_old / 24);
    $update_age = "$days_old days";
  }

  $theMailer = new RtTicketMailer();
  $theMailer->setFrom('systems.library@touro.edu');
  $theMailer->setRealName($v['realname']);
  $theMailer->setStatus($v['status']);
  $theMailer->setHoursOldTrigger($hours_old_trigger);
  $theMailer->setUpdateAge($update_age);
  $theMailer->setID($v['id']);
  $theMailer->setSubject($v['subject']);
  $theMailer->setRecipient($v['emailaddress']);
  $theMailer->setTemplate(dirname(__FILE__) . '/mailer_template1.txt');

  $theMailer->send();

  unset($theMailer);
}

/*
* End script body
* Begin function definitions
*/

function get_arg_array($input)
{
  $arg_string = join(' ', $input);

  $arg_parse = array();
  preg_match_all('/-[a-z]{1} [^-][^ ]+/', $arg_string, $arg_parse);

  $the_array = array();
  foreach ($arg_parse[0] as $a) {
    $the_array[substr($a, 1, 1)] = substr($a, 3);
  }

  return $the_array;
}

