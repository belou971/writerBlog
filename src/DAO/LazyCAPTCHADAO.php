<?php
/**
 * Created by PhpStorm.
 * User: belou
 * Date: 28/07/17
 * Time: 04:24
 */

namespace writerBlog\DAO;



use Domain\LazyCaptcha;

class LazyCAPTCHADAO
{
    const IMG_WIDTH  = 100;
    const IMG_HEIGHT = 50;
    const IMG_SRC    = "/tmp/lazy_captcha.png";

    private $value1;
    private $value2;
    private $operator;
    private $index;
    private $validation_code;
    private $image;

    /**
     * @return mixed
     */
    public function getValidationCode()
    {
        return $this->validation_code;
    }

    /**
     * @param mixed $validation_code
     */
    public function setValidationCode($validation_code)
    {
        $this->validation_code = $validation_code;
    }

    /**
     * LazyCaptcha constructor.
     * @param $value1
     */
    public function __construct()
    {
        $this->init();
    }


    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param mixed $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * @return mixed
     */
    public function getValue1()
    {
        return $this->value1;
    }

    /**
     * @param mixed $value1
     */
    public function setValue1($value1)
    {
        $this->value1 = $value1;
    }

    /**
     * @return mixed
     */
    public function getValue2()
    {
        return $this->value2;
    }

    /**
     * @param mixed $value2
     */
    public function setValue2($value2)
    {
        $this->value2 = $value2;
    }

    /**
     * @return mixed
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param mixed $operators
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    private function init()
    {
        $OPERATORS  = array("+", "-", "*", "/");

        $this->value1 = rand(0, 58);
        $this->value2 = rand(0, 58);
        $this->index  = rand(0, 3);

        $this->operator = $OPERATORS[rand(0, 3)];
        $this->validation_code = $this->compute();

        $this->generateImage();
    }

    private function compute()
    {
        switch($this->operator) {
            case "+":
                $result = $this->value1 + $this->value2;
                break;
            case "-":
                $result = $this->value1 - $this->value2;
                break;
            case "*":
                $result = $this->value1 * $this->value2;
                break;
            case "/":
                $result = $this->value1 / $this->value2;
                break;
            default:
                $result = "";
        }
        return $result;
    }

    private function generateImage()
    {
        //Create an image
        $this->image = imagecreate(LazyCAPTCHADAO::IMG_WIDTH, LazyCAPTCHADAO::IMG_HEIGHT);

        //Apply a background color on this image
        $bg = imagecolorallocate($this->image, 255, 255, 255);
        imagefilledrectangle($this->image,0,0,LazyCAPTCHADAO::IMG_WIDTH, LazyCAPTCHADAO::IMG_HEIGHT,$bg);

        //Apply dots on this image
        $dot_color = imagecolorallocate($this->image, 0,0,0);
        for($i=0 ; $i<500 ; $i++) {
            imagesetpixel($this->image,rand()%LazyCAPTCHADAO::IMG_WIDTH,rand()%LazyCAPTCHADAO::IMG_HEIGHT,$dot_color);
        }

        //Put text validation on the image
        $value1_color = imagecolorallocate($this->image, rand(0,255), rand(0,255), rand(0,255));
        imagestring($this->image, 5,  5+($this->index*30), rand()%LazyCAPTCHADAO::IMG_HEIGHT, $this->value1, $value1_color);

        $op_color = imagecolorallocate($this->image, rand(0,255), rand(0,255), rand(0,255));
        imagestring($this->image, 5,  5+($this->index*30), rand()%LazyCAPTCHADAO::IMG_HEIGHT, $this->operator, $op_color);

        $value2_color = imagecolorallocate($this->image, rand(0,255), rand(0,255), rand(0,255));
        imagestring($this->image, 5,  5+($this->index*30), rand()%LazyCAPTCHADAO::IMG_HEIGHT, $this->value2, $value2_color);

        //generate image into a file
        $path =
        imagepng($this->image,  __DIR__.'/../web/'.LazyCAPTCHADAO::IMG_SRC);
    }
}