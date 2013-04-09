<?php


/**
 * ownCloud - files_whoshare
 *
 * @author Jorge Rafael García Ramos
 * @copyright 2012 Jorge Rafael García Ramos <kadukeitor@gmail.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
 

OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('files_whoshare');

$path = stripslashes($_GET['path']) ;

OCP\JSON::success(array('data' => array( 'user' => getOwner( $path ) , 'photo' => OCP\App::isEnabled('user_photo') )));

    #who shared (owner)
    function getOwner($target) {
        
        $target = cleanPath($target);
        $query = OCP\DB::prepare("SELECT uid_owner FROM *PREFIX*share WHERE file_target = ? AND share_with".getUsersAndGroups()." LIMIT 1");
        $result = $query->execute(array($target))->fetchAll();
        
        if (count($result) > 0) {
            return \OCP\User::getDisplayName($result[0]['uid_owner']);
        }
        
        $target = dirname($target);
        $result = array();
        
        while ($target != "" && $target != "/" && $target != "." && $target != "\\") {
            $result = $query->execute(array($target))->fetchAll();
            if (count($result) > 0) {
                break;
            }
            $target = dirname($target);
        }
        
        if (count($result) > 0) {
            return \OCP\User::getDisplayName($result[0]['uid_owner']);
        } else {
            return false;
        }
    }
    
    #clean path
    function cleanPath($path) {
    
        $path = rtrim($path, "/");
        return preg_replace('{(/)\1+}', "/", $path);
    }

    #user and groups current user
    function getUsersAndGroups($uid = null) {
    
        $in = " IN(";
        $uid = OCP\USER::getUser();
        $in .= "'".$uid."'";
        $groups = OC_Group::getUserGroups($uid);
        foreach ($groups as $group) {
            $in .= ", '".$group."'";
        }
        $in .= ")";
        return $in;
    }
