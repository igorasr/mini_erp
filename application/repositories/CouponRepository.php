<?php

class CouponRepository extends My_Repository
{
  public function __construct()
  {
    parent::__construct();
    $this->CI->load->model('Coupon');
  }

  public function getCouponByCode(string $code): ?Coupon
  {
    $query = $this->db->get_where('coupons', ['code' => $code]);

    if ($query->num_rows() <= 0) return null;
    
    $data = $query->row_array();
    $coupon = new Coupon();
    $coupon->fill($data);
    return $coupon;
  }

  public function saveCoupon(Coupon $coupon): bool
  {
    return $this->db->replace('coupons', $coupon->_to_db());
  }

  public function markAsUsed(Coupon $coupon): void
  {
      $coupon->used_count += 1;
      $this->db->where('id', $coupon->id());
      $this->db->update('coupons', ['used_count' => $coupon->used_count]);
  }
}
