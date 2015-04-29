
<div>
{if $oCity->getLongitude() != ''}
	{assign var="centerMap" value="{$oCity->getLatitude()},{$oCity->getLongitude()}"}
{else}
	{assign var="centerMap" value="{$oConfig->Get('module.city.map.center')}"}
{/if}

<script src="http://api-maps.yandex.ru/2.0/?ns=ym&load=package.standard&lang=ru-RU" type="text/javascript"></script>
<script type="text/javascript">//<![CDATA[
ym.ready(init);
function init () {
	var myMap;
	$('#toggle').bind({
	click: function () {
		$('#YMapsCity').removeClass('hidden');
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

		if (!myMap) {
			myMap = new ym.Map("YMapsCity", { center: [{$centerMap}], zoom: {$oConfig->Get('module.city.map.view_zoom')} , type: "{$oConfig->Get('module.city.map.type')}" } );

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

		myMap.controls.add('smallZoomControl', { top: 30, left: 5 }); //.add("typeSelector")

		{if ($oCity->getLongitude() != '')}
			var myPlacemark = new ym.Placemark([{$centerMap}],
					{	iconContent: "{$oCity->getName()|escape:'html'}",
	                    balloonContentHeader: "{$oCity->getName()|escape:'html'}",
						balloonContentBody: "{if $oCity->getCountry()}{$oCity->getCountry()|escape:'html'}{/if}
											{if $oCity->getCity()}, {$oCity->getCity()|escape:'html'}{/if}
											{if $oCity->getAddress()}, {$oCity->getAddress()|escape:'html'}{/if}"
					},
					{	preset: 'twirl#blueStretchyIcon' }
			)
			myMap.geoObjects.add(myPlacemark);
		{/if}
		$("#toggle").text('{$aLang.plugin.city.city_info_hide_map}');
		} else {
			myMap.destroy();
			myMap = null;
			$('#YMapsCity').addClass('hidden');
			$("#toggle").text('{$aLang.plugin.city.city_info_show_map_again}');
		}
return false;
	}
	});
} //]]>
</script>


</div>