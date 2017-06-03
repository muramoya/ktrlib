<?php
/**
 * KTRLib\Validation\Validator\IntValue
 *
 * 数値のバリデーション
 * PhalconValidationの拡張クラス
 *
 * Phalconには数値のバリデーションクラスが組み込まれていますが
 * mix,maxの指定を個別でできなかったこととbetweenを指定した時のメッセージを別定義できるようにしたいため
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

class IntValue extends Validator
{
    public function validate(\Phalcon\Validation $validation, $attribute)
    {
        $value = intval($validation->getValue($attribute));
        $max =  $this->getOption('maximum');
        $min = $this->getOption('minimum');

        $messageMax = !$this->getOption("messageMax") ? $attribute . ' must not be exceed ' . $max : $this->getOption("messageMax");
        $messageMin = !$this->getOption("messageMin") ? $attribute . ' must be at least ' . $min : $this->getOption("messageMin");
        $messageBetween = !$this->getOption("message") ? $attribute . ' must be between ' . $min . ' and ' . $max : $this->getOption("message");


        if(is_null($max) && is_null($min)) throw new KtrUndefinedException('Undefined min or max condition.');


        if(!is_null($max) && !is_null($min))
        {
            if(intval($max) < intval($min)) throw new KtrRuntimeException('max less than min condition.');
            if ($value >= intval($min) && $value <= intval($max))
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
            if ($value <= intval($max))
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
            if ($value >= intval($min))
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