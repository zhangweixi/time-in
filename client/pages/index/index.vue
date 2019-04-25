<template>
	<view class="content">
		<view class="flex"></view>
		<view class="container">
			
			<view class="taskinfo">
				
				<view class="time">
					<image v-for="(time,key) in timearr" :class="'num-'+time" :src="'/static/img/icon/num-'+ time +'.png'" mode="widthFix" :key="key"></image>
				</view>
				
				<view v-if="currentTask.status !== false " class="btns">
					<view class="flex"></view>
					
					<image v-if="currentTask.status === 0" 	v-on:click="play()" 	src="/static/img/icon/play.png" mode="widthFix"></image>
					<image v-if="currentTask.status === 1" 	v-on:click="pause()"	src="/static/img/icon/pause.png" mode="widthFix"></image>
					
					<text>{{currentTask.typeName}}</text>
					<image src="/static/img/icon/stop.png" mode="widthFix" v-on:click="finishTask()"></image>
					<view class="flex"></view>
				</view>
			</view>
			
			
			<view class="types">
				<view class="setting">
					<image src="../../static/img/icon/setting.png" mode="widthFix" v-on:click="openSetting()"></image>
					<image src="../../static/img/icon/add.png" mode="widthFix" v-on:click="openAddType()"></image>
				</view>
				
				<view class="things" v-show="!adding">
					<view :class="'thing '+ ttype.type " v-for="(ttype,key)  in taskTypes"  :key="key"  v-on:click="beginTask(ttype.type_id)">
						<text>{{ttype.type_name}}</text>
						<image src="../../static/img/icon/del.png" mode="widthFix" v-show="setting == true" v-on:click="deleteType(ttype.type_id)"></image>
					</view>
				</view>
				
				<view v-show="adding === true">
					<view class="addtype" >
						<input type="text" placeholder="输入事件" v-model="newType">
						<text v-on:click="addType">添加</text>
					</view>
				</view>
				
			</view>
		</view>
		<view class="flex"></view>
	</view>
</template>

