<?php
class dc_cost_per_mile_admin_page{
    private $option_name = 'dc_settings';
    private $default_cost_per_mile = 2.5;


    public function __construct(){

        add_action('admin_enqueue_scripts', [$this,'add_scripts'] );
    }


    public function add_scripts(){
        $base_url  = get_theme_file_uri();

        wp_enqueue_style('font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
        wp_enqueue_style('semantic', 'https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css' );
    }


    public function display(){
        if (!current_user_can('manage_options')){
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }

        $settings = get_option( $this->option_name );
        $fields = [
            'costPerMile'=> [
                'type' => 'float',
                'title' => 'Cost Per Mile',
                'icon' => 'dollar sign',
                'placeholder' => 'cost',
                'default_val' => $this->default_cost_per_mile,
            ],
            'iframeUrl'=>[
                'type' => 'string',
                'title' => 'Iframe Url',
                'icon' => 'linkify',
                'placeholder' => 'https://link/to/iframe/file',
                'default_val' => 'https://swimmania.life/gmaps/taxi/index.php',
            ],
        ];

        if( $this->verify_nonce() && isset($_POST[$this->option_name]) ) {
            $inputs = $_POST[$this->option_name];
            $settings = [];

            foreach( $fields as $field_key => $field ){
                if( !isset($inputs[$field_key]) ){
                    continue;
                }

                $input = $inputs[$field_key];

                switch( $field['type'] ){
                    case 'float':
                        if( (float)$input ){
                            $settings[$field_key] = (float)$input;
                        }else{
                            unset( $settings[$field_key] );
                        }
                    break;
                    case 'string':
                        if( trim($input) ){
                            $settings[$field_key] = trim($input);
                        }else{
                            unset( $settings[$field_key] );
                        }
                    break;
                }
            }

            
            update_option( $this->option_name, $settings );

            ?>
                <div class="ui ignored positive message">
                    Settings saved
                </div>
            <?php
        }

        ?>
            <h2 class="ui center aligned large header">Distance Calculator Settings</h2>
            
            <form name="form1" method="post" action="" >
                <?php wp_nonce_field(basename(__FILE__), "wps-nonce"); ?>

                <div class="ui text container">
                    <div class="ui segments">
                        <?php foreach( $fields as $field_key => $field) : ?>
                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="four wide column">
                                        <label for="<?php echo $slug; ?>">
                                            <?php echo $field['title']; ?>
                                        </label>
                                    </div>
                                
                                    <div class="twelve wide column">
                                        <div class="ui left icon input fluid">
                                            <input 
                                                type="text"
                                                name="<?php echo $this->option_name.'['.$field_key.']'; ?>"
                                                value="<?php echo isset($settings[$field_key]) ? $settings[$field_key] : (isset($field['default_val'])?$field['default_val'] : '') ?>" 
                                                placeholder="<?php echo $field['placeholder'] ?>"
                                                size="20" />
                                            <i class="icon <?php echo $field['icon'] ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="ui segment">
                            <div class="ui grid">
                                <div class="four wide column">
                                    <label for="iframe">
                                        Iframe <span class="muted">( Copy and paste this iframe 
                                        on any site to show the calculator )</span>
                                    </label>
                                </div>
                            
                                <div class="twelve wide column" id="iframe-wrapper">
                                    <textarea id="iframe-text"><iframe src="<?php echo "$settings[iframeUrl]?cost_per_mile=$settings[costPerMile]" ?>" class="dc-iframe" style="border:none !important; width:100%; height:260px; margin:0 auto"></iframe><style>@media (max-width: 767.98px){iframe.dc-iframe{height: 350px !important;}}</style></textarea>
                                    <span class="copy-notice">Copied</span>
                                    <span class="copy-icon-wrapper"><i class="icon copy"></i></span>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>

                

                <div class="ui one column centered grid" style="margin-top:40px">
                    <div class="ui segment">
                        <button class="button button-primary">
                            Save changes
                        </button>
                    </div>
                </div>

            </form>
            
<style>
textarea#iframe-text{
    width: 100%;
    justify-content: start;
    height: 130px;
    line-height: 1.7;
    color: rgba(0, 0, 0, 0.5);
    padding: 5px;
    padding-top:15px;
}
span.muted{
    color: rgba(0, 0, 0, 0.42);
    font-style: italic;
    display: block;
    font-size: .9rem;
}

span.copy-notice,
span.copy-icon-wrapper{
    position:absolute;
    top:0;
    right:0;
    z-index: 2;
    padding: 5px 10px;
    margin: 15px;
    color: #fff;
    background: #444;
    font-size:0.9rem;
    opacity: 0;
    transition: opacity 0.8s;
}
span.copy-notice.copied{
    opacity: 1;
}
span.copy-icon-wrapper{
    opacity: 1;
    z-index: 1;
    background: rgba(234, 234, 234, 0.68);
    color: #a2a2a2;
    padding: 1px 5px 1px 8px;
}
</style>
<script>
    function addClass( el, className ){
        if( !el || !className ){
            return false;
        }

        if ( el.classList && el.classList.add ){
          el.classList.add(className);
        }
        else if( el.className ){
          el.className += ' ' + className;
        }
    };
    
    function removeClass( el, className){
        if( !el || !className ){
            return false;
        }

        if ( el.classList && el.classList.remove ){
            el.classList.remove(className);
        }else if( el.className && className.split ){
            el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
        }
    };

    var iframeWrapper = document.getElementById('iframe-wrapper')
    var iframeText = document.getElementById('iframe-text')
    var copiedNotice = document.querySelector('.copy-notice')

    iframeWrapper.addEventListener("click", function() {
        iframeText.select()
        document.execCommand('copy')

        addClass( copiedNotice, 'copied' );
        setTimeout( function(){
            removeClass( copiedNotice, 'copied' );
        }, 3000 )
    },false);

</script>
        <?php

    }


    private function verify_nonce(){
        return ( isset($_POST["wps-nonce"]) && wp_verify_nonce($_POST["wps-nonce"],basename(__FILE__)) );
    }

}