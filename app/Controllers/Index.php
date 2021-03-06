<?php

namespace App\Controllers;

use App\App;

class Index {
    public static function index() {
        $productDAO = App::app()->component('dao')->getModelDAO(\App\Models\Product::class);
        $products = $productDAO->readAll();
        
        $view = new \App\View();
        return $view->render('index.htm.php', [
            'cart' => App::app()->component('cart'),
            'products' => $products
        ]);
    }

    public static function cart() {
        $cart = App::app()->component('cart');
        $cart->applyDiscounts();
        
        $view = new \App\View();
        return $view->render('cart.htm.php', [
            'cart' => $cart
        ]);
    }

    public static function add($id) {
        $cart = App::app()->component('cart');
        $productDAO = App::app()->component('dao')->getModelDAO(\App\Models\Product::class);
        $cart->addProduct($productDAO->read($id));
        App::app()->component('router')->redirect('/');
    }

    public static function remove($id) {
        $cart = App::app()->component('cart');
        $productDAO = App::app()->component('dao')->getModelDAO(\App\Models\Product::class);
        $cart->removeProduct($productDAO->read($id));
        App::app()->component('router')->redirect('/cart');
    }
}