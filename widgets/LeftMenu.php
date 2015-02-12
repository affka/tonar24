<?php
/**
 * Виджет для тображение левого блога навигации.
 */

namespace app\widgets;
use app\models\Categories;

/**
 * Class LeftMenuWidget
 */
class LeftMenu extends \yii\base\Widget
{
    /**
     * Запуск виджета.
     */
    public function run()
    {
        $menu = [];

        foreach (Categories::find()->orderBy('parent_id')->all() as $category) {
            if (!$category->parent_id) {
                $menu[$category->id] = [
                    'parent' => $category,
                    'childs' => []
                ];
                continue;
            }

            $menu[$category->parent_id]['childs'][] = $category;
        }

        return $this->render('left-menu.php', [
            'menu' => $menu
        ]);
    }
}