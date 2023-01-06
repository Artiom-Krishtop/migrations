<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;


class Version_replace_data20230106103624 extends Version
{
    protected $description = "";

    protected $moduleVersion = "4.2.2";

    public function up()
    {
        if(Loader::includeModule('iblock')){
            function getElementsCodes($id){
                $result = array();
            
                $dbRes = \CIblockElement::GetList(
                    array(),
                    array(
                        'IBLOCK_ID' => $id,
                    ),
                    false,
                    false,
                    array(
                        'ID',
                        'CODE',
                    )
                );
        
                while ($res = $dbRes->GetNext()) {
                    $result[$res['ID']] = trim($res['CODE']);
                }
            
                return $result;
            }
            
            $arReplaceData = array();
            
            $iblockCatalogID = 17;
            $iblockBrandID = 12;
            $iblockServicesID = 16;
            
            $arCatalogCodes = getElementsCodes($iblockCatalogID);
            $arBrandCodes = getElementsCodes($iblockBrandID);
            $arServicesCodes = getElementsCodes($iblockServicesID);
        
        
            $dbRes = \CIblockElement::GetList(
                array(),
                array(
                    'IBLOCK_ID' => $iblockCatalogID,
                    array(
                        'LOGIC' => 'OR',
                        array('!PROPERTY_BRAND' => false),
                        array('!PROPERTY_SERVICES' => false),
                        array('!PROPERTY_EXPANDABLES' => false),
                        array('!PROPERTY_ASSOCIATED' => false),
                    )
                ),
                false,
                false,
                array(
                    'ID',
                    'CODE',
                    'PROPERTY_BRAND',
                    'PROPERTY_SERVICES',
                    'PROPERTY_EXPANDABLES',
                    'PROPERTY_ASSOCIATED',
                )
            );
        
            while ($res = $dbRes->GetNext()) {
                $arReplaceData[$res['ID']]['CODE'] = $res['CODE'];
        
                if(!empty($res['PROPERTY_BRAND_VALUE'])){
                    $arReplaceData[$res['ID']]['PROPERTY_BRAND_VALUE'] = $arBrandCodes[$res['PROPERTY_BRAND_VALUE']];
                }
        
                if(!empty($res['PROPERTY_SERVICES_VALUE'])){
                    $arReplaceData[$res['ID']]['PROPERTY_SERVICES_VALUE'] = $arServicesCodes[$res['PROPERTY_SERVICES_VALUE']];
                }
        
                if(!empty($res['PROPERTY_EXPANDABLES_VALUE'])){
                    $arReplaceData[$res['ID']]['PROPERTY_EXPANDABLES_VALUE'] = $arCatalogCodes[$res['PROPERTY_EXPANDABLES_VALUE']];
                }
        
                if(!empty($res['PROPERTY_ASSOCIATED_VALUE'])){
                    $arReplaceData[$res['ID']]['PROPERTY_ASSOCIATED_VALUE'] = $arCatalogCodes[$res['PROPERTY_ASSOCIATED_VALUE']];
                }
            }
        
            if(!empty($arReplaceData)){
                $arReplaceData = json_encode($arReplaceData, JSON_UNESCAPED_UNICODE);
                file_put_contents(__DIR__ . '/catalog_replace_data.json', $arReplaceData);
            }
        }
    }

    public function down()
    {
        //your code ...
    }
}
