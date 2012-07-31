	      <section class="content" style="word-wrap: break-word;">
		     <h1 style="color: #aaa; font-size: 14px;"><?=$comment -> poster;?> said...</h1>
		     <p style="font-size: 12px;"><?=$comment -> content;?></p>
		     <p class="time"><time datetime="<?=date('Y-m-d', $comment -> timestamp);?>" pubdate="pubdate">Posted: <?=date('F jS, Y', $comment -> timestamp);?></time></p>
	      </section>
