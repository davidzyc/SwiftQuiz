<?php

namespace Home\Controller;
use Think\Controller;

class UserController extends Controller {
    private function chkuserlogin(){
        if(session('uid')==null){
            $this->error('非法操作，请重新登录','/Home/user/index');
        }
    }
    private function chkuserlogined(){
        if(session('uid')!=null && session('type')==0){
            redirect('/Home/index/index');
        }elseif(session('uid')!=null && session('type')==1){
            redirect('/Admin/index/index');
        }
    }
    public function index(){
        $this->chkuserlogined();
        $this->display();
    }
    public function login(){

        $validlogin = array(
            array('username','require','请输入用户名！'),
            array('password','require','请输入密码！'),
        );
        $user = M('user');
        if($user->validate($validlogin)->create()){
            $map['username'] = $_POST['username'];
            $map['password'] = $_POST['password'];

            if($user->where($map)->limit(1)->count()==1){
                $data = $user->where($map)->limit(1)->select();
                if($data[0]['status']==0){
                    $this->error('账户被禁用，请联系管理员');
                }
                session('uid',$data[0]['uid']);
                session('type',$data[0]['type']);
                session('username',$data[0]['username']);
                if($data[0]['type']==0){
                    $this->success('登录成功', '/Home/index');
                }elseif($data[0]['type']==1){
                    $this->success('登录成功', '/Admin/index');
                }
            }else{
                $this->error('用户名或密码错误');
            }

        }else{
            $this->error($user->getError());
        }
    }
    public function logout(){
        session(null);
        $this->success('退出成功','index');
    }
    public function register(){
        $this->chkuserlogined();

        $this->display();

    }
    public function reguser(){
        $this->chkuserlogined();

        $validregister = array(
            array('username','6,64','用户名长度应在6-64个字符之间！',0,'length'),
            array('password','8,64','密码长度应在8-64个字符之间！',0,'length'),
            array('passwordagain','password','两次密码输入不同！',0,'confirm'),
            array('email','email','请输入正确的邮箱！'),
            array('age','number','请输入年龄！'),
            array('sex','require','用户名不得为空！'),
            array('validquestion','jiuyiban','验证问题错误！',1,'equal'),
            array('agreement','1','请您先同意网站服务条款！',1,'equal'),
        );

        $user = M('user');

        if($user->validate($validregister)->create()){

            $rule['username'] = $user->username;

            if($user->where($rule)->count()==1){
                $this->error('用户名已存在');
            }

            $user->type = 0;
            $user->status = 0;
            $user->regtime = date('Y-m-d H:i:s');
            $user->regip = get_client_ip();
            $user->lastlogintime = date('Y-m-d H:i:s');
            $user->lastloginip = get_client_ip();

            $user->add();
            $this->success('注册成功，请等待管理员审核');

        }else{
            $this->error($user->getError());
        }


    }
    public function findmypass(){
        $this->chkuserlogined();

    }
    public function account(){
        $this->chkuserlogin();

        $user = M('user');
        $rule['uid'] = session('uid');
        $data = $user->where($rule)->limit(1)->select();
        $user = $data[0];
//        var_dump($user);
        $this->assign('user',$user);
        $this->display();
    }
    public function accountupdate(){
        $this->chkuserlogin();

        $validupdate = array(
            array('password','require','请输入密码！'),
            array('email','email','请输入正确的邮箱！'),
            array('age','number','请输入年龄！'),
            array('sex','require','用户名不得为空！'),
        );

        $user = M('user');
//        var_dump($user->create());
        if($user->create()){
            $rule['uid'] = session('uid');
            $user->where($rule)->field('password,email,sex,age')->limit(1)->save();
            $this->success('用户修改成功');
        }else{
            $this->error($user->getError());
        }
    }
    public function changepassword(){
        $this->chkuserlogin();

        $validpass = array(
            array('password','8,64','密码长度应为8-64个字符！',0,'length'),
        );

        $user = M('user');
        $rule['uid'] = I('session.uid');
        $rule['password'] = I('post.passwordold');

        if($user->where($rule)->count()==1){
            if(I('post.password')!=I('post.passwordagain')){
                $this->error('两次密码输入不同，请重新输入');
            }
            $condition['uid'] = I('session.uid');
            if($user->validate($validpass)->create()){
                $user->where($condition)->save();
                $this->success('密码更改成功');
            }else{
                $this->error($user->getError());
            }


        }else{
            $this->error('原密码错误');
        }


    }
}