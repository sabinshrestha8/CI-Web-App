<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Article_model extends CI_Model
{

    function getArticle()
    {
    }

    function getArticles($param = array())
    {
        if (isset($param['offset']) && isset($param['limit'])) {
            $this->db->limit($param['offset'], $param['limit']);
        }

        if (isset($param['q'])) {
            // $this->db->like('title', $param['q']);
            $this->db->or_like('title', trim($param['q']));
            $this->db->or_like('author', trim($param['q']));
        }

        $query = $this->db->get('articles');

        // echo $this->db->last_query();

        $articles = $query->result_array();
        return $articles;
    }

    function getArticlesCount($param = array())
    {
        if (isset($param['q'])) {
            $this->db->or_like('title', trim($param['q']));
            $this->db->or_like('author', trim($param['q']));
        }

        $count = $this->db->count_all_results('articles');
        return $count;
    }

    // This method will save a article in DB
    function addArticle($formArray)
    {
        $this->db->insert('articles', $formArray);
        return $this->db->insert_id();
    }

    function updateArticle()
    {
    }

    function deleteArticle()
    {
    }
}
