<template>
	<div class="feeds-inner">
		<div id="monster">
			<p>RSSMonster</p>
		</div>
		<div class="drag">
			<div>
				<div @click="refreshFeeds()" class="option" id="refresh"><i id="newfeedicon" class="fas fa-sync-alt" data-fa-transform="left-4"></i>Refresh feeds<span v-show="refreshing"><i class="fas fa-spinner fa-spin" data-fa-transform="right-4"></i></span></div>
			</div>
			<div>
				<div @click="$store.modal='newfeed'" class="option" id="addnew"><i id="newfeedicon" class="fas fa-pencil-alt" data-fa-transform="left-4"></i>Add new feed</div>
			</div>
			<div>
				<p class="title">Current selection</p>
			</div>
			<div v-on:click="loadType('unread')" v-bind:class="{ 'selected':  $store.data.status === 'unread' }" id="unread" class="sidebar-category-top">
				<span class="glyphicon">
					<i class="fas fa-circle" data-fa-transform="down-5 shrink-2"></i>
				</span>
				<span class="title">Unread</span>
				<span class="badge-unread">
					<span class="badge">{{ composedSum['unread_count'] }}</span>
				</span>
			</div>
			<div v-on:click="loadType('read')" v-bind:class="{ 'selected':  $store.data.status === 'read' }" id="read" class="sidebar-category-top">
				<span class="glyphicon">
					<i class="far fa-circle" data-fa-transform="down-5 shrink-2"></i>
				</span>
				<span class="title">Read</span>
				<span class="badge-unread">
					<span class="badge">{{ composedSum['read_count'] }}</span>
				</span>
			</div>
			<div v-on:click="loadType('star')" v-bind:class="{ 'selected':  $store.data.status === 'star' }" id="star" class="sidebar-category-top">
				<span class="glyphicon">
					<i class="far fa-heart" data-fa-transform="down-5 shrink-2"></i>
				</span>
				<span class="title">Star</span>
				<span class="badge-unread">
					<span class="badge">{{ composedSum['star_count'] }}</span>
				</span>
			</div>
			<div>
				<p class="title">All</p>
			</div>
			<div v-on:click="loadAll()" v-bind:class="{ 'selected':  $store.data.category === null }" id="all" class="sidebar-category-top">
				<span class="glyphicon">
					<i class="fas fa-certificate" data-fa-transform="down-5 shrink-2"></i>
				</span>
				<span class="title">Load all categories</span>
				<span class="badge-unread">
					<span v-if="$store.data.status === 'unread'" class="badge">{{ composedSum['unread_count'] }}</span>
					<span v-if="$store.data.status === 'read'" class="badge">{{ composedSum['read_count'] }}</span>
					<span v-if="$store.data.status === 'star'" class="badge">{{ composedSum['star_count'] }}</span>
				</span>
			</div>
			<div>
				<p class="title">Categories</p>
			</div>
			<draggable :list="composedCategories" class="dragArea" :options="{group:{ name:'category', pull:'clone', put:false}}" @end="updateSortOrder">
				<div class="sidebar-category-main" v-bind:class="{ 'selected': ($store.data.category === category.id) && ($store.data.feed === null) }" v-on:click="loadCategory(category)" v-bind:id="category.id" v-for="(category, index) in composedCategories" :key="index">
					<div class="sidebar-category-sub">
						<span class="glyphicon">
							<i class="far fa-folder" data-fa-transform="down-5 shrink-2"></i>
						</span>
						<span class="title">{{category.name}}</span>
						<span class="badge-unread">
							<span v-if="$store.data.status === 'unread'" class="badge">{{category.unread_count}}</span>
							<span v-if="$store.data.status === 'read'" class="badge">{{category.read_count}}</span>
							<span v-if="$store.data.status === 'star'" class="badge">{{category.star_count}}</span>
						</span>
					</div>
					<div v-if="category.feeds">
						<div v-if="$store.data.category === category.id" class="sidebar-category-feeds">
							<div class="sidebar-category-feed" v-on:click.stop="loadFeed(feed)" v-bind:class="{ 'selected': $store.data.feed === feed.id }" v-bind:id="feed.id" v-for="(feed, index) in category.feeds" :key="index">
								<span class="glyphicon">
									<i class="far fa-square" data-fa-transform="down-5 shrink-2"></i>
								</span>
								<span class="title">{{feed.feed_name}}</span>
								<span class="badge-unread">
									<span v-if="$store.data.status === 'unread'" class="badge">{{feed.unread_count}}</span>
									<span v-if="$store.data.status === 'read'" class="badge">{{feed.read_count}}</span>
									<span v-if="$store.data.status === 'star'" class="badge">{{feed.star_count}}</span>
								</span>
							</div>
						</div>
					</div>
				</div>
			</draggable>
			<div>
				<p class="title">Options</p>
			</div>
			<div class="category-options">
				<div @click="$store.modal='newcategory'" id="add" class="category-button"><div><i class="fas fa-plus" data-fa-transform="down-8 grow-6"></i></div><div class="text">Add</div></div>
				<div v-if="($store.data.category != null) && ($store.data.feed == null)" @click="$store.modal='deletecategory'" id="delete" class="category-button"><div><i class="fas fa-trash-alt" data-fa-transform="down-8 grow-6"></i></div><div class="text">Delete</div></div>
				<div v-if="($store.data.category != null) && ($store.data.feed == null)" @click="$store.modal='renamecategory'" id="rename" class="category-button"><div><i class="fas fa-pencil-alt" data-fa-transform="down-8 grow-6"></i></div><div class="text">Name</div></div>
				<div v-if="($store.data.category != null) && ($store.data.feed != null)" @click="$store.modal='deletefeed'" id="delete" class="category-button"><div><i class="fas fa-trash-alt" data-fa-transform="down-8 grow-6"></i></div><div class="text">Delete</div></div>
				<div v-if="($store.data.category != null) && ($store.data.feed != null)" @click="$store.modal='renamefeed'" id="rename" class="category-button"><div><i class="fas fa-pencil-alt" data-fa-transform="down-8 grow-6"></i></div><div class="text">Name</div></div>
			</div>
		</div>
	</div>
