<?php
	/**
	 *	Module "City"
	 *	Author: Grebenkin Anton
	 *	Contact e-mail: 4freework@gmail.com
	 */

class PluginCity_ModuleContent_MapperContent extends Mapper {
	/**
	 * Возвращает список фотографий к компании по списку id фоток
	 *
	 * @param array $aPhotoId	Список ID фото
	 * @return array
	 */
	public function GetCityPhotosByArrayId($aArrayId) {
		if (!is_array($aArrayId) or count($aArrayId)==0) {
			return array();
		}
		$sql = "SELECT
					*
				FROM
					".Config::Get('db.table.city_photo')."
				WHERE
					id IN(?a)
				ORDER BY FIELD(id,?a) ";
		$aReturn=array();
		if ($aRows=$this->oDb->select($sql,$aArrayId,$aArrayId)) {
			foreach ($aRows as $aPhoto) {
				$aReturn[]=Engine::GetEntity('PluginCity_ModuleContent_EntityPhoto',$aPhoto);
			}
		}
		return $aReturn;
	}
	/**
	 * Получить список изображений из фотосета по id компании
	 *
	 * @param int $iCityId	ID топика
	 * @param int|null $iFromId	ID с которого начинать выборку
	 * @param int|null $iCount	Количество
	 * @return array
	 */
	public function GetPhotosByCityId($iCityId, $iFromId, $iCount) {
		$sql = 'SELECT * FROM ' . Config::Get('db.table.city_photo') . ' WHERE city_id = ?d {AND id > ?d LIMIT 0, ?d}';
		$aPhotos = $this->oDb->select($sql, $iCityId, ($iFromId !== null) ? $iFromId : DBSIMPLE_SKIP, $iCount);
		$aReturn = array();
		if (is_array($aPhotos) && count($aPhotos)) {
			foreach($aPhotos as $aPhoto) {
				$aReturn[] = Engine::GetEntity('PluginCity_ModuleContent_EntityPhoto', $aPhoto);
			}
		}
		return $aReturn;
	}
	/**
	 * Получить список изображений из фотосета по временному коду
	 *
	 * @param string $sTargetTmp	Временный ключ
	 * @return array
	 */
	public function GetPhotosByTargetTmp($sTargetTmp) {
		$sql = 'SELECT * FROM ' . Config::Get('db.table.city_photo') . ' WHERE target_tmp = ?';
		$aPhotos = $this->oDb->select($sql, $sTargetTmp);
		$aReturn = array();
		if (is_array($aPhotos) && count($aPhotos)) {
			foreach($aPhotos as $aPhoto) {
				$aReturn[] = Engine::GetEntity('PluginCity_ModuleContent_EntityPhoto', $aPhoto);
			}
		}
		return $aReturn;
	}
	/**
	 * Получить изображение из фотосета по его id
	 *
	 * @param int $iPhotoId	ID фото
	 * @return PluginCity_ModuleContent_EntityPhoto|null
	 */
	public function GetCityPhotoById($iPhotoId) {
		$sql = 'SELECT * FROM ' . Config::Get('db.table.city_photo') . ' WHERE id = ?d';
		$aPhoto = $this->oDb->selectRow($sql, $iPhotoId);
		if ($aPhoto) {
			return Engine::GetEntity('PluginCity_ModuleContent_EntityPhoto', $aPhoto);
		} else {
			return null;
		}
	}
	/**
	 * Получить число изображений из фотосета по id компании
	 *
	 * @param int $iCityId	ID топика
	 * @return int
	 */
	public function GetCountPhotosByCityId($iCityId) {
		$sql = 'SELECT count(id) FROM ' . Config::Get('db.table.city_photo') . ' WHERE city_id = ?d';
		$aPhotosCount = $this->oDb->selectCol($sql, $iCityId);
		return $aPhotosCount[0];
	}
	/**
	 * Получить число изображений из фотосета по id компании
	 *
	 * @param string $sTargetTmp	Временный ключ
	 * @return int
	 */
	public function GetCountPhotosByTargetTmp($sTargetTmp) {
		$sql = 'SELECT count(id) FROM ' . Config::Get('db.table.city_photo') . ' WHERE target_tmp = ?';
		$aPhotosCount = $this->oDb->selectCol($sql, $sTargetTmp);
		return $aPhotosCount[0];
	}
	/**
	 * Добавить к топику изображение
	 *
	 * @param PluginCity_ModuleContent_EntityPhoto $oPhoto	Объект фото к компании
	 * @return bool
	 */
	public function AddCityPhoto($oPhoto) {
		if (!$oPhoto->getCityId() && !$oPhoto->getTargetTmp()) return false;
		$sTargetType = ($oPhoto->getCityId()) ? 'city_id' : 'target_tmp';
		$iTargetId = ($sTargetType == 'city_id') ? $oPhoto->getCityId() : $oPhoto->getTargetTmp();
		$sql = 'INSERT INTO '. Config::Get('db.table.city_photo') . ' SET
                        path = ?, description = ?, ?# = ?';
		return $this->oDb->query($sql, $oPhoto->getPath(), $oPhoto->getDescription(), $sTargetType, $iTargetId);
	}
	/**
	 * Обновить данные по изображению
	 *
	 * @param PluginCity_ModuleContent_EntityPhoto $oPhoto Объект фото
	 */
	public function UpdateCityPhoto($oPhoto) {
		if (!$oPhoto->getCityId() && !$oPhoto->getTargetTmp()) return false;
		if ($oPhoto->getCityId()) {
			$oPhoto->setTargetTmp = null;
		}
		$sql = 'UPDATE '. Config::Get('db.table.city_photo') . ' SET
                        path = ?, description = ?, city_id = ?d, target_tmp=? WHERE id = ?d';
		$this->oDb->query($sql, $oPhoto->getPath(), $oPhoto->getDescription(), $oPhoto->getCityId(), $oPhoto->getTargetTmp(), $oPhoto->getId());
	}
	/**
	 * Удалить изображение
	 *
	 * @param int $iPhotoId	ID фото
	 */
	public function DeleteCityPhoto($iPhotoId) {
		$sql = 'DELETE FROM '. Config::Get('db.table.city_photo') . ' WHERE
                        id= ?d';
		$this->oDb->query($sql, $iPhotoId);
	}
}