<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 */
class Mx_common {
    var $base; // the base url for this module			
    var $form_base; // base url for forms
    var $module_name = "mx_easy_backup";
    var $settings = array();
    var $backup_filename = '';
    var $dir_tree = array();
    
    
    function Mx_common() {
        $this->EE =& get_instance();
    }
    
    /**
     * Saves the specified settings array to the database.
     *
     * @since Version 1.0.0
     * @access protected
     * @param array $settings an array of settings to save to the database.
     * @return void
     **/
    function getSettings($refresh = FALSE) {
        $settings = FALSE;
        if (isset($this->EE->session->cache[$this->module_name][__CLASS__]['settings']) === FALSE || $refresh === TRUE) {
            $settings_query = $this->EE->db->select('settings')->where('module_name', $this->module_name)->get('modules', 1);
            
            if ($settings_query->num_rows()) {
                $settings = unserialize($settings_query->row()->settings);
                $this->saveSettingsToSession($settings);
            }
        } else {
            $settings = $this->EE->session->cache[$this->module_name][__CLASS__]['settings'];
        }
        return $settings;
    }
    
    /**
     * Saves the specified settings array to the session.
     * @since Version 1.0.0
     * @access protected
     * @param array $settings an array of settings to save to the session.
     * @param array $sess A session object
     * @return array the provided settings array
     **/
    function saveSettingsToSession($settings, &$sess = FALSE) {
        // if there is no $sess passed and EE's session is not instaniated
        if ($sess == FALSE && isset($this->EE->session->cache) == FALSE)
            return $settings;
        
        // if there is an EE session available and there is no custom session object
        if ($sess == FALSE && isset($this->EE->session) == TRUE)
            $sess =& $this->EE->session;
        
        // Set the settings in the cache
        $sess->cache[$this->module_name][__CLASS__]['settings'] = $settings;
        
        // return the settings
        return $settings;
    }
    /**
     * Saves the specified settings array to the database.
     *
     * @since Version 1.0.0
     * @access protected
     * @param array $settings an array of settings to save to the database.
     * @return void
     **/
    function saveSettingsToDB($settings) {
        $this->EE->db->where('module_name', $this->module_name)->update('modules', array(
            'settings' => serialize($settings)
        ));
        
    }
    
    
    
    /**
     **/
    function saveTaskToDB($settings, $task_id = false) {
        if ($task_id) {
            $this->EE->db->where('task_id', $task_id)->update('mx_easy_backup_tasks', array(
                'settings' => serialize($settings)
            ));
            return true;
        } else {
            $this->EE->db->insert('mx_easy_backup_tasks', array(
                'task_id' => '',
                'site_id' => SITE_ID,
                'settings' => serialize($settings)
            ));
            $this->EE->db->insert_id();
        }
    }
    
    function getTask($refresh = FALSE, $task_id) {
        $settings = FALSE;
        
        $settings_query = $this->EE->db->select('settings')->where('task_id', $task_id)->get('mx_easy_backup_tasks', 1);
        
        if ($settings_query->num_rows()) {
            $settings = unserialize($settings_query->row()->settings);
            
        }
        
        return $settings;
    }
    
    function format_size($rawSize) {
        if ($rawSize / 1073741824 > 1)
            return round($rawSize / 1073741824, 1) . $this->EE->lang->line('gib');
        else if ($rawSize / 1048576 > 1)
            return round($rawSize / 1048576, 1) . $this->EE->lang->line('mib');
        else if ($rawSize / 1024 > 1)
            return round($rawSize / 1024, 1) . $this->EE->lang->line('kib');
        else
            return round($rawSize, 1) . $this->EE->lang->line('bytes');
    }
    
    

    
}