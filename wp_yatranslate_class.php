<?php

/*
Plugin Name: wp_yatranslate_class 
Plugin URI: http://127.0.0.1
Description: переводит выводимый текст на язык клиента через Yandex API
Version: 1.0
Author: Nataly-Mac
Author URI: http://127.0.0.1
License: GPL2
*/
?>
<?php
/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : EMAIl travers.nk@gmail.com)
 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php

class YaTranslate
/** 
* Translate text with YandexTranslate API
* @param  string $key           access key, generated by Yandex
*                               https://tech.yandex.ru/keys/get/?service=trnsl
* @param  string $lang_default  language for translate by default
* @param  string $format        format of text, can be html or plain
* @param  string $ya_link       Yandex Translator link according copyright 
* @param  string $copyright   
* @param  string $url           API Yandex.Translator url   
* all these parameters should be in the settings.json in the current directory 
* like {"param":"value","param":"value"....}
*/
{

public $key = NULL;
public $lang_default = NULL;
public $format = NULL;
public $ya_link = NULL;
public $copyright = NULL;
public $url = NULL;

// TODO public function setParams()

public function setParams()
{
if (@file_get_contents(__DIR__ . '/settings.json')) {
        if (json_decode(file_get_contents(__DIR__ . '/settings.json'), true)) {
        $settings = json_decode(file_get_contents(__DIR__ . '/settings.json'), true);
            //return $settings;
        } else {
        	echo "Can not read the settings file";
        	return false;
        }
    } else {
        echo "Can not find the settings file";
    	return false;
    }

	$this->key = $settings["key"];
    $this->lang_default = $settings["lang_default"];
    $this->format = $settings["format"];
    $this->copyright = $settings["copyright"];
    $this->ya_link = $settings["ya_link"];
    $this->url = $settings["URI"];
// here was a big bug
// i fixed it
}

public function __construct($content)
/** 
*
*/
{
    $this->setParams();
    add_filter('the_content',  array($this,'yaTranslateContentAndComment'));
    add_filter('the_title', array($this,'yaTranslateTitle'));
    add_filter('comment_text', array($this,'yaTranslateContentAndComment'));
}

public function langTo()
/** 
* detect client browser language to translate
* if not, use $lang_default 
*/

{
	$lang_to = $this->lang_default;
	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	    $lang_to = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);	
	}
	return $lang_to;
}

public function yaTranslateRequest($content)
/** 
* send request to yandex.translator with needed parameters
* text to translate = $content
* return translated text in $content
*/
{
	$params = array (
		'key' => $this->key,
		'text' => $content,
		'lang' => $this->langTo(),
		'format' => $this->format,
		);
	$url = $this->url;

        $response_data = wp_remote_post( $url, array(
	'method' => 'POST',
	'timeout' => 45,
	'redirection' => 5,
	'httpversion' => '1.0',
	'blocking' => true,
	'headers' => array(),
	'body' => $params,
	'cookies' => array()
    )
);




public function yaTranslateContentAndComment($content)
/** 
* add copyright to translated text and return for output 
*/
{
	$ya_link = $this->ya_link;
	$copyright = $this->copyright;
	$ya_link_to_add  = '<div><a href=' . $ya_link . '>' . $copyright . '</a></div>'; 
    $content = $this->yaTranslateRequest($content) . $ya_link_to_add;
        return $content;
}

public function yaTranslateTitle($content)
{
    $content = $this->yaTranslateRequest($content);
        return $content;	
}

}

$ya_translate = new YaTranslate();
