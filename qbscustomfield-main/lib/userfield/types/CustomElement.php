<?php


namespace QBS\Customfield\Userfield;

use Bitrix\Crm\ItemIdentifier;
use Bitrix\Crm\Service\Container;
use Bitrix\Crm\Settings\InvoiceSettings;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserField\Types\BaseType;
use Bitrix\Main\UserField\Types\StringType;
use CUserTypeManager;

Loc::loadMessages(__FILE__);

class CustomElement extends StringType
{
    public const
        USER_TYPE_ID = 'customelement',
        RENDER_COMPONENT = 'qbs:crm.field.element';

    protected const ENTITY_TYPE_NAMES = [
        \CCrmOwnerTypeAbbr::Deal => \CCrmOwnerType::DealName,
        \CCrmOwnerTypeAbbr::Contact => \CCrmOwnerType::ContactName,
        \CCrmOwnerTypeAbbr::Company => \CCrmOwnerType::CompanyName,
        \CCrmOwnerTypeAbbr::Order => \CCrmOwnerType::OrderName,
        \CCrmOwnerTypeAbbr::Lead => \CCrmOwnerType::LeadName,
        \CCrmOwnerTypeAbbr::Quote => \CCrmOwnerType::QuoteName,
        \CCrmOwnerTypeAbbr::SmartInvoice => \CCrmOwnerType::SmartInvoiceName,
        \CCrmOwnerTypeAbbr::DynamicTypeAbbreviationPrefix => \CCrmOwnerType::CommonDynamicName,
    ];

    protected const ENTITY_TYPE_NAME_DEFAULT = 'L';

    public static function getDescription(): array
    {
        return [
            "USER_TYPE_ID" => self::USER_TYPE_ID,
            "CLASS_NAME" => __CLASS__,
            'DESCRIPTION' => Loc::getMessage('QBS.CUSTOMELEMENT.DESCRIPTION'),
            'BASE_TYPE' => CUserTypeManager::BASE_TYPE_STRING,
            "EDIT_CALLBACK" => array(__CLASS__, 'getPublicEdit'),
            "VIEW_CALLBACK" => array(__CLASS__, 'getPublicView')
        ];
    }
    public static function prepareSettings(array $userField): array
    {
        $entityTypes = [];

        foreach ($userField['SETTINGS'] as $entityTypeName => $status)
        {
            $entityTypeId = \CCrmOwnerType::ResolveID($entityTypeName);
            if (
                $entityTypeId
                && (
                    in_array($entityTypeName, self::ENTITY_TYPE_NAMES, true)
                    || \CCrmOwnerType::isPossibleDynamicTypeId($entityTypeId)
                )
            )
            {
                $entityTypes[$entityTypeName] = ($status === 'Y' ? 'Y' : 'N');
            }
        }

        $entityQuantity = 0;

        foreach($entityTypes as $result)
        {
            if($result === 'Y')
            {
                $entityQuantity++;
            }
        }

        $entityTypes['LEAD'] = ($entityQuantity === 0 ? 'Y' : $entityTypes['LEAD']);

        return $entityTypes;
    }

    /**
     * @param array $userField
     * @param array|string $value
     * @return array
     */
    public static function checkFields(array $userField, $value): array
    {
        return [];
    }

    /**
     * @param array $userField
     * @param bool|int $userId
     * @return bool
     * @throws \Bitrix\Main\LoaderException
     */
    public static function checkPermission(array $userField, $userId = false): bool
    {
        //permission check is disabled
        if(!$userId)
        {
            return true;
        }

        if(!Loader::includeModule('crm'))
        {
            return false;
        }

        $userPerms = (
        $userId > 0
            ? CCrmPerms::GetUserPermissions($userId)
            : CCrmPerms::GetCurrentUserPermissions()
        );

        return CCrmPerms::IsAccessEnabled($userPerms);
    }

    /**
     * @param string $type
     * @return string
     */
    public static function getShortEntityType(string $type): string
    {
        $entityTypeNames = array_flip(self::ENTITY_TYPE_NAMES);

        return ($entityTypeNames[$type] ??
            $entityTypeNames[self::ENTITY_TYPE_NAME_DEFAULT]
        );
    }

