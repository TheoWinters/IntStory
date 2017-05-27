<?php
require_once 'tools.php';
OpenDatabase();

if( !isset($_POST['ChapterTitle']) ||
	!isset($_POST['ChapterContents']) ||
	!isset($_POST['OptionCount']) ||
	!isset($_POST['UserName']) ||
	!isset($_POST['UserEmail']) )
{
	PageError("Missing Post data, unable to preview.");
}

if(!isset($_GET['LinkID']))
	PageError("Missing Link ID. Unable to load a page");

$LinkID = $_GET['LinkID'];

$ChapterTitle = mysql_entities_string($_POST['ChapterTitle']);
$ChapterContents = mysql_entities_string($_POST['ChapterContents']);
$OptionCount = mysql_entities_string($_POST['OptionCount']);

$UserName = mysql_entities_string($_POST['UserName']);
$UserEmail = mysql_entities_string($_POST['UserEmail']);

if($UserName == "")
	PageError("User Name is missing.");

if($UserEmail == "")
	PageError("User E-mail is missing.");

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
$User = FindUser($UserEmail);
$UserID = 0;
if(mysql_num_rows($User) != 0)
{
	// The e-mail is already in use, so use that
	$RowData = mysql_fetch_row($User);
	$UserID = $RowData[0];
}
else
{
	AddUser(mysql_fix_string($UserName), mysql_fix_string($UserEmail));
	$UserID = mysql_insert_id();
}

// Step 2: Insert our newp age
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
<p>You're new chapter has been added to the site! </p>

<p><a href='showpage.php?PageID=$NewPageID'>Open your new page</a>.<br />


</body>
</html>
_END;

?>