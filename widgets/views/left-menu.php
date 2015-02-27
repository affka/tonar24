<?php
/**
 * @var \app\models\Categories[] $menu
 */
?>
<ul>
    <?php foreach ($menu as $item) { ?>
        <li><a href="/catalog/<?= $item['parent']->slug ?>" class=""><?= $item['parent']->name ?></a></li>
        <?php if (count($item['childs'])) { ?>
            <ul>
            <?php foreach ($item['childs'] as $child) { ?>
                <li><a href="/catalog/<?= $child->slug ?>" class=""><?= $child->name ?></a></li>
            <?php } ?>
            </ul>
        <?php } ?>
    <?php } ?>
</ul>