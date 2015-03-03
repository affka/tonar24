<?php
/**
 * @var \app\models\Categories[] $menu
 */

$currentCategory = null;
switch (Yii::$app->controller->id) {
    case 'product':
        $currentCategory = \app\models\Products::findOne(['slug' => Yii::$app->request->get('slug')])->category;
        break;

    case 'catalog':
        $currentCategory = \app\models\Categories::findOne(['slug' => Yii::$app->request->get('slug')]);
        break;
}

?>
<ul>
    <?php foreach ($menu as $item) { ?>
        <?php
            $isActive = $currentCategory && $item['parent']->id === $currentCategory->id;
            foreach ($item['childs'] as $child) {
                if ($currentCategory && $child->id === $currentCategory->id) {
                    $isActive = true;
                }
            }
        ?>
        <li class="<?= $isActive ? 'active' : '' ?>"><a href="/catalog/<?= $item['parent']->slug ?>" class=""><?= $item['parent']->name ?></a></li>
        <?php if (count($item['childs']) > 0) { ?>
            <ul>
            <?php foreach ($item['childs'] as $child) {
                $isChildActive = $currentCategory && $child->id === $currentCategory->id;
                ?>
                <li class="<?= $isChildActive ? 'active' : '' ?>"><a href="/catalog/<?= $child->slug ?>" class=""><?= $child->name ?></a></li>
            <?php } ?>
            </ul>
        <?php } ?>
    <?php } ?>
</ul>