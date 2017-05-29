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

$StoryID = $PageData[2];

// Okay, everything is up and running, so lets get this party started!

// Step 1: Find or add the user
$UserID = $Sesson['ID'];

// Step 2: Insert our new page
AddPage($PageID, $StoryID, mysql_fix_string($ChapterTitle), mysql_fix_string($ChapterContents), $UserID);
$NewPageID = mysql_insert_id();

// Step 4: Add the page links
for($i = 0; $i < $OptionCount; ++$i)
{
    AddPage_Links($NewPageID, mysql_fix_string($Option[$i]));
}

// Step 5: Update the page link to point to the new page
UpdatePage_Links($LinkID, $NewPageID);

// Step 6: Unlock the page
UnlockPage_Links($LinkID);


echo <<< _END
<html>
<head>
<title>New Chapter: Added!</title>
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
    echo "<p>You're new chapter has been added to the site! </p>";
    echo "<p><a href='showpage.php?PageID=$NewPageID'>Open your new page</a>.<br />";
}

echo <<< _END
<p><a href=".">Interactive Stories Homepage</a>.</p>



</body>
</html>
_END;

?>