<?php
require_once 'tools.php';
OpenDatabase();
$Sesson = LoadCurrentSesson();
$Message = "";

if($Sesson == null)
{
    $Message = "You must have an account to add pages to the site.";
}

if(!CanAddNewStories($Sesson))
{
    $Message = "You are currently not allow to add new stories to the site";
}

if( !isset($_POST['StoryTitle']) ||
    !isset($_POST['StoryDescription']) ||
    !isset($_POST['ChapterTitle']) ||
    !isset($_POST['ChapterContents']) ||
    !isset($_POST['OptionCount']) )
{
    PageError("Missing Post data, unable to preview.");
}


$StoryTitle = mysql_entities_fix_string($_POST['StoryTitle']);
$StoryDescription = mysql_entities_fix_string($_POST['StoryDescription']);
$ChapterTitle = mysql_entities_fix_string($_POST['ChapterTitle']);
$ChapterContents = mysql_entities_fix_string($_POST['ChapterContents']);
$OptionCount = mysql_entities_fix_string($_POST['OptionCount']);

if($StoryTitle == "")
    PageError("Story is title missing.");

if($StoryDescription == "")
    PageError("Story Description is missing.");

if($ChapterTitle == "")
    PageError("Chapter Title is missing.");

if($ChapterContents == "")
    PageError("Chapter text is missing. '$ChapterContents'");

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


echo <<< _END
<html>
<head>
<title>New Story: Preview</title>
</head>
<body>
<h1 align="center">New Story - Preview</h1>
_END;

include_once('pageheader.php');

if($Message != "")
{
    echo '<p>'.$Message.'</p>';
}
else
{
echo <<< _END
<h2>New Story - Preview</h2>

<p>This is the final preview of your story, if it dosn&lsquo;t look right or something is missing you can go back and fix it.</p>

<hr />
<b>$StoryTitle:</b> <i>$StoryDescription</i></br>
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
<form method="POST" action="insertstory.php">

<p><input type="submit" value="Submit >>"></p>

<input type=hidden name="StoryTitle"        value="$StoryTitle"><p>
<input type=hidden name="StoryDescription"  value="$StoryDescription"><p>
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
<p><a href=".">Return to the Interactive Stories Homepage</a>.</p>

</body>
</html>
_END;

?>