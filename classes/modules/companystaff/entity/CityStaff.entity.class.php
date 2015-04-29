<?php
class PluginCity_ModuleCityStaff_EntityCityStaff extends EntityORM {
    protected $aRelations = array(
        'user'=>array(EntityORM::RELATION_TYPE_BELONGS_TO,'ModuleUser_EntityUser','user_id'),
    );
}
?>