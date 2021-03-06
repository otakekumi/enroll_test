<?php
    namespace Home\Controller;
    use Think\Controller;
    class UserController extends Controller{
    	public function __initialize(){
    		//初始化函数
    	}

    	public function index(){
                $this->display();
    	}

        public function login(){
            // return "aaaaa";
            if(!isset($_SESSION)){
                session_start();
            }
            // $_SESSION['login_uid'] = 1;
            // $_SESSION['login_username'] = 'test';
            if( $_SESSION['login_uid'] ){

               $Common = D('Common');
                if($Common->isAdmin()){

                    $this->success('登录成功', U('Vote/index'));
                }
                else $this->success('登录成功', U('Vote/votepage'));

            }
            else{
                $this->assign("user",$_SESSION['status']);
                $this->display();
            }
        }


        //Readme
        //input:
        //  username
        //  password
        //output:
        //  true: login success
        //  u: username doesn't exist
        //  p: password is wrong
        //
        public function login_check(){
            $username = I('post.username');
            $password = I('post.password');
            $result = D('registration')
                -> where( "username = '%s'",$username )
                -> getField( 'password' );
            $_SESSION['status']=0;
            if( $result ){
                if( $result === hash("sha256", $password.'HowToUseBcryptInTP?')){
                    if(!isset($_SESSION)){
                        session_start();
                    }
                    $_SESSION['login_uid'] = 1;
                    $_SESSION['login_username']=$username;
                    $power = D('registration')
                        -> where(  "username = '%s'",$username  )
                        -> getField( 'power' );
                    if($power === hash('sha256', 'admin')) {
                            $this->redirect('Vote/index');
                         }
                    else {
                        $this->redirect('Vote/votepage');
                    }
                }
                else{
                    $_SESSION['status']=1;
                    $this->redirect('User/login');
                    
                }
                
            }else{
            $_SESSION['status']=1;
            $this->redirect('User/login');
            }
            
        }

        public function register_index(){
            $this -> display('User/register');
        }
        public function newadmin(){
            $this -> display('User/adregister');
        }
    	public function logout(){
    		$_SESSION = array();
    		session_destroy();
    		$this->success('退出登录', U('User/index'));
    	}
        public function adminregist(){
            $username     =     $_POST['username'];
            $adminpwd     =     $_POST['adminpwd'];
            $adminpwd     =     hash("sha256", $adminpwd.'HowToUseBcryptInTP?');
            $password     =     $_POST['password'];
            $password     =     hash("sha256", $password.'HowToUseBcryptInTP?');
            $phone_number =     $_POST['phone_number'];
            $email        =     $_POST['email'];
            

            $result = D('registration')
                -> where( "username = '%s'",$username )
                -> find();
            if( $result ){
                $this -> ajaxReturn( 'u_repeat' );
            }
            else{
                $isadmin =  D( 'Common' ) -> isAdmin();
                $adname  =  $_SESSION['login_username'];
                $adpass  =  D('registration')
                             -> where( "username = '%s'",$adname )
                             -> getField( 'password' );
                $check   =   $adminpwd===$adpass;
                if( $isadmin && $check ){
                $power = hash( "sha256", 'admin' );
                $data = [
                    "username"     =>   $username,
                    "password"     =>   $password,
                    'phone_number' =>   $phone_number,
                    'email'        =>   $email,
                    'power'        =>   $power
                ];
                 D('registration')
                      -> add( $data );
                   
                 if(!isset($_SESSION)){
                     session_start();
                    }
                 $_SESSION['login_uid'] = 1;
                 $_SESSION['login_username'] =$username;
             //    if( $power ){
                 $this -> ajaxReturn( 'admin' );
              //      }
              //   else{
              //  $this -> ajaxReturn( 'user' );
            //     }
             }
             else{$this -> ajaxReturn( 'powererr' );}
            }
        }
        public function register(){
            $username     =     $_POST['username'];
            $password     =     $_POST['password'];
            $password     =     hash("sha256", $password.'HowToUseBcryptInTP?');
            $phone_number =     $_POST['phone_number'];
            $email        =     $_POST['email'];
            $result = D('registration')
                -> where( "username = '%s'",$username )
                -> find();
            if( $result ){
                $this -> ajaxReturn( 'u_repeat' );
            }
            else{
                $data = [
                    "username"     =>   $username,
                    "password"     =>   $password,
                    'phone_number' =>   $phone_number,
                    'email'        =>   $email
                ];
            
                 D('registration')
                      -> add( $data );
                 if(!isset($_SESSION)){
                     session_start();
                    }
                 $_SESSION['login_uid'] = 1;
                 $_SESSION['login_username'] =$username;
                  
                  
                $this -> ajaxReturn( 'user' );
                 
            }
        }
    }