</template>

<style>
.view-toolbar {
	width: 100%;
	background-color: #31344B;
	display: -webkit-box;
	display: -webkit-flex;
	display: -ms-flexbox;
	display: flex;
	height: 41px;
	border-bottom: 1px solid transparent;
	border-color: #dcdee0;
	position: absolute;
	color: #fff;
}

.view-button {
	-webkit-box-flex: 1;
	-webkit-flex: 1;
	-ms-flex: 1;
	flex: 1;
	text-align: center;
	line-height: 41px;
	height: 100%;
	text-decoration: none;
	display: block;
	font-size: 10px;
	text-transform: uppercase;
	font-weight: bold;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
	color: #b4b6b8;
}

a#rssmonster.view-button {
	background: url(./../../images/monster.svg) 10px 10px no-repeat;
	background-size: 20px 20px;
    max-width: 40px;
    min-width: 40px;
}

a#title.view-button {
	text-align: left;
    margin-left: 10px;
	max-width: 90px;
}

a#unread.view-button, a#star.view-button, a#read.view-button {
	border-left: 1px solid transparent;
	border-color: #dcdee0;
	cursor:pointer;
}

.feeds-droppable {
	background-color: #eff1f3;
	position: absolute;
	top: 41px;
	bottom: 0;
	left: 0;
	right: 0;
	overflow-y: auto;
	overflow-x: hidden;
	-webkit-overflow-scrolling: touch;
}

.normal {
	background-color: grey;
}

.drag {
	background-color: #eff1f3;
	width:100%;
}

.dragArea {
	min-height: 20px;
}

div.sidebar-category-sub, div.sidebar-category-feed, div.sidebar-category-top {
	padding: 4px 4px 4px 12px;
}

span.badge {
	float: right;
	color: #fff;
	background-color: transparent !important;
}

sidebar-category-main {
	width: 100%;
}

span.glyphicon {
	float:left;
	margin-right:3px;
}

span.title {
	text-overflow: ellipsis;
	overflow:hidden;
	white-space:nowrap;
	display: block;
	padding-right: 25px;
}

span.badge-unread {
	float:right;
	position: absolute;
	right: 28px;
	margin-top: -19px;
}

div.drag {
	background-color: transparent;
    color: #fff;
	margin-top: 10px;
}

div.sidebar-category-top, div.sidebar-category-main {
    background-color: #696A7B;
    margin-left: 12px;
    margin-right: 12px;
	margin-top: 4px;
	border-radius: 4px;
	cursor: pointer;
}

div.sidebar-category-feed {
	background-color: #696A7B !important;
}

div.sidebar-category-top.selected, div.sidebar-category-main.selected, div.sidebar-category-feed.selected {
	background-color: #464F9E !important;
}

p.title {
    color: #AEB1D4;
    margin-left: 14px;
    margin-top: 10px;
    margin-bottom: 5px;
}

div.option {
    margin-left: 12px;
    padding: 6px;
    color: #fff;
	border-radius: 4px;
	text-indent: 10px;
	margin-bottom: 20px;
	cursor: pointer;
}

div#refresh, div#addnew {
	white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}

div#refresh.option {
	background-color: #6F79D3;
	margin-right: 70px;
	min-width: 155px;
}

div#addnew.option {
    background-color: #51556A;
	margin-right: 60px;
	min-width: 155px;
}

div#monster {
	background: url(./../../images/monster.svg) 14px 30px no-repeat;
	background-size: 30px 30px;
	height: 100px;
}

div#monster p {
	padding: 30px 0px 0px 50px;
    color: #fff;
    font-size: 20px;
}

::-webkit-scrollbar {
	width: 6px;	/* for vertical scrollbars */
	height: 6px; /* for horizontal scrollbars */
}

::-webkit-scrollbar-track {
	background: rgba(0, 0, 0, 0.1);
}

::-webkit-scrollbar-thumb {
	background: rgba(0, 0, 0, 0.5);
}

