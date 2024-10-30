<?php

namespace Magnetis\Features;

use Exception;
use Magnetis\Interfaces\Feature;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class MagnetisModule implements Feature
{
	public function __construct()
	{
		add_action('admin_init', fn () => $this->register_admin_settings());
		add_action('admin_menu', fn () => $this->render_admin_menu());

		// Validate that the module key is set before load module script
		if (!$this->check_module_settings()) {
			return;
		}
		add_action('wp_enqueue_scripts', fn () => $this->add_module_script());
	}

	public function uninstall(): void
	{
		// TODO
	}

	/**
	 * Check if module settings are set
	 */
	private function check_module_settings(): bool
	{
		$settings = ['mgt_module_key'];

		foreach ($settings as $setting) {
			if (get_option($setting) == "") {
				return false;
			}
		}

		return true;
	}

	private function check_module_exists(string $module): bool
	{
		$response = wp_remote_get('http://ext.magnetis.io/wp/module/' . $module . '/check');
		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body);

		return $data->module_is_valid;
	}

	/**
	 * Add module script and load it in wordpress
	 */
	private function add_module_script(): void
	{
		wp_enqueue_script_module('magnetis_module', ('https://s.modulecall.fr/' . get_option('mgt_module_key') . '.js'), [], null);
	}

	/**
	 * Register all
	 */
	private function register_admin_settings(): void
	{
		register_setting('mgt_module_options', 'mgt_module_key');
	}

	/**
	 * Add option page for magnetis module config
	 */
	private function render_admin_menu(): void
	{
		add_options_page('Magnetis Call-Tracking', 'Magnetis', 'manage_options', 'magnetis/module', fn () => $this->render_admin_settings());
	}

	/**
	 * Render all settings in wordpress admin settings
	 */
	private function render_admin_settings(): void
	{
?>
		<div class="wrap">

			<h1> <?php _e('Paramètres du module de call-tracking Magnetis', 'magnetis-call-tracking')	?></h1>
			<h2><?php _e('Entrez vos informations', 'magnetis-call-tracking') ?></h2>

			<p>Vous pouvez retrouver votre clé API directement dans <a href="https://app.magnetis.io" target="_blank">votre compte Magnétis</a>. Si vous n'avez pas encore de compte Magnétis, vous pouvez en <a href="https://app.magnetis.io/register" target="_blank">créer un gratuitement en cliquant ici.</a> Dans votre interface Magnétis, vous devez utiliser la clé dédiée à ce site situé dans les paramètres de votre module (la clé commence par "mod-" suivi d'un identifiant).</p>
			<p>Une fois votre clé Module saisie dans Wordpress, le script de suivi du call-tracking sera automatiquement installé sur toutes les pages de votre site si vous avez activé le module dans l'interface Magnétis.</p>
			<p>Notre <a href="https://support.magnetis.io" target="_blank">équipe support</a> se tient à votre disposition pour vous accompagner dans la configuration de votre module et de votre architecture de call-tracking.</p>

			<h2><?php _e('Paramètres', 'magnetis-call-tracking') ?></h2>
			<form action="" method="POST">
				<table>
					<?php @settings_fields('mgt_module_options'); ?>
					<?php @do_settings_fields('', 'mgt_module_options'); ?>
					<?php
					if (isset($_POST['mgt_module_key'])) {
						if ($this->check_module_exists($_POST['mgt_module_key'])) {
							update_option('mgt_module_key', $_POST['mgt_module_key']);
							$class = 'notice notice-success';
							$message = __('Le module a été mis à jour avec succès', 'magnetis-call-tracking');
						} else {
							$class = 'notice notice-error';
							$message = __('Le module n\'est pas valide', 'magnetis-call-tracking');
						}
						printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
					}
					?>
					<tr>
						<th scope="row" style="text-align: left; width: 20%; font-weight: 500;">
							<label for="mgt_module_key">Clé Module</label>
						</th>
						<td>
							<input type="text" name="mgt_module_key" id="mgt_module_key" style="width: 80%;" value="<?php echo get_option('mgt_module_key'); ?>" placeholder="mod-b6a41023-6605-4be0-8dc7-a64c960d507a" />
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<?php submit_button(__('Enregistrer les paramètres', 'magnetis-call-tracking')); ?>
						</td>
					</tr>
				</table>
			</form>
		</div>
<?php
	}
}
