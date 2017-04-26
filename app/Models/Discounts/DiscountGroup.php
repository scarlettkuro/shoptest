<?php

namespace App\Models\Discounts;

/**
 * Если одновременно выбраны категории $group, то их суммарная стоимость 
 * уменьшается на $decreaseValue процентов для каждого набора
 * 
 * @author kuro
 */
class DiscountGroup implements DiscountInterface {
    
    protected $decreaseValue;
    protected $group;
    protected $description;

    public function __construct($decreaseValue, $group, $description) {
        $this->description = $description;
        $this->decreaseValue = $decreaseValue;
        $this->group = $group;
    }
    
    public function apply($products) {        
        $discountCandidatesKeys = [];
        
        //save indexes of all products, that could be marked with this discount
        //we save index and not object - for easy using of unset function
        foreach ($products as $productKey => $product) {
            foreach ($this->group as $candidateType => $candidateTitle) {
                if ($product->title == $candidateTitle) {
                    $discountCandidatesKeys[$candidateType][] = $productKey;
                }
            }
        }
        
        //mark each full group of products with discount
        while(count($discountCandidatesKeys) == count($this->group)) {
            foreach (array_keys($discountCandidatesKeys) as $candidateType) {
                $productKey = array_shift($discountCandidatesKeys[$candidateType]);
                $product = $products[$productKey];
                $product->discount = $this;
                unset($products[$productKey]); //remove marked products
                if (empty($discountCandidatesKeys[$candidateType])) {
                    unset($discountCandidatesKeys[$candidateType]);
                }
            }
        }
        
        //return unmarked
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
