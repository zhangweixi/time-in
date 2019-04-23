<template>
	<view class="content" style="background-image: url('/static/img/common/bodybg.jpg');">
		<view class="flex"></view>
		<view class="container">
			<view class="time">
				<image v-for="(time,key) in timearr" :class="'num-'+time" :src="'/static/img/icon/num-'+ time +'.png'" mode="widthFix" :key="key"></image>
			</view>
			
			
			<view class="things">
				
				<view :class="'thing '+ thing.type " v-for="(thing,key)  in someThing"  :key="key"  v-on:click="begin(thing.thingId)">{{thing.name}}</view>
				
			</view>
		</view>
		<view class="flex"></view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				timearr:[10,10,'mid',10,10,'mid',10,10],
				timestr:0,
				thingId:0,
				timehandler:false,
				someThing:[
					{thingId:1,type:'self','name':"吃饭"},
					{thingId:2,type:'system','name':"学习"},
					{thingId:3,type:'checked','name':"看电影"},
					{thingId:4,type:'system',name:"学习历史"},
					{thingId:5,type:'system','name':"英语阅读"},
				]
			}
		},
		onLoad() {
			
		},
		methods: {
			begin:function(thingId){
				
				//创建新的
				if(this.thingId == 0){
					
					uni.showModal({
						title:"提示",
						content:"确定开始任务吗",
						showCancel:true,
						success:(res)=>{
							if(res.confirm == true){
								this.createTask(thingId)
							}
						}
					})
					return;
				}
				
				if(this.thingId == thingId){ //结束
					
					//提示确定结束任务吗
					uni.showModal({
						title:"提示",
						content:"确定结束计时吗",
						showCancel:true,
						success:(res)=>{
							if(res.confirm == true){
								
								this.finishTask()
							}
						}
					})
					
				}else{ //切换任务
					
					//提示确定更换任务吗
					uni.showModal({
						title:"提示",
						content:"确定切换任务吗",
						showCancel:true,
						success:(res)=>{
							if(res.confirm == true){
								this.finishTask(thingId)
								this.createTask(thingId);
							}
						}
					})
				}
			},
			finishTask:function(){
				
				clearInterval(this.timehandler);
			},
			createTask:function(thingId){
				
				var things = this.someThing;
				this.timearr = [10,10,'mid',10,10,'mid',10,10];
				this.timestr = 0;
				
				for(var thing of things){
					
					console.log(thing);
					
					if(thing.thingId == thingId){
						
						thing.type = "checked";
						
					}else{
						thing.type = "";
					}
				}
				
				this.someThing = things;
				this.thingId = thingId;
				this.timer();
			},
			timer:function(){
				
				this.timehandler = setInterval(()=>{
					
					var t = this.timestr + 1;
						
					var h = parseInt(t / 60);
					
					var m = parseInt(t%60/60);
					
					var s = parseInt(t % 60);
					
					this.timearr = [parseInt(h/10),h%10,'mid',parseInt(m/10),m%10,'mid',parseInt(s/10),s%10];
					
					this.timestr = t;
				},1000);
			}
		}
	}
</script>

<style lang="scss">
	.content{
		display: flex;
		flex-direction: column;
		position: fixed;
		top:0;
		left:0;
		height:100%;
		width:100%;
		.flex{
			flex:1;
		}
	}
	
	
	.time {
		text-align: center;
		padding:50upx 20upx;
		background:#ccc;
	}
	
	.time image{
		display: inline-block;
		width:50upx;
		margin-left: -15upx;
		vertical-align: middle;
	}
	.time image.num-mid{
		width:35upx;
		margin-left:0upx;
		margin-right:15upx;
	}
	
	.things{
		padding:50upx 10upx;
		background:#eee;
		text-align: center;
	}
	
	.thing{
		display: inline-block;
		padding:5upx 20upx;
		border:1px solid #bbb;
		margin:10upx;
		font-size:25upx;
		border-radius: 5upx;
		
		&.checked{
			background:green;
			color:#fff;
			border:1px solid green;
		}
	}
</style>
