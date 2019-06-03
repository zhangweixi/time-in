<script>
	
	import common from "common/common.js";
	
	export default {
		onLaunch: function() {
			console.log('App Launch')
			//return ;
			//check this weixin mini app whether login
			// #ifdef MP-WEIXIN
			
			if(!common.user.checkLogin()){

				uni.login({
					provider:"weixin",
					success:(res)=>{
						
						common.user.code = res.code;
						common.user.login(this);
					}
				})
			}
			// #endif
		},
		onShow: function() {
			console.log('App Show')
		},
		onHide: function() {
			console.log('App Hide')
		}
	}
</script>

<style>
	/*每个页面公共css */
</style>
