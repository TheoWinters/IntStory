<html>
<head>
<title>The Changing Mirror - Interactive Stories</title>
</head>
<body>
<h1 align="center">The Changing Mirror - Interactive Stories</h1>

<?php
require_once 'tools.php';
OpenDatabase();
$Sesson = LoadCurrentSesson();

include_once('pageheader.php');

$StoryList = LoadStories();

echo '<ul>';
for($i = 0; $i < mysql_num_rows($StoryList); ++$i)
{
    $RowData = mysql_fetch_row($StoryList);
    echo "<li><a href='showpage.php?PageID=$RowData[3]'>$RowData[1]</a>: $RowData[2] <br />";
}

echo '</ul>';
?>

<h2 align="center">Current Stories</h2>

<p><a href="newstory.php">Create a new story</a></p>
<hr />
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