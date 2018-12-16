<?php if ( ! function_exists( 'ghostpool_share_icons' ) ) {
	function ghostpool_share_icons() {
	
		$output = '<div id="gp-share-icons-wrapper">

			<div class="gp-divider-title-bg">
				<div class="gp-divider-title">' . esc_html__( 'Share This Post', 'aardvark-plugin' ) . '</div>
			</div>

			<div id="gp-share-icons">

				<a href="https://twitter.com/share?text=' . esc_attr( get_the_title( get_the_ID() ) ) . '&url=' . rawurlencode( get_permalink( get_the_ID() ) ) . '" title="' . esc_attr__( 'Twitter', 'aardvark-plugin' ) . '" class="gp-twitter-icon" onclick="window.open(this.href, \'gpwindow\', \'left=50,top=50,width=600,height=350,toolbar=0\'); return false;"></a>	

				<a href="https://www.facebook.com/sharer.php?u=' . rawurlencode( get_permalink( get_the_ID() ) ) . '&t=' . esc_attr( get_the_title( get_the_ID() ) ) . '" title="' . esc_attr__( 'Facebook', 'aardvark-plugin' ) . '" class="gp-facebook-icon" onclick="window.open(this.href, \'gpwindow\', \'left=50,top=50,width=600,height=350,toolbar=0\'); return false;"></a>

				<a href="https://plusone.google.com/_/+1/confirm?hl=en-US&url=' . rawurlencode( get_permalink( get_the_ID() ) ) . '" title="' . esc_attr__( 'Google+', 'aardvark-plugin' ) . '" class="gp-google-plus-icon" onclick="window.open(this.href, \'gpwindow\', \'left=50,top=50,width=600,height=350,toolbar=0\'); return false;"></a>

				<a href="javascript:void((function()%7Bvar%20e=document.createElement(\'script\');e.setAttribute(\'type\',\'text/javascript\');e.setAttribute(\'charset\',\'UTF-8\');e.setAttribute(\'src\',\'https://assets.pinterest.com/js/pinmarklet.js?r=\'+Math.random()*99999999);document.body.appendChild(e)%7D)());" count-layout="vertical" title="' . esc_attr__( 'Pinterest', 'aardvark-plugin' ) . '" class="gp-pinterest-icon" target="_blank"></a>

				<a href="https://www.linkedin.com/shareArticle?mini=true&url=' . rawurlencode( get_permalink( get_the_ID() ) ) . '&title=' . esc_attr( get_the_title( get_the_ID() ) ) . '" title="' . esc_attr__( 'LinkedIn', 'aardvark-plugin' ) . '" class="gp-linkedin-icon" onclick="window.open(this.href, \'gpwindow\', \'left=50,top=50,width=600,height=350,toolbar=0\'); return false;"></a>

				<a href="https://reddit.com/submit?url=' . rawurlencode( get_permalink( get_the_ID() ) ) . '&amp;title=' . esc_attr( get_the_title( get_the_ID() ) ) . '" title="' . esc_attr__( 'Reddit', 'aardvark-plugin' ) . '" class="gp-reddit-icon" onclick="window.open(this.href, \'gpwindow\', \'left=50,top=50,width=600,height=350,toolbar=0\'); return false;"></a>

				<a href="https://www.tumblr.com/share/link?url=' . rawurlencode( get_permalink( get_the_ID() ) ) . '&amp;title=' . esc_attr( get_the_title( get_the_ID() ) ) . '" title="' . esc_attr__( 'Tumblr', 'aardvark-plugin' ) . '" class="gp-tumblr-icon" onclick="window.open(this.href, \'gpwindow\', \'left=50,top=50,width=600,height=350,toolbar=0\'); return false;"></a>

				<a href="mailto:?subject=' . esc_attr( get_the_title( get_the_ID() ) ) . '&body=' . rawurlencode( get_permalink( get_the_ID() ) ) . '" title="' . esc_attr__( 'Email', 'aardvark-plugin' ) . '" class="gp-email-icon" onclick="window.open(this.href, \'gpwindow\', \'left=50,top=50,width=600,height=350,toolbar=0\'); return false;"></a>
			
			</div>

		</div>';
		
		return apply_filters( 'ghostpool_share_icons', $output );
		
	}	
} ?>