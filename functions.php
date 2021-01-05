<?php
/*
 *  Author: nullstep
 *  URL: nullstep.com
 */

/* no direct access */

defined('ABSPATH') or die('nope');

// ignore ips

define('_IGNORE', [
	// add ips here
]);

// theme api

define('_THEME', 'not_wp');

define('_ARGS', [
	'favicon_image' => [
		'type' => 'string',
		'default' => ''
	],
	'logo_image' => [
		'type' => 'string',
		'default' => ''
	],
	'header_image' => [
		'type' => 'string',
		'default' => ''
	],
	'navbar_colour' => [
		'type' => 'string',
		'default' => '#333333'
	]
]);

class _themeAPI {
	public function add_routes() {
		register_rest_route(_THEME . '-api/v1', '/settings', [
				'methods' => 'POST',
				'callback' => [$this, 'update_settings'],
				'args' => _themeSettings::args(),
				'permission_callback' => [$this, 'permissions']
			]
		);
		register_rest_route(_THEME . '-api/v1', '/settings', [
				'methods' => 'GET',
				'callback' => [$this, 'get_settings'],
				'args' => [],
				'permission_callback' => [$this, 'permissions']
			]
		);
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
	protected static $option_key = _THEME . '-settings';

	public static function args() {
		$args = _ARGS;
		foreach (_ARGS as $key => $val) {
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
		foreach (_ARGS as $key => $val) {
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
		foreach (_ARGS as $key => $val) {
			$defaults[$key] = $val['default'];
		}
		foreach ($settings as $i => $setting) {
			if (!array_key_exists($i, $defaults)) {
				unset($settings[$i]);
			}
		}
		update_option(self::$option_key, $settings);
	}
}

class _themeMenu {
	protected $slug = _THEME . '-menu';
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
			'dashicons-smiley',
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
				'url' => esc_url_raw(rest_url(_THEME . '-api/v1/settings')),
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
			#wpwrap {
				background: url(<?php echo $this->assets_url . '/not_wp.svg'; ?>) no-repeat;
			}
		</style>
		<h2>Theme Settings</h2>
		<p style="max-width:500px">Set your theme images...</p>
		<form id="_theme-form" method="post">
			<div class="form-block">
				<label for="favicon">
					Favicon Image:
				</label>
				<input id="favicon_image" type="text" name="favicon_image">
				<input data-id="favicon_image" type="button" class="button-primary choose-file-button" value="Select...">
			</div>
			<div class="form-block">
				<label for="logo_image">
					Logo Image:
				</label>
				<input id="logo_image" type="text" name="logo_image">
				<input data-id="logo_image" type="button" class="button-primary choose-file-button" value="Select...">
			</div>
			<div class="form-block">
				<label for="header">
					Header Image:
				</label>
				<input id="header_image" type="text" name="header_image">
				<input data-id="header_image" type="button" class="button-primary choose-file-button" value="Select...">
			</div>
			<div class="form-block-ns">
				<label for="navbar_colour">
					Navbar Colour:
				</label>
				<input id="navbar_colour" type="text" name="navbar_colour">
				<input data-id="navbar_colour" type="color" class="choose-colour-button" value="#000000">
			</div>
			<div>
				<?php submit_button(); ?>
			</div>
			<div id="feedback">
			</div>
		</form>
<?php
	}
}

// get setting

function getvalue($key, $echo = true) {
	if ($echo) {
		echo _themeSettings::get_settings()[$key];
	}
	else {
		return _themeSettings::get_settings()[$key];
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
	$form_fields['file_downloads'] = array(
		'label' => 'Downloads',
		'input' => 'text',
		'value' => get_post_meta($post->ID, 'file_downloads', true),
		'helps' => ''
	);
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

// fix content urls/classes etc

function fix_content($content) {
	$dom = new DOMDocument;
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
	return "\t\t\t\t\t" . str_replace("\n", '', $dom->saveHTML());
}

// remove crap

function remove_category_rel_from_category_list($thelist) {
	return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}

function remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action('wp_head', array(
		$wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
		'recent_comments_style'
	));
}

function remove_admin_bar() {
	return false;
}

function remove_block_library_css() {
	wp_dequeue_style('wp-block-library');
}

function remove_wp_jquery() {
	if (!is_admin()) {
		wp_deregister_script('jquery');
	}
}

// pagination

function pagination() {
	global $wp_query;
	$big = 999999999;
	echo paginate_links(array(
		'base' => str_replace($big, '%#%', get_pagenum_link($big)),
		'format' => '?paged=%#%',
		'current' => max(1, get_query_var('paged')),
		'total' => $wp_query->max_num_pages
	));
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

// shortcodes

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

function video_shortcode($atts = [], $content = null, $tag = '') {
	return '<div class="video"><iframe src="' . $content . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
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

	foreach($categories as $category) {
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

// clean up nav items

function nav_attributes_filter($var) {
	return is_array($var) ? array_intersect($var, ['current-menu-item', 'nav-item']) : '';
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

// actions

add_action('init', 'set_wp_options');
add_action('widgets_init', 'remove_recent_comments_style');
add_action('init', 'pagination');
add_action('init', 'remove_wp_jquery');
add_action('admin_init', 'flush_htaccess');
add_action('wp_enqueue_scripts', 'remove_block_library_css');
add_action('manage_posts_custom_column', 'posts_custom_column_views', 5, 2);
add_action('manage_pages_custom_column', 'pages_custom_column_views', 5, 2);
add_action('created_category', 'no_category_base_refresh_rules');
add_action('delete_category', 'no_category_base_refresh_rules');
add_action('edited_category', 'no_category_base_refresh_rules');
add_action('init', 'no_category_base_permastruct');

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

// filters

add_filter('widget_text', 'shortcode_unautop');
add_filter('the_category', 'remove_category_rel_from_category_list');
add_filter('the_excerpt', 'shortcode_unautop');
add_filter('show_admin_bar', 'remove_admin_bar');
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

// shortcodes

add_shortcode('inc', 'inc_shortcode');
add_shortcode('video', 'video_shortcode');

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

// EOF