<script>
	import common from '../../common/common.js';
	
	export default {
		data() {
			return {
				timearr:[10,10,'mid',10,10,'mid',10,10],
				timeNum:0,
				thingId:0,
				setting:false,
				adding:false,
				timehandler:false,
				newType:"",
				taskTypes:[],
				currentTask:{
					typeId:0,
					taskId:0,
					typeName:'',
					status:false,//false 没有任务 playing:进行中 puase:暂停状态
				}
			}
		},
		onLoad() {
						
		},
		onShow:function() {
			
			common.user.toLogin();
			
			if(common.user.userId > 0 && this.taskTypes.length == 0){
				
				this.getTaskType();
				this.getCurrentTask();
				
			}else{
				setTimeout(()=>{
					if(common.user.userId > 0 && this.taskTypes.length == 0)
					{
						this.getTaskType();
						this.getCurrentTask();
					}
				},1000);
			}
		},
		methods: {
			openSetting:function(){
				
				this.setting = !this.setting;
				this.adding = false;
			},
			openAddType:function(){
				this.adding = !this.adding;
				this.setting = false;
				
				console.log(this.adding);
			},
			getTaskType:function(){
				
				var url 	= this.APIURL + "task/get_task_type";
				var data 	= {};
				
				common.request(url,data,(res)=>{
					res = res.data;
					var types = res.data.types;
					for(var type in types){
						type.type == '';
					}
					this.taskTypes = types;
					this.markTask(this.currentTask.typeId);
				})
			},
			
			deleteType:function(typeId){
				if(this.currentTask.typeId == typeId){
					uni.showToast({
						title:"该事件还在计时",
						icon:"none"
					});
					return;
				}
				var url = this.APIURL + "task/delete_type";
				var data = {typeId:typeId};
				
				uni.showModal({
					title:"删除分类",
					content:"确定删除吗",
					showCancel:true,
					success: (res) => {
						if(res.confirm){
							common.request(url,data,(res)=>{
								this.getTaskType();
							})
						}
					}
				})
			},
			addType:function(){
				if(this.newType.length <2 || this.newType > 8){
					uni.showToast({
						title:"字数控制2~8个之间",
						icon:"none"
					})
					return;
				}
				
				var url = this.APIURL + 'task/create_task_type';
				var data = {typeName:this.newType};
				common.request(url,data,(res)=>{
					res = res.data;
					
					if(res.code != 200){
						uni.showToast({
							title:res.msg,
							icon:"none"
						})
						
					}else{
						
						this.adding = false;
						this.getTaskType();
					}
				})
				
			},
			getCurrentTask:function(){
				
				var url = this.APIURL + "task/get_current_task";
				common.request(url,{},(res)=>{
					res = res.data;
					var task = res.data.task;
					if(task != null){
						
						this.currentTask.typeId = task.type_id;
						this.currentTask.taskId = task.task_id;
						this.currentTask.status = task.status;
						this.currentTask.typeName = task.type_name;
						
						this.timeNum = task.time;
						
						this.formattime(task.time);
						this.markTask(task.type_id);
						
						if(task.status == 1)
						{
							this.timer();
						}
					}
				})
			},
			beginTask:function(taskTypeId)
			{
				if(this.setting == true){
					
					return;
				}
				//创建新的
				if(this.currentTask.taskId == 0){
					
					uni.showModal({
						title:"提示",
						content:"确定开始任务吗",
						showCancel:true,
						success:(res)=>{
							if(res.confirm == true){
								this.createTask(taskTypeId)
							}
						}
					})
					return;
				}else if(this.currentTask.typeId != taskTypeId){
					
					//提示确定更换任务吗
					uni.showModal({
						title:"提示",
						content:"确定切换任务吗",
						showCancel:true,
						success:(res)=>{
							if(res.confirm == true){
								this.finishTaskAction()
								this.createTask(taskTypeId);
							}
						}
					})
				}
			},
			
			
			finishTask:function()
			{
				//提示确定结束任务吗
				uni.showModal({
					title:"提示",
					content:"确定结束计时吗",
					showCancel:true,
					success:(res)=>{
						if(res.confirm == true){
							this.currentTask.status = false;
							this.finishTaskAction()
						}
					}
				})
			},
			
			
			finishTaskAction:function(){
				
				clearInterval(this.timehandler);
				
				this.resetTime();
				
				this.markTask(0);
				
				var url 	= this.APIURL + "task/finish_task";
				var data 	= {taskId:this.currentTask.taskId};
				common.request(url,data,(res)=>{
					
					this.currentTask.taskId = 0;
					this.currentTask.typeId = 0;
					//so something
				})
			},
			markTask:function(taskTypeId){
				
				var taskTypes = this.taskTypes;
				for(var type of taskTypes){
					
					
					if(type.type_id == taskTypeId){
						
						type.type = "checked";
						
					}else{
						type.type = "";
					}
				}
				this.taskTypes = taskTypes;
				
				
			},
			createTask:function(taskTypeId)
			{
				var url 	= this.APIURL + "task/create_task";
				var data 	= {typeId:taskTypeId};
				common.request(url,data,(res)=>{
					
					res = res.data;
					var task = res.data.task;
					this.currentTask.taskId = task.task_id;
					this.currentTask.typeName = task.type_name;
					this.currentTask.typeId = taskTypeId;
					this.currentTask.status = 1;
					
					this.resetTime();
					
					this.markTask(taskTypeId);
				})
				
				this.timer();
			},
			
			
			pause:function()
			{
				//停止计时
				this.currentTask.status = 0;
				clearInterval(this.timehandler);
				
				//向服务器更新时间
				var url = this.APIURL + "task/change_task";
				var data = {taskId:this.currentTask.taskId,'status':0};
				common.request(url,data,(res)=>{
					//do something
				})
			},
			
			
			
			play:function(){

				//向服务器更新
				var url = this.APIURL + "task/change_task";
				var data = {taskId:this.currentTask.taskId,'status':1};
				common.request(url,data,(res)=>{
					//do something
					this.currentTask.status = 1;
					this.timer();
				})
			},
			
			resetTime:function(){
				
				this.timearr = [10,10,'mid',10,10,'mid',10,10];
				this.timeNum = 0;
				
			},
			
			formattime:function(t){
				
				var h = parseInt(t / (60 * 60));
				
				var m = parseInt(t%(60*60) / 60);
				
				var s = parseInt(t % 60);
				
				this.timearr = [parseInt(h/10),h%10,'mid',parseInt(m/10),m%10,'mid',parseInt(s/10),s%10];
			},
			
			timer:function()
			{
				this.timehandler = setInterval(()=>
				{
					this.timeNum += 1;
					this.formattime(this.timeNum);
				},1000);
			}
		}
	}
