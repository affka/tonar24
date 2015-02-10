<?php
namespace app\commands;

use app\models\Products;
use yii\console\Controller;
use serhatozles\simplehtmldom\SimpleHTMLDom;
use app\models\Categories;
use yii\console\Exception;
use yii\db\ActiveRecord;
use yii\db\Expression;

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
            $name = html_entity_decode(trim($a->text()));
            $category = Categories::findOne(['name' => $name]);
            if (!$category) {
                $category = new Categories;
                $category->name = $name;
                if (!$category->save()) {
                    throw new Exception('Не удалось создать категорию');
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
            $products[] = $this->getProductInfo($category, $item->href);
        }

        return $products;
    }

    /**
     * Вернет информацию о товаре по url а так же сделает нужные записи в базе.
     * @param string $url
     */
    private function getProductInfo(&$category, $url)
    {
        echo "=== $url\n";

        //  Если товар уже в базе, то вернем его модель.
        $parseKey = sha1($url);
        $product = Products::findOne(['parse_key' => $parseKey]);
        if ($product) {
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
        $product->category_id = $category->id;

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

        $product->images = $images;
        $product->properties = $properties;
        if (!$product->save()) {
            var_dump($product->getErrors());
            throw new Exception('Не удалось сохранить товар! ');
        }

        return $product;
    }
}
