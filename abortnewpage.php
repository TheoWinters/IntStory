<?php
require_once 'tools.php';
OpenDatabase();

if(!isset($_GET['PageID']))
	PageError("Missing Page ID. Unable to load a page");

$PageID = $_GET['PageID'];

if(!isset($_GET['LinkID']))
	PageError("Missing Link ID. Unable to load a page");

$LinkID = $_GET['LinkID'];

UnlockPage_Links($LinkID);


echo <<< _END
<html>
<head>
<title>The Changing Mirror - Interactive Stories</title>
</head>
<body>
<p>You're new page has been canceled.</p>

<a href='showpage.php?PageID=$PageID'>Return to Parrent Page</a>.<br />

</body>
</html>
_END;

?>