<template>
	<view class="root">
		
		<view class="login" >
			<text>登</text>
			<text>录</text>
			<image src="../../static/img/icon/wx.png" mode="widthFix"></image>
			<button open-type="getUserInfo" v-on:click="getInfo()"></button>
		</view>
		
	</view>
</template>

<script>
	
	import common from "../../common/common.js";
	
	export default {
		data() {
			return {
				
			};
		},
		onLoad:function(){
			
			
		},
		
		methods:{
			getInfo:function(){
				
				uni.getUserInfo({
					success:(info)=>{
						
						var url = this.APIURL + 'user/register';
						var userInfo = info.userInfo;
						
						var data = {
							sex:userInfo.gender,
							head:userInfo.avatarUrl,
							nickName:userInfo.nickName,
							mpOpenid:common.user.openid,
							client:'wxmp'
						};
						
						uni.request({
							url:url,
							data:data,
							success:(res) => {
								
								var userinfo = res.data.data.userInfo;
								common.user.userId 	= userinfo.user_id;
								common.user.info 	= userinfo;
								common.user.token 	= userinfo.token;
								//return;
								uni.navigateBack({
									delta: 1
								});
							}
						})
					}
				})
			}
		}
	}
</script>

<style lang="scss">

.root{
	background-image:url('https://s3.ax2x.com/2019/04/25/login.jpg');
	background-position: center;
	background-size: cover;
	position: fixed;
	top:0;
	left:0;
	width:100%;
	height:100%;
}
	
.login{
	width:100upx;
	background:#fff;
	text-align: center;
	padding:30upx 0;
	position: fixed;
	right:50upx;
	top:300upx;
	color:#8a8a8a;
	border-radius: 5upx;
	text{
		text-align: center;
		display: block;
		line-height:60upx;
	}
	
	image{
		width:50upx;
		margin:auto;
		margin-top:15upx;
		
	}
	
	button{
		position: absolute;
		top:0;
		left:0;
		width:100%;
		height:100%;
		z-index: 100;
		background:rgba(0,0,0,0);
		border:none;
	}
}


</style>
