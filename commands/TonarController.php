<?php
namespace app\commands;

use app\models\Axis;
use app\models\Decision;
use app\models\Document;
use app\models\ProductComplAdd;
use app\models\ProductComplMain;
use app\models\Dealer;
use app\models\ProductFiles;
use app\models\ProductParts;
use app\models\Products;
use app\models\Spare;
use app\models\SpareGroup;
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

    public static function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

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

    public static function mb_trim($string, $trim_chars = '\s'){
        return preg_replace('/^['.$trim_chars.']*(?U)(.*)['.$trim_chars.']*$/u', '\\1',$string);
    }

    /**
     * Запуск.
     * @param bool $flush
     * @throws Exception
     * @throws \yii\db\Exception
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
            \Yii::$app->db->createCommand('TRUNCATE product_parts_junction')->execute();
            \Yii::$app->db->createCommand('TRUNCATE product_properties')->execute();

            static::removeDirectory(\Yii::$app->getBasePath() . '/web/uploads');
        }

        \Yii::$app->db->createCommand()->truncateTable('product_parts_junction')->execute();

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
            } else {
                $this->getProducts($category, $a->href);
            }
        }
    }

    public function actionDealers($flush=false) {
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

    public function actionSpares() {
        $root = SimpleHTMLDom::file_get_html(self::BASE_URL . '/service-spares/spares/catalog-spares/');
        $content = $root->find('#wrap', 0)->children(2);

        $items = [];
        $this->parseSpareItem($content, $items);

        $ids = [];
        $groupIds = [];
        foreach ($items as $groupTitle => $children) {
            echo "--- " . $groupTitle . "\n";
            // Save spare group
            $spareGroup = SpareGroup::findOne(['title' => $groupTitle]) ?: new SpareGroup();
            $spareGroup->title = $groupTitle;
            if (!$spareGroup->save()) {
                var_dump($spareGroup->getErrors());
                throw new Exception('Не удалось сохранить группу запчастей!');
            }
            $groupIds[] = $spareGroup->id;

            foreach ($children as $remoteUrl => $title) {
                echo $title . "\n";
                $hash = Spare::generateHash($remoteUrl);

                // Save spare item
                $spare = Spare::findOne(['hash' => $hash]) ?: new Spare();
                $spare->group_id = $spareGroup->id;
                $spare->title = $title;
                $spare->hash = $hash;
                $spare->remoteUrl = $remoteUrl;
                if (!$spare->save()) {
                    var_dump($spare->getErrors());
                    throw new Exception('Не удалось сохранить запчасть!');
                }
                $ids[] = $spare->id;
            }
        }

        // Remove not fined
        foreach (SpareGroup::find()->andWhere(['not in', 'id', $groupIds])->all() as $model) {
            $model->delete();
        }
        foreach (Spare::find()->andWhere(['not in', 'id', $ids])->all() as $model) {
            $model->delete();
        }
    }

    protected function parseSpareItem($node, &$items = [], &$group='') {

        switch ($node->tag) {
            case 'b':
                $text = self::mb_trim($node->plaintext);
                if ($text) {
                    $group = $text;
                }
                break;

            case 'a':
                $href = self::BASE_URL . $node->href;
                if (preg_match('/[а-яa-z0-9]/i', $node->plaintext) && $group && $href) {
                    if (!isset($items[$group])) {
                        $items[$group] = [];
                    }
                    if (!isset($items[$group][$href])) {
                        $items[$group][$href] = '';
                    }
                    $items[$group][$href] .= self::mb_trim($node->plaintext);
                }
                break;

            default:
                $i = 0;
                while ($item = $node->children($i++)){
                    $this->parseSpareItem($item, $items, $group);
                }
                break;
        }

        return $items;
    }

    public function actionDocuments() {
        $root = SimpleHTMLDom::file_get_html(self::BASE_URL . '/question-answer/normative-document/');
        $nodes = $root->find('.document-name a');

        $items = [];
        foreach ($nodes as $node) {
            $items[self::BASE_URL . $node->href] = self::mb_trim($node->plaintext);
        }

        $ids = [];
        foreach ($items as $remoteUrl => $title) {
            echo $title . "\n";
            $hash = Document::generateHash($remoteUrl);

            // Save item
            $document = Document::findOne(['hash' => $hash]) ?: new Document();
            $document->title = $title;
            $document->hash = $hash;
            $document->remoteUrl = $remoteUrl;
            if (!$document->save()) {
                var_dump($document->getErrors());
                throw new Exception('Не удалось сохранить документ!');
            }
            $ids[] = $document->id;
        }

        // Remove not fined
        foreach (Document::find()->andWhere(['not in', 'id', $ids])->all() as $model) {
            $model->delete();
        }
    }

    public function actionAxis() {
        $root = SimpleHTMLDom::file_get_html(self::BASE_URL . '/service-spares/spares/axis-tonar/');
        $nodes = $root->find('#wrap', 0)->children(2)->find('table td a');

        $items = [];
        foreach ($nodes as $node) {
            $text = trim($node->plaintext);
            if ($text) {
                $text = preg_replace('/^[0-9]+\.\s+/', '', $text); // remove number
                $items[self::BASE_URL . $node->href] = self::mb_trim($text);
            }
        }

        $ids = [];
        foreach ($items as $remoteUrl => $title) {
            echo $title . "\n";
            $hash = Axis::generateHash($remoteUrl);

            // Save item
            $axis = Axis::findOne(['hash' => $hash]) ?: new Axis();
            $axis->title = $title;
            $axis->hash = $hash;
            $axis->remoteUrl = $remoteUrl;
            if (!$axis->save()) {
                var_dump($axis->getErrors());
                throw new Exception('Не удалось сохранить ось!');
            }
            $ids[] = $axis->id;
        }

        // Remove not fined
        foreach (Axis::find()->andWhere(['not in', 'id', $ids])->all() as $model) {
            $model->delete();
        }
    }

    public function actionDecisions() {
        $root = SimpleHTMLDom::file_get_html(self::BASE_URL);
        $nodes = $root->find('#decision', 0)->find('option');

        \Yii::$app->db->createCommand()->truncateTable('decisions_products_junction')->execute();

        $ids = [];
        foreach ($nodes as $node) {
            echo "--- " . $node->value . "\n";

            $decision = Decision::findOne(['name' => $node->value]) ?: new Decision();
            $decision->name = $node->value;
            if (!$decision->save()) {
                var_dump($decision->getErrors());
                throw new Exception('Не удалось сохранить решение!');
            }
            $ids[] = $decision->id;

            $this->parseDecision($decision);
        }

        // Remove not fined
        foreach (Decision::find()->andWhere(['not in', 'id', $ids])->all() as $model) {
            $model->delete();
        }
    }

    protected function parseDecision($decision) {
        $root = SimpleHTMLDom::file_get_html(self::BASE_URL . '/catalog/?decision=' . urlencode($decision->name));

        foreach ($root->find('.catalog-section-item-img') as $item) {
            $product = Products::findOne(['parse_key' => sha1($item->href)]);
            if (!$product) {
                continue;
            }

            echo $product->name . "\n";
            $decision->link('products', $product);
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
        $product = Products::findOne(['parse_key' => $parseKey]) ?: new Products();

        $root = SimpleHTMLDom::file_get_html(self::BASE_URL . $url);
        $images = [];
        $properties = [];

        //  Основная информация.
        $product->name = trim($root->find('.card_title', 0)->plaintext);
        $product->description = trim(strip_tags($root->find('.card_description', 0)->innertext));
        $product->description_short = preg_replace('/[\s]{2,}/', ' ', $shortDesc);
        $product->category_id = $category->id;
        $product->parse_key = $parseKey;

        //  Парсинг изображений.
        foreach ($root->find('.card_left .fancybox') as $img) {
            $images[] = self::BASE_URL . $img->href;
        }

        //  Свойства.
        foreach ($root->find('.card_options_table tr') as $prop) {
            $name = trim($prop->find('td', 0)->text());
            $value = trim(htmlspecialchars_decode($prop->find('td', 1)->text()));

            if ($name && $value) {
                $properties[] = [
                    'name' => $name,
                    'value' => $value
                ];
            }
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
    private function getCosts($product, $url)
    {
        $root = SimpleHTMLDom::file_get_html(self::BASE_URL . $url);

        //  Основная комплектация.
        $ids = [];
        foreach ($root->find('.card_prices_body', 0)->find('tr') as $k => $tr) {
            if ($k == 0) {
                continue;
            }

            $modelName = $tr->find('td', 0)->text();

            $model = ProductComplMain::findOne(['product_id' => $product->id, 'model' => $modelName]) ?: new ProductComplMain;
            $model->product_id = $product->id;
            $model->model = $modelName;
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

            $ids[] = $model->id;
        }

        // Remove not fined
        $legacyModels = ProductComplMain::find()
            ->where(['product_id' => $product->id])
            ->andWhere(['not in', 'id', $ids])
            ->all();
        foreach ($legacyModels as $model) {
            $model->delete();
        }

        //  Доп. комплектация.
        $ids = [];
        foreach ($root->find('.card_prices_body', 1)->find('tr') as $k => $tr) {
            if ($k == 0) {
                continue;
            }

            $name = $tr->find('td', 0)->find('a', 0) ?
                $tr->find('td', 0)->find('a', 0)->text() :
                $tr->find('td', 0)->text();

            $hash = null;
            $remoteUrl = $tr->find('td', 1)->find('a', 0)->href;
            if ($remoteUrl) {
                $remoteUrl = self::BASE_URL . $remoteUrl;
                $hash = ProductComplAdd::generateHash($remoteUrl);
            }

            $model = ProductComplAdd::findOne(['product_id' => $product->id, 'name' => $name]) ?: new ProductComplAdd;
            $model->product_id = $product->id;
            $model->name = $name;
            $model->hash = $hash;
            $model->remoteUrl = $remoteUrl;
            $model->cost = $tr->find('td', 2)->text();

            if (!$model->save()) {
                var_dump($model->getErrors());
                throw new Exception('Не удалось сохранить доп. комплектацию у товара ' . $product->id);
            }

            $ids[] = $model->id;
        }

        // Remove not fined
        $legacyModels = ProductComplAdd::find()
            ->where(['product_id' => $product->id])
            ->andWhere(['not in', 'id', $ids])
            ->all();
        foreach ($legacyModels as $model) {
            $model->delete();
        }
    }

    /**
     * Вернет детали.
     * @param Products $product
     * @param $url
     */
    private function getParts(&$product, $url)
    {
        $root = SimpleHTMLDom::file_get_html(self::BASE_URL . $url);

        foreach ($root->find('a.zapchasti') as $a) {
            $parseKey = sha1($a->href);
            if ($model = ProductParts::findOne(['parse_key' => $parseKey])) {

                $product->link('parts', $model);
                continue;
            }

            $domItem = SimpleHTMLDom::file_get_html(self::BASE_URL . $a->href);
            if (!isset($domItem)) {
                continue;
            }

            $name = trim($domItem->find('.zapchast-name', 0) ?: '');
            $body = $domItem->find('.zapchast-body', 0);
            $remoteUrl = $domItem->find('img', 0) ? $domItem->find('img', 0)->src : null;
            $hash = null;
            if ($remoteUrl) {
                $remoteUrl = self::BASE_URL . $remoteUrl;
                $hash = ProductParts::generateHash($remoteUrl);
            }

            $model = ProductParts::findOne(['name' => $name, 'hash' => $hash]) ?: new ProductParts;
            $model->name = $name;
            $model->hash = $hash;
            $model->description = isset($body) ? trim(str_replace($model->name, '', $body->text())) : '';
            $model->remoteUrl = $remoteUrl;
            $model->parse_key = $parseKey;
            if (!$model->save()) {
                var_dump($model->getErrors());
                throw new Exception('Не удалось сохранить основную комплектацию у товара ' . $product->id);
            }

            $product->link('parts', $model);
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
            $remoteUrl = self::BASE_URL . $a->href;

            $hash = ProductFiles::generateHash($remoteUrl);

            $model = ProductFiles::findOne(['product_id' => $product->id, 'hash' => $hash]) ?: new ProductFiles;
            $model->product_id = $product->id;
            $model->remoteUrl = $remoteUrl;
            $model->hash = $hash;
            $model->name = trim($a->find('span', 0)->text());
            if (!$model->save()) {
                var_dump($model->getErrors());
                throw new Exception('Не удалось сохранить файл товара ' . $product->id);
            }
        }
    }
}
