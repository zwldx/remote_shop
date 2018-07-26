<?php

namespace app\index\model;

use think\Model;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class User extends Model //自动继承了//根据用户名查询密码
{
    public function get_password_by_username($username)
    {
        //根据用户名查询密码
        
        return $this->where('username', $username)->field('password,userid')->find();
        
        //$this指的是当前类指向的对象（实例化）
    }
    public function insert_to_shopuser($user, $paswd, $phone)
    {
        $data = ['username' => $user, 'password' => $paswd, 'phone' => $phone];
        //插入并返回插入自增的id
        return $this->insertGetId($data);
        //return User::table;
    }

    public function update_by_username($user, $newpaswd)
    {
        //根据名字修改密码(先查后改)
        $shopuser=User::where('username', $user)->find();//只能用静态
        $shopuser->password = $newpaswd;
        $shopuser->save();
    }

    //
    //激活邮箱：修改邮箱激活状态并清除激活码
    public function mailActive($uid){
        User::save(['active_code'=>'','mail_active_status'=>1],['userid'=>$uid]);
    }

    //生成激活邮件链接并把随机验证码插入数据库
    public function creatMailUrl($uid){
        //获取6位随机码
        $code = getRandStr();
        //将随机码保存到数据库中
        User::save([
            'active_code' =>$code
        ],['userid'=>$uid]);
        //激活链接过期时间
        $time = time()+30;
        //生成签名
        $sign = md5($uid.$code.$time);
        // echo $uid."<br>";
        // echo $code."<br>";
        // echo $time."<br>";
        $url = "http://shop.com/index/user/mailActive?uid={$uid}&sign={$sign}&time={$time}";
        return $url;
        
    }

     //发送激活邮件
     public function sendActiveMail($mail_addr,$url,$uid)
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
         $mail->Host = '	smtp.qq.com';                // SMTP服务器
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
         $mail->Subject = '你好，请激活';
             $mail->Body    = '<h1>激活邮箱</h1>'
             .'<a href="'.$url.'">点击激活邮箱</a>' 
             . date('Y-m-d H:i:s');
             $mail->AltBody = "如果上面的链接无法点击，复制下面的链接到浏览器地址栏访问\r\n{$url}";
 
             $mail->send();
            //  echo '邮件发送成功';
              //把用户email地址保存到数据库
            User::save(['email'=>$mail_addr,'mail_active_status'=>0],['userid' => $uid]);
            return json_encode(['error'=>'0']);
         } catch (Exception $e) {
            return json_encode(['error'=>'1']);
            //  echo '邮件发送失败: ', $mail->ErrorInfo;
         }
     }

     public function chkSign($uid,$get_sign,$time){
        //数据库中的用户激活码
        $active_code = User::where('userid',$uid)
        ->field('active_code')
        ->find()['active_code'];
        $sign = md5($uid.$active_code.$time);
        return $sign==$get_sign;
     }
}
