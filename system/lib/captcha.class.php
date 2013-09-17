<?php
class captcha {
	private $width = 80, $height = 20, $codenum = 4;
	public $checkcode = 8969; // 产生的验证码
	private $checkimage; // 验证码图片
	private $disturbColor = ''; // 干扰像素
	private $session_flag = 'captcha_code'; // 存到session中的索引
	                                      
	// 尝试开始session
	function __construct() {
		@session_start ();
	}
	/* 参数：（宽度，高度，字符个数） */
	function setConfig($config) {
		$this->width = $config ['width'];
		$this->height = $config ['height'];
		$this->codenum = $config ['codenum'];
		$this->session_flag = $config ['session_flag'];
	}
	function create() {
		ob_clean();
		//输出头
	   $this->outFileHeader();
	   //产生验证码
	   $this->createCode();
	
	   //产生图片
	   $this->createImage();
	   //设置干扰像素
	   $this->setDisturbColor();
	   //往图片上写验证码
	   $this->writeCheckCodeToImage();
	   imagepng($this->checkimage);
	   imagedestroy($this->checkimage);
	   
	}
	/* @brief 输出头 */
	private function outFileHeader() {
		header ( "Content-type: image/png" );
	}
	/**
	 * 产生验证码
	 */
	private function createCode() {
		$this->checkcode = strtoupper ( substr ( md5 ( rand () ), 0, $this->codenum ) );
		$_SESSION[$this->session_flag]=$this->checkcode;
		setcookie($this->session_flag,$this->checkcode);
	}
	/**
	 * 产生验证码图片
	 */
	private function createImage() {
		$this->checkimage = @imagecreate ( $this->width, $this->height );
		$back = imagecolorallocate ( $this->checkimage, 255, 255, 255 );
		$border = imagecolorallocate ( $this->checkimage, 0, 0, 0 );
		imagefilledrectangle ( $this->checkimage, 0, 0, $this->width - 1, $this->height - 1, $back ); // 白色底
		imagerectangle ( $this->checkimage, 0, 0, $this->width - 1, $this->height - 1, $border ); // 黑色边框
	}
	/**
	 * 设置图片的干扰像素
	 */
	private function setDisturbColor() {
		for($i = 0; $i <= 200; $i ++) {
			$this->disturbColor = imagecolorallocate ( $this->checkimage, rand ( 0, 255 ), rand ( 0, 255 ), rand ( 0, 255 ) );
			imagesetpixel ( $this->checkimage, rand ( 2, 128 ), rand ( 2, 38 ), $this->disturbColor );
		}
	}
	/**
	 * 在验证码图片上逐个画上验证码
	 */
	private function writeCheckCodeToImage() {
		for($i = 0; $i < $this->codenum; $i ++) {
			$bg_color = imagecolorallocate ( $this->checkimage, rand ( 0, 255 ), rand ( 0, 128 ), rand ( 0, 255 ) );
			$x = floor ( $this->width / $this->codenum ) * $i;
			$y = rand ( 0, $this->height - 15 );
			imagechar ( $this->checkimage, rand ( 5, 8 ), $x + 5, $y, $this->checkcode [$i], $bg_color );
		}
	}
	function __destruct() {
		unset ( $this->width, $this->height, $this->codenum, $this->session_flag );
	}
}
?>