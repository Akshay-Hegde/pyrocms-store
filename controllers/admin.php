<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a store module for PyroCMS
 *
 * @author 		Jaap Jolman And Kevin Meier - pyrocms-store Team
 * @website		http://jolman.eu
 * @package 	PyroCMS
 * @subpackage 	Store Module
**/
class Admin extends Admin_Controller
{

	public function __construct()
	{
		parent::__construct();

		// Load all the required classes
		$this->load->model('store_m');
		$this->load->library('form_validation');
		$this->load->language('store');
		$this->load->helper('date');

		
		// Set the validation rules
		$this->item_validation_rules = array(
				array('field' => 'name',					'label' => 'lang:store_field_name',					'rules' => 'trim|max_length[50]|required'),
				array('field' => 'email',					'label' => 'lang:store_field_email',				'rules' => 'trim|max_length[100]|required|valid_email'),
				array('field' => 'additional_emails',		'label' => 'lang:store_field_additional_emails',	'rules' => 'trim|max_length[100]|valid_emails'),
				array('field' => 'currency',				'label' => 'lang:store_field_currency',				'rules' => 'trim|max_length[10]|required|is_natural_no_zero'),
				array('field' => 'item_per_page',			'label' => 'lang:store_field_item_per_page',		'rules' => 'trim|max_length[10]|required'),
				array('field' => 'show_with_tax',			'label' => 'lang:store_field_show_with_tax',		'rules' => 'required'),
				array('field' => 'display_stock',			'label' => 'lang:store_field_display_stock',		'rules' => 'required'),
				array('field' => 'allow_comments',			'label' => 'lang:store_field_allow_comments',		'rules' => 'required'),
				array('field' => 'new_order_mail_alert',	'label' => 'lang:store_field_new_order_mail_alert',	'rules' => 'required'),
				array('field' => 'active',					'label' => 'lang:store_field_active',				'rules' => 'required'),
				array('field' => 'terms_and_conditions',	'label' => 'lang:store_field_agb',					'rules' => 'required'),
				array('field' => 'privacy_policy',			'label' => 'lang:store_field_privacy_policy',		'rules' => 'required'),
				array('field' => 'delivery_information',	'label' => 'lang:store_field_delivery_information',	'rules' => 'required')
		);

		$this->form_validation->set_rules($this->item_validation_rules);
		
		// We'll set the partials and metadata here since they're used everywhere
		$this->template->set_partial('shortcuts', 'admin/partials/shortcuts')
						->append_metadata(js('admin.js', 'store'))
						->append_metadata(css('admin.css', 'store'));
	}

	/**
	 * List all available Stores
	 */
	
	public function create()
	{
		// Set the validation rules
		$this->form_validation->set_rules($this->item_validation_rules);

		// Something went wrong..
		if ($this->form_validation->run()==FALSE)
		{
			$this->data = array(
			);
			
			// Flash data
			$this->session->set_flashdata('error', lang('store_messages_create_error'));
			
			// Loading the view
			$this->template
				->title($this->module_details['name'], lang('store_title_new_store'))
				->build('admin/create',$this->data);
		}	
		else
		{
			$this->session->set_flashdata('success', lang('store_messages_create_success'));
			$this->store_m->insert();
			redirect('admin/store');
		}
	}	
	
	public function index()
	{
		// Set the validation rules
		$this->form_validation->set_rules($this->item_validation_rules);

		// Something went wrong..
		if ($this->form_validation->run()==FALSE)
		{
			$this->sql					= $this->store_m->fill_edit($this->store_m->get_store_id());
			
			$this->data = array(
				'sql'			=>	$this->sql
			);
			
			// Flash data
			if (isset($save)){
				$this->session->set_flashdata('error', lang('store_messages_edit_error'));
			}	
			
			// Loading the view
			$this->template
				->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
				->title($this->module_details['name'], lang('store_title_edit_store'))
				->build('admin/index',$this->data);
		}	
		else
		{
			
			$this->session->set_flashdata('success', lang('store_messages_edit_success'));
			$this->store_m->edit($this->store_m->get_store_id());
			redirect('admin/store');
		}
	}
	
	public function delete()
	{
		$this->session->set_flashdata('success', lang('store_messages_delete_success'));
		$this->store_m->delete($this->uri->segment(4));
		redirect('admin/store');
	}	

