<?php

namespace Admin\Includes;

use GuzzleHttp\Client;

class ImagusApi {
	public $url;

	public function __construct() {
		$this->url = 'https://imagus.katodia.com';
	}

	public function optimize( $image, $quality = 70, $chroma_sub_sampling = '' ) {
		$client = new Client();

		$response = $client->request( 'POST', $this->url . '/optimize', [
			'multipart' => [
				[
					'name'     => 'quality',
					'contents' => $quality
				],
				[
					'name'     => 'image',
					'contents' => fopen( $image, 'r' )
				],
			]
		] );

		$body     = $response->getBody();
		$status   = $response->getStatusCode();
		$response = new ImagusResponse( $body, $status );

		return $response;
	}
}
