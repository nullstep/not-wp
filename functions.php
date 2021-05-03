<?php
/*
 *  Author: nullstep
 *  URL: nullstep.com
 */

// no direct access

defined('ABSPATH') or die('nope');

// theme name/slug

define('_THEME', 'not_wp');

// theme author

define('_AUTHOR', 'nullstep');

// ignore ips

define('_IGNORE', [
	// add ips here
]);

//     ▄████████     ▄███████▄   ▄█   
//    ███    ███    ███    ███  ███   
//    ███    ███    ███    ███  ███▌  
//    ███    ███    ███    ███  ███▌  
//  ▀███████████  ▀█████████▀   ███▌  
//    ███    ███    ███         ███   
//    ███    ███    ███         ███   
//    ███    █▀    ▄████▀       █▀    

define('_ARGS_NOT_WP', [
	'container_class' => [
		'type' => 'string',
		'default' => 'container'
	],
	'favicon_image' => [
		'type' => 'string',
		'default' => ''
	],
	'logo_image_normal' => [
		'type' => 'string',
		'default' => ''
	],
	'logo_image_contrast' => [
		'type' => 'string',
		'default' => ''
	],
	'nav_logo' => [
		'type' => 'string',
		'default' => 'normal'
	],
	'primary_colour' => [
		'type' => 'string',
		'default' => '#333333'
	],
	'secondary_colour' => [
		'type' => 'string',
		'default' => '#333333'
	],
	'tertiary_colour' => [
		'type' => 'string',
		'default' => '#333333'
	],
	'theme_css' => [
		'type' => 'string',
		'default' => ''
	],
	'theme_css_minified' => [
		'type' => 'string',
		'default' => ''
	],
	'theme_js' => [
		'type' => 'string',
		'default' => ''
	],
	'theme_js_minified' => [
		'type' => 'string',
		'default' => ''
	]
]);

class _themeAPI {
	public function add_routes() {
		register_rest_route(_THEME . '-theme-api/v1', '/settings', [
			'methods' => 'POST',
			'callback' => [$this, 'update_settings'],
			'args' => _themeSettings::args(),
			'permission_callback' => [$this, 'permissions']
		]);
		register_rest_route(_THEME . '-theme-api/v1', '/settings', [
			'methods' => 'GET',
			'callback' => [$this, 'get_settings'],
			'args' => [],
			'permission_callback' => [$this, 'permissions']
		]);
	}

	public function permissions() {
		return current_user_can('manage_options');
	}

	public function update_settings(WP_REST_Request $request) {
		$settings = [];
		foreach (_themeSettings::args() as $key => $val) {
			$settings[$key] = $request->get_param($key);
		}
		_themeSettings::save_settings($settings);
		return rest_ensure_response(_themeSettings::get_settings());
	}

	public function get_settings(WP_REST_Request $request) {
		return rest_ensure_response(_themeSettings::get_settings());
	}
}

class _themeSettings {
	protected static $option_key = _THEME . '-theme-settings';

	public static function args() {
		$args = _ARGS_NOT_WP;
		foreach (_ARGS_NOT_WP as $key => $val) {
			$val['required'] = true;
			switch ($val['type']) {
				case 'integer': {
					$cb = 'absint';
					break;
				}
				default: {
					$cb = 'sanitize_text_field';
				}
				$val['sanitize_callback'] = $cb;
			}
		}
		return $args;
	}

	public static function get_settings() {
		$defaults = [];
		foreach (_ARGS_NOT_WP as $key => $val) {
			$defaults[$key] = $val['default'];
		}
		$saved = get_option(self::$option_key, []);
		if (!is_array($saved) || empty($saved)) {
			return $defaults;
		}
		return wp_parse_args($saved, $defaults);
	}

	public static function save_settings(array $settings) {
		$defaults = [];
		foreach (_ARGS_NOT_WP as $key => $val) {
			$defaults[$key] = $val['default'];
		}
		foreach ($settings as $i => $setting) {
			if (!array_key_exists($i, $defaults)) {
				unset($settings[$i]);
			}
			if ($i == 'theme_css') {
				$settings['theme_css_minified'] = minify_css($setting);
			}
			if ($i == 'theme_js') {
				$settings['theme_js_minified'] = minify_js($setting);
			}
		}
		update_option(self::$option_key, $settings);
	}
}

class _themeMenu {
	protected $slug = _THEME . '-theme-menu';
	protected $assets_url;

	public function __construct($assets_url) {
		$this->assets_url = $assets_url;
		add_action('admin_menu', [$this, 'add_page']);
		add_action('admin_enqueue_scripts', [$this, 'register_assets']);
	}

	public function add_page() {
		add_menu_page(
			_THEME,
			_THEME,
			'manage_options',
			$this->slug,
			[$this, 'render_admin'],
			'dashicons-palmtree',
			2
		);
	}

	public function register_assets() {
		wp_register_script($this->slug, $this->assets_url . '/_admin.js', ['jquery']);
		wp_register_style($this->slug, $this->assets_url . '/_admin.css');
		wp_localize_script($this->slug, _THEME, [
			'strings' => [
				'saved' => 'Settings Saved',
				'error' => 'Error'
			],
			'api' => [
				'url' => esc_url_raw(rest_url(_THEME . '-theme-api/v1/settings')),
				'nonce' => wp_create_nonce('wp_rest')
			]
		]);
	}

	public function enqueue_assets() {
		if (!wp_script_is($this->slug, 'registered')) {
			$this->register_assets();
		}
		wp_enqueue_script($this->slug);
		wp_enqueue_style($this->slug);
	}

