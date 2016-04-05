<?php return array(
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
        'priority' => '7',
    ),
    'filesdir'  => '/tine20/files',
    'tmpdir' => '/tine20/tmp',
  );
?>