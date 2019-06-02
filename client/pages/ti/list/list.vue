<template>
	<view class="page">
		<navigator :url="'/pages/ti/da/da?id='+quest.quest_group_id" class="quest" v-for="(quest,key) in questList" :key="key">
			<text class="title" :style="{color:quest.color}">{{quest.title}}</text>
			<text class="number">{{quest.quest_num}}</text>
		</navigator>
		<view class="quest add">+</view>
	</view>
</template>

<script>
	import common from '../../../common/common.js';
	export default{
		data() {
			return {
				questList:[]
			}
		},
		onLoad() {
			
			this.getTiList();
		},
		onShow() {
			
		},
		methods:{
			getTiList:function(){
				var url = this.APIURL + "quest/get_quest_group";
				var data = {};
				var colors = ["#99CC66","#0099CC","#009966","#FF9933"];
				
				common.request(url,data,(res)=>{
					var res = res.data;
					var quests = res.data.questGroup;
					var i=0;
					
					for(var quest of quests){
						quest.color = colors[i%4];
						i++;
					}
					this.questList = quests;
					
				});
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
		}
		
		.number{
			margin-right:10px;
			color:green;
		}
		
		&.add{
			color:red;
			font-size:20px;
			font-weight: bold;
		}
	}
	
</style>
