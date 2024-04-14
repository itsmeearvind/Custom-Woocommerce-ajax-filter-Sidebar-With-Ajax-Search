<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

?>
<header class="woocommerce-products-header">
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
	<?php endif; ?>

	<?php
	/**
	 * Hook: woocommerce_archive_description.
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description - 10
	 */
	do_action( 'woocommerce_archive_description' );
	?>
</header>

<section class="filter">
	<div class="fl-row-content fl-row-fixed-width fl-node-content">
		<div class="main_filter">
			<div class="row">
				<div class="col-lg-3 col-md-3 col-sm-12">
					<div class="filter_aree">
						<div class="filter_price">
							<div class="two_sidebar">
								<h3>FILTER BY PRICE</h3>
								<fieldset class="filter-price">
									<div class="price-field">
										<input type="range" min="0" max="100" value="0" id="lower">
										<input type="range" min="0" max="1000" value="1000" id="upper">
										<?php
										// Set default min and max price dynamically
										$args = array(
											'post_type'      => 'product',
											'posts_per_page' => -1,
											'meta_query'     => array(
												array(
													'key'     => '_price',
													'value'   => '',
													'compare' => '!=',
												),
											),
											'orderby'        => 'meta_value_num',
											'order'          => 'ASC',
										);

										$products = new WP_Query($args);

										$min_price = 0;
										$max_price = 0;

										if ($products->have_posts()) {
											while ($products->have_posts()) {
												$products->the_post();
												$price = get_post_meta(get_the_ID(), '_price', true);
												if ($price != '') {
													$min_price = ($min_price == 0 || $price < $min_price) ? $price : $min_price;
													$max_price = ($price > $max_price) ? $price : $max_price;
												}
											}
										}

										wp_reset_postdata();
										?>
										<input type="hidden" id="min_price" value="<?php echo $min_price; ?>">
										<input type="hidden" id="max_price" value="<?php echo $max_price; ?>">
									</div>

									<div class="filter_pD">
										<button class="filter_BT">FILTER</button>
										<div class="price-wrap">
											<span class="price-title">Price : </span>
											<div class="price-wrap-1">
												<input id="one" readonly>
												<label for="one">$</label>
											</div>
											<div class="price-wrap_line">-</div>
											<div class="price-wrap-2">
												<input id="two" readonly>
												<label for="two">$</label>
											</div>
										</div>

									</div>
								</fieldset>
							</div>
						</div>

						<div class="filter_con">
							<div class="filter_side_are">
								<h3>PRODUCT CATEGORIES</h3>
							</div>

							<div class="product__categories">
								<div class="buttonsop">
									<?php
									$orderBy = 'name';
									$orders = 'asc';
									$hide_empty = false;
									$args = array(
										'orderby' => $orderBy,
										'order'   => $orders,
										'hide_empty' => $hide_empty,
										'parent' => 0,
									);
									// Get parent categories
									$product_cat = get_terms('product_cat', $args);
									if (!empty($product_cat)) {
										// Loop through parent categories
										foreach ($product_cat as $parent_category) {
											$parent_cat_name = $parent_category->name;
											$parent_cat_count = $parent_category->count;
											$parent_cat_id = $parent_category->term_id;

											// Get subcategories of the current parent category
											$sub_categories = get_terms(array(
												'taxonomy'    => 'product_cat',
												'orderby' => 'name',
												'hide_empty' => $hide_empty,
												'parent' => $parent_cat_id
											));

											// Check if subcategories exist
											$has_subcategories = !empty($sub_categories);
									?>
											<div class="buttonFolp">
												<a class="showSingle" target="<?php echo $parent_cat_id; ?>" data-target="<?php echo $parent_category->slug; ?>">
													<strong><?php echo $parent_cat_name; ?> <span>(<?php echo $parent_cat_count; ?>)</span></strong>
												</a>
												<?php if ($has_subcategories) { ?>
													<div id="div<?php echo $parent_cat_id; ?>" class="targetDiv">
														<ul class="list_Pri">
															<?php
															// Loop through subcategories
															foreach ($sub_categories as $sub_category) {
																$sub_cat_name = $sub_category->name;
															?>
																<li><a class="fitersubcategories" data-target="<?php echo $sub_category->term_id; ?>"><?php echo $sub_cat_name; ?></a></li>
															<?php } ?>
														</ul>
													</div>
												<?php } ?>
											</div>
									<?php
										}
									}
									?>
								</div>
							</div>
							<div class="age_part">
								<div class="filter_side_are">
									<h3>BY AGE</h3>
									<ul class="list_Pri">
										<?php
										$terms = get_terms('product_tag');
										$term_array = array();
										if (!empty($terms) && !is_wp_error($terms)) {
											foreach ($terms as $term) {
												$term_array[] = $term->name;
										?>
												<li><a class="fitertag" data-tag="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></a></li>
										<?php }
										} ?>
									</ul>
								</div>
							</div>
						</div>

					</div>
				</div>
				<div class="col-lg-9 col-md-9 col-sm-12">
					<div class="filter_contant_box">
						<div class="row">
							<?php
							if (woocommerce_product_loop()) {

								/**
								 * Hook: woocommerce_before_shop_loop.
								 *
								 * @hooked woocommerce_output_all_notices - 10
								 * @hooked woocommerce_result_count - 20
								 * @hooked woocommerce_catalog_ordering - 30
								 */
								do_action('woocommerce_before_shop_loop');

								woocommerce_product_loop_start();

								if (wc_get_loop_prop('total')) {
									while (have_posts()) {
										the_post();

										/**
										 * Hook: woocommerce_shop_loop.
										 */
										do_action('woocommerce_shop_loop');

										wc_get_template_part('content', 'product');
									}
								}

								woocommerce_product_loop_end();

								/**
								 * Hook: woocommerce_after_shop_loop.
								 *
								 * @hooked woocommerce_pagination - 10
								 */
								do_action('woocommerce_after_shop_loop');
							} else {
								/**
								 * Hook: woocommerce_no_products_found.
								 *
								 * @hooked wc_no_products_found - 10
								 */
								do_action('woocommerce_no_products_found');
							}

							/**
							 * Hook: woocommerce_after_main_content.
							 *
							 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
							 */
							do_action('woocommerce_after_main_content');
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<?php
/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
// do_action('woocommerce_sidebar');


get_footer('shop');