div.category-options {
	margin-bottom: 20px;
	height: 40px;
	width: 105%;
}

div.category-button {
	margin-left: 12px;
	height: 44px;
    color: #fff;
    border-radius: 4px;
    width: 42px;
    float: left;
	text-align: center;
}

div.category-button#add {
	background-color: #6F79D3;
}

div.category-button#delete {
	background-color: #6F79D3;
}

div.category-button#rename {
	background-color: #6F79D3;
}

div.category-button div.text {
	font-size: 12px;
	margin-top: 10px;
}

</style>

<script>
import draggable from 'vuedraggable';

export default {
	data() {
		return {
			categoriesOrder: [],
			refreshing: false
		}
	},
	store: {
		data: 'data',
		refreshCategories: 'refreshCategories',
		categories: 'categories'
	},
	components: {
		draggable
	},
	created: function () {
		this.fetchCategories();
	},
	mounted: function () {
		//when the unread count is zero start polling new articles
		/* setInterval(function () {
			if (this.$store.composedSum.unread_count == 0) {
				this.refreshFeeds();	
			}
		}.bind(this), 30000); */
	},
	methods: {
		fetchCategories: function() {
			this.$http.get('categories')
			.then(response => {
				return response.json();
			})
			.then(data => {
				this.$store.categories = data;
			});
		},
		loadType: function (status, event) {
			this.$store.data.status = status;
		},
		loadCategory: function (category, event) {
			this.$store.data.category = category.id;
			this.$store.data.feed = null;
		},
		loadFeed: function (feed, event) {
			this.$store.data.feed = feed.id;
		},
		loadAll: function (event) {
			this.$store.data.category = null;
			this.$store.data.feed = null;
		},
		refreshFeeds: function (event) {
			//show spinner
			this.refreshing = true;
			this.$http.get('feeds/updateall', {
				}).then(response => {
					//check if there is at least one article renewed
					if (response.body.count === parseInt(response.body.count, 10)) {
						//increase refresh which triggers watcher to refresh the Home
						this.$store.data.refresh++;
						//remove spinner
						this.refreshing = false;
					}
				}, response => {
					//remove spinner
					this.refreshing = false;
					console.log('oops something went wrong');
				});
		},
		updateSortOrder(e) {
			var orderList = new Array();
			for (let i = 0; i < this.$store.categories.length; i++) {
				orderList.push(this.$store.categories[i]['id']);
			}
			console.log(orderList);

			//make ajax request to change categories order
			this.$http.post('categories/updateorder', {order: orderList}).then(response => {
				//get status & status text
				console.log(response.status);
			}, response => {
				console.log('oops something went wrong');
			});
		}
	},
	//watch the refreshCategories, when changing reload the categories
    watch: {
		'$store.refreshCategories': {
			handler: function(val, oldVal) {
				this.fetchCategories();
        	}
		},
		composedSum: function(val) {
			this.$store.composedSum = val;
		}
    },
	computed: {
		orderList() {
			var orderList = new Array();
			this.$store.categories.forEach(function(category) {
				orderList.push(category.id);
			});
			this.categoriesOrder = orderList;
			return orderList;
		},
		composedCategories() {
			var composedCategories = this.$store.categories;
			Object.keys(composedCategories).forEach(function (key) {
				composedCategories[key]['unread_count'] = 0
				composedCategories[key]['read_count'] = 0
				composedCategories[key]['total_count'] = 0
				composedCategories[key]['star_count'] = 0
				var feeds = composedCategories[key]['feeds'];
				Object.keys(feeds).forEach(function (feed) {
					if (!(composedCategories[key] === undefined || composedCategories[key] === null)) {
						composedCategories[key]['unread_count'] = composedCategories[key]['unread_count'] + feeds[feed]['unread_count'];
						composedCategories[key]['read_count'] = composedCategories[key]['read_count'] + feeds[feed]['read_count'];
						composedCategories[key]['total_count'] = composedCategories[key]['total_count'] + feeds[feed]['total_count'];
						composedCategories[key]['star_count'] = composedCategories[key]['star_count'] + feeds[feed]['star_count'];
					}
				});
			});
			return composedCategories;
		},
		composedSum() {
			var composedCategories = this.$store.categories;
			var composedSum = [];
			composedSum['unread_count'] = 0
			composedSum['read_count'] = 0
			composedSum['total_count'] = 0
			composedSum['star_count'] = 0
			Object.keys(this.$store.categories).forEach(function (key) {
				var feeds = composedCategories[key]['feeds'];
				Object.keys(feeds).forEach(function (feed) {
					if (!(composedCategories[key] === undefined || composedCategories[key] === null)) {
						composedSum['unread_count'] = composedSum['unread_count'] + feeds[feed]['unread_count'];
						composedSum['read_count'] = composedSum['read_count'] + feeds[feed]['read_count'];
						composedSum['total_count'] = composedSum['total_count'] + feeds[feed]['total_count'];
						composedSum['star_count'] = composedSum['star_count'] + feeds[feed]['star_count'];
					}
				});
			});
			return composedSum;
		}
	}
}
</script>
