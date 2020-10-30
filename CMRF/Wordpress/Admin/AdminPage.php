<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

namespace CMRF\Wordpress\Admin;

class AdminPage {

  protected static $singleton = false;

  private function __construct() {
    add_action( 'admin_menu', [$this, 'admin_menu'] );
    add_filter( 'plugin_action_links', [$this, 'plugin_action_links'], 10, 2 );
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
    add_options_page( __('CiviMRF Settings', 'wpcmrf'), __('CiviCRM McRestFace Connections', 'wpcmrf'), 'manage_options', 'wpcmrf_admin', array($this, 'display_page' ) );
    add_options_page( __('CiviMRF Call Log', 'wpcmrf'), __('CiviCRM McRestFace Log', 'wpcmrf'), 'manage_options', 'wpcmrf_calllog', array($this, 'display_logpage' ) );
  }

  public function plugin_action_links( $links, $file ) {
    if ($file == plugin_basename(WPCMRF_PLUGIN_DIR) . '/wpcmrf.php') {
      $link = add_query_arg( [ 'page' => 'wpcmrf_admin' ], admin_url( 'options-general.php' ) );
      $links[] = sprintf(
        '<a href="%1$s">%2$s</a>',
        esc_url( $link ),
        esc_html__( 'Settings' )
      );
    }
    return $links;
  }

  public function display_logpage() {
    global $wpdb;
    $action = $_REQUEST['action'] ?? '';
    switch($action) {
      case 'clear':
        $wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."wpcmrf_core_call`"));
        $calls = $result =$wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpcmrf_core_call");
        self::view( 'calllog', ['calls' => $calls] );
        break;
      default:
        $calls = $result =$wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpcmrf_core_call");
        self::view( 'calllog', ['calls' => $calls] );
    }
  }

  public function display_page() {
    global $wpdb;
    $action = $_REQUEST['action'] ?? '';
    switch($action) {
      case 'delete':
        $wpdb->delete($wpdb->prefix.'wpcivimrf_profile', ["id" => $_REQUEST['profile_id']]);
        $profiles = $result =$wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpcivimrf_profile");
        self::view( 'profiles', ['profiles' => $profiles] );
        break;
      case 'save':
        $profile['label'] = $_POST['label'];
        $profile['connector'] = $_POST['connector'];
        $profile['url'] = $_POST['url'];
        $profile['site_key'] = $_POST['site_key'];
        $profile['api_key'] = $_POST['api_key'];
        if (!empty($_REQUEST['profile_id'])) {
          $wpdb->update($wpdb->prefix . 'wpcivimrf_profile', $profile, ["id" => $_REQUEST['profile_id']]);
        } else {
          $wpdb->insert($wpdb->prefix . 'wpcivimrf_profile', $profile);
        }
        $profiles =$wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpcivimrf_profile");
        self::view( 'profiles', ['profiles' => $profiles] );
        break;
      case 'new':
        $core = wpcmrf_get_core();
        $connectors = $core->getRegisteredConnectors();
        self::view( 'form', ['connectors' => $connectors]);
        break;
      case 'edit':
        $id = esc_sql($_REQUEST['profile_id']);
        $profile =$wpdb->get_row("SELECT * FROM {$wpdb->prefix}wpcivimrf_profile WHERE id = {$id}");
        $core = wpcmrf_get_core();
        $connectors = $core->getRegisteredConnectors();
        self::view( 'form', ['connectors' => $connectors, 'profile' => $profile]);
        break;
      default:
        $profiles =$wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpcivimrf_profile");
        self::view( 'profiles', ['profiles' => $profiles] );
    }
  }

  public static function view( $name, array $args = array() ) {
    $args = apply_filters( 'wpcmrf_view_arguments', $args, $name );
    foreach ( $args AS $key => $val ) {
      $$key = $val;
    }
    load_plugin_textdomain( 'wpcmrf' );
    $file = WPCMRF_PLUGIN_DIR . 'views/'. $name . '.php';
    include($file);
  }

}
