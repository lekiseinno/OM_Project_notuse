<nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <img alt="image" class="rounded-circle" src="picture/user.png"/>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="block m-t-xs font-bold">User OM</span>
                            <span class="text-muted text-xs block">Emp Control <b class="caret"></b></span>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a class="dropdown-item" href="profile.html">Profile</a></li>
                            <li><a class="dropdown-item" href="contacts.html">Contacts</a></li>
                            <li><a class="dropdown-item" href="mailbox.html">Mailbox</a></li>
                            <li class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="login.html">Logout</a></li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        OM-P
                    </div>
                </li>
                <li class="dashboards_active">
                    <a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboards</span> <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li><a href="index.php">Index</a></li>
                        <li><a href="_plan_upload.php">Plan Upload</a></li>
                        <li><a href="_losstime.php">Losstime input </a></li>
                        <li><a href="production_report.php">Production Report</a></li>
                    </ul>
                </li>
            </ul>

        </div>
    </nav>
<script type="text/javascript">
    $(document).ready(function(){
      $('.dashboards_active').addClass("active");
      $('a').each(function(){
            if ($(this).prop('href') == window.location.href) {
                $(this).addClass('active'); $(this).parents('li').addClass('active');
            }
      });
    });
</script>