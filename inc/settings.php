<?php

class bcroles {
	private $bcrole_settings_option;
	public function __construct(){

        add_action( 'admin_menu', array( $this, 'bc_roles_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'bcrole_settings_page_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_enqueue' ) );
    }
    public function bc_roles_add_plugin_page() {
		add_submenu_page(
            'bweb-component',
			'Hide Menu', // page_title
			'Hide Menu', // menu_title
			'manage_options', // capability
			'hide_menu', // menu_slug
			array( $this, 'bc_roles_create_admin_page' ) // function
		);
	}
    public function bc_roles_create_admin_page() {
		$this->bcrole_settings_option = get_option( 'bcrole_settings_option' );
    ?>
		<div id="setting_hide_menu" class="wrap">
			<h2 class="wp-heading-inline">Hide Menu</h2>
			<p></p>
			<?php settings_errors(); ?>
            
				<form method="post" action="options.php">
					<?php
						settings_fields( 'bcrole_settings_option_group' );
						do_settings_sections( 'bcrole-settings-admin' );
						submit_button();
					?>
				</form>

		</div>
        
    <?php
    }
	
	public function bcrole_settings_page_init() {
		register_setting(
			'bcrole_settings_option_group', // option_group
			'bcrole_settings_option', // option_name
			array( $this, 'bcrole_settings_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'bcrole_settings_setting_section', // id
			'Nascondi menu', // title
			array( $this, 'bcrole_settings_section_info' ), // callback
			'bcrole-settings-admin' // page
		);

		add_settings_field(
			'select_menu_admin', // id
			'Menu', // title
			array( $this, 'select_menu_admin_callback' ), // callback
			'bcrole-settings-admin', // page
			'bcrole_settings_setting_section' // section
		);

	}
	public function bcrole_settings_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['menu_admin'] ) ) {
			$sanitary_values['menu_admin'] = $input['menu_admin'];
		}
		return $sanitary_values;

	}
	public function bcrole_settings_section_info() {
		
	}

	public function select_menu_admin_callback() {
		global $menu, $submenu;
		/*$role = get_role( $_GET['role'] );*/
		?>
		<div id="table_menu">
			<div class="table_menu_column">
				<h4>Menu/Submenu</h4>
				<?php
				if(is_array($menu)){
				foreach($menu as $m => $m_value) : 
					if($m_value[4] != 'wp-menu-separator'){
						if($m_value[1]!='read'){
							printf(
								'<div class="row_menu">%s</div>',
								$m_value[0]
							);
							if(isset( $submenu[$m_value[2]] ) ){
								foreach($submenu[$m_value[2]] as $sm => $sm_value) : 
									if($sm_value[2]!=$m_value[2]):
										printf(
											'<div class="row_smenu">- %s</div>',
											$sm_value[0]
										);
									endif;
								endforeach;
							}
						}
					}
				endforeach;
				}

				?>
			</div>
		<?php
		foreach($this->get_editable_roles() as $x => $x_value) :
			if ( $x != 'administrator' ) :
				echo '<div class="table_menu_column chk">';
                echo '<h4>'.$x_value['name'].'</h4>';
				if(is_array($menu)){
				foreach($menu as $m => $m_value) : 
					/*
						[0] => Bacheca 
						[1] => read 
						[2] => index.php 
						[3] => 
						[4] => menu-top menu-top-first menu-icon-dashboard menu-top-last 
						[5] => menu-dashboard 
						[6] => dashicons-dashboard 
					*/
					if($m_value[4] != 'wp-menu-separator'){
						if($m_value[1]!='read'){
							if($m_value[2] != 'index.php'){
								printf(
									'<div class="row_menu"><input type="checkbox" name="bcrole_settings_option[menu_admin][%s][]" id="menu_admin" value="%s" %s></div>',
									$x,
									$m_value[2],
									( isset( $this->bcrole_settings_option['menu_admin'][$x] ) && in_array( $m_value[2], $this->bcrole_settings_option['menu_admin'][$x]) ) ? 'checked' : ''
								);
							}else{
								echo '<div class="row_menu"></div>';
							}
							if(isset( $submenu[$m_value[2]] ) ){
								foreach($submenu[$m_value[2]] as $sm => $sm_value) : 
									if($sm_value[2]!=$m_value[2]):
										printf(
											'<div class="row_smenu"><input type="checkbox" name="bcrole_settings_option[menu_admin][%s][]" id="menu_admin" value="%s" %s></div>',
											$x,
											$sm_value[2],
											( isset( $this->bcrole_settings_option['menu_admin'][$x] ) && in_array( $sm_value[2], $this->bcrole_settings_option['menu_admin'][$x]) ) ? 'checked' : ''
										);
									endif;
								endforeach;
							}
						}
					}
				endforeach;
				}
				echo '</div>';
			endif;
		endforeach;
		?>
		</div>
		<?php
	}

	public function get_editable_roles() {
		global $wp_roles;
	
		$all_roles = $wp_roles->roles;
		$editable_roles = apply_filters('editable_roles', $all_roles);
	
		return $editable_roles;
	}

	public function load_enqueue($hook){
		if($hook == 'bweb-component_page_hide_menu'){
			wp_enqueue_style( 'bc_hidemenu_settings_css', plugin_dir_url( DIR_COMPONENT .  '/bweb_component_functions/' ).'hide_menu/assets/style.css');
			//wp_enqueue_script( 'bc_settings_js', plugin_dir_url( DIR_COMPONENT .  '/bweb_component_functions/' ).'assets/script/script.js');
		}
    }

}
$bcroles = new bcroles();