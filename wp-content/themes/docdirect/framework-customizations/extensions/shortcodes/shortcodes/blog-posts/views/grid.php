<?php if (!defined('FW')) die( 'Forbidden' );
/**
 * @var $atts
 */
?>
<div class="sc-blogs">
	<div class="tg-view tg-blog-grid">
		<div class="row">
			<?php
			global $paged;
			if (empty($paged)) $paged = 1;
			
			$blog_view	= $atts['blog_view'];
			// Count Total Pssts
	
			$tax_query['cat']	= '';
			$cat_sepration = $atts['posts_category'];
			
			if( isset( $cat_sepration ) && !empty(  $cat_sepration) ) {
				$tax_query['cat']	= implode(',',$cat_sepration);
			}
			
			$show_posts    = $atts['show_posts'] ? $atts['show_posts'] : '-1';        
			$args = array('posts_per_page' => "-1", 'post_type' => 'post', 'order' => 'DESC', 'orderby' => 'ID', 'post_status' => 'publish', 'ignore_sticky_posts' => 1);
			
			if( isset( $cat_sepration ) && !empty( $cat_sepration )) {
				$args	= array_merge($args,$tax_query);
			}
			
			$query 		= new WP_Query( $args );
			$count_post = $query->post_count;        
			
			//Main Query	
			$args 		= array('posts_per_page' => $show_posts, 'post_type' => 'post', 'paged' => $paged, 'order' => 'DESC', 'orderby' => 'ID', 'post_status' => 'publish', 'ignore_sticky_posts' => 1);
			
			if( isset( $cat_sepration ) && !empty( $cat_sepration )) {
				$args	= array_merge($args,$tax_query);
			}
			
			$query 		= new WP_Query($args);
			while($query->have_posts()) : $query->the_post();
				global $post;
				$width  = '375';
				$height = '305';
				$thumbnail	= docdirect_prepare_thumbnail($post->ID ,$width,$height);
			
				if( empty( $thumbnail ) ){
					$thumbnail	= get_template_directory_uri().'/images/grid-placeholder.png';
				}
			?>
			<div class="col-md-4 col-sm-6 col-xs-6">
				<article class="tg-post">
					<div class="tg-box">
						<figure class="tg-feature-img">
							<a href="<?php echo esc_url( get_the_permalink() ); ?>"><img width="470" height="300" src="<?php echo esc_url($thumbnail);?>" alt="<?php echo esc_attr( get_the_title() ); ?>"></a>
							<ul class="tg-metadata">
								<li><i class="fa fa-clock-o"></i><time datetime="<?php echo date_i18n('Y-m-d', strtotime(get_the_date('Y-m-d',$post->ID))); ?>"><?php echo date_i18n('d M, Y', strtotime(get_the_date('Y-m-d',$post->ID))); ?></time> </li>
								<li><i class="fa fa-comment-o"></i><a href="<?php echo esc_url( comments_link());?>"><?php comments_number( ' 0 Comments', ' 1 Comment', ' % Comments' ); ?></a></li>
							</ul>
						</figure>
						<div class="tg-contentbox">
							<div class="tg-displaytable">
								<div class="tg-displaytablecell">
									<div class="tg-heading-border tg-small">
										<h3><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php the_title(); ?> </a></h3>
									</div>
									<?php if( isset( $atts['show_description'] ) && $atts['show_description'] === 'show' ){?>
										<div class="tg-description">
											<?php docdirect_prepare_excerpt($atts['excerpt_length'],'false',''); ?>
										</div>
									<?php }?>
								</div>
								<a href="<?php echo esc_url( get_the_permalink() ); ?>"><span class="tg-show"><em class="icon-add"></em></span></a>
							</div>
						</div>
						<?php
							  if (is_sticky()) :
							   echo '<div class="sticky-post-wrap">
										  <div class="sticky-txt">
										   <em class="tg-featuretext">'.esc_html__('Featured','docdirect').'</em>
										   <i class="fa fa-bolt"></i>
										  </div>
									 </div>';
							  endif;
							?>
					</div>
				</article>
			</div>
			<?php endwhile; wp_reset_postdata(); ?>
        </div>
	</div>
	<?php if(isset($atts['show_pagination']) && $atts['show_pagination'] == 'yes') : ?>
		<?php docdirect_prepare_pagination($count_post,$atts['show_posts']);?>
	<?php endif; ?>
</div>