	public function render_admin() {
		wp_enqueue_media();
		$this->enqueue_assets();
?>
		<style>
			.not_wp-tab {
				display: none;
				background: url(<?php echo $this->assets_url . '/not_wp.svg'; ?>) no-repeat;
			}
		</style>
		<div class="wrap">
			<h1>not_wp</h1>
			<p style="max-width:500px">Configure your theme settings...</p>
			<form id="not_wp-form" method="post">
				<nav id="not_wp-nav" class="nav-tab-wrapper">
					<a href="#not_wp-settings" class="nav-tab nav-tab-active">Settings</a>
					<a href="#not_wp-css" class="nav-tab">CSS</a>
					<a href="#not_wp-js" class="nav-tab">JS</a>
				</nav>
				<div class="tab-content">
					<div id=not_wp-settings class="not_wp-tab">
						<div class="form-block">
							<label for="container_class">
								Container Width:
							</label>
							<select id="container_class" name="container_class">
								<option value="container-fluid">Full Screen</option>
								<option value="container">Boxed</option>
							</select>					
						</div>
						<div class="form-block">
							<label for="favicon_image">
								Favicon Image:
							</label>
							<input id="favicon_image" type="text" name="favicon_image">
							<input data-id="favicon_image" type="button" class="button-primary choose-file-button" value="Select...">
						</div>
						<div class="form-block">
							<label for="logo_image_normal">
								Logo Image (normal):
							</label>
							<input id="logo_image_normal" type="text" name="logo_image_normal">
							<input data-id="logo_image_normal" type="button" class="button-primary choose-file-button" value="Select...">
						</div>
						<div class="form-block">
							<label for="logo_image_contrast">
								Logo Image (contrast):
							</label>
							<input id="logo_image_contrast" type="text" name="logo_image_contrast">
							<input data-id="logo_image_contrast" type="button" class="button-primary choose-file-button" value="Select...">
						</div>
						<div class="form-block">
							<label for="nav_logo">
								Nav Logo:
							</label>
							<select id="nav_logo" name="nav_logo">
								<option value="normal">Normal</option>
								<option value="contrast">Contrast</option>
								<option value="none">None</option>
							</select>					
						</div>
						<div class="form-block-ns">
							<label for="primary_colour">
								Primary Colour:
							</label>
							<input id="primary_colour" type="text" name="primary_colour">
							<input data-id="primary_colour" type="color" class="choose-colour-button" value="#000000">
						</div>
						<div class="form-block-ns">
							<label for="secondary_colour">
								Secondary Colour:
							</label>
							<input id="secondary_colour" type="text" name="secondary_colour">
							<input data-id="secondary_colour" type="color" class="choose-colour-button" value="#000000">
						</div>
						<div class="form-block-ns">
							<label for="tertiary_colour">
								Tertiary Colour:
							</label>
							<input id="tertiary_colour" type="text" name="tertiary_colour">
							<input data-id="tertiary_colour" type="color" class="choose-colour-button" value="#000000">
						</div>
					</div>
					<div id="not_wp-css" class="not_wp-tab">
						<div class="form-block">
							<textarea id="theme_css" class="tabs" name="theme_css"></textarea>
						</div>
					</div>
					<div id="not_wp-js" class="not_wp-tab">
						<div class="form-block">
							<textarea id="theme_js" class="tabs" name="theme_js"></textarea>
						</div> 
					</div>
				</div>
				<div>
					<?php submit_button(); ?>
				</div>
				<div id="feedback">
				</div>
			</form>
		</div>
<?php
	}
}

//  ███    █▄      ▄███████▄  ████████▄      ▄████████      ███         ▄████████     ▄████████  
//  ███    ███    ███    ███  ███   ▀███    ███    ███  ▀█████████▄    ███    ███    ███    ███  
//  ███    ███    ███    ███  ███    ███    ███    ███     ▀███▀▀██    ███    █▀     ███    ███  
//  ███    ███    ███    ███  ███    ███    ███    ███      ███   ▀   ▄███▄▄▄       ▄███▄▄▄▄██▀  
//  ███    ███  ▀█████████▀   ███    ███  ▀███████████      ███      ▀▀███▀▀▀      ▀▀███▀▀▀▀▀    
//  ███    ███    ███         ███    ███    ███    ███      ███        ███    █▄   ▀███████████  
//  ███    ███    ███         ███   ▄███    ███    ███      ███        ███    ███    ███    ███  
//  ████████▀    ▄████▀       ████████▀     ███    █▀      ▄████▀      ██████████    ███    ███  

class _themeUpdater {
	protected $theme = _THEME;
	protected $repository = _AUTHOR . '/' . _THEME;
	protected $domain = 'https://github.com/';
	protected $raw_domain = 'https://raw.githubusercontent.com/';
	protected $css_endpoint = '/main/style.css';
	protected $zip_endpoint = '/releases/download/v';
	protected $remote_css_uri;
	protected $remote_zip_uri;
	protected $remote_version;
	protected $local_version;

	public function init() {
		add_filter('auto_update_theme', [
			$this,
			'auto_update_theme'
		], 20, 2);
		add_filter('upgrader_source_selection', [$this,
			'upgrader_source_selection'
		], 10, 4);
		add_filter('pre_set_site_transient_update_themes', [
			$this,
			'pre_set_site_transient_update_themes'
		]);
	}

	public function auto_update_theme($update, $item) {
		return $this->theme === $item->theme;
	}

	public function upgrader_source_selection($source, $remote_source, $upgrader, $hook_extra) {
		global $wp_filesystem;

		$update = [
			'update-selected',
			'update-selected-themes',
			'upgrade-theme'
		];

		if (!isset($_GET['action']) || !in_array($_GET['action'], $update, true)) {
			return $source;
		}

		if (!isset($source, $remote_source)) {
			return $source;
		}

		if (false === stristr(basename($source), $this->theme)) {
			return $source;
		}

		$basename = basename($source);
		$upgrader->skin->feedback(esc_html_e('Renaming theme directory.', 'bootstrap'));
		$corrected_source = str_replace($basename, $this->theme, $source);

		if ($wp_filesystem->move($source, $corrected_source, true)) {
			$upgrader->skin->feedback(esc_html_e('Rename successful.', 'bootstrap'));
			return $corrected_source;
		}

		return new WP_Error();
	}

	public function pre_set_site_transient_update_themes($transient) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		$this->local_version = (wp_get_theme($this->theme))->get('Version');

		if ($this->has_update()) {
			$response = [
				'theme' => $this->theme,
				'new_version' => $this->remote_version,
				'url' => $this->construct_repository_uri(),
				'package' => $this->construct_remote_zip_uri(),
				'branch' => 'master'
			];
			$transient->response[$this->theme] = $response;
		}

		return $transient;
	}

	protected function construct_remote_stylesheet_uri() {
		return $this->remote_css_uri = $this->raw_domain . $this->repository . $this->css_endpoint;
	}

	protected function construct_remote_zip_uri() {
		return $this->remote_zip_uri = $this->domain . $this->repository . $this->zip_endpoint . $this->remote_version . '/' . $this->theme . '.zip';
	}

	protected function construct_repository_uri() {
		return $this->repository_uri = $this->domain . trailingslashit($this->repository);
	}

	protected function get_remote_version() {
		$this->remote_stylesheet_uri = $this->construct_remote_stylesheet_uri();
		$response = $this->remote_get($this->remote_stylesheet_uri);
		$response = str_replace("\r", "\n", wp_remote_retrieve_body($response));
		$headers = [
			'Version' => 'Version'
		];

		foreach ($headers as $field => $regex) {
			if (preg_match( '/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $response, $match) && $match[1]) {
				$headers[$field] = _cleanup_header_comment($match[1]);
			}
			else {
				$headers[$field] = '';
			}
		}

		return $this->remote_version = ('' === $headers['Version']) ? '' : $headers['Version'];
	}

	protected function has_update() {
		if (!$this->remote_version) {
			$this->remote_version = $this->get_remote_version();
		}
		return version_compare($this->remote_version, $this->local_version, '>');
	}

	protected function remote_get($url, $args = []) {
		return wp_remote_get($url, $args);
	}
}

