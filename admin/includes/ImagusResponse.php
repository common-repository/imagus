<?php

namespace Admin\Includes;

class ImagusResponse {
	public $body;
	public $status_code;

	public function __construct( $body, $status_code ) {
		$this->body        = $body;
		$this->status_code = $status_code;
	}

	/**
	 * @return mixed
	 */
	public function get_body() {
		return $this->body;
	}

	/**
	 * @return mixed
	 */
	public function get_status_code() {
		return $this->status_code;
	}

	public function is_ok() {
		return 200 === $this->status_code;
	}
}
