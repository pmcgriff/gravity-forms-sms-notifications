<?php
/**
 * GravityFormsSMS
 *
 * @package GravityFormsSMS
 **/
class GravityFormsSMS {
  
  /**
   * Array of GravityForms
   *
   * @var array Array of GravityForms
   **/
  public $forms = array();

  /**
   * Constructor
   *
   * Initialize the administration panel and GravityForms filters
   *
   * @return    void
	 * @since	    0.1
   **/
	public function __construct() {
		if( function_exists( 'add_action' ) ) 
    {
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
      add_action( 'gform_post_submission', array( &$this, 'do_form_processing' ), 10, 2 ); 
    }

		if( function_exists( 'add_filter' ) ) 
    {
      add_filter( 'gform_addon_navigation', array( &$this, 'add_menu_item' ) );     
    }
    
    // Setup all forms into a local variable
    $active = RGForms::get( 'active' ) == '' ? null : RGForms::get( 'active' );
    $this->forms = RGFormsModel::get_forms( $active, 'title' );
  }
  
  /**
   * Add the Mediaburst menu item under the Forms section
   *
   * @return    array Menu items with appended Mediaburst menu item 
	 * @since	    0.1
   **/
  public function add_menu_item( $menu_items ) {
    $menu_items[] = array( 
      'name' => 'sms_notifications',
      'label' => 'Mediaburst',
      'callback' => array( &$this, 'notifications_handler' )
    );
    return $menu_items;
  }

  /**
   * Hook into the Wordpress admin panel initializer
   *
   * Set up the settings page
   *
   * @return    void
	 * @since	    0.1
   **/
  public function admin_init() {
  	register_setting( 'gravityforms_sms', 'gravityforms_sms', array( &$this, 'validate_options' ) );
  
  	add_settings_section( 'gravityforms_sms_mb', __('Mediaburst Account Settings', 'gravityforms_sms'), array( &$this, 'opt_section_mediaburst' ), 'gravityforms_sms' );
  	add_settings_field( 'gravityforms_sms_username', __('Username', 'gravityforms_sms'), array( &$this, 'opt_value_username' ), 'gravityforms_sms', 'gravityforms_sms_mb' );
  	add_settings_field( 'gravityforms_sms_password', __('Password', 'gravityforms_sms'), array( &$this, 'opt_value_password' ), 'gravityforms_sms', 'gravityforms_sms_mb' );
    
  	add_settings_section( 'gravityforms_sms_mc', __('Message Content', 'gravityforms_sms'), array( &$this, 'opt_section_message' ), 'gravityforms_sms' );
  	add_settings_field( 'gravityforms_sms_username', __('Message', 'gravityforms_sms'), array( &$this, 'opt_value_message' ), 'gravityforms_sms', 'gravityforms_sms_mc' );
  }

  /**
   * Add the header text to the account settings block
   *
   * @return    void
	 * @since	    0.1
   **/
  public function opt_section_mediaburst() {
  	echo '<p>' . __('To use the SMS notifications you need a <a href="http://www.mediaburst.co.uk/api/?utm_source=wordpress&utm_medium=plugin&utm_campaign=gravityforms-sms">Mediaburst SMS API</a> account.', 'gravityforms_sms') . '</p>';
    
    $credit = $this->get_sms_credit();
    if( isset( $credit ) ) 
    {
      echo '<p>' . __('SMS Available', 'gravityforms_sms') . ': ' . $credit;
      echo '&nbsp;&nbsp;&nbsp;<a href="https://smsapi.mediaburst.co.uk/" target="_blank" class="button-secondary">' . __('Buy Messages', 'gravityforms_sms') . '</a></p>';    
    }      
  }
  
  /**
   * Add the header text to the message content block
   *
   * @return    void
	 * @since	    0.1
   **/
  public function opt_section_message() {
    echo '<p>' . __('You can change the SMS message content below. <strong>%form%</strong> will be replaced with the form name.', 'gravityforms_sms') . '</p>';
  }

  /**
   * Render input field for username
   *
   * @return    void
	 * @since	    0.1
   **/
  public function opt_value_username() {
  	$options = get_option( 'gravityforms_sms' );
  	echo '<input id="gravityforms_sms_username" name="gravityforms_sms[username]" size="40" type="text" value="' . $options['username'] . '" style="padding: 3px;" />';
  }
  
  /**
   * Render input field for password
   *
   * @return    void
	 * @since	    0.1
   **/
  public function opt_value_password() {
  	$options = get_option('gravityforms_sms');
  	echo '<input id="gravityforms_sms_password" name="gravityforms_sms[password]" size="40" type="password" value="' . $options['password'] . '" style="padding: 3px;" />';
  }
  
  /**
   * Render input field for message content
   *
   * @return    void
	 * @since	    0.1
   **/
  public function opt_value_message() {
  	$options = get_option('gravityforms_sms');
    
    if( !$options['message'] )
    {
      $options['message'] = __('The contact form %form% was submitted on your website.', 'gravityforms_sms');
    }
    
  	echo '<textarea id="gravityforms_sms_message" name="gravityforms_sms[message]" style="padding: 3px;" rows="4" cols="60">' . $options['message'] . '</textarea>';
  }

