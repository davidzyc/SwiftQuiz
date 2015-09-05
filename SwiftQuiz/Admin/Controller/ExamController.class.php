<?php

namespace Admin\Controller;
use Think\Controller;
use Think\Image;

class ExamController extends Controller {
    private function chkuserlogin(){
        if(session('uid')==null||session('type')!=1){
            $this->error('非法操作，请重新登录','/Home/user/index');
        }
    }

    public function index(){
        $this->chkuserlogin();
        $exam = M('exam');
        $page = abs(intval($_GET['p']));
        if($page==0){
            $page=1;
        }

        $title = I('get.title');
        $uname = I('get.uname');
        $rule['title'] = array('like','%'.$title.'%');
        $rule['uname'] = array('like','%'.$uname.'%');


        $count = $exam -> where($rule) -> count();
        $page = new \Think\Page($count,25);
        $page -> setConfig('first','首页');
        $page -> setConfig('prev','上页');
        $page -> setConfig('next','下页');
        $page -> setConfig('last','末页');
        $page -> setConfig('theme','<li><a>共%TOTAL_ROW%个用户</a></li><li><a>共%TOTAL_PAGE%页</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $show = $page -> show();
        $list = $exam -> order('date') -> where($rule) -> limit($page->firstRow,$page->listRows) -> select();
        $this -> assign('title','试卷');
        $this -> assign('list',$list);
        $this -> assign('page',$show);

        $this->display();
    }

    public function uploadimg(){

        $this->chkuserlogin();

        $ftype =  array('jpg', 'gif', 'png', 'jpeg', 'bmp');

        $upload = new \Think\Upload();
        $upload->maxSize = 2097152;
        $upload->ext = $ftype;
        $upload->rootPath = './Public/Upload/img/';
        $upload->savePath = '';
        $upload->saveName = 'time';
        $info = $upload->upload();
        if (!$info) {

            $fn = I('get.CKEditorFuncNum');
            $message = '上传失败';
            $str = '<script>window.parent.CKEDITOR.tools.callFunction('.$fn.', \'\', \''.$message.'\');</script>';
            echo $str;

        } else {

            $savepath = '/PUBLIC/Upload/img/'.$info['upload']['savepath'].$info['upload']['savename'];
            $fn = I('get.CKEditorFuncNum');
            $message = '上传成功';
            $str = '<script>window.parent.CKEDITOR.tools.callFunction('.$fn.', \''.$savepath.'\', \''.$message.'\');</script>';
            echo $str;

        }
    }

    public function addproblem(){

        $this->chkuserlogin();
        $problems = M('problems');
        $rule['eid'] = I('get.eid');

        $problemlist = $problems->where($rule)->select();

        $answer = M('answer');
        $answerdata = $answer
            ->field('count(aid) as num,pid,anscontent')
            ->where($rule)
            ->group('pid,anscontent')
            ->select();

        foreach($problemlist as &$problems){
            $pid = $problems['pid'];

            foreach ($answerdata as &$answers) {

                if($pid == $answers['pid']){

                    $problems['answer'][] = $answers;
                }
            }
        }

        $uelink = M('uelink');
        $uedata = $uelink->field('count(id) as num,status')->where($rule)->group('status')->select();

        $finished = 0;
        $doing = 0;
        $haventdo = 0;

        foreach($uedata as &$ue){
            if($ue['status']==0){
                $haventdo = intval($ue['num']);
            }elseif($ue['status']==1){
                $doing = intval($ue['num']);
            }elseif($ue['status']==2){
                $finished = intval($ue['num']);
            }
        }

        if($finished+$doing+$haventdo){
            $rate = 100/($finished+$doing+$haventdo);
            $finishedrate = $finished*$rate;
            $doingrate = $doing*$rate;
            $haventdorate = $haventdo*$rate;
        }

        $examrule['eid'] = I('get.eid');


        $average = intval($uelink->where($examrule)->avg('score'));
        $max = $uelink->where($examrule)->max('score');
        $min = $uelink->where($examrule)->min('score');

        $examrule['uelink.status'] = 2;
        $userfinished = $uelink->where($examrule)->join('user ON uelink.uid = user.uid')->order('score desc')->select();

        $this->assign('problemlist',$problemlist);
        $this->assign('finishedrate',$finishedrate);
        $this->assign('doingrate',$doingrate);
        $this->assign('haventdorate',$haventdorate);
        $this->assign('finished',$finished);
        $this->assign('doing',$doing);
        $this->assign('haventdo',$haventdo);
        $this->assign('average',$average);
        $this->assign('max',$max);
        $this->assign('min',$min);
        $this->assign('userfinished',$userfinished);
        $this->assign('all',$finished+$doing+$haventdo);
        $this->assign('title','题目');
        $this->display();

    }
    public function editproblem(){

        $this->chkuserlogin();
        $problems = M('problems');
        $rule['pid'] = I('get.pid');

        $problem = $problems->where($rule)->limit(1)->select();
        $this->assign('problem',$problem[0]);
        $this->assign('title','编辑题目');
        $this->display();

    }
    public function updateproblem(){

        $this->chkuserlogin();

        $validcreate = array(
            array('pcontent','require','请输入试卷内容！'),
            array('rightans','require','请输入正确答案！'),
            array('score','number','请输入分值！'),
            array('ansunique','require','选择阅卷方式！'),
        );

        $problems = M('problems');

        if($problems->validate($validcreate)->create()){
            $problems->pid = I('get.pid');
            $problems->ansformat = 1;
            $problems->pcontent =  I('post.pcontent','',false);
            $problems->where($rule)->limit(1)->save();
            $this->success('题目修改成功','/Admin/exam/index');
        }else{
            $this->error($problems->getError());
        }

    }

    public function createproblem(){
        $this->chkuserlogin();
        $this->assign('title','添加题目');
        $this->display();
    }

    public function insertproblem(){
        $this->chkuserlogin();

        $validcreate = array(
            array('pcontent','require','请输入试卷内容！'),
            array('rightans','require','请输入正确答案！'),
            array('score','number','请输入分值！'),
            array('ansunique','require','选择阅卷方式！'),
        );

        $problems = M('problems');

        if($problems->validate($validcreate)->create()){
            $problems->eid = I('get.eid');
            $problems->ansunique = 1;
            $problems->ansformat = 1;
            $problems->pcontent =  I('post.pcontent','',false);
            $problems->add();
            $this->success('题目添加成功','/Admin/exam/addproblem/eid/'.I('get.eid'));
        }else{
            $this->error($problems->getError());
        }

    }

    public function deleteproblem(){

        $this->chkuserlogin();
		$rule['pid'] = I('get.pid');
        $problems = M('problems');
        $answer = M('answer');

        if($problems->where($rule)->limit(1)->delete()&&$answer->where($rule)->delete()){
			
            $this->success('删除成功','/Admin/exam/index');
        }else{
            $this->error('删除失败');
        }
    }

    public function create(){
        $this->chkuserlogin();
        $this->assign('title','添加试卷');
        $this->display();
    }
    public function update(){
        $this->chkuserlogin();
        $exam = M('exam');
        $rule['eid'] = I('get.eid');
        $examdata = $exam->where($rule)->limit(1)->select();
        $examdataprocessed = $examdata[0];

        $uelink = M('uelink');

        $uedata = $uelink->field('uid')->where($rule)->select();

        $tag = M('etag');
        $tagdata = $tag->where($rule)->select();
        $this->assign('exam',$examdataprocessed);
        $this->assign('uelink',$uedata);
        $this->assign('tag',$tagdata);
        $this->assign('title','修改试卷信息');
        $this->display();
    }
    public function insert(){
        $this->chkuserlogin();
        $validcreate = array(
            array('title','require','请输入试卷名称！'),
            array('summary','require','请输入简介！'),
            array('timelimit','number','请输入数字！'),
        );

        $exam = M('exam');
        if($exam->validate($validcreate)->create()){
            $exam->date = date('Y-m-d H:i:s');
            $exam->ip = get_client_ip();
            $exam->uname = session('username');
            $exam->adminid = session('uid');
            $exam->add();
            $this->success('试卷添加成功','index');
        }else{
            $this->error($exam->getError());
        }
    }
    public function updatedata(){
        $this->chkuserlogin();
        $validupdate = array(
            array('title','require','请输入试卷名称！'),
            array('summary','require','请输入简介！'),
            array('timelimit','number','请输入数字！'),
        );

        $exam = M('exam');
//        var_dump($user->create());
        if($exam->validate($validupdate)->create()){
            $rule['eid'] = I('post.eid');

            $uelink = M('uelink');
            $exdata = $exam->where($rule)->select();
            $userid = array_filter(array_unique(explode(',',I('post.push'))));
            $userrule['uid'] = array('not in',$userid);
            $userrule['eid'] = I('post.eid');
            if(!$userid){
                $specialrule['eid'] = I('post.eid');
                $uelink->where($specialrule)->delete();
            }else{
                $uelink->where($userrule)->delete();
                $answer = M('answer');
                $answer->where($userrule)->delete();
            }

            foreach($userid as $uid){
                $uid = intval(trim($uid));
                if($uid){
                    $uerule['uid'] = $uid;
                    $uerule['eid'] = I('post.eid');
                    if($uelink->where($uerule)->count()!=1){
                        $uelink->uid = $uid;
                        $uelink->eid = I('post.eid');
                        $uelink->duration = $exdata[0]['timelimit'];
                        $uelink->status = 0;
                        $uelink->add();
                    }
                }
            }
            $exam->where($rule)->limit(1)->save();
            $this->success('试卷信息修改成功','index');
        }else{
            $this->error($exam->getError());
        }
    }
    public function addtag(){
        $this->chkuserlogin();
        $validtag = array(
            array('tagcontent','require','输入标签内容'),
        );

        $etag = M('etag');
        if($etag->validate($validtag)->create()){
            $etag->eid = I('get.eid');
            $etag->add();
            $this->ajaxReturn(true);
        }else{
            $this->ajaxreturn(false);
        }
    }
    public function deltag(){
        $this->chkuserlogin();

        $etag = M('etag');
        $rule['id'] = I('get.id');
        if($etag->where($rule)->limit(1)->delete()){
            $this->ajaxReturn(true);
        }else{
            $this->ajaxreturn(false);
        }
    }
    public function delete(){
        $this->chkuserlogin();
        $user = M('exam');
        $rule['eid'] = I('get.eid');
        if($user->where($rule)->limit(1)->delete()){
            $uelink = M('uelink');
            $uelink->where($rule)->delete();
            $etag = M('etag');
            $etag->where($rule)->delete();
            $answer = M('answer');
            $answer->where($rule)->delete();
            $problem = M('problems');
            $problem->where($rule)->delete();
            $this->success('试卷删除成功','/Admin/exam/index');
        }else{
            $this->error('试卷删除失败');
        }
    }


    public function seeanswer(){

        $this->chkuserlogin();

        $exam = M('exam');
        $examrule['eid'] = I('get.eid');

        $examdata = $exam->where($examrule)->limit(1)->select();

        $problems = M('problems');
        $problemsrule['problems.eid'] = I('get.eid');
        $problemsrule['answer.uid'] = I('get.uid');
        $problemlist = $problems->where($problemsrule)->join('answer ON problems.pid = answer.pid')->select();

        $this->assign('problemlist',$problemlist);
        $this->assign('exam',$examdata[0]);
        $this->assign('title','查看答题情况');

        $this->display();

    }

    public function delanswer(){

        $this->chkuserlogin();

        $answer = M('answer');
        $problemsrule['eid'] = I('get.eid');
        $problemsrule['uid'] = I('get.uid');
        $answer->where($problemsrule)->delete();

        $uelink = M('uelink');
        $uelink->where($problemsrule)->limit(1)->delete();

        $this->success('数据清除成功','/Admin/exam/index');
    }

    public function cleardata(){
        $this->chkuserlogin();
        $rule['eid'] = I('get.eid');
        $uelink = M('uelink');
        $uelink->where($rule)->delete();
        $answer = M('answer');
        $answer->where($rule)->delete();
        $this->success('数据清除成功','/Admin/exam/index');
    }

}