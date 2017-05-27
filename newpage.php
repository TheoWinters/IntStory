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
$PageTitle = $PageData[3];
$PageContents = nl2br($PageData[4]);

LockPage_Links($LinkID);


echo <<< _END
<html>
<head>
<title>New Chapter</title>
</head>
<body>
<h1 align="center">Parrent Chapter</h1>
<div style="padding-left: 15pt; padding-right: 50pt;background-color:lightgray;">
<h2>$StoryTitle - $PageTitle</h2>
$PageContents
</div>
<hr />

<p>Add a new chapter for the option: <b>$LinkData[3]</b></p>

<form method="POST" action="previewpage.php?LinkID=$LinkID">
<p>Chapter Title:<input type="text" name="ChapterTitle" size="80" value="$LinkData[3]"></p>
<p>Chapter Text: </p>
<textarea name="ChapterContents" rows="15" cols="80" wrap="soft"></textarea>
<p><i>Make sure to have a visable line break between paragraphs.</i></p>

<p>How many choices will this chapter have? <input type="text" name="OptionCount"></p>

<p><input type="submit" value="Next >>"></p>

</form>

<a href="abortnewpage.php?LinkID=$LinkID&PageID=$PageID">Cancel New Chapter</a>

</body>
</html>
_END;

?>