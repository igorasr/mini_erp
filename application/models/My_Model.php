<?php
defined('BASEPATH') or exit('No direct script access allowed');

class My_Model extends CI_Model
{
  protected string $tabel;
  protected int $id;

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  public function id(): int
  {
    return $this->id;
  }

  public function setId(int $id): void
  {
    if (!empty($this->id)) {
      throw new \InvalidArgumentException('ID is already set and cannot be changed.');
    }

    $this->id = $id;
  }

  public function _to_db(): array
  {
    $fields = $this->db->list_fields($this->tabel);
    $data = [];

    foreach ($fields as $field) {
      $data[$field] = isset($this->$field) ? $this->$field : null;
    }

    return $data;
  }

  public function fill(array $data): void
  {
    foreach ($data as $key => $value) {
      if (property_exists($this, $key)) {
        $this->$key = $value;
      }
    }
  }
}