    /**
     * @param string $type
     * @return string
     */
    public static function getLongEntityType(string $type): string
    {
        if (isset(self::ENTITY_TYPE_NAMES[$type]))
        {
            return self::ENTITY_TYPE_NAMES[$type];
        }

        $entityTypeName = \CCrmOwnerTypeAbbr::ResolveName($type);
        $entityTypeId = \CCrmOwnerType::ResolveID($entityTypeName);

        if (\CCrmOwnerType::isPossibleDynamicTypeId($entityTypeId))
        {
            if ($factory = Container::getInstance()->getFactory($entityTypeId))
            {
                return $factory->getEntityName();
            }
        }

        return self::ENTITY_TYPE_NAMES[self::ENTITY_TYPE_NAME_DEFAULT];
    }

    /**
     * @return array
     */
    public static function getEntityTypeNames(): array
    {
        return static::ENTITY_TYPE_NAMES;
    }

    public static function getUseInUserfieldTypes(): array
    {
        $typesMap = \Bitrix\Crm\Service\Container::getInstance()->getDynamicTypesMap()->load([
            'isLoadStages' => false,
            'isLoadCategories' => false,
        ]);

        $types = $typesMap->getTypes();

        $result = [];
        foreach($types as $type)
        {
            if ($type['IS_USE_IN_USERFIELD_ENABLED'])
            {
                $result[$type['ENTITY_TYPE_ID']] = $type['TITLE'];
            }
        }

        return $result;
    }

    public static function getPossibleEntityTypes(): array
    {
        $entityTypes = [
            \CCrmOwnerType::LeadName => \CCrmOwnerType::GetDescription(\CCrmOwnerType::Lead),
            \CCrmOwnerType::ContactName => \CCrmOwnerType::GetDescription(\CCrmOwnerType::Contact),
            \CCrmOwnerType::CompanyName => \CCrmOwnerType::GetDescription(\CCrmOwnerType::Company),
            \CCrmOwnerType::DealName => \CCrmOwnerType::GetDescription(\CCrmOwnerType::Deal),
            \CCrmOwnerType::OrderName => \CCrmOwnerType::GetDescription(\CCrmOwnerType::Order),
            \CCrmOwnerType::QuoteName => \CCrmOwnerType::GetDescription(\CCrmOwnerType::Quote),
        ];

        if (InvoiceSettings::getCurrent()->isSmartInvoiceEnabled())
        {
            $entityTypes[\CCrmOwnerType::SmartInvoiceName] = \CCrmOwnerType::GetDescription(\CCrmOwnerType::SmartInvoice);
        }

        foreach (static::getUseInUserfieldTypes() as $entityTypeId => $title)
        {
            $entityTypes[\CCrmOwnerType::ResolveName($entityTypeId)] = $title;
        }

        return $entityTypes;
    }

    public static function getAvailableTypes(array $userField): array
    {
        $availableTypes = ($userField['SETTINGS'] ?? []);
        if (isset($availableTypes[\CCrmOwnerType::CommonDynamicName]))
        {
            $dynamicTypesSettings = $availableTypes[\CCrmOwnerType::CommonDynamicName];
            unset($availableTypes[\CCrmOwnerType::CommonDynamicName]);
            foreach ($dynamicTypesSettings as $dynamicEntityId => $status)
            {
                $availableTypes[\CCrmOwnerType::ResolveName($dynamicEntityId)] = $status;
            }
        }

        return $availableTypes;
    }

    /**
     * Return parameters for destination selector of userfield with $settings.
     *
     * @param array $settings
     * @param bool $isMultiple
     * @return array
     */
    public static function getDestSelectorParametersForFilter(array $settings, bool $isMultiple): array
    {
        $possibleTypes = static::getPossibleEntityTypes();

        $entityTypeNames = [];
        foreach($settings as $entityTypeName => $value)
        {
            if(
                $value === 'Y'
                && isset($possibleTypes[$entityTypeName])
            )
            {
                $entityTypeNames[] = $entityTypeName;
            }
        }

        $destSelectorParams = [
            'apiVersion' => 3,
            'context' => 'CRM_UF_FILTER_ENTITY',
            'contextCode' => 'CRM',
            'useClientDatabase' => 'N',
            'enableAll' => 'N',
            'enableDepartments' => 'N',
            'enableUsers' => 'N',
            'enableSonetgroups' => 'N',
            'allowEmailInvitation' => 'N',
            'allowSearchEmailUsers' => 'N',
            'departmentSelectDisable' => 'Y',
            'enableCrm' => 'Y',
            'multiple' => ($isMultiple ? 'Y' : 'N'),
            'convertJson' => 'Y',
        ];

        $destSelectorParams = array_merge_recursive(
            $destSelectorParams,
            static::getEnableEntityTypesForSelectorOptions($entityTypeNames)
        );

        if (
            is_array($destSelectorParams['addTabCrmDynamics'])
            && count($destSelectorParams['addTabCrmDynamics'])
        )
        {
            $destSelectorParams['crmDynamicTitles'] = static::getDynamicEntityTitles();
        }

        return $destSelectorParams;
    }

