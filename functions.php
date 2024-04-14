<?php

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
    $parenthandle = 'parent-style'; 
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', 
        array(),  
        $theme->parent()->get('Version')
    );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(),
        array( $parenthandle ),
        $theme->get('Version')
    );
};

unction child_theme_enqueue_styles()
{
    // Owl Carousel CSS
    wp_enqueue_style('owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css', array(), null);

    // Bootstrap CSS
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), null);

    // Font Awesome CSS
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css', array(), null);
}
add_action('wp_enqueue_scripts', 'child_theme_enqueue_styles');

// Enqueue scripts in footer
function child_theme_enqueue_scripts()
{
    // jQuery
    wp_enqueue_script('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js', array(), null, true);

    // Bootstrap JavaScript
    wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);

    // Owl Carousel JavaScript
    wp_enqueue_script('owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', array('jquery'), null, true);

    // Custom JavaScript
    wp_enqueue_script('custom-script', get_stylesheet_directory_uri() . '/custom.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'child_theme_enqueue_scripts');


//AJAX handler for Price filtering products
add_action('wp_ajax_filter_products', 'filter_products');
add_action('wp_ajax_nopriv_filter_products', 'filter_products');
function filter_products()
{
    $min_price = isset($_POST['min_price']) ? $_POST['min_price'] : 0;
    $max_price = isset($_POST['max_price']) ? $_POST['max_price'] : 0;

    // Modify the product query to filter products by price
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 9,
        'meta_query' => array(
            array(
                'key' => '_price',
                'value' => array($min_price, $max_price),
                'type' => 'numeric',
                'compare' => 'BETWEEN'
            )
        )
    );

    $products = new WP_Query($args);

    // Output product listings
    if ($products->have_posts()) :
        while ($products->have_posts()) : $products->the_post();
            wc_get_template_part('content', 'product');
        endwhile;
    else :
        do_action('woocommerce_no_products_found');
    endif;

    wp_die();
}


//AJAX handler for Categories filtering products
add_action('wp_ajax_filter_products_categories', 'filter_products_categories');
add_action('wp_ajax_nopriv_filter_products_categories', 'filter_products_categories');
function filter_products_categories()
{
    // Get category and subcategory from the AJAX request
    $category = isset($_POST['category']) ? intval($_POST['category']) : 0;
    // Modify the product query to filter products by category and subcategory
    $tax_query = array();

    if ($category > 0) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $category,
        );
    }
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 12,
        'tax_query'      => $tax_query,
    );
    $products = new WP_Query($args);
    // Output product listings
    if ($products->have_posts()) :
        while ($products->have_posts()) : $products->the_post();
            wc_get_template_part('content', 'product');
        endwhile;
    else :
        do_action('woocommerce_no_products_found');
    endif;

    // Always remember to exit after processing AJAX calls
    wp_die();
}

//AJAX handler for Categories filtering products
add_action('wp_ajax_filter_products_subcategories', 'filter_products_subcategories');
add_action('wp_ajax_nopriv_filter_products_subcategories', 'filter_products_subcategories');
function filter_products_subcategories()
{
    // Get subcategory from the AJAX request
    $subcategory = isset($_POST['subcategory']) ? intval($_POST['subcategory']) : 0;
    // Modify the product query to filter products by subcategory
    $tax_query = array();
    if ($subcategory > 0) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $subcategory,
        );
    }

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 12,
        'tax_query'      => $tax_query,
    );

    $products = new WP_Query($args);

    // Output product listings
    if ($products->have_posts()) :
        while ($products->have_posts()) : $products->the_post();
            wc_get_template_part('content', 'product');
        endwhile;
    else :
        do_action('woocommerce_no_products_found');
    endif;

    // Always remember to exit after processing AJAX calls
    wp_die();
}


// AJAX handler for filtering products by tags
add_action('wp_ajax_filter_products_tags', 'filter_products_tags');
add_action('wp_ajax_nopriv_filter_products_tags', 'filter_products_tags');

function filter_products_tags()
{
    $tag = isset($_POST['tag']) ? intval($_POST['tag']) : 0;

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 12,
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_tag',
                'field'    => 'term_id',
                'terms'    => $tag,
            ),
        ),
    );

    $products = new WP_Query($args);

    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            wc_get_template_part('content', 'product');
        }
    } else {
        do_action('woocommerce_no_products_found');
    }

    wp_die();
}

