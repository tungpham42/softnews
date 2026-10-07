<?php
namespace App;
class Util
{
 public static function slug(string $value): string { $value=trim(mb_strtolower($value)); $value=iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$value) ?: $value; $value=preg_replace('/[^a-z0-9]+/','-',$value) ?? ''; return trim($value,'-') ?: 'item'; }
}
