<?php

namespace App\Models;

class Product implements \App\Components\DAO\ModelInterface {
    public $id;
    public $title;
    public $description;
    public $img;
    public $price;
    public $discount;
    
    public function getPriceWithDiscount() {
        return $this->discount ? 
            $this->discount->calculate($this->price) :
            $this->price;
    }

    public static function fields() {
        return ['id', 'title', 'description', 'img', 'price'];
    }

    public static function primary() {
        return 'id';
    }

    public static function table_name() {
        return 'products';
    }

}