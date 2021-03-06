<?php
require_once 'tools.php';
OpenDatabase();
$Sesson = LoadCurrentSesson();

$StoryList = LoadStories();

echo <<< _END
<html>
<head>
<title>The Changing Mirror - Interactive Stories</title>
</head>
<body>
<h1 align="center">The Changing Mirror - Interactive Stories</h1>
_END;

include_once('pageheader.php');

echo '<h2 align="center">Current Stories</h2>';
echo '<ul>';

for($i = 0; $i < mysql_num_rows($StoryList); ++$i)
{
    $RowData = mysql_fetch_row($StoryList);
    echo "<li><a href='showpage.php?PageID=$RowData[3]'>$RowData[1]</a>: $RowData[2] <br />";
}

echo '</ul>';

if(CanAddNewStories($Sesson))
{
    echo '<p><a href="newstory.php">Create a new story</a></p>';
    echo '<hr />';
}
else
{
    echo '<p>Create a new story</p>';
}

echo <<< _END
<p><a href="recentpages.php">Newest Pages</a></p>
<hr />
<div>
<p><b>Search</b>
<script>
  (function() {
    var cx = '004680414982002861687:psob6xpwcvi';
    var gcse = document.createElement('script');
    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
        '//www.google.com/cse/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(gcse, s);
  })();
</script>
<gcse:searchbox-only></gcse:searchbox-only>
</p>
</div>
<hr />
<p>Site created by Theo Winters.</p>
</body>
</html>
_END;

?>