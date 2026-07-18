<?php
namespace App\Helpers;

final class Schema
{
    public static function unitsTable(): string
    {
        $db = Database::connection();
        $exists = (int)$db->query("SELECT CASE WHEN OBJECT_ID('dbo.UnidadesMedida','U') IS NOT NULL THEN 1 ELSE 0 END")->fetchColumn();
        return $exists === 1 ? 'dbo.UnidadesMedida' : 'dbo.Unidades';
    }

    public static function unitAbbreviationExpression(string $alias = 'u'): string
    {
        $db = Database::connection();
        $has = (int)$db->query("SELECT CASE WHEN COL_LENGTH('" . str_replace('dbo.','dbo.',self::unitsTable()) . "','Abreviatura') IS NOT NULL THEN 1 ELSE 0 END")->fetchColumn();
        return $has === 1 ? "$alias.Abreviatura" : "$alias.Nombre";
    }
}
