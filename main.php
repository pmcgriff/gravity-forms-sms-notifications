<?php
class Clockwork_GravityForms_Plugin extends Clockwork_Plugin {  

  protected $plugin_name = 'Gravity Forms SMS';  
  protected $language_string = 'clockwork_gravityforms';
  protected $prefix = 'clockwork_gravityforms';
  protected $folder = '';
  
  /**
   * Array of GravityForms
   *
   * @var array Array of GravityForms
   **/
  public $forms = array();
  
  /**
   * Constructor: setup callbacks and plugin-specific options
   *
   * @author James Inman
   */
  public function __construct() {
    parent::__construct();
    
    // Set the plugin's Clockwork SMS menu to load the contact forms
    $this->plugin_callback = array( $this, 'clockwork_gravityforms' );    
    $this->plugin_dir = basename( dirname( __FILE__ ) );
    
    // Convert the old settings
    $this->convert_old_settings();
    
    // Setup all forms into a local variable
    if ( !class_exists( 'RGForms' ) ) {
      require_once( dirname( dirname( __FILE__ ) ) . '/gravityforms/gravityforms.php' );  
    }
    if ( !class_exists( 'RGFormsModel' ) ) {
      require_once( dirname( dirname( __FILE__ ) ) . '/gravityforms/forms_model.php' );  
    }
    $active = RGForms::get( 'active' ) == '' ? null : RGForms::get( 'active' );
    $this->forms = RGFormsModel::get_forms( $active, 'title' );
    
    // Options and callbacks
    add_action( 'gform_post_submission', array( &$this, 'do_form_processing' ), 10, 2 ); 
  }
  
  /**
   * Setup the admin navigation
   *
   * @return void
   * @author James Inman
   */
  public function setup_admin_navigation() {
    parent::setup_admin_navigation();
  }
  
  /**
   * Register the settings for this plugin
   *
   * @return void
   * @author James Inman
   */
  public function setup_admin_init() {
    parent::setup_admin_init();
    
  	register_setting( 'clockwork_gravityforms', 'clockwork_gravityforms', array( &$this, 'validate_options' ) );
    add_settings_section( 'clockwork_gravityforms', __('Default Settings', 'clockwork_gravityforms'), array( &$this, 'settings_header' ), 'clockwork_gravityforms' );
  	add_settings_field( 'default_to', __('Send To Number', 'clockwork_gravityforms'), array( &$this, 'settings_default_to' ), 'clockwork_gravityforms', 'clockwork_gravityforms' );
  }
  
  /**
   * Setup HTML for the admin <head>
   *
   * @return void
   * @author James Inman
   */
  public function setup_admin_head() {
    echo '<link rel="stylesheet" type="text/css" href="' . plugins_url( 'css/clockwork.css', __FILE__ ) . '">';
  }
  
  /**
   * Function to provide a callback for the main plugin action page
   *
   * @return void
   * @author James Inman
   */
  public function clockwork_gravityforms() {
    $this->render_template( 'gravityforms-options' );
  }
  
  /**
   * Check if username and password have been entered
   *
   * @return void
   * @author James Inman
   */
  public function get_existing_username_and_password() {
		$options = get_option( 'gravityforms_sms' );
    if( !is_array( $options ) ) {
      return false;
    }
    
		$options['username'] = trim( $input['username'] );
		$options['password'] = trim( $input['password'] );

    return array( 'username' => $options['username'], 'password' => $options['password'] );
  }
  
  /**
   * Output the header paragraph for the settings
   *
   * @return void
   * @author James Inman
   */
  public function settings_header() {
    echo '<p>' . __( 'Default settings are applied to all your forms unless you set more specific options below.', 'clockwork_gravityforms' ) . '</p>';
  }
  
  public function settings_default_to() {
  	$options = get_option( 'clockwork_gravityforms' );
  	echo '<input id="gravityforms_sms_username" name="clockwork_gravityforms[default_to]" size="40" type="text" value="' . $options['default_to'] . '" style="padding: 3px;" />';
  }
  
  public function validate_options( $input ) {
		$options = get_option( 'clockwork_gravityforms' );
		$options['default_to'] = trim( $input['default_to'] );
    
    foreach( $this->forms as $form )
    {
      $meta = RGFormsModel::get_form_meta( $form->id );
                  
      if( $_POST['active'][$form->id] == '1' )
      { 
        $meta['clockwork_active'] = 1;
        $meta['clockwork_to'] = $_POST['to'][$form->id];
        $meta['clockwork_message'] = $_POST['message'][$form->id];
      }
      else
      {
        $meta['clockwork_active'] = 0;
      }
      
      RGFormsModel::update_form_meta( $form->id, $meta );
    }
        
		return $options;
  }
  
  /**
   * Process the form
   *
   * @param string $entry Contact form entry to process
   * @param string $form Gravity Form to process
   * @return void
   * @author James Inman
   */
  public function do_form_processing( $entry, $form ) {
    $options = array_merge( get_option( 'clockwork_options' ), get_option( 'clockwork_gravityforms' ) );

    $meta = RGFormsModel::get_form_meta( $entry['form_id'] );
    $active = $meta['clockwork_active'];
    $phone = explode( ',', $meta['clockwork_to'] );
    $message = $meta['clockwork_message'];
    
    $message = str_replace( '%form%', $meta['title'], $message );
    $message = preg_replace( '/%([0-9]+\.?[0-9]*)%/e', '$entry["$1"]', $message );
    
    if( $active == '1' && !empty( $phone ) )
    {    
      try {
        $clockwork = new WordPressClockwork( $options['api_key'] );
        $messages = array();
        foreach( $phone as $to ) {
          $messages[] = array( 'from' => $options['from'], 'to' => $to, 'message' => $message );          
        }
        $result = $clockwork->send( $messages );
      } catch( ClockworkException $e ) {
        $result = "Error: " . $e->getMessage();
      } catch( Exception $e ) { 
        $result = "Error: " . $e->getMessage();
      }
    }
		
  }
  
  /**
   * Convert settings from v1.x of the plugin
   *
   * @return void
   * @author James Inman
   */
  public function convert_old_settings() {
    $options_to_delete = array( 'gravityforms_sms' );
    $old_options = get_option( 'gravityforms_sms' );
    
    foreach( $this->forms as $form )
    {
      $meta = RGFormsModel::get_form_meta( $form->id );
      $meta['clockwork_active'] = $meta['mediaburst_active'];
      $meta['clockwork_to'] = $meta['mediaburst_to'];
      if( isset( $old_options['message'] ) ) {
        $meta['clockwork_message'] = $old_options['message'];
      }
      unset( $meta['mediaburst_active'] );
      unset( $meta['mediaburst_to'] );
      
      RGFormsModel::update_form_meta( $form->id, $meta );
    }

    foreach( $options_to_delete as $option ) {
      delete_option( $option );
    }    
  }
  
}

$cp = new Clockwork_GravityForms_Plugin();
