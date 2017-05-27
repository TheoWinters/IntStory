<?php
require_once 'tools.php';
OpenDatabase();

echo <<< _END
<html>
<head>
<title>New Story</title>
</head>
<body>
<h1>New Story</h1>

<p>Before you add a new story, be sure there isn't a story on the site that you could add to, otherwise we could end up with quite a few stories with only a hand full of pages.</p>
<p>All stories have to be approved by the site administrator before becoming public.</a>

<h2>Step 1: Story Information</h2>

<form method="POST" action="previewstory.php">
<p>Story Title: <input type="text" name="StoryTitle" size="80"></p>
<p>Description: <input type="text" name="StoryDescription" size="80"></p>
<p><i>You can use the description to cover both the over all ideas of the story and any tags you would like it to have.</i></p>
<hr />

<h2>Step 2: First Chapter</h2>

<p>Chapter Title: <input type="text" name="ChapterTitle" size="80"></p>
<p>Chapter Text: </p>
<textarea name="ChapterContents" rows="15" cols="80" wrap="soft"></textarea>
<p><i>Make sure to have a visable line break between paragraphs.</i></p>

<p>How many choices will this chapter have? <input type="text" name="OptionCount"></p>

<p><input type="submit" value="Next >>"></p>
</form>

<p><a href=".">Return to the Interactive Stories Homepage</a>.</p>

</body>
</html>
_END;

?>