	public function add_category()
	{
		
		$id = $this->store_m->get_store_id();
		$this->load->library('form_validation');
		$this->form_validation->_field_data=array();
		$fields = array(
			array('field' => 'name',					'label' => 'store_cat_add_name',					'rules' => 'trim|max_length[50]|required'),
			array('field' => 'html',					'label' => 'store_cat_add_html',					'rules' => 'trim|max_length[1000]|required'),
			array('field' => 'parent_id',				'label' => 'store_cat_add_parent_id',				'rules' => 'trim|max_length[10]|'),
			array('field' => 'images_id',				'label' => 'store_cat_add_images_id',				'rules' => 'trim|max_length[10]|'),
			array('field' => 'thumbnail_id',			'label' => 'store_cat_add_thumbnail_id',			'rules' => 'trim|max_length[10]|'),
			array('field' => 'store_store_id',			'label' => 'store_cat_add_store_store_id',			'rules' => 'trim|max_length[10]|')
			);

		$this->form_validation->set_rules($fields);


		if ($this->form_validation->run())
		{
                    if ($this->store_m->add_category($_POST))
                    {
                        $this->session->set_flashdata('success', sprintf(lang('store_cat_add_success'), $this->input->post('name')));
                            redirect('admin/store');
                    }
                    else
                    {
                        $this->session->set_flashdata(array('error'=> lang('store_cat_add_error')));
                    }
		}

		if($id){$this->data->parent_id = $id;}else{$this->data->parent_id = '';}
		$this->data->categories = $this->store_m->make_categories_dropdown();

		$this->template
			->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
			->build('admin/add_category', $this->data);
	}
	
	public function add_product()
	{
		
		$id = $this->store_m->get_store_id();
		$this->load->library('form_validation');
		$this->form_validation->_field_data=array();
		$fields = array(
			array('field' => 'name',					'label' => 'store_product_add_name',					'rules' => 'trim|max_length[50]|required'),
			array('field' => 'html',					'label' => 'store_product_add_html',					'rules' => 'trim|max_length[1000]|required'),
			array('field' => 'categories_id',			'label' => 'store_product_add_categories_id',			'rules' => 'trim|max_length[10]|required'),
			array('field' => 'images_id',				'label' => 'store_product_add_images_id',				'rules' => 'trim|max_length[10]|'),
			array('field' => 'thumbnail_id',			'label' => 'store_product_add_thumbnail_id',			'rules' => 'trim|max_length[10]|'),
			array('field' => 'config_id',				'label' => 'store_product_add_store_config_id',			'rules' => 'trim|max_length[10]|'),
			array('field' => 'products_id',				'label' => 'store_product_add_store_products_id',		'rules' => 'trim|max_length[10]|'),
			array('field' => 'attributes_id',			'label' => 'store_product_add_store_attributes_id',		'rules' => 'trim|max_length[10]|'),
			array('field' => 'meta_description',		'label' => 'store_product_add_meta_description',		'rules' => 'trim|max_length[1000]|'),
			array('field' => 'meta_keywords',			'label' => 'store_product_add_meta_keywords',			'rules' => 'trim|max_length[1000]|'),
			array('field' => 'price',					'label' => 'store_product_add_price',					'rules' => 'trim|max_length[10]|required'),
			array('field' => 'stock',					'label' => 'store_product_add_stock',					'rules' => 'trim|max_length[10]|'),
			array('field' => 'limited',					'label' => 'store_product_add_limited',					'rules' => 'trim|max_length[10]|'),
			array('field' => 'limited_used',			'label' => 'store_product_add_limited_used',			'rules' => 'trim|max_length[10]|'),
			array('field' => 'discount',				'label' => 'store_product_add_discount',				'rules' => 'trim|max_length[10]|'),
			array('field' => 'images_id',				'label' => 'store_product_add_images_id',				'rules' => 'trim|max_length[10]|'),
			array('field' => 'thumbnail_id',			'label' => 'store_product_add_thumbnail_id',			'rules' => 'trim|max_length[10]|'),
			array('field' => 'allow_comments',			'label' => 'store_product_add_allow_comments',			'rules' => 'trim|max_length[10]|required')
			);

		$this->form_validation->set_rules($fields);


		if ($this->form_validation->run())
		{
                    if ($this->store_m->add_product($_POST))
                    {
                        $this->session->set_flashdata('success', sprintf(lang('store_product_add_success'), $this->input->post('name')));
                            redirect('admin/store');
                    }
                    else
                    {
                        $this->session->set_flashdata(array('error'=> lang('store_product_add_error')));
                    }
		}


		$this->template
			->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
			->build('admin/add_product', $this->data);
	}

	
}
