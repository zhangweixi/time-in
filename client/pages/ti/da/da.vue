<template>
	<view class="page">
		<view class="title">{{question.title}}</view>
		
		
		<view class="detail" v-if="question.type == 'JD' && showAnswer == true">{{question.answer}}</view>
		
		<view class="flex"></view>
		
		<view class="viewbtn" v-if="question.type == 'JD' && showAnswer == false" v-on:click="answer()">查看答案</view>
		
		
		<view class="answers" v-if="question.type != 'JD' ">
			
			<view v-on:click="answer()" v-for="(ans,key) in question.answer" :key="key">{{ans.content}}</view>
			<view v-on:click="getQuestion()" 	v-if="showAnswer" class="next">下一题</view>
			<view style="background:#fff;color:#fff;" v-if="showAnswer==false">-</view>
		</view>
		
		
		
		
		
		<view class="btns bottom left" v-if="question.type == 'JD'">
			<view class="b1" v-on:click="answer_jd(3)">重来</view>
			<view class="b2" v-on:click="answer_jd(2)">困难</view>
			<view class="b3" v-on:click="answer_jd(1)">一般</view>
			<view class="b4" v-on:click="answer_jd(0)">简单</view>
		</view>
	</view>
</template>


<script>
	import common from '../../../common/common.js';
	export default{
		data() {
			return {
				showAnswer:false,
				groupId:4,
				questId:0,
				question:{
					
				}
			}
		},
		onLoad() {
			
			this.getQuestion();
			
		},
		onShow() {
			
		},
		methods:{
			getQuestion:function(){
				this.showAnswer = false;
				var url = this.APIURL + "quest/get_quest";
				var data = {
					"groupId":this.groupId,
					"questId":this.questId
				};
				
				common.request(url,data,(res)=>{
					var res = res.data;
					
					if(res.data != null){
						
						this.question = res.data.questInfo;
						this.questId = this.question.quest_id;
					}
				});
			},
			answer:function(){
				this.showAnswer = true;
			},
			answer_jd:function(type){
				this.getQuestion();
			}
		}
	}
</script>

<style lang="scss">
	.page{
		display: flex;
		flex-direction: column;
		min-height:100vh;
		padding:0 10px;
	}
	.flex{
		flex:1;
	}
	.title{
		padding:10px 0;
		font-size:25px;
		color:seagreen;
	}
	
	.answers{
		
		margin-top:20px;
		border-radius: 5px;
		font-size:18px;
		
		
		view{
			background:#999;
			color:#fff;
			margin:8px 5px;
			line-height:1.5;
			border-radius: 5px;
			padding:10px;
		}
		.next{
			background:red;
			color:#fff;
			text-align: center;
		}
	}
	.btns{
		view{
			
			text-align: center;
			line-height:40px;
			font-size:25px;
		}
		.b1{
			color:red;
		}
		.b2{
			color:orange;
		}
		.b3{
			color:green;
		}
		
		.b4{
			color:lightseagreen;
		}
	}
	
	.left{
		position: fixed;
		bottom:0;
		left:0;
		width:60px;
		border-top:1px solid #ccc;
		border-right:1px solid #ccc;
		view{
			display: block;
			border-bottom:1px solid #ccc;
			line-height:50px;
		}
	}
	
	.bottom1{
		display: flex;
		bottom: 0;
		left:0;
		width:100%;
		border-top:1px solid #ccc;
		
		view{
			flex:1;
			border-right:1px solid #ccc;
		}
		
	}
	
	.viewbtn{
		text-align: center;
		margin-bottom:50px;
		font-size:30px;
		color:red;
	}
	
	.anstext{
		font-size:20px;
		margin-top:20px;
	}
</style>
