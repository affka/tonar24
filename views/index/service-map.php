<?php

/* @var $this \yii\web\View */
/* @var $dealerModels \app\models\Dealer[] */

$this->title = 'Карта сервисной сети';

?>

<script type="text/javascript" src="http://api-maps.yandex.ru/2.0/?load=package.standard&amp;lang=ru-RU"></script>
<script type="text/javascript" src="http://api-maps.yandex.ru/1.1/index.xml"></script>



<script type="text/javascript">
    YMaps.jQuery(function () {
        var mapContainer = $("#YMapsID");
        var syncSize = function() {
            mapContainer.css('height', $(window).height() - $('body > header').height());
        };
        $(window).on('resize', syncSize);
        syncSize();

        var map = new YMaps.Map(mapContainer.get(0));

        function setCenter() {
            map.setCenter(new YMaps.GeoPoint(75, 56), 4);
        }
        setCenter();

        var toolbar = new YMaps.ToolBar([new YMaps.ToolBar.MoveButton(), new YMaps.ToolBar.MagnifierButton(), new YMaps.ToolBar.RulerButton()]);

        if (YMaps.location) {
            var place = new YMaps.GeoPoint(YMaps.location.longitude, YMaps.location.latitude);

            var button_show_myplace = new YMaps.ToolBarButton({
                caption: "Мое местоположение",
                hint: "Показывает Ваше предположительное местоположение"
            });

            var zoom = 10;

            YMaps.Events.observe(button_show_myplace, button_show_myplace.Events.Click, function () {
                map.closeBalloon();
                if (YMaps.location.zoom) {
                    zoom = YMaps.location.zoom;
                }
                map.setCenter(place, zoom);
                map.openBalloon(place, "Место вашего предположительного местоположения:<br/>"
                    + (YMaps.location.country || "")
                    + (YMaps.location.region ? ", " + YMaps.location.region : "")
                    + (YMaps.location.city ? ", " + YMaps.location.city : "")
                )
            }, map);

            toolbar.add(button_show_myplace);
        }

        var button_show_all = new YMaps.ToolBarButton({
            caption: "Вся карта",
            hint: "Показывает все магазины"
        });

        YMaps.Events.observe(button_show_all, button_show_all.Events.Click, function () {
            map.closeBalloon();
            setCenter();
        }, map);

        toolbar.add(button_show_all);

        map.addControl(toolbar);

        var pin = new YMaps.Style();

        pin.iconStyle = new YMaps.IconStyle();
        pin.iconStyle.href = "<?= Yii::$app->request->baseUrl ?>/img/icon32-point.png";

        pin.iconStyle.size = new YMaps.Point(32,32);
        pin.iconStyle.offset = new YMaps.Point(-16,-32);

        var group = new YMaps.GeoObjectCollection(pin);
        map.addControl(new YMaps.Zoom());
        map.addControl(new YMaps.ScaleLine());
        map.enableScrollZoom();

        <?php foreach ($dealerModels as $dealer) { ?>
            group.add(createPlacemark(new YMaps.GeoPoint(<?= $dealer->geoPointX ?>, <?= $dealer->geoPointY ?>),
                <?= \yii\helpers\Json::encode($dealer->name) ?>,
                <?= \yii\helpers\Json::encode($dealer->description) ?>,
                <?= \yii\helpers\Json::encode($dealer->address) ?>,
                <?= \yii\helpers\Json::encode($dealer->phone) ?>,
                <?= \yii\helpers\Json::encode($dealer->siteUrl) ?>,
                "",
                <?= \yii\helpers\Json::encode($dealer->tonarId) ?>,
                <?= \yii\helpers\Json::encode($dealer->city) ?>
            ));
        <?php } ?>

        map.addOverlay(group);
        map.addControl(new OfficeNavigator(group));
    });

    function createPlacemark (geoPoint, name, description, adress, phone, www, detailUrl, id, city) {
        var placemark = new YMaps.Placemark(geoPoint);
        placemark.name = name;
        placemark.description = description;
        placemark.adress = adress;
        placemark.phone = phone;
        placemark.www = www;
        placemark.detailUrl = detailUrl;
        placemark.id = id;
        placemark.city = city;

        return placemark;
    }

    function OfficeNavigator (offices) {
        this.onAddToMap = function (map, position) {
            this.container = YMaps.jQuery('<div class="dealers"><ul></ul></div>');
            this.map = map;

            this._generateList();
            this.container.appendTo("#YMapsID");
        }

        this.onRemoveFromMap = function () {
            this.container.remove();
            this.container = this.map = null;
        };

        this.isFlying = 0;

        this._generateList = function () {
            var that = this;

            // Group by city
            var groupItems = {};

            offices.forEach(function(obj) {
                groupItems[obj.city] = groupItems[obj.city] || [];
                groupItems[obj.city].push(obj);
            });

            // Render
            $.each(groupItems, function(city, items) {
                var cityContainer = $("<li><span></span><ol></ol></li>")
                    .appendTo(that.container.find('ul'));
                cityContainer.find('span').text(city);

                $.each(items, function(i, obj) {
                    var li = $('<li><a href="javascript:void(0)"></a></li>');
                    li.find('a').text(obj.name);
                    li.appendTo(cityContainer.find('ol'));

                    li.bind("click", function () {
                        if(!$(li).hasClass("first"))
                        {
                            if (!that.isFlying) {
                                that.isFlying = 1;
                                that.map.panTo(obj.getGeoPoint(), {
                                    flying: 1,
                                    callback: function () {
                                        if(that.map.getZoom()<10) that.map.setCenter(obj.getGeoPoint(),10);
                                        obj.openBalloon();
                                        that.isFlying = 0;
                                    }
                                });
                            }
                            //return false;
                        }
                    });

                    YMaps.Events.observe(obj, obj.Events.BalloonOpen, function () {
                        $(li).addClass("selected");
                        $("div.YMaps-b-balloon-content > b").remove();
                    });

                    YMaps.Events.observe(obj, obj.Events.BalloonClose, function () {
                        $(li).removeClass("selected");
                    });
                });
            });
        };
    }
</script>

<div class="service-map">
    <div id="YMapsID"></div>
</div>

