<template>
	<view class="content">
		
		<view v-for="(task,key) in tasks" :key="key" class="thing">
			<view class="name"><text :style="{background:task.color}">{{task.type_name}}</text></view>
			<view class="time">
				<text class="length">{{task.time}}</text>
				<text class="begin">{{task.created_at}}</text>
			</view>
			<view class="delete">
				<image src="../../static/img/icon/delete.png" mode="widthFix" v-on:click="deleteTask(task.task_id)"></image>
			</view>
		</view>
	</view>
</template>

<script>
	
	import common from '../../common/common.js';
	
	export default {
		data() {
			return {
				lastTaskId:0,
				tasks:[],
				colors:[
					"#99CC66","#0099CC","#009966","#FF9933"
				]
			};
		},
		onLoad:function(){
			
			this.getTaskList(this.page);
			
		},
		onReachBottom:function(event){
			
			this.getTaskList();
			
		},
		onPullDownRefresh:function(){
			
			this.lastTaskId = 0;
			this.tasks = [];
			this.getTaskList();
		},
		methods:{
			getTaskList:function(){
				
				var url = this.APIURL + "task/get_task_list";
				var data = {lastTaskId:this.lastTaskId};
				
				common.request(url,data,(res)=>{
					
					var tasks = res.data.data.tasks;
					uni.stopPullDownRefresh();
					
					if(tasks.length == 0){
						
						var msg = this.tasks.length == 0 ? "还没有记录哦" : "到底啦";
						
						uni.showToast({
							title:msg,
							duration:2000,
							icon:"none"
						});
						return;
					}
					
					var i = 0;
					var t = 0;
					
					for(var task of tasks){
						
						t = task.time;
						var h = parseInt(t / 3600);
						var m = parseInt(t%3600/60);
						var s = t%60;
						
						task.color = this.colors[i%4];
						task.time = h > 0 ? h+"小时":'';
						task.time+= m > 0 ? m+"分钟":'';
						task.time+= s > 0 ? s+"秒":"";
						i++;
					}
					
					this.tasks = this.tasks.concat(tasks);
					this.lastTaskId = this.tasks[this.tasks.length-1].task_id;
				})
			},
			deleteTask:function(taskId){
				
				var url = this.APIURL + "task/delete_task";
				var data = {taskId:taskId};
				uni.showModal(
				{
					title:"提示",
					content:"确定删除吗",
					showCancel:true,
					success:(res)=>
					{
						if(res.confirm == true)
						{
							common.request(url,data,(res)=>
							{
								if(res.data.code != 200){
									
									return uni.showToast({
										title:res.data.msg,
										icon:"none"
									});
									
									return;
								}
								
								//从列表中删除
								var i = 0;
								for(var task of this.tasks){
									
									if(task.task_id == taskId){
										
										this.tasks.splice(i,1);
									}
									i++;
								}
							});
						}
					}
				})
			}
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
