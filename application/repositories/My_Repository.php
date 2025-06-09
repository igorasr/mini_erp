<?php

class My_Repository
{
  protected $db;
  protected $CI;

  public function __construct()
  {
    $this->CI =& get_instance();
    $this->db = $this->CI->db; 
  }
}