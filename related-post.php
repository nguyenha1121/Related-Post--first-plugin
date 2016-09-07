<?php
/**
 * Plugin Name: Related Post S
 * Plugin URI: http://learning.net 
 * Description: Đây là plugin đầu tiên mà tôi viết dành riêng cho WordPress, chỉ để học tập mà thôi. 
 * Version: 1.0 
 * Author: Hanguyen
 * Author URI: http://hanguyen.net
 * License: GPLv2
 */
?>

<?php
//option

class MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
        if(!function_exists('add_shortcode')) {
                return;
        }
        add_shortcode( 'hello' , array(&$this, 'hello_func') );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'My Settings', 
            'manage_options', 
            'my-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'my_option_name' );
        ?>
        <div class="wrap">
            <h1>Related posts setting</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'my-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'my_option_group', // Option group
            'my_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Related posts for ?', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );  

        add_settings_field(
            'id_number', // ID
            'Select (default : Tag)', // Title 
            array( $this, 'id_number_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );      
     
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['id_number'] ) )
            $new_input['id_number'] =  $input['id_number'] ;

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function id_number_callback(){
        $check1 = '';
        $check2 = 'checked="checked';
        //var_dump($this->options['id_number']);
        var_dump(get_option('id_number'));
        if($this->options['id_number']=="cat"){
                $check1 = 'checked="checked"';
                $check2 = "";
        }
        else {
                $check2 = 'checked="checked"';
                $check1 = "";
        }
        //var_dump($check1);
        //var_dump($check2);
        var_dump($this->options['id_number']);
        echo '<input type="radio" id="id_number" name="my_option_name[id_number]" value="tag" '.$check2.' />'.'Tag&nbsp;'.'<br>';
        echo '<input type="radio" id="id_number" name="my_option_name[id_number]" value="cat" '.$check1.' />'.'Catogories&nbsp;';
        //var_dump($this->options);
    }
      public function hello_func($atts = array(), $content = null) { 
        if(!is_single()){
                return '';
        }
        $em = get_option('my_option_name');
        var_dump($em['id_number']);
        if($em['id_number']=="cat"){
               ?>
                <div>  
                    <h3>Related posts</h3>  
                    <?php  
                        $orig_post = $post;  

                        global $post;  

                        $cats = get_the_category($post->ID);  
                        //var_dump($post);
                        if ($cats) {  
                        $cat_ids = array();  
                        foreach($cats as $individual_cat) $cat_ids[] = $individual_cat->term_id;  
                       // foreach($cats as $individual_category) $category_ids[] = $individual_category->term_id;
                        $args=array(  
                        'category__in' => $cat_ids,  
                        'post__not_in' => array($post->ID),  
                        'posts_per_page'=>4, // Số bài viết liên quan muốn hiển thị.  
                        'caller_get_posts'=>1  
                        );  
                 
                        $my_query = new wp_query( $args );  
                        //var_dump($my_query);
                        while( $my_query->have_posts() ) {  
                        $my_query->the_post();  
                        ?>          
                        <div>  
                            <a rel="external" href="<? the_permalink()?>"><?php the_post_thumbnail(array(150,100)); ?><br />  
                            <?php the_title(); ?>  
                            </a>  
                        </div>        
                        <?php }  
                        }  
                        $post = $orig_post;  
                        wp_reset_query();  
                        ?>  
                </div>
                <?php  
        }
        else {
             ?>
                <div>  
                    <h3>Related posts</h3>  
                    <?php  
                        $orig_post = $post;  

                        global $post;  

                        $tags = wp_get_post_tags($post->ID);  
                        //var_dump($tags);
                        if ($tags) {  
                        $tag_ids = array();  
                        foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;  
                        $args=array(  
                        'tag__in' => $tag_ids,  
                        'post__not_in' => array($post->ID),  
                        'posts_per_page'=>4, // Số bài viết liên quan muốn hiển thị.  
                        'caller_get_posts'=>1  
                        );  
                 
                        $my_query = new wp_query( $args );  
                        //var_dump($my_query);
                        while( $my_query->have_posts() ) {  
                        $my_query->the_post();  
                        ?>          
                        <div>  
                            <a rel="external" href="<? the_permalink()?>"><?php the_post_thumbnail(array(150,100)); ?><br />  
                            <?php the_title(); ?>  
                            </a>  
                        </div>        
                        <?php }  
                        }  
                        $post = $orig_post;  
                        wp_reset_query();  
                        ?>  
                </div>
                <?php  
                };
        }

    /** 
     * Get the settings option array and print one of its values
     */
}

// if( is_admin() )
//     $my_settings_page = new MySettingsPage();
function rp_load() {
        global $rp;
        $rp = new MySettingsPage();
        // global $st;
        // $st = $this->options['id_number'];
}
add_action( 'plugins_loaded', 'rp_load' );
?>