<?php
    require_once 'MDB2.php';

    define ("DB_HOST", "localhost");
    define ("DB_USER", "multichannel");
    define ("DB_PASSWORD", "multichannel");
    define ("DB_NAME", "multichannel");

    function connectToDB()
    {
        $dsn = array(
                    'phptype' => 'mysql',
                    'username' => DB_USER,
                    'password' => DB_PASSWORD,
                    'hostspec' => DB_HOST,
                    'database' => DB_NAME
                );

        $options = array(
                    'debug' => 2,
                    'portability' => MDB2_PORTABILITY_ALL
                );


        $mdb2 =& MDB2::connect($dsn, $options);		
        if (PEAR::isError($mdb2)) {
            die ($mdb2->getMessage());
        }
        $mdb2->loadModule('Extended');
        return $mdb2;
    }

    $dbconn = connectToDB();		
?>