<?php

include("globals.php");

$title = 'anyInventory: Move Items';

$item = new item($_REQUEST["id"]);

$output = '
		<form method="post" action="item_processor.php">
			<input type="hidden" name="action" value="do_move" />
			<input type="hidden" name="id" value="'.$_REQUEST["id"].'" />
			<table class="standardTable" cellspacing="0">
				<tr class="tableHeader">
					<td>Move an Item: '.$item->name.'</td>
					<td style="text-align: right;">[<a href="../docs/moving_items.php">Help</a>]</td>
				</tr>
				<tr>
					<td class="tableData">
						<table>
							<tr>
								<td class="form_label"><label for="c">Move to:</label></td>
								<td class="form_input">
									<select name="c" id="c">
										'.get_category_options($item->category->id, false).'
									</select>
								</td>
							</tr>
							<tr>
								<td class="form_label">&nbsp;</td>
								<td class="form_input" style="text-align: center;"><input type="submit" name="submit" id="submit" value="Submit" class="submitButton" /></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</form>';

display($output);

?>