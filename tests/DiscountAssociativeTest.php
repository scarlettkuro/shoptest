<?php

use PHPUnit\Framework\TestCase;

use App\Models\Discounts\DiscountAssociative;
use App\Models\Product;
/**
 * Description of DiscountAssociativeTest
 *
 * @author kuro
 */
class DiscountAssociativeTest extends TestCase {

    /**
     * @dataProvider calculateProvider()
     */
    public function testCalculate($price, $decreaseValue, $expected) {
        //arrange
        $discount = new DiscountAssociative(null, $decreaseValue, [], '');
        
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
     * @expectedException App\Models\Discounts\DiscountConditionSelfReferenceException
     */
    public function testSelfReference() {
        //arrange
        $product = new Product();
        $product->title = 'DiscountProduct';
        $discount = new DiscountAssociative($product->title, 0, [$product->title], '');
        
        //act
        $discount->apply([$product]);
        
        //assert
        //... exceptions
    }

    /**
     * @dataProvider selectGroupProvider()
     */
    public function testSelectDiscountGroup($product, $discountGroup, $products, $expected) {
        //arrange
        $discount = new DiscountAssociative($product->title, 0, $discountGroup, '');
        
        //act
        $discount->apply($products);
        
        //assert
        $this->assertEquals(
            $expected,
            isset($product->discount) && $product->discount == $discount
        );
    }
    
    public function selectGroupProvider() {
        $product = new Product();
        $product->title = 'DiscountProduct';
        
        $product1 = new Product();
        $product1->title = 'TestProduct1';
        $product2 = new Product();
        $product2->title = 'TestProduct2';
        $product3 = new Product();
        $product3->title = 'TestProduct3';
        
        $assertGroups = [];
        
        /* there is correct assicoiated product */
        $newProduct = clone $product;
        $assertGroups[] = [$newProduct, ['TestProduct1'], [$newProduct, clone $product1], true];
        
        $newProduct = clone $product;
        $assertGroups[] = [$newProduct, ['TestProduct1', 'TestProduct2'], [$newProduct, clone $product2], true];
        
        /* there is no correct assicoiated product */
        $newProduct = clone $product;
        $assertGroups[] = [$newProduct, ['TestProduct1'], [$newProduct, clone $product2], false];
        
        return $assertGroups;
    }

    /**
     * @dataProvider removeProductProvider()
     */
    public function testRemoveProduct($product, $discountGroup, $products, $expected) {
        //arrange
        $discount = new DiscountAssociative($product, 0, $discountGroup, '');
        
        //act
        $newProductList = $discount->apply($products);
        
        //assert
        $this->assertEquals(
            array_values($expected),
            array_values($newProductList)
        );
    }
    
    public function removeProductProvider() {
        $product1 = new Product();
        $product1->title = 'TestProduct1';
        $product2 = new Product();
        $product2->title = 'TestProduct2';
        $product3 = new Product();
        $product3->title = 'TestProduct3';
        
        return [
            ['TestProduct1', ['TestProduct2'], [$product1, $product2], [$product2]],
            ['TestProduct1', ['TestProduct2', 'TestProduct3'], [$product1, $product2], [$product2]],
            ['TestProduct1', ['TestProduct2', 'TestProduct3'], [$product1, $product2, $product3], [$product2, $product3]],
            ['TestProduct1', ['TestProduct2', 'TestProduct3'], [$product2, $product3], [$product2, $product3]]
        ];
    }
}
