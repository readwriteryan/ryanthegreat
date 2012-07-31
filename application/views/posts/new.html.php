		<div class="content">
		    <?php
		    if(isset($posted)) echo '<div style="background: green; color: white; font-weight: bold; text-align: center;"><p style="margin-top: 10px; margin-bottom: 10px;">Post created successfully.</p></div>';
		    ?>
		    <h1>&raquo; Post an article</h1>
		    <div style="padding: 10px;">
				<form action="<?=format('posts');?>" method="POST" />
					    Post Title: <input type="text" name="title" size="50" style="background: #d6e4ff; font: Verdana; border: 1px solid #000;"/><br />
					    Post Description: <br />
					    <textarea name="description" rows="7" cols="80" style="background: #d6e4ff; font: Verdana; border: 1px solid #000"></textarea><br />
					    Post Content:<br />
					    <textarea name="content" rows="15" cols="80" style="background: #d6e4ff; font: Verdana; border: 1px solid #000"></textarea><br />
					    Admin Password:<br />
					    <input type="password" name="admin_password" style="background: #d6e4ff; font: Verdana; border: 1px solid #000" /><br /><br />
					    <input type="hidden" name="miranda_form_token" value="<?=$this -> form_token();?>" />					    
					    <input type="submit" value="Post" />
				</form>
		    </div>
		</div>