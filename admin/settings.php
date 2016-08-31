<?php

/*******************************************************************************
 * settings.php
 * Contains settings used for the LifeSaver admin site.
 /*****************************************************************************/

// My personal Google Key.  Remove for production.
define('GOOGLE_GEOAPIKEY', "AIzaSyBpN0BcxFtmFKVteFgwsR6w5LQbz3NPioY");

// Local MYSQL database settings
define('LOCALDB_USERNAME', 'lsadmin');
define('LOCALDB_PASSWORD', 'k8U7Bn#maTrAq@FULv6%bhVqQLR*CpGz');
define('LOCALDB_DBNAME', 'lifesaver');

// 30 life saver professionals per page.
define('ADMIN_RESULTSPERPAGE', 30);

// Sessions expire after half an hour of inactivity.
define('SESSION_EXPIRETIME', 1800);
