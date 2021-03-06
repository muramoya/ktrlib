<?php
/**
 * 文字列長のバリデーション
 * PhalconValidationの拡張クラス
 *
 * Phalconには文字列長のバリデーションクラスが組み込まれていますが
 * 明示的にbetweenを指定したいこととbetweenを指定した時のメッセージを別定義できるようにしたいため
 * 独自の定義を作成しました。
 *
 * @author muramoya
 * @version: 1.0
 */

namespace KTRLib\Validation\Validator;

use KTRLib\KtrRuntimeException;
use KTRLib\KtrUndefinedException;
use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;

class StringLength extends Validator
{
    public function validate(\Phalcon\Validation $validation, $attribute)
    {
        $value = $validation->getValue($attribute);
        $max =  $this->getOption('max');
        $min = $this->getOption('min');

        $messageMax = !$this->getOption("messageMax") ? $attribute . ' must not exceed ' . $max . 'characters long' : $this->getOption("messageMax");
        $messageMin = !$this->getOption("messageMin") ? $attribute . ' length must be at least ' . $min . 'characters long' : $this->getOption("messageMin");
        $messageBetween = !$this->getOption("message") ? $attribute . ' length must be between ' . $min . ' and ' . $max  . 'characters long' : $this->getOption("message");


        if(is_null($max) && is_null($min)) throw new KtrUndefinedException('Undefined min or max condition.');


        if(!is_null($max) && !is_null($min))
        {
            if(intval($max) < intval($min)) throw new KtrRuntimeException('max less than min condition.');
            if (strlen($value) >= intval($min) && strlen($value) <= intval($max))
            {
                return true;
            }
            else
            {
                $validation->appendMessage(new Message($messageBetween, $attribute, "between"));
                return false;
            }
        }
        elseif (!is_null($max) && is_null($min))
        {
            if (strlen($value) <= intval($max))
            {
                return true;
            }
            else
            {
                $validation->appendMessage(new Message($messageMax, $attribute, "max"));
                return false;
            }
        }
        elseif (is_null($max) && !is_null($min))
        {
            if (strlen($value) >= intval($min))
            {
                return true;
            }
            else
            {
                $validation->appendMessage(new Message($messageMin, $attribute, "min"));
                return false;
            }
        }
    }
}