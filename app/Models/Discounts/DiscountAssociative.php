<?php

namespace App\Models\Discounts;

/**
 * Если одновременно выбраны продукт $product и один из $group, 
 * то стоимость $product уменьшается на $decreaseValue процентов
 *
 * @author kuro
 */
class DiscountAssociative implements DiscountInterface {
    
    protected $decreaseValue;
    protected $group;
    protected $description;

    public function __construct($product, $decreaseValue, $group, $description) {
        $this->description = $description;
        $this->product = $product;
        $this->decreaseValue = $decreaseValue;
        $this->group = $group;
    }
    
    public function apply($products) {
        $groupMarker = false;
        foreach($products as $productKey => $product) {
            foreach($this->group as $associatedProduct) {
                if ($product->title == $associatedProduct) {
                    $groupMarker = true;
                    break;
                }
            }
            if ($groupMarker) break;
        }
        
        if (!$groupMarker) return $products;
        
        foreach($products as $productKey => $product) {
            if ($product->title == $this->product) {
                $product->discount = $this;
                unset($products[$productKey]);
            }
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
