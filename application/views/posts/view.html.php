<?php
$this -> render_partial('sidebox');
?>
	    <div id="medium-content">
<?php
$this -> render_partial('posts/full', ['post' => $post]);
?>
<?php
if($comments)
{
?>
	    <h1 style="border-bottom: 1px solid #eee; margin-top: 15px;">Comments</h1>
<?php
foreach($comments as $comment)
$this -> render_partial('comment', ['comment' => $comment]);
}
?>
		<section class="comment">
		    <h1 style="border-bottom: 1px solid #eee; padding-top: 15px;">Post A Comment</h1>
		    <div class="success"><p>Your comment has been posted.</p></div>
		    <div class="error"></div>
		    <span style="text-align: center">
			<form name="addcomment" method="POST" action="">
			<input id="postid" type="hidden" name="postid" value="<?=$post -> id;?>" />
			<span style="font-family: 'Architects Daughter', Georgia, cursive; color: #aaa; font-size: 14px; font-weight: bold; font-style: italic;"> Your Name: <input id="poster" style="border: 1px solid #000; background: #eee;" type="text" name="poster" max="60" /></span>
			<textarea id="comment" style="margin-top: 10px; border: 1px solid #000; background: #eee; height: 100px; width: 600px;" name="comment" placeholder="Leave a comment.." /></textarea><br />
			<h2><a id="submitcomment" name="submitcomment" href="#submitcomment">Post Comment!</a></h2>
			</form>
		    </span>
		</section>
	    </div>
