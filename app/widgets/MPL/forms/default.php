<?php
/** no direct access **/
defined('_WBMPLEXEC_') or die();
?>
<p>
    <label for="<?php echo $this->get_field_id('layout_pagination'); ?>"><?php echo __('Pagination', WBMPL_TEXTDOMAIN); ?></label>
    <?php if(!$this->main->getPRO()): echo $this->main->pro_messages('upgrade'); ?>
    <?php else: ?>
    <select id="<?php echo $this->get_field_id('layout_pagination'); ?>" name="<?php echo $this->get_field_name('layout_pagination'); ?>" class="widefat">
        <option value="0" <?php echo ((isset($instance['layout_pagination']) and $instance['layout_pagination'] == 0) ? 'selected="selected"' : ''); ?>><?php echo __('No Pagination', WBMPL_TEXTDOMAIN); ?></option>
        <option value="1" <?php echo ((isset($instance['layout_pagination']) and $instance['layout_pagination'] == 1) ? 'selected="selected"' : ''); ?>><?php echo __('Load More', WBMPL_TEXTDOMAIN); ?></option>
    </select>
    <?php endif; ?>
</p>
<p>
    <label for="<?php echo $this->get_field_id('layout_display'); ?>"><?php echo __('Display', WBMPL_TEXTDOMAIN); ?></label>
    <select id="<?php echo $this->get_field_id('layout_display'); ?>" name="<?php echo $this->get_field_name('layout_display'); ?>" class="widefat">
        <option value="1" <?php echo ((isset($instance['layout_display']) and $instance['layout_display'] == '1') ? 'selected="selected"' : ''); ?>><?php echo __('List', WBMPL_TEXTDOMAIN); ?></option>
        <option value="2" <?php echo ((isset($instance['layout_display']) and $instance['layout_display'] == '2') ? 'selected="selected"' : ''); ?>><?php echo __('Grid', WBMPL_TEXTDOMAIN); ?></option>
    </select>
</p>
<p>
    <label for="<?php echo $this->get_field_id('layout_grid_size'); ?>"><?php echo __('Grid Size', WBMPL_TEXTDOMAIN); ?></label>
    <select id="<?php echo $this->get_field_id('layout_grid_size'); ?>" name="<?php echo $this->get_field_name('layout_grid_size'); ?>" class="widefat">
        <option value="2" <?php echo ((isset($instance['layout_grid_size']) and $instance['layout_grid_size'] == '2') ? 'selected="selected"' : ''); ?>>2</option>
        <option value="3" <?php echo ((isset($instance['layout_grid_size']) and $instance['layout_grid_size'] == '3') ? 'selected="selected"' : ''); ?>>3</option>
        <option value="4" <?php echo ((isset($instance['layout_grid_size']) and $instance['layout_grid_size'] == '4') ? 'selected="selected"' : ''); ?>>4</option>
        <option value="5" <?php echo ((isset($instance['layout_grid_size']) and $instance['layout_grid_size'] == '5') ? 'selected="selected"' : ''); ?>>5</option>
    </select>
</p>