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

    public function __construct($excludeProducts, $description) {
        $this->description = $description;
        $this->excludeProducts = $excludeProducts;
    }
    
    public function apply($products) {
        $discountProductsKeys = [];
        
        foreach($products as $productKey => $product) {
            if (!in_array($product->title, $this->excludeProducts)) {
                $discountProductsKeys[] = $productKey;
                if (count($discountProductsKeys) == 5) break;
            }
        }
        
        switch (count($discountProductsKeys)) {
            case 3: $this->decreaseValue = -0.05; break;
            case 4: $this->decreaseValue = -0.1; break;
            case 5: $this->decreaseValue = -0.2; break;
            default: return $products;
        }
        
        foreach($discountProductsKeys as $discountProductKey) {
            $discountProduct = $products[$discountProductKey];
            $discountProduct->discount = $this;
            unset($products[$discountProductKey]);
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