    /**
     * Return map of dynamic entity titles.
     *
     * @return array
     */
    public static function getDynamicEntityTitles(): array
    {
        $result = [];
        foreach (\CCrmOwnerType::GetAllDescriptions() as $id => $title)
        {
            if (\CCrmOwnerType::isPossibleDynamicTypeId($id))
            {
                $result['DYNAMICS_' . $id] = htmlspecialcharsbx($title);
            }
        }

        return $result;
    }

    /**
     * @param array|null $availableTypes
     * @param array|null $crmDynamicTitles
     * @return array
     */
    public static function getEnableEntityTypesForSelectorOptions(
        ?array $availableTypes = [],
        ?array $crmDynamicTitles = []
    ): array
    {
        $selectorOptions = [];
        $tabsCounter = 0;

        if(in_array(\CCrmOwnerType::ContactName, $availableTypes, true))
        {
            $selectorOptions['enableCrmContacts'] = 'Y';
            $selectorOptions['addTabCrmContacts'] = 'Y';
            $tabsCounter++;
        }
        if(in_array(\CCrmOwnerType::CompanyName, $availableTypes, true))
        {
            $selectorOptions['enableCrmCompanies'] = 'Y';
            $selectorOptions['addTabCrmCompanies'] = 'Y';
            $tabsCounter++;
        }
        if(in_array(\CCrmOwnerType::LeadName, $availableTypes, true))
        {
            $selectorOptions['enableCrmLeads'] = 'Y';
            $selectorOptions['addTabCrmLeads'] = 'Y';
            $tabsCounter++;
        }
        if(in_array(\CCrmOwnerType::DealName, $availableTypes, true))
        {
            $selectorOptions['enableCrmDeals'] = 'Y';
            $selectorOptions['addTabCrmDeals'] = 'Y';
            $tabsCounter++;
        }
        if(in_array(\CCrmOwnerType::OrderName, $availableTypes, true))
        {
            $selectorOptions['enableCrmOrders'] = 'Y';
            $selectorOptions['addTabCrmOrders'] = 'Y';
            $tabsCounter++;
        }
        if(in_array(\CCrmOwnerType::QuoteName, $availableTypes, true))
        {
            $selectorOptions['enableCrmQuotes'] = 'Y';
            $selectorOptions['addTabCrmQuotes'] = 'Y';
            $tabsCounter++;
        }
        if (in_array(\CCrmOwnerType::SmartInvoiceName, $availableTypes, true))
        {
            $selectorOptions['enableCrmSmartInvoices'] = 'Y';
            $selectorOptions['addTabCrmSmartInvoices'] = 'Y';
            $tabsCounter++;
        }

        foreach($availableTypes as $typeName)
        {
            $entityTypeId = \CCrmOwnerType::ResolveID($typeName);
            if (\CCrmOwnerType::isPossibleDynamicTypeId($entityTypeId))
            {
                $selectorOptions['enableCrmDynamics'][$entityTypeId] = 'Y';
                $selectorOptions['addTabCrmDynamics'][$entityTypeId] = 'Y';
                $tabsCounter++;
            }
        }

        if($tabsCounter <= 1)
        {
            $selectorOptions['addTabCrmContacts'] = 'N';
            $selectorOptions['addTabCrmCompanies'] = 'N';
            $selectorOptions['addTabCrmLeads'] = 'N';
            $selectorOptions['addTabCrmDeals'] = 'N';
            $selectorOptions['addTabCrmOrders'] = 'N';
            $selectorOptions['addTabCrmQuotes'] = 'N';
            $selectorOptions['addTabCrmSmartInvoices'] = 'N';
            if (!empty($selectorOptions['addTabCrmDynamics']))
            {
                foreach($selectorOptions['addTabCrmDynamics'] as $key => $item)
                {
                    $selectorOptions['addTabCrmDynamics'][$key] = 'N';
                }
            }
        }

        if (is_array($crmDynamicTitles) && count($crmDynamicTitles))
        {
            $selectorOptions['crmDynamicTitles'] = $crmDynamicTitles;
        }

        return $selectorOptions;
    }

    public static function getValueByIdentifier(ItemIdentifier $identifier): string
    {
        return \CCrmOwnerTypeAbbr::ResolveByTypeID($identifier->getEntityTypeId()) . '_' . $identifier->getEntityId();
    }
}