<?php
require_once 'tools.php';
OpenDatabase();

if( !isset($_POST['ChapterTitle']) ||
	!isset($_POST['ChapterContents']) ||
	!isset($_POST['OptionCount']) )
{
	PageError("Missing Post data, unable to preview.");
}

if(!isset($_GET['LinkID']))
	PageError("Missing Link ID. Unable to load a page");

$LinkID = $_GET['LinkID'];

$ChapterTitle = mysql_entities_string($_POST['ChapterTitle']);
$ChapterContents = mysql_entities_string($_POST['ChapterContents']);
$OptionCount = mysql_entities_string($_POST['OptionCount']);

if($ChapterTitle == "")
	PageError("Chapter Title is missing.");

if($ChapterContents == "")
	PageError("Chapter text is missing.");

if($OptionCount == 0)
	PageError("Please provide the number of choices you would like this chapter to have.");

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
<title>New Chapter: Preview</title>
</head>
<body>

<p> Here's a preview of your chapter, if it dosn't look right or something is missing you can go back and fix it.</p>

<hr />
<h1>$StoryTitle - $ChapterTitle</h1>
_END;
echo nl2br($ChapterContents);
echo <<< _END
<ol>
<form method="POST" action="previewpage2.php?LinkID=$LinkID">

<p><i>Provide the name of the choices for this chapter. You can always add "Something Else" as an option if you don't want to limit the choices.</i></p>
_END;

for($i = 0; $i < $OptionCount; ++$i)
{
	echo "<li><input type='text' name='Option$i' size='80'>";
}


echo <<< _END
</ol>
<p><input type="submit" value="Next >>"></p>

<input type=hidden name="ChapterTitle" 		value="$ChapterTitle"><p>
<input type=hidden name="ChapterContents" 	value="$ChapterContents"><p>
<input type=hidden name="OptionCount" 		value="$OptionCount"><p>

</form>

<a href="abortnewpage.php?LinkID=$LinkID&PageID=$PageID">Cancel New Chapter</a>

</body>
</html>
_END;

?>