<?php

class RtConstraint_Status implements RtConstraintInferface
{
  protected $statuses = array();

  public function __construct( array $statuses )
  {
    $this->statuses = $statuses;
  }
  
  public function getSql()
  {
  }
}
