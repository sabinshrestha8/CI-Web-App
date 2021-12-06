<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Category extends CI_Controller
{

    // This method will show category list page
    public function index()
    {

        $this->load->model('Category_model');
        $categories = $this->Category_model->getCategories();
        $data['categories'] = $categories;

        $this->load->view('admin/category/list', $data);
    }

    // This method will create category page
    public function create()
    {

        $this->load->helper('common_helper');

        $config['upload_path']    = './public/uploads/category/';
        $config['allowed_types']  = 'gif|jpg|png';
        $config['encrypt_name']   = true;
        // print_r($config);

        $this->load->library('upload', $config);


        $this->load->model('Category_model');
        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('<p class= "invalid-feedback">', '</p>');
        $this->form_validation->set_rules('name', 'Name', 'trim|required');

        if ($this->form_validation->run() == TRUE) {

            if (!empty($_FILES['image']['name'])) {
                // Now user has selected a file so we will proceed

                if ($this->upload->do_upload('image')) {
                    // // File uploaded successfully

                    // $data = $this->upload->data();

                    // Resizing an Image
                    resizeImage($config['upload_path'] . $this->upload->data('file_name'), $config['upload_path'] . 'thumb/' . $this->upload->data('file_name'), 300, 270);

                    // we will save categories in database
                    $formArray['image'] = $this->upload->data('file_name');
                    $formArray['name'] = $this->input->post('name');
                    $formArray['status'] = $this->input->post('status');
                    $formArray['created_at'] = date('Y-m-d H:i:s');

                    $this->Category_model->create($formArray);

                    $this->session->set_flashdata('success', 'Category added successfully');
                    redirect(base_url() . 'admin/category/index');
                } else {
                    // we got some errors
                    $error = $this->upload->display_errors("<p class= 'invalid-feedback'>", "</p>");
                    $data['errorImageUpload'] = $error;
                    $this->load->view('admin/category/create', $data);
                }
            } else {
                // we will create category without image
                // we will save categories in database
                $formArray['name'] = $this->input->post('name');
                $formArray['status'] = $this->input->post('status');
                $formArray['created_at'] = date('Y-m-d H:i:s');

                $this->Category_model->create($formArray);

                $this->session->set_flashdata('success', 'Category added successfully');
                redirect(base_url() . 'admin/category/index');
            }
        } else {
            // will show errors
            $this->load->view('admin/category/create');
        }

        // $this->load->view('admin/category/create');
    }

    // This method will edit category page
    public function edit()
    {
    }

    // This method will delete a category
    public function delete()
    {
    }
}
