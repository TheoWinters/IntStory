<?php
require_once 'tools.php';

OpenDatabase();

if(!isset($_GET['PageID']))
    PageError("Missing Page ID. Unable to load a page");

$PageID = $_GET['PageID'];

$Page = LoadPage($PageID);
$PageData = mysql_fetch_row($Page);

$Story = LoadStory($PageData[2]);
$StoryData = mysql_fetch_row($Story);

$User = LoadUser($PageData[5]);
$UserData = mysql_fetch_row($User);

$Links = LoadPage_Links($PageID);

$ParrentLink = "";
if($PageData[1] != 0)
    $ParrentLink = "<a href='showpage.php?PageID=$PageData[1]'>Parrent Chapter</a><p />";

$StoryTitle = $StoryData[1];
$PageTitle = $PageData[3];
$PageContents = nl2br($PageData[4]);



echo <<< _END
<html>
<head>
<title>$StoryTitle - $PageTitle</title>
</head>
<body>
<h1>$StoryTitle - $PageTitle</h1>
$ParrentLink
$PageContents
<ol>
_END;

for($i = 0; $i < mysql_num_rows($Links); ++$i)
{
    $RowData = mysql_fetch_row($Links);
    if($RowData[2] != 0)
    {
        echo "<li><a href='showpage.php?PageID=$RowData[2]'>". $RowData[3]."</a><br />";
    }
    else
    {
        if($RowData[4] == 0 || time() > $RowData[4])
        {
            echo "<li><a href='newpage.php?LinkID=$RowData[0]'>". $RowData[3]."</a> <i>(blank)</i><br />";
            //echo "<li><a href='newpage.php?LinkID=$RowData[0]&StoryID=$PageData[2]&ParrentID=$PageID'>". $RowData[3]."</a> <i>(blank)</i><br />";
        }
        else
        {
            echo "<li><a href='newpage.php?LinkID=$RowData[0]'>". $RowData[3]."</a> <i>(locked)</i><br />";
        }
    }

}


echo <<< _END
</ol>
<hr />
<p>Page created by: <b>$UserData[1]</b> on <i>$PageData[7]</i>. </p>
<p><a href="allpages.php?StoryID=$StoryData[0]">All Pages</a> in this story.</p> 
<hr />
<p><a href=".">Interactive Stories Homepage</a>.</p>

</body>
</html>
_END;

?>