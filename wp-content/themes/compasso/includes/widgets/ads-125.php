<?php
class G7_Ads125_Widget extends G7_Widget {

	function __construct() {

		$this->widget = array(
			'id_base' => 'g7_ads125',
			'name' => G7_NAME . ' - Ads 125px',
			'description' => __('Banner (125 pixels width)', 'g7theme')
		);

		parent::__construct();
	}

	function set_fields() {
		$fields = array(
			'title' => array(
				'type' => 'text',
				'label' => __('Title', 'g7theme'),
				'std' => ''
			),
			'banner1_url' => array(
				'type' => 'text',
				'label' => __('Banner 1 image url', 'g7theme'),
				'std' => ''
			),
			'banner1_link' => array(
				'type' => 'text',
				'label' => __('Banner 1 link', 'g7theme'),
				'std' => ''
			),
			'banner2_url' => array(
				'type' => 'text',
				'label' => __('Banner 2 image url', 'g7theme'),
				'std' => ''
			),
			'banner2_link' => array(
				'type' => 'text',
				'label' => __('Banner 2 link', 'g7theme'),
				'std' => ''
			)
		);
		$this->fields = $fields;
	}

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);

		echo $before_widget;
		$title = apply_filters('widget_title', $instance['title']);
		if ($title) {
			echo $before_title . $title . $after_title;
		}
		?>

		<?php if ($instance['banner1_url']) : ?>
		<a href="<?php echo $instance['banner1_link']; ?>">
			<img class="banner1" src="<?php echo $instance['banner1_url']; ?>" alt="banner">
		</a>
		<?php endif; ?>

		<?php if ($instance['banner2_url']) : ?>
		<a href="<?php echo $instance['banner2_link']; ?>">
			<img class="banner2" src="<?php echo $instance['banner2_url']; ?>" alt="banner">
		</a>
		<?php endif; ?>

		<?php
		echo $after_widget;
	}

}
