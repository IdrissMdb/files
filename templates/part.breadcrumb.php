	<?php for($i=0; $i<count($_["breadcrumb"]); $i++):
        $crumb = $_["breadcrumb"][$i]; ?>
		<div class="crumb <?php if($i == count($_["breadcrumb"])-1) echo 'last';?> svg" data-dir='<?php echo $crumb["dir"];?>' style='background-image:url("<?php echo OCP\image_path('core','breadcrumb.png');?>")'>
    		<a href="<?php echo $_['baseURL'].$crumb["dir"]; ?>"><?php echo OCP\Util::sanitizeHTML($crumb["name"]); ?></a>
		</div>
	<?php endfor;?>
