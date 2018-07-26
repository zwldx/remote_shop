<?php

namespace app\admin\model;

use think\Model;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class User extends Model
{
    //订单日报
    public function countOrder(){
        //昨天订单总数目
        $ord_total_count = $this->name('order')->whereTime('ctime', 'yesterday')->count();

        //昨天成交的订单数目
        $ord_finish_count = $this->name('order')->whereTime('ctime', 'yesterday')->where('status',1)->count();

        //昨天订单成交率
        $close_rate = $ord_total_count == 0 ? '0%' : ($ord_finish_count/$ord_total_count*100).'%';

        //昨天订单总金额
        $ord_total_money = $this->name('order')->whereTime('ctime', 'yesterday')->sum('total_price');

        //报表内容
        $str = "已完成的订单数目:{$ord_finish_count}<hr>订单总数量:{$ord_total_count}<hr>成交率:{$close_rate}<hr>订单总金额:{$ord_total_money}<hr>";
        return $str;
    }
    //发送邮件
    public function sendMail($mail_addr,$mail_detail)
     {
        //  require '/PHPMailer/src/Exception.php';
        //  require '/PHPMailer./src/PHPMailer.php';
        //  require '/PHPMailer./src/SMTP.php';
 
         $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
         try {
             //服务器配置
         $mail->CharSet ="UTF-8";					 //设定邮件编码
         $mail->SMTPDebug = 0;                        // 调试模式输出
         $mail->isSMTP();                             // 使用SMTP
         $mail->Host = 'smtp.qq.com';                // SMTP服务器
         $mail->SMTPAuth = true;                      // 允许 SMTP 认证
         $mail->Username = 'teo2018';                // SMTP 用户名  即邮箱的用户名
         $mail->Password = 'rxixhstksnpabeic';             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
         $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
         $mail->Port = 465;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持
 
         $mail->setFrom('teo2018@qq.com', 'Mailer');  //发件人
         // $mail->addAddress('sdzwl29@gmail.com', '张小兵');  // 收件人
         $mail->addAddress($mail_addr, '');  // 收件人
         //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
         $mail->addReplyTo('teo2018@qq.com', 'Mailer'); //回复的时候回复给哪个邮箱 建议和发件人一致
         //$mail->addCC('cc@example.com');					//抄送
         //$mail->addBCC('bcc@example.com');					//密送
 
         //发送附件
             // $mail->addAttachment('../xy.zip');         // 添加附件
             // $mail->addAttachment('../thumb-1.jpg', 'new.jpg');    // 发送附件并且重命名
 
             //Content
             $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
             $mail->Subject = '订单日报';
             $mail->Body    = '<h1>订单日报</h1>'             
             . date('Y-m-d H:i:s')
             .'<hr>'
             .$mail_detail;
            //  $mail->AltBody = "如果上面的链接无法点击，复制下面的链接到浏览器地址栏访问\r\n{$url}";
            
             $mail->send();
             echo '邮件发送成功';
              //把用户email地址保存到数据库
            // return json_encode(['error'=>'0']);
         } catch (Exception $e) {
            // return json_encode(['error'=>'1']);
             echo '邮件发送失败: ', $mail->ErrorInfo;
         }
     }
}