//   ▄█     █▄    ▄█   ████████▄      ▄██████▄      ▄████████      ███      
//  ███     ███  ███   ███   ▀███    ███    ███    ███    ███  ▀█████████▄  
//  ███     ███  ███▌  ███    ███    ███    █▀     ███    █▀      ▀███▀▀██  
//  ███     ███  ███▌  ███    ███   ▄███          ▄███▄▄▄          ███   ▀  
//  ███     ███  ███▌  ███    ███  ▀▀███ ████▄   ▀▀███▀▀▀          ███      
//  ███     ███  ███   ███    ███    ███    ███    ███    █▄       ███      
//  ███ ▄█▄ ███  ███   ███   ▄███    ███    ███    ███    ███      ███      
//   ▀███▀███▀   █▀    ████████▀     ████████▀     ██████████     ▄████▀    

class _themeWidget extends WP_Widget {
	protected $registered = false;
	protected $default_instance = [
		'title'   => '',
		'content' => ''
	];
 
	public function __construct() {
		$widget_ops  = [
			'classname' => 'nwp',
			'description' => 'not_wp widget',
			'customize_selective_refresh' => true
		];
		$control_ops = [
			'width' => 400,
			'height' => 350
		];
		parent::__construct(
			'nwp',
			'not_wp',
			$widget_ops,
			$control_ops
		);
	}
 
	public function _register_one($number = -1) {
		parent::_register_one($number);
		if ($this->registered) {
			return;
		}
		$this->registered = true;
 
		wp_add_inline_script('custom-html-widgets', sprintf('wp.customHtmlWidgets.idBases.push(%s);', wp_json_encode($this->id_base)));
		add_action('admin_print_scripts-widgets.php', [$this, 'enqueue_admin_scripts']);
		add_action('admin_footer-widgets.php', ['_themeWidget', 'render_control_template_scripts']);
	}
 
	public function _filter_gallery_shortcode_attrs($attrs) {
		if (!is_singular() && empty($attrs['id']) && empty($attrs['include'])) {
			$attrs['id'] = -1;
		}
		return $attrs;
	}
 
	public function widget($args, $instance) {
		global $post;
		$original_post = $post;

		if (is_singular()) {
			$post = get_queried_object();
		}
		else {
			$post = null;
		}
 
		add_filter('shortcode_atts_gallery', [$this, '_filter_gallery_shortcode_attrs']);
		$instance = array_merge($this->default_instance, $instance);
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		$css_classes = empty($instance['css_classes']) ? '' : $instance['css_classes'];
		$show_title = !empty($instance['show_title']) ? '1' : '0';
		$parse_php = !empty($instance['parse_php']) ? '1' : '0';
		$do_shortcodes = !empty($instance['do_shortcodes']) ? '1' : '0';
 
		$instance_copy = array_merge(
			$instance, [
				'text' => isset($instance['content']) ? $instance['content'] : '',
				'filter' => false,
				'visual' => false
			]
		);

		unset($instance_copy['content']); 
		$content = apply_filters('widget_text', $instance['content'], $instance_copy, $this);
		$post = $original_post;
		remove_filter('shortcode_atts_gallery', [$this, '_filter_gallery_shortcode_attrs']);

		if ($css_classes) {
			if (strpos($args['before_widget'], 'class') === false) {
				$args['before_widget'] = str_replace('>', ' class="'. $css_classes . '">', $args['before_widget']);
			}
			else {
				$args['before_widget'] = str_replace('class="', 'class="'. $css_classes . ' ', $args['before_widget']);
			}
		}

		if ($parse_php) {
			ob_start();
			eval('?>' . $content);
			$content = ob_get_contents();
			ob_end_clean();
		}

		if ($do_shortcodes) {
			$content = do_shortcode($content);
		}

		echo $args['before_widget'];

		if ($show_title && $title) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo $content;
		echo $args['after_widget'];
	}
 

	public function update($new_instance, $old_instance) {
		$instance = array_merge($this->default_instance, $old_instance);
		$instance['title'] = sanitize_text_field($new_instance['title']);
		$instance['css_classes'] = sanitize_text_field($new_instance['css_classes']);
		$instance['show_title'] = !empty($new_instance['show_title'] ) ? 1 : 0;
		$instance['parse_php'] = !empty($new_instance['parse_php'] ) ? 1 : 0;
		$instance['do_shortcodes'] = !empty($new_instance['do_shortcodes'] ) ? 1 : 0;

		if (current_user_can('unfiltered_html')) {
			$instance['content'] = $new_instance['content'];
		}
		else {
			$instance['content'] = wp_kses_post($new_instance['content']);
		}
		return $instance;
	}
 
	public function enqueue_admin_scripts() {
		$settings = wp_enqueue_code_editor([
			'type' => 'text/html',
			'codemirror' => [
				'indentUnit' => 2,
				'tabSize' => 2
			]
		]);
 
		wp_enqueue_script('custom-html-widgets');
		if (empty($settings)) {
			$settings = [
				'disabled' => true,
			];
		}
	}
 
	public function form($instance) {
		$instance = wp_parse_args((array) $instance, $this->default_instance);
		$css_classes = isset($instance['css_classes']) ? $instance['css_classes'] : '';
		$show_title = isset($instance['show_title']) ? (bool) $instance['show_title'] : false;
		$parse_php = isset($instance['parse_php']) ? (bool) $instance['parse_php'] : false;
		$do_shortcodes = isset($instance['do_shortcodes']) ? (bool) $instance['do_shortcodes'] : false;
?>
		<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" class="title sync-input" type="hidden" value="<?php echo esc_attr($instance['title']); ?>">
		<textarea id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>" class="content sync-input" hidden><?php echo esc_textarea($instance['content']); ?></textarea>
		<p>
			<label for="<?php echo $this->get_field_id('css_classes'); ?>">CSS Classes</label>
			<input id="<?php echo $this->get_field_id('css_classes'); ?>" name="<?php echo $this->get_field_name('css_classes'); ?>" class="widefat" type="text" value="<?php echo esc_attr($css_classes); ?>">
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_title'); ?>" name="<?php echo $this->get_field_name('show_title'); ?>"<?php checked($show_title); ?>>
			<label for="<?php echo $this->get_field_id('show_title'); ?>">Show Title</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('parse_php'); ?>" name="<?php echo $this->get_field_name('parse_php'); ?>"<?php checked($parse_php); ?>>
			<label for="<?php echo $this->get_field_id('parse_php'); ?>">Parse PHP</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('do_shortcodes'); ?>" name="<?php echo $this->get_field_name('do_shortcodes'); ?>"<?php checked($do_shortcodes); ?>>
			<label for="<?php echo $this->get_field_id('do_shortcodes'); ?>">Do Shortcodes</label>
		</p>
<?php
	}
 
