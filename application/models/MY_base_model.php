<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/application/libraries/GoogleMap.php');

abstract class MY_base_model extends CI_Model
{
    public $table;
    /** @var  CI_DB_query_builder */
    public $db;
    /** @var  CI_Controller */
    public $_ci;
    /** @var  CI_Loader */
    public $load;
    public $entity_name;
    protected $image_types = ['jpg','jpeg','gif','png' ,'bmp']; // File extensions
    protected $images_path;
    protected $images_url;
    protected $fields = [];

//    /** @var  CI_Email */
//    public $email;

    public $table_fields;

    public $boolean_fields = [];

    public $int_fields = [];


    public function __construct($table)
    {
        $this->table = $table;
        parent::__construct();
        $this->images_path = asset_path()."/images/{$this->entity_name}/";
        $this->images_url = asset_url()."/images/{$this->entity_name}/";
        $this->table_fields = array_flip($this->db->list_fields($table));


    }

    public function add($data)
    {
        $data = $this->check_fields($data);
        if(array_key_exists('created', $this->table_fields)) $data['created'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        $CR = new CI_DB_result($this->db);
        return mysqli_insert_id($CR->conn_id);
    }

    public function update($id, array $data)
    {
        $data = $this->check_fields($data);
        if(array_key_exists('updated', $this->table_fields)) $date['updated'] = date('Y-m-d H:i:s');
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table,['id' => $id]);
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table,['id' => $id])->row_array();
    }

    public function process_row($array)
    {
        foreach($array as $field => &$value)
        {
            if(in_array($field, $this->boolean_fields))
            {
                $value = !!$value;
            }

        }
        return $array;
    }

    public function process_batch($batch)
    {
        foreach($batch as &$array)
        {
            $array = $this->process_row($array);
        }
        return $batch;
    }

    protected function validate($data, $id = null)
    {
        $exist = $id ? $this->get_by_id($id) : [];
        if($id && !$exist)
        {
            throw new Exception(sprintf(lang('not_found'), ucfirst(lang($this->entity_name))), 404);
        }
        $errors  = [];
        foreach($this->fields as $field => $rule)
        {
            if(
                $exist &&
                array_key_exists($field, $exist) &&
                array_key_exists($field, $data) &&
                $data[$field] == $exist[$field])
            {
                continue;
            }
            if($rule['required'] && ((!array_key_exists($field, $data) && !array_key_exists($field, $exist)) || (array_key_exists($field, $data) && empty($data[$field]))))
            {
                $errors[$field] = sprintf(lang('empty_field'), ucfirst(lang($field)));
                continue;
            }
            elseif(!array_key_exists($field, $data) || empty($data[$field])) continue;
            elseif(in_array($field, $this->int_fields) && !is_int($data[$field]))
            {
                $errors[$field] = sprintf(lang('not_int'), ucfirst(lang($field)));
                continue;
            }
            elseif($field == 'email' && !$this->is_valid_email($data[$field]))
            {
                $errors[$field] = sprintf(lang('invalid_field'),ucfirst(lang($field)));
                continue;
            }
            elseif($rule['min_length'] > strlen($data[$field]))
            {
                $errors[$field] = sprintf(lang('short_field'), ucfirst(lang($field)), $rule['min_length']);
            }
            elseif($rule['max_length'] < 0 &&  $rule['max_length'] < strlen($data[$field]))
            {
                $errors[$field] = sprintf(lang('long_field'), ucfirst(lang($field)), $rule['max_length']);
            }
            elseif($rule['unique'] && !$this->is_unique($field, $data[$field], $id))
            {
                $errors[$field] = sprintf(lang('used_field'), ucfirst(lang($field)));
            }
            elseif(array_key_exists('presets', $rule) && !in_array($data[$field], $rule['presets']))
            {
                $errors[$field] = sprintf(lang('invalid_field'),ucfirst(lang($field)));
            }
        }
        return $errors;
    }

    protected function is_unique($field, $value, $id)
    {
        $where = [$field => $value];
        if($id) $where[] = "id != {$id}";
        return !$this->db->get_where($this->table, $where)->row_array();
    }

    protected function is_valid_email($email)
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }


    public function photo_upload($id, $file)
    {
        if (!empty($_FILES) && array_key_exists($file, $_FILES)) {
            $tempFile = $_FILES[$file]['tmp_name'];
            if(!is_dir($this->images_path))
            {
                mkdir($this->images_path, DIR_WRITE_MODE, TRUE);
            }


            $fileParts = pathinfo($_FILES[$file]['name']);
            $extension = strtolower($fileParts['extension']);
            $filename = "{$file}_{$id}.{$extension}";
            $targetFile = rtrim($this->images_path,'/') . '/' . $filename;

            if (
                in_array($extension,$this->image_types) &&
                move_uploaded_file($tempFile,$targetFile)
            ) {
                return  $this->images_url.$filename;
            } else {
                return null;
            }
        }
    }

    public function delete_photo($id, $file)
    {

        $filename = "{$file}_{$id}.";
        foreach($this->image_types as $extension)
        {
            if(file_exists($this->images_path.$filename.$extension))
            {
                unlink($this->images_path.$filename.$extension);
                return true;
            }
        }
        return false;
    }

    public function get_photo($id, $file)
    {
        $filename = "{$file}_{$id}.";
        foreach($this->image_types as $extension)
        {
            if(file_exists($this->images_path.$filename.$extension))
            {
                return $this->images_url.$filename.$extension;
            }
        }
        return '';
    }

    protected function check_fields($data)
    {
        foreach($data as $key => $value)
        {
            if(!array_key_exists($key, $this->table_fields)) unset($data[$key]);
        }
        return $data;
    }

}