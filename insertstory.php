<?php
require_once 'tools.php';
OpenDatabase();
$Sesson = LoadCurrentSesson();
$Message = "";
$UserID = "";
$JobState = 0;


if($Sesson == null)
{
    $Message = "You must have an account to add pages to the site.";
    $JobState = 1;
}

if(!CanAddNewStories($Sesson))
{
    $Message = "You are currently not allow to add new stories to the site";
    $JobState = 1;
}

if( !isset($_POST['StoryTitle']) ||
    !isset($_POST['StoryDescription']) ||
    !isset($_POST['ChapterTitle']) ||
    !isset($_POST['ChapterContents']) ||
    !isset($_POST['OptionCount']) )
{
    PageError("Missing Post data, unable to preview.");
}


$StoryTitle = mysql_entities_string($_POST['StoryTitle']);
$StoryDescription = mysql_entities_string($_POST['StoryDescription']);
$ChapterTitle = mysql_entities_string($_POST['ChapterTitle']);
$ChapterContents = mysql_entities_string($_POST['ChapterContents']);
$OptionCount = mysql_entities_string($_POST['OptionCount']);

if($StoryTitle == "")
    PageError("Story is title missing.");

if($StoryDescription == "")
    PageError("Story Description is missing.");

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


if($JobState == 0)
{
    $UserID = $Sesson['ID'];


    // Step 1: Insert our new story
    AddStory(mysql_fix_string($StoryTitle), mysql_fix_string($StoryDescription));
    $StoryID = mysql_insert_id();

    // Step 2: Insert the first page
    AddPage(0, $StoryID, mysql_fix_string($ChapterTitle), mysql_fix_string($ChapterContents), $UserID);
    $PageID = mysql_insert_id();

    // Step 3: Add the page links
    for($i = 0; $i < $OptionCount; ++$i)
    {
        AddPage_Links($PageID, mysql_fix_string($Option[$i]));
    }

    // Step 4: Update the page with the link to the first page
    UpdateStory($StoryID, $PageID);
}

echo <<< _END
<html>
<head>
<title>New Story: Added!</title>
</head>
<body>
<h1 align="center">New Story - Added</h1>
_END;

include_once('pageheader.php');

if($Message != "")
{
    echo '<p>'.$Message.'</p>';
}
else
{
    echo "<p><b><a href='showpage.php?PageID=$PageID'>$StoryTitle</a></b> has been added to the site.</p>";
}

echo <<< _END
<p><a href=".">Interactive Stories Homepage</a>.</p>

</body>
</html>
_END;

?>