	public static function render_control_template_scripts() {
?>
		<script type="text/html" id="tmpl-widget-custom-html-control-fields">
			<# var elementIdPrefix = 'el' + String(Math.random()).replace(/\D/g, '') + '_' #>
			<p>
				<label for="{{ elementIdPrefix }}title"><?php esc_html_e('Title:'); ?></label>
				<input id="{{ elementIdPrefix }}title" type="text" class="widefat title">
			</p> 
			<p>
				<label for="{{ elementIdPrefix }}content" id="{{ elementIdPrefix }}content-label"><?php esc_html_e( 'Content:' ); ?></label>
				<textarea id="{{ elementIdPrefix }}content" class="widefat code content" rows="16" cols="20"></textarea>
			</p> 
			<div class="code-editor-error-container"></div>
		</script>
<?php
	}
}

//     ▄████████  ███    █▄   ███▄▄▄▄▄     ▄████████     ▄████████  
//    ███    ███  ███    ███  ███▀▀▀▀██▄  ███    ███    ███    ███  
//    ███    █▀   ███    ███  ███    ███  ███    █▀     ███    █▀   
//   ▄███▄▄▄      ███    ███  ███    ███  ███           ███         
//  ▀▀███▀▀▀      ███    ███  ███    ███  ███         ▀███████████  
//    ███         ███    ███  ███    ███  ███    █▄            ███  
//    ███         ███    ███  ███    ███  ███    ███     ▄█    ███  
//    ███         ████████▀    ▀█    █▀   ████████▀    ▄████████▀   

// get css colours

function getcolours($echo = true) {
	$css = ':root{' .
		'--primary-colour:' . _themeSettings::get_settings()['primary_colour'] . ';' .
		'--secondary-colour:' . _themeSettings::get_settings()['secondary_colour'] . ';' .
		'--tertiary-colour:' . _themeSettings::get_settings()['tertiary_colour'] . ';' .
	'}';
	if ($echo) {
		echo $css;
	}
	else {
		return $css;
	}
}

// get favicon

function getfavicon($echo = true) {
	$setting = _themeSettings::get_settings()['favicon_image'];
	$favicon = ($setting != '') ? '/uploads/' . $setting : '/img/favicon.png';
	if ($echo) {
		echo $favicon;
	}
	else {
		return $favicon;
	}
}

// get setting

function getvalue($key, $newline = false, $echo = true) {
	$n = ($newline) ? "\n" : '';
	$setting = _themeSettings::get_settings()[$key] . $n;
	if ($echo) {
		echo $setting;
	}
	else {
		return $setting;
	}
}

// pages/posts views count

function getviews($postID) {
	$count_key = 'post_views_count';
	$count = get_post_meta($postID, $count_key, true);
	if ($count == '') {
		delete_post_meta($postID, $count_key);
		add_post_meta($postID, $count_key, '0');
		return "No Views";
	}
	return $count . ' View' . (($count != 1)? 's' : '');
}

function setviews($postID) {
	$ip = $_SERVER['REMOTE_ADDR'];
	if (!in_array($ip, _IGNORE)) {
		$count_key = 'post_views_count';
		$count = get_post_meta($postID, $count_key, true);
		if ($count == '') {
			$count = 0;
			delete_post_meta($postID, $count_key);
			add_post_meta($postID, $count_key, '0');
		}
		else {
			$count++;
			update_post_meta($postID, $count_key, $count);
		}
	}
}

function posts_column_views($defaults) {
	$defaults['post_views'] = 'Views';
	return $defaults;
}

function posts_custom_column_views($column_name, $id) {
	if ($column_name === 'post_views') {
		echo getviews(get_the_ID());
	}
}

function pages_column_views($defaults) {
	$defaults['page_views'] = 'Views';
	return $defaults;
}

function pages_custom_column_views($column_name, $id) {
	if ($column_name === 'page_views') {
		echo getviews(get_the_ID());
	}
}

// media downloads field

function media_downloads($form_fields, $post) {
	$form_fields['file_downloads'] = [
		'label' => 'Downloads',
		'input' => 'text',
		'value' => get_post_meta($post->ID, 'file_downloads', true),
		'helps' => ''
	];
	return $form_fields;
}
 
function media_downloads_save($post, $attachment) {
	if (isset($attachment['file_downloads'])) {
		update_post_meta($post->ID, 'file_downloads', $attachment['file_downloads']);
	}
	return $post;
}

// latest posts

function latest_posts($count) {
	wp_reset_postdata();
	$loop = 0;
	$none = true;
	$post_id = get_queried_object_id();
	$query = new WP_Query('posts_per_page=' . ($count + 1));
	echo '<ul class="latest-posts">';
	while ($query -> have_posts()) : $query -> the_post();
		if (get_the_ID() != $post_id) {
			echo '<li><a href="';
			the_permalink();
			echo '">';
			the_title();
			echo '</a><br><span>';
			the_time(get_option('date_format'));
			echo ' - ';
			the_time();
			echo '</span></li>';
			$none = false;
			$loop++;
		}
		if ($loop == 5) {
			break;
		}
	endwhile;
	if ($none) {
		echo '<li>No Other Posts :(</li>';
	}
	echo '</ul>';
}

// page column class metadata

function add_post_metadata() {
	$screen = 'page';
	add_meta_box('bla-bla-bla', 'Column Class', 'add_post_metadata_callback', $screen, 'side', 'default', null);
}

function add_post_metadata_callback($post) {
	wp_nonce_field('column_class_save_data', 'column_class_nonce');
	$value = get_post_meta($post->ID, 'column_class', true);
	echo '<input class="components-text-control__input" style="margin-top:8px" type="text" name="column_class" value="' . esc_attr($value) . '" placeholder="Enter Column Class...">';
}
 
function save_post_metadata($post_id) {
	if (!isset($_POST['column_class_nonce'])) {
		return;
	}
	if (!wp_verify_nonce($_POST['column_class_nonce'], 'column_class_save_data')) {
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return;
		}
	}
	else {
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}
	}

	$data = sanitize_text_field($_POST['column_class']);
	update_post_meta($post_id, 'column_class', $data);
}

// pagination

function pagination() {
	global $wp_query;
	$big = 999999999;
	echo paginate_links([
		'base' => str_replace($big, '%#%', get_pagenum_link($big)),
		'format' => '?paged=%#%',
		'current' => max(1, get_query_var('paged')),
		'total' => $wp_query->max_num_pages
	]);
}

// excerpts

function excerpt($length) {
	return $length;
}

