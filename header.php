<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><?php echo $title; ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo $DIR_PREFIX; ?>style.css">
	</head>
	<body>
		<table id="maintable" cellspacing="1" cellpadding="0" border="0">
			<tr>
				<td id="header_cell" style="background: url(<?php echo $DIR_PREFIX; ?>images/header_bg.jpg);">
					<h1 class="title">anyInventory 1.3</h1>		
				</td>
			</tr>
			<tr>
				<td>
					<div id="searchbox">
						<form method="get" action="<?php echo $DIR_PREFIX; ?>search.php">
							<div class="form_elements">
								<input type="hidden" name="action" value="search" />
								<input type="text" name="name" value="" />
								<input type="submit" name="submit" value="Search" />
							</div>
						</form>
					</div>
					<div id="mainmenu">
						<b>Main Menu: </b>
						[ <a href="<?php echo $DIR_PREFIX; ?>index.php">home</a> ]
						[ <a href="<?php echo $DIR_PREFIX; ?>search.php">advanced search</a> ]
						[ <a href="<?php echo $DIR_PREFIX; ?>admin/fields.php">fields</a> ]
						[ <a href="<?php echo $DIR_PREFIX; ?>admin/categories.php">categories</a> ]
						[ <a href="<?php echo $DIR_PREFIX; ?>admin/items.php">items</a> ]
						[ <a href="<?php echo $DIR_PREFIX; ?>admin/alerts.php">alerts</a> ]						
						[ <a href="<?php echo $DIR_PREFIX; ?>docs/">help</a> ]
					</div>	
				</td>
			</tr>
			<tr>
				<td style="background: #ffffff; width: 100%; padding: 5px; height: 400px;">
					<div style="height: 400px; padding: 5px; overflow: auto;">
