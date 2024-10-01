<?php

namespace App;

enum LanguageEnum : string
{
    case EN='en';
    case NL='nl';
    case AR='ar';

    public function getDisplayedName() : string{
        return match($this){
            self::EN => 'English',
            self::NL => 'Dutch',
            self::AR => 'Arabic'
        };
    }
}
