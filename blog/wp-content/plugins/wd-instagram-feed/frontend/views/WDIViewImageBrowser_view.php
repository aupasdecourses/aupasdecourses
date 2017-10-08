<?php
class WDIViewImageBrowser_view{
////////////////////////////////////////////////////////////////////////////////////////
// Events                                                                             //
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
// Constants                                                                          //
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
// Variables                                                                          //
////////////////////////////////////////////////////////////////////////////////////////
	private $model;
////////////////////////////////////////////////////////////////////////////////////////
// Constructor & Destructor                                                           //
////////////////////////////////////////////////////////////////////////////////////////
	public function __construct($model) {
		$this->model = $model;
	}
////////////////////////////////////////////////////////////////////////////////////////
// Public Methods                                                                     //
////////////////////////////////////////////////////////////////////////////////////////
	public function display(){
		$this->pass_feed_data_to_js();
		$feed_row = $this->model->get_feed_row();
		$wdi_feed_counter = $this->model->wdi_feed_counter;
		$this->generate_feed_styles($feed_row);
		$style = $this->model->theme_row;

		$wdi_data_ajax = defined('DOING_AJAX') && DOING_AJAX ? 'data-wdi_ajax=1' : '';

		?>
		<div id="wdi_feed_<?php echo $wdi_feed_counter?>" class="wdi_feed_main_container wdi_layout_ib" <?php echo $wdi_data_ajax; ?> >
			<div id="wdi_spider_popup_loading_<?php echo $wdi_feed_counter?>"  class="wdi_spider_popup_loading"></div>
			<div id="wdi_spider_popup_overlay_<?php echo $wdi_feed_counter?>" class="wdi_spider_popup_overlay" onclick="wdi_spider_destroypopup(1000)"></div>
			<div class="wdi_feed_container">
				<div class="wdi_feed_info">
					<div id="wdi_feed_<?php echo $wdi_feed_counter?>_header" class='wdi_feed_header'></div>
					<div id="wdi_feed_<?php echo $wdi_feed_counter?>_users" class='wdi_feed_users'></div>
				</div>
				<?php
				if($feed_row['feed_display_view']==='pagination' && $style['pagination_position_vert']==='top'){
					?><div id="wdi_pagination" class="wdi_pagination"><div class="wdi_pagination_container"><i id="wdi_first_page" title="<?php echo __('First Page',"wd-instagram-feed")?>" class="fa fa-step-backward wdi_pagination_ctrl wdi_disabled"></i><i id="wdi_prev" title="<?php echo __('Previous Page',"wd-instagram-feed")?>" class="fa fa-arrow-left wdi_pagination_ctrl"></i><i id="wdi_current_page" class="wdi_pagination_ctrl" style="font-style:normal">1</i><i id="wdi_next" title="<?php echo __('Next Page',"wd-instagram-feed")?>" class="fa fa-arrow-right wdi_pagination_ctrl"></i> <i id="wdi_last_page" title="<?php echo __('Last Page',"wd-instagram-feed")?>" class="fa fa-step-forward wdi_pagination_ctrl wdi_disabled"></i></div></div> <?php
				}
				?>
				<div class="wdi_feed_wrapper <?php echo 'wdi_col_'.$feed_row['number_of_columns']?>" wdi-res='<?php echo 'wdi_col_'.$feed_row['number_of_columns']?>'></div>
				<div class="wdi_clear"></div>
				<?php switch($feed_row['feed_display_view']){
					case 'load_more_btn':{
						?><div class="wdi_load_more"><div class="wdi_load_more_container"><div class="wdi_load_more_wrap"><div class="wdi_load_more_wrap_inner"><div class="wdi_load_more_text"><?php echo __('Load More',"wd-instagram-feed");?></div></div></div></div></div><?php
						break;
					}
					case 'pagination':{
						if($style['pagination_position_vert']==='bottom'){
							?><div id="wdi_pagination" class="wdi_pagination"><div class="wdi_pagination_container"><i id="wdi_first_page" title="<?php echo __('First Page',"wd-instagram-feed")?>" class="fa fa-step-backward wdi_pagination_ctrl wdi_disabled"></i><i id="wdi_prev" title="<?php echo __('Previous Page',"wd-instagram-feed")?>" class="fa fa-arrow-left wdi_pagination_ctrl"></i><i id="wdi_current_page" class="wdi_pagination_ctrl" style="font-style:normal">1</i><i id="wdi_next" title="<?php echo __('Next Page',"wd-instagram-feed")?>" class="fa fa-arrow-right wdi_pagination_ctrl"></i> <i id="wdi_last_page" title="<?php echo __('Last Page',"wd-instagram-feed")?>" class="fa fa-step-forward wdi_pagination_ctrl wdi_disabled"></i></div></div> <?php
						}

						break;
					}
					case 'infinite_scroll':{
						?><div id="wdi_infinite_scroll" class="wdi_infinite_scroll"></div> <?php
					}
				}
				wdi_feed_frontend_messages();
				?>
			</div>
		</div>
		<?php

	}
	public function pass_feed_data_to_js(){
		global $wdi_options;
		$feed_row = $this->model->get_feed_row();
		$wdi_feed_counter = $this->model->wdi_feed_counter;
		$feed_row['access_token'] = $wdi_options['wdi_access_token'];
		$feed_row['wdi_feed_counter'] = $wdi_feed_counter;


		wp_localize_script("wdi_frontend", 'wdi_feed_'.$wdi_feed_counter,array('feed_row'=>$feed_row,'data'=>array(),'usersData'=>array(),'dataCount'=>0));
		wp_localize_script("wdi_frontend", 'wdi_front',array('feed_counter'=>$wdi_feed_counter));
	}
	public function generate_feed_styles($feed_row){
		$style = $this->model->theme_row;
		$colNum = (100/$feed_row['number_of_columns']);
		$wdi_feed_counter = $this->model->wdi_feed_counter;
		if($style['header_position'] == 'center'){
			$headerfloatPos = 'left';
		}else{
			$headerfloatPos = $style['header_position'];
		}
		?>
		<style type="text/css">
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_container {
				width: <?php echo $style['feed_container_width']?>;
				background-color: <?php echo $style['feed_container_bg_color']?>;/*feed_container_bg_color*/
				border-bottom: 5px solid <?php echo $style['feed_container_bg_color']?>;/*feed_container_bg_color*/;
			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_wrapper{
				width: <?php echo $style['feed_wrapper_width']?>; /*feed_wrapper_width,column number * image size*/
				background-color: <?php echo $style['feed_wrapper_bg_color']?>;/*feed_wrapper_bg_color*/
				text-align: <?php echo $style['header_position']?>;/*header_position*/
			}

			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_header{
				margin: <?php echo $style['header_margin']?>;/*header_margin*/
				padding: <?php echo $style['header_padding']?>;/*header_padding*/
				border: <?php echo $style['header_border_size']?> solid <?php echo $style['header_border_color']?>;/*header_border_size, header_border_color*/
				text-align: <?php echo $style['header_position']?>;/*header_position*/
				display: <?php echo ($feed_row['display_header']=='1')? 'block' : 'none'?>; /*if display-header is true display:block*/
			}

			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_header_img_wrap,#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_users_img_wrap{
				height: <?php echo $style['header_img_width']?>px;/*header_img_width*/
				width: <?php echo $style['header_img_width']?>px;/*header_img_width*/
				border-radius: <?php echo $style['header_border_radius']?>px;/*header_img_width*/
			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_header_text{
				font-size: <?php echo $style['header_text_font_size']?>;
				font-style: <?php echo $style['header_text_font_style']?>;
				padding: <?php echo $style['header_text_padding']?>;/*header_text_padding*/
				color: <?php echo $style['header_text_color']?>;/*header_text_color*/
				font-weight: <?php echo $style['header_font_weight']?>;/*header_font_weight*/
				line-height: <?php echo $style['header_img_width']?>px;/*header_img_width*/
			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_single_user{
				padding-top: <?php echo $style['user_padding']?>;/*user_padding*/
				padding-bottom: <?php echo $style['user_padding']?>;/*user_padding*/
				padding-left: <?php echo $style['user_padding']?>;/*user_padding*/
				padding-right: <?php echo $style['user_padding']?>;/*user_padding*/
			}
			<?php
    
      if($feed_row['display_user_post_follow_number'] == '1'){
        $header_text_padding =(intval($style['user_img_width']) - intval($style['users_text_font_size']))/4;
      }else{
        $header_text_padding =(intval($style['user_img_width']) - intval($style['users_text_font_size']))/2;
      }	
      ?>
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_header_user_text {
				padding-top: <?php echo $header_text_padding; ?>px;

			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_header_user_text h3 {
				margin-top: <?php echo $header_text_padding ?>px;
			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_header_hashtag h3{
				margin-top: <?php echo (intval($style['user_img_width']) - intval($style['users_text_font_size']))/2?>px;
			}


			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_header_user_text h3{
				font-size: <?php echo $style['users_text_font_size']?>;
				font-style: <?php echo $style['users_text_font_style']?>;
				line-height: <?php echo $style['users_text_font_size']?>;
				color: <?php echo $style['users_text_color']?>;/*header_text_color*/;
			}

			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_user_img_wrap img{
				height: <?php echo $style['user_img_width']?>px;
				width: <?php echo $style['user_img_width']?>px;
				border-radius: <?php echo $style['user_border_radius']?>px;
			}



			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_media_info{
				/*display: inline-block;*/
				margin-left: <?php echo intval($style['user_img_width']) + 10;?>px;
				line-height: <?php echo $style['users_text_font_size']?>;
				color: <?php echo $style['users_text_color']?>;/*header_text_color !mmm/ seperate*/
				display: <?php echo ($feed_row['display_user_post_follow_number'] == '1') ? 'block' : 'none'; ?>
			}

			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_bio{
				color: <?php echo $style['users_text_color']?>;/*header_text_color*/
				font-size: <?php echo $style['user_description_font_size']?>;/*header_text_color*/
			}


			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_follow_btn{
				border-radius: <?php echo $style['follow_btn_border_radius']?>px;
				font-size: <?php echo $style['follow_btn_font_size']?>px;
				background-color: <?php echo $style['follow_btn_bg_color']?>;
				border-color: <?php echo $style['follow_btn_border_color']?>;
				color: <?php echo $style['follow_btn_text_color']?>;
				margin-left: <?php echo $style['follow_btn_margin']?>px;
				padding: 0 <?php echo $style['follow_btn_padding']?>px;
			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_follow_btn:hover{
				border-color: <?php echo $style['follow_btn_border_hover_color']?>;
				color: <?php echo $style['follow_btn_text_hover_color']?>;
				background-color: <?php echo $style['follow_btn_background_hover_color']?>;
			}

			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_filter_overlay{
				width: <?php echo $style['user_img_width'];?>px;/*user_img_width*/
				height: <?php echo $style['user_img_width'];?>px;/*user_img_width*/
				border-radius: <?php echo $style['user_border_radius']?>px;/*user_img_width*/
				background-color: <?php echo $style['th_overlay_hover_color'];?>;
			}

			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_filter_icon span{
				width: <?php echo $style['user_img_width'];?>px;/*header_img_width*/
				height: <?php echo $style['user_img_width'];?>px;/*header_img_width*/
			}

			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_photo_wrap {
				padding: <?php echo $style['image_browser_photo_wrap_padding']?>; /*photo_wrap_padding*/

			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_photo_wrap_inner{
				border: <?php echo $style['image_browser_photo_wrap_border_size']?> solid <?php echo $style['image_browser_photo_wrap_border_color']?>;/*photo_wrap_border_size,photo_wrap_border_color*/
				background-color: <?php echo $style['image_browser_photo_wrap_bg_color']?>;/*photo_wrap_bg_color*/
			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_photo_img{
				border-radius: <?php echo $style['image_browser_photo_img_border_radius']?>;/*photo_img_border_radius*/
			}

			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_item{
				width: <?php echo $colNum.'%'?>;/*thumbnail_size*/
			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_photo_meta {
				background-color: <?php echo $style['image_browser_photo_meta_bg_color']?>;/*photo_meta_bg_color*/
			}

			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_thumb_likes{
				width: <?php echo ($style['image_browser_photo_meta_one_line']=='1')? '50%' : '100%' ?>;/*photo_meta_one_line==false else 100%*/
				float: <?php echo ($style['image_browser_photo_meta_one_line']=='1')? 'left' : 'none'?>;/*photo_meta_one_line==true else float none*/
				font-size: <?php echo $style['image_browser_like_comm_font_size']?>;/*photo_caption_font_size*/;
				color: <?php echo $style['image_browser_like_text_color']?>;/*like_text_color*/

			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_thumb_comments{
				width: <?php echo ($style['image_browser_photo_meta_one_line']=='1')? '50%' : '100%' ?>;/*photo_meta_one_line==false else 100%*/
				float: <?php echo ($style['image_browser_photo_meta_one_line']=='1')? 'left' : 'none'?>;/*photo_meta_one_line==true else float none*/
				font-size: <?php echo $style['image_browser_like_comm_font_size']?>;/*photo_caption_font_size*/;
				color: <?php echo $style['image_browser_comment_text_color']?>;/*comment_text_color*/

			}

			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_photo_title {
				font-size: <?php echo $style['image_browser_photo_caption_font_size']?>;/*photo_caption_font_size*/
				color: <?php echo $style['image_browser_photo_caption_color']?>;/*photo_caption_color*/
			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_photo_title:hover{
				color: <?php echo $style['image_browser_photo_caption_hover_color']?>;
			}

			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_load_more{
				background-color: <?php echo $style['feed_container_bg_color']?>;/*feed_container_bg_color*/
				text-align: <?php echo $style['load_more_position']?>;/*load_more_position*/
			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_load_more_wrap{
				padding: <?php echo $style['load_more_padding']?>;/*load_more_padding*/
				background-color: <?php echo $style['load_more_bg_color']?>;/*load_more_bg_color*/
				border-radius: <?php echo $style['load_more_border_radius']?>;/*load_more_border_radius*/
				height: <?php echo $style['load_more_height']?>;/*load_more_height*/
				width: <?php echo $style['load_more_width']?>;/*load_more_width*/
				border: <?php echo $style['load_more_border_size']?> solid <?php echo $style['load_more_border_color']?>;/*load_more_border_size, load_more_border_color*/;
			}

			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_load_more_text{
				color: <?php echo $style['load_more_text_color']?>;/*load_more_text_color*/
				font-size: <?php echo $style['load_more_text_font_size']?>;/*load_more_text_font_size*/
			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_load_more_text img{
				height: <?php echo $style['load_more_height']?>;/*load_more_height*/
				width:  <?php echo $style['load_more_height']?>;/*load_more_height*/
			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_load_more_wrap:hover{
				background-color: <?php echo $style['load_more_wrap_hover_color']?>;/*load_more_wrap_hover_color*/
			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_pagination{
				text-align: <?php echo $style['pagination_position']?>;/*load_more_position*/
				color: <?php echo $style['pagination_ctrl_color']?>;/*pagination_ctrl_color*/
				font-size: <?php echo $style['pagination_size']?>;/*pagination_size*/
			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_pagination_ctrl{
				margin: <?php echo $style['pagination_ctrl_margin']?>;
			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_pagination_ctrl:hover{
				color: <?php echo $style['pagination_ctrl_hover_color']?>;
			}

			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_filter_active_bg{
				background-color: <?php echo $style['active_filter_bg_color'];?>;
			}
			#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_filter_active_col{
				color: <?php echo $style['active_filter_bg_color'];?>;
				border-color: <?php echo $style['active_filter_bg_color'];?>;
			}
			<?php if($feed_row['disable_mobile_layout']=="0"){
        ?>
			@media screen and (min-width: 800px) and (max-width:1024px){
				#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_item{
					width: <?php echo ($colNum<33.33) ? '33.333333333333%' : $colNum.'%'?>;/*thumbnail_size*/
					margin: 0;
					display: inline-block;
					vertical-align: top;
					overflow: hidden;
				}
				#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_container {
					width: 100%;
					margin: 0 auto;
					background-color: <?php echo $style['feed_container_bg_color']?>;/*feed_container_bg_color*/
				}

			}
			@media screen and (min-width: 480px) and (max-width:800px){
				#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_item{
					width: <?php echo ($colNum<50) ? '50%' : $colNum.'%'?>;/*thumbnail_size*/
					margin: 0;
					display: inline-block;
					overflow: hidden;
				}
				#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_container {
					width: 100%;
					margin: 0 auto;
					background-color: <?php echo $style['feed_container_bg_color']?>;/*feed_container_bg_color*/
				}
			}
			@media screen and (max-width: 480px){
				#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_item{
					width: <?php echo ($colNum<100) ? '100%' : $colNum.'%'?>;/*thumbnail_size*/
					margin: 0;
					display: inline-block;
					overflow: hidden;
				}
				#wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_container {
					width: 100%;
					margin: 0 auto;
					background-color: <?php echo $style['feed_container_bg_color']?>;/*feed_container_bg_color*/
				}
			}
			<?php
        }?>
		</style>
		<?php
	}






}
?>