<template>
	<view class="page">
		<view class="ans-list">
			<view :class="'ans'+ans.result " v-for="(ans,key) in results" :key="key">{{key+1}}</view>
		</view>
		
		<view class="review" v-on:click="review(0)">复习错误</view>
		<view class="review" v-on:click="review(2)">全部复习</view>
	</view>
</template>

<script>
	
	import common from '../../../../common/common.js';
	
	
	export default {
		data() {
			return {
				groupId:5,
				results:[]
			};
		},
		onLoad(option) {
			
			//this.groupId = option.id;
			
			this.getResult();
			
		},
		methods:{
			getResult:function(){
				
				var url = this.APIURL + "quest/results?groupId="+this.groupId;
				
				common.request(url,{},(res)=>{
					
					this.results = res.data.data.results;
				});
			},
			review:function(type){
				var url = this.APIURL + "quest/review";
				var data={type:type,groupId:this.groupId};
				
				common.request(url,data,(res)=>{
					uni.redirectTo({
						url:"../../da/da?id="+this.groupId+"&review=1"
					})
				})
			}
		}
	}
</script>

<style lang="scss">
.page{
	padding:10px;
}

.ans-list{
	text-align: center;
	margin-top:50px;
	margin-bottom:50px;
	view{
		display: inline-block;
		width:50px;
		line-height:50px;
		border-radius: 100%;
		color:#fff;
		text-align: center;
		font-size:20px;
		margin:5px 2px;
	}
	
	.ans1{
		background:green;
	}
	.ans0{
		background:red;
	}
}


.review{
	margin:auto;
	margin-top:20px;
	background:green;
	color:#fff;
	line-height:50px;
	text-align: center;
	border-radius: 25px;
	width:50%;
	font-size:25px;
	
}
</style>
