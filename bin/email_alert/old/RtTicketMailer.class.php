<?php

require_once ('Mail.php');

class rtTicketMailer
{
  private $from;
  private $to;
  private $subject;
  private $template;
  private $headers = array();
  private $body;
  private $id;
  private $realName;
  private $status;
  private $updateAge;
  private $hoursOldTrigger;

  public function setID ($id)
  {
        $this->id = $id;
  }

  public function getId ()
  {
        return $this->id;
  }

  public function setStatus ($status)
  {
        $this->status = $status;
  }

  public function getStatus ()
  {
        return $this->status;
  }

  public function setRealName ($realName)
  {
        $this->realName = $realName;
  }

  public function getRealName ()
  {
        return $this->realName;
  }

  public function setUpdateAge ($a)
  {
        $this->age = $a;
  }

  public function getUpdateAge ()
  {
        return $this->age;
  }

  public function setHoursOldTrigger ($h)
  {
        $this->hoursOldTrigger = $h;
  }

  public function getHoursOldTrigger ()
  {
        return $this->hoursOldTrigger;
  }

  public function setFrom ($from)
  {
        $this->from = $from;
  }

  public function setRecipient ($email)
  {
        if ($this->isValidEmail($email)) {
      $this->to = $email;
        } else {
      return false;
    }
  }

  public function setSubject ($subject)
  {
    $this->subject = 'Reminder - [Library Support #'. $this->getID() . '] '. $subject;
  }

  private function setHeader()
  {
    $this->headers = array(
      'From' => $this->from,
      'To' => $this->to,
      'Subject' => $this->subject
          );
  }

  public function setTemplate($f)
  {
        if (file_exists($f) === false) {
      throw new Exception('Template does not exist.');
    }

    if (filesize($f) > 5120) {
      throw new Exception('Template is too large; max is 5kb.');
    }

    $this->template = $f;
  }

  private function setBody()
  {
    $body_string = file_get_contents($this->template);

    preg_match_all('~\{([A-Za-z0-9]+)\}~', $body_string, $matches);

    $matches = array_combine($matches[0], $matches[1]);

    foreach ($matches as $token => $var_name) {
      if (isset($this->$var_name) === false) {
        throw new Exception('Template includes undefined variable $' . $var_name . '.');
      }

      $body_string = str_replace($token, $this->$var_name, $body_string);
    }

    $this->body = $body_string;
  }

  private function templateReplace($field)
  {
    $field = $field[1];

    if (isset($this->$field) === false) {
      throw new Exception('Template includes undefined variable.');
    }

    return $this->$field;
  }

  public function send()
  {
        $mail = Mail::factory("mail");
        $this->setBody();
    $this->setHeader();

    $send = $mail->send($this->to, $this->headers, $this->body);

        if (PEAR::isError($send)) {
      echo($send->getMessage());
    }
  }

  private function isValidEmail ($email)
  {
        if(preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $email)) {
                return true;
        } else {
      return false;
    }
  }
}

