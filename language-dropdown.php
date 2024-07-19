<?php
/*
   * Plugin Name:       Language Dropdown
   * Description:       Adds a customizable language dropdown menu via shortcode with Bootstrap styling.
   * Version:           1.0
   * Requires at least: 5.2
   * Requires PHP:      7.2
   * Author:            wpjdev
   * License:           GPL v2 or later
   * Text Domain:       language-dropdown
   * Domain Path:       /languages
*/

// Include admin page
require_once plugin_dir_path(__FILE__) . 'admin-page.php';

// Add shortcode
function language_dropdown_shortcode() {
   $languages = get_option('language_dropdown_entries', array());
   
   if (empty($languages)) {
      return ''; // Return empty if no languages are set
   }

   $current_language = isset($languages[0]) ? $languages[0] : array('name' => 'English', 'flag' => '', 'code' => 'en');

   ob_start();
   ?>
   <div class="ml-auto">
      <div class="ex-switch-lang mr-3 ml-auto">
         <button class="ex-btn ex-switch-lang-btn" id="switch-lang">
               <?php 
               $flag_url = wp_get_attachment_image_url($current_language['flag'], 'thumbnail');
               if ($flag_url):
               ?>
               <img src="<?php echo esc_url($flag_url); ?>" alt="<?php echo esc_attr($current_language['name']); ?>" />
               <?php endif; ?>
               <span class="mx-2 d-none d-md-inline"><?php echo esc_html($current_language['name']); ?></span>
               <i class="fa fa-angle-down d-none d-md-inline"></i>
         </button>
         <ul class="ex-switch-lang-options">
               <?php foreach ($languages as $lang): 
                  $flag_url = wp_get_attachment_image_url($lang['flag'], 'thumbnail');
               ?>
                  <li>
                     <a href="<?php echo esc_url($lang['url']); ?>" <?php echo ($lang === $current_language) ? 'class="active"' : ''; ?>>
                           <?php if ($flag_url): ?>
                           <img src="<?php echo esc_url($flag_url); ?>" alt="<?php echo esc_attr($lang['name']); ?>" />
                           <?php endif; ?>
                           <span><?php echo esc_html($lang['name']); ?></span>
                           <i class="fa fa-arrow-right"></i>
                     </a>
                  </li>
               <?php endforeach; ?>
         </ul>
      </div>
   </div>
   <?php
   return ob_get_clean();
}
add_shortcode('language_dropdown', 'language_dropdown_shortcode');

// Enqueue styles and scripts
function language_dropdown_enqueue_scripts() {
   wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
   wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');
   wp_enqueue_style('language-dropdown-style', plugin_dir_url(__FILE__) . 'css/style.css');
   
   wp_enqueue_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js', array('jquery'), null, true);
   wp_enqueue_script('language-dropdown-script', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'language_dropdown_enqueue_scripts');

// Add settings link on plugin page
function language_dropdown_settings_link($links) {
   $settings_link = '<a href="options-general.php?page=language-dropdown-settings">Settings</a>';
   array_unshift($links, $settings_link);
   return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'language_dropdown_settings_link');

// Activation hook
function language_dropdown_activate() {
   // Add default options if they don't exist
   if (!get_option('language_dropdown_entries')) {
      $default_languages = array(
         array(
               'name' => 'English',
               'code' => 'en',
               'flag' => '',
               'url' => home_url('/en/')
         )
      );
      update_option('language_dropdown_entries', $default_languages);
   }
}
register_activation_hook(__FILE__, 'language_dropdown_activate');

// Deactivation hook
function language_dropdown_deactivate() {
   // Cleanup tasks if needed
}
register_deactivation_hook(__FILE__, 'language_dropdown_deactivate');

function language_dropdown_compatibility_check() {
   $incompatible_plugins = array(
      'incompatible-plugin-1/incompatible-plugin-1.php',
      'incompatible-plugin-2/incompatible-plugin-2.php'
   );

   $active_plugins = get_option('active_plugins');
   $incompatible_active = array_intersect($incompatible_plugins, $active_plugins);

   if (!empty($incompatible_active)) {
      add_action('admin_notices', 'language_dropdown_compatibility_notice');
   }

   // Check for minimum WordPress version
   global $wp_version;
   if (version_compare($wp_version, '5.0', '<')) {
      add_action('admin_notices', 'language_dropdown_wp_version_notice');
   }
}
add_action('admin_init', 'language_dropdown_compatibility_check');

function language_dropdown_compatibility_notice() {
   echo '<div class="error"><p>The Language Dropdown plugin may not work correctly with some of your active plugins. Please check for compatibility.</p></div>';
}

function language_dropdown_wp_version_notice() {
   echo '<div class="error"><p>The Language Dropdown plugin requires WordPress version 5.0 or higher. Please upgrade WordPress to use this plugin.</p></div>';
}