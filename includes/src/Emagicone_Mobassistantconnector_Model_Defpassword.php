<?php
/**
 *    This file is part of Mobile Assistant Connector.
 *
 *   Mobile Assistant Connector is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   Mobile Assistant Connector is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with Mobile Assistant Connector.  If not, see <http://www.gnu.org/licenses/>.
 */

class Emagicone_Mobassistantconnector_Model_Defpassword extends Mage_Core_Model_Config_Data
{
    public function toOptionArray()
    {
		Mage::app()->cleanCache();
        $password = Mage::getStoreConfig('mobassistantconnectorinfosec/emoaccess/password');
        if($password === 'c4ca4238a0b923820dcc509a6f75849b') {
            Mage::getSingleton('core/session')->addNotice(Mage::helper('mobassistantconnector/data')->__('<span style="color:green">Mobile Assistant Connector: </span> Default password is "1". Please change it because of security reasons!'));
        }
    }
}