<?php
require_once 'tools.php';

OpenDatabase();

if(!isset($_GET['LinkID']))
	PageError("Missing Link ID. Unable to load a page");

$LinkID = $_GET['LinkID'];

$Link = LoadPage_Link($LinkID);
$LinkData = mysql_fetch_row($Link);

if($LinkData[2] != 0)
	PageError("Link ID is already pointing to a page. Someone probably just added that option.");

if(!CheckLockPage_Links($LinkID))
	PageError("Looks like someone is already in the process of adding a new page for this chocie.");

$PageID = $LinkData[1];

$Page = LoadPage($PageID);
$PageData = mysql_fetch_row($Page);

$Story = LoadStory($PageData[2]);
$StoryData = mysql_fetch_row($Story);

$StoryTitle = $StoryData[1];

echo <<< _END
<html>
<head>
<title>$StoryTitle: New Chapter</title>
</head>
<body>
<h1>$StoryTitle: New Chapter</h1>

<p>Would you like to make a new chapter for <b>$LinkData[3]</b>?</p>

<form method="POST" action="newpage.php?LinkID=$LinkID">
<p><input type="submit" value="Create Chapter"> or
   <a href='showpage.php?PageID=$PageID'>Go Back</a><p /></p>
</form>



<p><a href=".">Interactive Stories Homepage</a>.</p>
</body>
</html>
_END;

?>