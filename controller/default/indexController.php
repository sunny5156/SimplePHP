<?php
class indexController extends Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->display('index.html');
	}
	
	public function about(){
		echo "<div><img src='http://localhost/framework/index.php?a=captcha&c=index' /></div>";
	}
	
	public function ab(){
		echo $_SESSION['verify'];
	}
	
	public function captcha(){
		
		$captcha = new captcha();
		
		$config = config('captcha');
// 		debug($config);
		$captcha->setConfig($config);
// 		$captcha = load('captcha');
		$captcha->create();
// 		debug($captcha);
	}
	
}
