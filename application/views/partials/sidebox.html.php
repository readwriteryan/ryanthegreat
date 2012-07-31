	    <div id="sidebox" class="sidebar-heading">
		<div class="aboutme">
		    <img src="<?=format('images/me.jpg');?>" class="sidebar-avatar" />
		    <p style="padding: 5px;">This is the personal site for Ryan Wagner, a 24 year old computer scientist living in Virginia.</p>
		    <br />
		    <p style="text-align: center;">More About Me:</p>
		    <p style="text-align: center; margin-top: 5px;"><?=link_to('about', 'About');?> | <?=link_to('contact', 'Contact');?></p>
		</div>
		<h1 style="padding-top: 10px; text-align: left;">Search</h1>
		<form action="<?=format('blog/search');?>" method="GET">
		    <input id="searchbox" placeholder="Search here..." type="text" name="search" />
		</form>
		<br /><br />
		<h1 style="text-align: left">Recent Posts</h1>
<?php
$this -> render_partial('posts/sidebox');
$this -> render_partial('posts/sidebox');
$this -> render_partial('posts/sidebox');
?>
	    </div>
