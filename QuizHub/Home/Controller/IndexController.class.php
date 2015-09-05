<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->chkuser();

        $rule['uid'] = session('uid');

        $exam = M('uelink');
        $examdata = $exam->where($rule)->join('exam ON uelink.eid = exam.eid')->select();

        $this->assign('examlist',$examdata);
        $this->assign('title','试卷列表');
        $this->display();
    }
    private function chkuser(){
        if(session('uid')==null){
            $this->error('非法操作，请重新登录','/Home/user/index');
        }
    }
    public function about(){
        $this->display();
    }
}