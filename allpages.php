<?php
require_once 'tools.php';

OpenDatabase();
$Sesson = LoadCurrentSesson();

if(!isset($_GET['StoryID']))
    PageError("Missing Story ID. Unable to load a page");

$StoryID = $_GET['StoryID'];

$Story = LoadStory($StoryID);
$StoryData = mysql_fetch_row($Story);

$StoryTitle = $StoryData[1];

$Pages = LoadAllPages($StoryID);

$Index = rand(0, mysql_num_rows($Pages));

echo <<< _END
<html>
<head>
<title>$StoryTitle</title>
</head>
<body>
<h1 align="center">$StoryTitle - All Pages</h1>
_END;

include_once('pageheader.php');

echo <<< _END
<h2>$StoryTitle</h2>
<ul>
_END;

for($i = 0; $i < mysql_num_rows($Pages); ++$i)
{
    $RowData = mysql_fetch_row($Pages);
    echo "<li><a href='showpage.php?PageID=$RowData[0]'>". $RowData[3]."</a><br />";
}

echo <<< _END
</ul>
<hr />

<p><a href=".">Interactive Stories Homepage</a>.</p>
</body>
</html>
_END;

?>