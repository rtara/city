<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */

class PluginCity_ModuleContent extends Module {
	protected $oMapper;
	protected $oUserCurrent=null;

	/**
	 * Инициализация
	 */
	public function Init() {
		$this->oMapper=Engine::GetMapper(__CLASS__);
		$this->oUserCurrent = $this->User_GetUserCurrent();
		//$this->oMapper->SetUserCurrent($this->oUserCurrent);
	}

	/*
    * Загрузка лого компании
    */
	public function UploadLogo($aFile,$oCity) {
		if(!is_array($aFile) || !isset($aFile['tmp_name'])) {
			return false;
		}
		$sFileTmp=Config::Get('sys.cache.dir').func_generator();
		if (!move_uploaded_file($aFile['tmp_name'],$sFileTmp)) {
			return false;
		}
		$aParams = $this->Image_BuildParams('city_logo');
		$oImage=new LiveImage($sFileTmp);
		/**
		 * Если объект изображения не создан, возвращаем ошибку
		 */
		if($sError=$oImage->get_last_error()) {
			@unlink($sFileTmp);
			return false;
		}
		$oImage = $this->Image_CropSquare($oImage);
		$sPath = Config::Get('path.uploads.images').'/city/'.$oCity->getId();
		$aSize=Config::Get('module.city.logo_size');
		rsort($aSize,SORT_NUMERIC);
		$sSizeBig=array_shift($aSize);
		if ($oImage && $sFileLogo=$this->Image_Resize($sFileTmp,$sPath,"logo_city_{$oCity->getUrl()}_{$sSizeBig}x{$sSizeBig}",Config::Get('view.img_max_width'),Config::Get('view.img_max_height'),$sSizeBig,$sSizeBig,false,$aParams,$oImage)) {
			foreach ($aSize as $iSize) {
				if ($iSize==0) {
					$this->Image_Resize($sFileTmp,$sPath,"logo_city_{$oCity->getUrl()}",Config::Get('view.img_max_width'),Config::Get('view.img_max_height'),null,null,false,$aParams,$oImage);
				} else {
					$this->Image_Resize($sFileTmp,$sPath,"logo_city_{$oCity->getUrl()}_{$iSize}x{$iSize}",Config::Get('view.img_max_width'),Config::Get('view.img_max_height'),$iSize,$iSize,false,$aParams,$oImage);
				}
			}
			$oCity->setLogo(1);
			$aFileInfo=pathinfo($sFileLogo);
			$oCity->setLogoType($aFileInfo['extension']);
			@unlink($sFileTmp);
			return true;
		} else {
			@unlink($sFileTmp);
			$this->Message_AddError($this->Lang_Get('plugin.city.city_error_edit_load_logo'),$this->Lang_Get('error'));
			return false;
		}
	}

	/*
	 * Загрузка файла компании
	 */
	public function UploadFile($aFile,$oCity) {
		if(!is_array($aFile) || !isset($aFile['tmp_name'])) {
			return false;
		}
		$sFileTmp =  $aFile['tmp_name'];
		$sFileName = $aFile['name'];
		$sFileExtension = pathinfo($sFileName, PATHINFO_EXTENSION);
		//переводим название файла в транслит и добавляем расширение
		$sFileName = func_translit_url(pathinfo($sFileName, PATHINFO_FILENAME)).'.'.$sFileExtension;

		if(!in_array($sFileExtension, Config::Get('module.city.allow_file_ext'))) {
			$this->Message_AddError($this->Lang_Get('plugin.city.city_error_file_ext_not_allow'),$this->Lang_Get('error'));
			@unlink($sFileTmp);
			return false;
		}
		/**
		 * TODO: заменить на Image_SaveFile в версии 1.0
		 */
		$sDestDir = Config::Get('path.uploads.root').'/city/'.$oCity->getId().'/';
		$this->Image_CreateDirectory($sDestDir);
		$sFileFullPath=rtrim(Config::Get('path.root.server'),"/").'/'.trim($sDestDir,"/").'/'.$sFileName;
		if (move_uploaded_file($sFileTmp,$sFileFullPath)) {
			chmod($sFileFullPath,0666);
			$oCity->setFileName($sFileName);
			$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.city_notice_file_uploaded'),$this->Lang_Get('attention'));
		} else {
			$this->Message_AddError($this->Lang_Get('plugin.city.city_error_file_upload_error'),$this->Lang_Get('error'));
			@unlink($sFileTmp);
			return false;
		}
		@unlink($sFileTmp);
		return true;
	}

