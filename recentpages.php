<html>
<head>
<title>Interactive Stories - Recent Changes</title>
</head>
<body>
<h1 align="center">Interactive Stories - Recent Changes</h1>

<hr />

<h2 align="center">Recent Changes</h2>

<?php
require_once 'tools.php';
OpenDatabase();

$PageList = LoadPageList(25);

echo '<ul>';
for($i = 0; $i < mysql_num_rows($PageList); ++$i)
{
    $RowData = mysql_fetch_row($PageList);
    $PageID = $RowData[0];

    $Page = LoadPage($PageID);
    $PageData = mysql_fetch_row($Page);

    $Story = LoadStory($PageData[2]);
    $StoryData = mysql_fetch_row($Story);

    $User = LoadUser($PageData[5]);
    $UserData = mysql_fetch_row($User);

    $StoryTitle = $StoryData[1];
    $PageTitle = $PageData[3];

    $Date = new DateTime($PageData[7]);
    $DateTime = $Date->format("M jS, Y")." at ".$Date->format("g:i A");

    echo "<li><a href='showpage.php?PageID=$PageID'>$StoryTitle: $PageTitle</a>: by <b>$UserData[1]</b> on <i>$DateTime</i>";

}

echo '</ul>';
?>

<hr />
<p><a href=".">Interactive Stories Homepage</a>.</p>
</body>
</html>