function excerpts($length_callback = '', $more_callback = '') {
	global $post;
	if (function_exists($length_callback)) {
		add_filter('excerpt_length', $length_callback);
	}
	if (function_exists($more_callback)) {
		add_filter('excerpt_more', $more_callback);
	}
	$output = get_the_excerpt();
	$output = apply_filters('wptexturize', $output);
	$output = apply_filters('convert_chars', $output);
	$output = '<p>' . $output . '</p>';
	echo $output;
}

// mime types

function add_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	$mimes['ico'] = 'image/vnd.microsoft.icon';
	return $mimes;
}

// htaccess stuff

function output_htaccess($rules) {
	$theme = _THEME;
	$new_rules = "\n# BEGIN _theme\n<IfModule mod_rewrite.c>\nRewriteEngine On\nRewriteCond %{REQUEST_URI} ^/img [NC]\nRewriteRule /(.*) wp-content/themes/{$theme}/$1 [L]\nRewriteCond %{REQUEST_URI} ^/css [NC]\nRewriteRule /(.*) wp-content/themes/{$theme}/$1 [L]\nRewriteCond %{REQUEST_URI} ^/js [NC]\nRewriteRule /(.*) wp-content/themes/{$theme}/$1 [L]\nRewriteCond %{REQUEST_URI} ^/uploads [NC]\nRewriteRule /(.*) wp-content/themes/{$theme}/uploads.php?file=$1 [L]\n</IfModule>\n# END _theme\n\n";
	return $new_rules . $rules;
}

function flush_htaccess() {
	flush_rewrite_rules();
}

// register nav

register_nav_menus([
	'primary' => 'Primary Menu'
]);

//     ▄████████     ▄█    █▄      ▄██████▄      ▄████████      ███      
//    ███    ███    ███    ███    ███    ███    ███    ███  ▀█████████▄  
//    ███    █▀     ███    ███    ███    ███    ███    ███     ▀███▀▀██  
//    ███          ▄███▄▄▄▄███▄▄  ███    ███   ▄███▄▄▄▄██▀      ███   ▀  
//  ▀███████████  ▀▀███▀▀▀▀███▀   ███    ███  ▀▀███▀▀▀▀▀        ███      
//           ███    ███    ███    ███    ███  ▀███████████      ███      
//     ▄█    ███    ███    ███    ███    ███    ███    ███      ███      
//   ▄████████▀     ███    █▀      ▀██████▀     ███    ███     ▄████▀    

//   ▄████████   ▄██████▄   ████████▄      ▄████████     ▄████████  
//  ███    ███  ███    ███  ███   ▀███    ███    ███    ███    ███  
//  ███    █▀   ███    ███  ███    ███    ███    █▀     ███    █▀   
//  ███         ███    ███  ███    ███   ▄███▄▄▄        ███         
//  ███         ███    ███  ███    ███  ▀▀███▀▀▀      ▀███████████  
//  ███    █▄   ███    ███  ███    ███    ███    █▄            ███  
//  ███    ███  ███    ███  ███   ▄███    ███    ███     ▄█    ███  
//  ████████▀    ▀██████▀   ████████▀     ██████████   ▄████████▀   

// logo shortcodes

function logo_normal_shortcode($atts = [], $content = null, $tag = '') {
	return '<img src="/uploads/' . _themeSettings::get_settings()['logo_image_normal'] . '" class="logo ' . $content .'">';
}

function logo_contrast_shortcode($atts = [], $content = null, $tag = '') {
	return '<img src="/uploads/' . _themeSettings::get_settings()['logo_image_contrast'] . '" class="logo ' . $content .'">';
}

// include file shortcode

function inc_shortcode($atts = [], $content = null, $tag = '') {
	if ($content) {
		ob_start();
		get_template_part($content);
		return ob_get_clean();
	}
	else {
		return '';
	}
}

// responsive video shortcode

function video_shortcode($atts = [], $content = null, $tag = '') {
	return '<div class="video"><iframe src="' . $content . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
}

// show child pages shortcode

function children_shortcode() {
	ob_start();
	if (is_page()) {
		$current_page_id = get_the_ID();
		$child_pages = get_pages([ 
			'child_of' => $current_page_id,
			'sort_column' => 'menu_order',
			'sort_order' => 'ASC'
		]);
		if ($child_pages) {
			echo '<div class="row">';
			foreach ($child_pages as $child_page) {
				$page_id = $child_page->ID;
				$page_link = get_permalink($page_id);
				$page_title = $child_page->post_title;
				$page_content = $child_page->post_content;
				$page_col_class = get_post_meta($page_id, 'column_class', true);
				?><div class="<?php echo $page_col_class; ?>"><h3><?php echo $page_title; ?></h3><p><?php echo do_shortcode($page_content); ?></p></div><?php
			}
			echo '</div>';
		}
	}
	return ob_get_clean();
}

//     ▄████████   ▄█   ▀████    ▐████▀     ▄████████     ▄████████  
//    ███    ███  ███     ███▌   ████▀     ███    ███    ███    ███  
//    ███    █▀   ███▌     ███  ▐███       ███    █▀     ███    █▀   
//   ▄███▄▄▄      ███▌     ▀███▄███▀      ▄███▄▄▄        ███         
//  ▀▀███▀▀▀      ███▌     ████▀██▄      ▀▀███▀▀▀      ▀███████████  
//    ███         ███     ▐███  ▀███       ███    █▄            ███  
//    ███         ███    ▄███     ███▄     ███    ███     ▄█    ███  
//    ███         █▀    ████       ███▄    ██████████   ▄████████▀   

// fix content urls/classes etc

function fix_content($content) {
	libxml_use_internal_errors(true);
	$dom = new DOMDocument;
	$dom->strictErrorChecking = false;
	$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
	foreach ($dom->getElementsByTagName('img') as $img) {
		$file = basename(parse_url($img->getAttribute('src'), PHP_URL_PATH));
		$path = substr($img->getAttribute('src'), 0, (0 - strlen($file)));
		$img->setAttribute('src', '/uploads/' . $file);
		$set = str_replace($path, '/uploads/', $img->getAttribute('srcset'));
		$img->setAttribute('srcset', $set);
	}
	foreach ($dom->getElementsByTagName('figure') as $fig) {
		$fig->removeAttribute('class');
	}
	foreach ($dom->getElementsByTagName('pre') as $pre) {
		$pre->removeAttribute('class');
	}
	$xpath = new DOMXPath($dom);
	for ($els = $xpath->query('//comment()'), $i = $els->length - 1; $i >= 0; $i--) {
		$els->item($i)->parentNode->removeChild($els->item($i));
	}
	return "\t\t\t\t\t\t" . str_replace("\n", '', $dom->saveHTML());
}

// no category base