	public function DeleteLogo($oCity) {
		if($oCity->getLogo()) {
			$aSize=Config::Get('module.city.logo_size');
			foreach ($aSize as $iSize) {
				$this->Image_RemoveFile($this->Image_GetServerPath($oCity->getLogoPath($iSize)));
			}
		}
	}

	public function DeleteFile($oCity) {
		if($oCity->getFileName()) {
			$this->Image_RemoveFile($this->Image_GetServerPath($oCity->getFilePath()));
		}
	}

	public function DeleteBackground($oCity) {
		if($oCity->getBrandImage()) {
			$this->Image_RemoveFile($this->Image_GetServerPath($oCity->getBrandImage()));
		}
	}
	public function UploadBackground($aFile,$oCity) {
		if(!is_array($aFile) || !isset($aFile['tmp_name'])) {
			return false;
		}
		$sFileTmp=Config::Get('sys.cache.dir').func_generator();
		if (!move_uploaded_file($aFile['tmp_name'],$sFileTmp)) {
			return false;
		}
		$aParams = $this->Image_BuildParams('city_logo');
		$oImage= $this->Image_CreateImageObject($sFileTmp);

		/**
		 * Если объект изображения не создан, возвращаем ошибку
		 */
		if($sError=$oImage->get_last_error()) {
			@unlink($sFileTmp);
			return false;
		}
		$sPath = Config::Get('path.uploads.images').'/city/branding';
		$this->Image_CropProportion($oImage, 100, 100, true);
		if ($oImage && $sFileLogo=$this->Image_Resize($sFileTmp,$sPath,"{$oCity->getId()}_preview",Config::Get('view.img_max_width'),Config::Get('view.img_max_height'),100, 100,true,$aParams,$oImage)) {
			$sPath = $this->Image_SaveFile($sFileTmp,$sPath,"{$oCity->getId()}.".$oImage->get_image_params('format'),0644);
			$oCity->setBrandImage($this->Image_GetWebPath($sPath));
			@unlink($sFileTmp);
			return true;
		} else {
			@unlink($sFileTmp);
			$this->Message_AddError($this->Lang_Get('plugin.city.city_error_edit_load_branding_image'),$this->Lang_Get('error'));
			return null;
		}
	}



