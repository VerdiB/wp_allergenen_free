<?php

if (!defined('ABSPATH')) {
	exit;
}


enum FormType
{
	case ALLERGENS;
	case LICENSE;
	case UPDATE;
	public function match(FormType $formType): bool
	{
		return $this === $formType;
	}
}

