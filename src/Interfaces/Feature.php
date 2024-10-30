<?php

namespace Magnetis\Interfaces;

interface Feature
{
	/**
	 * Register and launch feature
	 */
	public function __construct();

	/**
	 * Remove all elements from feature to remove it nicely from wordpress
	 */
	public function uninstall(): void;
}
