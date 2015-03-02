<?php
namespace app\commands;

use app\models\Products;
use yii\console\Controller;
use serhatozles\simplehtmldom\SimpleHTMLDom;
use app\models\Categories;
use yii\console\Exception;

/**
 * Команда парсинга сайта tonar.info
 */
class TonarController extends Controller
{
    /**
     * @const
     */
    const BASE_URL = 'http://www.tonar.info';

    /**
     * Запуск.
     */
    public function actionIndex()
    {
        $root = SimpleHTMLDom::file_get_html(self::BASE_URL . '/catalog/');
        foreach ($root->find('.aside1 a') as $a) {
            $rootCategory = null;
            $name = html_entity_decode(trim($a->text()));
            $category = Categories::findOne(['name' => $name]);
            if (!$category) {
                $category = new Categories;
                $category->name = $name;

                //  Получим описание категории.
                $rootCategory = SimpleHTMLDom::file_get_html(self::BASE_URL . $a->href);
                $description = $rootCategory->find('.description', 0);
                $category->description = trim($description ? $description->innertext : '');

                if (!$category->save()) {
                    var_dump($category->getErrors());
                    throw new Exception('Не удалось создать категорию');
                }
            }

            //  Если есть родители, то ничего не делаем.
            if ($a->parent()->parent()->parent()->tag == 'li') {
                continue;
            }

            //  Выясняем, есть ли подкатегории.
            $subA = $a->parent()->find('ul a');
            if (count($subA)) {
                foreach ($subA as $sa) {
                    $subName = html_entity_decode(trim($sa->text()));
                    $subCategory = Categories::findOne(['name' => $subName]);

                    if (!$subCategory) {
                        $subCategory = new Categories;
                        $subCategory->name = $subName;
                        $subCategory->parent_id = $category->id;

                        //  Получим описание категории.
                        if ($rootCategory) {
                            $description = $rootCategory->find('.description', 0);
                            $subCategory->description = trim($description ? $description->innertext : '');
                        }

                        if (!$subCategory->save()) {
                            throw new Exception('Не удалось создать подкатегорию');
                        }
                    }

                    $this->getProducts($subCategory, $sa->href);
                }
            }

            $this->getProducts($category, $a->href);
        }
    }

    /**
     * Спарсит товары в категории.
     * @param Categories $category
     * @param string $url
     * @return Products[]
     */
    private function getProducts(&$category, $url)
    {
        echo "Parse category {$category->name} ($url)\n";
        $root = SimpleHTMLDom::file_get_html(self::BASE_URL . $url);
        $products = [];
        foreach ($root->find('.catalog-section-item-img') as $item) {
            $shortDesc = html_entity_decode(trim($item->parent()->find('.list_element_text', 0)->text()));
            $products[] = $this->getProductInfo($category, $item->href, $shortDesc);
        }

        return $products;
    }

    /**
     * Вернет информацию о товаре по url а так же сделает нужные записи в базе.
     * @param $category
     * @param $url
     * @param $shortDesc
     * @return Products|static
     * @throws Exception
     */
    private function getProductInfo(&$category, $url, $shortDesc)
    {
        echo "=== $url\n";

        //  Если товар уже в базе, то вернем его модель.
        $parseKey = sha1($url);
        $product = Products::findOne(['parse_key' => $parseKey]);
        if ($product) {
            //  Если нашли продукт, то ничего не делаем с ним.
            return $product;
        }
        else {
            $product = new Products;
        }

        $root = SimpleHTMLDom::file_get_html(self::BASE_URL . $url);
        $images = [];
        $properties = [];

        //  Основная информация.
        $product->name = trim(strip_tags($root->find('.card_title', 0)->innertext));
        $product->description = trim(strip_tags($root->find('.card_description', 0)->innertext));
        $product->description = preg_replace('/[\s]{2,}/', ' ', $product->description);
        $product->description_short = preg_replace('/[\s]{2,}/', ' ', $shortDesc);
        $product->category_id = $category->id;
        $product->parse_key = $parseKey;

        //  Парсинг изображений.
        foreach ($root->find('.card_left .fancybox') as $img) {
            $images[] = self::BASE_URL . $img->href;
        }

        //  Свойства.
        foreach ($root->find('.card_options_table tr') as $prop) {
            $name = $prop->find('td', 0)->text();
            $value = htmlspecialchars_decode($prop->find('td', 1)->text());
            $properties[] = [
                'name' => $name,
                'value' => $value
            ];
        }

        $product->productImages = $images;
        $product->productProperties = $properties;
        if (!$product->save()) {
            var_dump($product->getErrors());
            throw new Exception('Не удалось сохранить товар!');
        }

        return $product;
    }
}
