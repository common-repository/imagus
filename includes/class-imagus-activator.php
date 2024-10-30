<?php

/**
 * Class Imagus_Activator
 */
class Imagus_Activator {

	/**
	 * Activates the plugin. Also, creates the images backup directory if not exists.
	 */
	public static function activate():void {
		$upload = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		$upload_dir = $upload_dir . '/'.IMAGUS_BACKUP_FOLDER;
		if (! is_dir($upload_dir)) {
				$dir_created = mkdir( $upload_dir, 0700 );
				update_option( IMAGUS_BACKUP_FOLDER, $dir_created );
		}
	}

}
