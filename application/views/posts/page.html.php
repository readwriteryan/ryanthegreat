<?php
$this -> render_partial('sidebox');
?>
	    <div id="medium-content">
<?php
foreach($posts as $post)
    $this -> render_partial('posts/short', ['post' => $post]);
?>
	    </div>
