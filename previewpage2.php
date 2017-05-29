<?php
require_once 'tools.php';
OpenDatabase();
$Sesson = LoadCurrentSesson();
$Message = "";

if($Sesson == null)
{
    $Message = "You must have an account to add pages to the site.";
}

if(!CanAddNewPages($Sesson))
{
    $Message = "You are currently not allow to add new stories to the site";
}

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

for($i = 0; $i < $OptionCount; ++$i)
{
    if(!isset($_POST["Option$i"]))
        PageError("Missing Post data, unable to preview.");

    $OptionVal = mysql_entities_string($_POST["Option$i"]);
    $OptionNum = $i + 1;

    if($OptionVal == "")
        PageError("Option $OptionNum is missing text.");

    $Option[] = $OptionVal;
}

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
_END;

include_once('pageheader.php');

if($Message != "")
{
    echo '<p>'.$Message.'</p>';
}
else
{
echo <<< _END
<p>This is the final preview of your chapter, if it dosn't look right or something is missing you can go back and fix it.</p>

<hr />
<h1>$StoryTitle - $ChapterTitle</h1>
_END;
echo nl2br($ChapterContents);
echo <<< _END
<ol>
_END;

for($i = 0; $i < $OptionCount; ++$i)
{
    echo "<li><b>$Option[$i]</b>";
}


echo <<< _END
</ol>
<form method="POST" action="insertpage.php?LinkID=$LinkID">
<p><input type="submit" value="Submit >>"></p>

<input type=hidden name="ChapterTitle"      value="$ChapterTitle"><p>
<input type=hidden name="ChapterContents"   value="$ChapterContents"><p>
<input type=hidden name="OptionCount"       value="$OptionCount"><p>
_END;

for($i = 0; $i < $OptionCount; ++$i)
{
echo <<< _END
<input type=hidden name="Option$i"      value="$Option[$i]"><p>
_END;
}

echo <<< _END
</form>
_END;
}

echo <<< _END

<a href="abortnewpage.php?LinkID=$LinkID&PageID=$PageID">Cancel New Chapter</a>

</body>
</html>
_END;

?>