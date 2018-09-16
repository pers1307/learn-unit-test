<?php

namespace TDD;

class Receipt
{
    public $tax;

    public $formatter;

    public function __construct($formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @param array $items
     * @return float|int
     */
    public function subtotal(array $items = [], $coupon)
    {
        if ($coupon > 1.00) {
            throw new \BadMethodCallException('Coupon > 1');
        }

        $sum = array_sum($items);
        if (!is_null($items)) {
            return $sum - ($sum * $coupon);
        }

        return $sum;
    }

    public function tax($amount)
    {
        return $this->formatter->currencyAmt($amount * $this->tax);
    }

    public function postTaxTotal($items, $coupon)
    {
        $subtotal = $this->subtotal($items, $coupon);

        return $subtotal + $this->tax($subtotal);
    }
}