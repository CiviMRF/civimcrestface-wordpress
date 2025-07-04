<?php

/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

namespace CMRF\Wordpress\Admin;

class AdminPage {
  protected static $singleton = FALSE;

  private function __construct() {
    add_action('admin_init', [$this, 'clear_cache']);
    add_action('admin_menu', [$this, 'admin_menu']);
    add_filter('plugin_action_links', [$this, 'plugin_action_links'], 10, 2);
  }

  public static function init() {
    AdminPage::singleton();
  }

  /**
   * @return bool|AdminPage
   */
  public static function singleton() {
    if (!self::$singleton) {
      self::$singleton = new AdminPage();
    }
    return self::$singleton;
  }

  public function admin_menu() {
    add_options_page(__('CiviMRF Settings', 'wpcmrf'), __('CiviCRM McRestFace Connections', 'wpcmrf'), 'manage_options', 'wpcmrf_admin', array($this, 'display_page'));
    add_options_page(__('CiviMRF Call Log', 'wpcmrf'), __('CiviCRM McRestFace Log', 'wpcmrf'), 'manage_options', 'wpcmrf_calllog', array($this, 'display_logpage'));
  }

  public function plugin_action_links( $links, $file ) {
    if ($file == plugin_basename(WPCMRF_PLUGIN_DIR) . '/wpcmrf.php') {
      $link = add_query_arg(['page' => 'wpcmrf_admin'], admin_url('options-general.php'));
      $links[] = sprintf(
        '<a href="%1$s">%2$s</a>',
        esc_url($link),
        esc_html__('Settings')
      );
    }
    return $links;
  }

  public function clear_cache() {
    global $wpdb;
    if (current_user_can('manage_options')) {
      if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'wpcmrf_calllog') {
        $action = $_REQUEST['action'] ?? '';
        switch ($action) {
          case 'clear':
            $wpdb->query($wpdb->prepare("DELETE FROM `" . $wpdb->get_blog_prefix() . "wpcmrf_core_call`"));
            wp_redirect(menu_page_url('wpcmrf_calllog', FALSE));
            exit();
            break;
        }
      }
    }
  }

  public function display_logpage() {
    global $wpdb;
    $action = $_REQUEST[ 'action' ] ?? '';
    switch ($action) {
      case 'clear':
        $wpdb->query($wpdb->prepare("DELETE FROM `" . $wpdb->get_blog_prefix() . "wpcmrf_core_call`"));
      default:
        $calls = $result = $wpdb->get_results("SELECT * FROM {$wpdb->get_blog_prefix()}wpcmrf_core_call ORDER BY `create_date` DESC");
        self::view('calllog', ['calls' => $calls]);
    }
  }

  public function display_page() {
    global $wpdb;
    $action = $_REQUEST[ 'action' ] ?? '';
    switch ($action) {
      case 'delete':
        $wpdb->delete($wpdb->get_blog_prefix() . 'wpcivimrf_profile', ["id" => (int) $_REQUEST[ 'profile_id' ]]);
        $profiles = $result = $wpdb->get_results("SELECT * FROM {$wpdb->get_blog_prefix()}wpcivimrf_profile");
        self::view('profiles', ['profiles' => $profiles]);
        break;
      case 'save':
        $profile[ 'label' ] = sanitize_text_field($_POST[ 'label' ]);
        $profile[ 'connector' ] = sanitize_text_field($_POST[ 'connector' ]);
        $profile[ 'url' ] = sanitize_text_field($_POST[ 'url' ]);
        $profile[ 'urlV4' ] = sanitize_text_field($_POST[ 'urlV4' ]);
        $profile[ 'site_key' ] = sanitize_text_field($_POST[ 'site_key' ]);
        $profile[ 'api_key' ] = sanitize_text_field($_POST[ 'api_key' ]);
        if (!empty($_REQUEST[ 'profile_id' ])) {
          $profile_id = (int) $_REQUEST[ 'profile_id' ];
          $wpdb->update($wpdb->get_blog_prefix() . 'wpcivimrf_profile', $profile, ["id" => $profile_id]);
        }
        else {
          $wpdb->insert($wpdb->get_blog_prefix() . 'wpcivimrf_profile', $profile);
          $profile_id = $wpdb->insert_id;
        }
        // If validation fails, reshow form, otherwise go to profile list
        if (!self::validate($profile_id, FALSE)) {
          $profile = $wpdb->get_row("SELECT * FROM {$wpdb->get_blog_prefix()}wpcivimrf_profile WHERE id = {$profile_id}");
          $core = wpcmrf_get_core();
          $connectors = $core->getRegisteredConnectors();
          self::view('form', ['connectors' => $connectors, 'profile' => $profile]);
        }
        else {
          $profiles = $wpdb->get_results("SELECT * FROM {$wpdb->get_blog_prefix()}wpcivimrf_profile");
          self::view('profiles', ['profiles' => $profiles]);
        }
        break;
      case 'new':
        $core = wpcmrf_get_core();
        $connectors = $core->getRegisteredConnectors();
        self::view('form', ['connectors' => $connectors]);
        break;
      case 'edit':
        $id = (int) $_REQUEST[ 'profile_id' ];
        $profile = $wpdb->get_row("SELECT * FROM {$wpdb->get_blog_prefix()}wpcivimrf_profile WHERE id = {$id}");
        $core = wpcmrf_get_core();
        $connectors = $core->getRegisteredConnectors();
        self::view('form', ['connectors' => $connectors, 'profile' => $profile]);
        break;
      default:
        $profiles = $wpdb->get_results("SELECT * FROM {$wpdb->get_blog_prefix()}wpcivimrf_profile");
        self::view('profiles', ['profiles' => $profiles]);
    }
  }

  public static function view( $name, array $args = array() ) {
    $args = apply_filters('wpcmrf_view_arguments', $args, $name);
    foreach ($args as $key => $val) {
      $$key = $val;
    }
    load_plugin_textdomain('wpcmrf');
    $file = WPCMRF_PLUGIN_DIR . 'views/' . $name . '.php';
    include($file);
  }

  /**
   * Validate connection configuration
   *
   * @param int $profile_id
   * @param bool $show_message - if true, return html message, else boolean
   *
   * @return string|bool
   */
  public static function validate( $profile_id, $show_message = TRUE ) {
    if (!$profile_id) {
      return;
    }
    global $wpdb;
    $profile = $wpdb->get_row("SELECT * FROM {$wpdb->get_blog_prefix()}wpcivimrf_profile WHERE id = {$profile_id}");
    // There's nothing special about System.getcount - anything call will do
    $call = wpcmrf_api("Entity", "get", [], [], $profile_id);
    $reply = $call->getReply();
    $error = $reply[ 'error_message' ] ?? NULL;
    if (!$error) {
      $error = $reply[ 'is_error' ] ? 'ERROR: check api URL' : '';
    }
    if (empty($error) && !empty($profile->urlV4)) {
      $call = wpcmrf_api("Entity", "get", [], [], $profile_id, [], 4);
      $reply = $call->getReply();
      $error = $reply[ 'error_message' ] ?? NULL;
      if (!$error) {
        $error = isset($reply[ 'is_error' ]) ? 'ERROR: Check Rest Url (v4)' : '';
      } else {
        $error .= ' :: Check Rest Url (v4)';
      }
    }

    if ($show_message) {
      return $error ?
        '<div class="notice notice-error">' . __('Validation failed', 'wpcmrf') . ": " . $error . '</div>' :
        '<div class="notice notice-success">' . __('Validation successful', 'wpcmrf') . '</div>';
    }
    else {
      return empty($error);
    }
  }
}
