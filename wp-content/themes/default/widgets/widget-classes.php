<?php
class classes_widget extends WP_Widget 
{
	/** constructor */
    function classes_widget() 
	{
		global $themename;
		$widget_options = array(
			'classname' => 'classes_widget',
			'description' => 'Displays Classes Accordion'
		);
        parent::WP_Widget('gymbase_classes', __('Classes Accordion', $themename), $widget_options);
    }
	
	/** @see WP_Widget::widget */
    function widget($args, $instance) 
	{
		global $themename;
        extract($args);

		//these are our widget options
		$title = $instance['title'];
		$order = $instance['order'];
		$classes_page = $instance['classes_page'];
		$timetable_page = $instance['timetable_page'];

		echo $before_widget;
		
		query_posts(array( 
			'post_type' => 'classes',
			'posts_per_page' => '-1',
			'post_status' => 'publish',
			'orderby' => 'menu_order', 
			'order' => $order
		));

		$output = '';
		if($title) 
		{
			$output .= $before_title . $title . $after_title;
		}
		if(have_posts()):
			$output .= '<ul class="accordion">';
			while(have_posts()): the_post();
				global $post;
				$output .= '<li>
					<div id="accordion-' . $post->post_name . '">
						<h3>' . get_the_title() . '</h3>
						<h5>' . esc_attr(get_post_meta(get_the_ID(), $themename . "_subtitle", true)) . '</h5>
					</div>
					<div class="clearfix">
						<div class="item_content clearfix">';
							if(has_post_thumbnail())
								$output .= '<a class="thumb_image" href="' . get_permalink($classes_page) . '/#' . $post->post_name . '" title="' . get_the_title() . '">
									' . get_the_post_thumbnail(get_the_ID(), array("alt" => get_the_title(), "title" => "")) . '
								</a>';
				$output .= '<div class="text">
								' . get_the_excerpt() . '
							</div>
						</div>
						<div class="item_footer clearfix">
							<a class="more icon_small_arrow margin_right_white" href="' . get_permalink($classes_page) . '/#' . $post->post_name . '" title="' . __("Details", $themename) . '">' . __("Details", $themename) . '</a>
							<a class="more icon_small_arrow margin_right_white" href="' . get_permalink($timetable_page) . '/#' . $post->post_name . '" title="' . __("Timetable", $themename) . '">' . __("Timetable", $themename) . '</a>
						</div>
					</div>
				</li>';
			endwhile;
			$output .= '</ul>';
		endif;
		echo do_shortcode($output);
        echo $after_widget;
    }
	
	/** @see WP_Widget::update */
    function update($new_instance, $old_instance) 
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['order'] = strip_tags($new_instance['order']);
		$instance['classes_page'] = strip_tags($new_instance['classes_page']);
		$instance['timetable_page'] = strip_tags($new_instance['timetable_page']);
		return $instance;
    }
	
	 /** @see WP_Widget::form */
	function form($instance) 
	{	
		global $themename;
		$title = esc_attr($instance['title']);
		$order = esc_attr($instance['order']);
		$classes_page = esc_attr($instance['classes_page']);
		$timetable_page = esc_attr($instance['timetable_page']);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', $themename); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order', $themename); ?></label>
			<select id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
				<option <?php echo ($order=='ASC' ? ' selected="selected"':'');?> value='ASC'><?php _e("ASC", $themename); ?></option>
				<option <?php echo ($order=='DESC' ? ' selected="selected"':'');?> value='DESC'><?php _e("DESC", $themename); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('classes_page'); ?>"><?php _e('Classes Page', $themename); ?></label>
			<select id="<?php echo $this->get_field_id('classes_page'); ?>" name="<?php echo $this->get_field_name('classes_page'); ?>">
				<?php
				$args = array(
					'post_type' => 'page',
					'post_status' => 'publish',
					'posts_per_page' => -1,
					'orderby' => 'title', 
					'order' => 'ASC'
				);
				query_posts($args);
				if(have_posts()) : while (have_posts()) : the_post();
					global $post;
				?>
					<option <?php echo ($classes_page==get_the_ID() ? ' selected="selected"':'');?> value='<?php the_ID(); ?>'><?php the_title(); ?></option>
				<?php
				endwhile;
				endif;
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('timetable_page'); ?>"><?php _e('Timetable Page', $themename); ?></label>
			<select id="<?php echo $this->get_field_id('timetable_page'); ?>" name="<?php echo $this->get_field_name('timetable_page'); ?>">
				<?php
				if(have_posts()) : while (have_posts()) : the_post();
				?>
					<option <?php echo ($timetable_page==get_the_ID() ? ' selected="selected"':'');?> value='<?php the_ID(); ?>'><?php the_title(); ?></option>
				<?php
				endwhile;
				endif;
				?>
			</select>
		</p>
		<?php
	}
}
//register widget
add_action('widgets_init', create_function('', 'return register_widget("classes_widget");'));
?>