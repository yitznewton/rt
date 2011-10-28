#!/usr/bin/php
<?php

$args = parsed_args();
$dsn  = get_dsn( $args );

$pdo = new PDO( $dsn, $args['u'], $args['p'] );
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$q = new RtTicketQuery( $pdo );

if ( isset( $args['o'] )) {
  $q->addConstraint( new RtConstraint_MinHoursOld( $args['o'] ));
}
else {
  $q->addConstraint( new RtConstraint_MinHoursOld( 48 ));
}

if ( isset( $args['s'] )) {
  $q->addConstraint( new RtConstraint_Status(
    explode(',', $args['s'] ) ));
}

if ( isset( $args['t'] )) {
  $q->addConstraint( new RtConstraint_UserType(
    explode(',', $args['t'] ) ));
}

$r = $q->execute();

while ( $ticket = $q->fetch() ) {
  if ( $ticket['emailaddress'] === null ) {
    continue;
  }
  
  $hours_old = floor((time() - strtotime($v['lastupdated'])) / 3600);
  
  if ( $hours_old <= 48 ) {
    $update_age = "$hours_old hours";
  }
  else {
    $days_old = floor( $hours_old / 24 );
    $update_age = "$days_old days";
  }
  
  $mailer = new RtTicketMailer();
  $mailer->setFrom('systems.library@touro.edu');
  $mailer->setRealName($v['realname']);
  $mailer->setStatus($v['status']);
  $mailer->setHoursOldTrigger($hours_old_trigger);
  $mailer->setUpdateAge($update_age);
  $mailer->setID($v['id']);
  $mailer->setSubject($v['subject']);
  $mailer->setRecipient($v['emailaddress']);
  $mailer->setTemplate(dirname(__FILE__) . '/mailer_template1.txt');

  $mailer->send();

  unset( $mailer );
}


exit;










define('DEFAULT_STATUS_STRING', 'open');
define('DEFAULT_TYPE_STRING', 'Owner');
define('DEFAULT_HOURS_OLD', 48);

require_once(dirname(__FILE__) . '/RtTicketUsers.class.php');
require_once(dirname(__FILE__) . '/RtTicketMailer.class.php');

$args = get_arg_array($argv);
$dsn  = get_dsn( $args );

$password = isset( $args['p'] ) ? $args['p'] : null;

$theTickets = new RtTicketUsers( $dsn, $args['u'], $password );

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

  $mailer = new RtTicketMailer();
  $mailer->setFrom('systems.library@touro.edu');
  $mailer->setRealName($v['realname']);
  $mailer->setStatus($v['status']);
  $mailer->setHoursOldTrigger($hours_old_trigger);
  $mailer->setUpdateAge($update_age);
  $mailer->setID($v['id']);
  $mailer->setSubject($v['subject']);
  $mailer->setRecipient($v['emailaddress']);
  $mailer->setTemplate(dirname(__FILE__) . '/mailer_template1.txt');

  $mailer->send();

  unset($mailer);
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

function get_dsn( array $args )
{
  // mysql:dbname=testdb;host=127.0.0.1
  
  $dsn  = 'mysql:dbname=' . $args['d'] . ';host=';
  $dsn .= isset( $args['h'] ) ? $args['h'] : 'localhost';

  return $dsn;
}
