<?php
/**
 * Created by PhpStorm.
 * User: belou
 * Date: 28/07/17
 * Time: 03:36
 */

namespace writerBlog\Domain;


class LazyCaptcha
{
    const IMG_WIDTH  = 250;
    const IMG_HEIGHT = 50;
    const IMG_SRC    = "/img/lazy_captcha.png";

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
        $OPERATORS  = array("+", "-", "x",);

        $this->value1 = rand(0, 58);
        $this->value2 = rand(0, 58);
        $this->index  = rand(0, 3);

        $this->operator = $OPERATORS[rand(0, 2)];
        $this->validation_code = $this->compute();

        var_dump($this->validation_code);

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
            default:
                $result = "";
        }
        return $result;
    }

    private function generateImage()
    {
        //Create an image
        $this->image = imagecreate(LazyCaptcha::IMG_WIDTH, LazyCaptcha::IMG_HEIGHT);

        //Apply a background color on this image
        $bg = imagecolorallocate($this->image, 255, 255, 255);
        imagefilledrectangle($this->image,0,0,LazyCaptcha::IMG_WIDTH, LazyCaptcha::IMG_HEIGHT,$bg);

        //Apply dots on this image
        $dot_color = imagecolorallocate($this->image, 0,0,0);
        for($i=0 ; $i<200 ; $i++) {
            imagesetpixel($this->image,rand()%LazyCaptcha::IMG_WIDTH,rand()%LazyCaptcha::IMG_HEIGHT,$dot_color);
        }

        //Put text validation on the image
        $value1_color = imagecolorallocate($this->image, rand(0,255), rand(0,255), rand(0,255));
        imagestring($this->image, 5,  5, 15, $this->value1, $value1_color);

        $op_color = imagecolorallocate($this->image, rand(0,255), rand(0,255), rand(0,255));
        imagestring($this->image, 5,  125, 25, $this->operator, $op_color);

        $value2_color = imagecolorallocate($this->image, rand(0,255), rand(0,255), rand(0,255));
        imagestring($this->image, 5,  200, 35, $this->value2, $value2_color);

        //generate image into a file
        imagepng($this->image,  __DIR__.'/../../web'.LazyCaptcha::IMG_SRC);
    }
}