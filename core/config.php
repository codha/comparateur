<?php 

# one simple basic config file, dont use function to get config information each time you need it, this will slow the application at 200% !

$global_app_config['b']  = 1; #bold

$global_app_config['u']  = 2; #underlined

$global_app_config['link']  = 3; 

$global_app_config['ub']  = 4; # underlined + bold

$global_app_config['i']  = 5; # italic

$global_app_config_keys = array_keys($global_app_config);