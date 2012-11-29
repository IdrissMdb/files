		<script type="text/javascript">
		<?php if ( array_key_exists('publicListView', $_) && $_['publicListView'] == true ) :?>
				var publicListView = true;
		<?php else: ?>
				var publicListView = false;
		<?php endif; ?>
		</script>

		<?php foreach($_['files'] as $file):
			$simple_file_size = OCP\simple_file_size($file['size']);
			// the bigger the file, the darker the shade of grey; megabytes*2
			$simple_size_color = intval(200-$file['size']/(1024*1024)*2);
			if($simple_size_color<0) $simple_size_color = 0;
			$relative_modified_date = OCP\relative_modified_date($file['mtime']);
			// the older the file, the brighter the shade of grey; days*14
			$relative_date_color = round((time()-$file['mtime'])/60/60/24*14);
			if($relative_date_color>200) $relative_date_color = 200;
			$name = str_replace('+', '%20', urlencode($file['name']));
			$name = str_replace('%2F', '/', $name);
			$directory = str_replace('+', '%20', urlencode($file['directory']));
			$directory = str_replace('%2F', '/', $directory); ?>
			<tr data-id="<?php echo $file['id']; ?>"
				data-file="<?php echo $name;?>"
				data-type="<?php echo ($file['type'] == 'dir')?'dir':'file'?>"
				data-mime="<?php echo $file['mimetype']?>"
				data-size='<?php echo $file['size'];?>'
				data-permissions='<?php echo $file['permissions']; ?>'>
				<td class="filename svg"
				<?php if($file['type'] == 'dir'): ?>
					style="background-image:url(<?php echo OCP\mimetype_icon('dir'); ?>)"
				<?php else: ?>
					style="background-image:url(<?php echo OCP\mimetype_icon($file['mimetype']); ?>)"
				<?php endif; ?>
					>
				<?php if(!isset($_['readonly']) || !$_['readonly']): ?><input type="checkbox" /><?php endif; ?>
				<?php if($file['type'] == 'dir'): ?>
					<a class="name" href="<?php $_['baseURL'].$directory.'/'.$name; ?>)" title="">
				<?php else: ?>
					<a class="name" href="<?php echo $_['downloadURL'].$directory.'/'.$name; ?>" title="">
				<?php endif; ?>
					<span class="nametext">
						<?php if($file['type'] == 'dir'):?>
							<?php echo htmlspecialchars($file['name']);?>
						<?php else:?>
							<?php echo htmlspecialchars($file['basename']);?><span
								class='extension'><?php echo $file['extension'];?></span>
						<?php endif;?>
					</span>
					<?php if($file['type'] == 'dir'):?>
						<span class="uploadtext" currentUploads="0">
						</span>
					<?php endif;?>
					</a>
				</td>
				<td class="filesize"
					title="<?php echo OCP\human_file_size($file['size']); ?>"
					style="color:rgb(<?php echo $simple_size_color.','.$simple_size_color.','.$simple_size_color ?>)">
						<?php echo $simple_file_size; ?>
				</td>
				<td class="date">
					<span class="modified"
						  title="<?php echo $file['date']; ?>"
						  style="color:rgb(<?php echo $relative_date_color.','
														.$relative_date_color.','
														.$relative_date_color ?>)">
						<?php echo $relative_modified_date; ?>
					</span>
				</td>
			</tr>
		<?php endforeach;