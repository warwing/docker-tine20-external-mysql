<?php return array(
	// set 'count' equal zero to disable captcha, or set to number of invalid logins before request captcha.
    'captcha' => array('count'=>4),
    
    'database' => array(
        'host'          => '__TINE20_DB_HOST__',
        'dbname'        => 'tine20',
        'username'      => '__TINE20_DB_USER__',
        'password'      => '__TINE20_DB_PASS__',
        'adapter'       => 'pdo_mysql',
        'tableprefix'   => 'tine20_',
    ),
    'setupuser' => array(
        'username'      => '__TINE20_SETUP_USER__',
        'password'      => '__TINE20_SETUP_PASS__' 
    ),
    
    'caching' => array (
        'active' => true,
        'path' => '/tine20/cache',
        'lifetime' => 3600,
    ),
    
    'logger' => array (
        'active' => true,
        'filename' => '/tine20/log/tine20.log',
        'priority' => '5',
    ),
    // const EMERG   = 0;  // Emergency: system is unusable
    // const ALERT   = 1;  // Alert: action must be taken immediately
    // const CRIT    = 2;  // Critical: critical conditions
    // const ERR     = 3;  // Error: error conditions
    // const WARN    = 4;  // Warning: warning conditions
    // const NOTICE  = 5;  // Notice: normal but significant condition
    // const INFO    = 6;  // Informational: informational messages
    // const DEBUG   = 7;  // Debug: debug messages
    // const TRACE   = 8;  // Trace: trace messages (VERY verbose, WARNING: may contain passwords)
    'filesdir'  => '/tine20/files',
    'tmpdir' => '/tine20/tmp',
  );
?>
