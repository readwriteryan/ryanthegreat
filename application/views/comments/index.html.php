			<div id="medium-content">
<?
foreach($comments as $comment)
{
	$this -> render_partial('comment', ['comment' => $comment]);
?>
			<p style="text-align: center;"><a href="<?=format('comments/' . $comment -> id . '/approve');?>">Approve</a> | <a href="<?=format('comments/' . $comment -> id . '/decline');?>">Decline</a></p>
<?php
}
?>
			</div>