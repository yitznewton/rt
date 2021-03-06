<?php

require_once('MDB2.php');

class RtTicketUsers
{
  private $db = '';
  private $params = array();
  private $validStatuses = array();
  private $validTypes = array();

  public function __construct( $dsn )
  {
    $options = array('persistent' => true);
    $this->db = MDB2::factory($dsn, $options);
    if (PEAR::isError($this->db) === true) {
      throw new Exception('Error connecting to database: ' . $this->db->getMessage());
    }

        $this->validStatuses = array('new', 'open', 'stalled');
        $this->validTypes = $this->getValidTypes();
  }

  public function addParam($key, $value)
  {
    if ($key == 'status') {
          if (is_array($value) === false) {
            $value = array($value);
          }

          foreach ($value as $v) {
        if (self::isValidStatus(strtolower($v)) === false) {
                  throw new Exception ("$v is not a valid status.");
                }
          }

          $this->params['status'] = $value;
        }

        if ($key == 'type') {
          if (is_array($value) === false) {
            $value = array($value);
          }

          foreach ($value as $v) {
            if (self::isValidTypes(strtolower($v)) === false) {
                  throw new Exception ("$v is not a valid type.");
                }
          }

          $this->params['type'] = $value;
        }

        if ($key == 'minHoursOld') {
          if ((is_int($value) === false) || ($value < 0)) {
            throw new Exception('minHoursOld must be a positive integer.');
          }

          $this->params['minHoursOld'] = $value;
        }
  }

  public function getTickets()
  {
    $q = $this->formQuery();

        $this->db->setFetchMode(MDB2_FETCHMODE_ASSOC);
    $r = $this->db->query($q);

    if (PEAR::isError($r) === true) {
      throw new Exception('Database error:' . $r->getMessage());
    }

    $results = array();
    while ($row = $r->fetchRow()) {
      $results[] = $row;
    }

    return $results;
  }

  private function formQuery()
  {
        $q = "SELECT T.`id`, T.`Subject`, T.`Status`, T.`LastUpdated`,\n";
      $q .= "U.`RealName`, U.`EmailAddress`, G.`Type`\n";
      $q .= "FROM rt3.`Tickets` T\n";

        $firstSubquery = $q;
      $firstSubquery .= "JOIN rt3.`Groups` G ON G.`Instance`=T.`id`\n";
      $firstSubquery .= "JOIN rt3.`GroupMembers` M ON M.`GroupId`=G.`id`\n";
      $firstSubquery .= "JOIN rt3.`Users` U ON U.`id`=M.`MemberId`\n";
        $firstSubquery .= $this->getLimitSql() . "\n";

        if (strpos($q, 'WHERE') === false) {
          $firstSubquery .= "AND G.`Domain`='RT::Ticket-Role'\n";
        } else {
          $firstSubquery .= "WHERE G.`Domain`='RT::Ticket-Role'\n";
        }

        $secondSubquery = 'UNION ' . $q;
      $secondSubquery .= "JOIN rt3.`Groups` G ON G.`Instance`=T.`Queue`\n";
      $secondSubquery .= "JOIN rt3.`GroupMembers` M ON M.`GroupId`=G.`id`\n";
      $secondSubquery .= "JOIN rt3.`Users` U ON U.`id`=M.`MemberId`\n";
        $secondSubquery .= $this->getLimitSql() . "\n";

        if (strpos($q, 'WHERE') === false) {
          $secondSubquery .= "AND G.`Domain`='RT::Queue-Role'\n";
        } else {
          $secondSubquery .= "WHERE G.`Domain`='RT::Queue-Role'\n";
        }

        $q = "SELECT * FROM (\n";
        $q .= $firstSubquery . $secondSubquery;
        $q .= ") AS tmptable GROUP BY `id`, `EmailAddress`\n";

        return $q;
  }

  private function getLimitSql()
  {
    if (count($this->params) === 0) {
          return;
        }

        $limitSql = 'WHERE ';

        foreach ($this->params as $key => $param) {
          if ($key == 'status') {
            $statusString = '(';
                foreach ($param as $status) {
                  $status = strtolower($status);
          $statusString .= "'$status',";
                }
                $statusString = substr($statusString, 0, -1) . ')';

                $limitSql .= "LOWER(T.`Status`) IN $statusString AND\n";
          }

          if ($key == 'type') {
            $typeString = '(';
                foreach ($param as $type) {
                  $type = strtolower($type);
          $typeString .= "'$type',";
                }
                $typeString = substr($typeString, 0, -1) . ')';

                $limitSql .= "LOWER(G.`Type`) IN $typeString AND\n";
          }

          if ($key == 'minHoursOld') {
        $cutoffTime = (time() - ($param * 3600));
        $cutoffFormat = date('Y-m-d H:m:s', $cutoffTime);
                $dateString = "'$cutoffFormat'";

                $limitSql .= "T.`LastUpdated` < $dateString AND\n";
          }
        }

        $limitSql = substr($limitSql, 0, -5);

        return $limitSql;
  }

  private function isValidStatus($input)
  {
    if (in_array($input, $this->validStatuses) === true) {
      return true;
    } else {
      return false;
    }
  }

  private function isValidTypes($input)
  {
    if (in_array($input, $this->validTypes) === true) {
      return true;
    } else {
      return false;
    }
  }

  private function getValidTypes()
  {
    $q = 'SELECT DISTINCT LOWER(type) FROM Groups';
    $r = $this->db->query($q);

    if (PEAR::isError($r) === true) {
      throw new Exception($r->getMessage());
    }

    $a = array();
    while ($row = $r->fetchRow()) {
      $a[] = $row[0];
    }

    return $a;
  }
}

