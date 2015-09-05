<?php
namespace Admin\Controller;
use Think\Controller;
class UserController extends Controller {
    private function chkuserlogin(){
        if(session('uid')==null||session('type')!=1){
            $this->error('非法操作，请重新登录','/Home/user/index');
        }
    }
    public function index(){
        $this->chkuserlogin();

        $user = M('user');
        $rule['username'] = array('like','%'.I('get.uname').'%');
        $rule['uid'] = array('like','%'.I('get.uid').'%');
        $count = $user -> where($rule) -> count();
        $page = new \Think\Page($count,25);
        $page -> setConfig('first','首页');
        $page -> setConfig('prev','上页');
        $page -> setConfig('next','下页');
        $page -> setConfig('last','末页');
        $page -> setConfig('theme','<li><a>共%TOTAL_ROW%个用户</a></li><li><a>共%TOTAL_PAGE%页</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $show = $page -> show();
        $list = $user -> order('lastlogintime') -> where($rule) -> limit($page->firstRow,$page->listRows) -> select();
        $this -> assign('list',$list);
        $this -> assign('page',$show);
        $this -> assign('title','用户');
        $this -> display();

    }
    public function create(){
        $this->chkuserlogin();
        $this->assign('title','添加用户');
        $this->display();
    }
    public function update(){
        $this->chkuserlogin();
        $user = M('user');
        $rule['uid'] = I('get.uid');
        $userdata = $user->where($rule)->limit(1)->select();
        $userdataprocessed = $userdata[0];

        $tag = M('utag');
        $tagdata = $tag->where($rule)->select();

        $uelink = M('uelink');
        $uedata = $uelink->where($rule)->select();

        foreach($uedata as $ue){
            $data[] = $ue['eid'];
        }

        if($data){

            $exam = M('exam');
            $examrule['eid'] = array('in',$data);
            $examdata = $exam->where($examrule)->select();
            $this->assign('examlist',$examdata);

        }

        $this->assign('user',$userdataprocessed);
        $this->assign('tag',$tagdata);
        $this->assign('title','修改用户信息');

        $this->display();
    }
    public function insert(){
        $this->chkuserlogin();
        $validcreate = array(
            array('username','require','请输入用户名！'),
            array('password','require','请输入密码！'),
            array('email','email','请输入正确的邮箱！'),
            array('age','number','请输入年龄！'),
            array('sex','require','用户名不得为空！'),
            array('status','require','不得为空！'),
            array('type','require','不得为空！'),
        );

        $user = M('user');
        if($user->validate($validcreate)->create()){
            $user->regtime = date('Y-m-d H:i:s');
            $user->regip = get_client_ip();
            $user->lastlogintime = date('Y-m-d H:i:s');
            $user->lastloginip = get_client_ip();
            $user->add();
            $this->success('用户添加成功','index');
        }else{
            $this->error($user->getError());
        }
    }
    public function updatedata(){
        $this->chkuserlogin();
        $validupdate = array(
            array('uid','require','非法操作'),
            array('username','require','非法操作'),
            array('password','6,64','密码长度应为8-64个字符！',0,'length'),
            array('email','email','请输入正确的邮箱！'),
            array('age','number','请输入年龄！'),
            array('sex','require','用户名不得为空！'),
            array('status','require','非法操作'),
            array('type','require','非法操作'),
        );

        $user = M('user');
        if($user->validate($validupdate)->create()){
            $rule['uid'] = $user->uid;
            $user->where($rule)->limit(1)->save();
            $this->success('用户修改成功','index');
        }else{
            $this->error($user->getError());
        }
    }
    public function addtag(){
        $this->chkuserlogin();
        $validtag = array(
            array('tagcontent','require','输入标签内容'),
        );

        $utag = M('utag');
        if($utag->validate($validtag)->create()){
            $utag->uid = I('get.uid');
            $utag->add();
            $this->ajaxReturn(true);
        }else{
            $this->ajaxreturn(false);
        }
    }
    public function deltag(){
        $this->chkuserlogin();
        $etag = M('utag');
        $rule['id'] = I('get.id');
        if($etag->where($rule)->limit(1)->delete()){
            $this->ajaxReturn(true);
        }else{
            $this->ajaxreturn(false);
        }
    }
    public function delete(){
        $this->chkuserlogin();
        $user = M('user');
        $rule['uid'] = I('get.uid');
        if($user->where($rule)->limit(1)->delete()){
            $utag = M('utag');
            $utag->where($rule)->delete();
            $uelink = M('uelink');
            $uelink->where($rule)->delete();
            $answer = M('answer');
            $answer->where($rule)->delete();
            $this->success('用户删除成功','/Admin/user/index');
        }else{
            $this->error('用户删除失败');
        }
    }
}