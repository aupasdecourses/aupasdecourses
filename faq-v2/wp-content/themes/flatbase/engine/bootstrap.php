<?php

/****
  _ __  _  ___  ___ 
 | '_ \| |/ __|/ _ \
 | | | | | (__   __/ themes.com
 |_| |_|_|\___|\___|

****/

require_once( 'admin/init.php' );

nice_constants();

// FRAMEWORK STUFF
nice_loader ( get_template_directory() . '/engine/admin/functions.php' );
nice_loader ( get_template_directory() . '/engine/admin/' );
nice_loader ( get_template_directory() . '/engine/theming/' );


// THEME STUFF
nice_loader ( get_template_directory() . '/includes/' );
nice_loader ( get_template_directory() . '/includes/custom-post-types/' );


?>