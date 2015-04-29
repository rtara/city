<?php
class PluginCity_ModulePay_EntityTariff extends Entity {

	/**
	 * Возвращает ID
	 *
	 * @return int|null
	 */
	public function getId() {
		return $this->_getDataOne('id');
	}
	/**
	 * Возвращает заголовок
	 *
	 * @return int|null
	 */
	public function getTitle() {
		return $this->_getDataOne('title');
	}
	/**
	 * Возвращает права
	 *
	 * @return string|null
	 */
	public function getRights() {
		return $this->_getDataOne('rights');
	}
	/**
	 * Возвращает описание
	 *
	 * @return string|null
	 */
	public function getDescription() {
		return $this->_getDataOne('description');
	}

}