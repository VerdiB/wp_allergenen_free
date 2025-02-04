<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface Allergens_Dietary_Form_I {
	public function showForm( string $allergenName = null );
	public function submit( array $data );
	public function sanitize( array $data );
}