	/**
	 * Загрузка изображений при написании компании
	 *
	 * @param  array           $aFile	Массив $_FILES
	 * @param  ModuleUser_EntityUser $oUser	Объект пользователя
	 * @return string|bool
	 */
	public function UploadCityImageFile($aFile,$oUser) {
		if(!is_array($aFile) || !isset($aFile['tmp_name'])) {
			return false;
		}

		$sFileTmp=Config::Get('sys.cache.dir').func_generator();
		if (!move_uploaded_file($aFile['tmp_name'],$sFileTmp)) {
			return false;
		}
		$sDirUpload=$this->Image_GetIdDir($oUser->getId());
		$aParams=$this->Image_BuildParams('topic');

		if ($sFileImage=$this->Image_Resize($sFileTmp,$sDirUpload,func_generator(6),Config::Get('view.img_max_width'),Config::Get('view.img_max_height'),Config::Get('view.img_resize_width'),null,true,$aParams)) {
			@unlink($sFileTmp);
			return $this->Image_GetWebPath($sFileImage);
		}
		@unlink($sFileTmp);
		return false;
	}
	/**
	 * Загрузка изображений по переданному URL
	 *
	 * @param  string          $sUrl	URL изображения
	 * @param  ModuleUser_EntityUser $oUser
	 * @return string|int
	 */
	public function UploadCityImageUrl($sUrl, $oUser) {
		/**
		 * Проверяем, является ли файл изображением
		 */
		if(!@getimagesize($sUrl)) {
			return ModuleImage::UPLOAD_IMAGE_ERROR_TYPE;
		}
		/**
		 * Открываем файловый поток и считываем файл поблочно,
		 * контролируя максимальный размер изображения
		 */
		$oFile=fopen($sUrl,'r');
		if(!$oFile) {
			return ModuleImage::UPLOAD_IMAGE_ERROR_READ;
		}

		$iMaxSizeKb=Config::Get('view.img_max_size_url');
		$iSizeKb=0;
		$sContent='';
		while (!feof($oFile) and $iSizeKb<$iMaxSizeKb) {
			$sContent.=fread($oFile ,1024*1);
			$iSizeKb++;
		}
		/**
		 * Если конец файла не достигнут,
		 * значит файл имеет недопустимый размер
		 */
		if(!feof($oFile)) {
			return ModuleImage::UPLOAD_IMAGE_ERROR_SIZE;
		}
		fclose($oFile);
		/**
		 * Создаем tmp-файл, для временного хранения изображения
		 */
		$sFileTmp=Config::Get('sys.cache.dir').func_generator();

		$fp=fopen($sFileTmp,'w');
		fwrite($fp,$sContent);
		fclose($fp);

		$sDirSave=$this->Image_GetIdDir($oUser->getId());
		$aParams=$this->Image_BuildParams('topic');
		/**
		 * Передаем изображение на обработку
		 */
		if ($sFileImg=$this->Image_Resize($sFileTmp,$sDirSave,func_generator(),Config::Get('view.img_max_width'),Config::Get('view.img_max_height'),Config::Get('view.img_resize_width'),null,true,$aParams)) {
			@unlink($sFileTmp);
			return $this->Image_GetWebPath($sFileImg);
		}

		@unlink($sFileTmp);
		return ModuleImage::UPLOAD_IMAGE_ERROR;
	}
	/**
	 * Возвращает список фотографий к компании по списку id фоток
	 *
	 * @param array $aPhotoId	Список ID фото
	 * @return array
	 */
	public function GetCityPhotosByArrayId($aPhotoId) {
		if (!$aPhotoId) {
			return array();
		}
		if (!is_array($aPhotoId)) {
			$aPhotoId=array($aPhotoId);
		}
		$aPhotoId=array_unique($aPhotoId);
		$aPhotos=array();
		$s=join(',',$aPhotoId);
		if (false === ($data = $this->Cache_Get("city_photo_id_{$s}"))) {
			$data = $this->oMapper->GetCityPhotosByArrayId($aPhotoId);
			foreach ($data as $oPhoto) {
				$aPhotos[$oPhoto->getId()]=$oPhoto;
			}
			$this->Cache_Set($aPhotos, "city_photo_id_{$s}", array("city_photo_update"), 60*60*24*1);
			return $aPhotos;
		}
		return $data;
	}
	/**
	 * Добавить к компании изображение
	 *
	 * @param PluginCity_ModuleContent_EntityPhoto $oPhoto	Объект фото к компании
	 * @return PluginCity_ModuleContent_EntityPhoto|bool
	 */
	public function AddCityPhoto($oPhoto) {
		if ($sId=$this->oMapper->AddCityPhoto($oPhoto)) {
			$oPhoto->setId($sId);
			$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("city_photo_update"));
			return $oPhoto;
		}
		return false;
	}
	/**
	 * Получить изображение из фотосета по его id
	 *
	 * @param int $sId	ID фото
	 * @return ModuleCity_EntityCityPhoto|null
	 */
	public function GetCityPhotoById($sId) {
		$aPhotos=$this->GetCityPhotosByArrayId($sId);
		if (isset($aPhotos[$sId])) {
			return $aPhotos[$sId];
		}
		return null;
	}
	/**
	 * Получить список изображений из фотосета по id компании
	 *
	 * @param int $iCityId	ID компании
	 * @param int|null $iFromId	ID с которого начинать выборку
	 * @param int|null $iCount	Количество
	 * @return array
	 */
	public function GetPhotosByCityId($iCityId, $iFromId = null, $iCount = null) {
		return $this->oMapper->GetPhotosByCityId($iCityId, $iFromId, $iCount);
	}
	/**
	 * Получить список изображений из фотосета по временному коду
	 *
	 * @param string $sTargetTmp	Временный ключ
	 * @return array
	 */
	public function GetPhotosByTargetTmp($sTargetTmp) {
		return $this->oMapper->GetPhotosByTargetTmp($sTargetTmp);
	}
	/**
	 * Получить число изображений из фотосета по id компании
	 *
	 * @param int $iCityId	ID компании
	 * @return int
	 */
	public function GetCountPhotosByCityId($iCityId) {
		return $this->oMapper->GetCountPhotosByCityId($iCityId);
	}
	/**
	 * Получить число изображений из фотосета по id компании
	 *
	 * @param string $sTargetTmp	Временный ключ
	 * @return int
	 */
	public function GetCountPhotosByTargetTmp($sTargetTmp) {
		return $this->oMapper->GetCountPhotosByTargetTmp($sTargetTmp);
	}
	/**
	 * Обновить данные по изображению
	 *
	 * @param ModuleCity_EntityCityPhoto $oPhoto Объект фото
	 */
	public function UpdateCityPhoto($oPhoto) {
		$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("city_photo_update"));
		$this->oMapper->UpdateCityPhoto($oPhoto);
	}
	/**
	 * Удалить изображение
	 *
	 * @param ModuleCity_EntityCityPhoto $oPhoto	Объект фото
	 */
	public function DeleteCityPhoto($oPhoto) {
		$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("city_photo_update"));
		$this->oMapper->DeleteCityPhoto($oPhoto->getId());

		$this->Image_RemoveFile($this->Image_GetServerPath($oPhoto->getWebPath()));
		$aSizes=Config::Get('module.city.photo.size');
		// Удаляем все сгенерированные миниатюры основываясь на данных из конфига.
		foreach ($aSizes as $aSize) {
			$sSize = $aSize['w'];
			if ($aSize['crop']) {
				$sSize .= 'crop';
			}
			$this->Image_RemoveFile($this->Image_GetServerPath($oPhoto->getWebPath($sSize)));
		}
	}
	/**
	 * Загрузить изображение
	 *
	 * @param array $aFile	Массив $_FILES
	 * @return string|bool
	 */
	public function UploadCityPhoto($aFile) {
		if(!is_array($aFile) || !isset($aFile['tmp_name'])) {
			return false;
		}

		$sFileName = func_generator(10);
		$sPath = Config::Get('path.uploads.images').'/cityphoto/'.date('Y/m/d').'/';

		if (!is_dir(Config::Get('path.root.server').$sPath)) {
			mkdir(Config::Get('path.root.server').$sPath, 0755, true);
		}

		$sFileTmp = Config::Get('path.root.server').$sPath.$sFileName;
		if (!move_uploaded_file($aFile['tmp_name'],$sFileTmp)) {
			return false;
		}


		$aParams=$this->Image_BuildParams('photo');

		$oImage =$this->Image_CreateImageObject($sFileTmp);
		/**
		 * Если объект изображения не создан,
		 * возвращаем ошибку
		 */
		if($sError=$oImage->get_last_error()) {
			// Вывод сообщения об ошибки, произошедшей при создании объекта изображения
			$this->Message_AddError($sError,$this->Lang_Get('error'));
			@unlink($sFileTmp);
			return false;
		}
		/**
		 * Превышает максимальные размеры из конфига
		 */
		if (($oImage->get_image_params('width')>Config::Get('view.img_max_width')) or ($oImage->get_image_params('height')>Config::Get('view.img_max_height'))) {
			$this->Message_AddError($this->Lang_Get('plugin.city.city_photo_error_size'),$this->Lang_Get('error'));
			@unlink($sFileTmp);
			return false;
		}
		/**
		 * Добавляем к загруженному файлу расширение
		 */
		$sFile=$sFileTmp.'.'.$oImage->get_image_params('format');
		rename($sFileTmp,$sFile);

		$aSizes=Config::Get('module.city.photo.size');
		foreach ($aSizes as $aSize) {
			/**
			 * Для каждого указанного в конфиге размера генерируем картинку
			 */
			$sNewFileName = $sFileName.'_'.$aSize['w'];
			$oImage = $this->Image_CreateImageObject($sFile);
			if ($aSize['crop']) {
				$this->Image_CropProportion($oImage, $aSize['w'], $aSize['h'], true);
				$sNewFileName .= 'crop';
			}
			$this->Image_Resize($sFile,$sPath,$sNewFileName,Config::Get('view.img_max_width'),Config::Get('view.img_max_height'),$aSize['w'],$aSize['h'],true,$aParams,$oImage);
		}
		return $this->Image_GetWebPath($sFile);
	}

}