<?php

namespace HimmelKreis4865\BetterSkulls\utils;

trait InstantiableTrait {
	/** @var null | static $instance */
	protected static $instance = null;
	
	/**
	 * Returns the instance of the certain class
	 *
	 * @api
	 *
	 * @return static
	 */
	public static function getInstance(): self {
		if (self::$instance === null) self::$instance = new static();
		return self::$instance;
	}
}