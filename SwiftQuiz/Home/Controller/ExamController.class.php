<?php

namespace Home\Controller;
use Think\Controller;

class ExamController extends Controller {
    private function chkuserlogin(){
        if(session('uid')==null){
            $this->error('非法操作，请重新登录','/Home/user/index');
        }
    }
    public function index(){

        $uelink = M('uelink');
        $rule['eid'] = I('get.eid');
        $rule['uid'] = I('session.uid');
        $num = $uelink -> where($rule) -> count();

        if($num != 1){
            $this->error('非法操作');
        }else{

            $exam = M('exam');
            $rule['uid'] = null;
            $examdata = $exam -> where($rule) -> limit(1) -> select();
            $examdataprocessed = $examdata[0];

            $examrule['eid'] = I('get.eid');

            $average = intval($uelink->where($examrule)->avg('score'));
            $max = $uelink->where($examrule)->max('score');
            $min = $uelink->where($examrule)->min('score');

            $examrule['uid'] = I('session.uid');
            $score = $uelink->where($examrule)->select();

            $this->assign('examdata',$examdataprocessed);
            $this->assign('average',$average);
            $this->assign('max',$max);
            $this->assign('min',$min);
            $this->assign('score',$score[0]['score']);
            $this->assign('title','试卷');
            $this->display();
        }
    }
    public function exam(){

        $this->chkuserlogin();

        $uelink = M('uelink');
        $rule['eid'] = I('get.eid');
        $rule['uid'] = I('session.uid');
        $num = $uelink -> where($rule) -> count();
        if($num != 1){

            $this->error('非法操作');

        }else{

            $exam = M('exam');
            $examrule['eid'] = I('get.eid');
            $examdata = $exam->where($examrule)->limit(1)->select();

            $uedata = $uelink -> where($rule) -> limit(1) -> select();
//            var_dump($uedata[0]);

            $date = strtotime($uedata[0]['answerdate']);
            if($uedata[0]['status']==2){
                $this->error('您已提交试卷，请不要重复答题');
            }
            if($date == 0 || ($date+intval($uedata[0]['duration']))>=time()){



                if(!$date){
                    $uelink->where($rule);
                    $uelink->answerip = get_client_ip();
                    $uelink->answerdate = date('Y-m-d H:i:s');
                    $uelink->status = 1;
                    $uelink->save();
                }

                $problems = M('problems');
                $rule['eid'] = I('get.eid');
                $problemlist = $problems->field('pid,pcontent,ansformat,eid,score')->where($rule)->select();
                $this->assign('problemlist',$problemlist);
                $this->assign('exam',$examdata[0]);

                $answerlist = $problemlist;

                $answer = M('answer');

                if(!$date){
                    foreach ($answerlist as &$problem) {
                        $answer->uid = I('session.uid');
                        $answer->pid = $problem['pid'];
                        $answer->eid = $problem['eid'];
                        $answer->add();
                    }
                }

                session('eid',I('get.eid'));

                $this->assign('title','考试中');

                $this->display();

            }else{
                $this->error('答题超时');

            }

        }
    }

    public function addans(){

        $this->chkuserlogin();

        $uelink = M('uelink');
        $rule['eid'] = I('post.eid');
        $rule['uid'] = I('session.uid');
        $num = $uelink -> where($rule) -> count();
        if($num != 1){

            $this->ajaxreturn('非法操作');

        }else{

            $uedata = $uelink -> where($rule) -> limit(1) -> select();
//            var_dump($uedata[0]);

            $date = strtotime($uedata[0]['answerdate']);

            if($date == 0 || ($date+intval($uedata[0]['duration']))>=time()){

                $answer = M('answer');
                $rule['pid'] = I('post.pid');
                $rule['uid'] = I('session.uid');
                $answer->anscontent = I('post.anscontent');

                $problems = M('problems');
                $condition['pid'] = I('post.pid');
                $problem = $problems->field('score,ansunique,rightans')->where($condition)->limit(1)->select();
                if($problem[0]['ansunique']==1){
                    if(I('post.anscontent')==$problem[0]['rightans']){
                        $answer->score = intval($problem[0]['score']);
                    }else{
                        $answer->score = 0;
                    }
                }
                if($answer->where($rule)->limit(1)->save()){
                    $this->ajaxreturn(true);
                }else{
                    $this->ajaxreturn('失败');
                }

            }else{
                $this->ajaxreturn('答题超时');
            }

        }

    }

    public function postexam(){

        $this->chkuserlogin();

        $uelink = M('uelink');
        $rule['uid'] = I('session.uid');
        $rule['eid'] = I('session.eid');
        $uedata = $uelink->where($rule)->limit(1)->select();

        $uelink->where($rule);
        $uelink->duration = time() - strtotime($uedata[0]['answerdate']);

        $answer = M('answer');
        $score = $answer->field('SUM(score) as score')->where($rule)->select();

        $uelink->score = $score[0]['score'];
        $uelink->status = 2;

        $uelink->save();

        $this->success('提交成功','/Home/index/index');

    }


    public function detail(){

        $this->chkuserlogin();

        $uelink = M('uelink');
        $uerule['uid'] = session('uid');
        $uerule['eid'] = session('eid');
        $uedata = $uelink -> where($uerule) -> limit(0) -> select();

        if($uedata[0]['status'] != 2){
            $this->error('非法操作');
        }

        $exam = M('exam');
        $examrule['exam.eid'] = I('get.eid');
        $examrule['uelink.uid'] = I('session.uid');

        $examdata = $exam->where($examrule)->join('uelink ON exam.eid = uelink.eid')->limit(1)->select();

        $problems = M('problems');
        $problemsrule['problems.eid'] = I('get.eid');
        $problemsrule['answer.uid'] = I('session.uid');
        $problemlist = $problems->where($problemsrule)->join('answer ON problems.pid = answer.pid')->select();

        $this->assign('problemlist',$problemlist);
        $this->assign('exam',$examdata[0]);
        $this->assign('title','查看成绩');

        $this->display();

    }

}