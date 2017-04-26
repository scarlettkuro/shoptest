<?php use App\App; ?>
<div class="card-columns">
    <?php foreach($products as $product) : ?>
        <div class="card">
            <img class="card-img-top img-thumbnail" src="<?=$product->img?>" alt="Card image cap">
            <div class="card-block">
                <h4 class="card-title"><?=$product->title?></h4>
                <p class="card-text"><?=nl2br($product->description)?></p>
                <?php if ($cart->hasProduct($product)) : ?>
                    <p class="card-text">
                        <small class="text-muted">Добавлено</small>
                    </p>
                <?php else : ?>
                    <a href="<?=App::app()->route([\App\Controllers\Index::class, 'add'], ['id' => $product->id])?>" class="btn btn-primary">Добавить в корзину</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>