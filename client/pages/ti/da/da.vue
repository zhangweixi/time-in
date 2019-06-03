<template>
	<view class="page">
		<view class="title">{{question.title}}</view>
		
		
		<view class="detail" v-if="showAnswer == true && question.desc">{{question.desc}}</view>
		
		<view class="flex"></view>
		
		<view class="viewbtn" v-if="question.type == 'JD' && showAnswer == false" v-on:click="showAns()">查看答案</view>
		
		
		<view class="answers" v-if="question.type != 'JD' ">
			
			<view class="option" v-on:click="answer(key,ans.isRight)" v-for="(ans,key) in question.answer" :key="key">
				
				<view class="nth" v-if="ans.result == 2">{{ans.nth}}</view>
				<view class="nth" v-if="ans.result == 1"><image src="../../../static/img/icon/r-right.png" mode="widthFix"></image></view>
				<view class="nth" v-if="ans.result == 0"><image src="../../../static/img/icon/r-wrong.png" mode="widthFix"></image></view>
				<view class="flex">{{ans.content}}</view>			
			</view>
			<view class="option next" 	v-if="showAnswer" v-on:click="getQuestion()" >下一题</view>
			<view class="option empty" 	v-if="showAnswer==false">-</view>
		</view>
		
	
		<view class="btns bottom left" v-if="question.type == 'JD'">
			<view class="b1" v-on:click="answer_jd(-1)">重来</view>
			<view class="b2" v-on:click="answer_jd(0)">困难</view>
			<view class="b3" v-on:click="answer_jd(1)">一般</view>
			<view class="b4" v-on:click="answer_jd(2)">简单</view>
		</view>
	</view>
</template>


<script>
	import common from '../../../common/common.js';
	export default{
		data() {
			return {
				showAnswer:false,
				groupId:0,
				questId:0,
				review:0,//是否是复习
				type:2,//复习类型,0:错误，2：所有
				question:{}
			}
		},
		onLoad(option) {
			
			this.groupId	= option.id;
			if(option.type){
				this.type = option.type;
			}
			
			if(option.review){
				this.review = option.review;
			}
			
			this.getQuestion();
			
		},
		onShow() {
			
		},
		methods:{
			getQuestion:function(){
				
				var url = this.APIURL + "quest/get_quest";
				var data = {
					"groupId":this.groupId,
					"questId":this.questId,
					"review":this.review,
					"type":this.type
				};
				
				common.request(url,data,(res)=>{
					var res = res.data;
					
					if(res.data.questInfo != null){
						
						var question	= res.data.questInfo;
						var lettes 		= ["A","B","C","D","E","F","G","H"];
						var i			= 0;
						
						if(question.type != "JD"){
							
							for(var ans of question.answer){
								
								ans.nth 	= lettes[i];
								ans.result 	= 2;
								i++;
							}
						}
						
						this.showAnswer = false;
						this.question 	= question;
						this.questId 	= question.quest_id;
						
					}else{
						
						uni.redirectTo({
							url:"../end/end"
						})
					}
				});
			},
			showAns:function(){
				
				this.showAnswer = true;
			},
			answer:function(ansKey,isRight){
				
				//记录答案
				var answers 	= this.question.answer;
				var len 		= answers.length; 
				
				for(var i = 0; i < len ;i++){
					
					if(i == ansKey){
					
						if(answers[i].isRight == 1){
							
							answers[i].result = 1;
							
						}else if(answers[i].isRight == 0){
							
							answers[i].result = 0;
						}
					}					
				}
				
				this.question.answer = answers;
				
				if(this.showAnswer == false){ //第一次选择正确的才视为回答正确
					
					this.saveAnswer(ansKey,isRight);
				}
				this.showAnswer = true;
			},
			answer_jd:function(answer){
				
				//type有四个等级-1：从来，0：困难，1：一般，2：简单
				var result = answer < 1 ? 0 : 1;
				this.saveAnswer(answer,result);
				setTimeout(()=>{
					
					this.getQuestion();
					
				},500);
				
			},
			saveAnswer:function(answer,result){
				
				var url 	= this.APIURL + "quest/save_answer";
				var data 	= {questId:this.questId,groupId:this.groupId,answer:answer,result:result};
				
				common.request(url,data,(res)=>{
					
					console.log(res);
					
				})
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
		
		.option{
			background:#efefef;
			color:#555;
			margin:8px 5px;
			line-height:1.5;
			border-radius: 5px;
			display: flex;
			overflow: hidden;
			border:1px solid #339966;
			
			view{
				padding:10px 0;
			}
			
			.flex{
				flex:1;
				padding-left:5px;
			}
			
			.nth{
				width:10vw;
				text-align: center;
				background:#339966;
				color:#fff;
				display: flex;
				justify-content:center;
				align-items:Center;
			}
			image{
				width:50%;
			}
		}
		.next{
			background:red;
			color:#fff;
			text-align: center;
			border:none;
			display: block;
		}
		.empty{
			background:#fff;
			color:#fff;
			border:none;
		}
		.empty,.next{
			padding:10px 0;
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
	
	.detail{
		font-size:20px;
		margin-top:20px;
		font-size:20px;
	}
</style>
