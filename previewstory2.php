<?php
require_once 'tools.php';
OpenDatabase();

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
<p>This is the final preview of your story, if it dosn't look right or something is missing you can go back and fix it.</p>

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
<p><i>You're user name is used to sign the story, and your e-mail address is for booking keeping (and future user accounts). We will always display the user name first used with a given e-mail address. So make sure everything is correct!</i></p>
<p>User Name: <input type='text' name='UserName' size='80'></p>
<p>E-mail address: <input type='text' name='UserEmail' size='80'></p>

<p><input type="submit" value="Submit >>"></p>

<input type=hidden name="StoryTitle" 		value="$StoryTitle"><p>
<input type=hidden name="StoryDescription" 	value="$StoryDescription"><p>
<input type=hidden name="ChapterTitle" 		value="$ChapterTitle"><p>
<input type=hidden name="ChapterContents" 	value="$ChapterContents"><p>
<input type=hidden name="OptionCount" 		value="$OptionCount"><p>
_END;

for($i = 0; $i < $OptionCount; ++$i)
{
echo <<< _END
<input type=hidden name="Option$i" 		value="$Option[$i]"><p>
_END;
}

echo <<< _END
</form>

<p><a href=".">Return to the Interactive Stories Homepage</a>.</p>

</body>
</html>
_END;

?>