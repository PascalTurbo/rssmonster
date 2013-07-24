<?php

include 'config.php';
include 'functions.php';

?>

<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
   <div class="container-fluid">

      <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>

      <!-- Be sure to leave the brand out there if you want it shown -->
      <a class="brand" href="<?php echo "$url/index.php"; ?>">Home</a>

    <ul class="nav">
      <li class="dropdown">
         <a id="drop1" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Views<b class="caret"></b></a>
         <ul class="dropdown-menu" role="menu" aria-labelledby="drop1">
            <li class="detailed-view" role="presentation"><a id="detailed-view" role="menuitem" tabindex="-1" href="#">Detailed view</a></li>
            <li class="list-view" role="presentation"><a id="list-view" role="menuitem" tabindex="-1" href="#">List view</a></li>
         </ul>
      </li>
    </ul>

    <ul class="nav">
      <li class="dropdown">
         <a id="drop1" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Sort<b class="caret"></b></a>
         <ul class="dropdown-menu" role="menu" aria-labelledby="drop1">
            <li class="sort-asc" role="presentation"><a id="sort-asc" role="menuitem" tabindex="-1" href="#">Oldest first</a></li>
            <li class="sort-desc" role="presentation"><a id="sort-desc" role="menuitem" tabindex="-1" href="#">Latest first</a></li>
         </ul>
      </li>
    </ul>

      <!-- Everything you want hidden at 940px or less, place within here -->
      <div class="nav-collapse collapse">

    <ul class="nav">
      <li><a id="import-opml" href="#">Import OPML</a></li>
    </ul>

    <form class="navbar-form pull-left" method="post" placeholder="submitfeed">
      <input type="text" placeholder="Add feed or url" name="feedname" class="feed-query">
      <button type="button" id="submitfeed" class="btn">Submit</button>
    </form>

    <ul class="nav">
      <li><a class="update" href="#">Update feeds</a></li>
    </ul>

    </div>

   </div>
  </div>
</div>

