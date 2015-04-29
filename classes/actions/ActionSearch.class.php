<?php
/**
 * Поиск по контенту
 *
 */
class PluginCity_ActionSearch extends PluginCity_Inherit_ActionSearch {
	protected $sTypesEnabled = array('topics' => array('topic_publish' => 1), 'comments' => array('comment_delete' => 0),'cityes' => array('city_active' => 0));
	
	protected function RegisterEvent() {
		parent::RegisterEvent();
		$this->AddEvent('cityes','EventCityes');
	}

	/**
	 * Поиск компаний
	 *
	 * @return unknown
	 */
	function EventCityes(){
		/**
		 * Ищем
		 */
		$aReq = $this->PrepareRequest();
		$aRes = $this->PrepareResults($aReq, Config::Get('module.comment.per_page'));
		if(FALSE === $aRes) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'));
			return Router::Action('error');
		}
		/**
		 * Если поиск дал результаты
		 */
		if($this->bIsResults){
			/**
			 *  Получаем топик-объекты по списку идентификаторов
			 */
			$aCityes = $this->PluginCity_City_GetCityesAdditionalData(array_keys($this->aSphinxRes['matches']));
			/**
			 * Конфигурируем парсер jevix
			 */
			$this->Text_LoadJevixConfig('search');
			/**
			 * Делаем сниппеты
			 */
			foreach($aCityes AS $oCity){
				$oCity->setDescription($this->Text_JevixParser($this->Sphinx_GetSnippet(
						$oCity->getDescription(),
						'cityes',
						$aReq['q'],
						'<span class="searched-item">',
						'</span>'
				)));
			}
			/**
			 *  Отправляем данные в шаблон
			 */
			$this->Viewer_Assign('aRes', $aRes);
			$this->Viewer_Assign('aCityes', $aCityes);
		}
	}
	/**
	 * Подготовка запроса на поиск
	 *
	 * @return unknown
	 */
	private function PrepareRequest(){
		$aReq['q'] = getRequest('q');
		if (!func_check($aReq['q'],'text', 2, 255)) {
			/**
			 *  Если запрос слишком короткий перенаправляем на начальную страницу поиска
			 * Хотя тут лучше показывать юзеру в чем он виноват
			 */
			Router::Location(Router::GetPath('search'));
		}
		$aReq['sType'] = strtolower(Router::GetActionEvent());		
		/**
		 * Определяем текущую страницу вывода результата
		 */
		$aReq['iPage'] = intval(preg_replace('#^page(\d+)$#', '\1', $this->getParam(0)));
		if(!$aReq['iPage']) { $aReq['iPage'] = 1; }		
		/**
		 *  Передача данных в шаблонизатор 
		 */
		$this->Viewer_Assign('aReq', $aReq);		
		return $aReq;
	}
}


?>