function add_script_footer()
{
?>
    <script>
        jQuery(function() {
            // Initially hide all targetDivs
            jQuery(".targetDiv").hide();

            // Event handler for clicking links with class "showSingle"
            jQuery(".showSingle").on("click", function() {
                var target = jQuery(this).attr("target");
                var targetDiv = jQuery("#div" + target);

                if (targetDiv.is(":visible")) {
                    targetDiv.hide();
                } else {
                    jQuery(".targetDiv").hide();
                    targetDiv.show();
                }

                var category = jQuery(this).attr("target");


                // Send AJAX request with category and subcategory data
                jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: "POST",
                    data: {
                        action: "filter_products_categories",
                        category: category,
                    },
                    success: function(response) {
                        jQuery(".products.columns-4").html(response);
                    },
                });
            });

            jQuery(".fitersubcategories").on("click", function() {
                var subcategory = jQuery(this).attr('data-target');
                // Send AJAX request with category and subcategory data
                jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: "POST",
                    data: {
                        action: "filter_products_subcategories",
                        subcategory: subcategory
                    },
                    success: function(response) {
                        jQuery(".products.columns-4").html(response);
                    },
                });
            });
            var lowerSlider = document.querySelector("#lower");
            var upperSlider = document.querySelector("#upper");

            document.querySelector("#two").value = upperSlider.value;
            document.querySelector("#one").value = lowerSlider.value;

            var lowerVal = parseInt(lowerSlider.value);
            var upperVal = parseInt(upperSlider.value);

            upperSlider.oninput = function() {
                lowerVal = parseInt(lowerSlider.value);
                upperVal = parseInt(upperSlider.value);

                if (upperVal < lowerVal + 4) {
                    lowerSlider.value = upperVal - 4;
                    if (lowerVal == lowerSlider.min) {
                        upperSlider.value = 4;
                    }
                }
                document.querySelector("#two").value = this.value;
            };

            lowerSlider.oninput = function() {
                lowerVal = parseInt(lowerSlider.value);
                upperVal = parseInt(upperSlider.value);
                if (lowerVal > upperVal - 4) {
                    upperSlider.value = lowerVal + 4;
                    if (upperVal == upperSlider.max) {
                        lowerSlider.value = parseInt(upperSlider.max) - 4;
                    }
                }
                document.querySelector("#one").value = this.value;
            };



            // Function to update the price range values
            function updatePriceRange() {
                var lowerVal = jQuery("#lower").val();
                var upperVal = jQuery("#upper").val();

                jQuery("#one").val(lowerVal);
                jQuery("#two").val(upperVal);
            }

            // Event listener for price range input change
            jQuery('.filter-price input[type="range"]').on("input", function() {
                updatePriceRange();
            });

            // Function to handle AJAX filtering
            function ajaxFilterProducts() {
                var lowerVal = jQuery("#lower").val();
                var upperVal = jQuery("#upper").val();

                // AJAX request to filter products
                jQuery.ajax({
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    type: "POST",
                    data: {
                        action: "filter_products",
                        min_price: lowerVal,
                        max_price: upperVal,
                    },
                    success: function(response) {
                        jQuery(".products.columns-4").html(response);
                    },
                });
            }

            // Event listener for filter button click
            jQuery(".filter_BT").on("click", function() {
                var minPrice = $("#min_price").val();
                var maxPrice = $("#max_price").val();
                ajaxFilterProducts("filter_products", {
                    min_price: minPrice,
                    max_price: maxPrice,
                });
            });

            jQuery(".owl-carousel").owlCarousel({
                loop: true,
                margin: 10,
                nav: true,
                dots: false,
                responsive: {
                    0: {
                        items: 1,
                    },
                    600: {
                        items: 3,
                    },
                    1000: {
                        items: 6,
                    },
                },
            });

            // Function to handle AJAX filtering
            function ajaxFilterProducts(action, data) {
                jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: "POST",
                    data: {
                        action: action,
                        ...data,
                    },
                    success: function(response) {
                        jQuery(".products.columns-4").html(response);
                    },
                });
            }


            // Event listener for clicking product tags
            jQuery(".fitertag").on("click", function(e) {
                // e.preventDefault();
                var tag = jQuery(this).attr("data-tag");
                ajaxFilterProducts("filter_products_tags", {
                    tag: tag
                });
            });

        });
    </script>
<?php
}
add_action('wp_footer', 'add_script_footer');
