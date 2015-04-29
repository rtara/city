<?php

class PluginCity_ModuleTopic_EntityTopic extends PluginCity_Inherit_ModuleTopic_EntityTopic {
    public function getUrl() { 
    	if ($this->getBlog()->getType()=='personal') {
    		return Router::GetPath('blog').$this->getId().'.html';
    	} else if ($this->getBlog()->getType()=='city') {
    		return Router::GetPath('city').$this->getBlog()->getUrl().'/blog/'.$this->getId().'.html'; //вырезаем city_
    	} else {
    		return Router::GetPath('blog').$this->getBlog()->getUrl().'/'.$this->getId().'.html';
    	}
    }
    
	public function setBlogType($data) {
        $this->_aData['blog_type']=$data;
    }
	public function setBlogUrl($data) {
        $this->_aData['blog_url']=$data;
    }
}
?>