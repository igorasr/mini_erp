<?php

class CouponService
{
    private CouponRepository $repository;

    public function __construct()
    {
        $this->repository = new CouponRepository();
    }

    public function getCouponByCode(string $code): ?Coupon
    {
        return $this->repository->getCouponByCode($code);
    }

    public function applyCouponToOrder(Order $order, string $code): Order
    {
        $coupon = $this->getCouponByCode($code);
        if ($coupon) {
            $order->applyCoupon($coupon);
        }
        return $order;
    }
}