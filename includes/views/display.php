<html>
<head>
<title>BattleShips</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<pre><?=$this->display_grid($show_ships);?></pre>
<?php if($shoot){ ?>
<p><?=$shoot?></p>
<?php } ?>
<?php if(!$this->game_end){ ?>
<form method="post" action="">
	Help func: show, reset<br />
	Enter coordinates (row, col), e.g. A5 
	<input type="text" name="coords" value="" size="5" autocomplete="off" autofocus=""/>
	<input type="submit" value="Submit" />
</form>
<?php }else{ ?>
<a href="">Start new game</a>
<?php } ?>
</body>
</html>