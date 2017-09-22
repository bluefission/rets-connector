<?php 
namespace BlueFission\Rets;

use BlueFission\Net\HTTP;
use BlueFission\DevString;
use BlueFission\HTML\Template;
use BlueFission\Wordpress\WPUpdateable;

require_once ( plugin_dir_path( __FILE__ ) . 'WPUpdateable.php' );

class Listing extends WPUpdateable {
	const CONFIG_META = '_listing_data';

	private $_idx_data;
	private $_mapping;

	protected $_config = array(
	);

	protected $_labels = array(
		'Street Number',
		'Street Name',
		'Street Direction',
		'Street Suffix',
		'Unit Number',
		'City',
		'State',
		'Zip',
		'Price',
		'SQFT',
		'Beds',
		'Full Baths',
		'Half Baths',
		'Baths Total',
		'Description',
		'Agent Remarks',
		'Listing Status',
		'Stipulation of Sale',
		'Days On Market',
		'Geo Lat',
		'Geo Lon',
		'MLS ID',
		'County',
		'Subdivision',
		'Year Built',
		'Price Per SqFt',
		'Elementary School',
		'Middle School',
		'High School',
		'HOA',
		'HOA Company',
		'HOA Includes',
		'Building Style',
		'Location/Features',
		'Construction',
		'Construction Type',
		'Sub Type',
		'Exterior Finish',
		'Exterior Features',
		'Interior Features',
		'Fireplace',
		'Flooring',
		'Foundation',
		'Home Warranty',
		'Waterview',
		'Waterfront',
		'Lot Water Features',
		'Lot Demensions',
		'Lot Acres',
		'Lot Description',
		'Parking/Driveway',
		'Pool/Spa',
		'Porch/Balcony/Deck',
		'Water Heater',
		'Water/Sewer',
		'Sub',
		'Roof',
		'Heating System',
		'Cooling System',
		'Extras',
		'Outdoor Features',
		'Lot Dimensions',
		'Lot Size (in acres)',
		'Agent ID',
		'Office ID',
		'Office Name',
		'Listing Member Name',
		'Title',
		'Virtual Tour',
		'Address',
	);

	protected $_data = array(
		'mls_id'=>'',
		'photos'=>''
	);
	
	public function load() {
		// Get this card to also load in related gravity form data
		foreach ($this->_labels as $label) {
			$key = $this->formatAsKey($label);
			$this->_data[$key] = '';
		}

		if ( parent::load() ) {

			// $config_meta = self::CONFIG_META;

			$config = get_post_meta( $this->_post_id, self::CONFIG_META, true );
			
			if ( is_array($config) ) {
				$this->config($config);
			}

			if ( !is_numeric($this->mls_id) ) {
				$this->mls_id = get_post_custom_values( 'mls_id' );
			}

			$mls_id = $this->mls_id;
		}
	}

	public function value($string) {
		return $this->field($this->formatAsKey($string));
	}

	public function title() {
		$string = $this->street_number.' '.$this->street_direction.' '.$this->street_name.' '.$this->street_suffix.' '.$this->unit_number.' '.$this->city.' '.$this->state.' '.$this->zip;
		return $string;
	} 

	public function prepare() {
		// Place mapping here
		

		foreach ($this->_mapping as $map=>$source) {
			$field = $this->formatAsKey($map);
			if ($source) {
				$this->$field = $this->_idx_data[$source];
			}
		}

		// Generate Address
		$this->title = $this->street_number.' '.$this->street_direction.' '.$this->street_name.' '.$this->street_suffix.' '.$this->unit_number.' '.$this->city.' '.$this->state.' '.$this->zip;
		$this->photos = array();
	}

	public function mapping($mapping) {
		$this->_mapping = $mapping;
	}

	public function set($data) {
		$this->_idx_data = $data;
		$this->prepare();
	}

	public function save() {

		$args = array(
		   'meta_query' => array(
		       array(
		           'key' => 'mls_id',
		           'value' => $this->mls_id,
		           'compare' => '=',
		       )
		   )
		);
		$query = new \WP_Query($args);
		if ( $query->have_posts() ) {
			// The 2nd Loop
			while ( $query->have_posts() ) {
				$query->the_post();
				// echo '<li>' . get_the_title( $query->post->ID ) . '</li>';
				$this->_post_id = $query->post->ID;
			}

			// Restore original Post Data
			wp_reset_postdata();
		}

		if ( !$this->_post_id ) {
			$post = array(
				'post_title'    => wp_strip_all_tags( $this->title ),
				'post_content'  => $this->description,
				'post_status'   => 'publish',
				'post_author'   => 1,
				'post_type'=>'listing',
			);
			$this->_post_id = wp_insert_post($post);
		} else {
			$post = array(
				'ID'			=> $this->_post_id,
				'post_title'    => wp_strip_all_tags( $this->title ),
				'post_content'  => $this->description . '...',
			);
			$error = wp_update_post($post, true);
		}

		$status = parent::save();

		// $status = update_post_meta($this->_post_id, self::CONFIG_META, $this->_config);

		return $status;
	}

	public function get_data() {
		return $this->_data;
	}
}