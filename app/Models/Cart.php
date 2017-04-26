<?php

namespace App\Models;

use App\App;

class Cart {
	
    const SESSION_CART_PARAM = 'cart';

    protected $discounts;
    protected $products;

    public function __construct() {
        if (!isset($_SESSION[self::SESSION_CART_PARAM])) {
            $_SESSION[self::SESSION_CART_PARAM] = [
                'products' => []
            ];
        }
        
        $this->discounts = [];
    }

    public function addProduct($product) {
        $_SESSION[self::SESSION_CART_PARAM]['products']['id_'.$product->id] = $product->id;
    }

    public function removeProduct($product) {
        unset($_SESSION[self::SESSION_CART_PARAM]['products']['id_'.$product->id]);
    }

    public function hasProduct($product) {
        return array_key_exists('id_'.$product->id, $_SESSION[self::SESSION_CART_PARAM]['products']);
    }

    public function getProducts() {
        if (!$this->products) {
            $this->products = [];
            $productDAO = App::app()->component('dao')->getModelDAO(\App\Models\Product::class);
            foreach ($_SESSION[self::SESSION_CART_PARAM]['products'] as $id) {
                $this->products[] = $productDAO->read($id);
            }
        }
        return $this->products;
    }
    
    public function applyDiscounts() {
        $products = $this->getProducts();
        foreach($this->discounts as $discount) {
            $products = $discount->apply($products);
        }
    }

    public function getTotal() {
        return array_reduce($this->getProducts(), function($total, $product) {
            return $total + $product->getPriceWithDiscount();
        });
    }
}