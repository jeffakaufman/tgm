<?php

class Silk_Ordersku_Model_System_Config_Source_Orders_Grid
{

    public function toArray()
    {
        $options = array(
            'real_order_id',
            'store_id',
            'created_at',
            'billing_name',
            'shipping_name',
            'ip',
            'skus',
            'grand_total',
            'coupon',
            'ship_method',
            'product_names',
            'product_options',
            'qnty',
            'weight',
            'billing_company',
            'shipping_company',
            'billing_street',
            'shipping_street',
            'billing_city',
            'shipping_city',
            'billing_region',
            'shipping_region',
            'billing_country',
            'shipping_country',
            'billing_postcode',
            'shipping_postcode',
            'billing_telephone',
            'shipping_telephone',
            'shipping_method',
            'tracking_number',
            'shipped',
            'customer_email',
            'customer_group',
            'payment_method',
            'base_subtotal',
            'subtotal',
            'base_shipping_amount',
            'shipping_amount',
            'base_tax_amount',
            'tax_amount',
            'base_discount_amount',
            'discount_amount',
            'base_internal_credit',
            'internal_credit',
            'base_total_refunded',
            'total_refunded',
            'base_grand_total',
            'order_comment',
            'order_group',
            'is_edited',
            'status',
            'action'
        );
        return $options;
    }
    public function toListColumnsArray()
    {
/*1.  Purchased From (Store)
2.  Purchased On
3.  Order #
4.  Bill to Name
5.  Ship to Name
6.  IP
7.  SKU
8.  G.T. Purchased (G.T. Base seems to be the same as G.T. Purchased, so it’s a bit redundant. Please remove if you can verify that there is no difference between the two.)
9.  Coupon
10. Ship Method
11. Status*/
        $options = array(
            'store_id',
            'created_at',
            'real_order_id',
            'billing_name',
            'shipping_name',
            'ip',
            'skus',
            'grand_total',
            'coupon',
            'ship_method',
            'status',
            );
        return $options;
    }
}
