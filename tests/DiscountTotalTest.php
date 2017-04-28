<?php

use PHPUnit\Framework\TestCase;

use App\Models\Discounts\DiscountTotal;
use App\Models\Product;

/**
 * Description of DiscountTotalTest
 *
 * @author kuro
 */
class DiscountTotalTest extends TestCase  {

    /**
     * @dataProvider decreaseValueProvider()
     */
    public function testDecreaseValue($excludeProducts, $products, $expected) {
        //arrange
        $discount = new DiscountTotal($excludeProducts, '');
        
        //act
        $discount->apply($products);
        
        //assert
        $this->assertEquals(
            $expected,
            $discount->decreaseValue
        );
    }

    public function decreaseValueProvider() {
        $product1 = new Product();
        $product1->title = 'TestProduct1';
        $product2 = new Product();
        $product2->title = 'TestProduct2';
        $product3 = new Product();
        $product3->title = 'TestProduct3';
        $product4 = new Product();
        $product4->title = 'TestProduct4';
        $product5 = new Product();
        $product5->title = 'TestProduct5';
        
        return [
            [[], [$product1], 0],
            [[], [$product1, $product2], 0],
            [[], [$product1, $product2, $product3], -0.05],
            [[], [$product1, $product2, $product3, $product4], -0.1],
            [[], [$product1, $product2, $product3, $product4, $product5], -0.2],
            [['TestProduct1'], [$product1, $product2, $product3, $product4], -0.05],
            [['TestProduct1', 'TestProduct3'], [$product1, $product2, $product3, $product4, $product5], -0.05],
            [['TestProduct1', 'TestProduct3'], [$product1, $product2, $product3, $product4], 0]
        ];
    }

    /**
     * @dataProvider selectGroupProvider()
     */
    public function testSelectGroup($excludeProducts, $products, $expected) {
        //arrange
        $discount = new DiscountTotal($excludeProducts, '');
        
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
        $product4 = new Product();
        $product4->title = 'TestProduct4';
        $product5 = new Product();
        $product5->title = 'TestProduct5';
        
        return [
            [[], [$product1], [false]],
            [[], [$product1, $product2], [false, false]],
            [[], [$product1, $product2, $product3], [true, true, true]],
            [[], [$product1, $product2, $product3, $product4], [true, true, true, true]],
            [[], [$product1, $product2, $product3, $product4, $product5], [true, true, true, true, true]],
            [['TestProduct1'], [$product1, $product2, $product3, $product4], [false, true, true, true]],
            [['TestProduct1', 'TestProduct3'], [$product1, $product2, $product3, $product4, $product5], [false, true, false, true, true]],
            [['TestProduct1', 'TestProduct3'], [$product1, $product2, $product3, $product4], [false, false, false, false]]
        ];
    }

    /**
     * @dataProvider removeGroupProvider()
     */
    public function testRemoveGroup($excludeProducts, $products, $expected) {
        //arrange
        $discount = new DiscountTotal($excludeProducts, '');
        
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
        $product4 = new Product();
        $product4->title = 'TestProduct4';
        $product5 = new Product();
        $product5->title = 'TestProduct5';
        
        return [
            [[], [$product1], [$product1]],
            [[], [$product1, $product2], [$product1, $product2]],
            [[], [$product1, $product2, $product3], []],
            [[], [$product1, $product2, $product3, $product4], []],
            [[], [$product1, $product2, $product3, $product4, $product5], []],
            [['TestProduct1'], [$product1, $product2, $product3, $product4], [$product1]],
            [['TestProduct1', 'TestProduct3'], [$product1, $product2, $product3, $product4, $product5], [$product1, $product3]],
            [['TestProduct1', 'TestProduct3'], [$product1, $product2, $product3, $product4], [$product1, $product2, $product3, $product4]]
        ];
    }
}