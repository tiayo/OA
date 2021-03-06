<!--sidebar nav start-->
<ul style="margin-top:100px;" class="nav nav-pills nav-stacked custom-nav">
    @if(can('manage'))
        <li class="menu-list" id="nav_0"><a href=""><i class="fa fa-user"></i> <span>管理员操作</span></a>
            <ul class="sub-menu-list">
                <li id="nav_0_1"><a href="{{ Route('salesman_list') }}">业务员列表</a></li>
                <li id="nav_0_2"><a href="{{ Route('salesman_add') }}">添加业务员</a></li>
                @if(can('admin'))
                    <li id="nav_0_3"><a href="{{ Route('message_list') }}">消息列表</a></li>
                    <li id="nav_0_4"><a href="{{ Route('group_list') }}">业务员分组</a></li>
                @endif
            </ul>
        </li>
    @endif

    <li class="menu-list" id="nav_1"><a href=""><i class="fa fa-star"></i> <span>业务员操作</span></a>
        <ul class="sub-menu-list">
            <li id="nav_1_1"><a href="{{ Route('customer_list') }}">客户列表</a></li>
            <li id="nav_1_2"><a href="{{ Route('customer_add') }}">添加客户</a></li>
            <li id="nav_1_3"><a href="{{ Route('visit_list') }}">客户跟踪记录</a></li>
        </ul>
    </li>

</ul>
<!--sidebar nav end-->