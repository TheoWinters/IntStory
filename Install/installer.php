<?php
require_once '.\..\tools.php';

if( !isset($_POST['UserName']) ||
    !isset($_POST['UserEmail']) ||
    !isset($_POST['AdminPW']) )
{
    PageError("Missing Post data, unable to preview.");
}

$UserName = mysql_entities_string($_POST['UserName']);
$UserEmail = mysql_entities_string($_POST['UserEmail']);
$AdminPW = mysql_entities_string($_POST['AdminPW']);

if($UserName == "")
    PageError("User Name is missing.");

if($UserEmail == "")
    PageError("User E-mail is missing.");

if($AdminPW == "")
    PageError("Admin PW is missing.");

?>

<html>
<head>
<title>The Changing Mirror - Interactive Stories Installer</title>
</head>
<body>
<h1 align="center">The Changing Mirror - Interactive Stories Installer</h1>

<hr />

<p>Okay, can we connect to the DB?</p>
<?php OpenDatabase(); ?>

<p>That seems to have worked...</p>

<p>Lets make some tables!</p>
<?php

echo "<p>Adding <b>stories</b></p>";

$Query = "CREATE TABLE IF NOT EXISTS `stories` (
  `STORY_ID` int(11) NOT NULL AUTO_INCREMENT,
  `TITLE` text NOT NULL,
  `DESC` text NOT NULL,
  `FIRST_PAGE_ID` int(11) NOT NULL,
  `APPROVED` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`STORY_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1";

$val = mysql_query($Query);
if(!$val)
    mysql_fatal_error("Adding Stories Failed");

echo "<p>Adding <b>pages</b></p>";

$Query = "CREATE TABLE IF NOT EXISTS `pages` (
  `PAGE_ID` int(11) NOT NULL AUTO_INCREMENT,
  `PARRENT_PAGE_ID` int(11) NOT NULL,
  `STORY_ID` int(11) NOT NULL,
  `TITLE` text NOT NULL,
  `CONTENTS` text NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `IPSOURCE` text NOT NULL,
  `MODIFIED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`PAGE_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1";

$val = mysql_query($Query);
if(!$val)
    mysql_fatal_error("Adding Pages Failed");


echo "<p>Adding <b>page_link</b></p>";

$Query = "CREATE TABLE IF NOT EXISTS `page_link` (
  `LINK_ID` int(11) NOT NULL AUTO_INCREMENT,
  `OWNER_PAGE_ID` int(11) NOT NULL,
  `DEST_PAGE_ID` int(11) NOT NULL DEFAULT '0',
  `NAME` text NOT NULL,
  `LOCK` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`LINK_ID`),
  KEY `LINK_ID` (`LINK_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1";

$val = mysql_query($Query);
if(!$val)
    mysql_fatal_error("Adding page_link Failed");

echo "<p>Adding <b>users</b></p>";

$Query = "CREATE TABLE IF NOT EXISTS `user` (
  `USER_ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` text NOT NULL,
  `PASSWORD` text NOT NULL,
  `EMAIL` char(80) NOT NULL,
  `ACCESS` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`USER_ID`),
  KEY `EMAIL` (`EMAIL`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1";



$val = mysql_query($Query);
if(!$val)
    mysql_fatal_error("Adding users Failed");

echo "<p>Adding <b>$UserName</b> as Admin</p>";

$PW = strtoupper(md5("SaltA" + $AdminPW + "SaltB"));

$Query = "INSERT INTO user VALUES(NULL, '$UserName', '$PW', '$UserEmail', 65536)";

$val = mysql_query($Query);
if(!$val)
    mysql_fatal_error("AddUser Failed.");


?>

<p>and done!</p>

<hr />
<p>Site created by Theo Winters.</p>
</body>
</html>

