<?php

use PHPUnit\Framework\TestCase;

use App\Models\Discounts\DiscountGroup;
use App\Models\Product;
/**
 * Description of DiscountGroupTest
 *
 * @author kuro
 */
class DiscountGroupTest extends TestCase {

    /**
     * @dataProvider calculateProvider()
     */
    public function testCalculate($price, $decreaseValue, $expected) {
        //arrange
        $discount = new DiscountGroup($decreaseValue, [], '');
        
        //act
        $priceWithDiscount = $discount->calculate($price);
        
        //assert
        $this->assertEquals(
            $expected,
            $priceWithDiscount
        );
    }

    public function calculateProvider() {
        
        return [
            [0, 0.5, 0],
            [0, -0.5, 0],
            [0, 0, 0],
            [80, -0.1, 72],
            [80, 0, 80],
            
        ];
    }
    
    /**
     * @expectedException App\Models\Discounts\DiscountConditionContainDuplicatesException
     * @dataProvider duplicatesGroupProvider()
     */
    public function testDuplicatesDiscountGroup($discountGroup, $products, $expected) {
        //arrange
        $discount = new DiscountGroup(0, $discountGroup, '');
        
        //act
        $discount->apply($products);
        
        //assert
        //... exception
    }
    
    public function duplicatesGroupProvider() {
        $product1 = new Product();
        $product1->title = 'TestProduct1';
        $product2 = new Product();
        $product2->title = 'TestProduct2';
        
        return [
            [['TestProduct1', 'TestProduct1'], [clone $product1], [true]], 
            [['TestProduct1', 'TestProduct2', 'TestProduct1'], [clone $product1, clone $product2, clone $product1], [true, true, false]]
        ];
    }

    /**
     * @dataProvider selectGroupProvider()
     */
    public function testSelectDiscountGroup($discountGroup, $products, $expected) {
        //arrange
        $discount = new DiscountGroup(0, $discountGroup, '');
        
        //act
        $discount->apply($products);
        
        //assert
        $discountMarks = array_map(function($product) use ($discount) {
            return isset($product->discount) && $product->discount == $discount;
        }, $products);
        $this->assertEquals(
            $expected,
            $discountMarks
        );
    }
    
    public function selectGroupProvider() {
        $product1 = new Product();
        $product1->title = 'TestProduct1';
        $product2 = new Product();
        $product2->title = 'TestProduct2';
        $product3 = new Product();
        $product3->title = 'TestProduct3';
        
        return [
            /* product group appears */
            [['TestProduct1'], [clone $product1], [true]],
            [['TestProduct1'], [clone $product1, clone $product2], [true, false]],
            [['TestProduct1'], [clone $product2, clone $product1], [false, true]],
            [['TestProduct1', 'TestProduct2'], [clone $product1, clone $product2], [true, true]],
            [['TestProduct1', 'TestProduct2'], [clone $product1, clone $product2, clone $product3], [true, true, false]],
            [['TestProduct1', 'TestProduct2', 'TestProduct3'], [clone $product1, clone $product2, clone $product3], [true, true, true]],
            
            /* elements are in different positions */
            [['TestProduct1', 'TestProduct2'], [clone $product2, clone $product3, clone $product1], [true, false, true]],
            
            /* detection : group appears few times*/
            [['TestProduct1'], [clone $product1, clone $product1], [true, true]],
            [['TestProduct1', 'TestProduct2'], [clone $product1, clone $product2, clone $product2, clone $product3, clone $product1], [true, true, true, false, true]],
            
            /* appeared only part of the group */
            [['TestProduct1', 'TestProduct2'], [clone $product1], [false]],
            [['TestProduct1', 'TestProduct2'], [clone $product1, clone $product2, clone $product1], [true, true, false]],
            [['TestProduct1', 'TestProduct2', 'TestProduct3'], [clone $product1], [false]],
            [['TestProduct1', 'TestProduct2', 'TestProduct3'], [clone $product1, clone $product2], [false, false]],
            
            /* detection : no correct group appeared */
            [['TestProduct1'], [clone $product2], [false]],
            [['TestProduct1'], [clone $product2, clone $product2], [false, false]]
        ];
    }

    /**
     * @dataProvider removeGroupProvider()
     */
    public function testRemoveDiscountGroup($discountGroup, $products, $expected) {
        //arrange
        $discount = new DiscountGroup(0, $discountGroup, '');
        
        //act
        $newProductList = $discount->apply($products);
        
        //assert
        $this->assertEquals(
            array_values($expected),
            array_values($newProductList)
        );
    }
    
    public function removeGroupProvider() {
        $product1 = new Product();
        $product1->title = 'TestProduct1';
        $product2 = new Product();
        $product2->title = 'TestProduct2';
        $product3 = new Product();
        $product3->title = 'TestProduct3';
        
        return [
            /* product group appears */
            [['TestProduct1'], [$product1], []],
            [['TestProduct1'], [$product1, $product2], [$product2]],
            [['TestProduct1'], [$product2, $product1], [$product2]],
            [['TestProduct1', 'TestProduct2'], [$product2, $product3, $product1], [$product3]],
            
            /* detection : group appears few times*/
            [['TestProduct1'], [$product1, $product1], []],
            [['TestProduct1', 'TestProduct2'], [$product1, $product2, $product2, $product3, $product1], [$product3]]
            
        ];
    }
}
