<?php $this -> render_partial('sidebox');?>		
		<div id="medium-content">		
		    <div class="content">
			<h1 style="margin-bottom: 10px;">&raquo; Whoops, something went wrong!</h1>
			<img style="float: left; padding: 5px;" src="<?=format('images/error.png');?>" />
			<p>Sorry, there was an error while generating the page. You can try navigating through our <?=link_to('posts', 'posts');?> or using our search feature to find what you're looking for.</p><br />
			<p>The error message caught was: <strong>"<?=$exception -> getMessage();?>"</strong></p>
		    </div>
		</div>
