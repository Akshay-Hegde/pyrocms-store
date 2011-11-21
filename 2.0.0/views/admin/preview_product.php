<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a store module for PyroCMS
 *
 * @author 		Jaap Jolman And Kevin Meier - pyrocms-store Team
 * @website		http://jolman.eu
 * @package 	PyroCMS
 * @subpackage 	Store Module
**/
?>
<section class="title">
	<h4><?php echo $this->product->name; ?></h4>
</section>

<section class="item">
<p style="float:left; width: 40%;">
	<?php echo anchor('store/product/' .$this->product->products_id .'/'. , NULL, 'target="_blank"'); ?>
</p>

<iframe src="<?php echo site_url('store/product/' .$this->product->products_id; ?>" width="99%" height="400"></iframe>
</section>