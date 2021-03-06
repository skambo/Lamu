<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Form_Groups
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Form_Group extends ORM {
	/**
	 * A form_group has and belongs to many attributes
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'form_attributes' => array('through' => 'form_groups_form_attributes'),
		);

	/**
	 * A form_group belongs to a form
	 *
	 * @var array Relationships
	 */
	protected $_belongs_to = array(
		'form' => array(),
		);

	/**
	 * Rules for the form_attribute model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'form_id' => array(
				array('numeric'),
				array(array($this, 'fk_exists'), array('Form', ':field', ':value'))
			),
			'label' => array(
				array('not_empty'),
				array('max_length', array(':value', 150))
			),
			'priority' => array(
				array('numeric')
			),
		);
	}

	/**
	 * Prepare group data for API
	 * 
	 * @return array $response - array to be returned by API (as json)
	 */
	public function for_api()
	{
		$response = array();
		if ( $this->loaded() )
		{
			$response = array(
				'id' => $this->id,
				'url' => URL::site('api/v'.Ushahidi_Api::version().'/forms/'.$this->form_id.'/groups/'.$this->id, Request::current()),
				'form' => empty($this->form_id) ? NULL : array(
					'url' => URL::site('api/v'.Ushahidi_Api::version().'/forms/'.$this->form_id, Request::current()),
					'id' => $this->form_id
				),
				'label' => $this->label,
				'priority' => $this->priority,
				'attributes' => array()
				);
			
			foreach ($this->form_attributes->find_all() as $attribute)
			{
				$response['attributes'][] = $attribute->for_api();
			}
		}
		else
		{
			$response = array(
				'errors' => array(
					'Group does not exist'
					)
				);
		}

		return $response;
	}
}