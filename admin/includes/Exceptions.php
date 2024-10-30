<?php


namespace Admin\Includes;


use Exception;
use Throwable;

class ImagusException extends Exception {

}

class UncontrolledException extends ImagusException {

}

class ImagusAPIException extends ImagusException {
	public function __construct( $message = "internal error", $code = 500, Throwable $previous = null ) {
		parent::__construct( $message, $code, $previous );
	}
}


