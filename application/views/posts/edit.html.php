		<div class="content">
		    <?php
		    if(isset($updated)) echo '<div style="background: green; color: white; font-weight: bold; text-align: center;"><p style="margin-top: 10px; margin-bottom: 10px;">Article successfully updated.</p></div>';
		    ?>
		    <h1>&raquo; Post an article</h1>
		    <div style="padding: 10px;">
				<form action="<?=format("posts/{$post -> id}");?>" method="POST" />
					<input type="hidden" name="http_method_override" value="PUT" />
					<input type="hidden" name="id" value="<?=$post -> id;?>" />
					Post Title: <input type="text" name="title" size="50" style="background: #eee; font: Verdana; border: 1px solid #000;" value="<?=$post -> title;?>" /><br />
					Post Description: <br />
					<textarea name="description" rows="7" cols="80" style="background: #eee; font: Verdana; border: 1px solid #000"><?=$post -> description;?></textarea><br />
					Post Content:<br />
					<textarea name="content" rows="15" cols="80" style="background: #eee; font: Verdana; border: 1px solid #000"><?=$post -> content;?></textarea><br />
					Admin Password:<br />
					<input type="password" name="admin_password" style="background: #eee; font: Verdana; border: 1px solid #000" /><br /><br />
					<input type="submit" value="Post" />
				</form>
		    </div>
		</div>