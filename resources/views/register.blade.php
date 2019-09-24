<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{config('admin.title')}} | 注册</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  @if(!is_null($favicon = Admin::favicon()))
  <link rel="shortcut icon" href="{{$favicon}}">
  @endif

  <!-- Bootstrap 3.3.5 -->
  <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/bootstrap/css/bootstrap.min.css") }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/font-awesome/css/font-awesome.min.css") }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/dist/css/AdminLTE.min.css") }}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/iCheck/square/blue.css") }}">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition login-page" @if(config('admin.login_background_image'))style="background: url({{config('admin.login_background_image')}}) no-repeat;background-size: cover;"@endif>
<div id="app" class="login-box">
  <div class="login-logo">
    <a href="{{ admin_url('/') }}"><b>{{config('admin.name')}}</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">注册</p>

    <form action="{{ route('register') }}" method="post">
      <div class="form-group has-feedback {!! !$errors->has('mobile') ?: 'has-error' !!}">

        @if($errors->has('mobile'))
          @foreach($errors->get('mobile') as $message)
            <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
          @endforeach
        @endif

        <input type="text" class="form-control" placeholder="手机号码" name="mobile" value="{{ old('mobile') }}" v-model="mobile">
        <span class="glyphicon glyphicon-phone form-control-feedback"></span>
      </div>
      <div class="form-group mb-3 has-feedback {!! !$errors->has('code') ?: 'has-error' !!}" style="display: flex;">

        @if($errors->has('code'))
          @foreach($errors->get('code') as $message)
            <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
          @endforeach
        @endif

        <input type="text" class="form-control" placeholder="验证码" name="code">
        <div class="input-group-append">
          <button v-bind:disabled="count_down != 60" class="btn btn-outline-secondary" type="button" v-on:click="sendCode">@{{ code_text }}</button>
        </div>
      </div>
      <div class="form-group has-feedback {!! !$errors->has('password') ?: 'has-error' !!}">

        @if($errors->has('password'))
          @foreach($errors->get('password') as $message)
            <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
          @endforeach
        @endif

        <input type="password" class="form-control" placeholder="{{ trans('admin.password') }}" name="password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <!-- /.col -->
        <div class="col-xs-offset-8 col-xs-4">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <button type="submit" class="btn btn-primary btn-block btn-flat">注册</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<script src="https://cdn.bootcss.com/vue/2.6.10/vue.min.js"></script>
<script src="https://cdn.bootcss.com/axios/0.19.0/axios.min.js"></script>
<script>
  new Vue({
    el: '#app',
    data: {
      mobile: '{{ old('mobile') }}',
      count_down: 60,
      code_text: '发送验证码'
    },
    methods: {
      sendCode: function () {
        if(!(/^1[3456789]\d{9}$/.test(this.mobile))){
          alert("手机号码有误，请重填");
          return false;
        }
        this.countdown()
        axios.post('register/send-code', {mobile: this.mobile})
      },
      countdown: function() {
        var that = this;
        if(that.count_down == 0) {
          this.code_text = '发送验证码'
          that.count_down = 60;
          return false;
        } else {
          this.code_text = this.count_down + ' 秒后发送验证码'
          that.count_down--;
        }
        setTimeout(function() {
          that.countdown();
        }, 1000);
      },
    }
  })
</script>
</body>
</html>
