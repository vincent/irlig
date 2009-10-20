<?php

class Zend_View_Helper_Texturepattern {

	public function texturepattern($def) {

		if (!array_key_exists('name', $def) OR !$def['name']) return;
		if (!array_key_exists('texture', $def) OR !$def['texture']) return;

		if (!array_key_exists('pat_x', $def)) $def['pat_x'] = 0;
		if (!array_key_exists('pat_y', $def)) $def['pat_y'] = 0;
		if (!array_key_exists('pat_w', $def)) $def['pat_w'] = 64;
		if (!array_key_exists('pat_h', $def)) $def['pat_h'] = 64;

		if (!array_key_exists('image_x', $def)) $def['image_x'] = 0;
		if (!array_key_exists('image_y', $def)) $def['image_y'] = 0;
		if (!array_key_exists('image_w', $def)) $def['image_w'] = 64;
		if (!array_key_exists('image_h', $def)) $def['image_h'] = 64;

		return '
		    <pattern id="'.$def['name'].'" x="'.intval($def['pat_x']).'" y="'.intval($def['pat_y']).'" width="'.intval($def['pat_w']).'" height="'.intval($def['pat_h']).'" patternUnits="userSpaceOnUse">
		      <image id="'.$def['name'].'image" xlink:href="/resources/images/oF-Slayer_textures/'.$def['texture'].'" width="'.intval($def['image_w']).'" height="'.intval($def['image_h']).'" x="'.intval($def['image_x']).'" y="'.intval($def['image_y']).'" />
		    </pattern>
		';

	}

}

?>