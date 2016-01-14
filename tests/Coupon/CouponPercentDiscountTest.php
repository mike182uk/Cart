<?php

use Cart\Cart;
use Cart\Coupon\CouponCollection;
use Cart\Catalog\Catalog;
use Cart\Catalog\ProductDomain;
use Cart\Catalog\ProductSharedHosting;
use Cart\Catalog\ProductSsl;
use Cart\Catalog\Term;
use Mockery as m;

class CouponPercentDiscountTest extends CartTestCase
{

    /**
     * @group coupon
     */
    public function testCartCouponPercent()
    {
        $term        = new Term(1);
        $term->setOld(12);
        $term->setPrice(10);

        $product        = new ProductSharedHosting();
        $product->setId(21);
        $product->setTitle('Silver');
        $product->getBilling()->addTerm($term);

        $catalog = $this->getCatalog();

        $item = $catalog->getCartItem($product, [
            'plan' => 'silver',
        ]);

        $coupons = $this->getCouponsCollection();
        $coupon  = $coupons->getCoupon('CYBER');

        $cart = $this->getCart();
        $cart->add($item);
        $coupon->calculateDiscount($cart);

        $items = $cart->all();
        $this->assertEquals(2.5, $items[0]->getDiscount());
    }
}