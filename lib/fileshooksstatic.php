<?php

/**
 * ownCloud - Activity App
 *
 * @author Joas Schilling
 * @copyright 2014 Joas Schilling nickvergessen@owncloud.com
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
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\Activity;

use OCP\Util;

/**
 * The class to handle the filesystem hooks
 */
class FilesHooksStatic {
	/** @var FilesHooks */
	static protected $instance;

	/**
	 * Registers the filesystem hooks for basic filesystem operations.
	 * All other events has to be triggered by the apps.
	 */
	public static function register() {
		Util::connectHook('OC_Filesystem', 'post_create', 'OCA\Activity\FilesHooksStatic', 'fileCreate');
		Util::connectHook('OC_Filesystem', 'post_update', 'OCA\Activity\FilesHooksStatic', 'fileUpdate');
		Util::connectHook('OC_Filesystem', 'delete', 'OCA\Activity\FilesHooksStatic', 'fileDelete');
		Util::connectHook('OC_Filesystem', 'rename', 'OCA\Activity\FilesHooksStatic', 'fileRenameBefore');
		Util::connectHook('OC_Filesystem', 'post_rename', 'OCA\Activity\FilesHooksStatic', 'fileRename');
		Util::connectHook('\OCA\Files_Trashbin\Trashbin', 'post_restore', 'OCA\Activity\FilesHooksStatic', 'fileRestore');
		Util::connectHook('OCP\Share', 'post_shared', 'OCA\Activity\FilesHooksStatic', 'share');

		$eventDispatcher = \OC::$server->getEventDispatcher();
		$eventDispatcher->addListener('OCA\Files::loadAdditionalScripts', ['OCA\Activity\FilesHooksStatic', 'onLoadFilesAppScripts']);
	}

	/**
	 * @return FilesHooks
	 */
	static protected function getHooks() {
		if (self::$instance === null) {
			$app = new AppInfo\Application();
			self::$instance = $app->getContainer()->query('Hooks');
		}
		return self::$instance;

	}

	/**
	 * Store the create hook events
	 * @param array $params The hook params
	 */
	public static function fileCreate($params) {
		self::getHooks()->fileCreate($params['path']);
	}

	/**
	 * Store the update hook events
	 * @param array $params The hook params
	 */
	public static function fileUpdate($params) {
		self::getHooks()->fileUpdate($params['path']);
	}

	/**
	 * Store the delete hook events
	 * @param array $params The hook params
	 */
	public static function fileDelete($params) {
		self::getHooks()->fileDelete($params['path']);
	}

	/**
	 * Store the restore hook events
	 * @param array $params The hook params
	 */
	public static function fileRestore($params) {
		self::getHooks()->fileRestore($params['filePath']);
	}

	/**
	 * Store the rename hook events
	 * @param array $params The hook params
	 */
	public static function fileRenameBefore($params) {
		self::getHooks()->fileRenameBefore($params['oldpath'], $params['newpath']);
	}

	/**
	 * Store the rename hook events
	 * @param array $params The hook params
	 */
	public static function fileRename($params) {
		self::getHooks()->fileRename($params['oldpath'], $params['newpath']);
	}

	/**
	 * Manage sharing events
	 * @param array $params The hook params
	 */
	public static function share($params) {
		self::getHooks()->share($params);
	}

	/**
	 * Load additional scripts when the files app is visible
	 */
	public static function onLoadFilesAppScripts() {
		Util::addStyle('activity', 'style');
		Util::addScript('activity', 'formatter');
		Util::addScript('activity', 'activitymodel');
		Util::addScript('activity', 'activitycollection');
		Util::addScript('activity', 'activitytabview');
		Util::addScript('activity', 'filesplugin');
	}
}