function no_category_base_refresh_rules() {
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

function no_category_base_permastruct() {
	global $wp_rewrite;
	global $wp_version;
	$wp_rewrite->extra_permastructs['category']['struct'] = '%category%';
}

function no_category_base_rewrite_rules($category_rewrite) {
	global $wp_rewrite;
	$category_rewrite = [];
	$categories = get_categories([
		'hide_empty' => false
	]);

	foreach ($categories as $category) {
		$category_nicename = $category->slug;

		if ($category->parent == $category->cat_ID) {
			$category->parent = 0;
		}
		elseif ($category->parent != 0) {
			$category_nicename = get_category_parents($category->parent, false, '/', true) . $category_nicename;
		}

		$category_rewrite['('.$category_nicename.')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
		$category_rewrite["({$category_nicename})/{$wp_rewrite->pagination_base}/?([0-9]{1,})/?$"] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
		$category_rewrite['('.$category_nicename.')/?$'] = 'index.php?category_name=$matches[1]';
	}

	$old_category_base = get_option('category_base') ? get_option('category_base') : 'category';
	$old_category_base = trim($old_category_base, '/');
	$category_rewrite[$old_category_base.'/(.*)$'] = 'index.php?category_redirect=$matches[1]';

	return $category_rewrite;
}

function no_category_base_query_vars($public_query_vars) {
	$public_query_vars[] = 'category_redirect';
	return $public_query_vars;
}

function no_category_base_request($query_vars) {
	if (isset($query_vars['category_redirect'])) {
		$catlink = trailingslashit(get_option('home')) . user_trailingslashit($query_vars['category_redirect'], 'category');
		status_header(301);
		header('Location: ' . $catlink);
		exit();
	}

	return $query_vars;
}

// remove crap

function remove_category_rel_from_category_list($thelist) {
	return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}

function remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action('wp_head', [
		$wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
		'recent_comments_style'
	]);
}

function remove_crap() {
	wp_dequeue_style('wp-block-library');
	wp_deregister_script('jquery');
}

// clean up nav items

function nav_attributes_filter($var) {
	return is_array($var) ? array_intersect($var, ['current-menu-item', 'nav-item']) : '';
}

// add widget stuff

function register_widget_stuff() {
	$widgets = [
		'top-area' => 'top area',
		'header-area' => 'header area',
		'page-top' => 'page top',
		'page-bottom' => 'page bottom',
		'footer-top' => 'footer top',
		'footer-bottom' => 'footer bottom'
	];

	foreach  ($widgets as $class => $title) {
		register_sidebar([
			'id' => $class,
			'name' => $title,
			'description' => $title . ' widget area',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="' . $class . '-title-holder"><h3 class="' . $class . '-title">',
			'after_title' => '</h3></div>'
		]);
	}

	register_widget('_themeWidget');
}

// set some wp options

function set_wp_options() {
	update_option('permalink_structure', '/%postname%/');
	update_option('category_base', '');
	update_option('tag_base', '');
	update_option('posts_per_page', 10);
	update_option('uploads_use_yearmonth_folders', 0);
	update_option('ping_sites', '');
	update_option('use_smilies', 0);
	update_option('default_pingback_flag', 0);
	update_option('show_avatars', 0);
}

// login screen

function not_wp_login_logo() {
	echo '<style>h1 a { background-image:url(' . get_template_directory_uri() . '/not_wp_dark.svg) !important; width: 300px !important; background-size: auto auto !important; }</style>';
}

//   ▄█   ███▄▄▄▄▄     ▄█       ███      
//  ███   ███▀▀▀▀██▄  ███   ▀█████████▄  
//  ███▌  ███    ███  ███▌     ▀███▀▀██  
//  ███▌  ███    ███  ███▌      ███   ▀  
//  ███▌  ███    ███  ███▌      ███      
//  ███   ███    ███  ███       ███      
//  ███   ███    ███  ███       ███      
//  █▀     ▀█    █▀   █▀       ▄████▀    

// theme updater

$updater = new _themeUpdater();

// actions

add_action('init', [$updater, 'init']);
add_action('init', 'set_wp_options');
add_action('widgets_init', 'remove_recent_comments_style');
add_action('init', 'pagination');
add_action('admin_init', 'flush_htaccess');
add_action('wp_enqueue_scripts', 'remove_crap');
add_action('manage_posts_custom_column', 'posts_custom_column_views', 5, 2);
add_action('manage_pages_custom_column', 'pages_custom_column_views', 5, 2);
add_action('created_category', 'no_category_base_refresh_rules');
add_action('delete_category', 'no_category_base_refresh_rules');
add_action('edited_category', 'no_category_base_refresh_rules');
add_action('init', 'no_category_base_permastruct');
add_action('add_meta_boxes', 'add_post_metadata');
add_action('save_post', 'save_post_metadata');
add_action('widgets_init', 'register_widget_stuff');
add_action('login_head', 'not_wp_login_logo');
add_action('shutdown', function() {
	while (@ob_end_flush());
});

remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
remove_action('wp_head', 'noindex', 1);
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('template_redirect', 'rest_output_link_header', 11, 0);
remove_action('wp_head', 'wp_oembed_add_host_js');
remove_action('rest_api_init', 'wp_oembed_register_route');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'oa_social_login_add_javascripts');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_action('wp_head', 'wp_resource_hints', 2);
remove_action('shutdown', 'wp_ob_end_flush_all', 1);

// filters

add_filter('widget_text', 'shortcode_unautop');
add_filter('the_category', 'remove_category_rel_from_category_list');
add_filter('the_excerpt', 'shortcode_unautop');
add_filter('show_admin_bar', '__return_false');
add_filter('post_thumbnail_html', 'remove_thumbnail_dimensions', 10);
add_filter('image_send_to_editor', 'remove_thumbnail_dimensions', 10);
add_filter('manage_posts_columns', 'posts_column_views');
add_filter('manage_pages_columns', 'pages_column_views');
add_filter('the_content', 'fix_content', 20);
add_filter('mod_rewrite_rules', 'output_htaccess');
add_filter('upload_mimes', 'add_mime_types');
add_filter('attachment_fields_to_edit', 'media_downloads', 10, 2);
add_filter('attachment_fields_to_save', 'media_downloads_save', 10, 2);
add_filter('category_rewrite_rules', 'no_category_base_rewrite_rules');
add_filter('query_vars', 'no_category_base_query_vars');
add_filter('request', 'no_category_base_request');
add_filter('nav_menu_css_class', 'nav_attributes_filter', 100, 1);
add_filter('nav_menu_item_id', 'nav_attributes_filter', 100, 1);
add_filter('page_css_class', 'nav_attributes_filter', 100, 1);

remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
remove_filter('the_excerpt', 'wpautop');
remove_filter('wp_robots', 'wp_robots_max_image_preview_large');

// shortcodes

