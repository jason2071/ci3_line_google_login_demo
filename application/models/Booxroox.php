<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booxroox extends CI_Model 
{

    public function getLastData()
    {
        $query = $this->db->get('category', 10);
        return $query->result();
    }

}