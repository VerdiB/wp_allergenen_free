<?php

if (!defined('ABSPATH')) {
	exit;
}


enum Allergens_Dietary_FormType
{
	case ALLERGENS;
	case LICENSE;
	case UPDATE;

	public function match(Allergens_Dietary_FormType $formType): bool
	{
		return $this === $formType;
	}
}

