		     <article class="article-full">
			    <h1>&raquo; <?=$post -> title;?></h1>
			    <p><?=$post -> content;?></p>
			    <p class="time"><time datetime="<?=date('Y-m-d', $post -> timestamp);?>" pubdate="pubdate">Posted: <?=date('F jS, Y', $post -> timestamp);?></time></p>
		     </article>
