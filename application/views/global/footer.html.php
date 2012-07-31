	</div>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<?php
foreach($this -> js as $source)
{
?>
	<script type="text/javascript" src="<?=$source;?>"></script><?="\n";?>
<?}?>
    </body>
    <footer>
	<p>&copy; 2012 | All Rights Reserved</p>
    </footer>
</html>