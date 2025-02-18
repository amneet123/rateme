<?php if (!defined('FW')) die( 'Forbidden' );
/**
 * @var $atts
 */

?>
<div class="sc-review">
	<div class="tg-patientfeedbacks tg-haslayout">
    	<?php if( !empty( $atts['title'] ) || !empty( $atts['description'] ) ){?>
            <div class="col-sm-10 col-sm-offset-1 col-xs-12">
                <div class="tg-theme-heading">
                    <?php if( !empty( $atts['title'] ) ){?>
                        <h2><?php echo esc_attr( $atts['title'] );?></h2>
                        <span class="tg-roundbox"></span>
					<?php }?> 
                    <?php if( !empty( $atts['description'] ) ){?>
                    <div class="tg-description">
                        <p><?php echo esc_attr( $atts['description'] );?></p>
                    </div>
                    <?php }?> 
                </div>
            </div>
        <?php }?>
		<?php
		global $paged;
		if (empty($paged)) $paged = 1;
		
		$show_posts    = isset( $atts['show_posts'] ) && !empty( $atts['show_posts'] ) ? $atts['show_posts'] : '-1';        
		$meta_query_args = array();
		//Directory Posts
		
		
		if( isset( $atts['directory_type'] ) && !empty( $atts['directory_type'] ) ){
			foreach( $atts['directory_type'] as $value ){
				$meta_query_args[] = array(
										'key'     => 'directory_type',
										'value'   => $value,
										'compare' => '='
									);
			}
		}
		
		
		
		$query_args = array('posts_per_page' => "-1", 
			'post_type' => 'docdirectreviews', 
			'order' => 'DESC', 
			'orderby' => 'ID', 
			'post_status' => 'publish', 
			'ignore_sticky_posts' => 1
		);
		
		//Merge Query
		if( !empty( $meta_query_args ) ) {
			$query_relation = array('relation' => 'OR',);
			$meta_query_args	= array_merge( $query_relation,$meta_query_args );
			$query_args['meta_query'] = $meta_query_args;
		}

		$query 		= new WP_Query( $query_args );
		$count_post = $query->post_count;        
		
		//Main Query	
		$query_args 		= array('posts_per_page' => $show_posts, 
			'post_type' => 'docdirectreviews', 
			'paged' => $paged, 
			'order' => 'DESC', 
			'orderby' => 'ID', 
			'post_status' => 'publish', 
			'ignore_sticky_posts' => 1
		);
		
		//Merge Query
		if( !empty( $meta_query_args ) ) {
			$query_relation = array('relation' => 'OR',);
			$meta_query_args	= array_merge( $query_relation,$meta_query_args );
			$query_args['meta_query'] = $meta_query_args;
		}
		
		
		$query 		= new WP_Query($query_args);
		if( $query->have_posts() ){
			while($query->have_posts()) : $query->the_post();
				global $post;
				$user_rating = fw_get_db_post_option($post->ID, 'user_rating', true);
				$user_to = fw_get_db_post_option($post->ID, 'user_to', true);
				$user_from = fw_get_db_post_option($post->ID, 'user_from', true);
				$review_date = fw_get_db_post_option($post->ID, 'review_date', true);
				$user_data 	  = get_user_by( 'id', intval( $user_from ) );
				$user_to_data 	  = get_user_by( 'id', intval( $user_to ) );
				
				$avatar = apply_filters(
								'docdirect_get_user_avatar_filter',
								 docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user_from),
								 array('width'=>150,'height'=>150) //size width,height
							);

				
				if( !empty( $user_data ) && !empty( $user_to_data ) ){
				
					$user_name	= $user_data->first_name.' '.$user_data->last_name;
					if( empty( $user_name ) ){
						$user_name	= $user_data->user_login;
					}
					
					$percentage	= $user_rating*20;
					
					if( empty( $user_to_data->last_name ) && $user_to_data->last_name ){
						$user_name_to	= $user_to_data->first_name.' '.$user_to_data->last_name;
					} else{
						$user_name_to	= $user_to_data->display_name;
					}
				?>
        		<div class="col-md-6 col-sm-6 col-xs-6 tg-feedbackwidht">
                    <div class="tg-patientfeedback">
                        <figure class="tg-patient-pic">
                            <!--<a href="<?php //echo get_author_posts_url($user_from); ?>"><img src="<?php //echo esc_url( $avatar );?>" alt="<?php //esc_html_e('Reviewer','docdirect');?>"></a>-->
                            <img src="<?php echo esc_url( $avatar );?>" alt="<?php esc_html_e('Reviewer','docdirect');?>">
                        </figure>
                        <div class="tg-patient-message">
                          <!--  <span class="tg-patient-name"><a href="<?php //echo get_author_posts_url($user_from); ?>"><?php //echo esc_attr( $user_name );?></a></span>-->
                             <span class="tg-patient-name"><?php echo esc_attr( $user_name );?></span>
                            <span class="tg-doctor-name"><?php echo esc_html_e('For ','docdirect');?><a href="<?php echo get_author_posts_url($user_to); ?>"><?php echo esc_attr( $user_name_to );?></a></span>
                            <div class="tg-stars star-rating">
                                <span style="width:<?php echo esc_attr( $percentage );?>%"></span>
                            </div>
                            <div class="tg-description">
                                <p><?php docdirect_prepare_excerpt($atts['excerpt_length'],'false',''); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
				<?php 
				}
			
			endwhile; wp_reset_postdata();
		} else{
			 DoctorDirectory_NotificationsHelper::informations(esc_html__('No Reviews Found.','docdirect'));
		}?>
	</div>
	<?php if(isset($atts['show_pagination']) && $atts['show_pagination'] == 'yes') : ?>
        <?php docdirect_prepare_pagination($count_post,$atts['show_posts']);?>
    <?php endif; ?>
</div>
