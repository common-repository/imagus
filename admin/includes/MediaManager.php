<?php


namespace Admin\Includes;


class MediaManager {

	private $backup_image;
	private $original_image;
	private $original_image_id;

	public function __construct( $image_id ) {
		$this->original_image_id = $image_id;
	}

	public function create_backup( string $image ) {
		$this->backup_image   = $image . '_' . IMAGUS_PREFIX . md5( $image );
		$this->original_image = $image;

		return copy( $image, $this->backup_image );
	}

	public function is_optimized() {
		if ( is_null( $this->backup_image ) ) {
			return null;
		}

		return filesize( $this->backup_image ) < filesize( $this->original_image );
	}

	public function attach_optimized_image( $settings ) {
		if ( $settings['original_local_copy'] && true == get_option(IMAGUS_BACKUP_FOLDER) ) {
			$this->save_original_image( $settings['id'] );
		}
		copy( $this->backup_image, $this->original_image );
	}

	public function save_original_image( int $image_id ) {
		$file_info                    = pathinfo( $this->original_image );
		$upload_dir = $this->get_local_copy_folder();
		$filename = $file_info['filename'] . '_imagus_copy_' . time() . '.' . $file_info['extension'];
		$tmp = $upload_dir.'/'.$filename;
		copy( $this->original_image, $tmp );
		update_post_meta($image_id, IMAGUS_HAS_COPY, $filename);
	}

	public function get_backup() {
		return $this->backup_image;
	}

	public function delete_compression_meta(){
		$metas = [
			'imagus_has_copy',
			'imagus_optimized',
			'imagus_bytes_saved',
			'imagus_human_bytes_saved',
			'imagus_bytes_percentage'
		];
		$this->delete_local_copy();

		foreach( $metas as $meta){
			delete_post_meta( $this->original_image_id, $meta);
		}

	}

	public function get_saved_bytes() {
		$backup_size   = filesize( $this->backup_image );
		$original_size = filesize( $this->original_image );

		if ( $backup_size >= $original_size ) {
			return [
				'bytes'       => 0,
				'human_bytes' => 0,
				'percentage'  => 0
			];
		}

		return [
			'bytes'       => $original_size - $backup_size,
			'human_bytes' => $this->formatSizeUnits( $original_size - $backup_size ),
			'percentage'  => round( ( 1 - ( $backup_size / $original_size ) ) * 100, 2 )
		];
	}

	private function formatSizeUnits( $bytes ) {
		if ( $bytes >= 1073741824 ) {
			$bytes = number_format( $bytes / 1073741824, 2 ) . ' GB';
		} elseif ( $bytes >= 1048576 ) {
			$bytes = number_format( $bytes / 1048576, 2 ) . ' MB';
		} elseif ( $bytes >= 1024 ) {
			$bytes = number_format( $bytes / 1024, 2 ) . ' KB';
		} elseif ( $bytes > 1 ) {
			$bytes = $bytes . ' bytes';
		} elseif ( $bytes == 1 ) {
			$bytes = $bytes . ' byte';
		} else {
			$bytes = '0 bytes';
		}

		return $bytes;
	}

	private function delete_local_copy(){
		$copy_name = get_option($this->original_image_id,IMAGUS_HAS_COPY);
		$copy_path = $this->get_local_copy_folder().'/'.$copy_name;

		if (!$copy_name || !file_exists($copy_path)){
			return false;
		}

		return unlink ($copy_path);
	}

	private function get_local_copy_folder(){
		$upload                       = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		return $upload_dir . '/'.IMAGUS_BACKUP_FOLDER;
	}
}
