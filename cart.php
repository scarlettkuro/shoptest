<?php
use \App\Models\Discounts\DiscountGroup;
use \App\Models\Discounts\DiscountAssociative;
use \App\Models\Discounts\DiscountTotal;

$cart = new App\Models\Cart();
$cart->addDiscount(new DiscountGroup(-0.1, ['A', 'B'], 'Для A и B -10%'));
$cart->addDiscount(new DiscountGroup(-0.05, ['D', 'E'], 'Для D и E -5%'));
$cart->addDiscount(new DiscountGroup(-0.05, ['E', 'F', 'G'], 'Для E, F, G -5%'));

$cart->addDiscount(new DiscountAssociative('A', -0.05, ['K', 'L', 'M'], 'Для A при [K,L,M] -5%'));

$cart->addDiscount(new DiscountTotal(['A', 'C'], 'Для нескольких продуктов'));

return $cart;
