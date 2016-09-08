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
            'Related posts setting', 
            'manage_options', 
            'RP_setting_admin', 
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
                do_settings_sections( 'RP_setting_admin' );
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
            'RP_setting_admin' // Page
        );  

        add_settings_field(
            'seclects', // ID
            'Select (default : Dissable)', // Title 
            array( $this, 'seclects_callback' ), // Callback
            'RP_setting_admin', // Page
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
        if( isset( $input['seclects'] ) )
            $new_input['seclects'] =  $input['seclects'] ;

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
    public function seclects_callback(){
        $check1 = '';
        $check2 = '';
        $check3 = 'checked="checked';
        //var_dump($this->options['seclects']);
        //var_dump(get_option('seclects'));
        if($this->options['seclects']=="cat"){
                $check1 = 'checked="checked"';
                $check2 = "";
                $check3 = "";
        }
        elseif($this->options['seclects']=="tag") {
                $check2 = 'checked="checked"';
                $check1 = "";
                $check3 = "";
        }
        else{
            $check1 = "";
            $check2 = "";
            $check3 = 'checked="checked"';
        }
        //var_dump($check1);
        //var_dump($check2);
        //var_dump($this->options['seclects']);
        echo '<input type="radio" id="seclects" name="my_option_name[seclects]" value="tag" '.$check2.' />'.'Tag&nbsp;'.'<br>';
        echo '<input type="radio" id="seclects" name="my_option_name[seclects]" value="cat" '.$check1.' />'.'Catogories&nbsp;'.'<br>';
        echo '<input type="radio" id="seclects" name="my_option_name[seclects]" value="dissable" '.$check3.' />'.'Dissable&nbsp;';
        //var_dump($this->options);
    }
      public function hello_func($atts = array(), $content = null) { 
        if(!is_single()){
                return '';
        }
        $em = get_option('my_option_name');
        //var_dump($em['seclects']);
        if($em['seclects']=="cat"){
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
                        ?>
                        <div id="owl-demo" class="owl-carousel owl-theme">   
                        <?php
                        $my_query = new wp_query( $args );  
                        //var_dump($my_query);
                        while( $my_query->have_posts() ) {  
                        $my_query->the_post();  
                        ?>       
                        <div class="item">  
                            <a rel="external" href="<? the_permalink()?>"><?php the_post_thumbnail('thumbnail'); ?><br />  
                            <p><?php the_title(); ?> </p> 
                            </a>  
                        </div> 
                              
                        <?php }  
                        }  
                        $post = $orig_post;  
                        wp_reset_query();  
                        ?>  
                    </div> 
                </div>
                <?php  
        }
        elseif($em['seclects']=="tag") {
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
                        ?>
                        <div id="owl-demo" class="owl-carousel owl-theme">
                        <?php
                        $my_query = new wp_query( $args );  
                        //var_dump($my_query);
                        while( $my_query->have_posts() ) {  
                        $my_query->the_post();  
                        ?>   
                               
                        <div class="item">  
                            <a rel="external" href="<? the_permalink()?>"><?php the_post_thumbnail(array('thumbnail')); ?><br />  
                            <p><?php the_title(); ?>  </p>
                            </a>  
                        </div>        
                        <?php }  
                        }  
                        $post = $orig_post;  
                        wp_reset_query();  
                        ?>  
                        </div>
                </div>
                <?php  
                }
                elseif($em['seclects']=="dissable"){
                    return "";
                }
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
}
add_action( 'plugins_loaded', 'rp_load' );


function enqueue_scripts_and_styles()
{
        wp_register_style( 'rp-foundation', plugins_url( '/Related-Post--first-plugin/css/style.css'));
        wp_enqueue_style( 'rp-foundation' );
        //wp_register_style( 'rp', get_style_uri(), array(), get_theme_version() );
        wp_enqueue_style( 'rp' );
        wp_enqueue_script( 'owl-js', plugins_url('/Related-Post--first-plugin/OwlCarousel-master/owl-carousel/owl.carousel.js') , array('jquery'), '20151215', true );

        wp_enqueue_style( 'owl-theme', plugins_url( '/Related-Post--first-plugin/OwlCarousel-master/owl-carousel/owl.carousel.css') );
        wp_enqueue_script('jquery');
        wp_enqueue_script( 'custom', plugins_url('/Related-Post--first-plugin/js/script.js') , array('jquery'), '20151215', true );
 
}
add_action( 'wp_enqueue_scripts', 'enqueue_scripts_and_styles' );
