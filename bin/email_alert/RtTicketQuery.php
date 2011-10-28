<?php

class RtTicketQuery
{
  protected $pdo;
  protected $constraints;
  
  public function __construct( PDO $pdo )
  {
    $this->pdo = $pdo;
  }
  
  public function addConstraint( RtConstraintInterface $constraint )
  {
    $this->constraints[] = $constraint;
  }
  
  public function execute()
  {
  }
}
