<?php

use PHPUnit\Framework\TestCase;

use App\Models\Product;

/**
 * Description of ProductTest
 *
 * @author kuro
 */
class ProductTest extends TestCase {
    
    public function testPriceWithDiscount() {
        //arrange
        $product = new Product();
        $product->price = 80;
        $product->discount = $this->getMockBuilder(App\Models\Discount::class)->setMethods(['calculate'])->getMock();
        $product->discount->expects($this->once())
                ->method('calculate')
                ->with($this->equalTo($product->price));
        
        //act, assert
        $product->getPriceWithDiscount();
    }
}
