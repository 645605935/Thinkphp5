<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
//判断是否登陆
function is_login()
{
    $user = session('ke_user_auth');

    if (empty($user)) {
        return 0;
    }
    else {
        return $user['aid'];
    }
}
//获取登陆uid
function is_admin()
{
    $user = session('ke_user_auth');

    if (empty($user)) {
        return 0;
    }
    else {
        return $user['aid'];
    }
}
function set_redirect_url($url)
{
    cookie('ke_redirect_url', $url);
}

function get_redirect_url()
{
    return cookie('ke_redirect_url');
}
function exprot_file($course_id)
{
    $Sign=db('Sign');
    $Course=db('Course');
    $course=$Course->where(array('course_id'=>$course_id))->find();
    $course_time=date('Y-m-d',$course['course_time_start']);
    $title='课程签到表_'.$course_time.'_'.$course['name'];
    $total = $Sign->where(array('course_id'=>$course_id))->count();
    require(EXTEND_PATH .'Excel/PHPExcel.php');
    $objPHPExcel = new \PHPExcel();
    $objPHPExcel->getProperties()->setCreator('courseSign')->setLastModifiedBy('courseSign')->setTitle($title)->setSubject($title)->setDescription($title)->setKeywords('课程,签到,列表')->setCategory($title);
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet(0)->setTitle($title);
    $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//设置水平居中
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $objPHPExcel->getActiveSheet(0)->setCellValue('A1', '姓名');
    $objPHPExcel->getActiveSheet(0)->setCellValue('B1', '工号');
    $objPHPExcel->getActiveSheet(0)->setCellValue('C1', '部门');
    $objPHPExcel->getActiveSheet(0)->setCellValue('D1', '签到地点');
    $objPHPExcel->getActiveSheet(0)->setCellValue('E1', '签到时间');
    $per_time = 100;
    $times = ceil($total / $per_time);
    $i = 2;
    $where = array(
        'course_id' => $course_id,
    );
    for ($j = 0; $j < $times; $j++) {
        $arr = $Sign->where($where)->limit($j * $per_time, $per_time)->select();
        foreach ($arr as $key => $value) {
            $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('A' . $i, $value['user_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet(0)->setCellValue('B' . $i, $value['user_id']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('C' . $i, $value['user_department']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('D' . $i, $value['sign_place'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet(0)->setCellValue('E' . $i, date('Y-m-d H:i:s',$value['creat_time']));
            $i++;
        }
    }

    $filename = iconv('UTF-8', 'GBK', $title . '.xls');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename=' . $filename);
    header('Cache-Control: max-age=0');
    ob_clean();
    flush();
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
    $objWriter->save('php://output');
}
//获取客户端ip
function getIP(){
    global $ip;
    if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if(getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if(getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else $ip = "Unknow IP";
    return $ip;
}

function getStatus($t){
    return $t?'是':'否';
}
//导出申请表
function exprot_job_file()
{
    $Job=db('Job');
    $title='校招申请表'.date("Y-m-d");
    $total = $Job->count();
    require(EXTEND_PATH .'Excel/PHPExcel.php');
    $objPHPExcel = new \PHPExcel();
    $objPHPExcel->getProperties()->setCreator('jobList')->setLastModifiedBy('jobList')->setTitle($title)->setSubject($title)->setDescription($title)->setKeywords('招聘,申请,列表')->setCategory($title);
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet(0)->setTitle($title);
    $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//设置水平居中
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $objPHPExcel->getActiveSheet(0)->setCellValue('A1', '姓名');
    $objPHPExcel->getActiveSheet(0)->setCellValue('B1', '性别');
    $objPHPExcel->getActiveSheet(0)->setCellValue('C1', '电话');    
    $objPHPExcel->getActiveSheet(0)->setCellValue('D1', '学校');
    $objPHPExcel->getActiveSheet(0)->setCellValue('E1', '专业');
    $objPHPExcel->getActiveSheet(0)->setCellValue('F1', '班级');
    $per_time = 100;
    $times = ceil($total / $per_time);
    $i = 2;
    for ($j = 0; $j < $times; $j++) {
        $arr = $Job->limit($j * $per_time, $per_time)->select();
        foreach ($arr as $key => $value) {
            $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('A' . $i, $value['user_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet(0)->setCellValue('B' . $i, getSex($value['user_sex']),PHPExcel_Cell_DataType::TYPE_STRING);            
            $objPHPExcel->getActiveSheet(0)->setCellValue('C' . $i, $value['user_tel'],PHPExcel_Cell_DataType::TYPE_STRING);            
            $objPHPExcel->getActiveSheet(0)->setCellValue('D' . $i, $value['user_school'],PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet(0)->setCellValue('E' . $i, $value['user_special'],PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet(0)->setCellValue('F' . $i, $value['user_classes'], PHPExcel_Cell_DataType::TYPE_STRING);
            $i++;
        }
    }

    $filename = iconv('UTF-8', 'GBK', $title . '.xls');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename=' . $filename);
    header('Cache-Control: max-age=0');
    ob_clean();
    flush();
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
    $objWriter->save('php://output');
}
function getSex($val){
    return $val=="woman"?'女':'男';
}
function xss_filter($content)
{
    $content = preg_replace('/<script[^>]*?>.*?<\\/script>/si', '', $content);
    $content = preg_replace('/&lt;script[^(&gt;)]*?&gt;.*?&lt;\\/script&gt;/si', '', $content);
    $content = preg_replace('/<img[^>]*?onerror[^>]*?>/si', '', $content);
    $content = preg_replace('/&lt;img[^(&gt;)]*?onerror[^(&gt;)]*?&gt;/si', '', $content);
    $content = preg_replace('/<input[^>]*?on[^>]*?>/si', '', $content);
    $content = preg_replace('/&lt;input[^(&gt;)]*?on[^(&gt;)]*?&gt;/si', '', $content);
    return $content;
}
