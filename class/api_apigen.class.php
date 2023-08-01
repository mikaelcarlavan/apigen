<?php
/* Copyright (C) 2015   Jean-François Ferry     <jfefe@aternatik.fr>
 * Copyright (C) 2020   Thibault FOUCART     	<support@ptibogxiv.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

use Luracast\Restler\RestException;

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/usergroup.class.php';

/**
 * API class for users
 *
 * @access protected
 * @class  DolibarrApiAccess {@requires user,external}
 */
class ApiGenApi extends DolibarrApi
{
    /**
     * @var array   $FIELDS     Mandatory fields, checked when create and update object
     */
    static $FIELDS = array();
    /**
     * @var User $user {@type User}
     */
    public $useraccount;

    /**
     * Constructor
     */
    public function __construct()
    {
        global $db, $conf;

        $this->db = $db;
        $this->useraccount = new User($this->db);
    }

    /**
     * Get properties of an user object
     *
     * @param 	int 	$id 					ID of user
     * @param	int		$includepermissions		Set this to 1 to have the array of permissions loaded (not done by default for performance purpose)
     * @return 	array|mixed 					data without useless information
     *
     * @throws RestException 401 Insufficient rights
     * @throws RestException 404 User or group not found
     */
    public function get($id, $includepermissions = 0)
    {
        if (empty(DolibarrApiAccess::$user->rights->user->user->lire) && empty(DolibarrApiAccess::$user->admin) && $id != 0 && DolibarrApiAccess::$user->id != $id) {
            throw new RestException(401, 'Not allowed');
        }

        if ($id == 0) {
            $result = $this->useraccount->initAsSpecimen();
        } else {
            $result = $this->useraccount->fetch($id);
        }
        if (!$result) {
            throw new RestException(404, 'User not found');
        }

        if ($id > 0 && !DolibarrApi::_checkAccessToResource('user', $this->useraccount->id, 'user')) {
            throw new RestException(401, 'Access not allowed for login ' . DolibarrApiAccess::$user->login);
        }

        return $this->useraccount->api_key;
    }
}