add_shortcode('logo-normal', 'logo_normal_shortcode');
add_shortcode('logo-contrast', 'logo_contrast_shortcode');
add_shortcode('inc', 'inc_shortcode');
add_shortcode('video', 'video_shortcode');
add_shortcode('children', 'children_shortcode');

// boot theme

add_action('init', function() {
	if (is_admin()) {
		$assets_url = get_template_directory_uri();
		new _themeMenu($assets_url);
	}
});

add_action('rest_api_init', function() {
	_themeSettings::args();
	$api = new _themeAPI();
	$api->add_routes();
});

//     ▄█    █▄        ▄████████   ▄█           ▄███████▄  
//    ███    ███      ███    ███  ███          ███    ███  
//    ███    ███      ███    █▀   ███          ███    ███  
//   ▄███▄▄▄▄███▄▄   ▄███▄▄▄      ███          ███    ███  
//  ▀▀███▀▀▀▀███▀   ▀▀███▀▀▀      ███        ▀█████████▀   
//    ███    ███      ███    █▄   ███          ███         
//    ███    ███      ███    ███  ███▌    ▄    ███         
//    ███    █▀       ██████████  █████▄▄██   ▄████▀       

// navwalker class - https://github.com/wp-bootstrap/wp-bootstrap-navwalker

if (!class_exists('WP_Bootstrap_Navwalker')) :
	class WP_Bootstrap_Navwalker extends Walker_Nav_Menu {

		private $has_schema = false;

		public function __construct() {
			if (!has_filter('wp_nav_menu_args', [$this, 'add_schema_to_navbar_ul'])) {
				add_filter('wp_nav_menu_args', [$this, 'add_schema_to_navbar_ul']);
			}
		}

		public function start_lvl(&$output, $depth = 0, $args = null) {
			if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
				$t = '';
				$n = '';
			}
			else {
				$t = "\t";
				$n = "\n";
			}
			$indent = str_repeat($t, $depth);
			$classes = ['dropdown-menu'];
			$class_names = join(' ', apply_filters('nav_menu_submenu_css_class', $classes, $args, $depth));
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$labelledby = '';
			preg_match_all('/(<a.*?id=\"|\')(.*?)\"|\'.*?>/im', $output, $matches);
			if (end($matches[2])) {
				$labelledby = 'aria-labelledby="' . esc_attr(end($matches[2])) . '"';
			}
			$output .= "{$n}{$indent}<ul$class_names $labelledby>{$n}";
		}

		public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
			if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
				$t = '';
				$n = '';
			}
			else {
				$t = "\t";
				$n = "\n";
			}
			$indent = ($depth) ? str_repeat($t, $depth) : '';

			if (false !== strpos($args->items_wrap, 'itemscope') && false === $this->has_schema) {
				$this->has_schema = true;
				$args->link_before = '<span itemprop="name">' . $args->link_before;
				$args->link_after .= '</span>';
			}

			$classes = empty($item->classes) ? [] : (array) $item->classes;

			$split_on_spaces = function ($class) {
				return preg_split('/\s+/', $class);
			};
			$classes = $this->flatten(array_map($split_on_spaces, $classes));

			$linkmod_classes = [];
			$icon_classes = [];

			$classes = self::separate_linkmods_and_icons_from_classes($classes, $linkmod_classes, $icon_classes, $depth);
			$icon_class_string = join(' ', $icon_classes);

			$args = apply_filters('nav_menu_item_args', $args, $item, $depth);

			if ($this->has_children) {
				$classes[] = 'dropdown';
			}
			if (in_array('current-menu-item', $classes, true) || in_array('current-menu-parent', $classes, true)) {
				$classes[] = 'active';
			}

			$classes[] = 'menu-item-' . $item->ID;
			$classes[] = 'nav-item';
			$classes = apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth);
			$class_names = join(' ', $classes);
			$class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

			$id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
			$id = $id ? ' id="' . esc_attr($id) . '"' : '';

			$output .= $indent . '<li ' . $id . $class_names . '>';

			$atts = [];
			$atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
			$atts['target'] = !empty($item->target) ? $item->target : '';
			if ('_blank' === $item->target && empty($item->xfn)) {
				$atts['rel'] = 'noopener noreferrer';
			}
			else {
				$atts['rel'] = ! empty($item->xfn) ? $item->xfn : '';
			}

			if ( $this->has_children && 0 === $depth ) {
				$atts['href'] = '#';
				$atts['data-toggle'] = 'dropdown';
				$atts['aria-haspopup'] = 'true';
				$atts['aria-expanded'] = 'false';
				$atts['class'] = 'dropdown-toggle nav-link';
				$atts['id'] = 'menu-item-dropdown-' . $item->ID;
			}
			else {
				if (true === $this->has_schema) {
					$atts['itemprop'] = 'url';
				}

				$atts['href'] = ! empty($item->url) ? $item->url : '#';
				if ( $depth > 0 ) {
					$atts['class'] = 'dropdown-item';
				}
				else {
					$atts['class'] = 'nav-link';
				}
			}

			$atts['aria-current'] = $item->current ? 'page' : '';
			$atts = self::update_atts_for_linkmod_type($atts, $linkmod_classes);
			$atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

			$attributes = '';
			foreach ($atts as $attr => $value) {
				if (!empty($value)) {
					$value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$linkmod_type = self::get_linkmod_type($linkmod_classes);
			$item_output = isset($args->before) ? $args->before : '';

			if ('' !== $linkmod_type) {
				$item_output .= self::linkmod_element_open($linkmod_type, $attributes);
			}
			else {
				$item_output .= '<a' . $attributes . '>';
			}

			$icon_html = '';
			if (!empty($icon_class_string)) {
				$icon_html = '<i class="' . esc_attr($icon_class_string) . '" aria-hidden="true"></i> ';
			}

			$title = apply_filters('the_title', $item->title, $item->ID);
			$title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

			if (in_array('sr-only', $linkmod_classes, true)) {
				$title = self::wrap_for_screen_reader($title);
				$keys_to_unset = array_keys($linkmod_classes, 'sr-only', true);
				foreach ($keys_to_unset as $k) {
					unset($linkmod_classes[$k]);
				}
			}

			$item_output .= isset($args->link_before) ? $args->link_before . $icon_html . $title . $args->link_after : '';

			if ('' !== $linkmod_type) {
				$item_output .= self::linkmod_element_close($linkmod_type);
			}
			else {
				$item_output .= '</a>';
			}

			$item_output .= isset($args->after) ? $args->after : '';
			$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
		}

		public static function fallback($args) {
			if (!current_user_can('edit_theme_options')) {
				return;
			}

			$fallback_output = '';
			$show_container = false;
			if ($args['container']) {
				$allowed_tags = apply_filters('wp_nav_menu_container_allowedtags', ['div', 'nav']);
				if (is_string($args['container']) && in_array($args['container'], $allowed_tags, true)) {
					$show_container = true;
					$class = $args['container_class'] ? ' class="menu-fallback-container ' . esc_attr($args['container_class']) . '"' : ' class="menu-fallback-container"';
					$id = $args['container_id'] ? ' id="' . esc_attr($args['container_id']) . '"' : '';
					$fallback_output .= '<' . $args['container'] . $id . $class . '>';
				}
			}

			$class = $args['menu_class'] ? ' class="menu-fallback-menu ' . esc_attr($args['menu_class']) . '"' : ' class="menu-fallback-menu"';
			$id = $args['menu_id'] ? ' id="' . esc_attr($args['menu_id']) . '"' : '';
			$fallback_output .= '<ul' . $id . $class . '>';
			$fallback_output .= '<li class="nav-item"><a href="' . esc_url(admin_url('nav-menus.php')) . '" class="nav-link" title="' . esc_attr__('Add a menu', 'wp-bootstrap-navwalker') . '">' . esc_html__('Add a menu', 'wp-bootstrap-navwalker') . '</a></li>';
			$fallback_output .= '</ul>';

			if ($show_container) {
				$fallback_output .= '</' . $args['container'] . '>';
			}

			if (array_key_exists('echo', $args) && $args['echo'] ) {
				echo $fallback_output;
			}
			else {
				return $fallback_output;
			}
		}

		public function add_schema_to_navbar_ul($args) {
			$wrap = $args['items_wrap'];
			if (strpos($wrap, 'SiteNavigationElement') === false) {
				$args['items_wrap'] = preg_replace('/(>).*>?\%3\$s/', ' itemscope itemtype="http://www.schema.org/SiteNavigationElement"$0', $wrap);
			}

			return $args;
		}

		private function separate_linkmods_and_icons_from_classes($classes, &$linkmod_classes, &$icon_classes, $depth) {
			foreach ($classes as $key => $class) {
				if (preg_match('/^disabled|^sr-only/i', $class)) {
					$linkmod_classes[] = $class;
					unset($classes[$key]);
				}
				elseif (preg_match('/^dropdown-header|^dropdown-divider|^dropdown-item-text/i', $class) && $depth > 0) {
					$linkmod_classes[] = $class;
					unset($classes[$key]);
				}
				elseif (preg_match( '/^fa-(\S*)?|^fa(s|r|l|b)?(\s?)?$/i', $class)) {
					$icon_classes[] = $class;
					unset($classes[$key]);
				}
				elseif (preg_match( '/^glyphicon-(\S*)?|^glyphicon(\s?)$/i', $class)) {
					$icon_classes[] = $class;
					unset($classes[$key]);
				}
			}

			return $classes;
		}

		private function get_linkmod_type($linkmod_classes = []) {
			$linkmod_type = '';
			if (!empty($linkmod_classes)) {
				foreach ($linkmod_classes as $link_class) {
					if (!empty($link_class)) {
						if ('dropdown-header' === $link_class) {
							$linkmod_type = 'dropdown-header';
						}
						elseif ('dropdown-divider' === $link_class) {
							$linkmod_type = 'dropdown-divider';
						}
						elseif ('dropdown-item-text' === $link_class) {
							$linkmod_type = 'dropdown-item-text';
						}
					}
				}
			}
			return $linkmod_type;
		}

		private function update_atts_for_linkmod_type($atts = [], $linkmod_classes = []) {
			if (!empty($linkmod_classes)) {
				foreach ($linkmod_classes as $link_class) {
					if (!empty($link_class)) {
						if ('sr-only' !== $link_class) {
							$atts['class'] .= ' ' . esc_attr($link_class);
						}
						if ('disabled' === $link_class) {
							$atts['href'] = '#';
							unset($atts['target']);
						}
						elseif ('dropdown-header' === $link_class || 'dropdown-divider' === $link_class || 'dropdown-item-text' === $link_class) {
							unset($atts['href']);
							unset($atts['target']);
						}
					}
				}
			}
			return $atts;
		}

		private function wrap_for_screen_reader($text = '') {
			if ($text) {
				$text = '<span class="sr-only">' . $text . '</span>';
			}
			return $text;
		}

		private function linkmod_element_open($linkmod_type, $attributes = '') {
			$output = '';
			if ('dropdown-item-text' === $linkmod_type) {
				$output .= '<span class="dropdown-item-text"' . $attributes . '>';
			}
			elseif ('dropdown-header' === $linkmod_type) {
				$output .= '<span class="dropdown-header h6"' . $attributes . '>';
			}
			elseif ('dropdown-divider' === $linkmod_type) {
				$output .= '<div class="dropdown-divider"' . $attributes . '>';
			}
			return $output;
		}

		private function linkmod_element_close($linkmod_type) {
			$output = '';
			if ('dropdown-header' === $linkmod_type || 'dropdown-item-text' === $linkmod_type) {
				$output .= '</span>';
			}
			elseif ('dropdown-divider' === $linkmod_type) {
				$output .= '</div>';
			}
			return $output;
		}

		public function flatten($array) {
			$result = [];
			foreach ($array as $element) {
				if (is_array( $element)) {
					array_push($result, ...$this->flatten($element));
				}
				else {
					$result[] = $element;
				}
			}
			return $result;
		}
	}
