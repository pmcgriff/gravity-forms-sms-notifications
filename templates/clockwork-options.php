<div class="wrap">
  <div class="left-content">
      
    <div class="icon32"><img src="<?php echo plugins_url( 'images/logo_32px_32px.png', dirname( __FILE__ ) ); ?>" /></div>
    <h2>Clockwork SMS Options</h2>
    
    <?php $errors = get_settings_errors(); ?>
    <?php if( isset( $errors ) ) { ?>
      <?php foreach( $errors as $e ) { ?>
      <div id="message" class="<?php print $e['type']; ?>">
        <p><?php _e( $e['message'] ) ?></p>
      </div>    
      <?php } ?>
    <?php } ?>
    
    <form method="post" action="options.php">
    
    <?php
    settings_fields( 'clockwork_options' );
    do_settings_sections( 'clockwork' );
    settings_errors('clockwork_options');
    submit_button();
    ?>
    
    </form>
    
  </div>
  
  <div class="right-content">
    <div class="innerbox">
      <h2>Get Support</h2>
      <p>If you're having problems with any Clockwork plugin, you can get support here.</p>
      
      <p><a href="<?php echo self::SUPPORT_URL; ?>" class="button">Contact Clockwork</a></p>
    </div>
      
    <img src="<?php echo plugins_url( 'images/badrobot.png', dirname( __FILE__ ) ); ?>" />
  </div>
</div>