<?php
/**
 * KTRLib\Validation\Validation
 *
 * バリデーション定義クラス
 *
 * @author muramoya
 * @version: 1.0
 */

namespace KTRLib\Validation;

use KTRLib\KtrUndefinedException;
use KTRLib\Validation\Validator\IntValue;
use KTRLib\Validation\Validator\StringLength;
use Phalcon\Mvc\Model;
use Phalcon\Validation as PhValidation;

class Validation extends PhValidation
{
    private $fieldName = null;
    private $ruleNameGroup = null;
    private $ruleName = null;
    private $rules = [];

    /**
     * このクラスがnewされた時にバリデーションルールをセットします。
     * このクラスを継承して使う場合はこのメソッドをオーバライドしてバリデーションルールを設定してください。
     */
    public function initialize() {}

    /**
     * フィールド名を設定します。
     * 最初にこのメソッドを必ずコールしてください。
     * @param $name
     * @return $this
     */
    public function field($name)
    {
        $this->fieldName = $name;
        return $this;
    }

    /**
     * バリデーションメッセージを設定します。
     * メッセージを設定しなかったバリデーションルールはPhalconのデフォルトメッセージが表示されます。
     * @param  $msg
     * @return $this
     * @throws KtrUndefinedException
     */
    public function setMessage($msg)
    {
        if(is_null($this->fieldName)) throw new KtrUndefinedException("Field name isn't defined.\nCall 'field' function before this function.");

        if(!is_null($this->ruleNameGroup))
        {
            switch ($this->ruleName)
            {
                case 'filesize':
                    $this->rules[$this->ruleName]['messageSize'] = $msg;
                    break;
                case 'mimetype':
                    $this->rules[$this->ruleName]['messageType'] = $msg;
                    break;
                case 'file_resolution':
                    $this->rules[$this->ruleName]['messageResolution'] = $msg;
                    break;
                case 'min_length':
                    $this->rules[$this->ruleName]['messageMinimum'] = $msg;
                    break;
                case 'max_length':
                    $this->rules[$this->ruleName]['messageMaximum'] = $msg;
                    break;
            }
        }
        else
        {
            $this->rules[$this->ruleName]['message'] = $msg;
        }
        return $this;
    }

    /**
     * 設定したバリデーションを適用します。
     */
    public function build()
    {
        if(is_null($this->fieldName)) throw new KtrUndefinedException("Field name isn't defined.\nSet validation rules before build.");

        foreach ($this->rules as $ruleName => $param)
        {
            $class = $this->factoryValidator($param);
            $this->add($this->fieldName, $class);
        }
        $this->fieldName = null;
        $this->rules = [];
        $this->ruleName = null;
        $this->ruleNameGroup = null;
    }

    /**
     * バリデーションルール: 必須
     * @return $this
     */
    public function required()
    {
        $this->ruleName = __FUNCTION__;
        return $this;
    }

    /**
     * バリデーションルール: 英数字
     * @return $this
     */
    public function alnum()
    {
        $this->ruleName = __FUNCTION__;
        return $this;
    }

    /**
     * バリデーションルール: 英字
     * @return $this
     */
    public function alpha()
    {
        $this->ruleName = __FUNCTION__;
        return $this;
    }

