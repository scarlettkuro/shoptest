<?php

namespace App\Models\Discounts;

interface DiscountInterface {
    
    public function apply($products);
    public function calculate($price);
    public function setDescription($description);
    public function getDescription();
    
}