<template>
	<view class="page">
		<navigator :url="'/pages/ti/da/da?id='+quest.quest_group_id" class="quest" v-for="(quest,key) in questList" :key="key">
			<text class="title" :style="{color:quest.color}">{{quest.title}}</text>
			<text class="number">{{quest.quest_num}}</text>
		</navigator>
		
		<view class="addbtn" v-if="descstates == false" v-on:click="showdesc()"><image src="../../../static/img/icon/add.png" mode="widthFix"></image></view>
		
		<view class="ruledesc" v-if="descstates == true">
			<image class="close" src="../../../static/img/icon/del.png" mode="widthFix" v-on:click="closedesc()"></image>
			<view class="box">
				<view class="title">请在浏览器中打开这个网址，填写code，提交题库</view>
				<view class="flex"><input type="text" v-model="url" ><text v-on:click="copy(url)">复制网址</text></view>
				<view class="flex"><input type="text" v-model="code" ><text v-on:click="copy(code)">复制CODE</text></view>
			</view>
		</view>
	</view>
</template>

<script>
	import common from '../../../common/common.js';
	export default{
		data() {
			return {
				url:'',
				code:'',
				descstates:false,
				questList:[]
			}
		},
		onLoad() {
			
			this.getTiList();
		},
		onShow() {
			
		},
		onPullDownRefresh() {
			
			this.getTiList();
		},
		methods:{
			getTiList:function(){
				var url = this.APIURL + "quest/get_quest_group";
				var data = {};
				var colors = ["#99CC66","#0099CC","#009966","#FF9933"];
				
				
				common.request(url,data,(res)=>{
					uni.stopPullDownRefresh();
					var res = res.data;
					var quests = res.data.questGroup;
					var i=0;
					this.url 	= res.data.url;
					this.code 	= res.data.code;
					for(var quest of quests){
						quest.color = colors[i%4];
						i++;
					}
					this.questList = quests;
					
				});
			},
			copy:function(data){
				uni.setClipboardData({
					data: data,
					success: function () {
						
					}
				});
			},
			showdesc:function(){
				
				this.descstates = true;
				
			},closedesc:function(){
				this.descstates = false;
			}
		}
	}
	
</script>
	
<style lang="scss">
	.page{
		padding:10px;
	}
	.quest{
		margin:auto;
		padding:10px 0;
		border-bottom:1px solid #eee;
		display: flex;
		.title{
			flex:1;
			font-size:18px;
		}
		
		.number{
			margin-right:10px;
			color:green;
		}
	}
	
	.addbtn{
		position: fixed;
		bottom:10vh;
		right:10vw;
		border-radius: 100%;
		border:2px solid #1296db;
		padding:8px;
		height:25px;
		width:25px;
		
		image{
			width:25px;
		}
	}
	
	.ruledesc{
		position: fixed;
		top:40vh;
		width:80%;
		left:10%;
		background:#555;
		border-radius: 5px;
		color:#fff;
		.close{
			width:40px;
			position: absolute;
			right:-15px;
			top:-15px;
			background:#555;
			border-radius: 100%;
		}
		.box{
			padding:20px 10px;
		}
		.title{
			text-align: center;
			font-size:20px;
		}
		
		.flex{
			display: flex;
			margin-top:20px;
			font-size:16px;
			line-height:30px;
			input{
				flex:1;
				background:#fff;
				color:#555;
				line-height:30px;
				height:30px;
				text-align: center;
			}
			text{
				background:#fff;
				color:red;
				padding:0 10px;
				margin-left:10px;
			}
			
		}
	}
</style>
