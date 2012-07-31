	      <article class="content">
		     <h1><a href="<?=format("posts/{$post -> id}");?>">&raquo; <?=$post -> title;?></a></h1>
		     <p><?=$post -> description;?></p>
		     <p class="time"><time datetime="<?=date('Y-m-d', $post -> timestamp);?>" pubdate="pubdate">Posted: <?=date('F jS, Y', $post -> timestamp);?></time></p>
	      </article>
