<?php

namespace App\Models\Discounts;

/**
 * Если пользователь выбрал одновременно 3 продукта, он получает скидку 5% от суммы заказа
 * Если пользователь выбрал одновременно 4 продукта, он получает скидку 10% от суммы заказа
 * Если пользователь выбрал одновременно 5 продуктов, он получает скидку 20% от суммы заказа
 * 
 * @author kuro
 */
class DiscountTotal implements DiscountInterface {
    
    public $decreaseValue;
    public $excludeProducts;
    protected $description;

    public function __construct($decreaseValue, $excludeProducts) {
        $this->decreaseValue = $decreaseValue;
        $this->excludeProducts = $excludeProducts;
    }
    
    public function apply($products) {
        $discountProducts = [];
        
        foreach($products as $productKey => $product) {
            $include = array_reduce($this->excludeProducts, function($include, $excludeProduct) use ($product) {
                return $include && ($product->title == $excludeProduct);
            }, true);
            if (!$include) continue;
            $discountProducts[] = $product;
            unset($products[$productKey]);
            
            if (count($discountProducts) == 5) break;
        }
        
        switch (count($discountProducts)) {
            case 3: $this->decreaseValue = -0.05; break;
            case 4: $this->decreaseValue = -0.1; break;
            case 5: $this->decreaseValue = -0.2; break;
        }
        
        foreach($discountProducts as $discountProduct) {
            $discountProduct->discount = $this;
        }
        
        return $products;
    }
    
    public function calculate($price) {
        return $price * (float)(1 + $this->decreaseValue);
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

}
