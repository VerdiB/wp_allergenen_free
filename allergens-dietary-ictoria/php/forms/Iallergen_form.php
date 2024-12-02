<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface I_Allergens_Dietary_Ictoria_Form {
	public function showForm( string $allergenName = null );
	public function submit( array $data );
	public function sanitize( array $data );
}
