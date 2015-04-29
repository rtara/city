<div>

    <script src="http://api-maps.yandex.ru/2.0/?ns=ym&load=package.standard&lang=ru-RU" type="text/javascript"></script>
    <script type="text/javascript">
        ym.ready(init);
        function init () {
		{if $oCity->getLongitude() != ''}
            var centerMap = [{$oCity->getLatitude()},{$oCity->getLongitude()}];
			{else}
            var centerMap =  {$oConfig->Get('module.city.map.center')};
		{/if}
            var myMap, myPlacemark;
            var optionsMark = {	iconContent: "{$oCity->getName()|escape:'html'} {$aLang.plugin.city.city_edit_map_place}",
                balloonContentHeader: "<b>{$oCity->getName()|escape:'html'} {$aLang.plugin.city.city_edit_map_place}</b>",
                balloonContentBody: "{$aLang.plugin.city.city_edit_map_place_note}",
                balloonContentFooter: "<sup>{$aLang.plugin.city.city_edit_map_click_note}</sup>"
            };
            var iconMark = 	{	preset: 'twirl#blueStretchyIcon' };

            var layerOSM = function() {
                return new ym.Layer('http://otile%d.mqcdn.com/tiles/1.0.0/osm/%z/%x/%y.png', {
                    projection: ym.projection.sphericalMercator
                } );
            };
            var osMap = new ym.MapType('OSMap', [layerOSM]);
            ym.mapType.storage.add('openstreet#map', osMap);

            var layerGoogle = function() {
                return new ym.Layer('http://mt0.google.com/vt/lyrs=m@176000000&hl=ru&%c', {
                    projection: ym.projection.sphericalMercator
                });
            };
            var googleMap = new ym.MapType('GoogleMap', [layerGoogle]);
            ym.mapType.storage.add('google#map', googleMap);

            var layerMail = function() {
                return new ym.Layer('http://t0maps.mail.ru/tiles/scheme/%z/%y/%x.png', {
                    projection: ym.projection.sphericalMercator
                });
            };
            var mailMap = new ym.MapType('MailMap', [layerMail]);
            ym.mapType.storage.add('mail#map', mailMap);

            myMap = new ym.Map("YmapsCity", { center: centerMap, zoom: {$oConfig->Get('module.city.map.edit_zoom')}, type: "{$oConfig->Get('module.city.map.type')}" } );
		{if {$oConfig->Get('module.city.map.scroll')}}
            myMap.behaviors.enable("scrollZoom");
		{/if}
            var yandexPubSelectorItem = new ym.control.ListBoxItem( {
                data: {
                    content: "Яндекс.Народная"
                }
            } );
            var yandexSelectorItem = new ym.control.ListBoxItem( {
                data: {
                    content: "Яндекс.Схема"
                }
            } );
            var osmSelectorItem = new ym.control.ListBoxItem( {
                data: {

                    content: "Open Street Map"
                }
            } );
            var googleSelectorItem = new ym.control.ListBoxItem({
                data: {
                    content: "Google.Схема"
                }
            });
            var mailSelectorItem = new ym.control.ListBoxItem({
                data: {
                    content: "Mail.Схема"
                }
            });

            var typeSelector = new ym.control.ListBox( {
                data: {
                    title: "Показать"
                },
                items: [yandexPubSelectorItem, yandexSelectorItem, osmSelectorItem, mailSelectorItem, googleSelectorItem]
            } );

            osmSelectorItem.events.add("click", function(e) {
                this.setType("openstreet#map");
                typeSelector.setTitle('Open Street Map');
            }, myMap);
            yandexSelectorItem.events.add("click", function(e) {
                this.setType("yandex#map");
                typeSelector.setTitle('Яндекс.Схема');
            }, myMap);
            yandexPubSelectorItem.events.add("click", function(e) {
                this.setType('yandex#publicMap');
                typeSelector.setTitle('Яндекс.Народная');
            }, myMap);
            googleSelectorItem.events.add("click", function(e) {
                this.setType("google#map");
                typeSelector.setTitle('Google.Схема');
            }, myMap);
            mailSelectorItem.events.add("click", function(e) {
                this.setType("mail#map");
                typeSelector.setTitle('Mail.Схема');
            }, myMap);

            myMap.controls.add(typeSelector, {
                right: 5,
                top: 5
            });

            myMap.controls.add('zoomControl', { top: 30, left: 5 }); //.add("typeSelector")


		{if $oCity->getLongitude() != ''}
            myPlacemark = new ym.Placemark([centerMap], optionsMark, iconMark);
            myMap.geoObjects.add(myPlacemark);

			{elseif $oConfig->GetValue('module.city.map.geocode')}
            ym.geocode('{$oCity->getCity()|escape:'html'} {$oCity->getAddress()|escape:'html'}', { results: 1 } ).then(function (res) {
                myPlacemark = res.geoObjects;
                var coords = myPlacemark.get(0).geometry.getCoordinates();
                myMap.geoObjects.add(myPlacemark);
                myMap.setCenter(coords,13);
                $("#map_marker_longitude").attr("value", coords[1].toPrecision(8));
                $("#map_marker_latitude").attr("value", coords[0].toPrecision(8));
            });
		{/if}
            myMap.events.add('click', function (e) {
                var coords = e.get('coordPosition');
                if (myPlacemark)
                    myMap.geoObjects.remove(myPlacemark);
                myPlacemark = new ym.Placemark(coords, optionsMark, iconMark);
                myMap.geoObjects.add(myPlacemark);
                $("#map_marker_longitude").attr("value", coords[1].toPrecision(8));
                $("#map_marker_latitude").attr("value", coords[0].toPrecision(8));
            });

            $('#delete-mark').bind({
                click: function () {
                    if (myPlacemark)
                        myMap.geoObjects.remove(myPlacemark);
                    $("#map_marker_longitude").attr("value", '');
                    $("#map_marker_latitude").attr("value", '');
                    return false;
                }
            });

            $('#find-place').bind({
                click: function () {
                    var search_query = '{$oCity->getCity()|escape:'html'} {$oCity->getAddress()|escape:'html'}';
                    ym.geocode(search_query, { results: 1 } ).then(function (res) {
                        if (myPlacemark)
                            myMap.geoObjects.remove(myPlacemark);
                        myPlacemark = res.geoObjects;
                        var coords = myPlacemark.get(0).geometry.getCoordinates();
                        myMap.geoObjects.add(myPlacemark);
                        myMap.setCenter(coords,13);
                        $("#map_marker_longitude").attr("value", coords[1].toPrecision(8));
                        $("#map_marker_latitude").attr("value", coords[0].toPrecision(8));
                    });
                    return false;
                }
            });

        }
    </script>
    <p>
        <a href="#" id='find-place' class="dotted">{$aLang.plugin.city.city_edit_map_find_place}: {$oCity->getCity()} {$oCity->getAddress()}</a>
        <a href="#" id='delete-mark' class="dotted" style="float: right">{$aLang.plugin.city.city_edit_map_delete_mark}</a>
    <div id="YmapsCity" class="map"></div>
    </p>
    <input type="hidden" id="map_marker_latitude" name="map_marker_latitude" value="{$oCity->getLatitude()|escape:'html'}" />
    <input type="hidden" id="map_marker_longitude" name="map_marker_longitude" value="{$oCity->getLongitude()|escape:'html'}" />

</div>