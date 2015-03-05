<?php
namespace app\commands;

use app\models\ProductComplAdd;
use app\models\ProductComplMain;
use app\models\Dealer;
use app\models\ProductFiles;
use app\models\ProductImages;
use app\models\ProductParts;
use app\models\ProductProperties;
use app\models\Products;
use yii\console\Controller;
use serhatozles\simplehtmldom\SimpleHTMLDom;
use app\models\Categories;
use yii\console\Exception;
use yii\db\Query;
use yii\helpers\FileHelper;

/**
 * Команда парсинга сайта tonar.info
 */
class TonarController extends Controller
{
    /**
     * @const
     */
    const BASE_URL = 'http://www.tonar.info';

    public static function removeDirectory($dir)
    {
        if (!($handle = opendir($dir))) {
            return;
        }
        while (($file = readdir($handle)) !== false) {
            if ($file[0] === '.') { // Skip hidden files and current/parent dir
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                static::removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        closedir($handle);
    }

    /**
     * Запуск.
     */
    public function actionIndex($flush = false)
    {
        if ($flush) {
            \Yii::$app->db->createCommand('TRUNCATE categories')->execute();
            \Yii::$app->db->createCommand('TRUNCATE products')->execute();
            \Yii::$app->db->createCommand('TRUNCATE product_compl_add')->execute();
            \Yii::$app->db->createCommand('TRUNCATE product_compl_main')->execute();
            \Yii::$app->db->createCommand('TRUNCATE product_files')->execute();
            \Yii::$app->db->createCommand('TRUNCATE product_images')->execute();
            \Yii::$app->db->createCommand('TRUNCATE product_parts')->execute();
            \Yii::$app->db->createCommand('TRUNCATE product_properties')->execute();

            $uploadDir = \Yii::$app->getBasePath() . '/web/uploads';
            static::removeDirectory($uploadDir . '/original');
            static::removeDirectory($uploadDir . '/resized');
        }

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

    public function actionDealers($flush=fale) {
        if ($flush) {
            Dealer::deleteAll();
        }

        $input = file_get_contents(self::BASE_URL . '/service-spares/service/service-map/');
        $startString = 'group.add(createPlacemark(';
        $endString = '));';

        $codeItems = [];
        $index = 0;
        while (true) {
            $index = strpos($input, $startString, $index + 1);
            if ($index === false) {
                break;
            }

            $endIndex = strpos($input, $endString, $index);
            $codeItems[] = substr($input, $index + strlen($startString), $endIndex - $index - strlen($startString));
            $index = $endIndex;
        }

        $tonarIds = [];

        // Split by `,`
        foreach ($codeItems as $code) {
            $item = [];

            $lastSavedIndex = 0;
            $openedChar = null;
            for ($index = 0; $index < strlen($code); $index++) {
                $char = $code[$index];

                switch ($char) {
                    case ',':
                        if ($openedChar === null) {
                            $item[] = trim(substr($code, $lastSavedIndex, $index - $lastSavedIndex), " \t\n\r\0\x0B\"'");
                            $lastSavedIndex = $index + 1;
                        }
                        break;

                    case '"':
                    case "'":
                        if ($code[$index-1] !== '\\') {
                            if ($openedChar === $char) {
                                $openedChar = null;
                            } else if ($openedChar === null) {
                                $openedChar = $char;
                            }
                        }
                        break;
                }
            }

            $item[] = trim(substr($code, $lastSavedIndex), " \t\n\r\0\x0B\"'");

            list ($geoPointX, $geoPointY, $name, $description, $address, $phone, $siteUrl, $detailUrl, $tonarId, $city) = $item;
            $geoPointX = trim(preg_replace('/[^0-9.]/', '', $geoPointX), '.');
            $geoPointY = preg_replace('/[^0-9.]/', '', $geoPointY);
            $name = html_entity_decode($name);
            $description = html_entity_decode($description);

            $tonarIds[] = (int) $tonarId;

            $model = Dealer::findOne(['tonarId' => (int) $tonarId]) ?: new Dealer();
            $model->setAttributes([
                'geoPointX' => $geoPointX,
                'geoPointY' => $geoPointY,
                'name' => $name,
                'description' => $description,
                'address' => $address,
                'phone' => $phone,
                'siteUrl' => $siteUrl,
                'tonarId' => $tonarId,
                'city' => $city,
            ]);
            if (!$model->save()) {
                var_dump($model->getErrors(), $geoPointX, $geoPointY, $name, $description, $address, $phone, $siteUrl, $detailUrl, $tonarId, $city);
            }
        }

        // Remove old items
        if (count($tonarIds) > 0) {
            Dealer::deleteAll(['not in', 'tonarId', $tonarIds]);
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
            $product = $this->getProductInfo($category, $item->href, $shortDesc);
            $this->getCosts($product, $item->href);
            $this->getParts($product, $item->href);
            $this->downloadFiles($product, $item->href);
            $products[] = $product;
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

    /**
     * Вернет цены.
     * @param $product
     * @param $url
     */
    private function getCosts(&$product, $url)
    {
        $root = SimpleHTMLDom::file_get_html(self::BASE_URL . $url);

        //  Удаляем все.
        $models = ProductComplMain::findAll(['product_id' => $product->id]);
        foreach ($models as $model) {
            $model->delete();
        }
        $models = ProductComplAdd::findAll(['product_id' => $product->id]);
        foreach ($models as $model) {
            $model->delete();
        }

        //  Основная комплектация.
        foreach ($root->find('.card_prices_body', 0)->find('tr') as $k => $tr) {
            if ($k == 0) {
                continue;
            }

            $model = new ProductComplMain;
            $model->product_id = $product->id;
            $model->model = $tr->find('td', 0)->text();
            $model->description = $tr->find('td', 1)->text();
            if ($tr->find('td', 3)) {
                $model->ccy = $tr->find('td', 2)->text();
                $model->cost = $tr->find('td', 3)->text();
            }
            else {
                $model->cost = $tr->find('td', 2)->text();
            }
            if (!$model->save()) {
                var_dump($model->getErrors());
                throw new Exception('Не удалось сохранить основную комплектацию у товара ' . $product->id);
            }
        }

        //  Доп. комплектация.
        foreach ($root->find('.card_prices_body', 1)->find('tr') as $k => $tr) {
            if ($k == 0) {
                continue;
            }

            $model = new ProductComplAdd;
            $model->product_id = $product->id;
            $model->name = $tr->find('td', 0)->text();
            $model->complImage = self::BASE_URL . $tr->find('td', 1)->find('a', 0)->href;
            $model->cost = $tr->find('td', 2)->text();
            if (!$model->save()) {
                var_dump($model->getErrors());
                throw new Exception('Не удалось сохранить доп. комплектацию у товара ' . $product->id);
            }
        }
    }

    /**
     * Вернет детали.
     * @param $product
     * @param $url
     */
    private function getParts(&$product, $url)
    {
        $root = SimpleHTMLDom::file_get_html(self::BASE_URL . $url);

        //  Удаляем все.
        $models = ProductParts::findAll(['product_id' => $product->id]);
        foreach ($models as $model) {
            $model->delete();
        }

        foreach ($root->find('a.zapchasti') as $a) {
            $parseKey = sha1($a->href);
            if (ProductParts::findOne(['parse_key' => $parseKey])) {
                continue;
            }

            $domItem = SimpleHTMLDom::file_get_html(self::BASE_URL . $a->href);
            $name = $domItem->find('.zapchast-name', 0);

            $model = new ProductParts;
            $model->product_id = $product->id;
            $model->name = isset($name) ? trim($name->text()) : '';
            $model->description = trim(str_replace($model->name, '', $domItem->find('.zapchast-body', 0)->text()));
            $model->image = self::BASE_URL . $domItem->find('img', 0)->src;
            $model->parse_key = $parseKey;
            if (!$model->save()) {
                var_dump($model->getErrors());
                throw new Exception('Не удалось сохранить основную комплектацию у товара ' . $product->id);
            }
        }
    }

    /**
     * Скачает файлы из блока "скачать".
     * @param $product
     * @param $url
     */
    private function downloadFiles(&$product, $url)
    {
        $root = SimpleHTMLDom::file_get_html(self::BASE_URL . $url);

        foreach ($root->find('.download_item') as $item) {
            $a = $item->find('a', 0);
            $href = self::BASE_URL . $a->href;
            $parseKey = sha1($href);

            //  Если уже файл есть в базе, то не сохраняем его.
            if (ProductFiles::findOne(['parse_key' => $parseKey])) {
                return;
            }

            $model = new ProductFiles;
            $model->product_id = $product->id;
            $model->filename = $href;
            $model->name = trim($a->find('span', 0)->text());
            $model->parse_key = $parseKey;
            if (!$model->save()) {
                var_dump($model->getErrors());
                throw new Exception('Не удалось сохранить файл товара ' . $product->id);
            }
        }
    }
}