endif;

// minifying functions

function minify_css($input) {
	if (trim($input) === '') {
		return $input;
	}
	return preg_replace(
		[
			// remove comment(s)
			'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
			// remove unused white-space(s)
			'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~]|\s(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
			// replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
			'#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
			// replace `:0 0 0 0` with `:0`
			'#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
			// replace `background-position:0` with `background-position:0 0`
			'#(background-position):0(?=[;\}])#si',
			// replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
			'#(?<=[\s:,\-])0+\.(\d+)#s',
			// minify string value
			'#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
			'#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
			// minify HEX color code
			'#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
			// replace `(border|outline):none` with `(border|outline):0`
			'#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
			// remove empty selector(s)
			'#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
		],
		[
			'$1',
			'$1$2$3$4$5$6$7',
			'$1',
			':0',
			'$1:0 0',
			'.$1',
			'$1$3',
			'$1$2$4$5',
			'$1$2$3',
			'$1:0',
			'$1$2'
		],
	$input);
}

function minify_js($input) {
	if (trim($input) === '') {
		return $input;
	}
	return preg_replace(
		[
			// remove comment(s)
			'#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
			// remove white-space(s) outside the string and regex
			'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
			// remove the last semicolon
			'#;+\}#',
			// minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
			'#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
			// --ibid. From `foo['bar']` to `foo.bar`
			'#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
		],
		[
			'$1',
			'$1$2',
			'}',
			'$1$3',
			'$1.$3'
		],
	$input);
}

// EOF