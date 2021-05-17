<?php

namespace JReviews\WordPress\JReviewsPostMeta;

class PostMeta
{
    protected $db;

    public function __construct()
    {
        global $wpdb;

        $this->db = $wpdb;

        add_filter('get_post_metadata', [$this, 'getPostMeta'], 10, 4);
    }

    public function getPostMeta($value, $post_id, $meta_key, $single)
    {
        $post = get_post($post_id);

        if ($value) {
            return $value;
        }
    
        if (strpos($meta_key, 'jreviews:') !== 0) {
            return $value;
        }

        $data = false;
        
        $field = explode(':', $meta_key)[1];

        if (strpos($field, 'jr_') === 0) {
            $data = $this->loadFieldData($post_id, $field);
        } else {
            $data = $this->loadListingData($post_id, $field);
        }

        // Needs to return array even if data is an array
        return [
            $data,
        ];
    }

    public function loadFieldData(int $id, $fname)
    {
        // Get stored value
        $sql = "select {$fname} from {$this->db->prefix}jreviews_content where contentid = {$id}";
                
        $value = $this->db->get_var($sql);
        
        $field = $this->getFieldInfo($fname);

        $data = false;

        switch ($field->type) {
            case 'select':
            case 'selectmultiple':
            case 'radiobuttons':
            case 'checkboxes':
                $data = $this->getFieldOptionText($field->fieldid, $value);

                break;
            case 'text':
            case 'decimal':
            case 'integer':
            case 'date':
            case 'website':
            case 'email':
                $data = $value;

                break;
            case 'relatedlisting':
                $data = $this->getRelatedListings($field->fieldid, $value);

            break;
        }

        return $data;
    }

    protected function getFieldInfo($fname)
    {
        static $cache = [];

        // Get field type
        if (empty($cache[$fname])) {
            $sql = sprintf("select fieldid, type from {$this->db->prefix}jreviews_fields where name = '%s'", esc_sql($fname));
                
            $field = $this->db->get_row($sql);

            $cache[$fname] = $field;
        } else {
            $field = $cache[$fname];
        }

        return $field;
    }

    protected function loadListingData(int $id, $field)
    {
        $data = false;
        
        switch ($field) {
            case 'rating':
                $sql = "select user_rating from {$this->db->prefix}jreviews_listing_totals where listing_id = {$id}";

                $data = number_format($this->db->get_var($sql), 2);

                break;
        }

        return $data;
    }

    protected function getFieldOptionText(int $fieldId, $values)
    {
        $values = static::parseStoredValues($values);

        $sql = sprintf("select text from {$this->db->prefix}jreviews_fieldoptions where fieldid = {$fieldId} and value IN (%s)", implode(',', $values));

        return $this->db->get_col($sql);
    }

    protected function getRelatedListings($ids, $values)
    {
        $values = static::parseStoredValues($values);

        $sql = sprintf("select post_title from {$this->db->prefix}posts where ID IN (%s)", implode(',', $values));

        return $this->db->get_col($sql);
    }

    protected static function parseStoredValues($values, $cast = 'string')
    {
        $values = rtrim(ltrim($values, '*'), '*');

        $values = explode('*', $values);

        $values = array_map(function ($value) use ($cast) {
            return $cast == 'string' ? "'".esc_sql($value)."'" : esc_sql($value);
        }, $values);

        return $values;
    }
}
