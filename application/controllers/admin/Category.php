<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Category extends CI_Controller
{

    // This method will show category list page
    public function index()
    {

        $this->load->model('Category_model');
        $queryString = $this->input->get('q');
        $params['queryString'] = $queryString;
        
        $categories = $this->Category_model->getCategories($params);
        $data['categories'] = $categories;
        $data['queryString'] = $queryString;

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

        $this->form_validation->set_error_delimiters('<p class="invalid-feedback">', '</p>');
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
    public function edit($id)
    {
        // echo $id;
        $this->load->model('Category_model');
        $category = $this->Category_model->getCategory($id);
        // echo "<pre>";
        // print_r($category);
        // echo "</pre>";

        if (empty($category)) {
            $this->session->set_flashdata('error', 'Category not found!');
            redirect(base_url().'admin/category/index');

        }

        $this->load->helper('common_helper');

        $config['upload_path']    = './public/uploads/category/';
        $config['allowed_types']  = 'gif|jpg|png';
        $config['encrypt_name']   = true;
        $this->load->library('upload', $config);

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
                    $formArray['updated_at'] = date('Y-m-d H:i:s');

                    $this->Category_model->update($id, $formArray);

                    // delete existed image i.e category image & thumb image
                    if (file_exists('./public/uploads/category/'.$category['image'])) {
                        unlink('./public/uploads/category/' . $category['image']);
                    }

                    if (file_exists('./public/uploads/category/thumb/' . $category['image'])) {
                        unlink('./public/uploads/category/thumb/' . $category['image']);
                    }

                    $this->session->set_flashdata('success', 'Category updated successfully');
                    redirect(base_url() . 'admin/category/index');
                } else {
                    // we got some errors
                    $error = $this->upload->display_errors("<p class= 'invalid-feedback'>", "</p>");
                    $data['errorImageUpload'] = $error;
                    $data['category'] = $category;
                    $this->load->view('admin/category/edit', $data);
                }
            } else {
                // we will create category without image
                // we will save categories in database
                $formArray['name'] = $this->input->post('name');
                $formArray['status'] = $this->input->post('status');
                $formArray['updated_at'] = date('Y-m-d H:i:s');

                $this->Category_model->update($id, $formArray);

                $this->session->set_flashdata('success', 'Category updated successfully');
                redirect(base_url() . 'admin/category/index');
            }

        } else {
            $data['category'] = $category;
            $this->load->view('admin/category/edit', $data);
        }

    }

    // This method will delete a category
    public function delete($id)
    {
        $this->load->model('Category_model');
        $category = $this->Category_model->getCategory($id);
        // echo "<pre>";
        // print_r($category);
        // echo "</pre>";

        if (empty($category)) {
            $this->session->set_flashdata('error', 'Category not found!');
            redirect(base_url() . 'admin/category/index');
        }

        // delete existed image i.e category image & thumb image
        if (file_exists('./public/uploads/category/' . $category['image'])) {
            unlink('./public/uploads/category/' . $category['image']);
        }

        if (file_exists('./public/uploads/category/thumb/' . $category['image'])) {
            unlink('./public/uploads/category/thumb/' . $category['image']);
        }

        $this->Category_model->delete($id);

        $this->session->set_flashdata('success', 'Category deleted successfully');
        redirect(base_url().'admin/category/index');

    }

}