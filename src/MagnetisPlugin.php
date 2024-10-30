<?php

namespace Magnetis;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class MagnetisPlugin
{
	/**
	 * Instances.
	 *
	 * Holds the plugin instance.
	 * Use the instance function to access it.
	 */
	private static ?MagnetisPlugin $instance = null;

	/**
	 * Features.
	 *
	 * Holds all the features classes initialized.
	 */
	private array $features = [];

	/**
	 * Instance.
	 *
	 * Ensure only once instance of the plugin is load or can be loaded.
	 */
	public static function instance(): self
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Uninstall all features from wordpress before remove plugin completely.
	 */
	public function uninstall(): void
	{
		foreach ($this->features as $feature) {
			$feature->uninstall();
		}
	}

	/**
	 * Register Magnetis Plugin in WordPress.
	 */
	private function __construct()
	{
		// Load features
		$features = ['MagnetisModule'];

		foreach ($features as $feature) {
			$class = "\\Magnetis\\Features\\" . $feature;
			$loaded_feature = new $class();
			$this->features[] = $loaded_feature;
		}

		// Load translations
		add_filter('load_textdomain_mofile', function ($mofile, $domain) {
			if ('magnetis-call-tracking' === $domain && false !== strpos($mofile, WP_LANG_DIR . '/plugins/')) {
				$locale = apply_filters('plugin_locale', determine_locale(), $domain);
				$mofile = WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)) . '/languages/' . $domain . '-' . $locale . '.mo';
			}
			return $mofile;
		}, 10, 2);
		add_action('plugins_loaded', function () {
			load_plugin_textdomain('magnetis-call-tracking');
		});
	}
}
