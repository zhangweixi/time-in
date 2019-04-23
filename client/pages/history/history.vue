<template>
	<view class="content">
		
		<view v-for="(thing,key) in things" :key="key" class="thing">
			<view class="name"><text :style="{background:thing.color}">{{thing.thing}}</text></view>
			<view class="time">
				<text class="length">{{thing.time}}</text>
				<text class="begin">{{thing.begin}}</text>
			</view>
			<view class="delete">
				<image src="../../static/img/icon/delete.png" mode="widthFix"></image>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				things:[
					{
						thing:'吃饭',
						begin:'2019-05-20 02:34:34',
						time:'20:34:09',
						color:'green'
					},
					{
						thing:'学习',
						begin:'2019-05-20 02:34:34',
						time:'20分钟',
						color:'#89cd23'
					},
					{
						thing:'学习练习刚情趣',
						begin:'2019-05-20 02:34:34',
						time:'50分钟',
						color:'#092345'
					}
				]
			};
		}
	}
</script>

<style lang="scss">
	
.content{
	padding:20upx;
}

.thing{
	display: flex;
	padding:20upx 0;
	border-bottom:1px solid #eee;
	.name{
		flex:1;
		font-size:30upx;
		
		text{
			color:#fff;
			display: inline-block;
			vertical-align: middle;
			line-height:60upx;
			padding:0 10upx;
			border-radius: 10upx;
		}
	}
	
	.time{
		text-align: right;
		
		text{
			display: block;
			color:#555;
		}
		
		.length{
			line-height:30upx;
			font-size:30upx;
		}
		
		.begin{
			font-size:20upx;
			line-height:20upx;
			margin-top:10upx;
		}
	}
	
	
	.delete{
		margin-left:20upx;
		margin-right:20upx;
		image{
			width:50upx;
			vertical-align: middle;
		}
	}
	
}



</style>
