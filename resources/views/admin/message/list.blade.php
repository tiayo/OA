@extends('layouts.admin')

@section('title', '客户列表')

@section('style')
    @parent
@endsection

@section('breadcrumb')
    <li navValue="nav_0"><a href="#">管理员操作</a></li>
    <li navValue="nav_0_3"><a href="#">消息列表</a></li>
@endsection

@section('body')
<div class="row">
    <div class="col-md-12">
		<section class="panel">
            <div class="panel-body">
            <header class="panel-heading">
                消息列表
            </header>
            	<table class="table table-striped table-hover">
		            <thead>
		                <tr>
		                    <th>ID</th>
		                    <th>类型</th>
                            <th>操作</th>
                            <th>业务员</th>
							<th>内容</th>
							<th>创建时间</th>
                            <th>状态</th>
                            <th>操作</th>
		                </tr>
		            </thead>

		            <tbody id="target">
                        @foreach($messages as $message)
                        <tr>
                            <td>{{ $message['id'] }}</td>
                            <td>
                                @if ($message['type'] == 'group')
                                    分组
                                @elseif($message['type'] == 'customer')
                                    客户
                                @elseif($message['type'] == 'visit')
                                    客户跟踪记录
                                @endif
                            <td>
                                @if ($message['option'] == 1)
                                    更新
                                    @elseif($message['option'] == 2)
                                    新增
                                    @elseif($message['option'] == 3)
                                    删除
                                @endif
                            </td>
                            <td>{{ $message['salesman_name'] }}</td>
                            <td>
                                @foreach(unserialize($message['content']) as $key => $message_content)
                                    {{ $key }}:{{ $message_content }} <br>
                                @endforeach
                            </td>
                            <td>{{ $message['created_at'] }}</td>
                            <td>
                                @if ($message['status'] == 0)
                                    未处理
                                @elseif($message['status'] == 1)
                                    已处理
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-info" type="button" onclick="location='{{ route('message_update', ['id' => $message['id'] ]) }}'">修改状态</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
		        </table>
                {{ $messages->links() }}
            </div>
    	</section>
    </div>
</div>
@endsection

@section('script')
    @parent
    <script type="text/javascript">
        $(function(){
            $(function(){

                $('#group_form').submit(function () {
                    var keyword = $('#search').val();

                    if (stripscript(keyword) == '') {
                        $('#search').val('');
                        return false;
                    }

                    window.location='{{ route('group_search', ['keyword' => '']) }}/' + stripscript(keyword);

                    return false;
                });
            });
        });

        function stripscript(s)
        {
            var pattern = new RegExp("[`~!@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）——|{}【】‘；：”“'。，、？]");
            var rs = "";
            for (var i = 0; i < s.length; i++) {
                rs = rs+s.substr(i, 1).replace(pattern, '');
            }
            return rs;
        }
    </script>
@endsection
