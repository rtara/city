<?php
class PluginCity_ModuleContent_EntityPhoto extends Entity {

	/**
	 * Возвращает ID фото
	 *
	 * @return int|null
	 */
	public function getId() {
		return $this->_getDataOne('id');
	}
	/**
	 * Возвращает ID компании
	 *
	 * @return int|null
	 */
	public function getCityId() {
		return $this->_getDataOne('city_id');
	}
	/**
	 * Возвращает ключ временного владельца
	 *
	 * @return string|null
	 */
	public function getTargetTmp() {
		return $this->_getDataOne('target_tmp');
	}
	/**
	 * Возвращает описание фото
	 *
	 * @return string|null
	 */
	public function getDescription() {
		return $this->_getDataOne('description');
	}
	/**
	 * Вовзращает полный веб путь до фото
	 *
	 * @return mixed|null
	 */
	public function getPath() {
		return $this->_getDataOne('path');
	}
	/**
	 * Возвращает полный веб путь до фото определенного размера
	 *
	 * @param string|null $sWidth	Размер фото, например, '100' или '150crop'
	 * @return null|string
	 */
	public function getWebPath($sWidth = null) {
		if ($this->getPath()) {
			if ($sWidth) {
				$aPathInfo=pathinfo($this->getPath());
				return $aPathInfo['dirname'].'/'.$aPathInfo['filename'].'_'.$sWidth.'.'.$aPathInfo['extension'];
			} else {
				return $this->getPath();
			}
		} else {
			return null;
		}
	}

	/**
	 * Устанавливает ID компании
	 *
	 * @param int $iCityId
	 */
	public function setCityId($iCityId) {
		$this->_aData['city_id'] = $iCityId;
	}
	/**
	 * Устанавливает ключ временного владельца
	 *
	 * @param string $sTargetTmp
	 */
	public function setTargetTmp($sTargetTmp) {
		$this->_aData['target_tmp'] = $sTargetTmp;
	}
	/**
	 * Устанавливает описание фото
	 *
	 * @param string $sDescription
	 */
	public function setDescription($sDescription) {
		$this->_aData['description'] = $sDescription;
	}
}