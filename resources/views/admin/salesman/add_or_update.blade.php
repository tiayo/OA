@extends('layouts.admin')

@section('title', '添加/管理业务员')

@section('style')
    @parent
@endsection

@section('breadcrumb')
    <li navValue="nav_0"><a href="#">管理员操作</a></li>
    <li navValue="nav_0_2"><a href="#">添加/管理业务员</a></li>
@endsection

@section('body')
    <div class="col-md-12">

        <!--错误输出-->
        <div class="form-group">
            <div class="alert alert-danger fade in @if(!count($errors) > 0) hidden @endif" id="alert_error">
                <a href="#" class="close" data-dismiss="alert">×</a>
                <span>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </span>
            </div>
        </div>

        <section class="panel">
            <header class="panel-heading">
                添加/更新管理员
            </header>
            <div class="panel-body">
                <form id="form" class="form-horizontal adminex-form" method="post" action="{{ $url }}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="name" class="col-sm-2 col-sm-2 control-label">业务员姓名</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="name" name="name" value="{{ $old_input['name'] }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 col-sm-2 control-label">类型</label>
                        <div class="col-sm-3">
                            <select class="form-control" id="type" name="type">
                                @if(!empty($old_input['type']))
                                    <option value="{{ $old_input['type'] }}">
                                        @if($old_input['type'] == 0)业务员
                                        @elseif($old_input['type'] == 2)组长
                                        @elseif($old_input['type'] == 1)超级管理员
                                        @endif
                                    </option>
                                @endif

                                   <option value="0">业务员</option>

                                @if(can('admin'))
                                        <option value="2">组长</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    @if(can('admin'))
                        <div class="form-group">
                            <label for="group" class="col-sm-2 col-sm-2 control-label">分组</label>
                            <div class="col-sm-3">
                                <select class="form-control" id="group" name="group">
                                    @if(!empty($old_input['group']))
                                        @foreach($groups as $group)
                                            @if($group['id'] == $old_input['group'])
                                                <option value="{{ $group['id'] }}">{{ $group['name'] }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                    @foreach($groups as $group)
                                        <option value="{{ $group['id'] }}">{{ $group['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <label for="email" class="col-sm-2 col-sm-2 control-label">业务员邮箱</label>
                        <div class="col-sm-3">
                            <input type="email" id="old_email" name="email" class="hidden" disabled>
                            <input type="email" class="form-control" id="email" name="email" autoComplete="off" value="{{ $old_input['email'] or null}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-sm-2 col-sm-2 control-label">密码</label>
                        <div class="col-sm-3">
                            <input type="password" id="old_password" name="password" class="hidden" disabled>
                            <input type="password" class="form-control" id="password" autoComplete="off" placeholder="放空则使用默认值或不做修改">
                        </div>
                    </div>
                    <div class="form-group">
                        <div  class="col-sm-2 col-sm-2 control-label">
                            <button class="btn btn-success" type="submit"><i class="fa fa-cloud-upload"></i> 确认提交</button>
                        </div>
                    </div>

                </form>
            </div>
        </section>
    </div>
@endsection

@section('script')
    @parent
    <script>
        $(document).ready(function () {
            $('#password').bind('input propertychange', function() {
                $(this).attr('name', 'password')
            })
        })
    </script>
@endsection
