<?php
/*

 Copyright (c) 2001 - 2007 Ampache.org
 All rights reserved.

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License v2
 as published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

*/

/**
 * Sub-Ajax page, requires AJAX_INCLUDE as one
 */
if (AJAX_INCLUDE != '1') { exit; } 

switch ($_REQUEST['action']) { 
	case 'set_instance': 
		// Make sure they they are allowed to do this
		//... ok I don't really know what that means yet

		$type = $_REQUEST['instance'] ? 'localplay' : 'stream';

		$localplay = new Localplay($GLOBALS['user']->prefs['localplay_controller']); 
		$localplay->set_active_instance($_REQUEST['instance']); 
		Preference::update('play_type',$GLOBALS['user']->id,$type); 

		// Now reload the preferences into the user object
		$GLOBALS['user']->set_preferences(); 

		// We should also refesh the sidebar
		ob_start(); 
		require_once Config::get('prefix') . '/templates/sidebar.inc.php'; 
		$results['sidebar'] = ob_get_contents(); 
		ob_end_clean(); 
	break;
	case 'command': 
		// Make sure they are allowed to do this
		// ok I still don't know what that means... but I'm thinking about it

		$localplay = new Localplay($GLOBALS['user']->prefs['localplay_controller']); 
		$localplay->connect(); 
		
		// Switch on valid commands
		switch ($_REQUEST['command']) { 
			case 'prev': 
			case 'next': 
			case 'stop': 
			case 'play': 
			case 'pause': 
				$command = scrub_in($_REQUEST['command']); 
				$localplay->$command(); 
			break;
			default: 
				// Nothing
			break; 
		} // end whitelist

	break; 
	case 'delete_instance': 
		// Make sure that you have access to do this... again I really 
		// don't know what that means so I'm just going to do nothing fo now
		

		// Scrub it in
		$localplay = new Localplay($GLOBALS['user']->prefs['localplay_controller']); 
		$localplay->delete_instance($_REQUEST['instance']); 
		
		$key = 'localplay_instance_' . $_REQUEST['instance']; 
		$results[$key] = ''; 
	break; 
	default: 
		$results['rfc3514'] = '0x1'; 
	break;
} // switch on action; 

// We always do this
echo xml_from_array($results); 
?>
