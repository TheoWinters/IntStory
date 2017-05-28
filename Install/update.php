<?php
require( dirname(__FILE__) . '\..\tools.php' );

?>

<html>
<head>
<title>The Changing Mirror - Interactive Stories Updated</title>
</head>
<body>
<h1 align="center">The Changing Mirror - Interactive Stories Updater</h1>

<hr />

<p>Okay, can we connect to the DB?</p>
<?php OpenDatabase(); ?>

<p>That seems to have worked...</p>

<p>Lets make some tables!</p>
<?php

echo "<p>Adding <b>sessons</b></p>";

$Query = "CREATE TABLE IF NOT EXISTS `session_handler_table` (
    `id` varchar(255) NOT NULL,
    `data` mediumtext NOT NULL,
    `timestamp` int(255) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$val = mysql_query($Query);
if(!$val)
    mysql_fatal_error("Adding Sessons Failed");

echo "<p>Adding <b>accounts</b></p>";

$Query = "CREATE TABLE IF NOT EXISTS `accounts` (
        `ACCOUNT_ID` int(11) NOT NULL AUTO_INCREMENT,
        `USER_NAME` text NOT NULL,
        `PASSWORD` text NOT NULL,
        `EMAIL` char(80) NOT NULL,
        `ACCESS` int(1) NOT NULL DEFAULT '0',
        `STATUS` int(1) NOT NULL DEFAULT '0',
        `CONFIG_ID` int(11) NOT NULL DEFAULT '0',        
        PRIMARY KEY (`ACCOUNT_ID`),
        KEY `EMAIL` (`EMAIL`)        
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";

$val = mysql_query($Query);
if(!$val)
    mysql_fatal_error("Adding Account Table Failed");

$Query = "ALTER TABLE `Accounts` AUTO_INCREMENT = 10000;";
$val = mysql_query($Query);
if(!$val)
    mysql_fatal_error("Adding Account Table (S2) Failed");


echo "<p>Adding <b>reset data</b></p>";

$Query = "CREATE TABLE IF NOT EXISTS `reset_data` (
        `RESET_ID` int(11) NOT NULL AUTO_INCREMENT,
        `KEYCODE` char(128) NOT NULL,
        `ACCOUNT_ID` int(11) NOT NULL,
        `TIMESTAMP` int(255) NOT NULL,
        PRIMARY KEY (`RESET_ID`),        
        KEY `KEYCODE` (`KEYCODE`)        
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";

$val = mysql_query($Query);
if(!$val)
    mysql_fatal_error("Adding Reset Data Table Failed");

?>

<p>and done!</p>

<hr />
<p>Site created by Theo Winters.</p>
</body>
</html>

