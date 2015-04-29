<?php
/**
 * OAuth
 *
 */
class PluginCity_ModuleOauth extends Module {
	/**
	 * Инициализация модуля
	 *
	 */
	public function Init() {
        require_once(Plugin::GetPath(__CLASS__).'lib/external/OAuth/TwitterAPIExchange.php');

	}

    public function  CheckTwitterName($ScreenName){
        //если не заполнены настройки ничего не проверяем
        if (Config::Get('module.city.twitter.consumer_secret') == '')
            return false;
        $settings = array(
            'oauth_access_token' => Config::Get('module.city.twitter.oauth_access_token'),
            'oauth_access_token_secret' => Config::Get('module.city.twitter.oauth_access_token_secret'),
            'consumer_key' => Config::Get('module.city.twitter.consumer_key'),
            'consumer_secret' => Config::Get('module.city.twitter.consumer_secret')
        );

        $apiUrl = "https://api.twitter.com/1.1/statuses/user_timeline.json";
        $requestMethod = 'GET';
        $getField = '?screen_name=' . $ScreenName . '&count=1';
        $twitter = new TwitterAPIExchange($settings);

        $response = $twitter->setGetfield($getField)->buildOauth($apiUrl, $requestMethod)->performRequest();

        $followers = json_decode($response,true);
        if (!isset($followers['errors']))
            return true;
        else
            return false;

    }
}
?>