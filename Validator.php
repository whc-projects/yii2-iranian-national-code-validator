<?php

// in the name of Allah(God)

namespace mohammadhosain\yii2IranianNationalCodeValidator;

use Yii;

/**
 * 
 * Iranian national code validator for Yii2.
 * 
 * @author mohammad hosain <mohammad.hosain@chmail.ir>
 * @version 0.1
 * 
 * 
 */
class Validator extends \yii\validators\Validator {

    private $pattern = '^[0-9]{8,10}$';

    public function init() {
        if ($this->message == null) {
            Yii::$app->getI18n()->translations['iranian-national-code-validator'] = [
                'class' => \yii\i18n\PhpMessageSource::className(),
                'sourceLanguage' => 'en-US',
                'basePath' => '@vendor/mohammadhosain/yii2-iranian-national-code-validator/messages',
            ];
            $this->message = Yii::t('iranian-national-code-validator', '{attribute} is not valid iranian national code.');
        }
    }

    public function validateValue($value) {
    	if (! is_string ( $value ) || preg_match ( "/{$this->pattern}/", $value ) !== 1) {
    		return [
    				$this->message,
    				[ ]
    		];
    	}
    	if (strlen ( $value ) === 8) {
    		$value = '00' . $value;
    	}
    	if (strlen ( $value ) === 9) {
    		$value = '0' . $value;
    	}
    	if (! is_string ( $value ) || strlen ( $value ) != 10 || strpos ( $value, '000' ) === 0 || in_array ( $value, [
    			'0000000000',
    			'1111111111',
    			'2222222222',
    			'3333333333',
    			'4444444444',
    			'5555555555',
    			'6666666666',
    			'7777777777',
    			'8888888888',
    			'9999999999'
    	] )) {
    		return [
    				$this->message,
    				[ ]
    		];
    	}
    	$i = 0;
    	$chk=0;
    	while ( $i < 10 ) {
    		$chk += intval($value [$i]) * (10 - $i);
    		$i ++;
    	}
    	$chk = ($chk - intval($value [9])) % 11;
    	if ($chk >= 2) {
    		$chk = 11 - $chk;
    	}
    	if ($chk !== intval($value [9])) {
    		return [
    				$this->message,
    				[ ]
    		];
    	}
    	return null;
    }

    public function clientValidateAttribute($model, $attribute, $view) {
        $message = Yii::$app->getI18n()->format($this->message, ['attribute' => $model->getAttributeLabel($attribute)], Yii::$app->language);
        $message = json_encode($message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return <<<JS
myreg=new RegExp('{$this->pattern}');
if(!myreg.exec(value)){
	messages.push($message);
}
else{
	if($.inArray(value,['0000000000','1111111111','2222222222','3333333333','4444444444','5555555555','6666666666','7777777777','8888888888','9999999999'])>=0){
		messages.push($message);
	}
	else{
		chk=0;
		for(i=0;i<10;i++){
			chk=chk+(10-i)*parseInt(value[i]);
		}
		chk=(chk-parseInt(value[9]))%11;
		if(chk>=2){
			chk=11-chk;
		}
		if(chk!=parseInt(value[9])){
			messages.push($message);
		}
	}
}
JS;
        if ($this->skipOnEmpty) {
            $js = "if(value !== ''){ {$js} }";
        }
        return $js;
    }
}
