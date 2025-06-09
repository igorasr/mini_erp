<?php


class Coupon extends My_Model
{
  public string $code = '';
  public ?string $description;
  public float $discount_value;
  public ?int $usage_limit=null;
  public int $used_count=0;
  public $valid_from;
  public $valid_until;
  public bool $active; 

  public function __construct()
  {
    parent::__construct();
    $this->table = 'coupons';
  }

  public function isValid(): bool
  {
      $now = new DateTime();
      return $this->active && $now >= $this->valid_from && $now <= $this->valid_until;
  }

  public function _to_db(): array
  {
      $data = parent::_to_db();
      $data['valid_from'] = $this->valid_from->format('Y-m-d H:i:s');
      $data['valid_until'] = $this->valid_until->format('Y-m-d H:i:s');
      return $data;
  }
}