    /**
     * バリデーションルール: 最小値
     * @param $min
     * @return $this
     */
    public function min($min)
    {
        $this->ruleNameGroup = 'value';
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleNameGroup]['minimum'] = $min;
        return $this;
    }

    /**
     * バリデーションルール: 最大値
     * @param $max
     * @return $this
     */
    public function max($max)
    {
        $this->ruleNameGroup = 'value';
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleNameGroup]['maximum'] = $max;
        return $this;
    }

    /**
     * バリデーションルール: 数値範囲
     * @param $min
     * @param $max
     * @return $this
     */
    public function between($min, $max)
    {
        $this->ruleNameGroup = 'value';
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleNameGroup]['minimum'] = $min;
        $this->rules[$this->ruleNameGroup]['maximum'] = $max;

        return $this;
    }

    /**
     * コールバック関数を使ったバリデーションを設定します。
     * @param \Closure $func
     * @return $this
     */
    public function callback(\Closure $func)
    {
        $params['callback'] = $func;
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleName] = $params;
        return $this;
    }

    /**
     * バリデーションルール: 他のフィールドと値が一致
     * @param $with
     * @return $this
     */
    public function confirm_with($with)
    {
        $params['with'] = $with;
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleName] = $params;
        return $this;
    }

    /**
     * バリデーションルール: クレジットカード番号
     * @return $this
     */
    public function credit_card()
    {
        $this->ruleName = __FUNCTION__;
        return $this;
    }

    /**
     * バリデーションルール: 日付形式
     * @param $format
     * @return $this
     */
    public function date_format($format)
    {
        $params['format'] = $format;
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleName] = $params;
        return $this;
    }

    /**
     * バリデーションルール: 数値
     * @return $this
     */
    public function numeric()
    {
        $this->ruleName = __FUNCTION__;
        return $this;
    }

    /**
     * バリデーションルール: メール
     * @return $this
     */
    public function email()
    {
        $this->ruleName = __FUNCTION__;
        return $this;
    }

    /**
     * バリデーションルール: ブラックリスト
     * @param array $domain
     * @return $this
     */
    public function blacklist(array $domain)
    {
        $params['domain'] = $domain;
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleName] = $params;
        return $this;
    }

    /**
     * バリデーションルール: ファイルサイズ
     * @param array $size
     * @return $this
     */
    public function filesize($size)
    {
        $this->ruleNameGroup = 'file';
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleNameGroup]['maxSize'] = $size;

        return $this;
    }

    /**
     * バリデーションルール: MIMETYPE
     * @param array $mime
     * @return $this
     */
    public function mimetype($mime)
    {
        $this->ruleNameGroup = 'file';
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleNameGroup]['allowedTypes'] = $mime;
        return $this;
    }

    /**
     * バリデーションルール: 解像度
     * @param $resolution
     * @return $this
     */
    public function file_resolution($resolution)
    {
        $this->ruleNameGroup = 'file';
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleNameGroup]['maxResolution'] = $resolution;
        return $this;
    }

    /**
     * バリデーションルール: 一意の値(フォーム内)
     * @param $accepted
     * @return $this
     */
    public function identical($accepted = 'yes')
    {
        $params['accepted'] = $accepted;
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleName] = $params;
        return $this;
    }

    /**
     * バリデーションルール: ホワイトリスト
     * @param array $domain
     * @return $this
     */
    public function whitelist(array $domain)
    {
        $params['domain'] = $domain;
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleName] = $params;
        return $this;
    }

    /**
     * バリデーションルール: 正規表現
     * @param $pattern
     * @return $this
     */
    public function regex($pattern)
    {
        $params['pattern'] = $pattern;
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleName] = $params;
        return $this;
    }

    /**
     * バリデーションルール: 文字列の最小長
     * @param $min
     * @return $this
     */
    public function min_length($min)
    {
        $this->ruleNameGroup = 'string_length';
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleNameGroup]['min'] = $min;
        return $this;
    }

    /**
     * バリデーションルール: 文字列の最大長
     * @param $max
     * @return $this
     */
    public function max_length($max)
    {
        $this->ruleNameGroup = 'string_length';
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleNameGroup]['max'] = $max;
        return $this;
    }

    /**
     * バリデーションルール: 文字列長範囲
     * @param $min
     * @param $max
     * @return $this
     */
    public function between_length($min, $max)
    {
        $this->ruleNameGroup = 'string_length';
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleNameGroup]['min'] = $min;
        $this->rules[$this->ruleNameGroup]['max'] = $max;
        return $this;
    }

    /**
     * バリデーションルール: 一意の値(DB)
     * @param Model $model
     * @return $this
     */
    public function unique(Model $model)
    {
        $params['model'] = $model;
        $this->ruleName = __FUNCTION__;
        $this->rules[$this->ruleName] = $params;
        return $this;
    }

    /**
     * バリデーションルール: URL
     * @return $this
     */
    public function url()
    {
        $this->ruleName = __FUNCTION__;
        return $this;
    }

    /**
     * バリデーション失敗時にバリデーションを終了します。
     * @throws KtrUndefinedException
     * @return $this
     */
    public function cancel()
    {
        if(is_null($this->ruleName)) throw new KtrUndefinedException("Rule name isn't defined.\nSet validation rule before this function.");
        $rule = is_null($this->ruleNameGroup) ? $this->ruleName : $this->ruleNameGroup;
        $this->rules[$rule]['cancelOnFail'] = true;
    }

    /**
     * 値がない時に設定されたバリデーションを実行しないようにします。
     * @throws KtrUndefinedException
     * @return $this
     */
    public function allow_empty()
    {
        if(is_null($this->ruleName)) throw new KtrUndefinedException("Rule name isn't defined.\nSet validation rule before this function.");
        $rule = is_null($this->ruleNameGroup) ? $this->ruleName : $this->ruleNameGroup;
        $this->rules[$rule]['allowEmpty'] = true;
    }

    /**
     * バリデータクラスを作成します。
     * @param $param
     * @return PhValidation\ValidatorInterface
     */
    private function factoryValidator($param)
    {
        $rule = is_null($this->ruleNameGroup) ? $this->ruleName : $this->ruleNameGroup;

        switch ($rule)
        {
            case 'required':
                return new PhValidation\Validator\PresenceOf($param);
            case 'alnum':
                return new PhValidation\Validator\Alnum($param);
            case 'alpha':
                return new PhValidation\Validator\Alpha($param);
            case 'value':
                return new IntValue($param);
            case 'callback':
                return new PhValidation\Validator\Callback($param);
            case 'confirm_with':
                return new PhValidation\Validator\Confirmation($param);
            case 'credit_card':
                return new PhValidation\Validator\CreditCard($param);
            case 'date_format':
                return new PhValidation\Validator\Date($param);
            case 'numeric':
                return new PhValidation\Validator\Numericality($param);
            case 'email':
                return new PhValidation\Validator\Email($param);
            case ' blacklist':
                return new PhValidation\Validator\ExclusionIn($param);
            case 'file':
                return new PhValidation\Validator\File($param);
            case 'identical':
                return new PhValidation\Validator\Identical($param);
            case 'whitelist':
                return new PhValidation\Validator\InclusionIn($param);
            case 'regex':
                return new PhValidation\Validator\Regex($param);
            case 'string_length':
                return new StringLength($param);
            case 'unique':
                return new PhValidation\Validator\Uniqueness($param);
            case 'url':
                return new PhValidation\Validator\Url($param);
        }
    }
}