  /**
   * Display the administration form
   *
   * @return    void
	 * @since	    0.1
   **/
  public function notifications_handler() {
    echo '<link rel="stylesheet" href="' . GFCommon::get_base_url() . '/css/admin.css" />';
    echo '<div class="wrap">';
    echo '<div class="icon32" id="gravity-notification-icon"><br></div>';
    echo '<h2>' . __('Mediaburst SMS Notifications', 'gravityforms_sms') . '</h2>';
  
    echo '<form method="post" action="options.php">';

  	settings_fields('gravityforms_sms');
  	do_settings_sections('gravityforms_sms');
        
    echo '<h3>' . __('Forms', 'gravityforms_sms') . '</h3>';
    
    echo '<p>' . __('Numbers should be entered in international format, e.g. 447909123456', 'gravityforms_sms') . '</p>';
    
    echo '<table class="widefat fixed" cellspacing="0">
    <thead>
    <tr>
    <th scope="col" class="manage-column">Form Name</th>
    <th scope="col" class="manage-column">Send To Number</th>
    <th scope="col" class="manage-column" width="15%">Active</th>
    </tr>
    </thead>

    <tfoot>
    <tr>
    <th scope="col" class="manage-column">Form Name</th>
    <th scope="col" class="manage-column">Send To Number</th>
    <th scope="col" class="manage-column" width="15%">Active</th>
    </tr>
    </tfoot>';
    
    if( empty( $this->forms ) )
    { 
      echo '<tbody class="list:user user-list">
      <tr>
      <td colspan="3" style="padding: 20px;">
      To get started, please add some forms.
      </td>
      </tr>
      </tbody>';
    }
    else
    {
      echo '<tbody class="list:user user-list">';
      foreach( $this->forms as $form )
      {        
        $meta = RGFormsModel::get_form_meta( $form->id );      
        $active = $meta['mediaburst_active'];
        $to = $meta['mediaburst_to'];
        
        echo '<tr>
          <td style="padding: 10px 8px;"><p style="padding-top: 3px">' . $form->title . '</p></td>';
        
        echo '<td style="padding: 10px 8px;"><input type="text" size="40" maxlength="13" value="' . $to . '" name="to[' . $form->id . ']" style="padding: 3px;" /></td>';
        
        if( $active == '1' )
        {
          echo '<td style="padding: 10px 8px;"><input type="checkbox" value="1" name="active[' . $form->id . ']" checked="checked" /></td>';         
        }
        else
        {
          echo '<td style="padding: 10px 8px;"><input type="checkbox" value="1" name="active[' . $form->id . ']" /></td>';
        }
        
        echo '</tr>';
      }
      echo '</tbody>';      
    }
  
    echo '</table>';
            
    echo '<br /><input name="submit" type="submit" class="button-primary" value="' . __('Save Changes', 'gravityforms_sms') . '" />';
  
    echo '</form>';
  }
  
	/**
	 * Validate option fields
	 *
	 * @param	    $input Asssociative array of new option values
   * @return    Associative array of sanitised option values
	 * @since	    0.1
	 **/
	public function validate_options( $input ) {
		$options = get_option( 'gravityforms_sms' );
		$options['username'] = trim( $input['username'] );
		$options['password'] = trim( $input['password'] );
		$options['message'] = trim( $input['message'] );
    
    foreach( $this->forms as $form )
    {
      $meta = RGFormsModel::get_form_meta( $form->id );
            
      if( $_POST['active'][$form->id] == '1' )
      { 
        $meta['mediaburst_active'] = 1;
        $meta['mediaburst_to'] = $_POST['to'][$form->id];
      }
      else
      {
        $meta['mediaburst_active'] = 0;
      }
      
      RGFormsModel::update_form_meta( $form->id, $meta );
    }
        
		return $options;
	}
  
  public function do_form_processing( $entry, $form ) {
    $meta = RGFormsModel::get_form_meta( $entry['form_id'] );
    $active = $meta['mediaburst_active'];
    $to = $meta['mediaburst_to'];
        
    if( $active == '1' && !empty( $to ) )
    {
      try {
    		$options = get_option( 'gravityforms_sms' );
  			$sms = $this->get_mediaburst_sms( $options['username'], $options['password'] );
        
        $message = str_replace( '%form%', $meta['title'], $options['message'] );
  			$sms_result = $sms->Send( $to, $message );
  		}
      catch( Exception $e )
      {
        // There's not much we can do here
      }
    }
		
  }
  
	/**
	 * Get a mediaburstSMS object with sensible defaults set
	 * Saves having to set these defaults every time
	 *
	 * @return 	  mediaburstSMS Instance of a mediaburstSMS object
	 * @since	    0.1
	 **/
	protected function get_mediaburst_sms() {
		$sms_options = array(
			'long'          => true,
			'truncate'      => true,
			'http_class'    => 'WPWordPressMBHTTP',
	  );
		$options = get_option( 'gravityforms_sms' );
		return new WPmediaburstSMS( $options['username'], $options['password'], $sms_options );	
	}
  
	/**
	 * Get the number of SMS credits available for the current API user
   *
	 * @return 	  integer Number of SMS credits available
	 * @since	    0.1
	 **/  
  protected function get_sms_credit() {
		try {
  		$options = get_option('gravityforms_sms');
			$sms = $this->get_mediaburst_sms( $options['username'], $options['password'] );
			$sms_credit = $sms->CheckCredit();
		} catch( Exception $e ) {
			$sms_credit = null;
		}
    return $sms_credit;
  }
  
}
?>
