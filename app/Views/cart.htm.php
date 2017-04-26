<?php use App\App; ?>
<h1>Корзина</h1>
<div class="row">
    <div class="col-md-8">

        <?php if(count($cart->getProducts()) > 0) : ?>
            <table class="table">
                <thead class="thead-inverse">
                    <tr>
                      <th>Товар</th>
                      <th>Стоимость</th>
                      <th>Скидки</th>
                      <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($cart->getProducts() as $product) : ?>
                        <tr>
                            <th scope="row"><?=$product->title?></th>
                            <td><?=$product->getPriceWithDiscount()?></td>
                            <td>
                            <?php if ($product->discount) : ?>
                              <?=$product->discount->getDescription()?>
                            <?php endif; ?>
                            </td>
                            <td><a href="<?=App::app()->route([\App\Controllers\Index::class, 'remove'], ['id' => $product->id])?>" class="btn btn-secondary btn-sm">удалить</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>Ваша корзина пуста</p>
        <?php endif; ?>
    </div>

    <div class="col-md-4">
        <h1>Сумма:</h1>
        <h1><?=$cart->getTotal()?></h1>
    </div>
    
</div>