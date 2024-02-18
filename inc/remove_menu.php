<?php
class bcroles_removemenu {
    private $bcrole_settings_option;

    public function __construct(){
        add_action('admin_head', array($this, 'hide_menu'));
    }

    public function hide_menu() {
        $this->bcrole_settings_option = get_option( 'bcrole_settings_option' );
        $roles = ( array ) wp_get_current_user()->roles;
        foreach($this->get_editable_roles() as $x => $x_value) :
            if($roles[0]==$x):
                //print_r($this->bcrole_settings_option);
                if(isset( $this->bcrole_settings_option['menu_admin'][$x])):
                    foreach($this->bcrole_settings_option['menu_admin'][$x] as $v => $v_value) :
                        //print_r($v_value);
                        remove_menu_page(  $v_value );
                    endforeach;
                endif;
            endif;
        endforeach;
    }
    public function get_editable_roles() {
		global $wp_roles;
	
		$all_roles = $wp_roles->roles;
		$editable_roles = apply_filters('editable_roles', $all_roles);
	
		return $editable_roles;
	}
}
$bcroles_removemenu = new bcroles_removemenu();