</script>

<style lang="scss">
	.flex{
		flex: 1;
	}
	.content{
		display: flex;
		flex-direction: column;
		position: fixed;
		top:0;
		left:0;
		height:100%;
		width:100%;
		background-image: url('https://s3.ax2x.com/2019/04/25/bodybg.jpg');
		background-position: center;
		background-size: cover;
		background-repeat: no-repeat;
		.flex{
			flex:1;
		}
	}
	
	
	.taskinfo{
		background:#ddd;
		
		.time {
			text-align: center;
			padding:50upx 20upx;
			
			image{
				display: inline-block;
				width:50upx;
				margin-left: -15upx;
				vertical-align: middle;
			}
			
			image.num-mid{
				width:35upx;
				margin-left:0upx;
				margin-right:15upx;
			}
		}
		
		.btns{
			padding:50upx 0;
			display: flex;
			background:#c5c5c5;
			$h:90upx;
			text{
				font-size:30upx;
				vertical-align: middle;
				line-height:$h - 10;
				margin:5upx 10upx;
				color:#fff;
				background:#1296db;
				padding:0 50upx;
				border-radius: ($h - 10)/2;
			}
			
			image{
				width:$h;
				vertical-align: middle;
				background:#fff;
				border-radius: 100%;
			}
		}
	}
		
		
	.types{
		padding:20upx 10upx 50upx;
		background:#b5b5b5;
		
		.things{
			
			text-align: center;
			.thing{
				display: inline-block;
				padding:5upx 20upx;
				border:1px solid #bbb;
				margin:10upx;
				font-size:25upx;
				border-radius: 5upx;
				background:#fff;
				color:#777;
				position: relative;
				
				&.checked{
					background:#1296db;
					color:#fff;
					border:1px solid #1296db;
					border-radius: 20upx;
				}
				
				image{
					width:35upx;
					right:-15upx;
					top:-15upx;
					position: absolute;
					background:#fff;
					border-radius: 100%;
				}
			}
		}
		
		
		.setting{
			overflow: hidden;
			margin-bottom:30upx;
			image{
				width:30upx;
				float: right;
				border-radius: 100%;
				margin-right:20upx;
				margin-left:10upx;
			}
		}
		
		.addtype{
			display:flex;
			padding:0 20%;
			vertical-align:middle;
			input,text{
				
				height:40upx;
				line-height:40upx;
				padding-top:10upx;
				padding-bottom: 10upx;
			}
			input{
				height:20uxp;
				background:#fff;
				flex:1;
				font-size:25rpx;
				padding-left:10upx;
				color:#777;
				margin:0;
			}
			text{
				width:80upx;
				background:#1296DB;
				color:#fff;
				font-size:20upx;
				border:1px solid #1296DB;
				text-align: center;
				vertical-align:middle;
				padding-top:13upx;
				padding-bottom:13upx;
			}
		}
	}
	
</style>
